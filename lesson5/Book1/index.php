<?php
// Устанавливаем настройки для вывода всех ошибок на экран
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Стартуем сессию для работы с сессионными переменными
require 'functions.php'; // Подключаем файл с функциями для работы с базой данных и файлами

// Если в URL присутствует параметр 'download' и 'id'
if (isset($_GET['download']) && isset($_GET['id'])) {
    $id = $_GET['id']; // Получаем идентификатор книги из параметра 'id'
    $book = getBook($id); // Получаем информацию о книге по её идентификатору

    // Проверяем, что книга найдена и разрешено её скачивание
    if ($book && $book['allow_download']) {
        $file = 'uploads/files/' . $book['file']; // Путь к файлу книги на сервере

        // Проверяем, существует ли файл на сервере
        if (file_exists($file)) {
            // Устанавливаем заголовки HTTP для скачивания файла
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            header('Content-Length: ' . filesize($file));

            // Читаем содержимое файла и отправляем его пользователю
            readfile($file);
            exit; // Завершаем выполнение скрипта после скачивания файла
        }
    }
}

// Получаем список всех книг из базы данных
$books = getBooks();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Библиотека</title>
    <style>
        /* Стили для фиксированного размера обложки книги */
        .book-cover {
            width: 270px; /* Ширина обложки */
            height: auto; /* Высота автоматически подстраивается для сохранения пропорций */
        }
    </style>
</head>
<body>
<p>
    <?php if (!isLoggedIn()): ?>
        <a href="login.php">Войти</a> <!-- Ссылка для входа, если пользователь не авторизован -->
    <?php else: ?>
        <a href="logout.php">Выйти</a> <!-- Ссылка для выхода, если пользователь авторизован -->
    <?php endif; ?>
</p>

<?php if (isLoggedIn()): ?>
    <a href="add_book.php">Добавить книгу</a> <!-- Ссылка для добавления новой книги, доступна только авторизованным пользователям -->
<?php endif; ?>

<ul>
    <?php foreach ($books as $book): ?>
        <li>
            <h3><?php echo htmlspecialchars($book['title']); ?></h3> <!-- Вывод названия книги -->
            <p>Автор: <?php echo htmlspecialchars($book['author']); ?></p> <!-- Вывод автора книги -->
            <p>Дата: <?php echo htmlspecialchars($book['read_date']); ?></p> <!-- Вывод даты прочтения книги -->

            <!-- Вывод обложки книги с применением стилей для фиксированного размера -->
            <img class="book-cover" src="uploads/covers/<?php echo htmlspecialchars($book['cover']); ?>" alt="Обложка">

            <p>
                <?php if ($book['allow_download']): ?> <!-- Если разрешено скачивание книги -->
                    <a href="?download&id=<?php echo $book['id']; ?>">Скачать</a> <!-- Ссылка для скачивания книги -->
                <?php endif; ?>
            </p>

            <?php if (isLoggedIn()): ?> <!-- Если пользователь авторизован -->
                <p><a href="edit_book.php?id=<?php echo $book['id']; ?>">Редактировать</a></p> <!-- Ссылка для редактирования книги -->
                <p><a href="delete_book.php?id=<?php echo $book['id']; ?>">Удалить</a></p> <!-- Ссылка для удаления книги -->
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>
