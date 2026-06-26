<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php include 'blocks/head.php'; ?>

<body>
  <?php include 'blocks/header.php'; ?>
<main>

  <!-- HERO -->
  <section class="hero">
    <div class="container">
      <div class="hero-text">
        <h1><span>Біздің клубқа</span> қосыл!</h1>
        <p>
          Өз ойыңды еркін жеткізуді, сенімді сөйлеуді және логикалық дәлелдеуді үйрен.<br>
          Пікірталас мәдениетін бірге дамытайық!
        </p>
      </div>
      <div class="hero-image">
        <img src="images/cup4.jpg" alt="Debate photo">
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="about">
    <div class="container">
      <div class="about-image">
        <img src="images/happy.jpg" alt="About club">
      </div>
      <div class="about-text">
        <h2>DC President</h2>
        <p>
         Біз — белсенді, ақылды және өршіл студенттерді біріктіретін университеттің Торайғыров студенттік пікірсайыс клубымыз. 
         Біздің мақсатымыз — пікірталас пен сыни ойлау мәдениетін дамыту.
        </p>
        <a href="index.php#apply" class="join-btn">Өтінімді жіберу</a>
        <!-- <a href="members.php" class="btn btn-outline">President-тіктер</a> -->
      </div>
    </div>
  </section>

  <!-- EVENTS -->
    <section class="events">
        <div class="container">
            <h2>Іс-шаралар</h2>
            <div class="events-link">
              <a href="events.php" class="text-main-color">Барлық іс-шаралар</a>
            </div>
            
            <div class="events-grid" id="upcoming-events">
                <?php 
                $events = getEvents(6, 'upcoming');
                if (empty($events)): ?>
                    <p class="no-events">Алдағы іс-шаралар жоқ</p>
                <?php else: ?>
                    <?php foreach ($events as $event): ?>
                    <div class="event-card">
                        <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                            alt="<?= htmlspecialchars($event['title']) ?>">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p><?= date('d.m.Y', strtotime($event['event_date'])) ?> 
                        <?= $event['event_time'] ? '— ' . date('H:i', strtotime($event['event_time'])) : '' ?></p>
                        <span class="tag <?= strtolower($event['status']) ?>"><?= $event['status'] ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="events-grid" id="past-events" style="display: none;">
                <?php 
                $pastEvents = getEvents(6, 'past');
                if (empty($pastEvents)): ?>
                    <p class="no-events">Өткен іс-шаралар жоқ</p>
                <?php else: ?>
                    <?php foreach ($pastEvents as $event): ?>
                    <div class="event-card">
                        <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                            alt="<?= htmlspecialchars($event['title']) ?>">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p><?= date('d.m.Y', strtotime($event['event_date'])) ?></p>
                        <span class="tag completed">Аяқталды</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- achievements -->
    <section class="achievements">
        <div class="container">
            <h2 class="section-title">Жетістіктер</h2>
            <div class="achievements-link">
              <a href="achievements.php" class="text-main-color">Барлық жетістіктер</a>
            </div>
            <div class="achievements-grid">
                <?php 
                $achievements = getAchievements(3);
                foreach ($achievements as $achievement): ?>
                <div class="achievement-card">
                    <img src="<?= $achievement['image_path'] ?: 'images/cups.jpg' ?>" 
                        alt="<?= htmlspecialchars($achievement['title']) ?>">
                    <div class="achievement-content">
                        <h4><?= htmlspecialchars($achievement['title']) ?></h4>
                        <?php if ($achievement['tournament_name']): ?>
                            <p class="tournament"><?= htmlspecialchars($achievement['tournament_name']) ?></p>
                        <?php endif; ?>
                        <?php if ($achievement['position']): ?>
                            <p class="position"><?= htmlspecialchars($achievement['position']) ?></p>
                        <?php endif; ?>
                        <p class="description"><?= htmlspecialchars($achievement['description']) ?></p>
                        <?php if ($achievement['achievement_date']): ?>
                            <p class="date"><?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></p>
                        <?php endif; ?>

                        <a href="achievement-single.php?id=<?= $achievement['id'] ?>" class="achievement-link">Толығырақ →</a>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

  <!-- NEWS -->
  <section class="news">
      <div class="container">
          <h2 class="section-title">Жаңалықтар</h2>
          <div class="news-link">
            <a href="news.php" class="text-main-color">Барлық жаңалықтар</a>
          </div>
          <div class="news-list">
              <?php 
              $news = getNews(3); // Берем больше новостей
              foreach ($news as $newsItem): 
                  $newsDate = new DateTime($newsItem['news_date']);
              ?>
              <article class="news-item">
                  <div class="news-date-block">
                      <span class="day"><?= $newsDate->format('d') ?></span>
                      <span class="month"><?= getRussianMonth($newsDate->format('n')) ?></span>
                  </div>
                  <img src="<?= $newsItem['image_path'] ?: 'images/news.jpg' ?>" 
                      alt="<?= htmlspecialchars($newsItem['title']) ?>">
                  <div class="news-content">
                      <h4><?= htmlspecialchars($newsItem['title']) ?></h4>
                      <p><?= htmlspecialchars($newsItem['content']) ?></p>
                      <a href="news-single.php?id=<?= $newsItem['id'] ?>" class="news-link">Ары қарай оқу →</a>
                      <!-- <a href="#" class="news-link">Читать далее →</a> -->
                  </div>
              </article>
              <?php endforeach; ?>
          </div>
      </div>
  </section>

  <!-- APPLY -->
  <section class="apply" id="apply">
    <div class="container">
      <h2>Біздің клубқа өтініш беріңіз</h2>
      <p>біз оны 24 сағат ішінде қарастырамыз</p>
      <!-- <form action="submit.php" method="POST" class="apply-form"> -->
      <form action="submit_boevoi.php" method="POST" class="apply-form">
        <input type="text" name="name" placeholder="Есіміңіз" required>
        <input type="email" name="email" placeholder="Пошта" required>
        <input type="tel" name="phone" placeholder="Телефон / WhatsApp" required>
        <textarea name="message" placeholder="Неліктен клубқа қосылғыңыз келеді?" required></textarea>
        <button type="submit" class="btn">Жіберу</button>
      </form>
    </div>
  </section>

</main>

<?php include 'blocks/footer.php'; ?>