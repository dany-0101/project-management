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
        // Fetch project members
        $projectMemberController = new ProjectMemberController($this->db);
        $creator = $this->project->getProjectCreator($id);
        $members = $projectMemberController->getProjectMembers($id);
        if (!$project) {
            $_SESSION['error'] = "Project not found.";
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $board = $this->board->getByProjectId($id);
        if (!$board) {

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


        if (empty($data['name'])) {
            $_SESSION['error'] = "Invalid project data. Please provide a project name.";
            error_log("Invalid project data: " . json_encode($data));
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $projectData = [
            'title' => $data['name'],
            'user_id' => $_SESSION['user_id'] ?? null
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
        error_log("Attempting to delete project $projectId");
        $result = $this->project->delete($projectId);
        if ($result) {
            $_SESSION['success'] = "Project deleted successfully.";
            error_log("Project $projectId deleted successfully");
        } else {
            $error = $this->project->getLastError();
            if (empty($error)) {
                $error = "Unknown error occurred";
            }
            $_SESSION['error'] = "Error deleting project: " . $error;
            error_log("Error deleting project $projectId: $error");
        }
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
    public function leave() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
            $projectId = $_POST['project_id'];
            $userId = $_SESSION['user_id'];

            $projectMember = new \Models\ProjectMember($this->db);
            if ($projectMember->removeUserFromProject($userId, $projectId)) {
                $_SESSION['success'] = "You have successfully left the project.";
            } else {
                $_SESSION['error'] = "Failed to leave the project. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Invalid request.";
        }

        header("Location: " . BASE_URL . "/dashboard");
        exit();
    }
}