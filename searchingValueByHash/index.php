<?php

$redis = new Redis();
$redis->connect('127.127.126.42', 6379);

if (isset($_GET['hash'])) {
    $hash = $_GET['hash'];

    $result = $redis->get($hash);

    if ($result) {
        echo json_encode([
            'hash' => $hash,
            'string' => $result
        ]);
    } else {
        echo json_encode([
            'error' => 'Hash not found'
        ]);
    }
} else {
    echo json_encode([
        'error' => 'Hash parameter is required'
    ]);
}