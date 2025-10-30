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
        <h1><span>Скорей вступай</span> в наш клуб!</h1>
        <p>
          Это просто пример текста о дебатном клубе President. Lorem ipsum dolor sit amet,
          consectetur adipiscing elit. Развивай критическое мышление и лидерские качества вместе с нами!
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
          Мы — студенческий дебатный клуб Торайгыров Университета, объединяющий активных, умных и
          амбициозных студентов. Наша цель — развивать культуру дебатов и критического мышления.
        </p>
        <!-- <button class="btn">Отправить заявку</button> -->
        <a href="index.php#apply" class="join-btn">Отправить заявку</a>
        <a href="members.php" class="btn btn-outline">Наши участники</a>
      </div>
    </div>
  </section>

  <!-- EVENTS -->
    <section class="events">
        <div class="container">
            <h2>Мероприятия</h2>
            <div class="events-link">
              <a href="events.php" class="text-main-color">Все мероприятия</a>
            </div>
            <!-- <div class="tabs">
                <button class="tab-btn active" data-tab="upcoming">Предстоящие</button>
                <button class="tab-btn" data-tab="past">Прошедшие</button>
            </div> -->
            
            <div class="events-grid" id="upcoming-events">
                <?php 
                $events = getEvents(6, 'upcoming');
                if (empty($events)): ?>
                    <p class="no-events">Нет предстоящих мероприятий</p>
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
                    <p class="no-events">Нет прошедших мероприятий</p>
                <?php else: ?>
                    <?php foreach ($pastEvents as $event): ?>
                    <div class="event-card">
                        <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                            alt="<?= htmlspecialchars($event['title']) ?>">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p><?= date('d.m.Y', strtotime($event['event_date'])) ?></p>
                        <span class="tag completed">Завершено</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- achievements -->
    <section class="achievements">
        <div class="container">
            <h2 class="section-title">Достижения</h2>
            <div class="achievements-link">
              <a href="achievements.php" class="text-main-color">Все достижения</a>
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

                        <a href="achievement-single.php?id=<?= $achievement['id'] ?>" class="achievement-link">Подробнее →</a>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

  <!-- NEWS -->
  <section class="news">
      <div class="container">
          <h2 class="section-title">Новости</h2>
          <div class="news-link">
            <a href="news.php" class="text-main-color">Все новости</a>
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
                      <a href="news-single.php?id=<?= $newsItem['id'] ?>" class="news-link">Читать далее →</a>
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
      <h2>Подай заявку в наш клуб</h2>
      <p>и мы рассмотрим ее в течение 24 часов</p>
      <form action="submit.php" method="POST" class="apply-form">
        <input type="text" name="name" placeholder="Имя" required>
        <input type="email" name="email" placeholder="Почта" required>
        <textarea name="message" placeholder="Почему хочешь вступить?" required></textarea>
        <button type="submit" class="btn">Отправить</button>
      </form>
    </div>
  </section>

</main>

<?php include 'blocks/footer.php'; ?>
