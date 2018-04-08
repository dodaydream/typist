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

/**
 * Users
 */
$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('users/', 'UserController@listAllUsers');
    $router->put('user/{id:[0-9]+}', 'UserController@updateUser');
    $router->delete('user/{id:[0-9]+}', 'UserController@deleteUser');
    $router->post('posts/', 'PostController@createPost');
});
$router->get('user/{id:[0-9]+}', 'UserController@retriveUser');
$router->post('users/', 'UserController@createUser');
$router->post('user/token', 'TokenController@createToken');

$router->group(['prefix' => 'post', 'middleware' => 'auth'], function () use ($router) {
    $router->put('/{id:[0-9]+}', 'PostController@updatePost');
    $router->delete('/{id:[0-9]+}', 'PostController@deletePost');
    $router->get('/trashed/{id:[0-9]+}', 'PostController@retriveTrashedPost');
    $router->put('/trashed/{id:[0-9]+}', 'PostController@restoreTrashedPost');
    $router->delete('/trashed/{id:[0-9]+}', 'PostController@deleteTrashedPost');
});

$router->get('/post/{id:[0-9]+}', 'PostController@retrivePost');
$router->get('/posts[/{page:[0-9]+}/filter/{filter}/id/{id}]', 'PostController@listPosts');
$router->get('/post/{id:[0-9]+}/revisions', 'RevisionController@getRevisionsByPostId');
$router->put('/post/{post_id:[0-9]+}/revision/{rev_id:[0-9]+}', 'RevisionController@rollbackRevision');
$router->get('/posts/trashed/{id:[0-9]+}', 'PostController@listTrashedPosts');
$router->delete('/posts/trashed[/filter{filter}/id/{id}]', 'PostController@deleteTrashedPosts');

$router->get('/categories', 'CategoryController@listCategories');
$router->post('/categories', 'CategoryController@createCategory');
$router->group(['prefix' => 'category'], function () use ($router) {
    $router->put('/{id:[0-9]+}', 'CategoryController@updateCategory');
    $router->delete('/{id:[0-9]+}', 'CategoryController@deleteCategory');
});

$router->group(['prefix' => 'comment'], function () use ($router) {

});
