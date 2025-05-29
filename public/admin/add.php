<?php
require __DIR__ . '/../../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name   = trim($_POST['item_name'] ?? '');
    $type        = $_POST['type'] ?? 'lost';
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter'] ?? 'Admin');
    $image_path  = '';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $filename = basename($_FILES['image']['name']);
        $target_dir = __DIR__ . '/../uploads/';
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $filename;
        } else {
            $error = 'Image upload failed.';
        }
    }

    // Insert into database
    if (!$error) {
        $stmt = $mysqli->prepare(
            "INSERT INTO reports (item_name, type, location, description, reporter, image_path, submitted_by)
             VALUES (?, ?, ?, ?, ?, ?, 'admin')"
        );
        $stmt->bind_param('ssssss', $item_name, $type, $location, $description, $reporter, $image_path);
        $stmt->execute();
        $success = true;
    }
}
?>
