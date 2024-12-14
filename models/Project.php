<?php
namespace Models;

use PDO;
use PDOException;

class Project {
    private $db;
    private $conn;
    private $table = 'projects';

    public $id;
    public $title;
    public $user_id;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }


    public function getUserProjects($user_id) {
        $query = "SELECT p.*, COUNT(b.id) as board_count 
                  FROM " . $this->table . " p 
                  LEFT JOIN boards b ON p.id = b.project_id 
                  WHERE p.user_id = :user_id 
                  GROUP BY p.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function getAll() {
    try {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error in Project getAll: " . $e->getMessage());
        return false;
    }
}
    public function getProjectById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($data) {
        error_log("Create method called in Project model with data: " . json_encode($data));

        $query = "INSERT INTO " . $this->table . " (title, user_id, created_at) VALUES (:title, :user_id, :created_at)";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':user_id', $data['user_id']);
            $stmt->bindParam(':created_at', date('Y-m-d H:i:s'));

            if($stmt->execute()) {
                error_log("Project created successfully with ID: " . $this->conn->lastInsertId());
                return $this->conn->lastInsertId();
            }

            error_log("Project creation failed. PDO error info: " . json_encode($stmt->errorInfo()));
            return false;
        } catch(PDOException $e) {
            error_log("PDO Exception in Project create: " . $e->getMessage());
            return false;
        }
    }

    public function update($data) {
        $query = "UPDATE " . $this->table . " SET title = :title WHERE id = :id AND user_id = :user_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $data['project_id'], PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Exception when updating project: " . $e->getMessage());
            return false;
        }
    }

    public function delete($projectId) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND user_id = :user_id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $projectId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Exception when deleting project: " . $e->getMessage());
            return false;
        }
    }
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}