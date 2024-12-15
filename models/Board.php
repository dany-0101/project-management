<?php
namespace Models;

use PDO;
class Board {
    private $conn;
    private $table = 'boards';

    public $id;
    public $title;
    public $user_id;
    public $project_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getById($id) {
        $query = "SELECT * FROM boards WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getProjectIdByBoardId($board_id) {
        $query = "SELECT project_id FROM " . $this->table . " WHERE id = :board_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':board_id', $board_id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['project_id'] : null;
    }
    public function getBoardTasks($board_id) {
        $query = "SELECT t.* FROM tasks t
              JOIN statuses s ON t.status_id = s.id
              WHERE s.board_id = :board_id
              ORDER BY s.position, t.position";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':board_id', $board_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    public function getByProjectId($project_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE project_id = :project_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (title, project_id) VALUES (:title, :project_id)";

        $stmt = $this->conn->prepare($query);


        $title = htmlspecialchars(strip_tags($data['title']));
        $project_id = htmlspecialchars(strip_tags($data['project_id']));


        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':project_id', $project_id);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }

        printf("Error: %s.\n", $stmt->error);

        return false;
    }
}