<?php

require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

function isValidBrackets($input) {
    $stack = [];
    $allowedChars = ['(', ')', ' ', "\n", "\t", "\r"];

    for ($i = 0; $i < strlen($input); $i++) {
        $char = $input[$i];

        if (!in_array($char, $allowedChars)) {
            throw new InvalidArgumentException("Недопустимый символ '{$char}' в строке");
        }

        if ($char === '(') {
            array_push($stack, $char);
        } elseif ($char === ')') {
            if (empty($stack) || array_pop($stack) !== '(') { 
                return false;
            }
        }
    }

    return empty($stack);
}

function logToFile($message) {
    $logFile = 'server.log'; // Имя файла для логов
    $timestamp = date('[Y-m-d H:i:s]'); // Форматирование временной метки
    file_put_contents($logFile, "$timestamp $message\n", FILE_APPEND); // Запись сообщения в файл
}

// Проверяем, если это HTTP-запрос

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputString = $_POST['string'] ?? '';
        logToFile("Получен запрос с данными: $inputString");
        try {
            $isValid = isValidBrackets(trim($inputString));
            if ($isValid) {
                http_response_code(200);
                echo "Строка корректна: true";
            } else {
                http_response_code(400);
                echo "Строка некорректна: false";
            }
        } catch (InvalidArgumentException $e) {
            http_response_code(400);
            echo "Ошибка: " . $e->getMessage();
        }
    } else {
        http_response_code(405);
        echo "Метод не разрешен.";
    }

// Запуск сервера в консоле (также в Windows): php -S localhost:8080 server.php    -S - Этот параметр указывает, что запускать встроенный веб-сервер!!!!! 
// Использование в Windows:     curl -X POST -d "string=(()())" http://localhost:$port