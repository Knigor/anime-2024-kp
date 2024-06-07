<?php
include '../config/config.php';


class Model 
{
    protected $db;

    public function __construct()
    {
        global $db_host, $db_name, $db_user, $db_pass;
        try {
            $dsn = "pgsql:host={$db_host};dbname={$db_name}";
            $this->db = new PDO($dsn, $db_user,$db_pass);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            die('Ошибка подключения к БД: ' . $e->getMessage());
        } catch (Exception $e) {
            die('Ошибка: ' . $e->getMessage());
        }
    }

    public function getUsers()
    {
        $stmt = $this->db->query("SELECT login, fio
                                    FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getBooks()
    {
        $stmt = $this->db->query("SELECT id_book, title, author, cover_image, user_id, allow_download
                                    FROM books
                                    ORDER BY id_book ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function downloadBook($bookId){

        $stm = $this->db->prepare("SELECT title, book_file
                                        from books
                                        where id_book = :id_book");
        $stm->bindParam(':id_book',$bookId);
        $stm->execute();

        $book = $stm->fetch(PDO::FETCH_ASSOC);

        
         $fileName = $book['title'];
         $filePath = $book['book_file'];

         $fullPath = '../view/files/' . $filePath;

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: ' . basename($fullPath));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($fullPath));

        readfile($fullPath);
        exit();

        return ['title' => $fileName, 'bookFile' => $filePath,'FullPath' =>$fullPath];


    }


    public function deleteBook($bookId)
    {
        $stm = $this->db->prepare("DELETE
                                    from books
                                    using users
                                    where id_book = :id_book and books.user_id = users.id");
        
        $stm->bindParam(':id_book',$bookId);
    

        if ($stm->execute()){
            return ['status' => 'success', 'message' => 'Карточка удалена'];
        } else {
            return ['status' => 'error', 'message' => 'Данные не добавлены'];
        }


    }

    public function editBook($bookId)
    {
        $stm = $this->db->prepare("SELECT * from books where id_book = :id_book");

        $stm->bindParam(':id_book',$bookId);

        $stm->execute();

        return $stm->fetch(PDO::FETCH_OBJ);


    }

    public function editBookPOST($book, $author, $allowDownload, $uniqueName1, $uniqueName, $bookId)
    {
        if ($bookId === null) {
            return ['status' => 'error', 'message' => 'id_book is required'];
        }
    
        $fieldsToUpdate = [];
        $params = [':id_book' => $bookId];
    
        if ($book !== null) {
            $fieldsToUpdate[] = 'title = :title';
            $params[':title'] = $book;
        }
    
        if ($author !== null) {
            $fieldsToUpdate[] = 'author = :author';
            $params[':author'] = $author;
        }
    
        if ($uniqueName !== null) {
            $fieldsToUpdate[] = 'cover_image = :cover_image';
            $params[':cover_image'] = $uniqueName;
        }
    
        if ($uniqueName1 !== null) {
            $fieldsToUpdate[] = 'book_file = :book_file';
            $params[':book_file'] = $uniqueName1;
        }
    
        if ($allowDownload !== null) {
            $fieldsToUpdate[] = 'allow_download = :allow_download';
            $params[':allow_download'] = $allowDownload;
        }
    
        if (empty($fieldsToUpdate)) {
            return ['status' => 'error', 'message' => 'No fields to update'];
        }
    
        $sql = 'UPDATE books SET ' . implode(', ', $fieldsToUpdate) . ' WHERE id_book = :id_book';
        $stm = $this->db->prepare($sql);
    
        foreach ($params as $param => $value) {
            $stm->bindValue($param, $value);
        }
    
        if ($stm->execute()) {
            return ['status' => 'success', 'message' => 'Данные обновлены'];
        } else {
            return ['status' => 'error', 'message' => 'Ошибка при обновлении данных'];
        }
    }

    public function addBook($book,$author,$allowDownload,$userId,$uniqueName1,$uniqueName){
        $stm = $this->db->prepare("INSERT into books (title, author, cover_image, book_file, allow_download, user_id)
        values (:title, :author, :cover_image, :book_file, :allow_download, :user_id)");

        $stm->bindParam(':title',$book);
        $stm->bindParam(':author',$author);
        $stm->bindParam(':cover_image',$uniqueName);
        $stm->bindParam(':book_file',$uniqueName1);
        $stm->bindParam(':allow_download',$allowDownload);
        $stm->bindParam(':user_id',$userId);


        if ($stm->execute()){
            return ['status' => 'success', 'message' => 'Данные добавлены'];
        } else {
            return ['status' => 'error', 'message' => 'Данные не добавлены'];
        }
        

    }

    public function addUsers($login,$fio,$password)
    {

        $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE login = :login");
        $checkStmt->bindParam(':login', $login);
        $checkStmt->execute();
        $userExists = $checkStmt->fetchColumn();


        if ($userExists > 0)
        {

            return ['status' => 'error', 'message' => 'Данный пользователь с таким логином уже существует'];

        } else 
        {
            $hashed_password = hash('sha256', $password);

            $stm = $this->db->prepare("INSERT into users (login, fio, hash_password) 
                                        values (:login,:fio,:hash_password) ");
    
            $stm->bindParam(':login',$login);
            $stm->bindParam(':fio',$fio);
            $stm->bindParam(':hash_password',$hashed_password);
    
            if ($stm->execute()) {

                $stm2 = $this->db->query("SELECT id, login, fio
                                                from users
                                                order by id desc
                                                limit 1");

                // return $stm2->fetchAll(PDO::FETCH_ASSOC);
                
                return ['status' => 'success', 'User' => $stm2->fetch(PDO::FETCH_OBJ)];
            } else {
                return ['status' => 'error', 'message' => 'Ошибка при добавлении пользователя'];
            }

        }
    }

    public function authUsers($login,$password)
    {
       
        $stmt = $this->db->prepare("SELECT * FROM users WHERE login = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user && hash('sha256', $password) === $user['hash_password']) {
            // Успешная аутентификация
            $_SESSION["user"] = $user;
            
            // Добавляем выборку роли и ФИО пользователя
            $login = $user['login'];
            $fio = $user['fio'];
            $idUser = $user['id'];
            
            // Отправка JSON-ответа с ФИО пользователя
            header('Content-Type: application/json');
            return ['status' => 'success', 'id' => $idUser,'login' => $login, 'full_name' => $fio];
            exit();
        } else {
            // Ошибка авторизации
            header('Content-Type: application/json');
            return ['status' => 'error', 'message' => 'Неверное имя пользователя или пароль.'];
            exit();
        }

    }

}
