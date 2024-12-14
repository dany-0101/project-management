<?php include __DIR__ . '/../../views/layouts/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">Dashboard</h1>

    <?php
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>
    <div class="container mt-4">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Create Project Form -->
        <div class="card mb-4">
            <div class="card-body">
                <a href="<?php echo BASE_URL; ?>/projects/invited">View Invited Projects</a>
                <h5 class="card-title">Create New Project</h5>
                <form action="<?= BASE_URL ?>/projects/create" method="POST">
                    <div class="mb-3">
                        <label for="projectName" class="form-label">Project Name</label>
                        <input type="text" class="form-control" id="projectName" name="name" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Project</button>
                </form>
            </div>
        </div>


        <!-- Project List -->
        <h2 class="mb-3">Your Projects</h2>
        <div class="row">
            <?php if (empty($allProjects)): ?>
                <div class="col-12">
                    <p>You don't have any projects yet. Create a new project or accept an invitation to get started.</p>
                </div>
            <?php else: ?>
                <?php foreach ($allProjects as $project): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h5 class="card-title"><?= htmlspecialchars($project['title']) ?></h5>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?= $project['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton<?= $project['id'] ?>">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#renameProjectModal<?= $project['id'] ?>">Rename</a></li>
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#deleteProjectModal<?= $project['id'] ?>">Delete</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <a href="<?php echo BASE_URL . '/projects/view/' . $project['id']; ?>" class="btn btn-primary mt-2">View Project</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Modals -->
        <?php foreach ($allProjects as $project): ?>
            <!-- Rename Project Modal -->
            <div class="modal fade" id="renameProjectModal<?= $project['id'] ?>" tabindex="-1" aria-labelledby="renameProjectModalLabel<?= $project['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="renameProjectModalLabel<?= $project['id'] ?>">Rename Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="<?= BASE_URL ?>/projects/update" method="POST">
                            <div class="modal-body">
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <div class="mb-3">
                                    <label for="projectTitle<?= $project['id'] ?>" class="form-label">Project Title</label>
                                    <input type="text" class="form-control" id="projectTitle<?= $project['id'] ?>" name="title" value="<?= htmlspecialchars($project['title']) ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Project Modal -->
            <div class="modal fade" id="deleteProjectModal<?= $project['id'] ?>" tabindex="-1" aria-labelledby="deleteProjectModalLabel<?= $project['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteProjectModalLabel<?= $project['id'] ?>">Delete Project</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this project? This action cannot be undone.
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="<?= BASE_URL ?>/projects/delete" method="POST">
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

