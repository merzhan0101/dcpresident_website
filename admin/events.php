<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrfToken();
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        /*case 'add':
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'] ?: null;
            $status = $_POST['status'];

            $image_path = null;
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadEventPhoto($_FILES['event_image']);
            }

            $sql = "INSERT INTO events (title, description, event_date, event_time, status, image_path) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status, $image_path]);

            // $sql = "INSERT INTO events (title, description, event_date, event_time, status) 
            //         VALUES (?, ?, ?, ?, ?)";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $description, $event_date, $event_time, $status]);
            
            header("Location: events.php?success=added");
            exit;*/

        case 'add':
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'] ?: null;
            $status = $_POST['status'];
            $instagram_url = trim($_POST['instagram_url']) ?: null;

            // Основное фото
            $image_path = null;
            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                $image_path = uploadEventPhoto($_FILES['event_image']);
            }
            
            // Дополнительные фото
            $gallery_images = null;
            if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
                $uploaded = uploadMultipleImages($_FILES['gallery_images'], 'events');
                if (!empty($uploaded)) {
                    $gallery_images = json_encode($uploaded);
                }
            }

            $sql = "INSERT INTO events (title, description, event_date, event_time, status, image_path, gallery_images, instagram_url) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status, $image_path, $gallery_images, $instagram_url]);
            
            header("Location: events.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'] ?: null;
            $status = $_POST['status'];

            $image_path = $_POST['current_image'] ?? null;

            if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
                if (
                    $image_path &&
                    strpos($image_path, 'images/events/') === 0 &&
                    file_exists('../' . $image_path)
                ) {
                    unlink('../' . $image_path);
                }

                $image_path = uploadEventPhoto($_FILES['event_image']);
            }
            
            $sql = "UPDATE events 
                    SET title=?, description=?, event_date=?, event_time=?, status=?, image_path=? 
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $description, $event_date, $event_time, $status, $image_path, $id]);

            // $sql = "UPDATE events SET title=?, description=?, event_date=?, event_time=?, status=? WHERE id=?";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $description, $event_date, $event_time, $status, $id]);
            
            header("Location: events.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];

            // Удаляем фото и галерею мероприятия перед удалением записи
            $sql = "SELECT image_path, gallery_images FROM events WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $event = $stmt->fetch();

            if ($event) {
                if ($event['image_path'] && file_exists('../' . $event['image_path'])) {
                    unlink('../' . $event['image_path']);
                }
                if ($event['gallery_images']) {
                    $galleryFiles = json_decode($event['gallery_images'], true);
                    if (is_array($galleryFiles)) {
                        foreach ($galleryFiles as $galleryFile) {
                            if (file_exists('../' . $galleryFile)) {
                                unlink('../' . $galleryFile);
                            }
                        }
                    }
                }
            }

            $sql = "DELETE FROM events WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: events.php?success=deleted");
            exit;
    }
}

