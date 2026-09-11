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
        (name, email, phone, message, subject)
        VALUES (:name, :email, :phone, :message, :subject)";
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
        ':message' => $message,
        ':subject' => $subjectText
    ]);

    // Уведомление на почту клуба
    $notifySubject = "Жаңа хабарлама: $subjectText";
    $notifyBody = "
    <html>
    <head>
        <style>
            body { font-family: Montserrat, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #b50000; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #333; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>DC President — Байланыс формасы</h1>
            </div>
            <div class='content'>
                <div class='field'><span class='label'>Есімі:</span> " . htmlspecialchars($name) . "</div>
                <div class='field'><span class='label'>Email:</span> " . htmlspecialchars($email) . "</div>
                <div class='field'><span class='label'>Телефон:</span> " . htmlspecialchars($phone) . "</div>
                <div class='field'><span class='label'>Тақырып:</span> " . htmlspecialchars($subjectText) . "</div>
                <div class='field'><span class='label'>Хабарлама:</span><br>" . nl2br(htmlspecialchars($message)) . "</div>
            </div>
        </div>
    </body>
    </html>";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    $headers .= "From: DC President <info@presidentdc.kz>\r\n";
    $headers .= "Reply-To: $email\r\n";

    mail("info@presidentdc.kz", $notifySubject, $notifyBody, $headers);

    header("Location: application-success.php");
    exit;
}

header("Location: contact.php");
exit;