<?php
// Подключаем базу данных
include 'php/database.php';

// Устанавливаем кодировку
header('Content-Type: text/html; charset=utf-8');

// Функция для отправки email уведомления
function sendEmailNotification($name, $email, $message) {
    $to = "president.dc@toraighyrov.edu.kz";
    $subject = "Новая заявка в дебатный клуб DC President";
    
    $email_body = "
    <html>
    <head>
        <title>Новая заявка в дебатный клуб</title>
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
                <h1>DC President - Новая заявка</h1>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>Имя:</span> $name
                </div>
                <div class='field'>
                    <span class='label'>Email:</span> $email
                </div>
                <div class='field'>
                    <span class='label'>Сообщение:</span><br>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
                <div class='field'>
                    <span class='label'>Время отправки:</span> " . date('d.m.Y H:i:s') . "
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    $headers .= "From: DC President <noreply@toraighyrov.edu.kz>\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    return mail($to, $subject, $email_body, $headers);
}

// Основная логика обработки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Получаем и валидируем данные
        $name = trim(htmlspecialchars($_POST['name'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $message = trim(htmlspecialchars($_POST['message'] ?? ''));
        
        // Проверяем обязательные поля
        $errors = [];
        
        if (empty($name)) {
            $errors[] = "Пожалуйста, введите ваше имя";
        }
        
        if (!$email) {
            $errors[] = "Пожалуйста, введите корректный email адрес";
        }
        
        if (empty($message)) {
            $errors[] = "Пожалуйста, напишите, почему хотите вступить в клуб";
        }
        
        // Проверка на спам (honeypot)
        if (!empty($_POST['website'])) {
            // Это бот, просто завершаем выполнение
            header("Location: index.php?success=1");
            exit;
        }
        
        // Если есть ошибки, показываем их
        if (!empty($errors)) {
            session_start();
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = compact('name', 'email', 'message');
            header("Location: index.php#apply");
            exit;
        }
        
        // Получаем дополнительную информацию
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        // Сохраняем в базу данных
        $sql = "INSERT INTO applications (name, email, message, ip_address, user_agent) 
                VALUES (:name, :email, :message, :ip_address, :user_agent)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message,
            ':ip_address' => $ip_address,
            ':user_agent' => $user_agent
        ]);
        
        // Сохраняем в файл для backup
        $log_entry = date('Y-m-d H:i:s') . " | $name | $email | $ip_address\n";
        file_put_contents('logs/applications.log', $log_entry, FILE_APPEND);
        
        // Отправляем email уведомление
        sendEmailNotification($name, $email, $message);
        
        // Отправляем ответное письмо пользователю
        sendConfirmationEmail($name, $email);
        
        // Перенаправляем на страницу успеха
        header("Location: application-success.php");
        exit;
        
    } catch (Exception $e) {
        // Логируем ошибку
        error_log("Application form error: " . $e->getMessage());
        
        // Перенаправляем с ошибкой
        session_start();
        $_SESSION['form_errors'] = ["Произошла ошибка при отправке формы. Пожалуйста, попробуйте позже."];
        header("Location: index.php#apply");
        exit;
    }
} else {
    // Если не POST запрос, перенаправляем на главную
    header("Location: index.php");
    exit;
}

// Функция для отправки подтверждения пользователю
function sendConfirmationEmail($name, $email) {
    $subject = "Ваша заявка в DC President принята!";
    
    $email_body = "
    <html>
    <head>
        <title>Подтверждение заявки</title>
        <style>
            body { font-family: Montserrat, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #b50000; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>DC President</h1>
            </div>
            <div class='content'>
                <h2>Уважаемый(ая) $name,</h2>
                <p>Благодарим вас за проявленный интерес к нашему дебатному клубу!</p>
                <p>Мы получили вашу заявку и рассмотрим её в течение 24 часов.</p>
                <p>С уважением,<br>Команда DC President</p>
                <hr>
                <p><small>Это автоматическое письмо, пожалуйста, не отвечайте на него.</small></p>
            </div>
        </div>
    </body>
    </html>";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    $headers .= "From: DC President <president.dc@toraighyrov.edu.kz>\r\n";
    
    mail($email, $subject, $email_body, $headers);
}
?>