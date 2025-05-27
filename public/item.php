<?php
require __DIR__ . '/../config/config.php';

// Get item ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

// Fetch report
$stmt = $mysqli->prepare("SELECT * FROM reports WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) {
    header('Location: index.php');
    exit;
}

$pageTitle = 'Item: ' . $row['item_name'];
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<div class="container mx-auto px-4">
  <article class="bg-white shadow rounded p-6">
    <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($row['item_name']) ?></h1>
    <p><strong>Type:</strong> <?= ucfirst($row['type']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
    <p><strong>Reporter:</strong> <?= htmlspecialchars($row['reporter']) ?></p>
    <p><strong>Date:</strong> <?= date('Y-m-d H:i', strtotime($row['date_reported'])) ?></p>
    <p class="mt-4">
      <strong>Description:</strong><br>
      <?= nl2br(htmlspecialchars($row['description'])) ?>
    </p>
    <p class="mt-4">
      <strong>Status:</strong>
      <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
    </p>

    <a href="index.php" class="text-blue-600 mt-4 inline-block">← Back to list</a>
  </article>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
