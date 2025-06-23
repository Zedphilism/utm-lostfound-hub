<?php
ob_start(); // Pastikan tiada output sebelum header
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/cloudinary.php';
require __DIR__ . '/../config/vision_helper.php';

use Cloudinary\Api\Upload\UploadApi;

session_start();

$success = false;
$error = '';
$vision_labels = '';

// Debug: pastikan fungsi wujud
if (!function_exists('getVisionLabels')) {
    error_log("❌ Function getVisionLabels() not found!");
    $error = 'Internal error: Vision function not found.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $item_name   = trim($_POST['item_name'] ?? '');
    $type        = $_POST['type'] ?? 'lost';
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter'] ?? 'Anonymous');
    $image_path  = '';

    if (!empty($_FILES['image']['tmp_name'])) {
        try {
            $result = (new UploadApi())->upload($_FILES['image']['tmp_name']);
            $image_path = $result['secure_url'];

            $tempImage = tempnam(sys_get_temp_dir(), 'vision_');
            file_put_contents($tempImage, file_get_contents($image_path));

            // Pastikan fungsi vision berfungsi
            if (function_exists('getVisionLabels')) {
                $vision_labels = getVisionLabels($tempImage);
                error_log("🧠 Vision Labels Debug: " . $vision_labels);
            }

            unlink($tempImage);
        } catch (Exception $e) {
            $error = 'Image processing failed: ' . $e->getMessage();
        }
    }

    if (!$error) {
        $stmt = $mysqli->prepare(
            "INSERT INTO reports (item_name, type, location, description, reporter, image_path, vision_labels, submitted_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'public')"
        );
        $stmt->bind_param('sssssss', $item_name, $type, $location, $description, $reporter, $image_path, $vision_labels);
        $stmt->execute();
        $success = true;
    }
}
ob_end_flush(); // Habis output buffering
?>
