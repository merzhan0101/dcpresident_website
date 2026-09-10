<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $full_content = trim($_POST['full_content']);
            $tournament_name = trim($_POST['tournament_name']);
            $position = trim($_POST['position']);
            $achievement_date = $_POST['achievement_date'];
            $instagram_url = trim($_POST['instagram_url']) ?: null;

            // Основное фото (уже обрезано на клиенте через Cropper.js — просто сохраняем)
            $image_path = null;
            if (isset($_FILES['achievement_image']) && $_FILES['achievement_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadAchievementPhoto($_FILES['achievement_image']);
            }

            // Дополнительные фото
            $gallery_images = null;
            if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
                $uploaded = uploadMultipleImages($_FILES['gallery_images'], 'achievements');
                if (!empty($uploaded)) {
                    $gallery_images = json_encode($uploaded);
                }
            }

            $sql = "INSERT INTO achievements 
                    (title, description, full_content, tournament_name, position, achievement_date, image_path, gallery_images, instagram_url) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $full_content, $tournament_name, $position, $achievement_date, $image_path, $gallery_images, $instagram_url]);
            
            header("Location: achievements.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $full_content = trim($_POST['full_content']);
            $tournament_name = trim($_POST['tournament_name']);
            $position = trim($_POST['position']);
            $achievement_date = $_POST['achievement_date'];
            $instagram_url = trim($_POST['instagram_url']) ?: null;

            $image_path = $_POST['current_image'] ?? null;
            if (isset($_FILES['achievement_image']) && $_FILES['achievement_image']['error'] === UPLOAD_ERR_OK) {
                if ($image_path && strpos($image_path, 'images/achievements/') === 0 && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);    
                }
                $image_path = uploadAchievementPhoto($_FILES['achievement_image']);
            }

            // Дополнительные фото при редактировании
            $gallery_images = $_POST['current_gallery'] ?? null;
            if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
                $uploaded = uploadMultipleImages($_FILES['gallery_images'], 'achievements');
                if (!empty($uploaded)) {
                    $gallery_images = json_encode($uploaded);
                }
            }

            $sql = "UPDATE achievements 
                    SET title=?, description=?, full_content=?, tournament_name=?, position=?, achievement_date=?, image_path=?, gallery_images=?, instagram_url=? 
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $full_content, $tournament_name, $position, $achievement_date, $image_path, $gallery_images, $instagram_url, $id]);
            
            header("Location: achievements.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];

            // Удаляем фото и галерею достижения перед удалением записи
            $sql = "SELECT image_path, gallery_images FROM achievements WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $achievement = $stmt->fetch();

            if ($achievement) {
                if ($achievement['image_path'] && file_exists('../' . $achievement['image_path'])) {
                    unlink('../' . $achievement['image_path']);
                }
                if ($achievement['gallery_images']) {
                    $galleryFiles = json_decode($achievement['gallery_images'], true);
                    if (is_array($galleryFiles)) {
                        foreach ($galleryFiles as $galleryFile) {
                            if (file_exists('../' . $galleryFile)) {
                                unlink('../' . $galleryFile);
                            }
                        }
                    }
                }
            }

            $sql = "DELETE FROM achievements WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: achievements.php?success=deleted");
            exit;
    }
}

