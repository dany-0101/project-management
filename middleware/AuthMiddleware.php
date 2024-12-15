<?php


namespace Middleware;

class AuthMiddleware {
    public static function isAuthenticated() {
        error_log("AuthMiddleware called");
        if (!isset($_SESSION['user_id'])) {
            error_log("User not authenticated");
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
        error_log("User authenticated");
    }
}