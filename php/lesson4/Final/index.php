<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>PHP Tasks</title>
</head>
<body>

<?php
// Функция для обработки задачи A
function process_advertisements($input) {
    // Разбиваем входные данные на строки
    $entries = explode("\n", trim($input));
    $advertisements = [];

    // Обходим каждую строку
    foreach ($entries as $entry) {
        if (!empty($entry)) {
            // Разбиваем строку на части по пробелам
            $parts = preg_split('/\s+/', trim($entry));

            // Проверяем, что количество частей достаточно для обработки
            if (count($parts) >= 3) {
                // Извлекаем необходимые данные из частей
                $banner_id = $parts[0]; // Идентификатор баннера
                $date = $parts[1]; // Дата показа
                $time = $parts[2]; // Время показа

                // Преобразуем дату и время в формат Unix timestamp
                $datetime = strtotime($date . ' ' . $time);
                $datetime_str = $date . ' ' . $time;

                // Проверяем, есть ли уже такой баннер в массиве
                if (isset($advertisements[$banner_id])) {
                    // Увеличиваем счетчик показов и обновляем время, если необходимо
                    $advertisements[$banner_id]['count']++;
                    if ($datetime > $advertisements[$banner_id]['time']) {
                        $advertisements[$banner_id]['time'] = $datetime;
                        $advertisements[$banner_id]['datetime_str'] = $datetime_str;
                    }
                } else {
                    // Создаем новую запись для данного баннера
                    $advertisements[$banner_id] = [
                        'count' => 1,
                        'time' => $datetime,
                        'datetime_str' => $datetime_str
                    ];
                }
            }
        }
    }

    // Формируем вывод в требуемом формате
    $result = '';
    foreach ($advertisements as $banner_id => $ad) {
        $result .= "{$ad['count']} {$banner_id} {$ad['datetime_str']}\n";
    }

    return $result;
}

// Функция для обработки задачи B
function process_catalog($input) {
    $lines = explode("\n", trim($input));
    $sections = [];

    // Обходим каждую строку
    foreach ($lines as $line) {
        list($id, $name, $left, $right) = explode(' ', $line);
        $sections[] = [
            'id' => (int)$id,
            'name' => $name,
            'left' => (int)$left,
            'right' => (int)$right,
        ];
    }

    // Сортируем секции по левому значению
    usort($sections, function($a, $b) {
        return $a['left'] <=> $b['left'];
    });

    $stack = [];
    $tree = [];
    foreach ($sections as $section) {
        // Управляем стеком для создания дерева секций
        while (!empty($stack) && end($stack)['right'] < $section['right']) {
            array_pop($stack);
        }
        $level = count($stack);
        $tree[] = str_repeat('-', $level) . $section['name'];
        $stack[] = $section;
    }

    return implode("\n", $tree);
}

// Функция для обработки задачи C
function process_banners($input) {
    $lines = explode("\n", trim($input));
    $banners = [];

    // Обходим каждую строку
    foreach ($lines as $line) {
        list($id, $weight) = explode(' ', $line);
        $banners[] = [
            'id' => $id,
            'weight' => (int)$weight
        ];
    }

    // Вычисляем общий вес баннеров
    $total_weight = array_sum(array_column($banners, 'weight'));
    $num_shows = 1000000; // Количество имитаций показов

    // Инициализируем массив для подсчета показов
    $show_counts = array_fill(0, count($banners), 0);

    // Имитируем показы баннеров
    for ($i = 0; $i < $num_shows; $i++) {
        $random = rand(0, $total_weight - 1); // Генерируем случайное число
        $sum = 0;
        foreach ($banners as $index => $banner) {
            // Определяем, какой баннер был показан
            $sum += $banner['weight'];
            if ($random < $sum) {
                $show_counts[$index]++;
                break;
            }
        }
    }

    // Вычисляем пропорции показов и формируем вывод
    $output = [];
    foreach ($banners as $index => $banner) {
        $proportion = $show_counts[$index] / $num_shows; // Вычисляем пропорцию
        $output[] = "{$banner['id']} " . number_format($proportion, 6, '.', ''); // Формируем строку вывода
    }

    return implode("\n", $output);
}

