<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$basePath = '/project-management/public';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        .navbar .dropdown-menu {
            z-index: 1030;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo $basePath; ?>">Easy plan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/dashboard">Dashboard</a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (isset($_SESSION['user_photo']) && !empty($_SESSION['user_photo'])): ?>
                                <img src="<?php echo BASE_URL . $_SESSION['user_photo']; ?>" alt="Profile Photo" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                    <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User'; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">Edit Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/auth/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $basePath; ?>/auth/register">Register</a>
                    </li>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Profile Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo BASE_URL; ?>/profile/update" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>