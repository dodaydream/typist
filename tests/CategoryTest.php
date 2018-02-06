<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Posts;

class CategoryTest extends TestCase
{

    public function testGetCategories()
    {
        $this->json('GET', '/categories', ['data' => json_encode(['page' => 1])])
             ->seeJson(['current_page' => 1]);
    }

    public function testCreateCategory()
    {
        $data = [
            'name' => 'Test',
            'description' => 'A sample Category'
        ];
        $this->json('POST', 'admin/category', ['data' => json_encode($data)])
             ->seeJson(['created' => true]);
    }
    //
    // public function testUpdateCategory()
    // {
    //
    // }
    //
    // public function testDeleteCategory()
    // {
    //
    // }

}
