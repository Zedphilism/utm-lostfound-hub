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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="max-w-xl mx-auto p-6 bg-white mt-10 rounded shadow text-sm md:text-base">
  <h1 class="text-2xl font-semibold mb-4"><?= htmlspecialchars($item['item_name']) ?></h1>
  <p><strong>Type:</strong> <?= htmlspecialchars($item['type']) ?></p>
  <p><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
  <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p>
  <p><strong>Reported By:</strong> <?= htmlspecialchars($item['reporter']) ?></p>
  <p><strong>Status:</strong> <?= htmlspecialchars($item['status']) ?></p>
  <p><strong>Date Reported:</strong> <?= $item['date_reported'] ?></p>

  <?php if (!empty($item['image_path'])): ?>
    <img src="/uploads/<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" class="w-40 h-auto mx-auto mt-4" />
  <?php else: ?>
    <p class="italic text-gray-400 mt-4 text-center">No image available</p>
  <?php endif; ?>

  <div class="mt-6 text-center">
    <a href="/admin/list.php" class="text-blue-600 hover:underline">← Back to All Reports</a>
  </div>
</div>

</body>
</html>
