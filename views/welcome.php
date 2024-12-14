<?php

$page_title = 'Welcome to Project Management System';
include __DIR__ . '/layouts/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0061f2 0%, #00a6e6 100%);
            color: white;
            padding: 100px 0;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0061f2;
            margin-bottom: 1rem;
        }
        .auth-buttons .btn {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }
        .feature-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-4 mb-4">Manage Your Projects with Ease</h1>
        <p class="lead mb-5">A comprehensive project management solution with Kanban boards, sprint planning, and real-time collaboration</p>
        <div class="auth-buttons">
            <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-light btn-lg me-3">Get Started</a>
            <a href="<?php echo BASE_URL; ?>/auth/login" class="btn btn-outline-light btn-lg">Sign In</a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Key Features</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h5 class="card-title">Kanban Board</h5>
                        <p class="card-text">Visualize your workflow with customizable Kanban boards. Drag and drop tasks to update their status.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="card-title">Sprint Planning</h5>
                        <p class="card-text">Plan and track sprints effectively. Monitor progress with burndown charts and sprint metrics.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="card-title">Team Collaboration</h5>
                        <p class="card-text">Real-time updates, comments, and notifications keep your team in sync and productive.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4">
                <h2 class="display-4 text-primary">1000+</h2>
                <p class="lead">Active Users</p>
            </div>
            <div class="col-md-4">
                <h2 class="display-4 text-primary">5000+</h2>
                <p class="lead">Projects Managed</p>
            </div>
            <div class="col-md-4">
                <h2 class="display-4 text-primary">98%</h2>
                <p class="lead">Customer Satisfaction</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="mb-4">Ready to Get Started?</h2>
        <p class="lead mb-4">Join thousands of teams already using our project management solution</p>
        <a href="<?php echo BASE_URL; ?>/auth/register" class="btn btn-primary btn-lg">Create Free Account</a>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-light py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>Project Management System</h5>
                <p>Simplify your project management workflow</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="text-light me-3">Terms of Service</a>
                <a href="#" class="text-light me-3">Privacy Policy</a>
                <a href="#" class="text-light">Contact</a>
            </div>
        </div>
    </div>
</footer>


</body>
</html>