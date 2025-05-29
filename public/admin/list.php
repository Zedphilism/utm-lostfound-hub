<?php
require __DIR__ . '/../config/config.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 1) Admin-submitted
$sqlA = "
  SELECT id, item_name, type, location,
         DATE_FORMAT(date_reported, '%Y-%m-%d %H:%i:%s') AS date_reported,
         reporter, status, image_path
    FROM reports
   WHERE submitted_by = 'admin'
   ORDER BY date_reported DESC
";
$stmtA = $mysqli->prepare($sqlA);
$stmtA->execute();
$resA = $stmtA->get_result();

// 2) Public-submitted
$sqlP = "
  SELECT id, item_name, type, location,
         DATE_FORMAT(date_reported, '%Y-%m-%d %H:%i:%s') AS date_reported,
         reporter, status, image_path
    FROM reports
   WHERE submitted_by = 'public'
   ORDER BY date_reported DESC
";
$stmtP = $mysqli->prepare($sqlP);
$stmtP->execute();
$resP = $stmtP->get_result();

$pageTitle = 'All Reports';
include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  <div class="mb-4">
    <a href="index.php" class="text-blue-600 hover:underline text-sm">&larr; Back to Dashboard</a>
    <a href="/index.php" class="float-right text-blue-600 hover:underline text-sm">Public View →</a>
  </div>

  <h2 class="text-xl font-bold mb-2 bg-blue-600 text-white px-4 py-2 rounded">Admin Reports</h2>
  <div class="overflow-x-auto">
    <table class="w-full text-sm border-collapse mb-8">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">ID</th>
          <th class="p-2">Item</th>
          <th class="p-2">Type</th>
          <th class="p-2">Location</th>
          <th class="p-2">Date</th>
          <th class="p-2">Status</th>
          <th class="p-2">Reporter</th>
          <th class="p-2">Image</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $resA->fetch_assoc()): ?>
        <tr class="border-t">
          <td class="p-2"><?= $row['id'] ?></td>
          <td class="p-2"><?= htmlspecialchars($row['item_name']) ?></td>
          <td class="p-2"><?= ucfirst($row['type']) ?></td>
          <td class="p-2"><?= htmlspecialchars($row['location']) ?></td>
          <td class="p-2"><?= $row['date_reported'] ?></td>
          <td class="p-2">
            <form method="post" action="update_status.php" class="inline">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <select name="status" onchange="this.form.submit()" class="border p-1 rounded">
                <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_review" <?= $row['status'] == 'in_review' ? 'selected' : '' ?>>In Review</option>
                <option value="resolved" <?= $row['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
              </select>
            </form>
          </td>
          <td class="p-2"><?= htmlspecialchars($row['reporter']) ?></td>
          <td class="p-2">
            <?php if (!empty($row['image_path'])): ?>
              <img src="/<?= htmlspecialchars($row['image_path']) ?>" alt="Image" class="h-12 rounded">
            <?php else: ?>
              <span class="italic text-gray-400">No image</span>
            <?php endif; ?>
          </td>
          <td class="p-2">
            <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600">Edit</a>
            <span class="mx-1">|</span>
            <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <h2 class="text-xl font-bold mb-2 bg-green-600 text-white px-4 py-2 rounded">Public Reports</h2>
  <div class="overflow-x-auto">
    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="bg-gray-100 text-left">
          <th class="p-2">ID</th>
          <th class="p-2">Item</th>
          <th class="p-2">Type</th>
          <th class="p-2">Location</th>
          <th class="p-2">Date</th>
          <th class="p-2">Status</th>
          <th class="p-2">Reporter</th>
          <th class="p-2">Image</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $resP->fetch_assoc()): ?>
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
              <img src="/<?= htmlspecialchars($row['image_path']) ?>" alt="Image" class="h-12 rounded">
            <?php else: ?>
              <span class="italic text-gray-400">No image</span>
            <?php endif; ?>
          </td>
          <td class="p-2">
            <a href="edit.php?id=<?= $row['id'] ?>" class="text-blue-600">Edit</a>
            <span class="mx-1">|</span>
            <a href="delete.php?id=<?= $row['id'] ?>" class="text-red-600" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
