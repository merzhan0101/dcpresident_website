<?php
session_start();
require_once '../php/functions.php';
requireAdmin();

$sql = "SELECT id, name, email, phone, subject, message, status, ip_address, created_at
        FROM applications
        ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = "applications_" . date('Y-m-d') . ".xls";

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

echo "\xEF\xBB\xBF";
?>

<table border="1">
    <thead>
        <tr>
            <th>№</th>
            <th>Дата</th>
            <th>Имя</th>
            <th>Email</th>
            <th>Телефон</th>
            <th>Тема</th>
            <th>Сообщение</th>
            <th>Статус</th>
            <th>IP адрес</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($applications as $index => $app): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($app['created_at']) ?></td>
                <td><?= htmlspecialchars($app['name']) ?></td>
                <td><?= htmlspecialchars($app['email']) ?></td>
                <td><?= htmlspecialchars($app['phone'] ?? '') ?></td>
                <td><?= htmlspecialchars($app['subject'] ?? '') ?></td>
                <td><?= nl2br(htmlspecialchars($app['message'])) ?></td>
                <td><?= htmlspecialchars($app['status']) ?></td>
                <td><?= htmlspecialchars($app['ip_address'] ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>