<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма обратной связи</title>
    <style>
        .error {
            border: 1px solid red; /* Стиль для элементов с ошибками */
        }
    </style>
</head>
<body>

<div id="feedback-form">
    <form id="contactForm" method="post" action="submit_form.php"> <!-- Форма отправки данных на сервер -->
        <div>
            <label for="name">ФИО:</label><br>
            <input type="text" id="name" name="name"><br> <!-- Поле для ввода ФИО -->
        </div>
        <div>
            <label for="email">Почта:</label><br>
            <input type="email" id="email" name="email"><br> <!-- Поле для ввода почты -->
        </div>
        <div>
            <label for="phone">Телефон:</label><br>
            <input type="text" id="phone" name="phone"><br> <!-- Поле для ввода телефона -->
        </div>
        <div>
            <label for="comment">Комментарий:</label><br>
            <textarea id="comment" name="comment"></textarea><br> <!-- Поле для ввода комментария -->
        </div>
        <div>
            <button type="submit">Отправить</button> <!-- Кнопка отправки формы -->
        </div>
    </form>
</div>

<div id="message" style="display: none;"></div> <!-- Контейнер для отображения сообщений обратной связи -->

<script>
    // Обработчик события отправки формы
    document.getElementById('contactForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Отмена стандартного действия отправки формы

        clearErrors(); // Очистка отображения ошибок

        // Получение значений полей формы
        var name = document.getElementById('name').value.trim();
        var email = document.getElementById('email').value.trim();
        var phone = document.getElementById('phone').value.trim();
        var comment = document.getElementById('comment').value.trim();

        // Проверка на заполнение всех полей
        if (name === '' || email === '' || phone === '' || comment === '') {
            displayError('Заполните все поля.'); // Отображение ошибки
            return;
        }

        // Проверка правильного формата электронной почты
        if (!validateEmail(email)) {
            displayError('Неправильный формат почты.');
            document.getElementById('email').classList.add('error'); // Добавление класса для стилизации ошибки
            return;
        }

        // Проверка правильного формата номера телефона
        if (!validatePhone(phone)) {
            displayError('Неправильный формат телефона.');
            document.getElementById('phone').classList.add('error'); // Добавление класса для стилизации ошибки
            return;
        }

        // Создание объекта FormData для передачи данных формы
        var formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('comment', comment);

        // Создание XMLHttpRequest для отправки данных на сервер
        var xhr = new XMLHttpRequest();
        xhr.open('POST', "submit_form.php", true); // Указание метода и пути к обработчику формы
        xhr.onload = function() {
            if (xhr.status === 200) { // Проверка успешности запроса
                var response = JSON.parse(xhr.responseText); // Парсинг ответа сервера
                if (response.success) { // Если отправка данных прошла успешно
                    // Скрытие формы и отображение сообщения с информацией о сообщении
                    document.getElementById('feedback-form').style.display = 'none';
                    document.getElementById('message').style.display = 'block';
                    document.getElementById('message').innerHTML = 'Оставлено сообщение из формы обратной связи.<br>' +
                        'Имя: ' + response.name + '<br>' +
                        'E-mail: ' + response.email + '<br>' +
                        'Телефон: ' + response.phone + '<br>' +
                        'С Вами свяжутся после ' + response.time;
                } else {
                    displayError(response.message); // Отображение сообщения об ошибке
                }
            } else {
                displayError('Ошибка ' + xhr.status + ': ' + xhr.statusText); // Отображение сообщения об ошибке запроса
            }
        };
        xhr.send(formData); // Отправка данных на сервер
    });

    // Функция для очистки отображения ошибок
    function clearErrors() {
        var errorElements = document.querySelectorAll('.error');
        errorElements.forEach(function(element) {
            element.classList.remove('error'); // Удаление класса ошибки для стилизации
        });
        document.getElementById('message').style.display = 'none'; // Скрытие сообщений обратной связи
    }

    // Функция для отображения сообщений об ошибке
    function displayError(message) {
        document.getElementById('message').innerHTML = message; // Отображение сообщения об ошибке
        document.getElementById('message').style.display = 'block'; // Отображение блока с сообщениями
    }

    // Функция для проверки формата электронной почты
    function validateEmail(email) {
        var re = /\S+@\S+\.\S+/;
        return re.test(email); // Возвращение результата проверки формата
    }

    // Функция для проверки формата номера телефона
    function validatePhone(phone) {
        var re = /^\+?\d{10,}$/;
        return re.test(phone); // Возвращение результата проверки формата
    }
</script>

</body>
</html>
