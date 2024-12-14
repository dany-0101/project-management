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
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'];

        // Get user's projects
        $projects = $this->project->getUserProjects($userId);

        // Get projects where the user is a member
        $memberProjects = $this->projectMember->getMemberProjects($userId);

        // Get invited projects
        $invitedProjects = $this->projectMember->getInvitedProjects($userEmail);

        // Combine user's projects and member projects
        $allProjects = array_merge($projects, $memberProjects);

        // Pass the data to the view
        require __DIR__ . '/../views/dashboard/dashboardview.php';
    }
}