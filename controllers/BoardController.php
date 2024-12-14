<?php
namespace Controllers;

use Models\Board;
use Models\Status;
use Models\Task;

class BoardController {
    private $board;
    private $status;
    private $task;

    public function __construct($db) {
        $this->board = new Board($db);
        $this->status = new Status($db);
        $this->task = new Task($db);
    }

    public function view($id) {
        $board = $this->board->getById($id);
        $statuses = $this->board->getBoardStatuses($id);
        $tasks = $this->board->getBoardTasks($id);

        // Group tasks by status
        $tasksByStatus = [];
        foreach ($tasks as $task) {
            $tasksByStatus[$task['status_id']][] = $task;
        }

        // Change this line to use the correct path
        require __DIR__ . '/../views/projects/projectsview.php';
    }

    public function addStatus($board_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $position = $this->status->getLastPosition($board_id) + 1;

            if ($this->status->create($name, $board_id, $position)) {
                header("Location: " . BASE_URL . "/projects/view/" . $board_id);
                exit;
            } else {
                echo "Status creation failed";
            }
        }
    }

    public function addTask($board_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $taskData = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'status' => $_POST['status'],
                'board_id' => $board_id,
                'position' => $this->getNextTaskPosition($_POST['status'])
            ];

            if ($this->task->create($taskData)) {
                header("Location: " . BASE_URL . "/projects/view/" . $board_id);
                exit;
            } else {
                echo "Task creation failed";
            }
        }
    }

    private function getNextTaskPosition($status) {
        $tasks = $this->task->getTasksByStatus($status);
        return count($tasks) + 1;
    }
}