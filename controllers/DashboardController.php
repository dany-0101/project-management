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
        $data['success'] = isset($_SESSION['success']) ? $_SESSION['success'] : null;
        $data['error'] = isset($_SESSION['error']) ? $_SESSION['error'] : null;


        unset($_SESSION['success']);
        unset($_SESSION['error']);

        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'];


        $projects = $this->project->getUserProjects($userId);


        $invitedProjects = $this->projectMember->getInvitedProjects($userEmail);


        $acceptedProjects = $this->projectMember->getAcceptedProjects($userId);



        include __DIR__ . '/../views/dashboard/dashboardview.php';
    }
}