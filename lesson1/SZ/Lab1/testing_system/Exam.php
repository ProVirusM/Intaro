<?php
namespace testing_system;

class Exam
{
    private $dirTests; // Директория с тестами
    private $tests;    // Ассоциативный массив для хранения данных и ответов тестов

    // Конструктор класса, принимающий название директории с тестами
    public function __construct($dirTests)
    {
        $this->dirTests = "tests/$dirTests";  // Устанавливаем путь к директории с тестами
        $this->tests = [
            'dat' => [],  // Массив для данных тестов
            'ans' => []   // Массив для ответов тестов
        ];
    }

    // Метод инициализации тестов: считывает данные из файлов и загружает их в массив $tests
    private function initTests()
    {
        $files = scandir($this->dirTests);  // Получаем список файлов в директории с тестами
        unset($files[0], $files[1]);        // Удаляем . и .. из списка файлов (системные файлы)

        foreach ($files as $file) {
            $content = "$this->dirTests/$file";  // Путь к файлу с тестом

            if (str_ends_with($file, 'dat')) {
                // Если файл с расширением .dat, считываем его содержимое в массив данных
                $this->tests['dat'][] = trim(file_get_contents($content), "\n");
            } else {
                // Если файл с расширением не .dat (например, .ans), считываем его содержимое в массив ответов
                $this->tests['ans'][] = trim(file_get_contents($content), "\n");
            }
        }
    }

    // Метод для выполнения одного теста с помощью указанной функции обратного вызова
    public function examIter($callback, $numberTest)
    {
        $this->initTests();  // Инициализируем тесты

        // Вызываем функцию обратного вызова $callback с данными из файла $numberTest
        $result = call_user_func($callback, $this->tests['dat'][$numberTest]);

        // Сравниваем результат функции с ожидаемым ответом и выводим соответствующее сообщение
        print_r($result == $this->tests['ans'][$numberTest] ? 'Правильно' : 'Не правильно');
    }

    // Метод для выполнения всех тестов с помощью указанной функции обратного вызова
    public function examTests($callback)
    {
        $this->initTests();  // Инициализируем тесты

        $results = [];  // Массив для хранения результатов тестов

        // Проходим по всем тестам и проверяем функцию обратного вызова на каждом из них
        for ($i = 0; $i < count($this->tests['dat']); $i++) {
            // Вызываем функцию обратного вызова $callback с данными из файла $i
            $result = call_user_func($callback, $this->tests['dat'][$i]);

            // Сравниваем результат функции с ожидаемым ответом и добавляем результат проверки в массив
            $results[] = ($result == $this->tests['ans'][$i]);

            // Если результат не соответствует ожидаемому, выводим номер теста и результат функции
            if (!($result == $this->tests['ans'][$i])) {
                print_r("<pre>" . "$i<br>" . $result . "</pre>");
            }
        }

        return $results;  // Возвращаем массив результатов проверки тестов
    }
}
?>
