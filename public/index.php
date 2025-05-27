<?php
require __DIR__ . '/../config/config.php';

// Page title
$pageTitle = 'Lost & Found - Home';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';

// Handle filters
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// Build query
$sql = "SELECT id,item_name,type,location,date_reported,status FROM reports WHERE item_name LIKE ?";
$types = 's';
$params = ['%' . $search . '%'];
if ($statusFilter) {
    $sql .= " AND status=?";
    $types .= 's';
    $params[] = $statusFilter;
}
$sql .= " ORDER BY date_reported DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mx-auto px-4">
  <h1 class="text-2xl font-bold mb-4">Lost & Found Items</h1>

  <form method="get" class="mb-6 flex space-x-2">
    <input
      name="search"
      type="text"
      value="<?= htmlspecialchars($search) ?>"
      placeholder="Search items..."
      class="border p-2 rounded flex-grow"
    >
    <select name="status" class="border p-2 rounded">
      <option value="">All Status</option>
      <option value="pending" <?= $statusFilter=='pending'?'selected':'' ?>>Pending</option>
      <option value="in_review" <?= $statusFilter=='in_review'?'selected':'' ?>>In Review</option>
      <option value="resolved" <?= $statusFilter=='resolved'?'selected':'' ?>>Resolved</option>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
      Filter
    </button>
  </form>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php while ($row = $result->fetch_assoc()): ?>
    <article class="bg-white shadow rounded p-4">
      <h2 class="text-xl font-semibold">
        <?= htmlspecialchars($row['item_name']) ?>
      </h2>
      <p class="text-sm">Type: <?= ucfirst($row['type']) ?></p>
      <p class="text-sm">Location: <?= htmlspecialchars($row['location']) ?></p>
      <p class="text-sm">
        Reported: <?= date('Y-m-d H:i', strtotime($row['date_reported'])) ?>
      </p>
      <p class="text-sm">
        Status:
        <span class="font-medium">
          <?= ucfirst(str_replace('_',' ', $row['status'])) ?>
        </span>
      </p>
      <a
        href="item.php?id=<?= $row['id'] ?>"
        class="text-blue-600 mt-2 inline-block"
      >
        View Details →
      </a>
    </article>
    <?php endwhile; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
