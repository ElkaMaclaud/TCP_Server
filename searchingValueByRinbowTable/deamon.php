<?php
$redis = new Redis();
$redis->connect('127.127.126.42', 6379);

// Функция редукции
function reduceHash($hash, $iteration) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $reduced = '';
    for ($i = 0; $i < 8; $i++) {
        $index = (hexdec($hash[$i]) + $iteration) % strlen($chars);
        $reduced .= $chars[$index];
    }
    return $reduced;
}

// Генерация радужной таблицы
function generateRainbowTable($chainLength = 1000, $numChains = 100) {
    global $redis;

    // Добавляем слово "hello" в таблицу
    $testString = "hello";
    $currentString = $testString;

    for ($j = 0; $j < $chainLength; $j++) {
        $hash = hash('sha256', $currentString);
        $currentString = reduceHash($hash, $j);
    }

    // Сохраняем начало и конец цепочки для "hello"
    $redis->setex($currentString, 3600, $testString);
    echo "Test chain added: $testString -> $currentString\n";

    // Генерируем остальные цепочки
    for ($i = 0; $i < $numChains; $i++) {
        $startString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $currentString = $startString;

        for ($j = 0; $j < $chainLength; $j++) {
            $hash = hash('sha256', $currentString);
            $currentString = reduceHash($hash, $j);
        }

        // Сохраняем начало и конец цепочки в Redis
        $redis->setex($currentString, 3600, $startString);
        echo "Chain generated: $startString -> $currentString\n";
    }
}

// Генерация таблицы
generateRainbowTable();