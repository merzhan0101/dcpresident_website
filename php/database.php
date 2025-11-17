<?php
$host = 'localhost';
$dbname = 'bd_dcpresident';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}


// $host = 'db.fr-pari1.bengt.wasmernet.com';
// $port = 10272; // важно! порт нужно указывать явно
// $dbname = 'bd_dcpresident';
// $username = 'b2f8fa727fbb8000942559435773';
// $password = '0690b2f8-fa73-71b9-8000-7c964f652718';

// try {
//     $pdo = new PDO(
//         "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
//         $username,
//         $password,
//         [
//             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//             PDO::ATTR_TIMEOUT => 10, // 10 секунд тайм-аут
//         ]
//     );
//     // echo "✅ Подключение успешно!";
// } catch(PDOException $e) {
//     die("Ошибка подключения: " . $e->getMessage());
// }

?>