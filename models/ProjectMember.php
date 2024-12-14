<?php
namespace Models;

use PDO;
use PDOException;

class ProjectMember {
    private $conn;

    private $table = 'project_users';
    private $invitationsTable = 'project_invitations';
    public function __construct($db) {
        $this->conn = $db;
    }




    public function createInvitation($projectId, $email, $token) {
        $query = "INSERT INTO " . $this->invitationsTable . " (project_id, email, token, status) VALUES (:project_id, :email, :token, 'pending')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $projectId);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating invitation: " . $e->getMessage());
            return false;
        }
    }
    public function hasPendingInvitation($projectId, $email) {
        $query = "SELECT * FROM " . $this->invitationsTable . " WHERE project_id = :project_id AND email = :email AND status = 'pending'";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':project_id', $projectId);
            $stmt->bindParam(':email', $email);

            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error checking pending invitation: " . $e->getMessage());
            return false;
        }
    }


    public function getProjectMembers($projectId) {
        $query = "SELECT u.id, u.name, u.email 
              FROM users u 
              JOIN " . $this->table . " pm ON u.id = pm.user_id 
              WHERE pm.project_id = :project_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getInvitationByToken($token) {
        $query = "SELECT * FROM " . $this->invitationsTable . " WHERE token = :token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function acceptInvitation($invitationId, $userId) {
        $this->conn->beginTransaction();

        try {
            // Update invitation status
            $query = "UPDATE " . $this->invitationsTable . " SET status = 'accepted' WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $invitationId);
            $stmt->execute();

            // Get project_id from invitation
            $query = "SELECT project_id FROM " . $this->invitationsTable . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $invitationId);
            $stmt->execute();
            $invitation = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$invitation) {
                throw new \Exception("Invitation not found");
            }

            // Check if user is already a member of the project
            $query = "SELECT * FROM " . $this->table . " WHERE project_id = :project_id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':project_id', $invitation['project_id']);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                // Add user to project only if they're not already a member
                $query = "INSERT INTO " . $this->table . " (project_id, user_id) VALUES (:project_id, :user_id)";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':project_id', $invitation['project_id']);
                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            error_log("Error accepting invitation: " . $e->getMessage());
            return false;
        }
    }

    public function getInvitedProjects($userEmail) {
        $query = "SELECT p.id, p.title, pi.token, pi.created_at 
          FROM projects p 
          JOIN " . $this->invitationsTable . " pi ON p.id = pi.project_id 
          WHERE pi.email = :email AND pi.status = 'pending'";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching invited projects: " . $e->getMessage());
            return [];
        }
    }
    public function getAcceptedProjects($userId) {
        $query = "SELECT p.id, p.title 
              FROM projects p 
              JOIN " . $this->table . " pm ON p.id = pm.project_id 
              WHERE pm.user_id = :user_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching accepted projects: " . $e->getMessage());
            return [];
        }
    }
    public function removeUserFromProject($userId, $projectId) {
        $sql = "DELETE FROM " . $this->table . " WHERE user_id = :user_id AND project_id = :project_id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':project_id', $projectId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error removing user from project: " . $e->getMessage());
            return false;
        }
    }
}