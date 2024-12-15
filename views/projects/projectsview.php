<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project View</title>
    <link href="/project-management/assets/css/projectsview.css" rel="stylesheet">
</head>
<body>

<?php include __DIR__ . '/../layouts/header.php'; ?>
<?php
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>
    <!-- Include the project members section -->
<?php include __DIR__ . '/project_members.php'; ?>


    <div class="container-fluid mt-4">
        <h1><?php echo htmlspecialchars($project['title']); ?></h1>

        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addStatusModal">
            Add New Status
        </button>

        <div class="row">
            <?php foreach ($statuses as $status): ?>
                <div class="col-md-3 mb-4">
                    <div class="card status-column" data-status-id="<?php echo $status['id']; ?>">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><?php echo htmlspecialchars($status['name']); ?></h5>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $status['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                    ⋮
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $status['id']; ?>">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editStatusModal<?php echo $status['id']; ?>">Edit</a></li>
                                    <li>
                                        <form action="<?php echo BASE_URL; ?>/statuses/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this status and all its tasks?');">
                                            <input type="hidden" name="status_id" value="<?php echo $status['id']; ?>">
                                            <input type="hidden" name="board_id" value="<?php echo $board['id']; ?>">
                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                        </form>
                                    </li>
                                </ul>

                            </div>
                        </div>

                        <div class="card-body">
                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addTaskModal<?php echo $status['id']; ?>">
                                Add Task
                            </button>

                            <?php
                            $statusTasks = array_filter($tasks, function($task) use ($status) {
                                return $task['status_id'] == $status['id'];
                            });
                            usort($statusTasks, function($a, $b) {
                                return $a['position'] - $b['position'];
                            });
                            foreach ($statusTasks as $task):
                                ?>
                                <div class="card mb-2 task-card" draggable="true" data-task-id="<?php echo $task['id']; ?>" data-position="<?php echo $task['position'] ?? 0; ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="card-title"><?php echo htmlspecialchars($task['title']); ?></h6>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $task['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                    ⋮
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $task['id']; ?>">
                                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editTaskModal<?php echo $task['id']; ?>">Edit</a></li>
                                                    <li>
                                                        <form action="<?php echo BASE_URL; ?>/tasks/delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                            <input type="hidden" name="board_id" value="<?php echo $board['id']; ?>">
                                                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <p class="card-text"><?php echo htmlspecialchars($task['description']); ?></p>
                                        <p class="card-text"><small class="text-muted">Priority: <?php echo ucfirst($task['priority']); ?></small></p>
                                        <p class="card-text"><small class="text-muted">Due: <?php echo $task['due_date']; ?></small></p>
                                    </div>
                                </div>

                                <!-- Edit Task Modal -->
                                <div class="modal fade" id="editTaskModal<?php echo $task['id']; ?>" tabindex="-1" aria-labelledby="editTaskModalLabel<?php echo $task['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editTaskModalLabel<?php echo $task['id']; ?>">Edit Task</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="<?php echo BASE_URL; ?>/tasks/update" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                                    <input type="hidden" name="board_id" value="<?php echo $board['id']; ?>">
                                                    <input type="hidden" name="status_id" value="<?php echo $task['status_id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="editTaskTitle<?php echo $task['id']; ?>" class="form-label">Task Title</label>
                                                        <input type="text" class="form-control" id="editTaskTitle<?php echo $task['id']; ?>" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editTaskDescription<?php echo $task['id']; ?>" class="form-label">Description</label>
                                                        <textarea class="form-control" id="editTaskDescription<?php echo $task['id']; ?>" name="description"><?php echo htmlspecialchars($task['description']); ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editTaskPriority<?php echo $task['id']; ?>" class="form-label">Priority</label>
                                                        <select class="form-control" id="editTaskPriority<?php echo $task['id']; ?>" name="priority">
                                                            <option value="low" <?php echo $task['priority'] == 'low' ? 'selected' : ''; ?>>Low</option>
                                                            <option value="medium" <?php echo $task['priority'] == 'medium' ? 'selected' : ''; ?>>Medium</option>
                                                            <option value="high" <?php echo $task['priority'] == 'high' ? 'selected' : ''; ?>>High</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="editTaskDueDate<?php echo $task['id']; ?>" class="form-label">Due Date</label>
                                                        <input type="date" class="form-control" id="editTaskDueDate<?php echo $task['id']; ?>" name="due_date" value="<?php echo $task['due_date']; ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Update Task</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Add Task Modal for each status -->
                <div class="modal fade" id="addTaskModal<?php echo $status['id']; ?>" tabindex="-1" aria-labelledby="addTaskModalLabel<?php echo $status['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTaskModalLabel<?php echo $status['id']; ?>">Add Task to <?php echo htmlspecialchars($status['name']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?php echo BASE_URL . '/tasks/create'; ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="taskTitle<?php echo $status['id']; ?>" class="form-label">Task Title</label>
                                        <input type="text" class="form-control" id="taskTitle<?php echo $status['id']; ?>" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="taskDescription<?php echo $status['id']; ?>" class="form-label">Description</label>
                                        <textarea class="form-control" id="taskDescription<?php echo $status['id']; ?>" name="description"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="taskPriority<?php echo $status['id']; ?>" class="form-label">Priority</label>
                                        <select class="form-select" id="taskPriority<?php echo $status['id']; ?>" name="priority">
                                            <option value="low">Low</option>
                                            <option value="medium" selected>Medium</option>
                                            <option value="high">High</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="taskDueDate<?php echo $status['id']; ?>" class="form-label">Due Date</label>
                                        <input type="date" class="form-control" id="taskDueDate<?php echo $status['id']; ?>" name="due_date">
                                    </div>
                                    <input type="hidden" name="status_id" value="<?php echo $status['id']; ?>">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                    <input type="hidden" name="board_id" value="<?php echo $board['id']; ?>">
                                    <button type="submit" class="btn btn-primary">Create Task</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Status Modal -->
                <div class="modal fade" id="editStatusModal<?php echo $status['id']; ?>" tabindex="-1" aria-labelledby="editStatusModalLabel<?php echo $status['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editStatusModalLabel<?php echo $status['id']; ?>">Edit Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="<?php echo BASE_URL; ?>/statuses/update" method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="status_id" value="<?php echo $status['id']; ?>">
                                    <input type="hidden" name="board_id" value="<?php echo $board['id']; ?>">
                                    <div class="mb-3">
                                        <label for="editStatusName<?php echo $status['id']; ?>" class="form-label">Status Name</label>
                                        <input type="text" class="form-control" id="editStatusName<?php echo $status['id']; ?>" name="name" value="<?php echo htmlspecialchars($status['name']); ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Status</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- Add Status Modal -->
    <div class="modal fade" id="addStatusModal" tabindex="-1" aria-labelledby="addStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStatusModalLabel">Add New Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo BASE_URL; ?>/statuses/create" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="board_id" value="<?php echo $board['id']; ?>">
                        <div class="mb-3">
                            <label for="statusName" class="form-label">Status Name</label>
                            <input type="text" class="form-control" id="statusName" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const statusColumns = document.querySelectorAll('.card-body');
            statusColumns.forEach(column => {
                new Sortable(column, {
                    group: 'shared',
                    animation: 150,
                    draggable: '.card.mb-2', // Update this selector to match your task cards
                    onEnd: function (evt) {
                        const taskId = evt.item.querySelector('input[name="task_id"]').value;
                        const newStatusId = evt.to.closest('.card').querySelector('input[name="status_id"]').value;
                        const newPosition = Array.from(evt.to.children).indexOf(evt.item);


                        fetch('<?php echo BASE_URL; ?>/tasks/updateStatus', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                task_id: taskId,
                                new_status_id: newStatusId,
                                new_position: newPosition
                            }),
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    console.log('Task updated successfully');
                                } else {
                                    console.error('Failed to update task');

                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);

                            });
                    }
                });
            });
        });
    </script>
</body>
</html>