<?php



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");




// Обработка предварительного запроса OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Подключение маршрутизатора и маршрутов
include '../routes/router.php';
include '../routes/userRoutes.php';


?>