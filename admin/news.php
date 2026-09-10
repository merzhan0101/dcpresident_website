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
            $content = trim($_POST['content']);
            $full_content = trim($_POST['full_content']);
            $news_date = $_POST['news_date'];
            $instagram_url = trim($_POST['instagram_url']) ?: null;

            $image_path = null;
            if (
                isset($_FILES['news_image']) &&
                $_FILES['news_image']['error'] === UPLOAD_ERR_OK
            ) {
                $image_path = uploadNewsPhoto($_FILES['news_image']);
            }

            // Дополнительные фото
            $gallery_images = null;
            if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
                $uploaded = uploadMultipleImages($_FILES['gallery_images'], 'news');
                if (!empty($uploaded)) {
                    $gallery_images = json_encode($uploaded);
                }
            }

            $sql = "INSERT INTO news
                    (title, content, full_content, news_date, image_path, gallery_images, instagram_url)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $content,
                $full_content,
                $news_date,
                $image_path,
                $gallery_images,
                $instagram_url
            ]);
                        
            // $sql = "INSERT INTO news (title, content, full_content, news_date) 
            //         VALUES (?, ?, ?, ?)";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $content, $full_content, $news_date]);
            
            header("Location: news.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $full_content = trim($_POST['full_content']);
            $news_date = $_POST['news_date'];
            $instagram_url = trim($_POST['instagram_url']) ?: null;

            $image_path = $_POST['current_image'] ?? null;
            if (
                isset($_FILES['news_image']) &&
                $_FILES['news_image']['error'] === UPLOAD_ERR_OK
            ) {
                if (
                    $image_path &&
                    strpos($image_path, 'images/news/') === 0 &&
                    file_exists('../' . $image_path)
                ) {
                    unlink('../' . $image_path);
                }
                $image_path = uploadNewsPhoto($_FILES['news_image']);
            }

            // Дополнительные фото при редактировании
            $gallery_images = $_POST['current_gallery'] ?? null;
            if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
                $uploaded = uploadMultipleImages($_FILES['gallery_images'], 'news');
                if (!empty($uploaded)) {
                    $gallery_images = json_encode($uploaded);
                }
            }
        
            $sql = "UPDATE news
                    SET
                    title=?,
                    content=?,
                    full_content=?,
                    news_date=?,
                    image_path=?,
                    gallery_images=?,
                    instagram_url=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $content,
                $full_content,
                $news_date,
                $image_path,
                $gallery_images,
                $instagram_url,
                $id
            ]);
            
            header("Location: news.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];

            // Удаляем фото и галерею новости перед удалением записи
            $sql = "SELECT image_path, gallery_images FROM news WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $newsItem = $stmt->fetch();

            if ($newsItem) {
                if ($newsItem['image_path'] && file_exists('../' . $newsItem['image_path'])) {
                    unlink('../' . $newsItem['image_path']);
                }
                if ($newsItem['gallery_images']) {
                    $galleryFiles = json_decode($newsItem['gallery_images'], true);
                    if (is_array($galleryFiles)) {
                        foreach ($galleryFiles as $galleryFile) {
                            if (file_exists('../' . $galleryFile)) {
                                unlink('../' . $galleryFile);
                            }
                        }
                    }
                }
            }

            $sql = "DELETE FROM news WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            
            header("Location: news.php?success=deleted");
            exit;
    }
}

