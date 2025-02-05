<?php
$redis = new Redis();
$redis->connect('127.127.126.42', 6379);

// Функция редукции (та же, что и при генерации)
function reduceHash($hash, $iteration) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $reduced = '';
    for ($i = 0; $i < 8; $i++) {
        $index = (hexdec($hash[$i]) + $iteration) % strlen($chars);
        $reduced .= $chars[$index];
    }
    return $reduced;
}

// Функция поиска
function searchHash($hash, $chainLength = 1000) {
    global $redis;

    // Отладочный вывод
    echo "Searching for hash: $hash\n";

    for ($i = $chainLength - 1; $i >= 0; $i--) {
        $currentHash = $hash;

        // Применяем редукцию и хэширование
        for ($j = $i; $j < $chainLength; $j++) {
            $reduced = reduceHash($currentHash, $j);
            $currentHash = hash('sha256', $reduced);
        }

        // Проверяем, есть ли конец цепочки в Redis
        $startString = $redis->get($reduced);
        if ($startString) {
            // Восстанавливаем цепочку
            $currentString = $startString;
            for ($k = 0; $k < $chainLength; $k++) {
                if (hash('sha256', $currentString) === $hash) {
                    return $currentString;
                }
                $currentString = reduceHash(hash('sha256', $currentString), $k);
            }
        }
    }

    return null;
}

// Поиск
if (isset($_GET['hash'])) {
    $hash = $_GET['hash'];
    $result = searchHash($hash);

    if ($result) {
        echo "Found string: $result";
    } else {
        echo "No match found for hash: $hash";
    }
} else {
    echo "Please provide a hash as a GET parameter, e.g., ?hash=your_hash_here";
}