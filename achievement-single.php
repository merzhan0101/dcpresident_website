<!DOCTYPE html>
<html lang="ru">

<link rel="stylesheet" href="css/blocks.css">

<?php 
include 'php/functions.php';
include 'blocks/carousel.php';

// Получаем ID достижения из URL
$achievement_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$achievement = getAchievementById($achievement_id);

// Если достижение не найдено, показываем 404
if (!$achievement) {
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
      <h1><?= htmlspecialchars($achievement['title']) ?></h1>
      <p>Жетістік <?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></p>
    </div>
  </section>

  <section class="achievement-single">
    <div class="container">
      <article class="achievement-full">
        <div class="achievement-meta">
          <span class="achievement-date">📅 <?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></span>
          <?php if ($achievement['tournament_name']): ?>
            <span class="achievement-tournament">🏆 <?= htmlspecialchars($achievement['tournament_name']) ?></span>
          <?php endif; ?>
          <?php if ($achievement['position']): ?>
            <span class="achievement-position">🎯 <?= htmlspecialchars($achievement['position']) ?></span>
          <?php endif; ?>
          <?php if (!empty($achievement['instagram_url'])): ?>
            <a href="<?= $achievement['instagram_url'] ?>" target="_blank" class="btn-instagram-small">
              📷 Instagram
            </a>
          <?php endif; ?>
        </div>
        
        <?php if ($achievement['image_path']): ?>
        <div class="achievement-hero-image">
          <img src="<?= $achievement['image_path'] ?>" alt="<?= htmlspecialchars($achievement['title']) ?>">
        </div>
        <?php endif; ?>
        
        <!-- Карусель -->
        <?php if (!empty($achievement['gallery_images'])): ?>
          <?= renderCarousel($achievement['gallery_images'], 'achievement_single_' . $achievement['id']) ?>
        <?php endif; ?>
        
        <div class="achievement-content-full">
          <?= nl2br(htmlspecialchars($achievement['full_content'] ?: $achievement['description'])) ?>
        </div>
        
        <div class="achievement-footer">
          <a href="achievements.php" class="btn-back">← Жетістікке оралу</a>
          
          <?php if (!empty($achievement['instagram_url'])): ?>
            <a href="<?= $achievement['instagram_url'] ?>" target="_blank" class="btn-instagram">
              📷 Instagram-да көру
            </a>
          <?php endif; ?>
        </div>
      </article>
      
      <!-- Похожие достижения -->
      <aside class="related-achievements">
        <h3>Басқа жетістіктер</h3>
        <div class="related-grid">
          <?php 
          $related_achievements = getAchievements(3);
          foreach ($related_achievements as $related):
            if ($related['id'] != $achievement_id):
          ?>
          <div class="related-item">
            <img src="<?= $related['image_path'] ?: 'images/cups.jpg' ?>" 
                 alt="<?= htmlspecialchars($related['title']) ?>">
            <h4><?= htmlspecialchars($related['title']) ?></h4>
            <p><?= date('d.m.Y', strtotime($related['achievement_date'])) ?></p>
            <a href="achievement-single.php?id=<?= $related['id'] ?>" class="read-more">Толығырақ →</a>
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