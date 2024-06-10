<?php

include  '../model/model.php';


class UserController
{
    private $model;


    public function __construct()
    {
        $this->model = new Model();
    }

    public function getTopRating()
    {
        $rating = $this->model->getTopRating();
        
        echo json_encode($rating);
    }

    public function getUsers()
    {
        
        $users = $this->model->getUsers();

        echo json_encode($users);
        
    }

    public function getAnime()
    {
        $books = $this->model->getAnime();

        echo json_encode($books);
    }

    public function editAnimePOST()
    {
        // Получаем данные из POST-запроса
        $title = $_POST['title'] ?? null;
        $director = $_POST['director'] ?? null;
        $studio = $_POST['studio'] ?? null;
        $discription = $_POST['discription'] ?? null;
        $genre = $_POST['genre'] ?? null;

        // Снизу файлы
        $coverImage = $_FILES['cover_image'] ?? null;
        

        
        $uniqueName = null;



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
        $success = $this->model->editAnimePOST($title, $director,$discription, $studio, $uniqueName,$genre);

        echo json_encode($success);
    }

    public function addAnime()
    {
        // ниже данные
        $title = $_POST['title'] ?? null;
        $year = $_POST['year'] ?? null;
        $director = $_POST['director'] ?? null;
        $studio = $_POST['studio'] ?? null;
        $discription = $_POST['discription'] ?? null;
        $genre = $_POST['genre'] ?? null;
        
        

        // Снизу файлы
        $coverImage = $_FILES['cover_image'] ?? null;


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

            $success = $this->model->addAnime($title,$year,$director,$studio,$discription,$genre,$uniqueName);

            echo json_encode($success);

        } else {
            echo "Файл не был загружен или произошла ошибка при загрузке.";
        }
        
        
    }

    public function getSettings()
    {
        $email = $_POST["email"];

        if ($email){
            $success = $this->model->getSettings($email);

            echo json_encode($success);
        } else 
        {
            echo json_encode(['status' => 'error', 'message' => 'Данные пусты']);
        }

    }



    public function addUsers()
    {
        $email = $_POST["email"];
        $full_name = $_POST["full_name"];
        $password = $_POST["password"];
        $role_user = isset($_POST["role"]) ? $_POST["role"] : 'user';

        if ($email && $full_name && $password && $role_user){

            $success = $this->model->addUsers($email,$full_name,$password,$role_user);

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

    public function getView()
    {
        $email = $_POST['email'] ?? null;


        $success = $this->model->getView($email);

        echo json_encode($success);
    }

    public function addView()
    {
        $title = $_POST['title'] ?? null;
        $year = $_POST['year'] ?? null;
        $email = $_POST['email'] ?? null;
        $history = $_POST['history'] ?? null;

        $success = $this->model->addView($title,$year,$email,$history);

        echo json_encode($success);
    }

    public function deleteAnime()
    {
        $title = $_POST['title'] ?? null;

        $success = $this->model->deleteAnime($title);

        echo json_encode($success);
    }

    public function editAnime()
    {
        $title = $_POST['title'] ?? null;

        $success = $this->model->editAnime($title);
        
        echo json_encode($success);
    }
    
    public function authUsers()
    {
        $email = $_POST["email"];
        $password = $_POST["password"];

        if ($email && $password){
            $success = $this->model->authUsers($email,$password);


            echo json_encode($success);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Данные пусты']);
        }
    }
}