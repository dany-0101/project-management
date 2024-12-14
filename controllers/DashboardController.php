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

        // Fetch user's own projects
        $projects = $this->project->getUserProjects($userId);

        // Fetch invited projects (pending invitations)
        $invitedProjects = $this->projectMember->getInvitedProjects($userEmail);

        // Fetch accepted projects (where the user is a member)
        $acceptedProjects = $this->projectMember->getAcceptedProjects($userId);

        include __DIR__ . '/../views/dashboard/dashboardview.php';
    }
}