<?php
// Подключаем базу данных
include 'php/database.php';

// Устанавливаем кодировку
header('Content-Type: text/html; charset=utf-8');

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
        
        // Сохраняем в файл для backup (создайте папку logs если её нет)
        $log_entry = date('Y-m-d H:i:s') . " | $name | $email | $message | $ip_address\n";
        file_put_contents('applications.log', $log_entry, FILE_APPEND);
        
        // Для локальной разработки - просто пишем в лог вместо отправки email
        file_put_contents('email_log.log', 
            "НОВАЯ ЗАЯВКА:\n" .
            "Время: " . date('d.m.Y H:i:s') . "\n" .
            "Имя: $name\n" .
            "Email: $email\n" . 
            "Сообщение: $message\n" .
            "IP: $ip_address\n" .
            "------------------------\n", 
            FILE_APPEND
        );
        
        // Перенаправляем на страницу успеха
        header("Location: application-success.php");
        exit;
        
    } catch (Exception $e) {
        // Логируем ошибку
        error_log("Application form error: " . $e->getMessage());
        file_put_contents('error.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
        
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

























//  2. Версия с реальной отправкой email (для хостинга) ============================================================================================================
//Когда перенесете на хостинг, используйте эту версию:
// ... (остальной код такой же до места отправки email)

// ЗАМЕНИТЕ ЭТУ ЧАСТЬ ДЛЯ РЕАЛЬНОЙ ОТПРАВКИ:

// Ваш реальный email (укажите свой)
// $admin_email = "ваш_email@gmail.com"; // ЗАМЕНИТЕ НА СВОЙ EMAIL

// // Отправляем email уведомление администратору
// $admin_subject = "Новая заявка в DC President от $name";
// $admin_message = "
// Имя: $name
// Email: $email
// Сообщение: $message
// Время: " . date('d.m.Y H:i:s') . "
// IP: $ip_address
// ";

// mail($admin_email, $admin_subject, $admin_message);

// // Отправляем подтверждение пользователю
// $user_subject = "Ваша заявка в DC President принята!";
// $user_message = "
// Уважаемый(ая) $name,

// Благодарим вас за проявленный интерес к нашему дебатному клубу DC President!

// Мы получили вашу заявку и рассмотрим её в течение 24 часов.

// Наши контакты:
// Email: president.dc@toraighyrov.edu.kz
// Instagram: @tou_debate_club
// Telegram: @PresidentCup6

// С уважением,
// Команда DC President
// Торайгыров Университет
// ";

// mail($email, $user_subject, $user_message);

// ... (остальной код)
?>