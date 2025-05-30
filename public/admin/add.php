<?php
require __DIR__ . '/../../config/config.php';
require __DIR__ . '/../../config/cloudinary.php'; // connects to Cloudinary
use Cloudinary\Api\Upload\UploadApi;               // enables the upload method

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

    // Handle file upload
    $image_path = '';
if (!empty($_FILES['image']['tmp_name'])) {
    try {
        $result = (new UploadApi())->upload($_FILES['image']['tmp_name']);
        $image_path = $result['secure_url']; // Save the full Cloudinary URL
    } catch (Exception $e) {
        $error = 'Cloudinary upload failed: ' . $e->getMessage();
    }
}


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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-xl mx-auto mt-10 bg-white shadow p-6 rounded">

  <h1 class="text-xl font-semibold mb-4">Add New Report</h1>

  <?php if ($success): ?>
    <p class="text-green-600 mb-4">Report submitted successfully.</p>
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
      <label class="block text-sm font-medium">Reporter Name</label>
      <input type="text" name="reporter" value="Admin" class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Upload Image</label>
      <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-700">
    </div>
    <div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
      <a href="/admin/list.php" class="ml-4 text-sm text-blue-600 hover:underline">← Back to List</a>
    </div>
  </form>
</div>
</body>
</html>
