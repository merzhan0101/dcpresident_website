<!DOCTYPE html>
<html lang="ru">
  
<link rel="stylesheet" href="css/blocks.css">

<?php 
include 'php/functions.php';
include 'blocks/carousel.php';

// Получаем ID новости из URL
$news_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$news = getNewsById($news_id);

// Если новость не найдена, показываем 404
if (!$news) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}
?>

<?php include 'blocks/head.php'; ?>

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1><?= htmlspecialchars($news['title']) ?></h1>
      <p>Жарияланды: <?= date('d.m.Y', strtotime($news['news_date'])) ?></p>
    </div>
  </section>

  <section class="news-single">
    <div class="container">
      <article class="news-full">
        <div class="news-meta">
          <span class="news-date">📅 <?= date('d.m.Y', strtotime($news['news_date'])) ?></span>
          <?php if (!empty($news['instagram_url'])): ?>
            <a href="<?= $news['instagram_url'] ?>" target="_blank" class="btn-instagram-small">
              📷 Instagram
            </a>
          <?php endif; ?>
        </div>
        
        <?php if ($news['image_path']): ?>
        <div class="news-hero-image">
          <img src="<?= $news['image_path'] ?>" alt="<?= htmlspecialchars($news['title']) ?>">
        </div>
        <?php endif; ?>
        
        <!-- Карусель для дополнительных фото -->
        <?php if (!empty($news['gallery_images'])): ?>
          <?= renderCarousel($news['gallery_images'], 'news_single_' . $news['id']) ?>
        <?php endif; ?>
        
        <div class="news-content-full">
          <?= nl2br(htmlspecialchars($news['full_content'] ?: $news['content'])) ?>
        </div>
        
        <div class="news-footer">
          <a href="news.php" class="btn-back">← Жаңалықтарға оралу</a>
          
          <?php if (!empty($news['instagram_url'])): ?>
            <a href="<?= $news['instagram_url'] ?>" target="_blank" class="btn-instagram">
              📷 Instagram-да көру
            </a>
          <?php endif; ?>
        </div>
      </article>
      
      <!-- Похожие новости -->
      <aside class="related-news">
        <h3>Басқа жаңалықтар</h3>
        <div class="related-grid">
          <?php 
          $related_news = getNews(3);
          foreach ($related_news as $related):
            if ($related['id'] != $news_id):
          ?>
          <div class="related-item">
            <img src="<?= $related['image_path'] ?: 'images/news.jpg' ?>" 
                 alt="<?= htmlspecialchars($related['title']) ?>">
            <h4><?= htmlspecialchars($related['title']) ?></h4>
            <p><?= date('d.m.Y', strtotime($related['news_date'])) ?></p>
            <a href="news-single.php?id=<?= $related['id'] ?>" class="read-more">Толығырақ →</a>
          </div>
          <?php 
            endif;
          endforeach; 
          ?>
        </div>
      </aside>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>
</body>
</html>