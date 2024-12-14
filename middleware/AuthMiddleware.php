<?php


namespace Middleware;

class AuthMiddleware {
    public static function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit();
        }
    }
}