<?php
require __DIR__ . '/../../config/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
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
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="flex justify-between items-center px-6 pt-4">
  <a href="/admin/index.php" class="text-sm text-blue-600 hover:underline">← Back to Dashboard</a>
  <a href="/index.php" class="text-sm text-blue-600 hover:underline">Public View →</a>
</div>

<!-- Admin Table -->
<div class="bg-blue-600 text-white p-4 mt-4">
  <h1 class="text-xl font-semibold">Admin Reports</h1>
</div>
<div class="overflow-x-auto px-6 mt-4">
  <table class="min-w-full bg-white shadow border rounded">
    <thead class="bg-gray-200 text-left text-sm text-gray-700">
      <tr>
        <th class="p-2">ID</th><th>Item</th><th>Type</th><th>Location</th>
        <th>Date</th><th>Status</th><th>Reporter</th><th>Image</th><th>Actions</th>
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
        <td class="p-2">
          <select
            class="border rounded px-2 py-1 text-sm status-dropdown"
            data-id="<?= $row['id'] ?>"
          >
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
        <td class="p-2 flex space-x-2">
          <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline text-sm">Edit</a>
          <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600 hover:underline text-sm" onclick="return confirm('Are you sure?')">Delete</a>
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
<div class="overflow-x-auto px-6 mt-4 mb-10">
  <table class="min-w-full bg-white shadow border rounded">
    <thead class="bg-gray-200 text-left text-sm text-gray-700">
      <tr>
        <th class="p-2">ID</th><th>Item</th><th>Type</th><th>Location</th>
        <th>Date</th><th>Status</th><th>Reporter</th><th>Image</th>
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
        <td class="p-2"><?= ucfirst($row['status']) ?></td>
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

<!-- AJAX Script -->
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
