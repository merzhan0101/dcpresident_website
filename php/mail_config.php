<?php
// Настройки почтового ящика для отправки писем с сайта

define('SMTP_HOST', 'presidentdc.kz'); // сервер — проверьте в Plesk (Почта -> info@presidentdc.kz -> "Показать настройки")
define('SMTP_PORT', 465);                   // 465 для SSL, 587 для STARTTLS
define('SMTP_USER', 'info@presidentdc.kz'); // полный адрес ящика
define('SMTP_PASS', 'h2tAhVSjFMvKCQt');
define('SMTP_FROM_NAME', 'DC President');