<?php
namespace Models;

class Profile {
    private $db;
    public $user_id;

    public function __construct($db) {
        $this->db = $db;
    }



    public function update($data, $photo = null) {
        $sql = "UPDATE users SET name = ?, email = ?";
        $params = [$data['name'], $data['email']];

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if ($photo !== null) {
            $sql .= ", photo = ?";
            $params[] = $photo;
        }

        $sql .= " WHERE id = ?";
        $params[] = $this->user_id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}