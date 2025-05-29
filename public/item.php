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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 bg-white rounded shadow mt-6">
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
        <img src="/<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" class="w-full max-w-xs rounded mx-auto">
      </div>
    <?php else: ?>
      <p class="mt-4 italic text-gray-400">No image uploaded.</p>
    <?php endif; ?>

    <div class="mt-6 text-center">
      <a href="index.php" class="text-blue-600 hover:underline text-sm">&larr; Back to All Reports</a>
    </div>
  </div>
</body>
</html>
