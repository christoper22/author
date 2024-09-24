<?php

namespace App\Controllers;

use Ramsey\Uuid\Uuid;
use App\Controllers\BaseController;

// model
use App\Models\BookModel;
use App\Models\AuthorModel;

// service

class Book extends BaseController
{

    protected $book;
    protected $author;
    public function __construct()
    {
        $this->book = new BookModel();
        $this->author = new AuthorModel();
    }

    // for function testing and response
    public function getData()
    {
        $this->request = $this->request ?? service('request');
        // get query name
        $title = $this->request->getGet('title');
        $author = $this->request->getGet('author_name');
        $limit = $this->request->getGet('limit') ?? 10; // Default limit to 10
        $offset = $this->request->getGet('offset') ?? 0; // Default offset to 0

        // get all book
        $query = $this->book
            ->select('books.id,books.title,books.description,DATE_FORMAT(books.publish_date,"%d/%m/%Y") as publish_date,authors.name as author_name')
            ->join('authors', 'authors.id=books.author_id');

        if ($title) {
            $query->like('title', $title); // Filter by name
        }
        if ($author) {
            $query->like('authors.name', $author); // Filter by name
        }
        // Clone the query for counting total records
        $totalRecords = $query->countAllResults(false);

        // Apply limit and offset
        $bookList = $query
            ->orderBy('books.created_at', 'DESC')
            ->findAll($limit, $offset);



        // Calculate total pages
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'status' => 200,
            'message' => 'success get data',
            'data' => [
                'books' => $bookList,
                'totalRecords' => $totalRecords,
                'currentPage' => floor($offset / $limit) + 1,
                'totalPages' => $totalPages,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ];

        return $data;
    }

    public function getBookByAuthor($id)
    {
        $this->request = $this->request ?? service('request');
        $limit = $this->request->getGet('limit') ?? 10; // Default limit to 10
        $offset = $this->request->getGet('offset') ?? 0; // Default offset to 0


        // get by id
        $authorList = $this->author
            ->where('id', $id)
            ->first();

        //check if data not exist
        if (!$authorList) {
            return [
                'status' => 404,
                'message' => 'Author Not Found',
            ];
        }


        // get all book
        $query = $this->book
            ->select('books.id,books.title,books.description,DATE_FORMAT(books.publish_date,"%d/%m/%Y") as publish_date,authors.name as author_name')
            ->join('authors', 'authors.id=books.author_id');


        $query->like('authors.id', $id); // Filter by name

        // Clone the query for counting total records
        $totalRecords = $query->countAllResults(false);

        // Apply limit and offset
        $bookList = $query
            ->orderBy('books.created_at', 'DESC')
            ->findAll($limit, $offset);

        // Calculate total pages
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'status' => 200,
            'message' => 'success get data',
            'data' => [
                'books' => $bookList,
                'totalRecords' => $totalRecords,
                'currentPage' => floor($offset / $limit) + 1,
                'totalPages' => $totalPages,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ];

        return $data;
    }
    public function getDataDetail($id)
    {
        // get all book
        $query = $this->book
            ->select('books.id,books.title,books.description,DATE_FORMAT(books.publish_date,"%d/%m/%Y") as publish_date,authors.name as author_name')
            ->join('authors', 'authors.id=books.author_id');;

        // get by id
        $bookList = $query
            ->where('books.id', $id)
            ->first();

        //check if data not exist
        if (!$bookList) {
            return [
                'status' => 404,
                'message' => 'Data Not Found',
            ];
        }

        $data = [
            'status' => 200,
            'message' => 'success get data',
            'data' => $bookList,
        ];

        return $data;
    }

    public function storeData($post)
    {
        //convert date
        $publishDate = $this->changeDateSave($post['publish_date']);

        // check name
        $checkbook = $this->book->where('title', $post['title'])
            ->first();

        if ($checkbook) {
            return [
                'status' => 400,
                'message' => 'book already exist',
            ];
        }

        // Generate a new UUID
        $uuid = Uuid::uuid4()->toString();


        $body = [
            'id' =>  $uuid,
            'author_id' => $post['author_id'],
            'title' => $post['title'],
            'description' => $post['description'],
            'publish_date' => $publishDate,
        ];

        $this->book->insert($body);

        $data = [
            'status' => 200,
            'message' => 'success create data',
            'book' => $body,
        ];

        return $data;
    }

    public function updateData($id, $post)
    {
        // get by id
        $bookList = $this->book
            ->where('id', $id)
            ->first();

        //check if data not exist
        if (!$bookList) {
            return [
                'status' => 404,
                'message' => 'Data Not Found',
            ];
        }


        $publishDate = $this->changeDateSave($post['publish_date']);

        // check name
        $checkbook = $this->book
            ->where('title', $post['title'])
            ->where('id !=', $id)
            ->first();

        if ($checkbook) {
            return [
                'status' => 200,
                'message' => 'book already exist',
            ];
        }

        $body = [
            'author_id' => $post['author_id'],
            'title' => $post['title'],
            'description' => $post['description'],
            'publish_date' => $publishDate,
        ];

        $this->book->update($id, $body);

        $data = [
            'status' => 200,
            'message' => 'success update data',
            'book' => $body,
        ];

        return $data;
    }

    public function deleteData($id)
    {
        // get by id
        $bookList = $this->book
            ->where('id', $id)
            ->first();

        //check if data not exist
        if (!$bookList) {
            return [
                'status' => 404,
                'message' => 'Data Not Found',
            ];
        }

        $this->book->delete($id);

        $data = [
            'status' => 200,
            'message' => 'success delete data',
        ];

        return $data;
    }


    // for response
    public function index()
    {
        try {
            $data = $this->getData();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function bookByAuthor($id)
    {
        try {
            $data = $this->getBookByAuthor($id);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function getDetail($id)
    {
        try {
            $data = $this->getDataDetail($id);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function store()
    {
        try {
            $validate = $this->validate([
                "author_id" => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'author required',
                    ]
                ],
                "title" => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'title required',
                    ]
                ],
                "description" => [],
                "publish_date" => [
                    'rules' => 'valid_date[d/m/Y]', // Specify the expected format
                    'errors' => [
                        'valid_date' => 'Publish date must be in the format DD/MM/YYYY',
                    ]
                ],
            ]);

            // validate body
            if (!$validate) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => $this->validator->getErrors(),
                ]);
            }


            // GET ALL POST
            $post = $this->request->getJSON(true);

            $data = $this->storeData($post);

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function update($id)
    {
        try {
            $validate = $this->validate([
                "author_id" => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'author required',
                    ]
                ],
                "title" => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'title required',
                    ]
                ],
                "description" => [],
                "publish_date" => [
                    'rules' => 'valid_date[d/m/Y]', // Specify the expected format
                    'errors' => [
                        'valid_date' => 'Publish date must be in the format DD/MM/YYYY',
                    ]
                ],
            ]);

            if (!$validate) {
                return $this->response->setJSON([
                    'status' => 400,
                    'message' => $this->validator->getErrors(),
                ]);
            }

            // GET ALL POST
            $post = $this->request->getJSON(true);

            $data = $this->updateData($id, $post);

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {
        try {

            $data = $this->deleteData($id);

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
