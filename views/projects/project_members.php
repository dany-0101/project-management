<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Project Members</h5>
    </div>
    <div class="card-body">
        <!-- Invite User Button -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#inviteUserModal">
            Invite User
        </button>


<!-- Invite User Modal -->
<div class="modal fade" id="inviteUserModal" tabindex="-1" aria-labelledby="inviteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inviteUserModalLabel">Invite User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/projects/invite" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">User Email</label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Invite</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Project Members</h5>
    </div>





            <!-- Project Members List -->
            <ul class="list-group">
                <?php if (isset($creator)): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($creator['name']) ?>
                        <span class="text-muted">(<?= htmlspecialchars($creator['email']) ?>)</span>
                        <span class="badge bg-primary">Creator</span>
                    </li>
                <?php endif; ?>

                <?php if (!empty($members)): ?>
                    <?php foreach ($members as $member): ?>
                        <?php if ($member['id'] != $creator['id']): ?>
                            <li class="list-group-item">
                                <?= htmlspecialchars($member['name']) ?>
                                <span class="text-muted">(<?= htmlspecialchars($member['email']) ?>)</span>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <?php if (empty($members) && !isset($creator)): ?>
                <p>No members in this project yet.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>
</div>