<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'app/core/Router.php';

use App\Core\Router;

$router = new Router();

$router->add('GET', '/login', 'AuthController', 'loginView');
$router->add('POST', '/login', 'AuthController', 'loginPost');
$router->add('GET', '/register', 'AuthController', 'registerView');
$router->add('POST', '/register', 'AuthController', 'registerPost');
$router->add('GET', '/logout', 'AuthController', 'logout');

$router->add('GET', '/', 'HomeController', 'index'); 
$router->add('GET', '/profile', 'ProfileController', 'index'); 
$router->add('GET', '/streak', 'StreakController', 'index'); 

$router->add('GET', '/class', 'ClassController', 'index'); 
$router->add('GET', '/class/create', 'ClassController', 'create');
$router->add('POST', '/class', 'ClassController', 'store'); 
$router->add('POST', '/class/{id}/complete', 'ClassController', 'complete');
$router->add('GET', '/class/{id}/edit', 'ClassController', 'edit');
$router->add('PUT', '/class/{id}', 'ClassController', 'update');
$router->add('DELETE', '/class/{id}', 'ClassController', 'destroy');
$router->add('POST', '/profile/update', 'ProfileController', 'update');

$router->run();