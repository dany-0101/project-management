<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
<div class="card mb-3">
    <div class="card-body">

        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#inviteUserModal">
            Invite User
        </button>
        <!-- Project Members List -->
        <div class="mb-3">
            <h5>The team</h5>
            <?php if (empty($members)): ?>
                <p>No members in this project yet.</p>
            <?php else: ?>
                <ul class="list-unstyled">
                    <?php foreach ($members as $member): ?>
                        <li class="d-flex align-items-center mb-2">
                            <div class="me-2">
                                <?php
                                $displayName = htmlspecialchars($member['name'] ?? 'Pending');
                                $email = htmlspecialchars($member['email']);
                                echo "{$displayName} ({$email})";
                                ?>
                            </div>
                            <?php if ($member['is_creator']): ?>
                                <span class="badge bg-primary me-2">Creator</span>
                            <?php endif; ?>
                            <?php if ($member['status'] === 'pending'): ?>
                                <span class="badge bg-warning me-2">Pending</span>
                            <?php endif; ?>
                            <?php if (!$member['is_creator'] && $member['id'] !== $_SESSION['user_id']): ?>
                                <button class="btn btn-danger btn-sm remove-member"
                                        data-member-id="<?php echo $member['id']; ?>"
                                        data-member-status="<?php echo $member['status']; ?>">
                                    <i class="fas fa-times"></i>
                                </button>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

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

            <?php if (empty($members) && !isset($creator)): ?>
                <p>No members in this project yet.</p>
            <?php endif; ?>


<script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/js/all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const removeButtons = document.querySelectorAll('.remove-member');
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const memberId = this.getAttribute('data-member-id');
                const memberStatus = this.getAttribute('data-member-status');
                const action = memberStatus === 'pending' ? 'cancel the invitation for' : 'remove';
                if (confirm(`Are you sure you want to ${action} this member from the project?`)) {
                    removeMember(memberId, memberStatus);
                }
            });
        });

        function removeMember(memberId, memberStatus) {
            const endpoint = memberStatus === 'pending' ? '/projects/cancel-invitation' : '/projects/remove-member';
            fetch('<?php echo BASE_URL; ?>' + endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    project_id: <?php echo $project['id']; ?>,
                    member_id: memberId
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        console.error('Server responded with an error:', data.message);
                        alert('Failed to remove member or cancel invitation. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }
    });
</script>

</body>
</html>
