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
      <h1>Іс-шаралар</h1>
      <p>Біздің клубтың алдағы және өткен барлық іс-шаралары</p>
    </div>
  </section>

  <section class="events-page">
    <div class="container">
      <div class="tabs">
        <button class="tab-btn active" data-tab="upcoming">Алдағы</button>
        <button class="tab-btn" data-tab="past">Өткен</button>
      </div>
      
      <!-- Предстоящие мероприятия -->
      <div class="events-grid-page" id="upcoming-events-page">
        <?php 
        $events = getEvents(12, 'upcoming');
        if (empty($events)): ?>
          <div class="no-events">
            <p>Қазіргі уақытта алдағы іс шаралар жоқ</p>
            <p>Біздің әлеуметтік желілерде хабардар болыңыз</p>
          </div>
        <?php else: ?>
          <?php foreach ($events as $event): ?>
            <div class="event-card-page">
                <div class="event-image">
                    <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                        alt="<?= htmlspecialchars($event['title']) ?>">
                </div>
                <div class="event-info">
                    <h3><?= htmlspecialchars($event['title']) ?></h3>
                    <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
                    
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


          <!-- <div class="event-card-page">
            <div class="event-image">
              <img src="</?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                   alt="</?= htmlspecialchars($event['title']) ?>">
            </div>
            <div class="event-info">
              <h3></?= htmlspecialchars($event['title']) ?></h3>
              <p class="event-description"></?= htmlspecialchars($event['description']) ?></p>
              <div class="event-meta">
                <span class="event-date">
                  📅 </?= date('d.m.Y', strtotime($event['event_date'])) ?>
                  </?= $event['event_time'] ? '— ' . date('H:i', strtotime($event['event_time'])) : '' ?>
                </span>
                <span class="tag </?= strtolower($event['status']) ?>"></?= $event['status'] ?></span>
              </div>
            </div>
          </div> -->
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      
      <!-- Прошедшие мероприятия -->
      <div class="events-grid-page" id="past-events-page" style="display: none;">
        <?php 
        $pastEvents = getEvents(12, 'past');
        if (empty($pastEvents)): ?>
          <div class="no-events">
            <p>Әзірге өткен іс-шаралар жоқ</p>
          </div>
        <?php else: ?>
          <?php foreach ($pastEvents as $event): ?>
          <div class="event-card-page">
            <div class="event-image">
              <img src="<?= $event['image_path'] ?: 'images/school_debate.jpg' ?>" 
                   alt="<?= htmlspecialchars($event['title']) ?>">
            </div>
            <div class="event-info">
              <h3><?= htmlspecialchars($event['title']) ?></h3>
              <p class="event-description"><?= htmlspecialchars($event['description']) ?></p>
              <div class="event-meta">
                <span class="event-date">
                  📅 <?= date('d.m.Y', strtotime($event['event_date'])) ?>
                </span>
                <span class="tag completed">Аяқталды</span>
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
    const tabBtns = document.querySelectorAll('.tab-btn');
    const upcomingEvents = document.getElementById('upcoming-events-page');
    const pastEvents = document.getElementById('past-events-page');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (this.dataset.tab === 'upcoming') {
                upcomingEvents.style.display = 'grid';
                pastEvents.style.display = 'none';
            } else {
                upcomingEvents.style.display = 'none';
                pastEvents.style.display = 'grid';
            }
        });
    });

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