<?php
// File: admin/list.php
require __DIR__ . '/../config/config.php';

// Only start session if none is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all reports, alias `image` → `image_path`
$sql = "
    SELECT
        id,
        item_name,
        type,
        location,
        DATE_FORMAT(date_reported, '%Y-%m-%d') AS date_reported,
        reporter,
        status,
        image AS image_path
    FROM reports
    ORDER BY date_reported DESC
";
$stmt   = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Close PHP so we can emit HTML below
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
</head>
<body>

  <!-- PLAIN BLUE HEADER -->
  <header class="bg-primary text-white py-3 mb-4">
    <div class="container d-flex justify-content-between align-items-center">
      <h1 class="h4 m-0">Lost &amp; Found Admin</h1>
      <div>
        <a href="../public/index.php" class="btn btn-light btn-sm mr-2">
          Home
        </a>
        <a href="../admin/index.php" class="btn btn-light btn-sm">
          Dashboard
        </a>
      </div>
    </div>
  </header>

  <div class="container">

    <!-- PAGE TITLE & ADD BUTTON -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="h5 m-0">All Reports</h2>
      <a href="add.php" class="btn btn-success btn-sm">
        + Add New Report
      </a>
    </div>

    <!-- REPORTS TABLE -->
    <table class="table table-striped table-bordered">
      <thead class="thead-dark">
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Item Name</th>
          <th>Type</th>
          <th>Location</th>
          <th>Date</th>
          <th>Reporter</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td>
              <?php if ($row['image_path']): ?>
                <img
                  src="../assets/uploads/<?= htmlspecialchars($row['image_path']) ?>"
                  alt="Thumb"
                  class="img-thumbnail"
                  style="width: 80px;"
                >
              <?php else: ?>
                &mdash;
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
            <td><?= htmlspecialchars(ucfirst($row['type'])) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td><?= htmlspecialchars($row['date_reported']) ?></td>
            <td><?= htmlspecialchars($row['reporter']) ?></td>
            <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
            <td>
              <a href="edit.php?id=<?= $row['id'] ?>"
                 class="btn btn-warning btn-sm">Edit</a>
              <a href="delete.php?id=<?= $row['id'] ?>"
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Optional Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
