<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Book;
use App\Models\BookModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

class BookTest extends CIUnitTestCase
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
        $Book = new BookModel();
        // check name
        $checkBook = $Book
            ->where('title', 'the Legend of Ang Part2')
            ->first();
        $this->id = $checkBook['id'];
    }

    public function testCreateDataBook()
    {
        // Create an instance of the Book controller
        // Optionally, set up the request if needed

        $bookController = new Book();
        $dataMock =
            [
                "author_id" => "291129d7-7978-11ef-9f9b-d8f2ca0e0910",
                "title" => "the Legend of Ang Part2",
                "description" => "Description of the first book by Book One.",
                "publish_date" => "01/01/2023"
            ];


        $response = $bookController->storeData($dataMock);

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }

    public function testGetAll()
    {
        // Create an instance of the Book controller
        // Optionally, set up the request if needed
        $this->request->withMethod('get');
        $_GET['title'] = 'first';
        $_GET['author_name'] = 'one';

        $bookController = new Book();
        $response = $bookController->getData();

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }

    public function testGetDetail()
    {
        // Create an instance of the Book controller
        // Optionally, set up the request if needed

        $bookController = new Book();
        $response = $bookController->getDataDetail('633d75a8-7978-11ef-9f9b-d8f2ca0e0910');

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null
        // Check the response status
        $this->assertEquals(200, $response['status']);
    }
    public function testGetByAuthor()
    {
        // Create an instance of the Book controller
        // Optionally, set up the request if needed

        $bookController = new Book();
        $response = $bookController->getBookByAuthor('29114572-7978-11ef-9f9b-d8f2ca0e0910');

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null
        // Check the response status
        $this->assertEquals(200, $response['status']);
    }


    public function testUpdateDataBook()
    {
        //get id
        $this->getId();
        // Create an instance of the Book controller
        // Optionally, set up the request if needed
        $bookController = new Book();
        $dataMock =
            [
                "author_id" => "291129d7-7978-11ef-9f9b-d8f2ca0e0910",
                "title" => "the Legend of Ang Part2",
                "description" => "Description of the first book by Book One.Amazing",
                "publish_date" => "01/01/2023"
            ];

        $response = $bookController->updateData($this->id, $dataMock);

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }

    public function testDeleteDataBook()
    {
        //get id
        $this->getId();
        // Create an instance of the Book controller
        // Optionally, set up the request if needed
        $bookController = new Book();
        $response = $bookController->deleteData($this->id);

        // Validate the response
        $this->assertNotNull($response); // Ensure the response is not null

        // Check the response status
        $this->assertEquals(200, $response['status']);
    }
}
