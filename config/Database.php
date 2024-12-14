<?php
namespace Config;
define('BASE_URL', 'http://localhost/project-management/public');

class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function __construct() {
        // Define the path to .env file relative to the config directory
        $envFile = __DIR__ . '/../.env';

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    switch ($key) {
                        case 'DB_HOST':
                            $this->host = $value;
                            break;
                        case 'DB_USER':
                            $this->user = $value;
                            break;
                        case 'DB_PASS':
                            $this->pass = $value;
                            break;
                        case 'DB_NAME':
                            $this->dbname = $value;
                            break;
                    }
                }
            }
        }
    }

    public function connect() {
        try {
            $this->conn = new \PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->user,
                $this->pass
            );
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch(\PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            return null;
        }
    }
}