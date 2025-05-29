<?php
// File: admin/delete.php
require __DIR__ . '/../../config/config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('Location: /admin/list.php');
exit;
