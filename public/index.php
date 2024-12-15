<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../routes/Routes.php';

require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../controllers/ProfileController.php';

use Config\Database;
use Routes\Router;
use Routes\Routes;



$database = new Database();
$db = $database->connect();


$router = new Router();

new Routes($router, $db);


$router->run();