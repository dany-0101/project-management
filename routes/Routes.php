<?php

namespace Routes;

use Controllers\AuthController;
use Controllers\PasswordResetController;
use Controllers\ProfileController;
use Controllers\ProjectController;
use Controllers\BoardController;
use Controllers\StatusController;
use Controllers\TaskController;
use Controllers\ProjectMemberController;
use Controllers\DashboardController;
use Middleware\AuthMiddleware;



class Router {
    public function debug() {
        echo "<pre>";
        print_r($this->routes);
        echo "</pre>";
        echo "Current path: " . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    private $routes = [];
    private $notFoundCallback;

    public function get($path, $callback, $middleware = null) {
        $this->addRoute('GET', $path, $callback, $middleware);
    }

    public function post($path, $callback, $middleware = null) {
        $this->addRoute('POST', $path, $callback, $middleware);
    }

    private function addRoute($method, $path, $callback, $middleware) {
        $this->routes[$method][$path] = [
            'callback' => $callback,
            'middleware' => $middleware
        ];
    }
    public function set404($callback) {
        $this->notFoundCallback = $callback;
    }

    public function run() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = '/project-management/public';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        $path = rtrim($path, '/');
        if (empty($path)) $path = '/';

        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = '@^' . $pattern . '$@D';

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if ($handler['middleware']) {
                    if (is_array($handler['middleware'])) {
                        $middlewareClass = $handler['middleware'][0];
                        $middlewareMethod = $handler['middleware'][1];
                        $middlewareClass::$middlewareMethod();
                    } else {
                        $handler['middleware']::isAuthenticated();
                    }
                }

                call_user_func($handler['callback'], $params);
                return;
            }
        }

        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }
}
class Routes {
    private $router;
    private $db;

    public function __construct($router, $db) {
        $this->router = $router;
        $this->db = $db;
        $this->defineRoutes();
    }

    private function defineRoutes() {
        // Initialize controllers
        $auth = new AuthController($this->db);
        $project = new ProjectController($this->db);
        $board = new BoardController($this->db);
        $dashboard = new DashboardController($this->db);
        $status = new StatusController($this->db);
        $task = new TaskController($this->db);
        $projectMember = new ProjectMemberController($this->db);
        $profile = new ProfileController($this->db);
        $passwordReset = new PasswordResetController($this->db);
        // Define routes
        $this->router->get('/', function() {
            require __DIR__ . '/../views/welcome.php';
        });

        $this->router->get('/auth/login', function() use ($auth) {
            require __DIR__ . '/../views/auth/login.php';
        });

        $this->router->post('/auth/login', function() use ($auth) {
            $auth->login($_POST['email'], $_POST['password']);
        });

        $this->router->get('/auth/register', function() {
            require __DIR__ . '/../views/auth/register.php';
        });

        $this->router->post('/auth/register', function() use ($auth) {
            $auth->register($_POST);
        });

        $this->router->get('/auth/logout', function() use ($auth) {
            $auth->logout();
        });

        $this->router->get('/dashboard', function() use ($dashboard) {
            $dashboard->index();
        }, AuthMiddleware::class);

        $this->router->post('/projects/create', function() use ($project) {
            $project->create($_POST);
        }, AuthMiddleware::class);

        $this->router->post('/projects/delete', function() use ($project) {
            $project->delete($_POST['project_id']);
        }, AuthMiddleware::class);

        $this->router->post('/projects/update', function() use ($project) {
            $project->update($_POST['project_id'], $_POST['title']);
        }, AuthMiddleware::class);


        $this->router->get('/projects/view/{id}', function($params) use ($project) {
            $project->view($params['id']);
        }, AuthMiddleware::class);


        $this->router->post('/tasks/create', function() use ($task) {
            $task->create($_POST);
        }, AuthMiddleware::class);

        $this->router->post('/tasks/update', function() use ($task) {
            $task->update($_POST);
        }, AuthMiddleware::class);

        $this->router->post('/tasks/delete', function() use ($task) {
            $task->delete($_POST['task_id']);
        }, AuthMiddleware::class);

        $this->router->get('/tasks/view/{id}', function($params) use ($task) {
            $task->view($params['id']);
        }, AuthMiddleware::class);

        $this->router->post('/statuses/create', function() use ($status) {
            $status->create($_POST);
        }, AuthMiddleware::class);

        $this->router->post('/statuses/update', function() use ($status) {
            $status->update($_POST);
        }, AuthMiddleware::class);

        $this->router->post('/statuses/delete', function() use ($task) {
            $task->deleteStatus();
        }, AuthMiddleware::class);

        $this->router->post('/tasks/updateStatus', function() use ($task) {
            $task->updateStatus();
        }, AuthMiddleware::class);

        $this->router->post('/projects/invite', function() use ($projectMember) {
            $projectMember->inviteUser($_POST['project_id'], $_POST['email']);
        }, AuthMiddleware::class);

        $this->router->get('/projects/accept-invitation/{token}', function($params) use ($projectMember) {
            $projectMember->acceptInvitation($params['token']);
        });
        $this->router->get('/projects/invited', function() use ($projectMember) {
            $projectMember->showInvitedProjects();
        }, AuthMiddleware::class);

        // Add this line with your other routes
        $this->router->post('/projects/leave', function() use ($project) {
            $project->leave();
        }, AuthMiddleware::class);

        $this->router->post('/projects/reject-invitation', function() use ($projectMember) {
            $projectMember->rejectInvitation();
        }, AuthMiddleware::class);

        $this->router->get('/profile', function() use ($profile) {
            $profile->index();
        }, AuthMiddleware::class);

        $this->router->post('/profile/update', function() use ($profile) {
            $profile->update();
        }, AuthMiddleware::class);

        $this->router->post('/projects/remove-member', function() use ($projectMember) {
            $projectMember->removeMember();
        }, AuthMiddleware::class);
        $this->router->post('/projects/cancel-invitation', function() use ($projectMember) {
            $projectMember->cancelInvitation();
        }, AuthMiddleware::class);

        $this->router->get('/auth/forgot-password', function() use ($passwordReset) {
            $passwordReset->forgotPassword();
        });
        $this->router->post('/auth/send-reset-link', function() use ($passwordReset) {
            $passwordReset->sendResetLink();
        });
        $this->router->get('/auth/reset-password/{token}', function($params) use ($passwordReset) {
            $passwordReset->resetPassword($params['token']);
        });
        $this->router->post('/auth/reset-password/{token}', function($params) use ($passwordReset) {
            $passwordReset->resetPassword($params['token']);
        });

        $this->router->set404(function() {
            http_response_code(404);
            echo "404 Not Found";
        });
    }
}