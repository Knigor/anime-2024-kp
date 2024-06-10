<?php

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Methods: GET');

$dbhost = 'postgres-db';
$dbname = 'anime-2024';
$dbuser = 'user';
$dbpass = 'user';

header('Content-Type: application/json');

$response = array();

try {
    $pdo = new PDO("pgsql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    $response['error'] = "Ошибка подключения к базе данных: " . $e->getMessage();
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из POST-запроса
    if (empty($_POST["email"])) {
        $response['error'] = "Email обязателен для обновления.";
        echo json_encode($response);
        exit;
    }

    $email = $_POST["email"];

    // Проверяем, был ли передан пароль для обновления
    $password = null;
    if (isset($_POST["old_password"]) && isset($_POST["new_password"])) {
        $old_password = $_POST["old_password"];
        $new_password = $_POST["new_password"];
        // Проверяем, совпадает ли введенный старый пароль с тем, что есть в базе данных
        if (verifyOldPassword($email, $old_password)) {
            $password = hash("sha256", $new_password); // Хешируем новый пароль
        } else {
            $response['error'] = "Введен неправильный старый пароль.";
            echo json_encode($response);
            exit;
        }
    }

    // Получаем остальные данные для обновления
    $full_name = $_POST["full_name"] ?? null;
    $photo = $_FILES["photo"] ?? null;
    $photo_name = null;


    // Проверяем, был ли передан файл для загрузки
    if ($photo && $photo["error"] == UPLOAD_ERR_OK) {
        $photo_name = uploadImage($photo);
        if (!$photo_name) {
            $response['error'] = "Ошибка при загрузке изображения.";
            echo json_encode($response);
            exit;
        }
        // Добавляем слеш перед именем файла
        
    } else {
        $photo_name = null;
    }

    // Обновляем данные пользователя
    if (updateUser($full_name, $email, $password, $photo_name )) {
        $response['message'] = "Данные пользователя успешно обновлены.";
    } else {
        $response['error'] = "Ошибка при обновлении данных пользователя.";
    }
    echo json_encode($response);
}

function updateUser($full_name, $email, $password, $photo)
{
    global $pdo;

    $update_fields = [];
    $params = [];

    if ($full_name !== null) {
        $update_fields[] = "name_user=?";
        $params[] = $full_name;
    }
    if ($email !== null) {
        $update_fields[] = "email=?";
        $params[] = $email;
    }
    if ($password !== null) {
        $update_fields[] = "hash_password=?";
        $params[] = $password;
    }
    if ($photo !== null) {
        $update_fields[] = "photo_user=?";
        $params[] = $photo;
    }


    $update_fields_str = implode(",", $update_fields);

    try {
        $stmt = $pdo->prepare("UPDATE \"user\" SET $update_fields_str WHERE email= ?");
        $params[] = $email;
        $stmt->execute($params);
        return true;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

function uploadImage($image)
{
    $target_dir = "../view/images/";
    $imageFileType = strtolower(pathinfo(basename($image["name"]), PATHINFO_EXTENSION));
    $target_file = $target_dir . date("YmdHis") . "." . $imageFileType;
    $uploadOk = 1;
    // Проверка наличия файла
    if (file_exists($target_file)) {
        $uploadOk = 0;
    }
    // Проверка размера файла
    if ($image["size"] > 500000) {
        $uploadOk = 0;
    }
    // Разрешенные форматы файлов
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $uploadOk = 0;
    }
    // Попытка загрузки файла
    if ($uploadOk == 0) {
        die("Ошибка при загрузке изображения.");
    } else {
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            return basename($target_file);
        } else {
            die("Ошибка при загрузке изображения.");
        }
    }
}

function verifyOldPassword($email, $old_password)
{
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT hash_password from \"user\" where email=?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row) {
            die("Пользователь не найден.");
        }
        $stored_password = $row['hash_password']; 
        $old_password_hash = hash("sha256", $old_password);
        return $stored_password === $old_password_hash;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}
?>