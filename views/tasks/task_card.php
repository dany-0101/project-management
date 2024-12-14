<div class="card mb-3" draggable="true" data-task-id="<?= $task['id'] ?>">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($task['title']) ?></h5>
        <p class="card-text"><?= htmlspecialchars($task['description']) ?></p>
        <p>Priority: <?= htmlspecialchars($task['priority']) ?></p>
        <p>Due Date: <?= htmlspecialchars($task['due_date']) ?></p>
        <a href="/tasks/view/<?= $task['id'] ?>" class="btn btn-info btn-sm">View</a>
        <form action="/tasks/delete" method="post" class="d-inline">
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?')">Delete</button>
        </form>
    </div>
</div>