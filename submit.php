<?php

// Подключаем базу данных
include 'php/database.php';

// Устанавливаем кодировку
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    
    // Проверяем обязательные поля
    if (empty($name) || empty($email) || empty($message)) {
        echo "Пожалуйста, заполните все поля";
        exit;
    }
    
    // Сохраняем в файл
    $data = date('Y-m-d H:i:s') . " | $name | $email | $message\n";
    file_put_contents('applications.txt', $data, FILE_APPEND);
    
    // Перенаправляем на успех
    header("Location: application-success.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}

?>