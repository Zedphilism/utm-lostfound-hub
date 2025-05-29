<?php
require __DIR__ . '/../../config/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

// Fetch admin reports
$sqlAdmin = "
    SELECT
        id, item_name, type, location,
        DATE_FORMAT(date_reported, '%Y-%m-%d') AS date_reported,
        status, reporter, image_path
    FROM reports
    WHERE submitted_by = 'admin'
    ORDER BY date_reported DESC
";
$stmtA = $mysqli->prepare($sqlAdmin);
$stmtA->execute();
$adminReports = $stmtA->get_result();

// Fetch public reports
$sqlPublic = "
    SELECT
        id, item_name, type, location,
        DATE_FORMAT(date_reported, '%Y-%m-%d') AS date_reported,
        status, reporter, image_path
    FROM reports
    WHERE submitted_by = 'public'
    ORDER BY date_reported DESC
";
$stmtP = $mysqli->prepare($sqlPublic);
$stmtP->execute();
$publicReports = $stmtP->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Reports</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

  <!-- Nav buttons -->
  <div class="flex justify-between items-center px-6 pt-4">
    <a href="/admin/index.php" class="text-sm text-blue-600 hover:underline">
      ← Back to Dashboard
    </a>
    <a href="/index.php" class="text-sm text-blue-600 hover:underline">
      Public View →
    </a>
  </div>

  <!-- Admin Reports Section -->
  <div class="bg-blue-600 text-white p-4 mt-4">
    <h1 class="text-xl font-semibold">Admin Reports</h1>
  </div>

  <div class="overflow-x-auto px-6 mt-4">
    <table class="min-w-full bg-white shadow border rounded">
      <thead>
        <tr class="bg-gray-200 text-left text-sm text-gray-700">
          <th class="p-2">ID</th>
          <th class="p-2">Item</th>
          <th class="p-2">Type</th>
          <th class="p-2">Location</th>
          <th class="p-2">Date</th>
          <th class="p-2">Status</th>
          <th class="p-2">Reporter</th>
          <th class="p-2">Image</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $adminReports->fetch_assoc()): ?>
        <tr class="border-t text-sm">
          <td class="p-2"><?= $row['id'] ?></td>
          <td class="p-2"><?= htmlspecialchars($row['item_name']) ?></td>
          <td class="p-2"><?= ucfirst($row['type']) ?></td>
          <td class="p-2"><?= htmlspecialchars($row['location']) ?></td>
          <td class="p-2"><?= $row['date_reported'] ?></td>
          <td class="p-2"><?= ucfirst(str_replace('_', ' ', $row['status'])) ?></td>
          <td class="p-2"><?= htmlspecialchars($row['reporter']) ?></td>
          <td class="p-2">
            <?php if (!empty($row['image_path'])): ?>
              <img src="/uploads/<?= htmlspecialchars($row['image_path']) ?>" class="w-16 h-auto rounded" />
            <?php else: ?>
              <span class="text-gray-400 italic">No image</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Public Reports Section -->
  <div class="bg-green-600 text-white p-4 mt-10">
    <h1 class="text-xl font-semibold">Public Reports</h1>
  </div>

  <div class="overflow-x-auto px-6 mt-4 mb-10">
    <table class="min-w-full bg-white shadow border rounded">
      <thead>
        <tr class="bg-gray-200 text-left text-sm text-gray-700">
          <th class="p-2">ID</th>
          <th class="p-2">Item</th>
          <th class="p-2">Type</th>
          <th class="p-2">Location</th>
          <th class="p-2">Date</th>
          <th class="p-2">Status</th>
          <th class="p-2">Reporter</th>
          <th class="p-2">Image</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $publicReports->fetch_assoc()): ?>
        <tr class="border-t text-sm">
          <td class="p-2"><?= $row['id'] ?></td>
          <td class="p-2"><?= htmlspecialchars($row['item_name']) ?></td>
          <td class="p-2"><?= ucfirst($row['type']) ?></td>
          <td class="p-2"><?= htmlspecialchars($row['location']) ?></td>
          <td class="p-2"><?= $row['date_reported'] ?></td>
          <td class="p-2"><?= ucfirst(str_replace('_', ' ', $row['status'])) ?></td>
          <td class="p-2"><?= htmlspecialchars($row['reporter']) ?></td>
          <td class="p-2">
            <?php if (!empty($row['image_path'])): ?>
              <img src="/uploads/<?= htmlspecialchars($row['image_path']) ?>" class="w-16 h-auto rounded" />
            <?php else: ?>
              <span class="text-gray-400 italic">No image</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
