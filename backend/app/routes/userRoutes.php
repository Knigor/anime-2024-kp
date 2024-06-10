<?php


include '../controller/userController.php';
include '../controller/geoDataController.php';


$controller = new UserController();

// Определяем маршруты
Route::get('/users', 'UserController', 'getUsers');
Route::get('/anime', 'UserController', 'getAnime');
Route::post('/getSettings', 'UserController', 'getSettings');
Route::post('/add-user', 'UserController', 'addUsers');
Route::post('/auth-user', 'UserController', 'authUsers');
Route::post('/add-anime', 'UserController', 'addAnime');
Route::post('/downloadBook', 'UserController', 'downloadBook');
Route::post('/deleteAnime', 'UserController', 'deleteAnime');
Route::post('/editAnime', 'UserController', 'editAnime');
Route::post('/editAnimePOST', 'UserController', 'editAnimePOST');
Route::post('/addView', 'UserController', 'addView');
Route::post('/getView', 'UserController', 'getView');
Route::get('/getTopRating', 'UserController', 'getTopRating');



// Выполняем маршрутизацию
Route::dispatch();