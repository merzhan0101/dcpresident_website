<?php
if ($_POST) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    
    // Здесь можно:
    // 1. Сохранить в файл
    // 2. Отправить на email
    // 3. Сохранить в базу данных
    
    file_put_contents('applications.txt', "$name | $email\n", FILE_APPEND);
    
    echo "Заявка отправлена! Мы свяжемся с вами в течение 24 часов.";
}
?>