<?php
session_start(); // Начинаем сессию PHP для работы с данными сессии
require 'functions.php'; // Подключаем файл с вспомогательными функциями

if (!isLoggedIn()) { // Проверяем, авторизован ли пользователь
    header('Location: login.php'); // Если не авторизован, перенаправляем на страницу входа
    exit; // Прекращаем выполнение скрипта
}

$id = $_GET['id']; // Получаем идентификатор книги из параметра GET
$book = getBook($id); // Получаем информацию о книге по её идентификатору

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Проверяем метод запроса (POST)
    $title = $_POST['title']; // Получаем название книги из формы
    $author = $_POST['author']; // Получаем автора книги из формы
    $read_date = $_POST['read_date']; // Получаем дату прочтения из формы
    $allow_download = isset($_POST['allow_download']) ? 1 : 0; // Получаем разрешение на скачивание из формы

    // Загружаем файл обложки книги с ограничением размера до 2 Мб
    $cover = uploadFile('cover', 'uploads/covers/', 2 * 1024 * 1024);
    // Если файл обложки был загружен успешно, удаляем старый файл обложки
    // Если загрузка не удалась, используем старое значение обложки
    if ($cover) {
        deleteFile('uploads/covers/' . $book['cover']);
    } else {
        $cover = $book['cover'];
    }

    // Загружаем файл книги с ограничением размера до 5 Мб
    $file = uploadFile('file', 'uploads/files/', 5 * 1024 * 1024);
    // Если файл книги был загружен успешно, удаляем старый файл книги
    // Если загрузка не удалась, используем старое значение файла книги
    if ($file) {
        deleteFile('uploads/files/' . $book['file']);
    } else {
        $file = $book['file'];
    }

    // Обновляем информацию о книге в базе данных
    updateBook($id, $title, $author, $cover, $file, $read_date, $allow_download);
    header('Location: index.php'); // Перенаправляем пользователя на главную страницу
    exit; // Прекращаем выполнение скрипта
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактировать книгу</title>
</head>
<body>
<h1>Редактировать книгу</h1>
<?php if (isset($error)): ?> <!-- Выводим сообщение об ошибке, если оно установлено -->
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>
<form action="edit_book.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <label for="title">Название:</label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($book['title']); ?>" required><br>
    <label for="author">Автор:</label>
    <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($book['author']); ?>" required><br>
    <label for="cover">Обложка (png, jpg):</label>
    <input type="file" name="cover" id="cover" accept="image/png, image/jpeg"><br>
    <label for="file">Файл с книгой (до 5Мб):</label>
    <input type="file" name="file" id="file"><br>
    <label for="read_date">Дата прочтения:</label>
    <input type="date" name="read_date" id="read_date" value="<?php echo $book['read_date']; ?>" required><br>
    <label for="allow_download">Разрешить скачивание:</label>
    <input type="checkbox" name="allow_download" id="allow_download" <?php echo $book['allow_download'] ? 'checked' : ''; ?>><br>
    <button type="submit">Сохранить изменения</button>
</form>
</body>
</html>
