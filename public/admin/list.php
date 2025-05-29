<?php
// File: admin/list.php
require __DIR__ . '/../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/admin/login.php');
    exit;
}

// Fetch all reports, reading the real `image_path` column
$sql = "
    SELECT
        id,
        item_name,
        type,
        location,
        DATE_FORMAT(date_reported, '%Y-%m-%d') AS date_reported,
        reporter,
        status,
        image_path
    FROM reports
    ORDER BY date_reported DESC
";
$stmt   = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Reports</title>
  <link
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    rel="stylesheet"
  >
  <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>

  <!-- BLUE HEADER -->
  <div class="bg-primary text-white p-3 mb-4">
    <div class="container">
      <h1 class="h4 mb-0">Report List</h1>
    </div>
  </div>

  <div class="container">
    <table class="table table-bordered">
      <thead class="thead-light">
        <tr>
          <th>ID</th>
          <th>Item</th>
          <th>Type</th>
          <th>Location</th>
          <th>Date</th>
          <th>Status</th>
          <th>Reporter</th>
          <th>Image</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= $row['type'] ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= $row['date_reported'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= htmlspecialchars($row['reporter']) ?></td>
            <td>
              <?php if ($row['image_path']): ?>
                <img src="/public/<?= $row['image_path'] ?>" alt="Item Image" width="100">
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
