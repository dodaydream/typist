<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Posts;

class CategoryTest extends TestCase
{
    /**
     * @group category
     * Test create category
     */
    public function testCreateCategory()
    {
        $data = [
            'name' => 'Test',
            'description' => 'A sample Category'
        ];
        $this->json('POST', 'admin/category', ['data' => json_encode($data)])
             ->seeJson(['created' => true]);
    }

    /**
     * @group category
     * Test get categories
     */
    public function testGetCategories()
    {
        $this->json('GET', '/categories')
             ->seeJson(['id' => 1]);
    }

    /**
     * @group category
     * Test get categories
     */
    public function testUpdateCategory()
    {
        $data = ['name' => 'TestModified', 'description' => 'Lorem Ipsum'];
        $this->json('PUT', 'admin/category/1', ['data' => json_encode($data)])
        ->seeJson(['updated' => true]);
    }

    /**
     * @group category
     * Test get categories
     */
    public function testDeleteCategory()
    {
        $this->json('DELETE', 'admin/category/1')
             ->seeJson(['deleted' => true]);
    }

}
