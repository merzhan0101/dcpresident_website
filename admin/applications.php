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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)$_POST['id'];

    $sql = "DELETE FROM applications WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    header("Location: applications.php?success=deleted");
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

        .applications-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin: 20px 0;
            padding: 18px;
            background: var(--gray);
            border: 1px solid #2a2a2a;
            border-radius: 14px;
        }

        .toolbar-left {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .applications-toolbar input,
        .applications-toolbar select {
            height: 42px;
            padding: 0 14px;
            border-radius: 8px;
            border: 1px solid #333;
            background: var(--bg);
            color: var(--text);
        }

        .applications-toolbar input {
            min-width: 320px;
        }

        .toolbar-stats {
            display: flex;
            gap: 10px;
            color: #aaa;
            font-size: 14px;
        }

        .toolbar-stats span {
            padding: 8px 12px;
            background: rgba(181, 0, 0, 0.15);
            border-radius: 8px;
        }

        .application-info {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 25px;
        }

        .info-card {
            background: #252525;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 16px 18px;
        }

        .info-label {
            display: block;
            color: #999;
            font-size: 13px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .info-value {
            color: #fff;
            font-size: 17px;
            font-weight: 500;
        }

        .info-link {
            color: #4da3ff;
            text-decoration: none;
        }

        .info-link:hover {
            text-decoration: underline;
        }

        .message-box {
            background: #111;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 15px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .application-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media(max-width:700px){
            .application-grid{
                grid-template-columns:1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="admin-header">
                <h1>Заявки на вступление</h1>
            </div>
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background: rgba(76, 175, 80, 0.2); color: #4caf50; padding: 10px; border-radius: 6px; margin-bottom: 20px;">
                    ✅ Статус заявки обновлен
                </div>
            <?php endif; ?>

            <!-- ФИЛЬТР -->
            <div class="applications-toolbar">
                <div class="toolbar-left">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="🔍 Поиск по имени, email или телефону..."
                    >

                    <select id="statusFilter">
                        <option value="all">Все статусы</option>
                        <option value="новая">Новые</option>
                        <option value="обработана">Обработанные</option>
                        <option value="отклонена">Отклонённые</option>
                    </select>

                    <a href="export_applications.php" class="btn-export">
                        📥 Экспорт Excel
                    </a>
                </div>

                <div class="toolbar-stats">
                    <span>Всего: <?= count($applications) ?></span>
                    <span>Новых: <?= count(array_filter($applications, function($app) { return $app['status'] === 'новая'; })) ?></span>
                </div>
            </div>
            
            <div class="admin-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>Сообщение</th>
                            <th>Дата</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $index=>$application): ?>
                        <tr 
                            class="application-row"
                            data-name="<?= htmlspecialchars(mb_strtolower($application['name'])) ?>"
                            data-email="<?= htmlspecialchars(mb_strtolower($application['email'])) ?>"
                            data-phone="<?= htmlspecialchars($application['phone'] ?? '') ?>"
                            data-status="<?= htmlspecialchars($application['status']) ?>"
                        >
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($application['name']) ?></td>
                            <td><?= htmlspecialchars($application['email']) ?></td>
                            <td><?= htmlspecialchars($application['phone'] ?? '-') ?></td>
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
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Удалить заявку?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $application['id'] ?>">
                                    <button type="submit" class="btn-action btn-delete">🗑️</button>
                                </form>
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
        function escapeHtml(text) {
            return String(text ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function normalizePhone(phone) {
            let cleaned = String(phone ?? '').replace(/\D/g, '');

            if (cleaned.length === 11 && cleaned.startsWith('8')) {
                cleaned = '7' + cleaned.slice(1);
            }

            return cleaned;
        }

        function viewApplication(id) {
            fetch(`../api/get_application.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('applicationId').textContent = data.id;
                    document.getElementById('applicationDetails').innerHTML = `
                    <div class="application-info">

                        <div class="application-grid">

                            <div class="info-card">
                                <span class="info-label">👤 Имя</span>
                                <div class="info-value">${escapeHtml(data.name)}</div>
                            </div>

                            <div class="info-card">
                                <span class="info-label">📧 Email</span>
                                <div class="info-value">
                                    <a class="info-link"
                                    href="mailto:${escapeHtml(data.email)}">
                                        ${escapeHtml(data.email)}
                                    </a>
                                </div>
                            </div>

                            <div class="info-card">
                                <span class="info-label">📱 WhatsApp</span>
                                <div class="info-value">
                                    ${
                                        data.phone
                                        ? `<a class="info-link"
                                            target="_blank"
                                            href="https://api.whatsapp.com/send?phone=${normalizePhone(data.phone)}">
                                            ${escapeHtml(data.phone)}
                                        </a>`
                                        : '-'
                                    }
                                </div>
                            </div>

                            <div class="info-card">
                                <span class="info-label">🕒 Отправлено</span>
                                <div class="info-value">
                                    ${new Date(data.created_at).toLocaleString('ru-RU')}
                                </div>
                            </div>

                            <div class="info-card">
                                <span class="info-label">🌐 IP</span>
                                <div class="info-value">
                                    ${escapeHtml(data.ip_address)}
                                </div>
                            </div>

                            <div class="info-card">
                                <span class="info-label">📌 Статус</span>

                                <form method="POST">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="id" value="${data.id}">

                                    <select
                                        name="status"
                                        class="status-select"
                                        onchange="this.form.submit()">

                                        <option value="новая"
                                            ${data.status==='новая'?'selected':''}>
                                            Новая
                                        </option>

                                        <option value="обработана"
                                            ${data.status==='обработана'?'selected':''}>
                                            Обработана
                                        </option>

                                        <option value="отклонена"
                                            ${data.status==='отклонена'?'selected':''}>
                                            Отклонена
                                        </option>

                                    </select>

                                </form>

                            </div>

                        </div>

                        <div class="info-card">
                            <span class="info-label">💬 Сообщение</span>

                            <div class="message-box">
                                ${escapeHtml(data.message)}
                            </div>

                        </div>

                    </div>
                    `;
                    
                    document.getElementById('applicationModal').style.display = 'flex';
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

        // FILTER
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const rows = document.querySelectorAll('.application-row');

        function filterApplications() {
            const search = searchInput.value.toLowerCase().trim();
            const status = statusFilter.value;

            rows.forEach(row => {
                const name = row.dataset.name || '';
                const email = row.dataset.email || '';
                const phone = row.dataset.phone || '';
                const rowStatus = row.dataset.status || '';

                const matchesSearch =
                    name.includes(search) ||
                    email.includes(search) ||
                    phone.includes(search);

                const matchesStatus =
                    status === 'all' || rowStatus === status;

                row.style.display = matchesSearch && matchesStatus ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterApplications);
        statusFilter.addEventListener('change', filterApplications);
    </script>
</body>
</html>