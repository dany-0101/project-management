<?php

require_once __DIR__ . '/../vendor/autoload.php';

class MockDatabase {
    private $lastInsertId = 0;
    public $transactionActive = false;

    public function prepare($query) {
        return new MockStatement($query, $this);
    }

    public function lastInsertId() {
        return $this->lastInsertId;
    }

    public function setLastInsertId($id) {
        $this->lastInsertId = $id;
    }

    public function beginTransaction() {
        $this->transactionActive = true;
        return true;
    }

    public function commit() {
        $this->transactionActive = false;
        return true;
    }

    public function rollBack() {
        $this->transactionActive = false;
        return true;
    }
}

class MockStatement {
    private $query;
    private $db;
    private $params = [];

    public function __construct($query, $db) {
        $this->query = $query;
        $this->db = $db;
    }

    public function bindParam($param, &$value, $type = null) {
        $this->params[$param] = $value;
    }

    public function execute($params = null) {
        if ($params !== null) {
            $this->params = array_merge($this->params, $params);
        }
        if (strpos($this->query, 'INSERT INTO') === 0) {
            $this->db->setLastInsertId($this->db->lastInsertId() + 1);
        }
        return true;
    }

    public function fetch($fetch_style = null) {
        // Simulate fetching data based on the query
        if (strpos($this->query, 'SELECT') === 0) {
            if (strpos($this->query, 'FROM users') !== false) {
                return ['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com'];
            } else {
                return ['id' => 1, 'title' => 'Test Project', 'user_id' => 1];
            }
        }
        return false;
    }

    public function fetchAll($fetch_style = null) {
        // Simulate fetching multiple rows
        return [
            ['id' => 1, 'title' => 'Project 1', 'user_id' => 1, 'board_count' => 2],
            ['id' => 2, 'title' => 'Project 2', 'user_id' => 1, 'board_count' => 1]
        ];
    }

    public function rowCount() {
        return 1; // Simulate that one row was affected
    }

    public function errorInfo() {
        return [null, null, null]; // No error
    }
}

function runTest($testName, $testFunction) {
    try {
        $testFunction();
        echo "✅ {$testName} passed\n";
    } catch (Exception $e) {
        echo "❌ {$testName} failed: {$e->getMessage()}\n";
    }
}

// Include your test files here
require_once __DIR__ . '/UserTest.php';
require_once __DIR__ . '/ProjectTest.php';

// Run your tests
echo "Running tests...\n";
runUserTests();
runProjectTests();
echo "Tests completed.\n";