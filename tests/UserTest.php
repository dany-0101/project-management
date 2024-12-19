<?php

use Models\User;
use Config\Database;

function runUserTests() {
    $db = (new Database())->connect();

    runTest('User Creation', function() use ($db) {
        $user = new User($db);
        $user->name = 'John Doe';
        $user->email = 'john@example.com';
        $user->password = password_hash('password123', PASSWORD_DEFAULT);

        assert($user->name === 'John Doe', 'User name does not match');
        assert($user->email === 'john@example.com', 'User email does not match');
    });

    runTest('User Password Verification', function() use ($db) {
        $user = new User($db);
        $user->name = 'Jane Doe';
        $user->email = 'jane@example.com';
        $user->password = password_hash('password123', PASSWORD_DEFAULT);

        assert(password_verify('password123', $user->password), 'Password verification failed');
    });
}