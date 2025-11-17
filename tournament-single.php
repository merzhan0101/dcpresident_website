<!DOCTYPE html>
<html lang="ru">

<link rel="stylesheet" href="css/tournaments.css">

<?php 
include 'php/functions.php';

$tournament_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tournament = getTournamentById($tournament_id);

if (!$tournament) {
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
      <h1><?= htmlspecialchars($tournament['title']) ?></h1>
      <p>Сезон <?= htmlspecialchars($tournament['season']) ?> • <?= ucfirst($tournament['level']) ?> турнир</p>
    </div>
  </section>

  <section class="tournament-single">
    <div class="container">
      <div class="tournament-hero">
        <div class="tournament-main-image">
          <img src="<?= $tournament['image_path'] ?: 'images/tournament-default.jpg' ?>" 
               alt="<?= htmlspecialchars($tournament['title']) ?>">
          <div class="tournament-badges">
            <span class="tournament-status <?= $tournament['status'] ?>"><?= ucfirst($tournament['status']) ?></span>
            <span class="tournament-level <?= $tournament['level'] ?>"><?= ucfirst($tournament['level']) ?></span>
          </div>
        </div>
        
        <div class="tournament-quick-info">
          <h2>Негізгі ақпарат</h2>
          <div class="info-grid">
            <div class="info-item">
              <span class="info-label">📅 Өткізу күндері:</span>
              <span class="info-value">
                <?= date('d.m.Y', strtotime($tournament['start_date'])) ?>
                <?= $tournament['end_date'] ? ' - ' . date('d.m.Y', strtotime($tournament['end_date'])) : '' ?>
              </span>
            </div>
            <?php if ($tournament['registration_deadline']): ?>
            <div class="info-item">
              <span class="info-label">⏰ Дейін тіркеу:</span>
              <span class="info-value"><?= date('d.m.Y', strtotime($tournament['registration_deadline'])) ?></span>
            </div>
            <?php endif; ?>
            <?php if ($tournament['prize_fund']): ?>
            <div class="info-item">
              <span class="info-label">💰 Жүлде қоры:</span>
              <span class="info-value prize"><?= number_format($tournament['prize_fund'], 0, ',', ' ') ?> ₸</span>
            </div>
            <?php endif; ?>
            <?php if ($tournament['participants_count']): ?>
            <div class="info-item">
              <span class="info-label">👥 Қатысушылар саны:</span>
              <span class="info-value"><?= $tournament['participants_count'] ?>+ командалар</span>
            </div>
            <?php endif; ?>
          </div>
          
          <?php if ($tournament['status'] == 'Тіркелу'): ?>
          <div class="registration-section">
            <a href="#" class="btn btn-large">Турнирге тіркелу</a>
            <p class="registration-note">Тіркеу ашық <?= date('d.m.Y', strtotime($tournament['registration_deadline']))?> дейін</p>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Детальная информация -->
      <div class="tournament-details">
        <div class="detail-section">
          <h3>Турнир жайлы</h3>
          <div class="tournament-description-full">
            <?= nl2br(htmlspecialchars($tournament['full_description'] ?: $tournament['description'])) ?>
          </div>
        </div>

        <?php if ($tournament['organizers']): ?>
        <div class="detail-section">
          <h3>📋 Ұйымдастырушылар</h3>
          <div class="organizers-list">
            <?php
            $organizers = explode(',', $tournament['organizers']);
            foreach ($organizers as $organizer):
              $organizer = trim($organizer);
            ?>
            <div class="organizer-item">
              <span class="organizer-name"><?= htmlspecialchars($organizer) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($tournament['judges']): ?>
        <div class="detail-section">
          <h3>⚖️ Төрешілер</h3>
          <div class="judges-list">
            <?php
            $judges = explode(',', $tournament['judges']);
            foreach ($judges as $judge):
              $judge = trim($judge);
            ?>
            <div class="judge-item">
              <span class="judge-name"><?= htmlspecialchars($judge) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($tournament['teaser_video']): ?>
        <div class="detail-section">
          <h3>🎬 Турнир тизері</h3>
          <div class="teaser-video">
            <video controls width="100%">
              <source src="<?= $tournament['teaser_video'] ?>" type="video/mp4">
              Сіздің браузеріңіз бейнені қолдамайды.
            </video>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="tournament-footer">
        <a href="tournaments.php" class="btn-back">← Турнирлерге оралу</a>
        <!-- <div class="tournament-share">
          <span>Поделиться:</span>
          <a href="#" class="share-link">📱</a>
          <a href="#" class="share-link">📧</a>
          <a href="#" class="share-link">🔗</a>
        </div> -->
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>
</body>
</html>