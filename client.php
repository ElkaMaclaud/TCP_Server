<?php
// Запрос номера порта
$port = (int)readline("Введите номер TCP-порта: ");

// Создание сокета
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    die("Не удалось создать сокет: " . socket_strerror(socket_last_error()) . "\n");
}

// Подключение к серверу
$serverAddress = '127.0.0.1'; // Локальный адрес сервера
if (socket_connect($socket, $serverAddress, $port) === false) {
    die("Не удалось подключиться к серверу: " . socket_strerror(socket_last_error($socket)) . "\n");
}

// Ввод строки для проверки скобок
$input = readline("Введите строку для проверки скобок: ");

// Отправка данных на сервер
socket_write($socket, $input, strlen($input));

// Чтение ответа от сервера
$response = socket_read($socket, 1024);
echo "Ответ от сервера: $response";

// Закрытие сокета
socket_close($socket);
?>