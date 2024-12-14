<?php
namespace Controllers;

use Models\Project;
use Models\Board;
use Models\Status;
use Models\Task;
use Config\Database;

class ProjectController {
    private $db;
    private $project;
    private $board;
    private $status;
    private $task;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->project = new Project($this->db);
        $this->board = new Board($this->db);
        $this->status = new Status($this->db);
        $this->task = new Task($this->db);

    }


    public function view($id) {
        $project = $this->project->getProjectById($id);

        if (!$project) {
            $_SESSION['error'] = "Project not found.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $board = $this->board->getByProjectId($id);
        if (!$board) {
            // If no board exists, create one
            $boardData = [
                'title' => $project['title'] . ' Board',
                'project_id' => $id
            ];
            $board_id = $this->board->create($boardData);
            $board = $this->board->getById($board_id);
        }

        $statuses = $this->status->getByBoardId($board['id']);
        $tasks = $this->task->getByBoardId($board['id']);

        // Pass all the data to the view
        include __DIR__ . '/../views/projects/projectsview.php';
    }

    public function create($data) {
        error_log("Create method called in ProjectController with data: " . json_encode($data));

        // Validate input data
        if (empty($data['name'])) {
            $_SESSION['error'] = "Invalid project data. Please provide a project name.";
            error_log("Invalid project data: " . json_encode($data));
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        // Prepare project data
        $projectData = [
            'title' => $data['name'],
            'user_id' => $_SESSION['user_id'] ?? null // Ensure you have user_id in session
        ];

        error_log("Attempting to create project with data: " . json_encode($projectData));

        $result = $this->project->create($projectData);

        if ($result) {
            $_SESSION['success'] = "Project created successfully with ID: " . $result;
            error_log("Project created successfully with ID: " . $result);
        } else {
            $_SESSION['error'] = "Error creating project. Please try again.";
            error_log("Failed to create project. Project model returned false.");
        }

        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

    public function update($projectId, $title) {
        // Ensure we're passing an array to the update method
        $data = [
            'project_id' => $projectId,
            'title' => $title
        ];

        if ($this->project->update($data)) {
            $_SESSION['success'] = "Project renamed successfully.";
        } else {
            $_SESSION['error'] = "Failed to rename project.";
        }

        header('Location: ' . BASE_URL . '/dashboard');
        exit();
    }


    public function delete($projectId) {
        $result = $this->project->delete($projectId);
        if ($result) {
            $_SESSION['success'] = "Project deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting project. Please try again.";
        }
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }

}