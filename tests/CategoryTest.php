<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Posts;
use App\Categories;

class CategoryTest extends TestCase
{

    private $headers;
    /**
     * @group category
     * Test create category
     */
    public function __construct() {
        parent::__construct();
        $this->headers = [
            'Authorization' => 'Bearer ' + getenv('TEST_JWT_CREDENTIAL')
        ];
    }

    public function testCreateCategory()
    {
        $data = [
            'name' => 'Test'
        ];

        $expect = [
            'created' => 'true'
        ];

        $response = $this->json('POST', '/categories', $data, $this->headers);
        $response->assertJson(json_encode($expect));
    }

    /**
     * @group category
     * Test get categories
     */
    public function testGetCategories()
    {
        $this->json('GET', '/categories')
             ->assertJson(Categories::all()->toJson());
    }

    /**
     * @group category
     * Test get categories
     */
    public function testUpdateCategory()
    {
        $data = ['name' => 'TestModified', 'description' => 'Lorem Ipsum'];
        $expect = ['updated' => true];
        $this->json('PUT', '/category/1', $data, $this->headers)
        ->assertJson(json_encode($expect));
    }

    /**
     * @group category
     * Test get categories
     */
    public function testDeleteCategory()
    {
        $expect = ['deleted' => true];
        $this->json('DELETE', '/category/1', [], $this->headers)
             ->assertJson(json_encode($expect));
    }

}
