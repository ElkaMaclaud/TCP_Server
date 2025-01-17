<?php
// if (extension_loaded('sockets')) {
//     echo "Расширение sockets включено.\n";
// } else {
//     echo "Расширение sockets не включено.\n";
// } 

if (!extension_loaded('sockets')) {
    die("Расширение sockets не загружено.\n");
}

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

// Запрос номера порта
$port = (int)readline("Введите номер TCP-порта: ");

// Создание сокета
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("Не удалось создать сокет: " . socket_strerror(socket_last_error()) . "\n");
}

// Привязка сокета к адресу и порту
if (socket_bind($socket, '0.0.0.0', $port) === false) {
    die("Не удалось привязать сокет: " . socket_strerror(socket_last_error($socket)) . "\n");
}

// Начало прослушивания
if (socket_listen($socket, 5) === false) {
    die("Не удалось начать прослушивание: " . socket_strerror(socket_last_error($socket)) . "\n");
}

echo "Сервер запущен на порту $port. Ожидание соединений...\n";

while (true) {
    // Принятие входящего соединения
    $clientSocket = socket_accept($socket);
    if ($clientSocket === false) {
        echo "Ошибка при принятии соединения: " . socket_strerror(socket_last_error($socket)) . "\n";
        continue;
    }

    // Обработка соединения в отдельном потоке
    $pid = pcntl_fork(); // В Windows не работает этот подход - это для Unix-подоб. системы
    if ($pid == -1) {
        die("Не удалось создать процесс.\n");
    } elseif ($pid) {
        // Родительский процесс
        socket_close($clientSocket);
    } else {
        // Дочерний процесс
        socket_close($socket); // Закрываем сокет в дочернем процессе

        while (true) {
            $input = socket_read($clientSocket, 1024);
            if ($input === false || trim($input) === '') {
                break; // Завершение, если соединение закрыто или пустой ввод
            }

            // Валидация скобок
            try {
                $isValid = isValidBrackets(trim($input));

                $response = $isValid ? "Строка корректна: true\n" : "Строка некорректна: false\n";
            } catch (InvalidArgumentException $e) {
                $response = "Ошибка: " . $e->getMessage() . "\n";
            }

            socket_write($clientSocket, $response, strlen($response));
        }

        socket_close($clientSocket);
        exit(0); // Завершение дочернего процесса
    }
}

// Закрытие основного сокета (не достигнет этого кода, так как бесконечный цикл)
socket_close($socket);