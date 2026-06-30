<?php
include 'php/database.php';
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || !$email || empty($phone) || empty($message)) {
        echo "Заполните все обязательные поля";
        exit;
    }

   $sql = "INSERT INTO applications
        (name, email, phone, message)
        VALUES (:name, :email, :phone, :message)";
    $stmt = $pdo->prepare($sql);
    $subjects = [
        'join' => 'Клубқа қосылу',
        'partnership' => 'Серіктестік',
        'question' => 'Сұрақ',
        'other' => 'Басқа'
    ];

    $subjectText = $subjects[$subject] ?? 'Басқа';

    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':phone'   => $phone,
        ':message' => "Тақырып: $subjectText\n\n$message"
    ]);

    header("Location: application-success.php");
    exit;
}

header("Location: contact.php");
exit;