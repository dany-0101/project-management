<?php

use Models\Project;

function runProjectTests() {
    $db = new MockDatabase();

    runTest('Create Project', function() use ($db) {
        $project = new Project($db);
        $projectData = [
            'title' => 'New Project',
            'user_id' => 1
        ];
        $result = $project->create($projectData);
        assert($result !== false, 'Failed to create project');
        assert(is_numeric($result), 'Project ID should be numeric');
        echo "Project created successfully with ID: " . $result . "\n";
    });

    runTest('Update Project', function() use ($db) {
        $project = new Project($db);
        $projectData = [
            'project_id' => 1,
            'title' => 'Updated Project'
        ];
        $_SESSION['user_id'] = 1; // Simulate logged-in user
        $result = $project->update($projectData);
        assert($result === true, 'Failed to update project');
    });

    runTest('Delete Project', function() use ($db) {
        $project = new Project($db);
        $projectId = 1;
        $_SESSION['user_id'] = 1; // Simulate logged-in user
        $result = $project->delete($projectId);
        assert($result === true, 'Failed to delete project');
    });

    runTest('Get Project by ID', function() use ($db) {
        $project = new Project($db);
        $projectId = 1;
        $result = $project->getProjectById($projectId);
        assert($result !== false, 'Failed to get project by ID');
        assert(isset($result['title']), 'Project should have a title');
    });

    runTest('Get User Projects', function() use ($db) {
        $project = new Project($db);
        $userId = 1;
        $result = $project->getUserProjects($userId);
        assert(is_array($result), 'getUserProjects should return an array');
    });

    runTest('Get Project Creator', function() use ($db) {
        $project = new Project($db);
        $projectId = 1;
        $result = $project->getProjectCreator($projectId);
        assert($result !== false, 'Failed to get project creator');
        assert(isset($result['name']), 'Project creator should have a name');
    });
}