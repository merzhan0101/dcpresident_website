<?php
// Ключи reCAPTCHA — впишите свои, полученные на google.com/recaptcha/admin

define('RECAPTCHA_SITE_KEY', '6Lf3KMAtAAAAAK1H40UgZSAoIFnIH4wUR5so0110');
define('RECAPTCHA_SECRET_KEY', '6Lf3KMAtAAAAAGco0hyWz6d50g50JdrFihZr8Blx');

// Проверяет ответ пользователя (чекбокс "Я не робот") через API Google
function verifyRecaptcha($response) {
    if (empty($response)) {
        return false;
    }

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret'   => RECAPTCHA_SECRET_KEY,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result = curl_exec($ch);
    curl_close($ch);

    if ($result === false) {
        error_log('reCAPTCHA: не удалось связаться с Google API');
        return false;
    }

    $json = json_decode($result, true);
    return !empty($json['success']);
}