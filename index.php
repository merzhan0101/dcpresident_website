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
        <h1><span>Ойлан. Дәлелде. Жеңіске жет!</span></h1>
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
            Біз — Торайғыров университетінің пікірсайыс клубымыз. Мұнда әртүрлі көзқарастар тоғысып, жаңа идеялар талқыланады.<br><br>
            Біздің мақсатымыз — студенттердің сыни ойлауын дамытып, өз пікірін еркін әрі сенімді жеткізе алатын орта қалыптастыру.
        </p>
        <a href="index.php#apply" class="join-btn">Өтінімді жіберу</a>
        <!-- <a href="members.php" class="btn btn-outline">President-тіктер</a> -->
      </div>
    </div>
  </section>

  <!-- INSTAGRAM FEED -->
  <section class="instagram-feed-section">
    <div class="container">
        <h2 class="section-title">Instagram-дағы соңғы жаңалықтар</h2>
        <script src="https://elfsightcdn.com/platform.js" async></script>
        <div class="elfsight-app-9a2cf366-f64c-4e8f-b62c-b502c916a0b4" data-elfsight-app-lazy></div>
    </div>
  </section>

  <!-- EVENTS -->
    <section class="events">
        <div class="container">
            <h2>Іс-шаралар</h2>
            <div class="events-link">
              <a href="events.php" class="text-main-color">Барлық іс-шаралар</a>
            </div>
            
            <div class="hcarousel">
                <button class="hcarousel-btn" onclick="hcarouselScroll('upcoming-events', -1)">❮</button>
                <div class="events-grid hcarousel-track" id="upcoming-events">
                    <?php 
                    $events = getEvents(10, 'all');
                    if (empty($events)): ?>
                        <p class="no-events">Іс-шаралар жоқ</p>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <img src="<?= $event['image_path'] ?: 'images/event-default.jpg' ?>" 
                                alt="<?= htmlspecialchars($event['title']) ?>">
                            <h3><?= htmlspecialchars($event['title']) ?></h3>
                            <p><?= date('d.m.Y', strtotime($event['event_date'])) ?> 
                            <?= $event['event_time'] ? '— ' . date('H:i', strtotime($event['event_time'])) : '' ?></p>
                            <div class="event-actions">
                                <span class="tag <?= strtolower($event['status']) ?>"><?= $event['status'] ?></span>
                                <a href="events-single.php?id=<?= $event['id'] ?>" class="btn-event">Толығырақ →</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button class="hcarousel-btn" onclick="hcarouselScroll('upcoming-events', 1)">❯</button>
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
            <div class="hcarousel">
                <button class="hcarousel-btn" onclick="hcarouselScroll('achievements-track', -1)">❮</button>
                <div class="achievements-grid hcarousel-track" id="achievements-track">
                    <?php 
                    $achievements = getAchievements(10);
                    foreach ($achievements as $achievement): ?>
                    <div class="achievement-card">
                        <img src="<?= $achievement['image_path'] ?: 'images/achievement-default.png' ?>" 
                            alt="<?= htmlspecialchars($achievement['title']) ?>">
                        <div class="achievement-content">
                            <h4><?= htmlspecialchars($achievement['title']) ?></h4>
                            <?php if ($achievement['tournament_name']): ?>
                                <p class="tournament"><?= htmlspecialchars($achievement['tournament_name']) ?></p>
                            <?php endif; ?>
                            <?php if ($achievement['position']): ?>
                                <p class="position"><?= htmlspecialchars($achievement['position']) ?></p>
                            <?php endif; ?>
                            <p class="description"><?= htmlspecialchars(excerpt($achievement['description'], 100)) ?></p>
                            <?php if ($achievement['achievement_date']): ?>
                                <p class="date"><?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></p>
                        <?php endif; ?>

                        <a href="achievement-single.php?id=<?= $achievement['id'] ?>" class="achievement-link">Толығырақ →</a>

                    </div>
                </div>
                <?php endforeach; ?>
                </div>
                <button class="hcarousel-btn" onclick="hcarouselScroll('achievements-track', 1)">❯</button>
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
          <div class="hcarousel">
              <button class="hcarousel-btn" onclick="hcarouselScroll('news-track', -1)">❮</button>
              <div class="news-list hcarousel-track" id="news-track">
                <?php 
                $news = getNews(10);
                foreach ($news as $newsItem): 
                    $newsDate = new DateTime($newsItem['news_date']);
                ?>
                <article class="news-item">
                    <img src="<?= $newsItem['image_path'] ?: 'images/news-default.png' ?>" 
                        alt="<?= htmlspecialchars($newsItem['title']) ?>">
                    <div class="news-content">
                        <h4><?= htmlspecialchars($newsItem['title']) ?></h4>
                        <p><?= htmlspecialchars(excerpt($newsItem['content'], 100)) ?></p>
                        <p class="news-full-date">📅 <?= date('d.m.Y', strtotime($newsItem['news_date'])) ?></p>
                        <a href="news-single.php?id=<?= $newsItem['id'] ?>" class="news-link">Ары қарай оқу →</a>

                        <?php if (!empty($newsItem['instagram_url'])): ?>
                        <div class="instagram-link">
                            <a href="<?= $newsItem['instagram_url'] ?>" target="_blank" class="btn-instagram">
                                📷 Instagram-да көру
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
              </div>
              <button class="hcarousel-btn" onclick="hcarouselScroll('news-track', 1)">❯</button>
          </div>
      </div>
  </section>

  <!-- APPLY -->
  <section class="apply" id="apply">
    <div class="container">
      <h2>Біздің клубқа өтініш беріңіз</h2>
      <p>біз оны 24 сағат ішінде қарастырамыз</p>
      <!-- <form action="submit.php" method="POST" class="apply-form"> -->
      <form action="submit_boevoi" method="POST" class="apply-form">
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

<script>
function hcarouselScroll(id, direction) {
    const track = document.getElementById(id);
    if (!track) return;
    const cards = Array.from(track.children);
    if (cards.length === 0) return;

    // Находим карточку, ближе всего к левому краю видимой области трека
    const trackLeft = track.getBoundingClientRect().left;
    let currentIndex = 0;
    let minDiff = Infinity;
    cards.forEach((card, i) => {
        const diff = Math.abs(card.getBoundingClientRect().left - trackLeft);
        if (diff < minDiff) {
            minDiff = diff;
            currentIndex = i;
        }
    });

    // Прицельно скроллим к реальному соседнему блоку — без ручного расчёта
    // пикселей, поэтому ошибка накопиться не может
    const targetIndex = Math.min(Math.max(currentIndex + direction, 0), cards.length - 1);
    cards[targetIndex].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
}
</script>