<?php
namespace Models;

use PDO;

class PasswordReset {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createResetToken($userId, $token, $expiry) {
        $query = "INSERT INTO password_resets (user_id, token, expiry) VALUES (:user_id, :token, :expiry)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expiry', $expiry);
        return $stmt->execute();
    }

    public function getResetInfo($token) {
        $query = "SELECT * FROM password_resets WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteResetToken($token) {
        $query = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        return $stmt->execute();
    }
}