$achievements = getAllAchievements();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление достижениями - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">

    <!-- Cropper.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <!-- Cropper.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        .achievements-toolbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:16px;
            margin:20px 0;
            padding:18px;
            background:var(--gray);
            border:1px solid #333;
            border-radius:12px;
        }

        .toolbar-left{
            display:flex;
            gap:12px;
            flex-wrap:wrap;
        }

        .achievements-toolbar input,
        .achievements-toolbar select{

            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;

        }

        .achievements-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{

            padding:9px 14px;
            background:rgba(181,0,0,.15);
            border-radius:8px;
            color:#ddd;

        }

        @media(max-width:900px){

            .achievements-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .achievements-toolbar input{
                min-width:100%;
            }

        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Управление достижениями</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить достижение</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div id="successAlert" style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px; transition: opacity 0.5s ease;">
                    ✅ Достижение успешно <?= $_GET['success'] == 'added' ? 'добавлено' : ($_GET['success'] == 'updated' ? 'обновлено' : 'удалено') ?>
                </div>
                <script>
                    setTimeout(function () {
                        const alertBox = document.getElementById('successAlert');
                        if (alertBox) {
                            alertBox.style.opacity = '0';
                            setTimeout(function () { alertBox.remove(); }, 500);
                        }
                    }, 3000);
                </script>
            <?php endif; ?>

            <div class="achievements-toolbar">
                <div class="toolbar-left">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по названию..."
                    >
                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($achievements) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Турнир</th>
                            <th>Позиция</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($achievements as $index=>$achievement): ?>
                        <tr
                            class="achievement-row"
                            data-title="<?= htmlspecialchars(mb_strtolower($achievement['title'])) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($achievement['title']) ?></td>
                            <td><?= htmlspecialchars($achievement['tournament_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($achievement['position'] ?? '-') ?></td>
                            <td><?= date('d.m.Y', strtotime($achievement['achievement_date'])) ?></td>
                            <td>
                                <button onclick="openModal('edit', <?= $achievement['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $achievement['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для достижений -->
    <div id="achievementModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Добавить достижение</h2>
            <form method="POST" id="achievementForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="achievementId">
                <input type="hidden" name="current_image" id="currentImage">
                <input type="hidden" name="current_gallery" id="currentGallery">

                <div id="currentImageContainer" style="display: none; margin-bottom: 15px;">
                    <img id="currentImagePreview"
                        src=""
                        alt="Текущее фото"
                        style="width: 140px; height: 90px; object-fit: cover; border-radius: 8px;">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Фото достижения</label>
                    <input type="file" name="achievement_image" id="achievementImage" accept="image/*" 
                        onchange="openCropModal(this, 'achievementPreview')">
                    <div id="achievementPreview" class="image-preview-container" style="display: none;">
                        <img id="achievementPreviewImg" src="" alt="Предпросмотр">
                        <div class="crop-hint">✂️ Обрезано</div>
                    </div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Дополнительные фото (несколько)</label>
                    <input type="file" name="gallery_images[]" id="galleryImages" accept="image/*" multiple>
                    <div class="form-hint">Выберите несколько фото, удерживая Ctrl или Shift</div>
                    
                    <!-- Превью уже загруженной галереи (при редактировании) -->
                    <div id="galleryPreview" style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px;"></div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Ссылка на Instagram пост</label>
                    <input type="url" name="instagram_url" id="instagram_url" placeholder="https://www.instagram.com/p/...">
                </div>
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Название достижения *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Турнир</label>
                        <input type="text" name="tournament_name" id="tournament_name">
                    </div>
                    
                    <div class="form-group">
                        <label>Позиция/награда</label>
                        <input type="text" name="position" id="position" placeholder="1 место, Лучший спикер">
                    </div>
                    
                    <div class="form-group">
                        <label>Дата достижения *</label>
                        <input type="date" name="achievement_date" id="achievement_date" required>
                    </div>
                    
                    <div class="form-group"style="grid-column: 1 / -1;">
                        <label>Краткое описание *</label>
                        <textarea name="description" id="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Полное описание</label>
                        <textarea name="full_content" id="full_content" rows="5"></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="btn-admin" style="background: #666;">Отмена</button>
                    <button type="submit" class="btn-admin">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Crop Modal -->
    <div id="cropModal" class="crop-modal">
        <div class="crop-modal-content">
            <h3>✂️ Обрезать изображение</h3>
            <p style="color: #888; text-align: center; margin-bottom: 15px; font-size: 14px;">
                Выберите область для отображения (соотношение 16:9)
            </p>
            <div class="crop-container">
                <img id="cropImage" src="" alt="Изображение для обрезки">
            </div>
            <div class="crop-controls">
                <button type="button" class="btn-crop btn-cancel" onclick="closeCropModal()">Отмена</button>
                <button type="button" class="btn-crop btn-apply" onclick="applyCrop()">✅ Применить</button>
            </div>
        </div>
    </div>
    
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>

    <!-- CONFIRM -->
    <div id="deleteModal" class="delete-modal">
        <div class="delete-modal-content">
            <div class="delete-modal-icon">🗑️</div>

            <h3>Удалить запись?</h3>

            <p>
                Это действие нельзя будет отменить.
                Запись будет удалена безвозвратно.
            </p>

            <div class="delete-modal-actions">
                <button type="button" class="btn-cancel-delete" onclick="closeDeleteModal()">
                    Отмена
                </button>

                <button type="button" class="btn-confirm-delete" onclick="submitDeleteForm()">
                    Да, удалить
                </button>
            </div>
        </div>
    </div>
    
    

    <script>
   
    // ===== ОСНОВНЫЕ ФУНКЦИИ =====
    function openModal(action, id = null) {
        const modal = document.getElementById('achievementModal');
        const title = document.getElementById('modalTitle');
        const formAction = document.getElementById('formAction');
        
        if (action === 'add') {
            title.textContent = 'Добавить достижение';
            formAction.value = 'add';
            document.getElementById('achievementForm').reset();
            document.getElementById('achievement_date').valueAsDate = new Date();
            document.getElementById('galleryPreview').innerHTML = '';
        } else {
            title.textContent = 'Редактировать достижение';
            formAction.value = 'edit';
            loadAchievementData(id);
        }
        
        modal.style.display = 'flex';
    }
    
    function closeModal() {
        document.getElementById('achievementModal').style.display = 'none';
    }
    
    function loadAchievementData(id) {
        fetch(`../api/get_achievement.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                const currentImage = document.getElementById('currentImage');
                const currentImageContainer = document.getElementById('currentImageContainer');
                const currentImagePreview = document.getElementById('currentImagePreview');

                if (data.image_path) {
                    currentImage.value = data.image_path;
                    currentImagePreview.src = '../' + data.image_path;
                    currentImageContainer.style.display = 'block';
                } else {
                    currentImage.value = '';
                    currentImagePreview.src = '';
                    currentImageContainer.style.display = 'none';
                }

                document.getElementById('achievementId').value = data.id;
                document.getElementById('title').value = data.title;
                document.getElementById('tournament_name').value = data.tournament_name || '';
                document.getElementById('position').value = data.position || '';
                document.getElementById('achievement_date').value = data.achievement_date;
                document.getElementById('description').value = data.description;
                document.getElementById('full_content').value = data.full_content || '';
                document.getElementById('instagram_url').value = data.instagram_url || '';

                // Восстанавливаем уже загруженную галерею (доп. фото)
                document.getElementById('currentGallery').value = data.gallery_images || '';
                const galleryPreview = document.getElementById('galleryPreview');
                galleryPreview.innerHTML = '';
                if (data.gallery_images) {
                    try {
                        const existingImages = JSON.parse(data.gallery_images);
                        existingImages.forEach(function (imgPath) {
                            const div = document.createElement('div');
                            div.style.cssText = 'position: relative; width: 100px; height: 75px; border-radius: 8px; overflow: hidden; border: 2px solid #333;';
                            div.innerHTML = `<img src="../${imgPath}" style="width: 100%; height: 100%; object-fit: cover;">`;
                            galleryPreview.appendChild(div);
                        });
                    } catch (e) {
                        console.error('Не удалось разобрать gallery_images:', e);
                    }
                }
            });
    }
    
    let deleteId = null;

    function confirmDelete(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        deleteId = null;
        document.getElementById('deleteModal').classList.remove('active');
    }

    function submitDeleteForm() {
        if (!deleteId) return;
        document.getElementById('deleteId').value = deleteId;
        document.getElementById('deleteForm').submit();
    }

    // ===== ПОИСК И ФИЛЬТР =====
    const searchInput = document.getElementById("searchInput");
    const rows = document.querySelectorAll(".achievement-row");

    function filterAchievements() {
        const search = searchInput.value.toLowerCase().trim();
        rows.forEach(row => {
            const title = row.dataset.title;
            const searchOk = title.includes(search);
            row.style.display = searchOk ? "" : "none";
        });
    }

    if (searchInput) {
        searchInput.addEventListener("input", filterAchievements);
    }

    // ===== ЗАКРЫТИЕ МОДАЛОК ПО КЛИКУ ВНЕ =====
    window.onclick = function(event) {
        const modal = document.getElementById('achievementModal');
        if (event.target === modal) {
            closeModal();
        }
        const cropModal = document.getElementById('cropModal');
        if (event.target === cropModal) {
            closeCropModal();
        }
    }

    // ===== CROPPER.JS ДЛЯ ОСНОВНОГО ФОТО =====
    let cropper = null;
    let cropInput = null;
    let cropTargetId = null;

    function openCropModal(inputElement, targetId) {
        const file = inputElement.files[0];
        if (!file) return;
        
        cropInput = inputElement;
        cropTargetId = targetId;
        
        const modal = document.getElementById('cropModal');
        modal.classList.add('active');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('cropImage');
            img.src = e.target.result;
            
            img.onload = function() {
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(img, {
                    aspectRatio: 16 / 9,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.9,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    ready: function () {
                        // Рамка стартует сверху (там обычно лица), а не по центру
                        const canvasData = cropper.getCanvasData();
                        const cropBoxData = cropper.getCropBoxData();
                        cropper.setCropBoxData({
                            left: cropBoxData.left,
                            top: canvasData.top,
                            width: cropBoxData.width,
                            height: cropBoxData.width * 9 / 16
                        });
                    }
                });
            };
        };
        reader.readAsDataURL(file);
    }

    function closeCropModal() {
        const modal = document.getElementById('cropModal');
        modal.classList.remove('active');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        cropInput = null;
        cropTargetId = null;
    }

    function applyCrop() {
        if (!cropper || !cropInput) return;
        
        const canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 450,
        });
        
        canvas.toBlob(function(blob) {
            const fileName = 'cropped_' + Date.now() + '.jpg';
            const file = new File([blob], fileName, { type: 'image/jpeg' });
            
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            cropInput.files = dataTransfer.files;
            
            const previewContainer = document.getElementById(cropTargetId);
            const previewImg = document.getElementById(cropTargetId + 'Img');
            if (previewContainer && previewImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
            
            closeCropModal();
            
        }, 'image/jpeg', 0.92);
    }


        // Отключаем отправку формы по Enter в однострочных полях (кроме textarea)
        document.getElementById('achievementForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>