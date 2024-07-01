<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Подключение автозагрузчика PHPMailer

// Подключение к базе данных
$config = parse_ini_file('parameters.ini'); // Чтение параметров подключения из ini файла

$host = $config['host']; // Хост базы данных
$dbname = $config['dbname']; // Имя базы данных
$username = $config['login']; // Логин пользователя базы данных
$password = $config['password']; // Пароль пользователя базы данных

try {
    // Подключение к PostgreSQL базе данных с использованием PDO
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Установка режима обработки ошибок
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage()); // В случае ошибки подключения выводим сообщение и прекращаем выполнение скрипта
}

// Проверка метода запроса (должен быть POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name']; // Получение имени из формы
    $email = $_POST['email']; // Получение email из формы
    $phone = $_POST['phone']; // Получение телефона из формы
    $comment = $_POST['comment']; // Получение комментария из формы
    $timestamp = time(); // Текущее время в формате Unix timestamp
    $timeToSend = date('H:i:s d.m.Y', strtotime('+1.5 hours', $timestamp)); // Время для отображения, через 1.5 часа от текущего

    // Проверка наличия записи с данным email в базе данных
    $stmt = $conn->prepare("SELECT timestamp FROM feedback WHERE email = :email");
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если запись найдена
    if ($result) {
        $lastEditTimestamp = strtotime($result['timestamp']); // Время последнего редактирования записи
        $timeDiff = $timestamp - $lastEditTimestamp; // Разница во времени с последнего редактирования
        $hoursDiff = $timeDiff / 3600; // Разница в часах

        // Если время с последнего редактирования менее 1 часа назад
        if ($hoursDiff < 1) {
            $response = array("success" => false, "message" => "Редактирование заявки возможно только через час после последнего редактирования.");
            echo json_encode($response); // Вывод сообщения об ошибке в формате JSON и завершение выполнения скрипта
            exit();
        }
    }

    // Если запись с данным email уже существует в базе данных, обновляем её
    if ($result) {
        $stmt = $conn->prepare("UPDATE feedback SET name = :name, phone = :phone, comment = :comment, timestamp = NOW() WHERE email = :email");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":comment", $comment);
        $stmt->bindParam(":email", $email);
        $stmt->execute(); // Выполнение запроса на обновление записи

        // Формирование ответа в случае успешного обновления
        $response = array("success" => true, "message" => "Заявка успешно обновлена", "name" => $name, "email" => $email, "phone" => $phone, "time" => $timeToSend);
        echo json_encode($response); // Вывод успешного ответа в формате JSON
    } else {
        // Если запись с данным email не найдена, добавляем новую запись
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, phone, comment, timestamp) VALUES (:name, :email, :phone, :comment, NOW())");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":comment", $comment);
        $stmt->execute(); // Выполнение запроса на добавление новой записи

        // Формирование ответа в случае успешного добавления
        $response = array("success" => true, "message" => "Заявка успешно добавлена", "name" => $name, "email" => $email, "phone" => $phone, "time" => $timeToSend);
        echo json_encode($response); // Вывод успешного ответа в формате JSON

        // Отправка уведомления на указанный email с использованием PHPMailer
        require_once('vendor/autoload.php');
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';

        $mail->isSMTP(); // Указываем, что используем SMTP для отправки писем
        $mail->Host = 'smtp.gmail.com'; // SMTP сервер для отправки писем
        $mail->SMTPAuth = true; // Включаем аутентификацию SMTP
        $mail->Username = 'danik4814let@gmail.com'; // Ваш логин от почты, с которой будут отправляться письма
        $mail->Password = 'akdjsjhbfgrllckk'; // Ваш пароль от почты, с которой будут отправляться письма
        $mail->SMTPSecure = 'ssl'; // Протокол шифрования, ssl - рекомендуется
        $mail->Port = 465; // Порт SMTP сервера

        $mail->setFrom('danik4814let@gmail.com'); // От кого будет отправлено письмо
        $mail->addAddress('danik4814let@gmail.com'); // Кому будет отправлено письмо
        $mail->isHTML(true); // Указываем, что содержимое письма в формате HTML

        $mail->Subject = 'Новое сообщение обратной связи'; // Тема письма
        $mail->Body    = 'Имя: ' . $name . "\n" .
            'E-mail: ' . $email . "\n" .
            'Телефон: ' . $phone . "\n" .
            'Комментарий: ' . $comment; // Текст письма

        // Отправка письма
        $mail->send();
        echo json_encode($response); // Вывод успешного ответа в формате JSON
    }
} else {
    echo json_encode(array("success" => false, "message" => "Данные не были отправлены")); // Вывод сообщения об ошибке в формате JSON, если данные не были отправлены
}

$conn = null; // Закрытие соединения с базой данных
?>
