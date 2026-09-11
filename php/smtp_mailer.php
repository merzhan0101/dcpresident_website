<?php
require_once __DIR__ . '/mail_config.php';

/**
 * Отправляет письмо через SMTP напрямую (без сторонних библиотек)
 *
 * @param string $to      Кому
 * @param string $subject Тема письма
 * @param string $htmlBody HTML-тело письма
 * @param string|null $replyTo Адрес для ответа (Reply-To)
 * @return bool true при успехе, false при ошибке (детали — в error_log)
 */
function sendSmtpMail($to, $subject, $htmlBody, $replyTo = null) {
    $host = (SMTP_PORT == 465 ? 'ssl://' : '') . SMTP_HOST;

    $smtp = @stream_socket_client(
        "$host:" . SMTP_PORT,
        $errno,
        $errstr,
        15,
        STREAM_CLIENT_CONNECT
    );

    if (!$smtp) {
        error_log("SMTP: не удалось подключиться к " . SMTP_HOST . ":" . SMTP_PORT . " — $errstr ($errno)");
        return false;
    }

    stream_set_timeout($smtp, 15);

    // Читает один ответ сервера (может занимать несколько строк)
    $readResponse = function () use ($smtp) {
        $data = '';
        while ($line = fgets($smtp, 515)) {
            $data .= $line;
            // Последняя строка ответа начинается с "код "+пробел (не дефис)
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };

    $send = function ($command) use ($smtp) {
        fwrite($smtp, $command . "\r\n");
    };

    $expectCode = function ($response, $expected) {
        $code = substr(trim($response), 0, 3);
        return in_array($code, (array)$expected, true);
    };

    $response = $readResponse(); // приветствие сервера
    if (!$expectCode($response, '220')) {
        error_log("SMTP: сервер не поприветствовал: $response");
        fclose($smtp);
        return false;
    }

    $domain = substr(strrchr(SMTP_USER, "@"), 1) ?: 'localhost';

    $send("EHLO $domain");
    $response = $readResponse();
    if (!$expectCode($response, '250')) {
        error_log("SMTP: EHLO отклонён: $response");
        fclose($smtp);
        return false;
    }

    // Если порт 587 — нужен STARTTLS до авторизации
    if (SMTP_PORT == 587) {
        $send("STARTTLS");
        $response = $readResponse();
        if (!$expectCode($response, '220')) {
            error_log("SMTP: STARTTLS отклонён: $response");
            fclose($smtp);
            return false;
        }
        if (!stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log("SMTP: не удалось включить TLS");
            fclose($smtp);
            return false;
        }
        $send("EHLO $domain");
        $readResponse();
    }

    $send("AUTH LOGIN");
    $response = $readResponse();
    if (!$expectCode($response, '334')) {
        error_log("SMTP: AUTH LOGIN отклонён: $response");
        fclose($smtp);
        return false;
    }

    $send(base64_encode(SMTP_USER));
    $response = $readResponse();
    if (!$expectCode($response, '334')) {
        error_log("SMTP: логин отклонён: $response");
        fclose($smtp);
        return false;
    }

    $send(base64_encode(SMTP_PASS));
    $response = $readResponse();
    if (!$expectCode($response, '235')) {
        error_log("SMTP: неверный логин/пароль: $response");
        fclose($smtp);
        return false;
    }

    $send("MAIL FROM:<" . SMTP_USER . ">");
    $response = $readResponse();
    if (!$expectCode($response, '250')) {
        error_log("SMTP: MAIL FROM отклонён: $response");
        fclose($smtp);
        return false;
    }

    $send("RCPT TO:<$to>");
    $response = $readResponse();
    if (!$expectCode($response, ['250', '251'])) {
        error_log("SMTP: RCPT TO отклонён: $response");
        fclose($smtp);
        return false;
    }

    $send("DATA");
    $response = $readResponse();
    if (!$expectCode($response, '354')) {
        error_log("SMTP: DATA отклонён: $response");
        fclose($smtp);
        return false;
    }

    $fromName = '=?UTF-8?B?' . base64_encode(SMTP_FROM_NAME) . '?=';
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $headers = [];
    $headers[] = "From: $fromName <" . SMTP_USER . ">";
    $headers[] = "To: <$to>";
    $headers[] = "Subject: $encodedSubject";
    if ($replyTo) {
        $headers[] = "Reply-To: <$replyTo>";
    }
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "Content-Transfer-Encoding: 8bit";

    // Экранируем точки в начале строк (SMTP-стандарт)
    $body = preg_replace('/^\./m', '..', $htmlBody);

    $send(implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.");
    $response = $readResponse();
    if (!$expectCode($response, '250')) {
        error_log("SMTP: письмо отклонено при отправке: $response");
        fclose($smtp);
        return false;
    }

    $send("QUIT");
    fclose($smtp);

    return true;
}