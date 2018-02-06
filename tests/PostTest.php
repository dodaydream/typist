<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Posts;

class PostTest extends TestCase
{

    public function testGetPosts()
    {
        $this->json('GET', '/posts', ['data' => json_encode(['page' => 1])])
             ->seeJson(['current_page' => 1]);
    }

    public function testGetPostById()
    {
        $this->json('GET', '/post/1')
             ->seeJson(['id' => 1]);
        $this->json('GET', '/post/666')
              ->seeJson(['msg' => 'Post Not Found']);
    }

    public function testCreatePost()
    {
        $this->json('POST', 'admin/post', ['data' => json_encode(['title' => 'Test', 'content' => 'Lorem Ipsum', 'user_id' => 1])])
             ->seeJsonEquals([
                'created' => true
             ]);
    }

    public function testUpdatePost()
    {
        $this->json('PUT', 'admin/post/1', ['data' => json_encode(['title' => 'TestModified', 'content' => 'Lorem Ipsum', 'user_id' => 1])])
        ->seeJson(['updated' => true ]);

        $this->json('PUT', 'admin/post/1', ['data' => json_encode(['title' => 'TestModified', 'content' => 'Lorem Ipsum', 'user_id' => 1])])
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
