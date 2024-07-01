<?php
header('Content-Type: application/json'); // Устанавливаем заголовок для ответа в формате JSON

$apiKey = 'e146b000-29ac-450d-9ce2-c8571bf8108a'; // API ключ для доступа к сервисам Яндекса
$latitude = $_POST['latitude'] ?? ''; // Получаем широту из POST запроса
$longitude = $_POST['longitude'] ?? ''; // Получаем долготу из POST запроса

// Проверяем, что координаты не пусты
if (empty($latitude) || empty($longitude)) {
    echo json_encode(['error' => 'Координаты не указаны']); // Возвращаем ошибку, если координаты отсутствуют
    exit;
}

// Формируем URL для запроса к геокодеру Яндекса для получения адреса по координатам
$geocodeUrl = 'https://geocode-maps.yandex.ru/1.x/?format=json&apikey=' . $apiKey . '&geocode=' . $longitude . ',' . $latitude;

// Выполняем запрос к геокодеру Яндекса
$geocodeResponse = file_get_contents($geocodeUrl);
if ($geocodeResponse === FALSE) {
    echo json_encode(['error' => 'Ошибка запроса к API Геокодера', 'debug' => 'URL: ' . $geocodeUrl]); // Возвращаем ошибку, если запрос не удался
    exit;
}

// Декодируем ответ от геокодера из JSON в массив PHP
$geocodeData = json_decode($geocodeResponse, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Ошибка декодирования JSON', 'debug' => $geocodeResponse]); // Возвращаем ошибку, если декодирование JSON не удалось
    exit;
}

// Проверяем наличие адреса в ответе от геокодера
if (!isset($geocodeData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'])) {
    echo json_encode(['error' => 'Не удалось найти адрес', 'debug' => $geocodeData]); // Возвращаем ошибку, если адрес не найден
    exit;
}

// Извлекаем структурированный адрес и координаты из ответа геокодера
$geoObject = $geocodeData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
$structuredAddress = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['formatted'];
$coordinates = explode(' ', $geoObject['Point']['pos']);
$longitude = $coordinates[0];
$latitude = $coordinates[1];

// Формируем URL для запроса к сервису Яндекс.Карты для получения информации о ближайших метро
$metroParameters = [
    'apikey' => $apiKey,
    'geocode' => $longitude . ',' . $latitude, // Правильный формат для координат
    'kind' => 'metro',
    'format' => 'json'
];
$metroResponse = file_get_contents('https://geocode-maps.yandex.ru/1.x/?' . http_build_query($metroParameters));

// Проверяем успешность запроса к сервису метро
if ($metroResponse === FALSE) {
    echo json_encode(['error' => 'Ошибка запроса к API метро', 'debug' => 'URL: ' . $metroUrl]); // Возвращаем ошибку, если запрос не удался
    exit;
}

// Декодируем ответ от сервиса метро из JSON в массив PHP
$metroData = json_decode($metroResponse, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Ошибка декодирования JSON для метро', 'debug' => $metroResponse]); // Возвращаем ошибку, если декодирование JSON не удалось
    exit;
}

// Проверяем наличие информации о ближайшем метро в ответе от сервиса метро
if (!isset($metroData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'])) {
    echo json_encode(['error' => 'Ближайшее метро не найдено', 'debug' => $metroData]); // Возвращаем ошибку, если метро не найдено
    exit;
}

// Извлекаем название ближайшего метро
$nearestMetro = $metroData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
$nearestStationName = $nearestMetro;

// Формируем и возвращаем ответ в формате JSON с информацией о местоположении и ближайшем метро
$response = [
    'address' => $structuredAddress,
    'coordinates' => [
        'latitude' => $latitude,
        'longitude' => $longitude
    ],
    'nearestStation' => $nearestStationName
];

echo json_encode($response); // Выводим ответ в формате JSON
?>
