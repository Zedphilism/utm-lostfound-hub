<?php
require __DIR__ . '/../config/config.php';

$pageTitle = 'Lost & Found - Home';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<!-- ✅ Animated Announcement Banner -->
<div class="bg-yellow-100 border border-yellow-300 text-yellow-800 text-sm font-medium rounded shadow-sm mt-2 mx-4 overflow-hidden h-[1.8rem] relative">
  <div class="absolute animate-slide-down space-y-1 py-1 pl-4">
    <div>📢 Items reported will be verified before appearing.</div>
    <div>⚠️ Claimed items must be collected at Student Affairs Office, Level 2, Block A, UTMKL SPACE.</div>
    <div>🔍 You can filter reports by status or search by item name.</div>
    <div>📦 Found items unclaimed after 30 days will be handed over to campus security.</div>
  </div>
</div>

<style>
  @keyframes slide-down {
    0%   { transform: translateY(0%); }
    25%  { transform: translateY(-100%); }
    50%  { transform: translateY(-200%); }
    75%  { transform: translateY(-300%); }
    100% { transform: translateY(0%); }
  }
  .animate-slide-down {
    animation: slide-down 16s linear infinite;
  }
</style>

<?php
// ✅ Handle filters
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// ✅ Build query
$sql = "SELECT id, item_name, type, location, date_reported, status, vision_labels FROM reports WHERE item_name LIKE ?";
$types = 's';
$params = ['%' . $search . '%'];

if ($statusFilter) {
    $sql .= " AND status = ?";
    $types .= 's';
    $params[] = $statusFilter;
}
$sql .= " ORDER BY date_reported DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
  <h1 class="text-2xl font-bold mb-4 mt-6">Lost & Found Items</h1>

  <!-- Filter form -->
  <form method="get" class="mb-6 grid grid-cols-1 md:flex md:items-center md:space-x-2 space-y-2 md:space-y-0">
    <input
      name="search"
      type="text"
      value="<?= htmlspecialchars($search) ?>"
      placeholder="Search items..."
      class="border p-2 rounded w-full md:flex-grow"
    >
    <select name="status" class="border p-2 rounded w-full md:w-auto">
      <option value="">All Status</option>
      <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="in_review" <?= $statusFilter == 'in_review' ? 'selected' : '' ?>>In Review</option>
      <option value="claimed" <?= $statusFilter == 'claimed' ? 'selected' : '' ?>>Claimed</option>
      <option value="resolved" <?= $statusFilter == 'resolved' ? 'selected' : '' ?>>Resolved</option>
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full md:w-auto">
      Filter
    </button>
  </form>

  <!-- Item cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php while ($row = $result->fetch_assoc()): ?>
      <article class="relative bg-white shadow rounded p-4">
        <?php if ($row['status'] === 'claimed'): ?>
          <div class="absolute top-2 right-2 bg-yellow-300 text-gray-800 text-xs font-semibold px-2 py-1 rounded shadow">
            CLAIMED
          </div>
        <?php endif; ?>

        <h2 class="text-xl font-semibold">
          <?= htmlspecialchars($row['item_name']) ?>
        </h2>
        <p class="text-sm">Type: <?= ucfirst($row['type']) ?></p>
        <p class="text-sm">Location: <?= htmlspecialchars($row['location']) ?></p>
        <p class="text-sm">Reported: <?= date('Y-m-d H:i', strtotime($row['date_reported'])) ?></p>
        <p class="text-sm">
          Status:
          <span class="font-medium">
            <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
          </span>
        </p>

        <?php if (!empty($row['vision_labels'])): ?>
          <p class="text-sm text-gray-600 italic mt-1">
            Auto-tags: <?= htmlspecialchars($row['vision_labels']) ?>
          </p>
        <?php endif; ?>

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
