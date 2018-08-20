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
| TODO: Mistakenly spell retrieve as retrive
*/

$router->group(['middleware' => 'auth'], function () use ($router) {
    // User API
    $router->get('users/', 'UserController@listAllUsers');
    $router->post('users/', 'UserController@createUser');
    $router->put('user/{id:[0-9]+}', 'UserController@updateUser');
    $router->delete('user/{id:[0-9]+}', 'UserController@deleteUser');
    $router->put('user/token', 'TokenController@refreshToken');

    // Post API
    $router->post('posts/', 'PostController@createPost');
    $router->put('post/{id:[0-9]+}', 'PostController@updatePost');
    $router->delete('post/{id:[0-9]+}', 'PostController@deletePost');

    // Revision API
    $router->put('/post/{post_id:[0-9]+}/revision/{rev_id:[0-9]+}', 'RevisionController@rollbackRevision');
    $router->delete('/post/{post_id:[0-9]+}/revision/{rev_id:[0-9]+}', 'RevisionController@deleteRevision');

    // Trashed Post API
    $router->get('/posts/trashed/{page:[0-9]+}', 'PostController@listTrashedPosts');
    $router->get('post/trashed/{id:[0-9]+}', 'PostController@retriveTrashedPost');
    $router->put('post/trashed/{id:[0-9]+}', 'PostController@restoreTrashedPost');
    $router->delete('post/trashed/{id:[0-9]+}', 'PostController@deleteTrashedPost');

    // Category API
    $router->post('/categories', 'CategoryController@createCategory');
    $router->put('/category/{id:[0-9]+}', 'CategoryController@updateCategory');
    $router->delete('/category/{id:[0-9]+}', 'CategoryController@deleteCategory');
    // Comment API
    $router->get('/comments/{page:[0-9]+}', 'CommentController@retriveComments');

    // Attachment API
    $router->post('/attachments', 'AttachmentController@createAttachment');
    $router->put('/attachment/{id}', 'AttachmentController@updateAttachment');
    $router->delete('/attachment/{id}', 'AttachmentController@deleteAttachment');
});


// User API
$router->get('user/{id:[0-9]+}', 'UserController@retriveUser');
$router->post('user/token', 'TokenController@createToken');

// Post API
$router->get('/post/{id:[0-9]+}', 'PostController@retrivePost');
$router->get('/posts/{page:[0-9]+}', 'PostController@listPosts');

// Revision API
$router->get('/post/{id:[0-9]+}/revisions', 'RevisionController@getRevisionsByPostId');
$router->get('/post/{id:[0-9]+}/revision/{revision_id}', 'RevisionController@getRevisionByPostId');

// Category API
$router->get('/categories', 'CategoryController@listCategories');

// Comment API
$router->get('/comments/{page:[0-9]+}/post/{id}', 'CommentController@retriveCommentsByPostId');
$router->post('/comments/post/{id:[0-9]+}', 'CommentController@createCommentByPostId');
$router->delete('/comment/{id}', 'CommentController@deleteComment');

// Attachment API
$router->get('/attachment/{filename}', 'AttachmentController@retrieveAttachment');


// RSS Feed
$router->get('/atom', 'ATOMController@retrieveATOM');
