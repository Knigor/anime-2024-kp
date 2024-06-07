<?php

include  '../model/model.php';


class UserController
{
    private $model;


    public function __construct()
    {
        $this->model = new Model();
    }


    public function getUsers()
    {
        
        $users = $this->model->getUsers();

        echo json_encode($users);
        
    }

    public function getBooks()
    {
        $books = $this->model->getBooks();

        echo json_encode($books);
    }

    public function editBookPOST()
    {
        // Получаем данные из POST-запроса
        $book = $_POST['books'] ?? null;
        $author = $_POST['author'] ?? null;
        $allowDownload = $_POST['allow_download'] ?? null;
        $bookId = $_POST['id_book'] ?? null;

        // Снизу файлы
        $coverImage = $_FILES['cover_image'] ?? null;
        $book_file = $_FILES['book_file'] ?? null;

        $uniqueName1 = null;
        $uniqueName = null;

        // Обработка файла книги
        if ($book_file && $book_file['error'] === UPLOAD_ERR_OK) {
            $uniqueName1 = uniqid('book_', true) . '.' . pathinfo($book_file['name'], PATHINFO_EXTENSION);
            $uploadDirectory = '../view/files/';
            $destination = $uploadDirectory . $uniqueName1;

            if (!move_uploaded_file($book_file['tmp_name'], $destination)) {
                echo json_encode(['error' => 'Error saving book file']);
                return;
            }
        }

        // Обработка обложки книги
        if ($coverImage && $coverImage['error'] === UPLOAD_ERR_OK) {
            $uniqueName = uniqid('cover_', true) . '.' . pathinfo($coverImage['name'], PATHINFO_EXTENSION);
            $uploadDirectory = '../view/images/';
            $destination = $uploadDirectory . $uniqueName;

            if (!move_uploaded_file($coverImage['tmp_name'], $destination)) {
                echo json_encode(['error' => 'Error saving cover image']);
                return;
            }
        }

        // Вызов метода модели с переданными параметрами
        $success = $this->model->editBookPOST($book, $author, $allowDownload, $uniqueName1, $uniqueName, $bookId);

        echo json_encode($success);
    }

    public function addBooks()
    {
        // ниже данные
        $book = $_POST['books'] ?? null;
        $author = $_POST['author'] ?? null;
        $allowDownload = $_POST['allow_download'] ?? null;
        $userId = $_POST['user_id'] ?? null;

        // Снизу файлы
        $coverImage = $_FILES['cover_image'] ?? null;
        $book_file = $_FILES['book_file'] ?? null;

        // echo "$book \n";
        // echo "$author \n";
        // echo "$allowDownload \n";
        // echo "$userId \n";

        // перемещаем файл

        if ($book_file && $book_file['error'] === UPLOAD_ERR_OK)
        {
            $uniqueName1 = uniqid('book_', true) . '.' . pathinfo($book_file['name'], PATHINFO_EXTENSION);

            $uploadDirectory = '../view/files/';

            $destination = $uploadDirectory . $uniqueName1;


            if (move_uploaded_file($book_file['tmp_name'], $destination)) {
               // echo "Файл успешно загружен и сохранен под именем: " . $uniqueName1;
            } else {
               // echo "Ошибка при сохранении файла.";
            }


        }

        // перемещаем обложку

        if ($coverImage && $coverImage['error'] === UPLOAD_ERR_OK) {
            // Создаем уникальное имя для файла
            $uniqueName = uniqid('cover_', true) . '.' . pathinfo($coverImage['name'], PATHINFO_EXTENSION);
            
            // Путь к папке для сохранения файла
            $uploadDirectory = '../view/images/';
            
            // Полный путь к файлу с новым именем
            $destination = $uploadDirectory . $uniqueName;
            
            // Перемещаем файл в нужную папку
            if (move_uploaded_file($coverImage['tmp_name'], $destination)) {
              //  echo "Файл успешно загружен и сохранен под именем: " . $uniqueName;
            } else {
              //  echo "Ошибка при сохранении файла.";
            }

            $success = $this->model->addBook($book,$author,$allowDownload,$userId,$uniqueName1,$uniqueName);

            echo json_encode($success);

        } else {
            echo "Файл не был загружен или произошла ошибка при загрузке.";
        }
        
        
    }

    public function addUsers()
    {
        $login = $_POST['login'] ?? null;
        $fio = $_POST['fio'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($login && $fio && $password){

            $success = $this->model->addUsers($login,$fio,$password);

            echo json_encode($success);

        }  else {
            echo json_encode(['status' => 'error', 'message' => 'Данные пусты']);

        }
    }

    public function downloadBook()
    {
        $bookId = $_POST['id'] ?? null;

        

        $success = $this->model->downloadBook($bookId);

        echo  json_encode($success);
        

    }

    public function deleteBook()
    {
        $bookId = $_POST['id'] ?? null;

        $success = $this->model->deleteBook($bookId);

        echo json_encode($success);
    }

    public function editBook()
    {
        $bookId = $_POST['id'] ?? null;

        $success = $this->model->editBook($bookId);
        
        echo json_encode($success);
    }
    
    public function authUsers()
    {
        $login = $_POST['login'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($login && $password){
            $success = $this->model->authUsers($login,$password);


            echo json_encode($success);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Данные пусты']);
        }
    }
}