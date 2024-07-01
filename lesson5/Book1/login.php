<?php
session_start(); // Старт сессии для работы с сессионными данными
require 'functions.php'; // Подключение файла с функциями для работы с базой данных и аутентификации

// Если форма была отправлена методом POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // Получаем имя пользователя из формы
    $password = $_POST['password']; // Получаем пароль пользователя из формы

    // Пытаемся аутентифицировать пользователя по введенным данным
    if (authenticateUser($username, $password)) {
        header('Location: index.php'); // Перенаправляем пользователя на главную страницу после успешной аутентификации
        exit; // Прекращаем выполнение текущего скрипта
    } else {
        $error = 'Неправильное имя пользователя или пароль.'; // Устанавливаем сообщение об ошибке
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Вход</title>
</head>
<body>
<h1>Вход</h1>
<?php if (isset($error)): ?>
    <p style="color: red;"><?php echo $error; ?></p> <!-- Вывод сообщения об ошибке, если переменная $error установлена -->
<?php endif; ?>
<form action="login.php" method="post">
    <label for="username">Имя пользователя:</label>
    <input type="text" name="username" id="username" required><br> <!-- Поле для ввода имени пользователя -->
    <label for="password">Пароль:</label>
    <input type="password" name="password" id="password" required><br> <!-- Поле для ввода пароля -->
    <button type="submit">Войти</button> <!-- Кнопка для отправки формы -->
</form>
</body>
</html>
