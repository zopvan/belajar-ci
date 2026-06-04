<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// $routes->group('/', ['filter' => 'auth'], function ($routes){
//     $routes->get('', 'Home::index');
//     $routes->get('/profile', 'ProfileController::index');
// });

$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->group('product', ['filter' => 'auth'], function ($routes) {
    $routes->get('', 'ProductController::index');
    $routes->post('', 'ProductController::create');
    $routes->post('edit/(:any)', 'ProductController::edit/$1');
    $routes->get('delete/(:any)', 'ProductController::delete/$1');
});

$routes->get('keranjang', 'TransaksiController::index', ['filter' => 'auth']);

$routes->get('profile', 'ProfileController::index', ['filter' => 'auth']);
$routes->get('faq', 'Home::faq', ['filter' => 'auth']);
$routes->get('contact', 'Home::contact', ['filter' => 'auth']);
