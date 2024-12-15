<?php
namespace Controllers;

use Models\Board;
use Models\Status;

class StatusController {
    private $db;
    private $status;
    private $board;

    public function __construct($db) {
        $this->db = $db;
        $this->status = new Status($db);
        $this->board = new Board($db);
    }

    public function create($data) {
        $boardId = $data['board_id'] ?? null;
        $name = $data['name'] ?? null;

        if ($boardId && $name) {
            $result = $this->status->create($boardId, $name);
            if ($result) {
                $_SESSION['success'] = "New status created successfully.";
            } else {
                $_SESSION['error'] = "Failed to create new status.";
            }
        } else {
            $_SESSION['error'] = "Invalid data provided.";
        }

        $board = $this->board->getById($boardId);
        $projectId = $board['project_id'] ?? null;

        if ($projectId) {

            header('Location: ' . BASE_URL . '/projects/view/' . $projectId);
        }
        exit();
    }
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status_id = $_POST['status_id'];
            $board_id = $_POST['board_id'];


            $board = $this->board->getById($board_id);
            $project_id = $board['project_id'];

            if ($this->status->delete($status_id)) {
                $_SESSION['success'] = "Status and associated tasks deleted successfully.";
            } else {
                $_SESSION['error'] = "Failed to delete status and associated tasks.";
            }


            header("Location: " . BASE_URL . "/projects/view/" . $project_id);
            exit();
        }
    }

    public function update($data) {
        $status_id = $data['status_id'];
        $name = $data['name'];
        $board_id = $data['board_id'];

        if ($this->status->update($status_id, $name)) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            echo "Status update failed";
        }
    }

}





