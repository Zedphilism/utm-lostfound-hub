<?php
require __DIR__ . '/../../config/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $mysqli->prepare("SELECT * FROM reports WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $type = $_POST['type'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $reporter = $_POST['reporter'] ?? '';
    $status = $_POST['status'] ?? '';

    $image_path = $item['image_path'];

    // If new image uploaded, replace
    if (!empty($_FILES['image']['name'])) {
        $upload_dir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $file_name;
        }
    }

    $stmt = $mysqli->prepare("
        UPDATE reports
        SET item_name=?, type=?, location=?, description=?, reporter=?, status=?, image_path=?
        WHERE id=?
    ");
    $stmt->bind_param('sssssssi', $item_name, $type, $location, $description, $reporter, $status, $image_path, $id);
    $stmt->execute();

    header('Location: /admin/list.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Item</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Edit Item</h1>
    <form method="post" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block mb-1">Item Name</label>
        <input type="text" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>" class="w-full border p-2 rounded" required>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Type</label>
        <select name="type" class="w-full border p-2 rounded" required>
          <option value="lost" <?= $item['type']=='lost'?'selected':'' ?>>Lost</option>
          <option value="found" <?= $item['type']=='found'?'selected':'' ?>>Found</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($item['location']) ?>" class="w-full border p-2 rounded" required>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Description</label>
        <textarea name="description" class="w-full border p-2 rounded"><?= htmlspecialchars($item['description']) ?></textarea>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Reporter</label>
        <input type="text" name="reporter" value="<?= htmlspecialchars($item['reporter']) ?>" class="w-full border p-2 rounded">
      </div>
      <div class="mb-4">
        <label class="block mb-1">Status</label>
        <select name="status" class="w-full border p-2 rounded" required>
          <option value="pending" <?= $item['status']=='pending'?'selected':'' ?>>Pending</option>
          <option value="in_review" <?= $item['status']=='in_review'?'selected':'' ?>>In Review</option>
          <option value="resolved" <?= $item['status']=='resolved'?'selected':'' ?>>Resolved</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Replace Image (optional)</label>
        <input type="file" name="image" class="w-full">
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
  </div>
</body>
</html>
