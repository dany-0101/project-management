<?php

use Models\User;
use Models\Project;
use Models\ProjectMember;

function runUserProjectIntegrationTests() {
    $db = new MockDatabase();

    runTest('Associate User with Project', function() use ($db) {
        $user = new User($db);
        $user->id = 1;
        $user->name = 'John Doe';
        $user->email = 'john@example.com';

        $project = new Project($db);
        $project->id = 1;
        $project->name = 'Test Project';
        $project->description = 'This is a test project';

        $projectMember = new ProjectMember($db);
        $projectMember->user_id = $user->id;
        $projectMember->project_id = $project->id;

        assert($projectMember->user_id === $user->id, 'User ID does not match');
        assert($projectMember->project_id === $project->id, 'Project ID does not match');
    });

    runTest('Remove User from Project', function() use ($db) {
        $projectMember = new ProjectMember($db);
        $projectMember->user_id = 1;
        $projectMember->project_id = 1;

        $result = $projectMember->delete();
        assert($result === true, 'Failed to remove user from project');
    });

    runTest('List Project Members', function() use ($db) {
        $project = new Project($db);
        $project->id = 1;

        $members = $project->getMembers();
        assert(is_array($members), 'Project members should be an array');
        assert(count($members) > 0, 'Project should have at least one member');
    });
}