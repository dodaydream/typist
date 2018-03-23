<?php

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

// Publically Visible
$router->get('/post/{id:[0-9]+}', 'PostController@getPostById');
$router->get('/posts/{page:[0-9]+}', 'PostController@getPosts');
$router->get('/posts/category/{categoryId:[0-9]+}/{page:[0-9]+}', 'PostController@getPostsByCategoryId');
$router->get('/categories', 'CategoryController@getCategories');

/**
 * Users
 */

// User
$router->group(['prefix' => 'user'], function () use ($router) {
	$router->get('/{id:[0-9]+}', 'UserController@retriveUser');
    $router->put('/{id:[0-9]+}', 'UserController@updateUser');
    $router->delete('/{id:[0-9]+}', 'UserController@deleteUser');
});
// User Collection
$router->group(['prefix' => 'users'], function () use ($router) {
    $router->post('/', 'UserController@createUser');
    $router->get('/', 'UserController@listAllUsers');
});
// Token
$router->post('/token', 'TokenController@getToken');

$router->group(['prefix' => 'post'], function () use ($router) {
    $router->post('/', 'PostController@createPost');
    $router->put('/{id:[0-9]+}', 'PostController@updatePost');
    $router->delete('/{id:[0-9]+}', 'PostController@deletePost');
    $router->get('/trashed/{id:[0-9]+}', 'PostController@getTrashedPostById');
    $router->put('/trashed/{id:[0-9]+}', 'PostController@restorePost');
});

$router->get('/posts/trashed/{id:[0-9]+}', 'PostController@getTrashedPosts');

$router->group(['prefix' => 'category'], function () use ($router) {
    $router->post('/', 'CategoryController@createCategory');
    $router->put('/{id:[0-9]+}', 'CategoryController@updateCategory');
    $router->delete('/{id:[0-9]+}', 'CategoryController@deleteCategory');
});

$router->group(['prefix' => 'comment'], function () use ($router) {

});
