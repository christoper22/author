<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Author;
use App\Models\AuthorModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class AuthorTest extends CIUnitTestCase
{
    protected $request;
    protected $response;
    protected $mockRequest;
    public $id;

    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = service('request');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getId(): void
    {
        $author = new AuthorModel();
        // check name
        $checkAuthor = $author
            ->where('name', 'Emily Davis')
            ->first();
        $this->id = $checkAuthor['id'];
    }

    public function testCreateDataAuthor()
    {
        // Create an instance of the Author controller
        // Optionally, set up the request if needed

        $authorController = new Author();
        $dataMock = [
            "name" => "Emily Davis",
            "bio" => "A professional writer with several published books.",
            "birth_date" => "22/05/1998"
        ];

        $response = $authorController->storeData($dataMock);

        $this->id = $response['author']['id'];
        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }
    public function testGetAll()
    {
        // Create an instance of the Author controller
        // Optionally, set up the request if needed
        $this->request->withMethod('get');
        $_GET['name'] = 'Emily Davis';

        $authorController = new Author();
        $response = $authorController->getData();

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }




    public function testUpdateDataAuthor()
    {
        //get id
        $this->getId();
        // Create an instance of the Author controller
        // Optionally, set up the request if needed
        $authorController = new Author();
        $dataMock = [
            "name" => "Emily Davis",
            "bio" => "A professional writer with several published books.Great",
            "birth_date" => "22/05/1998"
        ];

        $response = $authorController->updateData($this->id, $dataMock);

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }

    public function testDeleteDataAuthor()
    {
        //get id
        $this->getId();
        // Create an instance of the Author controller
        // Optionally, set up the request if needed
        $authorController = new Author();
        $response = $authorController->deleteData($this->id);

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }

    public function testGetDetailNotFound()
    {
        // Create an instance of the Author controller
        // Optionally, set up the request if needed

        $authorController = new Author();
        $response = $authorController->getDataDetail('22');

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null
        // Check the response status
        $this->assertEquals(404, $response['status']);
    }
}
