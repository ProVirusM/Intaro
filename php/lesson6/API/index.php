<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Геокодер Яндекс.Карт</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
        }
        header {
            background-color: #4CAF50;
            color: white;
            padding: 10px 0;
            width: 100%;
            text-align: center;
        }
        main {
            padding: 20px;
            text-align: center;
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            width: 100%;
        }
        #map {
            width: 700px;
            height: 70vh; /* Увеличили высоту карты */
        }
        #data-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>Геокодер Яндекс.Карт</h1>
</header>
<main>
    <div id="map"></div> <!-- Див для отображения карты -->
    <div id="data-container"></div> <!-- Див для отображения данных о местоположении -->
</main>
<footer>
    <p>&copy; 2024 Геокодер Яндекс.Карт. Все права защищены.</p>
</footer>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_UA&apikey=e146b000-29ac-450d-9ce2-c8571bf8108a" type="text/javascript"></script>
<script>
    ymaps.ready(init);

    function init() {
        var map = new ymaps.Map("map", {
            center: [55.751574, 37.573856], // Центр карты (Москва)
            zoom: 10 // Уровень масштабирования
        });

        var placemark;

        // Обработчик события клика на карту
        map.events.add('click', function (e) {
            var coords = e.get('coords'); // Координаты клика

            if (placemark) {
                placemark.geometry.setCoordinates(coords); // Перемещаем метку на новые координаты
            } else {
                placemark = new ymaps.Placemark(coords, {}, {
                    preset: 'islands#redDotIcon' // Стиль метки
                });
                map.geoObjects.add(placemark); // Добавляем метку на карту
            }

            fetchAddress(coords); // Вызываем функцию для получения адреса по координатам
        });
    }

    async function fetchAddress(coords) {
        const [latitude, longitude] = coords; // Извлекаем широту и долготу из координат
        const response = await fetch('geocode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'latitude=' + encodeURIComponent(latitude) + '&longitude=' + encodeURIComponent(longitude)
        });
        const data = await response.json(); // Получаем ответ в формате JSON

        const container = document.getElementById('data-container'); // Контейнер для вывода данных
        if (data.error) {
            container.innerHTML = `<p><strong>Ошибка:</strong> ${data.error}</p>`; // Выводим ошибку, если она есть
        } else {
            // Выводим данные о местоположении (адрес, координаты, ближайшее метро)
            container.innerHTML = `
                    <p><strong>Адрес:</strong> ${data.address}</p>
                    <p><strong>Координаты:</strong> Широта: ${data.coordinates.latitude}, Долгота: ${data.coordinates.longitude}</p>
                    <p><strong>Ближайшее метро:</strong> ${data.nearestStation}</p>
                `;
        }
    }
</script>
</body>
</html>
