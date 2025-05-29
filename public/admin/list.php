<?php
// File: admin/list.php
require __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch admin reports
$sqlA = "SELECT * FROM reports WHERE submitted_by = 'admin' ORDER BY date_reported DESC";
$stmtA = $mysqli->prepare($sqlA);
$stmtA->execute();
$adminReports = $stmtA->get_result();

// Fetch public reports
$sqlP = "SELECT * FROM reports WHERE submitted_by = 'public' ORDER BY date_reported DESC";
$stmtP = $mysqli->prepare($sqlP);
$stmtP->execute();
$publicReports = $stmtP->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>All Reports</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="flex flex-wrap justify-between items-center px-4 pt-4 text-sm">
  <a href="/admin/index.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
  <a href="/index.php" class="text-blue-600 hover:underline">Public View →</a>
</div>

<!-- Admin Table -->
<div class="bg-blue-600 text-white p-4 mt-4">
  <h1 class="text-xl font-semibold">Admin Reports</h1>
</div>
<div class="overflow-x-auto px-4 mt-4">
  <table class="min-w-full bg-white shadow border rounded text-sm">
    <thead class="bg-gray-200 text-left text-gray-700">
      <tr>
        <th class="p-2">ID</th><th>Item</th><th>Type</th><th>Location</th>
        <th>Date</th><th>Status</th><th>Reporter</th><th>Image</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $adminReports->fetch_assoc()): ?>
      <tr class="border-t">
        <td class="p-2"><?= $row['id'] ?></td>
        <td class="p-2"><?= htmlspecialchars($row['item_name']) ?></td>
        <td class="p-2"><?= ucfirst($row['type']) ?></td>
        <td class="p-2"><?= htmlspecialchars($row['location']) ?></td>
        <td class="p-2"><?= $row['date_reported'] ?></td>
        <td class="p-2">
          <select class="border rounded px-2 py-1 status-dropdown" data-id="<?= $row['id'] ?>">
            <option value="pending" <?= $row['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="claimed" <?= $row['status'] === 'claimed' ? 'selected' : '' ?>>Claimed</option>
            <option value="resolved" <?= $row['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
          </select>
        </td>
        <td class="p-2"><?= htmlspecialchars($row['reporter']) ?></td>
        <td class="p-2">
          <?php if (!empty($row['image_path'])): ?>
            <img src="/uploads/<?= htmlspecialchars($row['image_path']) ?>" class="w-16 h-auto rounded" />
          <?php else: ?>
            <span class="text-gray-400 italic">No image</span>
          <?php endif; ?>
        </td>
        <td class="p-2 flex gap-2">
          <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
          <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Public Table -->
<div class="bg-green-600 text-white p-4 mt-10">
  <h1 class="text-xl font-semibold">Public Reports</h1>
</div>
<div class="overflow-x-auto px-4 mt-4 mb-10">
  <table class="min-w-full bg-white shadow border rounded text-sm">
    <thead class="bg-gray-200 text-left text-gray-700">
      <tr>
        <th class="p-2">ID</th><th>Item</th><th>Type</th><th>Location</th>
        <th>Date</th><th>Status</th><th>Reporter</th><th>Image</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $publicReports->fetch_assoc()): ?>
      <tr class="border-t">
        <td class="p-2"><?= $row['id'] ?></td>
        <td class="p-2"><?= htmlspecialchars($row['item_name']) ?></td>
        <td class="p-2"><?= ucfirst($row['type']) ?></td>
        <td class="p-2"><?= htmlspecialchars($row['location']) ?></td>
        <td class="p-2"><?= $row['date_reported'] ?></td>
        <td class="p-2"><?= ucfirst($row['status']) ?></td>
        <td class="p-2"><?= htmlspecialchars($row['reporter']) ?></td>
        <td class="p-2">
          <?php if (!empty($row['image_path'])): ?>
            <img src="/uploads/<?= htmlspecialchars($row['image_path']) ?>" class="w-16 h-auto rounded" />
          <?php else: ?>
            <span class="text-gray-400 italic">No image</span>
          <?php endif; ?>
        </td>
        <td class="p-2 flex gap-2">
          <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
          <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<script>
  document.querySelectorAll('.status-dropdown').forEach(select => {
    select.addEventListener('change', function () {
      const id = this.dataset.id;
      const status = this.value;

      fetch('/admin/update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&status=${status}`
      })
      .then(res => {
        if (res.ok) {
          alert('Status updated!');
        } else {
          alert('Failed to update status.');
        }
      });
    });
  });
</script>

</body>
</html>
