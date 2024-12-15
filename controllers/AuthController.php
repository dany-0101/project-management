<?php
namespace Controllers;
use Models\User;

class AuthController {
    private $user;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function register($data) {
        if ($data['password'] !== $data['password_confirmation']) {
            $_SESSION['error'] = "Passwords do not match.";
            header('Location: /project-management/public/auth/register');
            exit();
        }

        if ($this->user->emailExists($data['email'])) {
            $_SESSION['error'] = "Email already exists. Please use a different email or try logging in.";
            header('Location: /project-management/public/auth/register');
            exit();
        }

        $this->user->name = $data['name'];
        $this->user->email = $data['email'];
        $this->user->password = password_hash($data['password'], PASSWORD_DEFAULT);

        if($this->user->create()) {
            $_SESSION['user_id'] = $this->user->id;
            $_SESSION['user_name'] = $this->user->name;
            $_SESSION['success'] = "Registration successful. Welcome, " . $this->user->name . "!";

            // Redirect to dashboard
            header('Location: /project-management/public/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Registration failed. Please try again.";
            header('Location: /project-management/public/auth/register');
            exit();
        }
    }

    public function login($email, $password) {
        $result = $this->user->login($email, $password);
        if ($result) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];

            // Fetch the user's photo from the database
            $userPhoto = $this->user->getUserPhoto($result['id']);
            $_SESSION['user_photo'] = $userPhoto ? $userPhoto : null;

            $_SESSION['success'] = "Login successful. Welcome back, " . $result['name'] . "!";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password. Please try again.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }


    public function logout() {
        session_start();
        session_destroy();
        header('Location: ' . BASE_URL . '/auth/login');
        exit();
    }
}