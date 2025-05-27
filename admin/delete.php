<?php
require __DIR__ . '/../config/config.php';

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $mysqli->prepare("DELETE FROM reports WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

// Redirect back to the reports list
header('Location: list.php');
exit;
