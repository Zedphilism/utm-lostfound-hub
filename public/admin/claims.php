<?php
require __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claim_id = $_POST['claim_id'];
    $action = $_POST['action'];

    if (in_array($action, ['approved', 'rejected'])) {
        $stmt = $mysqli->prepare("UPDATE claims SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $action, $claim_id);
        $stmt->execute();
    }
}

// Get all claims
$sql = "
SELECT
  c.id, c.report_id, c.claimant_name, c.contact_info, c.justification,
  c.status, c.claim_date,
  r.item_name
FROM claims c
JOIN reports r ON c.report_id = r.id
ORDER BY c.claim_date DESC
";
$result = $mysqli->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Claim Requests</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="max-w-5xl mx-auto mt-8 bg-white p-6 shadow rounded">
  <h1 class="text-2xl font-bold mb-4">All Claim Requests</h1>

  <?php if ($result->num_rows === 0): ?>
    <p class="text-gray-500 italic">No claims submitted yet.</p>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm border border-gray-200">
        <thead class="bg-gray-200 text-gray-700">
          <tr>
            <th class="p-2">Claimant</th>
            <th class="p-2">Contact</th>
            <th class="p-2">Item</th>
            <th class="p-2">Reason</th>
            <th class="p-2">Status</th>
            <th class="p-2">Action</th>
          </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr class="border-t">
            <td class="p-2"><?= htmlspecialchars($row['claimant_name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($row['contact_info']) ?></td>
            <td class="p-2"><?= htmlspecialchars($row['item_name']) ?></td>
            <td class="p-2"><?= nl2br(htmlspecialchars($row['justification'])) ?></td>
            <td class="p-2"><?= ucfirst($row['status']) ?></td>
            <td class="p-2">
              <?php if ($row['status'] === 'pending'): ?>
                <form method="POST" class="flex gap-1">
                  <input type="hidden" name="claim_id" value="<?= $row['id'] ?>">
                  <button name="action" value="approved" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 text-xs">Approve</button>
                  <button name="action" value="rejected" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-xs">Reject</button>
                </form>
              <?php else: ?>
                <span class="text-gray-500 italic">No action</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
