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


        require __DIR__ . '/../views/projects/projectsview.php';
    }

}