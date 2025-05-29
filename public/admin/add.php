<?php
require __DIR__ . '/../../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name   = trim($_POST['item_name'] ?? '');
    $type        = $_POST['type'] ?? 'lost';
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter'] ?? 'Admin');
    $image_path  = '';

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = 'uploads/';
        if (!is_dir(__DIR__ . '/' . $target_dir)) {
            mkdir(__DIR__ . '/' . $target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/' . $target_file)) {
            $image_path = $target_file;
        }
    }

    // Save to DB
    $stmt = $mysqli->prepare("INSERT INTO reports (
        item_name, type, location, description, image_path, date_reported, status, reporter, submitted_by
    ) VALUES (?, ?, ?, ?, ?, NOW(), 'pending', ?, 'admin')");
    $stmt->bind_param("ssssss", $item_name, $type, $location, $description, $image_path, $reporter);
    $stmt->execute();
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Lost/Found Item</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

  <!-- Nav -->
  <div class="flex justify-between items-center px-6 pt-4">
    <a href="/admin/index.php" class="text-sm text-blue-600 hover:underline">
      ← Back to Dashboard
    </a>
    <a href="/index.php" class="text-sm text-blue-600 hover:underline">
      Public View →
    </a>
  </div>

  <div class="bg-blue-600 text-white p-4 mt-4">
    <h1 class="text-xl font-semibold">Add Lost/Found Item</h1>
  </div>

  <div class="max-w-xl mx-auto mt-6 bg-white p-6 shadow rounded">
    <?php if ($success): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        ✅ Report submitted successfully.
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Item Name</label>
        <input type="text" name="item_name" class="w-full border p-2 rounded" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Type</label>
        <select name="type" class="w-full border p-2 rounded">
          <option value="lost">Lost</option>
          <option value="found">Found</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Location</label>
        <input type="text" name="location" class="w-full border p-2 rounded" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Description</label>
        <textarea name="description" class="w-full border p-2 rounded" rows="4"></textarea>
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Reporter</label>
        <input type="text" name="reporter" class="w-full border p-2 rounded" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Image</label>
        <input type="file" name="image" class="w-full" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">
        Submit Report
      </button>
    </form>
  </div>
</body>
</html>