$events = getAllEvents('all');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление мероприятиями - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <style>
        .events-toolbar{
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

        .events-toolbar input,
        .events-toolbar select{

            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;

        }

        .events-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{

            padding:9px 14px;
            background:rgba(181,0,0,.15);
            border-radius:8px;
            color:#ddd;

        }

        @media(max-width:900px){

            .events-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .events-toolbar input{
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
                <h1>Управление мероприятиями</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить мероприятие</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div id="successAlert" style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px; transition: opacity 0.5s ease;">
                    ✅ Мероприятие успешно <?= $_GET['success'] == 'added' ? 'добавлено' : ($_GET['success'] == 'updated' ? 'обновлено' : 'удалено') ?>
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

            <div class="events-toolbar">
                <div class="toolbar-left">

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по названию..."
                    >

                    <select id="statusFilter">
                        <option value="all">Все статусы</option>
                        <option value="Тіркелу">Тіркелу</option>
                        <option value="Орындалуда">Орындалуда</option>
                        <option value="Аяқталды">Аяқталды</option>
                    </select>

                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($events) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $index=>$event): ?>
                        <tr
                            class="event-row"
                            data-title="<?= htmlspecialchars(mb_strtolower($event['title'])) ?>"
                            data-status="<?= htmlspecialchars($event['status']) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($event['title']) ?></td>
                            <td><?= date('d.m.Y', strtotime($event['event_date'])) ?></td>
                            <td><?= $event['event_time'] ? date('H:i', strtotime($event['event_time'])) : '-' ?></td>
                            <td><span class="status-badge status-<?= $event['status'] ?>"><?= $event['status'] ?></span></td>
                            <td>
                                <button onclick="openModal('edit', <?= $event['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $event['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для мероприятий -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Добавить мероприятие</h2>
            <form method="POST" id="eventForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="eventId">
                <input type="hidden" name="current_image" id="currentImage">
                <input type="hidden" name="current_gallery" id="currentGallery">

                <div id="currentImageContainer" style="display: none; margin-bottom: 15px;">
                    <img id="currentImagePreview"
                        src=""
                        alt="Текущее фото"
                        style="width: 140px; height: 90px; object-fit: cover; border-radius: 8px;">
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Фото мероприятия</label>
                    <input type="file" name="event_image" id="eventImage" accept="image/*">
                </div>

                <!-- В модальном окне после загрузки основного фото -->
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Дополнительные фото (несколько)</label>
                    <input type="file" name="gallery_images[]" id="galleryImages" accept="image/*" multiple>
                    <div class="form-hint">Выберите несколько фото, удерживая Ctrl или Shift</div>
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Ссылка на Instagram пост</label>
                    <input type="url" name="instagram_url" id="instagram_url" placeholder="https://www.instagram.com/p/...">
                </div>
                
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Название мероприятия *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Дата *</label>
                        <input type="date" name="event_date" id="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Время</label>
                        <input type="time" name="event_time" id="event_time">
                    </div>
                    
                    <div class="form-group">
                        <label>Статус *</label>
                       <select name="status" id="status" required>
                            <option value="Тіркелу">Тіркелу</option>
                            <option value="Аяқталды">Аяқталды</option>
                            <option value="Орындалуда">Орындалуда</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Описание *</label>
                        <textarea name="description" id="description" rows="4" required></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="btn-admin" style="background: #666;">Отмена</button>
                    <button type="submit" class="btn-admin">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Модальное окно обрезки фото -->
    <div id="cropModal" class="modal" style="z-index: 9999;">
        <div class="modal-content" style="max-width: 600px;">
            <h2>Обрезать фото</h2>
            <p class="form-hint" style="margin-bottom: 12px; color: #ffb74d; font-weight: 600;">⚠️ Проверьте, что в рамку попадают лица/важные детали — при необходимости подвиньте рамку мышью перед тем как применить.</p>
            <div style="max-height: 420px; overflow: hidden; background: #000;">
                <img id="cropImage" style="max-width: 100%; display: block;">
            </div>
            <div class="form-actions" style="margin-top: 15px;">
                <button type="button" onclick="cancelCrop()" class="btn-admin" style="background: #666;">Отмена</button>
                <button type="button" onclick="applyCrop()" class="btn-admin">Применить обрезку</button>
            </div>
        </div>
    </div>

    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
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
        function openModal(action, id = null) {
            const modal = document.getElementById('eventModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            
            if (action === 'add') {
                title.textContent = 'Добавить мероприятие';
                formAction.value = 'add';
                document.getElementById('eventForm').reset();
                document.getElementById('event_date').valueAsDate = new Date();
                document.getElementById('currentImageContainer').style.display = 'none';
                document.getElementById('currentImagePreview').src = '';
            } else {
                title.textContent = 'Редактировать мероприятие';
                formAction.value = 'edit';
                loadEventData(id);
            }
            
            modal.style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('eventModal').style.display = 'none';
        }
        
        function loadEventData(id) {
            fetch(`../api/get_event.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Event data:', data); // Проверяем данные
                    
                    // Проверяем галерею
                    console.log('Gallery images:', data.gallery_images);
                    if (data.gallery_images) {
                        try {
                            const gallery = JSON.parse(data.gallery_images);
                            console.log('Parsed gallery:', gallery);
                        } catch (e) {
                            console.log('Gallery is not JSON:', data.gallery_images);
                        }
                    }

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

                    document.getElementById('eventId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('event_date').value = data.event_date;
                    document.getElementById('event_time').value = data.event_time || '';
                    document.getElementById('status').value = data.status;
                    document.getElementById('description').value = data.description;
                    document.getElementById('instagram_url').value = data.instagram_url || '';
                    document.getElementById('currentGallery').value = data.gallery_images || '';
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

        // FILTER
        const searchInput=document.getElementById("searchInput");
        const statusFilter=document.getElementById("statusFilter");

        const rows=document.querySelectorAll(".event-row");

        function filterEvents(){

            const search=searchInput.value.toLowerCase().trim();
            const status=statusFilter.value;

            rows.forEach(row=>{

                const title=row.dataset.title;
                const rowStatus=row.dataset.status;

                const searchOk=
                    title.includes(search);

                const statusOk=
                    status==="all" ||
                    rowStatus===status;

                row.style.display=
                    searchOk && statusOk
                    ? ""
                    : "none";

            });

        }

        searchInput.addEventListener("input",filterEvents);
        statusFilter.addEventListener("change",filterEvents);
        
        // ============================================================
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        // ============================================================
        // ОБРЕЗКА ФОТО МЕРОПРИЯТИЯ
        // ============================================================
        let eventCropper = null;

        document.getElementById('eventImage').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (evt) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = evt.target.result;
                document.getElementById('cropModal').style.display = 'flex';

                if (eventCropper) {
                    eventCropper.destroy();
                }
                eventCropper = new Cropper(cropImage, {
                    aspectRatio: 1,      // квадратная область — так фото хорошо смотрится и в узкой карточке на /events, и в широкой на главной
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                    ready: function () {
                        // По умолчанию ставим рамку сверху картинки (там чаще всего лица),
                        // а не по центру — центр часто попадает на торс/фон
                        const canvasData = eventCropper.getCanvasData();
                        const cropBoxData = eventCropper.getCropBoxData();
                        eventCropper.setCropBoxData({
                            left: cropBoxData.left,
                            top: canvasData.top,
                            width: cropBoxData.width,
                            height: cropBoxData.width
                        });
                    }
                });
            };
            reader.readAsDataURL(file);
        });

        function cancelCrop() {
            document.getElementById('cropModal').style.display = 'none';
            document.getElementById('eventImage').value = '';
            if (eventCropper) {
                eventCropper.destroy();
                eventCropper = null;
            }
        }

        function applyCrop() {
            if (!eventCropper) return;

            eventCropper.getCroppedCanvas({ width: 800, height: 800 }).toBlob(function (blob) {
                const croppedFile = new File([blob], 'event_photo.jpg', { type: 'image/jpeg' });

                // Подменяем файл в input'е обрезанной версией
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                document.getElementById('eventImage').files = dataTransfer.files;

                // Показываем превью результата
                const previewUrl = URL.createObjectURL(blob);
                document.getElementById('currentImagePreview').src = previewUrl;
                document.getElementById('currentImageContainer').style.display = 'block';

                document.getElementById('cropModal').style.display = 'none';
                eventCropper.destroy();
                eventCropper = null;
            }, 'image/jpeg', 0.9);
        }

        // Отключаем отправку формы по Enter в однострочных полях (кроме textarea)
        document.getElementById('eventForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>