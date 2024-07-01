<?php
session_start(); // Начинаем сессию PHP для работы с сессией
require 'functions.php'; // Подключаем файл с вспомогательными функциями

if (!isLoggedIn()) { // Проверяем, авторизован ли пользователь
    header('Location: index.php'); // Если не авторизован, перенаправляем на главную страницу
    exit; // Прекращаем выполнение скрипта
}

$book_id = $_GET['id']; // Получаем идентификатор книги из параметра GET
$book = getBook($book_id); // Получаем информацию о книге по её идентификатору

// Удаляем файлы обложки и книги из директорий uploads/covers и uploads/files
deleteFile('uploads/covers/' . $book['cover']); 
deleteFile('uploads/files/' . $book['file']);

deleteBook($book_id); // Удаляем запись о книге из базы данных

header('Location: index.php'); // Перенаправляем пользователя на главную страницу
exit; // Прекращаем выполнение скрипта
?>
