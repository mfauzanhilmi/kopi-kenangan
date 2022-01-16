<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    $data = [
        'version' => $router->app->version(),
        'description' => 'Kopi Kenangan API',
        'author' => [
            [
                'name' => 'Dwika Cahya Febriana',
                'nim' => 'D111911026',
            ],
            [
                'name' => 'Muhammad Fauzan Hilmi',
                'nim' => 'D111911027',
            ],
            [
                'name' => 'Firman Mardiyanto',
                'nim' => 'D111911034',
            ]
        ],];

        return response()->json($data, 200);
});

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('register', 'AuthController@register');
    $router->post('confirmation/{token}', 'AuthController@confirmation');
    $router->post('passwords/forgot', 'AuthController@forgotPassword');
    $router->post('passwords/confirmation/{forgotToken}', 'AuthController@resetPassword');
    $router->post('logout', 'AuthController@logout');
});

$router->group(['prefix'=>'admin'], function () use ($router) {
    $router->post('users', 'BuyerController@store');
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix'=>'admin'], function () use ($router) {
        $router->get('users', 'BuyerController@index');
        $router->get('users/{id}', 'BuyerController@show');
        $router->put('users/{id}', 'BuyerController@update');
        $router->delete('users/{id}', 'BuyerController@destroy');
    });
});
