<?php

namespace App\Controllers;

use Ramsey\Uuid\Uuid;
use App\Controllers\BaseController;
use App\Exceptions\ApiException;
// model
use App\Models\AuthorModel;

// service

class Author extends BaseController
{

    public $author;
    public function __construct()
    {
        $this->author = new AuthorModel();
    }

    //function for test and get data
    public function getData()
    {
        $this->request = $this->request ?? service('request');

        // get query name
        // $name = $this->request->getGet('name');
        $name = $this->request->getGet('name');
        $limit = $this->request->getGet('limit') ?? 10; // Default limit to 10
        $offset = $this->request->getGet('offset') ?? 0; // Default offset to 0

        // get all Author
        $query = $this->author
            ->select('id,name,bio, DATE_FORMAT(birth_date,"%d/%m/%Y") as birth_date');

        if ($name) {
            $query->like('name', $name); // Filter by name
        }

        // Clone the query for counting total records
        $totalRecords = $query->countAllResults(false);

        // Apply limit and offset
        $authorList = $query
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);



        // Calculate total pages
        $totalPages = ceil($totalRecords / $limit);

        $data = [
            'status' => 200,
            'message' => 'success get data',
            'data' => [
                'authors' => $authorList,
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
        // get all Author
        $query = $this->author
            ->select('id,name,bio, DATE_FORMAT(birth_date,"%d/%m/%Y") as birth_date');

        // get by id
        $authorList = $query
            ->where('id', $id)
            ->first();

        //check if data not exist
        if (!$authorList) {
            return [
                'status' => 404,
                'message' => 'Data Not Found',
            ];
        }

        $data = [
            'status' => 200,
            'message' => 'success get data',
            'data' => $authorList,
        ];

        return $data;
    }

    public function storeData($post)
    {


        //convert date
        $birthDate = $this->changeDateSave($post['birth_date']);

        // check name
        $checkAuthor = $this->author->where('name', $post['name'])
            ->first();

        if ($checkAuthor) {
            return [
                'status' => 400,
                'message' => 'Author already exist',
            ];
        }

        // Generate a new UUID
        $uuid = Uuid::uuid4()->toString();


        $body = [
            'id' =>  $uuid,
            'name' => $post['name'],
            'bio' => $post['bio'],
            'birth_date' => $birthDate,
        ];

        $this->author->insert($body);
        $data = [
            'status' => 200,
            'message' => 'success create data',
            'author' => $body,
        ];

        return $data;
    }

    public function updateData($id, $post)
    {
        // get by id
        $authorList = $this->author
            ->where('id', $id)
            ->first();

        //check if data not exist
        if (!$authorList) {
            return [
                'status' => 404,
                'message' => 'Data Not Found',
            ];
        }


        $birthDate = $this->changeDateSave($post['birth_date']);

        // check name
        $checkAuthor = $this->author
            ->where('name', $post['name'])
            ->where('id !=', $id)
            ->first();

        if ($checkAuthor) {
            return [
                'status' => 400,
                'message' => 'Author already exist',
            ];
        }

        $body = [
            'name' => $post['name'],
            'bio' => $post['bio'],
            'birth_date' => $birthDate,
        ];

        $this->author->update($id, $body);

        $data = [
            'status' => 200,
            'message' => 'success update data',
            'author' => $body,
        ];

        return $data;
    }


    public function deleteData($id)
    {
        // get by id
        $authorList = $this->author
            ->where('id', $id)
            ->first();

        //check if data not exist
        if (!$authorList) {
            return [
                'status' => 404,
                'message' => 'Data Not Found',
            ];
        }

        $this->author->delete($id);

        $data = [
            'status' => 200,
            'message' => 'success delete data',
        ];

        return $data;
    }

    // function for response api
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
                "name" => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'name required',
                    ]
                ],
                "bio" => [],
                "birth_date" => [
                    'rules' => 'valid_date[d/m/Y]', // Specify the expected format
                    'errors' => [
                        'valid_date' => 'Birth date must be in the format DD/MM/YYYY',
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
                "name" => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'name required',
                    ]
                ],
                "bio" => [],
                "birth_date" => [
                    'rules' => 'valid_date[d/m/Y]', // Specify the expected format
                    'errors' => [
                        'valid_date' => 'Birth date must be in the format DD/MM/YYYY',
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
