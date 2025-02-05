<?php

$redis = new Redis();
$redis->connect('127.127.126.42', 6379);

function generateRandomString($length = 8) {
    return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
}

function generateHash($string) {
    return hash('sha256', $string);
}

while (true) {
    $randomString = generateRandomString();
    $hash = generateHash($randomString);

    // $redis->set($hash, $randomString);

    $redis->setex($hash, 3600, $randomString); // Хэш будет храниться 1 час
    echo "Stored: $hash -> $randomString\n";

    sleep(1);
}


// Запускаем этого демона 
// Запускаем веб-сервер:   php -S localhost:8080 index.php
// Делаем запрос - curl "http://localhost:8080/index.php?hash=<hash_value>" c126f83bca06cf3c8e26f97b3bee65a4e46280be63cc11784e1c73d82256e31d
// Пример ответа: 
        // StatusCode        : 200
        // StatusDescription : OK
        // Content           : {"hash":"c126f83bca06cf3c8e26f97b3bee65a4e46280be63cc11784e1c73d82256e31d","string":"vKdrQRGg"}
        // RawContent        : HTTP/1.1 200 OK
        //                     Host: localhost:8080
        //                     Connection: close
        //                     Content-Type: text/html; charset=UTF-8
        //                     Date: Tue, 04 Feb 2025 19:02:28 GMT
        //                     X-Powered-By: PHP/8.4.2

        //                     {"hash":"c126f83bca06cf3c8e26f97b3bee6...
        // Forms             : {}
        // Headers           : {[Host, localhost:8080], [Connection, close], [Content-Type, text/html; charset=UTF-8], [Date, Tue, 04 Feb 2025 19:02:28 GMT]...}
        // Images            : {}
        // InputFields       : {}
        // Links             : {}
        // ParsedHtml        : System.__ComObject
        // RawContentLength  : 95