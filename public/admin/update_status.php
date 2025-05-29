<?php
require __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? '';

    if ($id && in_array($status, ['pending', 'claimed', 'resolved'])) {
        $stmt = $mysqli->prepare("UPDATE reports SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
    }
}

header("Location: /admin/list.php");
exit;
