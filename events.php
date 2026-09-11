<!DOCTYPE html>
<html lang="ru">

<?php include 'php/functions.php'; ?>
<?php $pageTitle = 'Іс-шаралар'; $pageDescription = 'President дебат клубының іс-шаралары'; ?>
<?php include 'blocks/head.php'; ?>
<?php include 'blocks/carousel.php'; ?>

<link rel="stylesheet" href="css/blocks.css">

<body>
  <?php include 'blocks/header.php'; ?>
  
<main>
  <section class="page-header">
    <div class="container">
      <h1>Іс-шаралар</h1>
      <p>Біздің клубтың барлық іс-шаралары</p>
    </div>
  </section>

  <section class="events-page">
    <div class="container">
      <div class="events-grid-page" id="events-page">
        <?php 
        $events = getEvents(12, 'all');
        if (empty($events)): ?>
          <div class="no-events">
            <p>Әзірге іс-шаралар жоқ</p>
          </div>
        <?php else: ?>
          <?php foreach ($events as $event): ?>
            <div class="event-card-page">
                <div class="event-image">
                    <img src="<?= $event['image_path'] ?: 'images/event-default.jpg' ?>" 
                        alt="<?= htmlspecialchars($event['title']) ?>">
                </div>
                <div class="event-info">
                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                    <p class="event-description"><?= htmlspecialchars(excerpt($event['description'], 140)) ?></p>
                    
                    <div class="event-meta">
                        <span class="event-date">
                            📅 <?= date('d.m.Y', strtotime($event['event_date'])) ?>
                            <?= $event['event_time'] ? '— ' . date('H:i', strtotime($event['event_time'])) : '' ?>
                        </span>
                        <span class="tag <?= strtolower($event['status']) ?>"><?= $event['status'] ?></span>
                    </div>
                    
                    <!-- Кнопка Instagram -->
                    <?php if (!empty($event['instagram_url'])): ?>
                    <div class="instagram-link">
                        <a href="<?= $event['instagram_url'] ?>" target="_blank" class="btn-instagram">
                            📷 Instagram-да көру
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="event-actions">
                        <a href="events-single.php?id=<?= $event['id'] ?>" class="btn-event">Толығырақ →</a>
                    </div>
                </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<?php include 'blocks/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.getElementById("scrollTopBtn");

    window.addEventListener("scroll", () => {

        if (window.scrollY > 400) {
            scrollBtn.classList.add("show");
        } else {
            scrollBtn.classList.remove("show");
        }

    });

    scrollBtn.addEventListener("click", () => {

        window.scrollTo({
            top:0,
            behavior:"smooth"
        });

    });
});
</script>