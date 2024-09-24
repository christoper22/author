<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//get book by author
$routes->get('/authors/(:any)/books', 'Book::bookByAuthor/$1');

// author endpoint
$routes->get('/authors', 'Author::index');
$routes->get('/authors/(:any)', 'Author::getDetail/$1');
$routes->post('/authors', 'Author::store');
$routes->put('/authors/(:any)', 'Author::update/$1');
$routes->delete('/authors/(:any)', 'Author::delete/$1');

// book endpoint
$routes->get('/books', 'Book::index');
$routes->get('/books/(:any)', 'Book::getDetail/$1');
$routes->post('/books', 'Book::store');
$routes->put('/books/(:any)', 'Book::update/$1');
$routes->delete('/books/(:any)', 'Book::delete/$1');
