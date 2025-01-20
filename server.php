<?php

require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

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

    if (!isset($config['server']['port']) || !is_numeric($config['server']['port'])) {
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

        echo "Сервер запущен на новом порту $newPort.\n";
        $currentPort = $newPort;
    }

    $config = $newConfig;
}

$configFilePath = $argv[1] ?? null;
if (!$configFilePath) {
    die("Укажите путь до конфигурационного файла YAML.\n");
}

restartServer($configFilePath);

pcntl_signal(SIGHUP, function () use ($configFilePath) {
    restartServer($configFilePath);
});

echo "Сервер запущен. Ожидание соединений...\n";

while (true) {
    pcntl_signal_dispatch();

    $clientSocket = @socket_accept($socket);
    if ($clientSocket === false) {
        usleep(100000);
        continue;
    }

    $input = socket_read($clientSocket, 1024);
    if ($input === false || trim($input) === '') {
        socket_close($clientSocket);
        continue;
    }

    $response = "Вы отправили: " . trim($input) . "\n";
    socket_write($clientSocket, $response, strlen($response));

    socket_close($clientSocket);
}