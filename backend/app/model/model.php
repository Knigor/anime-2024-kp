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

    public function getTopRating()
    {
        $stmt = $this->db->query("SELECT title_anime, AVG(rating) AS average_rating, COUNT(*) AS rating_count
                                    FROM rating_anime
                                    GROUP BY title_anime 
                                    ORDER BY average_rating DESC
                                    LIMIT 10");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsers()
    {
        $stmt = $this->db->query("SELECT login, fio
                                    FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAnime()
    {
        $stmt = $this->db->query("SELECT anime.title_anime, anime.year_release, director, studio_manufacture, discription_plot, anime_img, name_genre 
                                    from anime
                                    join stores_genre sg on sg.title_anime = anime.title_anime
                                    join genre g ON g.id_genre = sg.id_genre");
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

    public function getView($email)
    {
                    $stm = $this->db->prepare("SELECT * from view
                                                where email = :email");

            $stm->bindParam(':email',$email);

            $stm->execute();

            return $stm->fetchAll(PDO::FETCH_ASSOC);

    }


    public function addView($title, $year, $email, $history)
    {
        $query = "INSERT INTO view (title_anime, year_release, email, history, date_view) 
                  VALUES (:title_anime, :year_release, :email, :history, CURRENT_DATE)";
        
        $stmt = $this->db->prepare($query);
        
        // Привязка значений
        $stmt->bindParam(':title_anime', $title);
        $stmt->bindParam(':year_release', $year);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':history', $history);

        if($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Запись успешно добавлена.'];
        } else {
            return ['status' => 'error', 'message' => 'Не удалось добавить запись.'];
        }
    }



    public function deleteAnime($title) {
        try {
            // Начало транзакции
            $this->db->beginTransaction();
    
            // Удаление записи из таблицы stores_genre
            $stm1 = $this->db->prepare("DELETE FROM stores_genre WHERE title_anime = :title");
            $stm1->bindParam(':title', $title);
    
            if (!$stm1->execute()) {
                throw new Exception("Ошибка при удалении записи из таблицы stores_genre");
            }
    
            // Удаление записи из таблицы anime
            $stm2 = $this->db->prepare("DELETE FROM anime WHERE title_anime = :title");
            $stm2->bindParam(':title', $title);
    
            if (!$stm2->execute()) {
                throw new Exception("Ошибка при удалении записи из таблицы anime");
            }
    
            // Подтверждение транзакции
            $this->db->commit();
    
            return ['status' => 'success', 'message' => 'Записи удалены'];
    
        } catch (Exception $e) {
            // Откат транзакции в случае ошибки
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'Записи не удалены: ' . $e->getMessage()];
        }
    }

    public function editAnime($title)
    {
        $stm = $this->db->prepare("SELECT anime.title_anime, anime.year_release, director, studio_manufacture, discription_plot, anime_img, name_genre 
                                    from anime
                                    join stores_genre sg on sg.title_anime = anime.title_anime and sg.year_release = anime.year_release 
                                    join genre g ON g.id_genre = sg.id_genre
                                    where anime.title_anime = :title_anime");

        $stm->bindParam(':title_anime',$title);

        $stm->execute();

        return $stm->fetch(PDO::FETCH_OBJ);


    }

    public function editAnimePOST($title, $director,$discription, $studio, $uniqueName)
    {

    
        $fieldsToUpdate = [];
        $params = [':title' => $title];
    
    
        if ($director !== null) {
            $fieldsToUpdate[] = 'director = :director';
            $params[':director'] = $director;
        }

        if ($discription !== null) {
            $fieldsToUpdate[] = 'discription_plot = :discription';
            $params[':discription'] = $discription;
        }


        if ($studio !== null) {
            $fieldsToUpdate[] = 'studio_manufacture = :studio';
            $params[':studio'] = $studio;
        }
    
        if ($uniqueName !== null) {
            $fieldsToUpdate[] = 'anime_img = :cover_image';
            $params[':cover_image'] = $uniqueName;
        }
    
    
        if (empty($fieldsToUpdate)) {
            return ['status' => 'error', 'message' => 'No fields to update'];
        }
    
        $sql = 'UPDATE anime SET ' . implode(', ', $fieldsToUpdate) . ' WHERE title_anime = :title';
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

    


    public function addAnime($title, $year, $director, $studio, $description, $genre, $uniqueName) {
        try {
            // Начало транзакции
            $this->db->beginTransaction();
    
            // Первый запрос
            $stm1 = $this->db->prepare("INSERT INTO anime (title_anime, year_release, director, studio_manufacture, discription_plot, anime_img)
                VALUES (:title, :year_release, :director, :studio_manufacture, :discription_plot, :anime_img)");
    
            $stm1->bindParam(':title', $title);
            $stm1->bindParam(':year_release', $year);
            $stm1->bindParam(':director', $director);
            $stm1->bindParam(':studio_manufacture', $studio);
            $stm1->bindParam(':discription_plot', $description);
            $stm1->bindParam(':anime_img', $uniqueName);
    
            if (!$stm1->execute()) {
                throw new Exception("Ошибка при выполнении первого запроса");
            }
    
            // Второй запрос
            $stm2 = $this->db->prepare("INSERT INTO stores_genre (id_genre, title_anime, year_release)
                VALUES (:id_genre, :title, :year_release)");
    
            $stm2->bindParam(':id_genre', $genre);
            $stm2->bindParam(':title', $title);
            $stm2->bindParam(':year_release', $year);
    
            if (!$stm2->execute()) {
                throw new Exception("Ошибка при выполнении второго запроса");
            }
    
            // Подтверждение транзакции
            $this->db->commit();
    
            return ['status' => 'success', 'message' => 'Данные добавлены'];
    
        } catch (Exception $e) {
            // Откат транзакции в случае ошибки
            $this->db->rollBack();
            return ['status' => 'error', 'message' => 'Данные не добавлены: ' . $e->getMessage()];
        }
    }

    public function getSettings($email)
    {
        $stmt = $this->db->prepare("SELECT email, id_profile, name_user, type_user, photo_user  FROM \"user\" WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        unset($user['0']);        
        unset($user['1']);        
        unset($user['2']);        
        unset($user['3']);        
        unset($user['4']);        

        return $user;

    }


    public function addUsers($email,$full_name,$password,$role_user)
    {

        
        $stmtemail = $this->db->prepare("SELECT * FROM \"user\" WHERE email = ?");
        $stmtemail->execute([$email]);
        $useremail = $stmtemail->fetch();

        if ($useremail > 0)
        {

            return ['status' => 'error', 'message' => 'Данный пользователь с такой почтой уже существует'];

        } else 
        {

            

            $data = [
                'email' => $email,
                'password' => $password,
                'full_name_user' => $full_name,
                'role_user' => $role_user,
            ];

            $data_json = [
                'email' => $email,
                'full_name_user' => $full_name,
                'role_user' => $role_user,
                'photo_user' => 'empty.svg'
            ];

            $stmtInsert = $this->db->prepare("INSERT INTO \"user\" (email, id_profile, name_user, hash_password, date_registration, type_user, photo_user) VALUES (:email, 1,:full_name_user,:password,'2023-01-01',:role_user, 'empty.svg' )");
            $stmtInsert->execute($data);


            return ['status' => 'success', 'user' => $data_json];

            exit();

        }
    }

    public function authUsers($email,$password)
    {
       
        $stmt = $this->db->prepare("SELECT * 
                                    from \"user\"
                                    where email = ? and hash_password = ? ");
        $stmt->execute([$email, $password]);
        $user = $stmt->fetch();

        if ($user) {

            $_SESSION["user"] = $user;

            $role = $user['type_user'];
            $fullName = $user['name_user'];
            $photo = $user['photo_user']; 
            $email = $user['email']; 

            
            return ['status' => 'success', 'id_profile' => $user['id_profile'], 'role' => $role, 'full_name' => $fullName, 'photo_user' => $photo, 'email' => $email];
            exit();
        } else {

            
            return ['status' => 'error', 'message' => 'Неверный email пользователя или пароль.'];
            exit();
        }

    }

}
