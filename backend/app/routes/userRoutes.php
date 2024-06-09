<?php


include '../controller/userController.php';
include '../controller/geoDataController.php';


$controller = new UserController();

// Определяем маршруты
Route::get('/users', 'UserController', 'getUsers');
Route::get('/anime', 'UserController', 'getAnime');
Route::post('/add-user', 'UserController', 'addUsers');
Route::post('/auth-user', 'UserController', 'authUsers');
Route::post('/add-book', 'UserController', 'addBooks');
Route::post('/downloadBook', 'UserController', 'downloadBook');
Route::post('/deleteBook', 'UserController', 'deleteBook');
Route::post('/editBook', 'UserController', 'editBook');
Route::post('/editBookPOST', 'UserController', 'editBookPOST');



// Выполняем маршрутизацию
Route::dispatch();