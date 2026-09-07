<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка отправлена - DC President</title>
    <link rel="stylesheet" href="css/style_success.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
</head>
<body>
    <?php include 'blocks/header.php'; ?>
    
    <main>
        <section class="page-header">
            <div class="container">
                <h1>Өтінім жіберілді!</h1>
                <p>Клубымызға қызығушылық танытқаныңыз үшін рақмет!</p>
            </div>
        </section>

        <section class="success-message">
            <div class="container">
                <div class="success-card">
                    <div class="success-icon">✅</div>

                    <h2>Өтініміңіз сәтті жіберілді!</h2>

                    <p>
                        Өтініміңіз қабылданды. Біз оны 24 сағат ішінде қарап,
                        көрсетілген электрондық пошта арқылы немесе whatsapp арқылы сізбен хабарласамыз.
                    </p>

                    <p>
                        Осы уақыт аралығында біздің
                        <a href="events.php">іс-шараларымызбен</a> танысып
                        немесе <a href="about.php">клуб туралы</a> толығырақ ақпарат ала аласыз.
                    </p>

                    <div class="success-actions">
                        <a href="index.php" class="btn">Басты бетке</a>
                        <a href="events.php" class="btn btn-outline">Іс-шаралар</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'blocks/footer.php'; ?>
</body>
</html>