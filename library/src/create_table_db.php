<?php
require 'connected_db.php';

try {
    $files = [
        'sql/authors.sql',
        'sql/students.sql',
        'sql/categories.sql',
        'sql/books.sql',
        'sql/book_authors.sql',
        'sql/book_categories.sql',
        'sql/borrowed_books.sql',
    ];

    foreach ($files as $filePath) {
        if (is_file($filePath)) {
            $sql = file_get_contents($filePath);

            try {
                $pdo->exec($sql);
                echo "SQL из файла '" . basename($filePath) . "' успешно выполнен!<br>";
            } catch (PDOException $e) {
                echo "Ошибка при выполнении SQL из файла '" . basename($filePath) . "': " . $e->getMessage() . "<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "Общая ошибка: " . $e->getMessage();
}
?>
































<?php
// require 'connected_db.php';

// try {
//     $directory = 'sql';
//     $files = scandir($directory);

//     foreach ($files as $file) {
//         if ($file === '.' || $file === '..') {
//             continue;
//         }

//         $filePath = $directory . DIRECTORY_SEPARATOR . $file; 

//         if (is_file($filePath)) {

//             $sql = file_get_contents($filePath);

//             try {
//                 $pdo->exec($sql);
//                 echo "SQL из файла '$file' успешно выполнен!<br>";
//             } catch (PDOException $e) {
//                 echo "Ошибка при выполнении SQL из файла '$file': " . $e->getMessage() . "<br>";
//             }
//         }
//     }
// } catch (Exception $e) {
//     echo "Общая ошибка: " . $e->getMessage();
// }
?>




<?php
// require 'connected_db.php'; 

// try {
//     $directory = 'sql'; 
//     $files = scandir($directory);

//     foreach ($files as $file) {
//         if ($file === '.' || $file === '..') {
//             continue;
//         }

//         $filePath = $directory . DIRECTORY_SEPARATOR . $file;
//         if (is_file($filePath)) {
//             $tableStructure = file_get_contents($filePath);
//             $tableName = pathinfo($file, PATHINFO_FILENAME);

//             $sql = "CREATE TABLE IF NOT EXISTS $tableName (\n" . $tableStructure . "\n);";
//             $conn->exec($sql);
//             echo "Таблица '$tableName' успешно создана!<br>";
//         }
//     }
// } catch (PDOException $e) {
//     echo "Ошибка при создании таблиц: " . $e->getMessage();
// }
?>



