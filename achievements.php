<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php include 'blocks/head.php'; ?>

<link rel="stylesheet" href="css/blocks.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1>Достижения</h1>
      <p>Наши победы и успехи в дебатных турнирах</p>
    </div>
  </section>

  <section class="achievements-page">
    <div class="container">
      <div class="achievements-grid-page">
        <?php 
        $achievements = getAchievements(12);
        if (empty($achievements)): ?>
          <div class="no-achievements">
            <p>Пока нет достижений</p>
          </div>
        <?php else: ?>
          <?php foreach ($achievements as $achievement): ?>
          <div class="achievement-card-page">
            <div class="achievement-image">
              <img src="<?= $achievement['image_path'] ?: 'images/cups.jpg' ?>" 
                   alt="<?= htmlspecialchars($achievement['title']) ?>">
            </div>
            <div class="achievement-content-page">
              <h3><?= htmlspecialchars($achievement['title']) ?></h3>
              <?php if ($achievement['tournament_name']): ?>
                <p class="tournament-page">🏆 <?= htmlspecialchars($achievement['tournament_name']) ?></p>
              <?php endif; ?>
              <?php if ($achievement['position']): ?>
                <p class="position-page">🎯 <?= htmlspecialchars($achievement['position']) ?></p>
              <?php endif; ?>
              <p class="description-page"><?= htmlspecialchars($achievement['description']) ?></p>
              <?php if ($achievement['achievement_date']): ?>
                <p class="date-page">📅 <?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></p>
              <?php endif; ?>

              <a href="achievement-single.php?id=<?= $achievement['id'] ?>" class="achievement-link-page">Подробнее →</a>
              
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>