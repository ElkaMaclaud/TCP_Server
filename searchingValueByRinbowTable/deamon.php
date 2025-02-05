<?php
$redis = new Redis();
$redis->connect('127.127.126.42', 6379);

// Функция редукции (преобразование хэша обратно в строку)
function reduceHash($hash, $iteration) {
    // Пример редукции: берём первые 8 символов хэша и интерпретируем их как строку
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $reduced = '';
    for ($i = 0; $i < 8; $i++) {
        $index = (hexdec($hash[$i]) + $iteration) % strlen($chars);
        $reduced .= $chars[$index];
    }
    return $reduced;
}

// Добавляем тестовую запись "hello" в Redis
$testString = "hello";
$testHash = hash('sha256', $testString);
$redis->setex($testHash, 3600, $testString); // Сохраняем пару хэш -> строка с TTL 1 час
echo "Test entry added: $testHash -> $testString\n";

// Генерация цепочек
function generateRainbowTable($chainLength = 1000, $numChains = 100) {
    global $redis;

    for ($i = 0; $i < $numChains; $i++) {
        $startString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 8);
        $currentString = $startString;

        for ($j = 0; $j < $chainLength; $j++) {
            $hash = hash('sha256', $currentString);
            $currentString = reduceHash($hash, $j);
        }

        // Сохраняем начало и конец цепочки в Redis
        $redis->setex($startString, 3600, $currentString);
        echo "Chain generated: $startString -> $currentString\n";
    }
}

// Генерация таблицы
generateRainbowTable();