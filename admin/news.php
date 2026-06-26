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

            $image_path = null;
            if (
                isset($_FILES['news_image']) &&
                $_FILES['news_image']['error'] === UPLOAD_ERR_OK
            ) {
                $image_path = uploadNewsPhoto($_FILES['news_image']);
            }

            $sql = "INSERT INTO news
                    (title, content, full_content, news_date, image_path)
                    VALUES (?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $content,
                $full_content,
                $news_date,
                $image_path
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
           
            $sql = "UPDATE news
                    SET
                    title=?,
                    content=?,
                    full_content=?,
                    news_date=?,
                    image_path=?
                    WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $content,
                $full_content,
                $news_date,
                $image_path,
                $id
            ]);
            
            // $sql = "UPDATE news SET title=?, content=?, full_content=?, news_date=? WHERE id=?";
            // $stmt = $pdo->prepare($sql);
            // $stmt->execute([$title, $content, $full_content, $news_date, $id]);
            
            header("Location: news.php?success=updated");
            exit;
            
        case 'delete':
            $id = $_POST['id'];
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
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Новость успешно <?= $_GET['success'] == 'added' ? 'добавлена' : ($_GET['success'] == 'updated' ? 'обновлена' : 'удалена') ?>
                </div>
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
    
    <form method="POST" id="deleteForm" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="deleteId">
    </form>
    
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
                });
        }
        
        function confirmDelete(id) {
            if (confirm('Вы уверены, что хотите удалить эту новость?')) {
                document.getElementById('deleteId').value = id;
                document.getElementById('deleteForm').submit();
            }
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
    </script>
</body>
</html>