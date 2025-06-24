<?php
// ✅ Elakkan warning header: mula buffer awal
ob_start();

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/cloudinary.php';

$visionHelperPath = __DIR__ . '/../config/vision_helper.php';
if (!file_exists($visionHelperPath)) {
    file_put_contents(__DIR__ . '/../config/vision_log.txt', "❌ vision_helper.php not found\n", FILE_APPEND);
    die(); // Jangan echo supaya header belum dihantar
}
require $visionHelperPath;

use Cloudinary\Api\Upload\UploadApi;

// ✅ Mula session selepas semua require & sebelum ada echo/output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name     = trim($_POST['item_name'] ?? '');
    $type          = $_POST['type'] ?? 'lost';
    $location      = trim($_POST['location'] ?? '');
    $description   = trim($_POST['description'] ?? '');
    $reporter      = trim($_POST['reporter'] ?? 'Anonymous');
    $image_path    = '';
    $vision_labels = '';

    try {
        if (!empty($_FILES['image']['tmp_name'])) {
            $result = (new UploadApi())->upload($_FILES['image']['tmp_name']);
            $image_path = $result['secure_url'];

            // Sementara fail
            $tempImage = tempnam(sys_get_temp_dir(), 'vision_');
            file_put_contents($tempImage, file_get_contents($image_path));

            // Vision API
            if (function_exists('getVisionLabels')) {
                $vision_labels = getVisionLabels($tempImage);
            } else {
                $vision_labels = '❌ getVisionLabels() not defined';
            }

            unlink($tempImage);
        }

        // Simpan ke DB
        $stmt = $mysqli->prepare(
            "INSERT INTO reports (item_name, type, location, description, reporter, image_path, vision_labels, submitted_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'public')"
        );
        $stmt->bind_param('sssssss', $item_name, $type, $location, $description, $reporter, $image_path, $vision_labels);
        $stmt->execute();
        $success = true;

    } catch (Exception $e) {
        $error = '❌ Error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report Lost/Found Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-xl mx-auto mt-10 bg-white shadow p-6 rounded">
  <h1 class="text-xl font-semibold mb-4">Report a Lost/Found Item</h1>

  <?php if ($success): ?>
    <p class="text-green-600 mb-4">✅ Your report has been submitted successfully.</p>
  <?php elseif ($error): ?>
    <p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Item Name</label>
      <input type="text" name="item_name" required class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Type</label>
      <select name="type" class="w-full border px-3 py-2 rounded">
        <option value="lost">Lost</option>
        <option value="found">Found</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Location</label>
      <input type="text" name="location" required class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Description</label>
      <textarea name="description" rows="3" class="w-full border px-3 py-2 rounded"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium">Your Name</label>
      <input type="text" name="reporter" placeholder="Optional" class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Upload Image</label>
      <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-700">
    </div>
    <div>
      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit Report</button>
      <a href="/index.php" class="ml-4 text-sm text-blue-600 hover:underline">← Back to Home</a>
    </div>
  </form>
</div>
</body>
</html>
