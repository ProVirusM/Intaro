<?php

// Функция для установления соединения с базой данных PostgreSQL
function getDbConnection() {
    $host = "localhost"; // Хост базы данных
    $port = "5432"; // Порт базы данных
    $dbname = "book1"; // Имя базы данных
    $username = "postgres"; // Имя пользователя базы данных
    $password = "0000"; // Пароль пользователя

    try {
        // Устанавливаем соединение с базой данных и возвращаем объект PDO
        return new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
    } catch (PDOException $e) {
        // В случае ошибки выводим сообщение об ошибке и завершаем скрипт
        die("Error: " . $e->getMessage());
    }
}

// Функция для получения списка всех книг из базы данных, отсортированных по дате прочтения по убыванию
function getBooks() {
    $db = getDbConnection(); // Получаем соединение с базой данных
    $stmt = $db->query('SELECT * FROM books ORDER BY read_date DESC'); // Выполняем запрос на выборку всех книг
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Возвращаем массив ассоциативных массивов с данными книг
}

// Функция для получения информации о конкретной книге по её идентификатору
function getBook($id) {
    $db = getDbConnection(); // Получаем соединение с базой данных
    $stmt = $db->prepare('SELECT * FROM books WHERE id = ?'); // Подготавливаем запрос на выборку книги по идентификатору
    $stmt->execute([$id]); // Выполняем запрос с передачей идентификатора в качестве параметра
    return $stmt->fetch(PDO::FETCH_ASSOC); // Возвращаем ассоциативный массив с данными книги
}

// Функция для добавления новой книги в базу данных
function addBook($title, $author, $cover, $file, $read_date, $allow_download) {
    $db = getDbConnection(); // Получаем соединение с базой данных
    $stmt = $db->prepare('INSERT INTO books (title, author, cover, file, read_date, allow_download) VALUES (?, ?, ?, ?, ?, ?)');
    // Подготавливаем запрос на добавление новой книги в базу данных
    $stmt->execute([$title, $author, $cover, $file, $read_date, $allow_download]); // Выполняем запрос с передачей данных книги в качестве параметров
}

// Функция для обновления информации о книге в базе данных
function updateBook($id, $title, $author, $cover, $file, $read_date, $allow_download) {
    $db = getDbConnection(); // Получаем соединение с базой данных
    $stmt = $db->prepare('UPDATE books SET title = ?, author = ?, cover = ?, file = ?, read_date = ?, allow_download = ? WHERE id = ?');
    // Подготавливаем запрос на обновление информации о книге по её идентификатору
    $stmt->execute([$title, $author, $cover, $file, $read_date, $allow_download, $id]); // Выполняем запрос с передачей новых данных книги в качестве параметров
}

// Функция для удаления книги из базы данных по её идентификатору
function deleteBook($id) {
    $db = getDbConnection(); // Получаем соединение с базой данных
    $stmt = $db->prepare('DELETE FROM books WHERE id = ?'); // Подготавливаем запрос на удаление книги по её идентификатору
    $stmt->execute([$id]); // Выполняем запрос с передачей идентификатора книги в качестве параметра
}

// Функция для аутентификации пользователя по его имени пользователя и паролю
function authenticateUser($username, $password) {
    $db = getDbConnection(); // Получаем соединение с базой данных
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ? AND password = ?'); // Подготавливаем запрос на выборку пользователя по имени пользователя и паролю
    $stmt->execute([$username, $password]); // Выполняем запрос с передачей имени пользователя и пароля в качестве параметров
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Получаем данные пользователя в виде ассоциативного массива

    if ($user) {
        $_SESSION['user_id'] = $user['id']; // Если пользователь найден, инициализируем сессию с идентификатором пользователя
        return true; // Возвращаем true, если аутентификация успешна
    } else {
        return false; // Возвращаем false, если аутентификация неуспешна
    }
}

// Функция для проверки, авторизован ли текущий пользователь
function isLoggedIn() {
    return isset($_SESSION['user_id']); // Возвращаем true, если сессия пользователя установлена (т.е. пользователь авторизован), иначе false
}

// Функция для загрузки файла на сервер
function uploadFile($inputName, $destinationFolder, $maxFileSize = null, $prefix = '') {
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] != UPLOAD_ERR_OK) {
        return false; // Если файл не был успешно загружен, возвращаем false
    }

    $file = $_FILES[$inputName]; // Получаем информацию о загруженном файле
    if ($maxFileSize && $file['size'] > $maxFileSize) {
        return false; // Если размер файла превышает максимально допустимый, возвращаем false
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION); // Получаем расширение загруженного файла
    $filename = uniqid($prefix) . '.' . $extension; // Генерируем уникальное имя файла с префиксом

    $destination = $destinationFolder . $filename; // Полный путь к месту назначения для сохранения файла

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return false; // Если перемещение файла не удалось, возвращаем false
    }

    return $filename; // Возвращаем имя файла, если загрузка прошла успешно
}

// Функция для удаления файла с сервера по указанному пути
function deleteFile($filePath) {
    if (file_exists($filePath)) {
        unlink($filePath); // Если файл существует, удаляем его
    }
}

?>
