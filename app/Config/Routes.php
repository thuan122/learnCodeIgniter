<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

/**
 * Normal route: $routes-><route type>('URI', 'Controller::function', filter/middleware (need to be registered through App\Config\Filters))
 * 
 * Group route: 
 * $routes->group('URI prefix', 
 *                ['namespace' => 'App\Controllers\<your folder>]
 *                (if the folder that contain controllers does not directly inside App\Controllers, for example App\Controllers\Api),
 *                  function ($routes) {
 *                      write like normal routes again
 *                  }
 *                  
 */

service('auth')->routes($routes);

$routes->group('api', static function ($routes) {
    $routes->post('register', 'AuthController::register');
    $routes->post('login', 'AuthController::login');
    $routes->get('profile', 'AuthController::profile', ['filter' => 'apiauth']);
    $routes->get('logout', 'AuthController::logout', ['filter' => 'apiauth']);

    $routes->post('add-project', 'ProjectController::addProject', ['filter' => 'apiauth']);
    $routes->get('list-projects', 'ProjectController::listProjects', ['filter' => 'apiauth']);
    $routes->delete('delete-project/(:any)', 'ProjectController::deleteProject/$1', ['filter' => 'apiauth']);

    $routes->get('invalid', 'AuthController::invalidRequest');
});
