<?php
namespace Models;

class Status {
    private $conn;
    private $table = 'statuses';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByBoardId($board_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE board_id = :board_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':board_id', $board_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function create($boardId, $name) {
        $query = "INSERT INTO " . $this->table . " (board_id, name) VALUES (:board_id, :name)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':board_id', $boardId);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    public function update($status_id, $name) {
        $query = "UPDATE " . $this->table . " SET name = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $status_id]);
    }

}