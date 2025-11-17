<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

// Обработка изменения статуса заявки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE applications SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $id]);
    
    header("Location: applications.php?success=updated");
    exit;
}

// Получение заявок
$sql = "SELECT * FROM applications ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на вступление - DC President</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/png" href="/images/logo_president.png">
    <style>
        .status-select {
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #333;
            background: var(--bg);
            color: var(--text);
        }
        
        .status-новая { color: #ffa726; }
        .status-обработана { color: #4caf50; }
        .status-отклонена { color: #f44336; }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Заявки на вступление</h1>
                <div class="stats" style="color: #888; font-size: 14px;">
                    Всего: <?= count($applications) ?> | 
                    Новых: <?= count(array_filter($applications, function($app) { return $app['status'] === 'новая'; })) ?>
                </div>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Статус заявки обновлен
                </div>
            <?php endif; ?>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Сообщение</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                        <tr>
                            <td><?= $application['id'] ?></td>
                            <td><?= htmlspecialchars($application['name']) ?></td>
                            <td><?= htmlspecialchars($application['email']) ?></td>
                            <td style="max-width: 200px;">
                                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= htmlspecialchars($application['message']) ?>
                                </div>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($application['created_at'])) ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id" value="<?= $application['id'] ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="новая" <?= $application['status'] === 'новая' ? 'selected' : '' ?>>Новая</option>
                                        <option value="обработана" <?= $application['status'] === 'обработана' ? 'selected' : '' ?>>Обработана</option>
                                        <option value="отклонена" <?= $application['status'] === 'отклонена' ? 'selected' : '' ?>>Отклонена</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <button onclick="viewApplication(<?= $application['id'] ?>)" class="btn-action btn-edit">👁️</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Модальное окно для просмотра заявки -->
    <div id="applicationModal" class="modal">
        <div class="modal-content">
            <h2>Заявка #<span id="applicationId"></span></h2>
            <div id="applicationDetails">
                <!-- Детали заявки будут загружены здесь -->
            </div>
            <div class="form-actions">
                <button type="button" onclick="closeModal()" class="btn-admin" style="background: #666;">Закрыть</button>
            </div>
        </div>
    </div>
    
    <script>
        function viewApplication(id) {
            fetch(`../api/get_application.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('applicationId').textContent = data.id;
                    document.getElementById('applicationDetails').innerHTML = `
                        <div style="margin-bottom: 15px;">
                            <strong>Имя:</strong> ${data.name}
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Email:</strong> ${data.email}
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Сообщение:</strong>
                            <div style="background: var(--bg); padding: 10px; border-radius: 6px; margin-top: 5px;">
                                ${data.message.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Дата отправки:</strong> ${new Date(data.created_at).toLocaleString('ru-RU')}
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>IP адрес:</strong> ${data.ip_address}
                        </div>
                        <div>
                            <strong>Статус:</strong> 
                            <form method="POST" style="display: inline-block; margin-left: 10px;">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="id" value="${data.id}">
                                <select name="status" class="status-select" onchange="this.form.submit()">
                                    <option value="новая" ${data.status === 'новая' ? 'selected' : ''}>Новая</option>
                                    <option value="обработана" ${data.status === 'обработана' ? 'selected' : ''}>Обработана</option>
                                    <option value="отклонена" ${data.status === 'отклонена' ? 'selected' : ''}>Отклонена</option>
                                </select>
                            </form>
                        </div>
                    `;
                    
                    document.getElementById('applicationModal').style.display = 'block';
                });
        }
        
        function closeModal() {
            document.getElementById('applicationModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('applicationModal');
            if (event.target === modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>