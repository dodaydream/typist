<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Posts;

class PostTest extends TestCase
{
    private $headers;

    public function __construct() {
        $this->headers = [
            'Authorization' => 'Bearer ' + getenv('TEST_JWT_CREDENTIAL')
        ];
    }

    public function testGetPosts()
    {
        // TODO
        $this->json('GET', '/posts/1', [])
             ->assertJson(json_encode(['current_page' => 1]));
    }

    public function testGetPostById()
    {
        $this->json('GET', '/post/1', [])
             ->assertSee('id');

        $this->json('GET', '/post/666', [])
              ->assertSee('Post Not Found');
    }

    public function testCreatePost()
    {
        $data = ['title' => 'Test', 'content' => 'Lorem Ipsum', 'user_id' => 1, 'category_id' => 1];
        $this->json('POST', 'admin/post', ['data' => json_encode($data)])
             ->seeJsonEquals([
                'created' => true
             ]);
    }

    public function testUpdatePost()
    {
        $this->json('PUT', 'admin/post/1', ['data' => json_encode(['title' => 'TestModified', 'content' => 'Lorem Ipsum', 'user_id' => 1])])
        ->seeJson(['updated' => true ]);

        $this->json('PUT', 'admin/post/666', ['data' => json_encode(['title' => 'TestModified', 'content' => 'Lorem Ipsum', 'user_id' => 1])])
        ->seeJson(['updated' => true ]);
    }

    public function testDeletePost()
    {
        $this->json('DELETE', 'admin/post/1')
             ->seeJson(['deleted' => true]);
    }

    public function testGetTrashedPostById()
    {
        $this->json('GET', 'admin/post/trashed/1', ['data' => json_encode(['page' => 1])])
             ->seeJson(['title' => 'TestModified']);
    }

    // TODO
    public function testGetTrashedPosts()
    {
        $this->json('GET', 'admin/posts/trashed', ['data' => json_encode(['page' => 1])])
             ->seeJson(['current_page' => 1]);
    }

    public function testRestorePost()
    {
        $this->json('PUT', 'admin/post/trashed/1')
             ->seeJson(['restored' => true]);
    }

}
