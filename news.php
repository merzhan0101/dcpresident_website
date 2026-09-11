<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php $pageTitle = 'Жаңалықтар'; $pageDescription = 'President дебат клубының жаңалықтары'; ?>
<?php include 'blocks/head.php'; ?>
<?php include 'blocks/carousel.php'; ?>

<link rel="stylesheet" href="css/blocks.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1>Жаңалықтар</h1>
      <p>Біздің клубтың соңғы оқиғалары мен жаңартулары</p>
    </div>
  </section>

  <section class="news-page">
    <div class="container">
      <div class="news-list-page">
        <?php 
        $news = getNews(12);
        if (empty($news)): ?>
          <div class="no-news">
            <p>Әзірге жаңалық жоқ</p>
          </div>
        <?php else: ?>
          <?php foreach ($news as $newsItem): 
            $newsDate = new DateTime($newsItem['news_date']);
          ?>
          <article class="news-item-page">
            <div class="news-date-block-page">
              <span class="day"><?= $newsDate->format('d') ?></span>
              <span class="month"><?= getRussianMonth($newsDate->format('n')) ?></span>
            </div>
            <div class="news-image-page">
              <img src="<?= $newsItem['image_path'] ?: 'images/news-default.png' ?>" 
                   alt="<?= htmlspecialchars($newsItem['title']) ?>">
            </div>
            <div class="news-content-page">
              <h3><?= htmlspecialchars($newsItem['title']) ?></h3>
              <p class="news-excerpt"><?= htmlspecialchars($newsItem['content']) ?></p>
              
              <!-- Карусель -->
              <!-- <\?php if (!empty($newsItem['gallery_images'])): ?>
                <\?= renderCarousel($newsItem['gallery_images'], 'news_' . $newsItem['id']) ?>
              <\?php endif; ?> -->
              
              <div class="news-meta">
                <span class="news-full-date">📅 <?= date('d.m.Y', strtotime($newsItem['news_date'])) ?></span>
                <a href="news-single.php?id=<?= $newsItem['id'] ?>" class="news-link-page">Ары қарай оқу →</a>
              </div>
              
              <!-- Кнопка для Instagram -->
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
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>