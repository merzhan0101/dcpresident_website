<!DOCTYPE html>
<html lang="ru">

<link rel="stylesheet" href="css/blocks.css">

<?php 
include 'php/functions.php';
include 'blocks/carousel.php';

// Получаем ID мероприятия из URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$event = getEventById($event_id);

// Если мероприятие не найдено, показываем 404
if (!$event) {
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
      <h1><?= htmlspecialchars($event['title']) ?></h1>
      <p><?= date('d.m.Y', strtotime($event['event_date'])) ?> <?= $event['event_time'] ? '— ' . date('H:i', strtotime($event['event_time'])) : '' ?></p>
    </div>
  </section>

  <section class="event-single">
    <div class="container">
      <article class="event-full">
        <div class="event-meta">
          <span class="event-date">📅 <?= date('d.m.Y', strtotime($event['event_date'])) ?></span>
          <?php if ($event['event_time']): ?>
            <span class="event-time">⏰ <?= date('H:i', strtotime($event['event_time'])) ?></span>
          <?php endif; ?>
          <span class="event-status <?= strtolower($event['status']) ?>"><?= $event['status'] ?></span>
          <?php if (!empty($event['instagram_url'])): ?>
            <a href="<?= $event['instagram_url'] ?>" target="_blank" class="btn-instagram-small">
              📷 Instagram
            </a>
          <?php endif; ?>
        </div>
        
        <?php if ($event['image_path']): ?>
        <div class="event-hero-image">
          <img src="<?= $event['image_path'] ?>" alt="<?= htmlspecialchars($event['title']) ?>">
        </div>
        <?php endif; ?>
        
        <!-- Карусель для дополнительных фото -->
        <?php if (!empty($event['gallery_images'])): ?>
          <?= renderCarousel($event['gallery_images'], 'event_single_' . $event['id']) ?>
        <?php endif; ?>
        
        <div class="event-content-full">
          <?= nl2br(htmlspecialchars($event['description'])) ?>
        </div>
        
        <div class="event-footer">
          <a href="events.php" class="btn-back">← Іс-шараларға оралу</a>
          
          <?php if (!empty($event['instagram_url'])): ?>
            <a href="<?= $event['instagram_url'] ?>" target="_blank" class="btn-instagram">
              📷 Instagram-да көру
            </a>
          <?php endif; ?>
        </div>
      </article>
      
      <!-- Похожие мероприятия -->
      <aside class="related-events">
        <h3>Басқа іс-шаралар</h3>
        <div class="related-grid">
          <?php 
          $related_events = getEvents(3);
          foreach ($related_events as $related):
            if ($related['id'] != $event_id):
          ?>
          <div class="related-item">
            <img src="<?= $related['image_path'] ?: 'images/school_debate.jpg' ?>" 
                 alt="<?= htmlspecialchars($related['title']) ?>">
            <h4><?= htmlspecialchars($related['title']) ?></h4>
            <p><?= date('d.m.Y', strtotime($related['event_date'])) ?></p>
            <a href="events-single.php?id=<?= $related['id'] ?>" class="read-more">Толығырақ →</a>
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