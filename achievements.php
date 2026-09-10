<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php include 'blocks/head.php'; ?>
<?php include 'blocks/carousel.php'; ?>

<link rel="stylesheet" href="css/blocks.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1>Жетістіктер</h1>
      <p>Пікірсайыс турнирлеріндегі жеңістеріміз бен жетістіктеріміз</p>
    </div>
  </section>

  <section class="achievements-page">
    <div class="container">
      <div class="achievements-grid-page">
        <?php 
        $perPage = 6;
        $totalAchievements = getAchievementsCount();
        $totalPages = max(1, (int)ceil($totalAchievements / $perPage));
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $achievements = getAchievements($perPage, $offset);
        if (empty($achievements)): ?>
          <div class="no-achievements">
            <p>Әзірге жетістіктер жоқ</p>
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
              
              <!-- Карусель -->
              <!-- <\?php if (!empty($achievement['gallery_images'])): ?>
                <\?= renderCarousel($achievement['gallery_images'], 'achievement_' . $achievement['id']) ?>
              <\?php endif; ?\> -->

              <!-- Кнопка Instagram -->
              <?php if (!empty($achievement['instagram_url'])): ?>
              <div class="instagram-link">
                <a href="<?= $achievement['instagram_url'] ?>" target="_blank" class="btn-instagram">
                  📷 Instagram-да көру
                </a>
              </div>
              <?php endif; ?>

              <a href="achievement-single.php?id=<?= $achievement['id'] ?>" class="achievement-link-page">Толығырақ →</a>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Пагинация">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>" class="page-btn page-prev">← Алдыңғы</a>
        <?php endif; ?>

        <?php foreach (paginationRange($page, $totalPages) as $p): ?>
          <?php if ($p === null): ?>
            <span class="page-dots">…</span>
          <?php else: ?>
            <a href="?page=<?= $p ?>" class="page-btn <?= $p === $page ? 'active' : '' ?>"><?= $p ?></a>
          <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1 ?>" class="page-btn page-next">Келесі →</a>
        <?php endif; ?>
      </nav>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>

<script src="js/script.js"></script>
</body>
</html>