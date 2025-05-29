<?php
// File: public/item.php
require __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "Invalid request.";
    exit;
}

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
  <title>Item Details</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-2xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($item['item_name']) ?></h1>
    <p><strong>Type:</strong> <?= $item['type'] ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($item['location']) ?></p>
    <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($item['description'])) ?></p>
    <p><strong>Reported By:</strong> <?= htmlspecialchars($item['reporter']) ?></p>
    <p><strong>Status:</strong> <?= ucfirst(str_replace('_', ' ', $item['status'])) ?></p>
    <p><strong>Date Reported:</strong> <?= date('Y-m-d H:i', strtotime($item['date_reported'])) ?></p>

    <?php if (!empty($item['image_path'])): ?>
      <div class="mt-4">
        <img src="/public/<?= $item['image_path'] ?>" alt="Item Image" class="w-full max-w-xs rounded shadow">
      </div>
    <?php endif; ?>

    <a href="/public/index.php" class="inline-block mt-6 text-blue-600 hover:underline">← Back to List</a>
  </div>
</body>
</html>
