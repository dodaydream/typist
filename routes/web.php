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
$router->get('/posts', 'PostController@getPosts');

// Only visible by admin
$router->group(['prefix' => 'admin'], function () use ($router) {
    $router->group(['prefix' => 'post'], function () use ($router) {
        $router->post('/', 'PostController@createPost');
        $router->put('/{id:[0-9]+}', 'PostController@updatePost');
        $router->delete('/{id:[0-9]+}', 'PostController@deletePost');
        $router->get('/trashed/{id:[0-9]+}', 'PostController@getTrashedPostById');
        $router->put('/trashed/{id:[0-9]+}', 'PostController@restorePost');
    });
    $router->get('/posts/trashed', 'PostController@getTrashedPosts');
});
