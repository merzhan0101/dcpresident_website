<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка отправлена - DC President</title>
    <link rel="stylesheet" href="css/style_success.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'blocks/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Заявка отправлена!</h1>
                <p>Спасибо за ваш интерес к нашему клубу</p>
            </div>
        </section>

        <section class="success-message">
            <div class="container">
                <div class="success-card">
                    <div class="success-icon">✅</div>
                    <h2>Ваша заявка успешно отправлена!</h2>
                    <p>Мы рассмотрим вашу заявку в течение 24 часов и свяжемся с вами по указанному email.</p>
                    <p>Пока вы ждете, можете ознакомиться с нашими <a href="events.php">мероприятиями</a> или узнать больше <a href="about.php">о клубе</a>.</p>
                    <div class="success-actions">
                        <a href="index.php" class="btn">На главную</a>
                        <a href="events.php" class="btn btn-outline">Мероприятия</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'blocks/footer.php'; ?>
</body>
</html>