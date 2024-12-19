<?php
namespace Controllers;

use Models\ProjectMember;
use Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class ProjectMemberController {
    private $projectMember;
    private $user;

    public function __construct($db) {
        $this->projectMember = new ProjectMember($db);
        $this->user = new User($db);
    }

    public function inviteUser($projectId, $email) {

        $currentUserEmail = $this->user->getUserEmailById($_SESSION['user_id']);
        if ($currentUserEmail === $email) {
            $_SESSION['error'] = "You cannot invite yourself to the project. You are already the owner.";
            header('Location: ' . BASE_URL . '/projects/view/' . $projectId);
            exit;
        }


        $user = $this->user->getUserByEmail($email);
        $userExists = ($user !== false);


        if ($this->projectMember->hasPendingInvitation($projectId, $email)) {
            $_SESSION['error'] = "An invitation has already been sent to this email.";
            header('Location: ' . BASE_URL . '/projects/view/' . $projectId);
            exit;
        }

        $token = bin2hex(random_bytes(16));


        if ($this->projectMember->createInvitation($projectId, $email, $token)) {

            if ($this->sendInvitationEmail($email, $token, $projectId, $userExists)) {
                $_SESSION['success'] = "Invitation sent successfully.";
            } else {
                $_SESSION['error'] = "Invitation created but email could not be sent.";
            }
        } else {
            $_SESSION['error'] = "Failed to create invitation. Please try again.";
        }

        header('Location: ' . BASE_URL . '/projects/view/' . $projectId);
        exit;
    }



    public function getProjectMembers($projectId) {
        $members = $this->projectMember->getProjectMembers($projectId);
        $creator = $this->projectMember->getProjectCreator($projectId);
        $invitations = $this->projectMember->getPendingInvitations($projectId);


        $allMembers = array_merge([$creator], $members, $invitations);

        return array_map(function($member) {
            return [
                'id' => $member['id'],
                'name' => $member['name'] ?? 'Pending',
                'email' => $member['email'],
                'status' => $member['status'],
                'is_creator' => $member['status'] === 'creator',
            ];
        }, $allMembers);
    }

    public function removeMember() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $projectId = $data['project_id'];
            $memberId = $data['member_id'];


            if (!$this->userCanManageProject($projectId)) {
                echo json_encode(['success' => false, 'message' => 'You do not have permission to remove members from this project.']);
                return;
            }

            if ($this->projectMember->removeUserFromProject($memberId, $projectId)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to remove member from the project.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
    public function cancelInvitation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $projectId = $data['project_id'] ?? null;
        $memberId = $data['member_id'] ?? null;

        if (!$projectId || !$memberId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing required data']);
            return;
        }


        error_log("Attempting to cancel invitation for project $projectId and member $memberId");

        $result = $this->projectMember->cancelInvitation($projectId, $memberId);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            error_log("Failed to cancel invitation for project $projectId and member $memberId");
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to cancel invitation']);
        }
    }


    public function showInvitedProjects() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to view invited projects.";
            header("Location: " . BASE_URL . "/auth/login");
            exit();
        }

        $userId = $_SESSION['user_id'];

        // Get the user's email
        $userEmail = $this->user->getUserEmailById($userId);

        if (!$userEmail) {
            $_SESSION['error'] = "Unable to retrieve user information.";
            header("Location: " . BASE_URL . "/dashboard");
            exit();
        }

        $invitedProjects = $this->projectMember->getInvitedProjects($userEmail);

        error_log("Invited projects for user email $userEmail: " . print_r($invitedProjects, true));


        require_once __DIR__ . '/../views/dashboard/dashboardview.php';
    }
    public function acceptInvitation($token) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = "You must be logged in to accept an invitation.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $invitation = $this->projectMember->getInvitationByToken($token);

        if (!$invitation) {
            $_SESSION['error'] = "Invalid or expired invitation.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $result = $this->projectMember->acceptInvitation($invitation['id'], $userId);

        if ($result) {
            $_SESSION['success'] = "Invitation accepted successfully.";
        } else {
            $_SESSION['error'] = "There was an error accepting the invitation.";
        }

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }




    private function sendInvitationEmail($email, $token, $projectId, $userExists) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = getenv('SMTP_USERNAME');
            $mail->Password   = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = getenv('SMTP_PORT');


            // Recipients
            $mail->setFrom('from@example.com', 'Project Management System');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Invitation to join a project';
            $mail->Body    = $this->getEmailBody($token, $projectId, $userExists);
            $invitationLink = BASE_URL . '/projects/accept-invitation/' . $token;
            $mail->send();

            return true;
        } catch (PHPMailerException $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }

    }

    private function getEmailBody($token, $projectId, $userExists) {
        $invitationLink = BASE_URL . '/auth/login?project=' . $projectId . '&token=' . $token;

        $body = "You have been invited to join a project in our Project Management System.<br><br>";

        if ($userExists) {
            $body .= "Please click the link below to log in and accept the invitation:<br>";
        } else {
            $body .= "If you don't have an account yet, you'll be able to register after clicking the link below:<br>";
        }

        $body .= "<a href='{$invitationLink}'>{$invitationLink}</a>";

        return $body;
    }


    public function rejectInvitation() {
        if (!isset($_SESSION['user_id']) || !isset($_POST['token'])) {
            $_SESSION['error'] = "Invalid request.";
            header('Location: ' . BASE_URL . '/projects/invited');
            exit;
        }

        $token = $_POST['token'];
        $invitation = $this->projectMember->getInvitationByToken($token);

        if (!$invitation) {
            $_SESSION['error'] = "Invalid or expired invitation.";
            header('Location: ' . BASE_URL . '/projects/invited');
            exit;
        }

        $result = $this->projectMember->rejectInvitation($invitation['id']);

        if ($result) {
            $_SESSION['success'] = "Invitation rejected successfully.";
        } else {
            $_SESSION['error'] = "There was an error rejecting the invitation.";
        }

        header('Location: ' . BASE_URL . '/projects/invited');
        exit;
    }
}