<?php
namespace Controllers;

use Models\Profile;

class ProfileController {
    private $profileModel;

    public function __construct() {
        global $db;
        $this->profileModel = new Profile($db);
        $this->profileModel->user_id = $_SESSION['user_id'] ?? null;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $userId = $_SESSION['user_id'];

            $data = [
                'name' => $name,
                'email' => $email,
                'password' => $password
            ];

            $photoPath = null;

            // Handle file upload
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/profile_photos/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                    $photoPath = '/' . $uploadFile;
                } else {
                    $_SESSION['error'] = "Failed to upload file.";
                    header('Location: ' . BASE_URL . '/dashboard');
                    exit;
                }
            }

            // Update user information
            $result = $this->profileModel->update($data, $photoPath);

            if ($result) {
                // Update session data
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                if ($photoPath) {
                    $_SESSION['user_photo'] = $photoPath;
                }
                $_SESSION['success'] = "Profile updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update profile.";
            }

            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }
}