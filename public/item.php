<?php
// File: public/item.php
require __DIR__ . '/../config/config.php';

// 1) Validate & get the ID
$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    header('Location: index.php');
    exit;
}

// 2) Fetch the report (including image_path)
$sql = "
  SELECT
    item_name,
    type,
    location,
    reporter,
    DATE_FORMAT(date_reported, '%Y-%m-%d %H:%i') AS date_reported,
    description,
    status,
    image_path
  FROM reports
  WHERE id = ?
  LIMIT 1
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result(
  $item_name,
  $type,
  $location,
  $reporter,
  $date_reported,
  $description,
  $status,
  $image_path
);
if (!$stmt->fetch()) {
  // no such report → back to list
  header('Location: index.php');
  exit;
}
$stmt->close();

// 3) Render
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<div class="container mx-auto px-4 py-6 md:flex md:space-x-8">
  <!-- Left column: text details -->
  <div class="md:w-2/3 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($item_name) ?></h1>
    <p><strong>Type:</strong> <?= htmlspecialchars(ucfirst($type)) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($location) ?></p>
    <p><strong>Reporter:</strong> <?= htmlspecialchars($reporter) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($date_reported) ?></p>

    <?php if ($description): ?>
      <div class="mt-4">
        <strong>Description:</strong>
        <p class="mt-1"><?= nl2br(htmlspecialchars($description)) ?></p>
      </div>
    <?php endif; ?>

    <p class="mt-4"><strong>Status:</strong> <?= htmlspecialchars(ucfirst($status)) ?></p>

    <div class="mt-6">
      <a href="index.php" class="text-blue-600 hover:underline">← Back to list</a>
    </div>
  </div>

  <!-- Right column: uploaded image -->
  <div class="md:w-1/3 mt-6 md:mt-0">
    <?php if ($image_path): ?>
      <img
        src="../<?= htmlspecialchars($image_path) ?>"
        alt="Uploaded item"
        class="w-full rounded shadow"
      >
    <?php else: ?>
      <div class="border border-dashed border-gray-300 h-48 flex items-center justify-center text-gray-500">
        No image uploaded
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
