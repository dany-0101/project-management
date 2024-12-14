<?php
namespace Controllers;

use Models\Project;
use Config\Database;
use Models\ProjectMember;
class DashboardController {
    private $db;
    private $project;
    private $projectMember;
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->project = new Project($this->db);
        $this->projectMember = new ProjectMember($this->db);
    }

    public function index() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
            // Redirect to login if user is not logged in
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'];
        $projects = $this->project->getUserProjects($userId);
        $invitedProjects = $this->projectMember->getInvitedProjects($userEmail);

        // Add this line to pass the projects to the view
        include __DIR__ . '/../views/dashboard/dashboardview.php';
    }

}