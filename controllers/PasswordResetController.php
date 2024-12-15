<?php
namespace Controllers;

use Models\PasswordReset;
use Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class PasswordResetController {
    private $passwordReset;
    private $user;

    public function __construct($db) {
        $this->passwordReset = new PasswordReset($db);
        $this->user = new User($db);
    }

    public function forgotPassword() {
        // Display the forgot password form
        require_once __DIR__ . '/../views/auth/forgot_password.php';
    }

    public function sendResetLink() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $user = $this->user->getUserByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(16));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                if ($this->passwordReset->createResetToken($user['id'], $token, $expiry)) {
                    $this->sendResetEmail($email, $token);
                    $_SESSION['success'] = "Password reset link has been sent to your email.";
                } else {
                    $_SESSION['error'] = "Failed to create reset token. Please try again.";
                }
            } else {
                $_SESSION['error'] = "No user found with that email address.";
            }

            header('Location: ' . BASE_URL . '/auth/forgot-password');
            exit;
        }
    }

    public function resetPassword($token) {
        $resetInfo = $this->passwordReset->getResetInfo($token);

        if (!$resetInfo || strtotime($resetInfo['expiry']) < time()) {
            $_SESSION['error'] = "Invalid or expired reset token.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = "Passwords do not match.";
                header('Location: ' . BASE_URL . '/auth/reset-password/' . $token);
                exit;
            }

            if ($this->user->updatePassword($resetInfo['user_id'], password_hash($password, PASSWORD_DEFAULT))) {
                $this->passwordReset->deleteResetToken($token);
                $_SESSION['success'] = "Your password has been reset successfully. You can now log in with your new password.";
                header('Location: ' . BASE_URL . '/auth/login');
            } else {
                $_SESSION['error'] = "Failed to reset password. Please try again.";
                header('Location: ' . BASE_URL . '/auth/reset-password/' . $token);
            }
            exit;
        }

        // Display the reset password form
        require_once __DIR__ . '/../views/auth/reset_password.php';
    }

    private function sendResetEmail($email, $token) {
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
            $mail->Subject = 'Password Reset Request';
            $resetLink = BASE_URL . '/auth/reset-password/' . $token;
            $mail->Body    = "Click the following link to reset your password: <a href='{$resetLink}'>{$resetLink}</a>";

            $mail->send();
        } catch (PHPMailerException $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}