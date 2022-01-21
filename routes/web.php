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

$router->get('shops', 'ShopController@index');
$router->get('shops/{id}', 'ShopController@show');

$router->get('shops/{id}/products', 'ShopController@getProducts');
$router->get('shops/{id}/products/{productId}', 'ShopController@getProduct');

$router->get('products', 'ProductController@index');
$router->get('products/{id}', 'ProductController@show');

$router->get('orders', 'OrderController@getOrdersByUserLogin');
$router->post('orders', 'OrderController@addOrderByUser');
$router->delete('orders/{id}', 'OrderController@deleteOrderByUser');

$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->get('vouchers', 'VoucherController@getVoucherByBuyerLogin');

    $router->group(['prefix'=>'admin'], function () use ($router) {
        $router->get('users', 'BuyerController@index');
        $router->get('users/{id}', 'BuyerController@show');
        $router->put('users/{id}', 'BuyerController@update');
        $router->delete('users/{id}', 'BuyerController@destroy');

        $router->get('shops', 'ShopController@index');
        $router->get('shops/{id}', 'ShopController@show');
        $router->post('shops', 'ShopController@store');
        $router->put('shops/{id}', 'ShopController@update');
        $router->delete('shops/{id}', 'ShopController@destroy');
        $router->get('shops/{id}/products', 'ShopController@getProducts');
        $router->post('shops/{id}/products', 'ShopController@addProduct');
        $router->get('shops/{id}/products/{productId}', 'ShopController@getProduct');
        $router->delete('shops/{id}/products/{productId}', 'ShopController@removeProduct');
        

        $router->get('products', 'ProductController@index');
        $router->get('products/{id}', 'ProductController@show');
        $router->post('products', 'ProductController@store');
        $router->put('products/{id}', 'ProductController@update');
        $router->delete('products/{id}', 'ProductController@destroy');

        $router->get('vouchers', 'VoucherController@index');
        $router->get('vouchers/{id}', 'VoucherController@show');
        $router->post('vouchers', 'VoucherController@store');
        $router->put('vouchers/{id}', 'VoucherController@update');
        $router->delete('vouchers/{id}', 'VoucherController@destroy');
        $router->post('vouchers/buyers/{buyerId}/{id}', 'VoucherController@addVoucherToBuyer');
        $router->delete('vouchers/buyers/{buyerId}/{id}', 'VoucherController@removeVoucherFromBuyer');
        $router->get('vouchers/buyers/{buyerId}', 'VoucherController@getVouchersByBuyer');

        $router->get('orders', 'OrderController@index');
        $router->get('orders/{id}', 'OrderController@show');
        $router->get('orders/buyers/{buyerId}', 'OrderController@getOrdersByBuyer');
    });
});
