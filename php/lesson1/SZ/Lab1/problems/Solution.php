<?php

namespace problems;

class Solution
{
    // Задача "Три острых топора"
    public static function threeSharpAxes($input): int
    {
        $inputArray = [];

        // Разбиваем входную строку по переносу строки
        $tmpArray = explode("\n", $input);
        
        // Обрабатываем каждую строку
        foreach ($tmpArray as $item) {
            // Если строка содержит 3 элемента, считаем это ставкой
            if (count(explode(" ", $item)) == 3) {
                // Разделяем строку на номер матча, сумму ставки и результат
                list($n, $total, $result) = explode(" ", $item);
                // Формируем ассоциативный массив для ставки и добавляем его в общий массив ставок
                $bet = compact('n', 'total', 'result');
                $inputArray['bets'][] = $bet;
                continue;
            }
            
            // Если строка содержит 5 элементов, считаем это информацией о матче
            if (count(explode(" ", $item)) == 5) {
                // Разделяем строку на номер матча, коэффициенты для команд и результат
                list($n, $L, $R, $D, $result) = explode(" ", $item);
                // Формируем ассоциативный массив для матча и добавляем его в общий массив матчей
                $match = compact('n', 'L', 'R', 'D', 'result');
                $inputArray['matches'][] = $match;
                continue;
            }
            
            // Если строка не подходит ни под одно условие, просто разбиваем её на элементы
            $inputArray[] = explode(" ", $item);
        }

        // Подсчет общей суммы ставок и выигрыша
        $overTotalBets = 0;  // Общая сумма ставок
        $totalWin = 0;       // Выигрыш со ставок

        // Обработка каждой ставки
        foreach ($inputArray['bets'] as $bet) {
            // Добавляем сумму ставки к общей сумме ставок
            $overTotalBets += (int)$bet['total'];
            // Находим соответствующий матч для текущей ставки
            $match = $inputArray['matches'][(int)$bet['n'] - 1];
            // Проверяем, выиграла ли ставка
            if ($bet['result'] == $match['result']) {
                // Рассчитываем выигрыш и добавляем его к общему выигрышу
                $totalWin += (int)$bet['total'] * (float)$match[$bet['result']];
            }
        }

        // Возвращаем разницу между общим выигрышем и общей суммой ставок
        return $totalWin - $overTotalBets;
    }

    // Задача "Размер имеет значение"
    public static function sizeMatters($input): string
    {
        $inputArray = [];
        $tmpArray = explode("\n", $input);

        // Обработка каждой строки входных данных
        foreach ($tmpArray as $item) {
            // Разбиваем строку по символу ':'
            $item = explode(":", $item);
            // Удаляем пустые элементы
            $item = array_diff($item, array(''));
            // Дополняем каждый элемент нулями до четырех символов
            $item = array_map(function ($value) {
                if (strlen($value) < 4) {
                    $value = str_split($value);  // Разбиваем строку на символы
                    $zeroArray = array_fill(0, 4 - count($value), 0);  // Создаем массив нулей
                    return implode('', array_merge($zeroArray, $value));  // Сливаем массивы в строку
                }
                return $value;
            }, (array)$item);
            $inputArray[] = $item;  // Добавляем обработанный элемент в итоговый массив
        }

        // Дополняем каждый элемент массива до восьми элементов
        foreach ($inputArray as $key => $item) {
            if (count($item) < 8) {
                $tmp = 8 - count($item);  // Определяем количество нулей, которые нужно добавить
                for ($i = 0; $i < 8; $i++) {
                    // Если ключ отсутствует в массиве, добавляем элемент с нулями
                    if (!key_exists($i, $item)) {
                        $res = [];
                        for ($j = 0; $j < $tmp; $j++) {
                            $res[] = '0000';  // Создаем массив из нулей нужного размера
                        }
                        array_splice($item, $i, 0, $res);  // Вставляем массив нулей в исходный массив
                    }
                }
            }
            $inputArray[$key] = implode(':', $item);  // Объединяем элементы массива в строку
        }

        $output = implode("\n", $inputArray);  // Объединяем строки массива в одну строку
        return $output;  // Возвращаем итоговую строку
    }

    // Задача "Семь раз отмерь, один раз отрежь"
    public static function cutOnce($input): string
    {
        // Разбиваем входную строку на значения и параметры валидации
        $inputArray = array_map(function ($v) {
            return preg_split("/(?<=\>)\s/", $v);  // Разбиваем строку по разделителю '>'
        }, explode("\n", $input));

        // Обрабатываем каждый элемент массива
        $result = array_map(function ($v) {
            switch ($v[1][0]) {
                case 'S':  // Валидация для обычной строки
                    $params = explode(" ", $v[1]);  // Разбиваем параметры на отдельные элементы
                    // Проверяем длину строки
                    return strlen(trim($v[0], "<>")) <= (int)$params[2] &&
                        strlen(trim($v[0], "<>")) >= (int)$params[1] ?
                        "OK" : "FAIL";  // Возвращаем результат валидации
                case 'N':  // Валидация для числа
                    $params = explode(" ", $v[1]);  // Разбиваем параметры на отдельные элементы
                    $str = trim($v[0], "<>");  // Удаляем '<' и '>'
                    // Проверяем, является ли строка числом и входит ли в заданный диапазон
                    if (preg_match("#^(-\d+)$|^(\d+)$#", $str)) {
                        return (int)$str <= (int)$params[2] &&
                            (int)$str >= (int)$params[1] ?
                            "OK" : "FAIL";  // Возвращаем результат валидации
                    }
                    return "FAIL";  // Если не является числом, возвращаем FAIL
                case 'P':  // Валидация для номера телефона
                    return preg_match('#^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$#', trim($v[0], "<>")) ?
                        "OK" : "FAIL";  // Возвращаем результат валидации
                case 'D':  // Валидация для даты
                    $matches = [];
                    if (preg_match('#^([0-9]|[0-2][0-9]|[3][0-1]).([0][0-9]|[1][0-1]|[0-9]|12).(\d{4}) ([0-1][0-9]|[2][0-3]|[0-9]):([0-5][0-9])$#',
                        trim($v[0], "<>"), $matches)) {
                        // Проверяем дни февраля в високосный и невисокосный годы
                        if ((int)$matches[2] == 2) {
                            if ((int)$matches[1] <= 28 && (int)$matches[3] % 4 != 0) return "OK";
                            if ((int)$matches[1] <= 29 && (int)$matches[3] % 4 == 0) return "OK";
                            else return "FAIL";
                        }
                        return "OK";  // Возвращаем результат валидации
                    }
                    return "FAIL";  // Если не проходит по регулярному выражению, возвращаем FAIL
                case 'E':  // Валидация для электронной почты
                    return preg_match('#^(^[a-zA-Z0-9][a-zA-Z0-9_]{3,29})@([a-zA-Z]{2,30})\.([a-z]{2,10})$#',
                        trim($v[0], "<>")) ?
                        "OK" : "FAIL";  // Возвращаем результат валидации
            }
            return "FAIL";  // Возвращаем FAIL, если не удалось распознать тип валидации
        }, $inputArray);

        $output = implode("\n", $result);  // Объединяем результаты в одну строку
        return $output;  // Возвращаем итоговую строку
    }
}

?>
