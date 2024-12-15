<?php

namespace Controllers;

use Models\Task;

class TaskController
{
    private $db;
    private $task;

    public function __construct($db)
    {
        $this->db = $db;
        $this->task = new Task($db);
    }

    public function create($data)
    {
        error_log("Attempting to create task with data: " . json_encode($data));

        $this->task->title = $data['title'];
        $this->task->description = $data['description'];
        $this->task->status_id = $data['status_id'];
        $this->task->board_id = $data['board_id'];
        $this->task->priority = $data['priority'];
        $this->task->due_date = $data['due_date'];
        $this->task->project_id = $data['project_id'];
        $this->task->user_id = $_SESSION['user_id'] ?? null;

        error_log("Task object before creation: " . json_encode($this->task));

        $result = $this->task->create();
        if ($result === true) {
            error_log("Task created successfully with ID: " . $this->task->id);
            header("Location: " . BASE_URL . "/projects/view/" . $data['project_id']);
            exit;
        } else {
            error_log("Task creation failed. Error: " . $result);
            $_SESSION['error'] = "Task creation failed. Please try again.";
            header("Location: " . BASE_URL . "/projects/view/" . $data['project_id']);
            exit;
        }
    }
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = $_POST['task_id'];

            // Fetch the current task data
            $currentTask = $this->task->getById($task_id);

            if (!$currentTask) {
                $_SESSION['error'] = "Task not found.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            $data = [
                'id' => $task_id,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'priority' => $_POST['priority'],
                'due_date' => $_POST['due_date'],
                'status_id' => $_POST['status_id'] ?? $currentTask['status_id']
            ];

            if ($this->task->update($data)) {
                $_SESSION['success'] = "Task updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update task.";
            }

            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
    public function deleteStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status_id = $_POST['status_id'] ?? null;
            $board_id = $_POST['board_id'] ?? null;

            if (!$status_id || !$board_id) {
                $_SESSION['error'] = "Missing required parameters for status deletion.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }


            $this->db->beginTransaction();

            try {

                if (!$this->task->deleteByStatusId($status_id)) {
                    throw new \Exception("Failed to delete tasks associated with the status.");
                }


                $status = new \Models\Status($this->db);
                if (!$status->delete($status_id)) {
                    throw new \Exception("Failed to delete status.");
                }


                $board = new \Models\Board($this->db);
                $project_id = $board->getProjectIdByBoardId($board_id);

                if (!$project_id) {
                    throw new \Exception("Failed to find associated project.");
                }

                $this->db->commit();
                $_SESSION['success'] = "Status and associated tasks deleted successfully.";


                header("Location: " . BASE_URL . "/projects/view/" . $project_id);
                exit;
            } catch (\Exception $e) {
                $this->db->rollBack();
                $_SESSION['error'] = "An error occurred: " . $e->getMessage();
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
    }
    public function delete($task_id) {
        if ($this->task->delete($task_id)) {
            $_SESSION['success'] = "Task deleted successfully.";
        } else {
            $_SESSION['error'] = "Task deletion failed.";
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);

            $task_id = $data['task_id'] ?? null;
            $new_status_id = $data['new_status_id'] ?? null;
            $new_position = $data['new_position'] ?? null;

            if (!$task_id || !$new_status_id || !is_numeric($new_position)) {
                echo json_encode(['success' => false, 'message' => 'Invalid data']);
                return;
            }

            $result = $this->task->updateTaskStatusAndPosition($task_id, $new_status_id, $new_position);

            if ($result) {
                // Update positions of other tasks in the same status
                $this->task->updatePositionsInStatus($new_status_id);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
        }
    }



}
