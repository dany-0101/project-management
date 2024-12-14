<?php
namespace Controllers;

use Models\ProjectMember;
use Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PDO;
use PDOException;
class ProjectMemberController {
    private $projectMember;
    private $user;

    public function __construct($db) {
        $this->projectMember = new ProjectMember($db);
        $this->user = new User($db);
    }

    public function inviteUser($projectId, $email) {
        // Check if the user already exists
        $user = $this->user->getUserByEmail($email);
        $userExists = ($user !== false);

        // Check if there's already a pending invitation
        if ($this->projectMember->hasPendingInvitation($projectId, $email)) {
            $_SESSION['error'] = "An invitation has already been sent to this email.";
            header('Location: ' . BASE_URL . '/projects/view/' . $projectId);
            exit;
        }

        // Generate invitation token
        $token = bin2hex(random_bytes(16));

        // Save invitation to database
        if ($this->projectMember->createInvitation($projectId, $email, $token)) {
            // Send invitation email
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
        return $this->projectMember->getProjectMembers($projectId);
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

        // Render the view with invited projects
        require_once __DIR__ . '/../views/projects/invited_projects.php';
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

    public function leaveProject($projectId) {
        $userId = $_SESSION['user_id']; // Assuming you store user ID in session

        if ($this->projectMember->removeUserFromProject($projectId, $userId)) {
            $_SESSION['success'] = "You have successfully left the project.";
        } else {
            $_SESSION['error'] = "There was an error leaving the project.";
        }

        header("Location: /project-management/public/dashboard");
        exit();
    }


    private function sendInvitationEmail($email, $token, $projectId, $userExists) {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ca8ae61497d64e';  // Replace with your Mailtrap username
            $mail->Password   = '766609d68fd7e6';  // Replace with your Mailtrap password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 2525;

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

}