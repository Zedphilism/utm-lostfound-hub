<?php
require __DIR__ . '/../config/config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 1) Admin-submitted
$sqlA = "
  SELECT id, item_name, type, location,
         DATE_FORMAT(date_reported, '%Y-%m-%d') AS date_reported,
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
         DATE_FORMAT(date_reported, '%Y-%m-%d') AS date_reported,
         reporter, status, image_path
    FROM reports
   WHERE submitted_by = 'public'
   ORDER BY date_reported DESC
";
$stmtP = $mysqli->prepare($sqlP);
$stmtP->execute();
$resP = $stmtP->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All Reports</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <header class="bg-primary text-white py-3 mb-4">
    <div class="container d-flex justify-content-between">
      <h1 class="h3 mb-0">Lost & Found Admin</h1>
      <div>
        <a href="index.php" class="btn btn-light btn-sm">Dashboard</a>
      </div>
    </div>
  </header>

  <div class="container">

    <!-- Admin group -->
    <h2 class="h5">Admin-Submitted Reports</h2>
    <?php if ($resA->num_rows): ?>
      <table class="table table-sm table-striped table-bordered mb-5">
        <thead class="thead-dark">
          <tr>
            <th>ID</th><th>Image</th><th>Item Name</th><th>Type</th>
            <th>Location</th><th>Date</th><th>Reporter</th>
            <th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $resA->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>
                <?php if ($row['image_path']): ?>
                  <img
                    src="../<?= htmlspecialchars($row['image_path']) ?>"
                    class="img-thumbnail"
                    style="width:80px"
                  >
                <?php else: ?>—<?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['item_name']) ?></td>
              <td><?= ucfirst($row['type']) ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td><?= $row['date_reported'] ?></td>
              <td><?= htmlspecialchars($row['reporter']) ?></td>
              <td><?= ucfirst(str_replace('_',' ',$row['status'])) ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this report?')">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p><em>No admin reports found.</em></p>
    <?php endif; ?>

    <!-- Public group -->
    <h2 class="h5">Public-Submitted Reports</h2>
    <?php if ($resP->num_rows): ?>
      <table class="table table-sm table-striped table-bordered">
        <thead class="thead-dark">
          <tr>
            <th>ID</th><th>Image</th><th>Item Name</th><th>Type</th>
            <th>Location</th><th>Date</th><th>Reporter</th>
            <th>Status</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $resP->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>
                <?php if ($row['image_path']): ?>
                  <img
                    src="../<?= htmlspecialchars($row['image_path']) ?>"
                    class="img-thumbnail"
                    style="width:80px"
                  >
                <?php else: ?>—<?php endif; ?>
              </td>
              <td><?= htmlspecialchars($row['item_name']) ?></td>
              <td><?= ucfirst($row['type']) ?></td>
              <td><?= htmlspecialchars($row['location']) ?></td>
              <td><?= $row['date_reported'] ?></td>
              <td><?= htmlspecialchars($row['reporter']) ?></td>
              <td><?= ucfirst(str_replace('_',' ',$row['status'])) ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this report?')">
                  Delete
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p><em>No public reports found.</em></p>
    <?php endif; ?>

  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
