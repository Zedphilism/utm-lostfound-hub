<?php
// File: admin/add.php
require __DIR__ . '/../config/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$item_name = '';
$type = '';
$location = '';
$description = '';
$reporter = '';
$status = 'pending';
$image_path = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $item_name   = trim($_POST['item_name'] ?? '');
    $type        = $_POST['type'] ?? '';
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter'] ?? '');
    $status      = $_POST['status'] ?? 'pending';

    // Validate required fields
    if ($item_name === '') {
        $errors[] = 'Item name is required.';
    }
    if (!in_array($type, ['lost','found'], true)) {
        $errors[] = 'Type must be either "lost" or "found".';
    }
    if ($location === '') {
        $errors[] = 'Location is required.';
    }
    if ($reporter === '') {
        $errors[] = 'Reporter name is required.';
    }

    // Handle optional image upload
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Image must be JPG, PNG, or GIF.';
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed.';
        } else {
            $dest_dir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0755, true);
            }
            $filename = uniqid() . '.' . $ext;
            $target = $dest_dir . $filename;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = 'Could not save uploaded image.';
            } else {
                $image_path = 'assets/uploads/' . $filename;
            }
        }
    }

    // If no errors, insert into database
    if (empty($errors)) {
        $stmt = $mysqli->prepare("
            INSERT INTO reports
              (item_name, type, location, description, reporter, status, image_path, date_reported)
            VALUES
              (?, ?, ?, ?, ?, ?, ?, CURDATE())
        ");
        $stmt->bind_param(
            'sssssss',
            $item_name, $type, $location,
            $description, $reporter,
            $status, $image_path
        );
        $stmt->execute();
        header('Location: list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Report</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-dark bg-primary">
    <span class="navbar-brand">Lost & Found Admin</span>
    <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
  </nav>

  <div class="container my-4">
    <h1 class="h3 mb-4">Add New Report</h1>

    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>Item Name *</label>
        <input type="text" name="item_name" class="form-control"
               value="<?= htmlspecialchars($item_name) ?>" required>
      </div>

      <div class="form-group">
        <label>Type *</label>
        <select name="type" class="form-control" required>
          <option value="">— Select —</option>
          <option value="lost"  <?= $type==='lost'  ? 'selected':'' ?>>Lost</option>
          <option value="found" <?= $type==='found' ? 'selected':'' ?>>Found</option>
        </select>
      </div>

      <div class="form-group">
        <label>Location *</label>
        <input type="text" name="location" class="form-control"
               value="<?= htmlspecialchars($location) ?>" required>
      </div>

      <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($description) ?></textarea>
      </div>

      <div class="form-group">
        <label>Reporter *</label>
        <input type="text" name="reporter" class="form-control"
               value="<?= htmlspecialchars($reporter) ?>" required>
      </div>

      <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
          <option value="pending"  <?= $status==='pending'  ? 'selected':'' ?>>Pending</option>
          <option value="in_review"<?= $status==='in_review'? 'selected':'' ?>>In Review</option>
          <option value="resolved" <?= $status==='resolved' ? 'selected':'' ?>>Resolved</option>
        </select>
      </div>

      <div class="form-group">
        <label>Image (optional)</label>
        <input type="file" name="image" class="form-control-file">
      </div>

      <button type="submit" class="btn btn-success">Create Report</button>
      <a href="list.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</body>
</html>
