<?php

function updateLinks($texts)
{
    // Регулярное выражение для поиска старых ссылок
    $pattern = "/http:\/\/asozd\.duma\.gov\.ru\/main\.nsf\/\(Spravka\)\?OpenAgent&RN=([0-9\-]+)&[0-9]+/";
    // Формат новой ссылки
    $replacement = "http://sozd.parlament.gov.ru/bill/$1";

    $updatedTexts = array(); // Массив для обновленных текстов

    foreach ($texts as $text) {
        // Замена всех найденных ссылок на новые в текущем тексте
        $updatedTexts[] = preg_replace($pattern, $replacement, $text);
    }

    return $updatedTexts;
}

// Чтение всех строк из текстового файла
$fileContent = file('file_with_links.txt', FILE_IGNORE_NEW_LINES);

// Обновление всех ссылок
$updatedTexts = updateLinks($fileContent);

// Вывод обновленных текстов
foreach ($updatedTexts as $updatedText) {
    echo $updatedText . PHP_EOL;

}