// Функция для обработки задачи D
function minify_css($input) {
    // Удаляем комментарии
    $content = preg_replace('/\/\*.*?\*\//s', '', $input);

    // Удаляем пустые блоки
    $content = preg_replace('/[^\}]*\{\s*\}/', '', $content);

    // Удаляем пробелы вокруг символов {, }, ;, :, и ,
    $content = preg_replace('/\s*([{};,:])\s*/', '$1', $content);

    // Удаляем лишние точки с запятой
    $content = str_replace(';}', '}', $content);

    // Заменяем множественные пробелы одним
    $content = preg_replace('/\s+/', ' ', $content);

    // Удаляем нули с единицами измерения
    $content = preg_replace('/\b0(px|em|%|rem|vh|vw)\b/', '0', $content);

    // Сокращаем шестнадцатеричные цвета
    $content = preg_replace('/#([0-9a-fA-F])\1([0-9a-fA-F])\2([0-9a-fA-F])\3\b/', '#$1$2$3', $content);

    // Заменяем некоторые шестнадцатеричные цвета на именованные
    $replacements = [
        '#FF0000' => 'red',
        '#CD853F' => 'peru',
        '#FFC0CB' => 'pink',
        '#DDA0DD' => 'plum',
        '#FFFAFA' => 'snow',
        '#D2B48C' => 'tan',
        '#F00' => 'red'
    ];
    $content = str_replace(array_keys($replacements), array_values($replacements), $content);

    // Обрабатываем shorthand-свойства margin и padding
    $content = preg_replace_callback('/\{([^}]*)\}/', function ($matches) {
        $styles = $matches[1];

        // Собираем shorthand-свойства
        $shorthandProperties = ['margin', 'padding'];
        foreach ($shorthandProperties as $property) {
            $properties = [];
            preg_match_all("/{$property}-(top|right|bottom|left):([^;]*);/", $styles, $matchesAll);
            if (count($matchesAll[0]) == 4) {
                foreach ($matchesAll[1] as $index => $side) {
                    $properties[$side] = $matchesAll[2][$index];
                }
                $styles = preg_replace("/{$property}-(top|right|bottom|left):[^;]*;/", '', $styles);
                $styles .= "{$property}:{$properties['top']} {$properties['right']} {$properties['bottom']} {$properties['left']};";
            }
        }

        return "{" . trim($styles) . "}";
    }, $content);

    // Оптимизируем shorthand-свойства margin и padding
    $content = preg_replace_callback('/\b(margin|padding):([^;]*);/', function ($matches) {
        $values = preg_split('/\s+/', trim($matches[2]));
        if (count($values) == 4) {
            return "{$matches[1]}:{$values[0]} {$values[1]} {$values[2]} {$values[3]};";
        }
        if (count($values) == 3) {
            return "{$matches[1]}:{$values[0]} {$values[1]} {$values[2]} {$values[1]};";
        }
        if (count($values) == 2) {
            return "{$matches[1]}:{$values[0]} {$values[1]} {$values[0]} {$values[1]};";
        }
        return "{$matches[1]}:{$values[0]};";
    }, $content);

    return $content;
}

// Функция для проверки результатов выполнения задачи
function check_output($task_name) {
    $input_dir = "tasks/$task_name/input";
    $output_dir = "tasks/$task_name/output";

    $input_files = glob("$input_dir/*.txt");
    $expected_output_files = glob("$output_dir/*.txt");

    $results = [];

    // Обходим каждый входной файл
    foreach ($input_files as $index => $input_file) {
        $expected_output_file = $expected_output_files[$index];

        // Читаем входные и ожидаемые выходные данные
        $input = file_get_contents($input_file);
        $expected_output = file_get_contents($expected_output_file);

        // Выполняем задачу в зависимости от её имени
        switch ($task_name) {
            case 'A':
                $output = process_advertisements($input);
                break;
            case 'B':
                $output = process_catalog($input);
                break;
            case 'C':
                $output = process_banners($input);
                break;
            case 'D':
                $output = minify_css($input);
                break;
            default:
                $output = "Неизвестная задача";
        }

        // Сравниваем результаты и формируем запись результата
        $results[] = [
            'input_file' => $input_file,
            'expected_output_file' => $expected_output_file,
            'expected_output' => $expected_output,
            'function_output' => $output
        ];
    }

    return $results;
}

// Задачи для выполнения
$tasks = ['A', 'B', 'C', 'D'];

// Выводим результаты выполнения каждой задачи
echo "<h1>Результаты выполнения задач</h1>";

foreach ($tasks as $task_name) {
    echo "<h2>Задача $task_name</h2>";

    // Получаем результаты проверки для текущей задачи
    $results = check_output($task_name);

    // Выводим результаты
    foreach ($results as $result) {
        echo "<h3>Входные данные: {$result['input_file']}</h3>";
        echo "<h4>Ожидаемый результат: {$result['expected_output_file']}</h4>";
        echo "<h4>Ожидаемый результат:</h4><pre>{$result['expected_output']}</pre>";
        echo "<p>Результат функции:</p><pre>{$result['function_output']}</pre>";
    }
}
?>
</body>
</html>
