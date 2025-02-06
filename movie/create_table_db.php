<?php
require 'connection.php';

$filePath = 'movielens.sql';
try {
    if (is_file($filePath)) {
        $sql = file_get_contents($filePath);

        try {
            $pdo->exec($sql);
            echo "SQL из файла '" . basename($filePath) . "' успешно выполнен!<br>";
        } catch (PDOException $e) {
            echo "Ошибка при выполнении SQL из файла '" . basename($filePath) . "': " . $e->getMessage() . "<br>";
        }
    }
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage();
}


