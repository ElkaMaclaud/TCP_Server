<?php

require_once __DIR__ . '/vendor/autoload.php'; // Используем __DIR__ для получения текущей директории

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

if (!extension_loaded('sockets')) {
    die("Расширение sockets не загружено.\n");
}

$socket = null;
$config = [];
$currentPort = null;

function loadConfig($configFilePath) {
    if (!file_exists($configFilePath)) {
        die("Конфигурационный файл не найден: $configFilePath\n");
    }

    try {
        $config = Yaml::parseFile($configFilePath);
    } catch (Exception $e) {
        die("Ошибка при чтении конфигурационного файла: " . $e->getMessage() . "\n");
    }

    if (!isset($config['server']['port']) || !is_numeric($config['server']['port'])) { // исправлено условие
        die("Некорректный порт в конфигурационном файле.\n");
    }

    return $config;
}

function restartServer($configFilePath) {
    global $socket, $config, $currentPort;

    echo "Перезагрузка конфигурации...\n";

    $newConfig = loadConfig($configFilePath);
    $newPort = $newConfig['server']['port'];

    putenv("APP_PORT=$newPort");

    if ($newPort !== $currentPort) {
        echo "Порт изменился: $currentPort -> $newPort. Перезапуск сервера...\n";

        // Закрываем старый сокет
        if ($socket) {
            socket_close($socket);
        }

        // Создаем новый сокет
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            die("Не удалось создать сокет: " . socket_strerror(socket_last_error()) . "\n");
        }

        if (socket_bind($socket, '0.0.0.0', $newPort) === false) {
            die("Не удалось привязать сокет: " . socket_strerror(socket_last_error($socket)) . "\n");
        }

        if (socket_listen($socket, 5) === false) {
            die("Не удалось начать прослушивание: " . socket_strerror(socket_last_error($socket)) . "\n");
        }
    }
    echo "Сервер запущен на новом порту $newPort.\n";
    $currentPort = $newPort;

    $config = $newConfig;
}

$configFilePath = $argv[1] ?? null;
if (!$configFilePath) {
    die("Укажите путь до конфигурационного файла YAML.\n");
}

restartServer($configFilePath);

// Код для Unix-подобных систем
// pcntl_signal(SIGHUP, function () use ($configFilePath) {
//     restartServer($configFilePath);
// });

echo "Сервер запущен. Ожидание соединений...\n";

// Открываем стандартный ввод один раз 
// Открываем стандартный ввод один раз
// $stdin = fopen('php://stdin', 'r');
// if (!$stdin) {
//     die("Не удалось открыть стандартный ввод.\n");
// }

// $read = [$socket, $stdin];

$lastModifiedTime = filemtime($configFilePath); // Получаем время последнего изменения

while (true) {
    // pcntl_signal_dispatch(); // В Windows не работает этот подход - это для Unix-подобных систем
    clearstatcache(); // Очищаем кэш статуса файловой системы
    $currentModifiedTime = filemtime($configFilePath); // Получаем текущее время изменения файла

    // if ($newPort !== $currentPort) {
    if ($currentModifiedTime !== $lastModifiedTime) {
        echo "Конфигурация изменена. Перезагрузка сервера...\n";
        $lastModifiedTime = $currentModifiedTime;
        restartServer($configFilePath);
    }

    // Фильтруем массив, чтобы оставить только сокеты
    //  $read = array_filter($read, function($item) {
    //     return is_resource($item) && get_resource_type($item) === 'Socket';
    // });
    // Используем select для ожидания событий  - ВЫЗЫВАЕТ ОШИБКУ  Uncaught TypeError: socket_select(): Argument #1 ($read) must only have elements of type Socket, resource given in
    // if (socket_select($read, $write, $except, null) === false) {
    //     die("Ошибка при ожидании события: " . socket_strerror(socket_last_error()) . "\n");
    // };

    // Обработка новых соединений
    $clientSocket = socket_accept($socket);
    if ($clientSocket !== false) {
        $inputSocket = socket_read($clientSocket, 1024);
        if ($inputSocket !== false && trim($inputSocket) !== '') {
            try {
                $isValid = isValidBrackets(trim($inputSocket));
                $response = $isValid ? "Строка корректна: true\n" : "Строка некорректна: false\n";
            } catch (InvalidArgumentException $e) {
                 $response = "Ошибка: " . $e->getMessage() . "\n";
            }
            socket_write($clientSocket, $response, strlen($response));
        }
        socket_close($clientSocket);
    }

    // // Обработка ввода из стандартного ввода - Так не работает!!!! Пересстает слушать клиента!
    // if (in_array($stdin, $read)) {
    //     $input = fgets($stdin);
    //     if ($input !== false) {
    //         $command = trim($input);
    //         if ($command === 'reload') {
    //             echo "Перезагрузка конфигурации...\n";
    //            restartServer($configFilePath);
    //         } elseif ($command === 'exit') {
    //             echo "Остановка сервера...\n";
    //             break; // Выход из цикла и завершение работы сервера
    //         } else {
    //             echo "Неизвестная команда: " . $command . "\n";
    //         }
    //     }
    // }
}

// Закрываем сокет и стандартный ввод перед выходом
if ($socket) {
    socket_close($socket);
}
fclose($stdin);

echo "Сервер остановлен.\n";
