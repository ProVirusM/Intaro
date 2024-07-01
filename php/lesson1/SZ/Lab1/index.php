<?php
namespace main;

// Подключаем необходимые классы
include './testing_system/Exam.php';  // Подключение класса Exam из файла Exam.php
include './problems/Solution.php';   // Подключение класса Solution из файла Solution.php

use problems\Solution;   // Использование класса Solution из пространства имен problems
use testing_system\Exam;  // Использование класса Exam из пространства имен testing_system

ini_set('display_errors', 'Off');  // Отключение отображения ошибок (на практике рекомендуется Off)
function printResult($zadacha)
{
    $output = '';
    foreach ($zadacha as $key => $result) {
        $n = $key + 1;
        $mark = $result ? ' Верно' : 'Не верно';  // Определение результатов теста
        $output .= "<div>Тест $n : $mark</div>";  // Формирование строки вывода результата
    }
    echo $output;  // Вывод результата
}

$input1 = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['input1']){
    $input1 = $_POST['input1'];  // Получение значения input1 из POST запроса
}

$input2 = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['input2']){
    $input2 = $_POST['input2'];  // Получение значения input2 из POST запроса
}

// Создание экземпляра класса Exam для каждой задачи
$ex1 = new Exam('A');  // Создание экземпляра Exam с параметром 'A'
$zadacha1 = $ex1->examTests('problems\Solution::threeSharpAxes');  // Выполнение тестов для задачи "Три острых топора"

$ex2 = new Exam('B');  // Создание экземпляра Exam с параметром 'B'
$zadacha2 = $ex2->examTests('problems\Solution::sizeMatters');  // Выполнение тестов для задачи "Размер имеет значение"

$ex3 = new Exam('C');  // Создание экземпляра Exam с параметром 'C'
$zadacha3 = $ex3->examTests('problems\Solution::cutOnce');  // Выполнение тестов для задачи "Семь раз отмерь, один раз отрежь"
?>

<!-- HTML разметка для вывода результатов тестов -->
<div>
    <div>Три острых топора</div>
    <?php printResult($zadacha1); ?>  <!-- Вывод результатов для задачи "Три острых топора" -->
</div>
<div>
    <div>Размер имеет значение</div>
    <?php printResult($zadacha2); ?>  <!-- Вывод результатов для задачи "Размер имеет значение" -->
</div>
<div>
    <div>Семь раз отмерь, один раз отрежь</div>
    <?php printResult($zadacha3); ?>  <!-- Вывод результатов для задачи "Семь раз отмерь, один раз отрежь" -->
</div>
