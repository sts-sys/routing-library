<?php
require_once 'autoload.php';

use sts\routes\Router;

$router = new Router();

// Definirea unei rute GET
$router->get('/home', function () {
    echo 'Welcome to the home page!';
});

// Definirea unei rute POST
$router->post('/login', 'AuthController@login');

// Definirea unui grup de rute pentru panoul de administrare
$router->group('/admin', function ($router) {
    $router->middleware('AuthMiddleware')
           ->get('/dashboard', 'AdminController@dashboard')
           ->name('admin.dashboard');

    $router->post('/users', 'AdminController@createUser')
           ->name('admin.createUser');
});

// Dispatcher automat
$router->dispatch();
