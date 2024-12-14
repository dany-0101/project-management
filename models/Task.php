<?php
namespace Models;

use PDO;
use PDOException;

class Task {
    private $conn;
    private $table = 'tasks';

    public $id;
    public $title;
    public $description;
    public $status;
    public $project_id;
    public $board_id;
    public $user_id;
    public $priority;
    public $due_date;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
              (title, description, status_id, board_id, project_id, priority, due_date, user_id) 
              VALUES 
              (:title, :description, :status_id, :board_id, :project_id, :priority, :due_date, :user_id)";

        try {
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':description', $this->description);
            $stmt->bindParam(':status_id', $this->status_id);
            $stmt->bindParam(':board_id', $this->board_id);
            $stmt->bindParam(':project_id', $this->project_id);
            $stmt->bindParam(':priority', $this->priority);
            $stmt->bindParam(':due_date', $this->due_date);
            $stmt->bindParam(':user_id', $this->user_id);

            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error in Task create: " . $e->getMessage());
            return $e->getMessage();
        }
    }

    public function update($data) {
        $query = "UPDATE " . $this->table . " 
              SET title = :title, 
                  description = :description, 
                  priority = :priority, 
                  due_date = :due_date, 
                  status_id = :status_id 
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':priority', $data['priority']);
        $stmt->bindParam(':due_date', $data['due_date']);
        $stmt->bindParam(':status_id', $data['status_id']);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }


    public function updateTaskStatusAndPosition($taskId, $newStatusId, $newPosition)
    {
        $query = "UPDATE " . $this->table . " SET status_id = :status_id, position = :position WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status_id', $newStatusId);
        $stmt->bindParam(':position', $newPosition);
        $stmt->bindParam(':id', $taskId);
        return $stmt->execute();
    }
    public function updatePositionsInStatus($statusId)
    {
        $query = "SELECT id FROM " . $this->table . " WHERE status_id = :status_id ORDER BY position";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status_id', $statusId);
        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tasks as $index => $task) {
            $query = "UPDATE " . $this->table . " SET position = :position WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $position = $index;
            $stmt->bindParam(':position', $position);
            $stmt->bindParam(':id', $task['id']);
            $stmt->execute();
        }
    }

    public function deleteByStatusId($status_id) {
        $query = "DELETE FROM " . $this->table . " WHERE status_id = :status_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status_id', $status_id);
        return $stmt->execute();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getByBoardId($board_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE board_id = :board_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':board_id', $board_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTasksByStatus($status) {
        $query = "SELECT * FROM " . $this->table . " WHERE status = :status ORDER BY created_at";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByProjectId($project_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id, \PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching tasks by project ID: " . $e->getMessage());
            return false;
        }
    }
}