$news = getAllNews();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление новостями - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
        <style>
        .news-toolbar{
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

        .news-toolbar input,
        .news-toolbar select{

            height:42px;
            padding:0 14px;
            border-radius:8px;
            border:1px solid #444;
            background:#1b1b1b;
            color:#fff;

        }

        .news-toolbar input{
            min-width:300px;
        }

        .toolbar-stats span{

            padding:9px 14px;
            background:rgba(181,0,0,.15);
            border-radius:8px;
            color:#ddd;

        }

        @media(max-width:900px){

            .news-toolbar{
                flex-direction:column;
                align-items:stretch;
            }

            .toolbar-left{
                width:100%;
            }

            .news-toolbar input{
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
                <h1>Управление новостями</h1>
                <button onclick="openModal('add')" class="btn-admin">+ Добавить новость</button>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div id="successAlert" style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px; transition: opacity 0.5s ease;">
                    ✅ Новость успешно <?= $_GET['success'] == 'added' ? 'добавлена' : ($_GET['success'] == 'updated' ? 'обновлена' : 'удалена') ?>
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

            <div class="news-toolbar">
                <div class="toolbar-left">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="🔍 Поиск по названию..."
                    >
                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($news) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Заголовок</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($news as $index=>$newsItem): ?>
                        <tr
                            class="news-row"
                            data-title="<?= htmlspecialchars(mb_strtolower($newsItem['title'])) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($newsItem['title']) ?></td>
                            <td><?= date('d.m.Y', strtotime($newsItem['news_date'])) ?></td>
                            <td>
                                <button onclick="openModal('edit', <?= $newsItem['id'] ?>)" class="btn-action btn-edit">✏️</button>
                                <button onclick="confirmDelete(<?= $newsItem['id'] ?>)" class="btn-action btn-delete">🗑️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для новостей -->
    <div id="newsModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Добавить новость</h2>
            <form method="POST" id="newsForm" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="newsId">
                <input type="hidden" name="current_image" id="currentImage">
                <input type="hidden" name="current_gallery" id="currentGallery">

                <div id="currentImageContainer" style="display:none;margin-bottom:15px;">
                    <img id="currentImagePreview"
                        src=""
                        style="width:140px;height:90px;object-fit:cover;border-radius:8px;">
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label>Фото новости</label>
                    <input
                        type="file"
                        name="news_image"
                        id="newsImage"
                        accept="image/*">
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
                        <label>Заголовок новости *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Дата публикации *</label>
                        <input type="date" name="news_date" id="news_date" required>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Краткое содержание *</label>
                        <textarea name="content" id="content" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label>Полный текст новости</label>
                        <textarea name="full_content" id="full_content" rows="6"></textarea>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" onclick="closeModal()" class="btn-admin" style="background: #666;">Отмена</button>
                    <button type="submit" class="btn-admin">Опубликовать</button>
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
            const modal = document.getElementById('newsModal');
            const title = document.getElementById('modalTitle');
            const formAction = document.getElementById('formAction');
            
            if (action === 'add') {
                title.textContent = 'Добавить новость';
                formAction.value = 'add';
                document.getElementById('newsForm').reset();
                document.getElementById('news_date').valueAsDate = new Date();
                document.getElementById('currentImageContainer').style.display = 'none';
                document.getElementById('currentImagePreview').src = '';
            } else {
                title.textContent = 'Редактировать новость';
                formAction.value = 'edit';
                loadNewsData(id);
            }
            
            modal.style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('newsModal').style.display = 'none';
        }
        
        function loadNewsData(id) {
            fetch(`../api/get_news.php?id=${id}`)
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

                    document.getElementById('newsId').value = data.id;
                    document.getElementById('title').value = data.title;
                    document.getElementById('news_date').value = data.news_date;
                    document.getElementById('content').value = data.content;
                    document.getElementById('full_content').value = data.full_content || '';
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
        const rows=document.querySelectorAll(".news-row");

        function filterNews(){

            const search=searchInput.value.toLowerCase().trim();

            rows.forEach(row=>{

                const title=row.dataset.title;

                const searchOk=
                    title.includes(search);

                row.style.display=
                    searchOk 
                    ? ""
                    : "none";

            });

        }

        searchInput.addEventListener("input",filterNews);

        // ===============================================================
        window.onclick = function(event) {
            const modal = document.getElementById('newsModal');
            if (event.target === modal) {
                closeModal();
            }
        }
        // Отключаем отправку формы по Enter в однострочных полях (кроме textarea)
        document.getElementById('newsForm').addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });

        // ============================================================
        // ОБРЕЗКА ФОТО НОВОСТИ
        // ============================================================
        let newsCropper = null;

        document.getElementById('newsImage').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (evt) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = evt.target.result;
                document.getElementById('cropModal').style.display = 'flex';

                if (newsCropper) {
                    newsCropper.destroy();
                }
                newsCropper = new Cropper(cropImage, {
                    aspectRatio: 4 / 3,
                    viewMode: 1,
                    autoCropArea: 1,
                    background: false,
                    ready: function () {
                        const canvasData = newsCropper.getCanvasData();
                        const cropBoxData = newsCropper.getCropBoxData();
                        newsCropper.setCropBoxData({
                            left: cropBoxData.left,
                            top: canvasData.top,
                            width: cropBoxData.width,
                            height: cropBoxData.width * 3 / 4
                        });
                    }
                });
            };
            reader.readAsDataURL(file);
        });

        function cancelCrop() {
            document.getElementById('cropModal').style.display = 'none';
            document.getElementById('newsImage').value = '';
            if (newsCropper) {
                newsCropper.destroy();
                newsCropper = null;
            }
        }

        function applyCrop() {
            if (!newsCropper) return;

            newsCropper.getCroppedCanvas({ width: 800, height: 600 }).toBlob(function (blob) {
                const croppedFile = new File([blob], 'news_photo.jpg', { type: 'image/jpeg' });

                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                document.getElementById('newsImage').files = dataTransfer.files;

                const previewUrl = URL.createObjectURL(blob);
                document.getElementById('currentImagePreview').src = previewUrl;
                document.getElementById('currentImageContainer').style.display = 'block';

                document.getElementById('cropModal').style.display = 'none';
                newsCropper.destroy();
                newsCropper = null;
            }, 'image/jpeg', 0.9);
        }
    </script>
</body>
</html>