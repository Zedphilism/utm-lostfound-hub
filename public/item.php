<?php
require __DIR__ . '/../config/config.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "No item ID provided.";
    exit;
}

// Get item data
$stmt = $mysqli->prepare("SELECT * FROM reports WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "Item not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($item['item_name']) ?> - Lost & Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-xl mx-auto p-6 bg-white mt-10 rounded shadow">
    <h1 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($item['item_name']) ?></h1>

    <div class="space-y-2 text-sm">
      <p><strong>Type:</strong> <?= htmlspecialchars($item['type']) ?></p>
      <p><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
      <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
      <p><strong>Reported By:</strong> <?= htmlspecialchars($item['reporter']) ?></p>
      <p><strong>Status:</strong> <?= htmlspecialchars($item['status']) ?></p>
      <p><strong>Date Reported:</strong> <?= htmlspecialchars($item['date_reported']) ?></p>
    </div>

    <?php if (!empty($item['image_path'])): ?>
      <div class="mt-4">
        <img src="/uploads/<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" class="w-full max-w-xs rounded">
      </div>
    <?php else: ?>
      <p class="mt-4 italic text-gray-400">No image uploaded.</p>
    <?php endif; ?>

    <div class="mt-6">
      <a href="/admin/list.php" class="text-blue-600 hover:underline text-sm">&larr; Back to List</a>
    </div>
  </div>
</body>
</html>
