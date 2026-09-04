<?php
function renderCarousel($images, $id = 'carousel') {
    // Если передан JSON, парсим его
    if (is_string($images)) {
        $images = json_decode($images, true);
    }
    
    // Проверяем, что это массив и содержит изображения
    if (!is_array($images) || empty($images)) {
        return '';
    }
    
    // Фильтруем пустые значения
    $images = array_filter($images);
    
    if (count($images) < 2) {
        // Если только одно фото, просто показываем его без карусели
        $firstImage = reset($images);
        return '<div class="single-image"><img src="' . htmlspecialchars($firstImage) . '" alt="Фото"></div>';
    }
    
    ob_start();
?>
<div class="carousel-container" id="<?= $id ?>">
    <div class="carousel-slides">
        <?php foreach ($images as $index => $image): ?>
        <div class="carousel-slide <?= $index === 0 ? 'active' : '' ?>">
            <img src="<?= htmlspecialchars($image) ?>" alt="Фото <?= $index + 1 ?>">
        </div>
        <?php endforeach; ?>
    </div>
    
    <button class="carousel-prev" onclick="changeSlide('<?= $id ?>', -1)">❮</button>
    <button class="carousel-next" onclick="changeSlide('<?= $id ?>', 1)">❯</button>
    
    <div class="carousel-dots">
        <?php foreach ($images as $index => $image): ?>
        <span class="carousel-dot <?= $index === 0 ? 'active' : '' ?>" 
              onclick="goToSlide('<?= $id ?>', <?= $index ?>)"></span>
        <?php endforeach; ?>
    </div>
</div>

<style>
.carousel-container {
    position: relative;
    max-width: 100%;
    margin: 20px 0;
    overflow: hidden;
    border-radius: 12px;
}

.carousel-slides {
    display: flex;
    transition: transform 0.5s ease;
}

.carousel-slide {
    min-width: 100%;
    display: none;
}

.carousel-slide.active {
    display: block;
}

.carousel-slide img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 12px;
}

.carousel-prev,
.carousel-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    padding: 12px 18px;
    cursor: pointer;
    border-radius: 50%;
    font-size: 18px;
    transition: background 0.3s ease;
    z-index: 10;
}

.carousel-prev { left: 10px; }
.carousel-next { right: 10px; }

.carousel-prev:hover,
.carousel-next:hover {
    background: rgba(181, 0, 0, 0.8);
}

.carousel-dots {
    text-align: center;
    margin-top: 15px;
}

.carousel-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #555;
    margin: 0 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.carousel-dot.active {
    background: var(--red);
}

.carousel-dot:hover {
    background: var(--red);
}
</style>

<script>
function changeSlide(carouselId, direction) {
    const container = document.getElementById(carouselId);
    const slides = container.querySelectorAll('.carousel-slide');
    const dots = container.querySelectorAll('.carousel-dot');
    let currentIndex = 0;
    
    slides.forEach((slide, index) => {
        if (slide.classList.contains('active')) {
            currentIndex = index;
        }
    });
    
    let newIndex = currentIndex + direction;
    if (newIndex < 0) newIndex = slides.length - 1;
    if (newIndex >= slides.length) newIndex = 0;
    
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    
    slides[newIndex].classList.add('active');
    dots[newIndex].classList.add('active');
}

function goToSlide(carouselId, index) {
    const container = document.getElementById(carouselId);
    const slides = container.querySelectorAll('.carousel-slide');
    const dots = container.querySelectorAll('.carousel-dot');
    
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    
    slides[index].classList.add('active');
    dots[index].classList.add('active');
}
</script>
<?php
    return ob_get_clean();
}
?>