<?php
session_start(); // Начинаем сеанс PHP для работы с сессиями
require 'functions.php'; // Подключаем файл с вспомогательными функциями

// Проверяем, авторизован ли пользователь. Если нет, перенаправляем на страницу логина
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Проверяем, была ли отправлена форма методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $title = $_POST['title'];
    $author = $_POST['author'];
    $read_date = $_POST['read_date'];
    $allow_download = isset($_POST['allow_download']) ? 1 : 0; // Проверяем, установлен ли чекбокс

    // Загружаем обложку книги с проверкой на тип и размер файла (максимум 2Мб)
    $cover = uploadFile('cover', 'uploads/covers/', 2 * 1024 * 1024);
    
    // Загружаем файл книги с проверкой на тип и размер файла (максимум 5Мб)
    $file = uploadFile('file', 'uploads/files/', 5 * 1024 * 1024);

    // Если обе загрузки прошли успешно, добавляем книгу в базу данных
    if ($cover && $file) {
        addBook($title, $author, $cover, $file, $read_date, $allow_download);
        // Перенаправляем пользователя на главную страницу после успешного добавления
        header('Location: index.php');
        exit;
    } else {
        // Если возникла ошибка при загрузке файлов, выводим сообщение об ошибке
        $error = 'Ошибка загрузки файлов. Убедитесь, что файлы соответствуют требованиям.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Добавить книгу</title>
</head>
<body>
<h1>Добавить книгу</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p> <!-- Выводим сообщение об ошибке, если есть -->
<?php endif; ?>
<form action="add_book.php" method="post" enctype="multipart/form-data">
    <!-- Форма для добавления книги -->
    <label for="title">Название:</label>
    <input type="text" name="title" id="title" required><br>
    <label for="author">Автор:</label>
    <input type="text" name="author" id="author" required><br>
    <label for="cover">Обложка (png, jpg):</label>
    <input type="file" name="cover" id="cover" accept="image/png, image/jpeg" required><br>
    <label for="file">Файл с книгой (до 5Мб):</label>
    <input type="file" name="file" id="file" required><br>
    <label for="read_date">Дата прочтения:</label>
    <input type="date" name="read_date" id="read_date" required><br>
    <label for="allow_download">Разрешить скачивание:</label>
    <input type="checkbox" name="allow_download" id="allow_download"><br>
    <button type="submit">Добавить книгу</button>
</form>
</body>
</html>
