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
            
            $sql = "INSERT INTO news (title, content, full_content, news_date) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $full_content, $news_date]);
            
            header("Location: news.php?success=added");
            exit;
            
        case 'edit':
            $id = $_POST['id'];
            $title = trim($_POST['title']);
            $content = trim($_POST['content']);
            $full_content = trim($_POST['full_content']);
            $news_date = $_POST['news_date'];
            
            $sql = "UPDATE news SET title=?, content=?, full_content=?, news_date=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $full_content, $news_date, $id]);
            
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
                        <?php foreach ($news as $newsItem): ?>
                        <tr>
                            <td><?= $newsItem['id'] ?></td>
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
            <form method="POST" id="newsForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="newsId">
                
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
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('newsModal').style.display = 'none';
        }
        
        function loadNewsData(id) {
            fetch(`../api/get_news.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
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
        
        window.onclick = function(event) {
            const modal = document.getElementById('newsModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>