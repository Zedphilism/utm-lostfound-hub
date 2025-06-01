<?php
// File: admin/index.php
require __DIR__ . '/../../config/config.php';

// Session & auth guard
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch report counts
$total = $mysqli->query("SELECT COUNT(*) AS cnt FROM reports")->fetch_assoc()['cnt'];
$pend  = $mysqli->query("SELECT COUNT(*) AS cnt FROM reports WHERE status='pending'")->fetch_assoc()['cnt'];
$inrev = $mysqli->query("SELECT COUNT(*) AS cnt FROM reports WHERE status='in_review'")->fetch_assoc()['cnt'];
$resvd = $mysqli->query("SELECT COUNT(*) AS cnt FROM reports WHERE status='resolved'")->fetch_assoc()['cnt'];

// ✅ Fetch pending claim count
$claims = $mysqli->query("SELECT COUNT(*) AS cnt FROM claims WHERE status='pending'")->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-primary">
    <span class="navbar-brand mb-0 h1">Lost & Found Admin</span>
    <div>
      <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </nav>

  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h3">Dashboard</h1>
    </div>

    <div class="row mb-4">
      <div class="col-md-3 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <p class="card-text text-muted">Total Reports</p>
            <h5 class="card-title"><?= $total ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <p class="card-text text-muted">Pending</p>
            <h5 class="card-title"><?= $pend ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <p class="card-text text-muted">In Review</p>
            <h5 class="card-title"><?= $inrev ?></h5>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center">
          <div class="card-body">
            <p class="card-text text-muted">Resolved</p>
            <h5 class="card-title"><?= $resvd ?></h5>
          </div>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <a href="list.php" class="btn btn-primary mr-2">Manage Reports</a>
      <a href="add.php" class="btn btn-success mr-2">Add New Report</a>
      <a href="claims.php" class="btn btn-warning">
        Manage Claims
        <?php if ($claims > 0): ?>
          <span class="badge badge-light ml-1"><?= $claims ?> Pending</span>
        <?php endif; ?>
      </a>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
