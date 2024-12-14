<div class="container mt-4">
    <h1 class="mb-4">Invited Projects</h1>
    <?php if (!empty($invitedProjects)): ?>
        <h2>Invited Projects</h2>
        <ul>
            <?php foreach ($invitedProjects as $project): ?>
                <li>
                    <?= htmlspecialchars($project['title']) ?>
                    <a href="<?= BASE_URL ?>/projects/accept-invitation/<?= $project['token'] ?>">Accept Invitation</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

