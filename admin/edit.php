<?php
// File: admin/edit.php
require __DIR__ . '/../config/config.php';

// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auth guard
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get report ID
$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: list.php');
    exit;
}

$error = '';

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token.';
    } else {
        // Gather inputs
        $item_name   = trim($_POST['item_name']   ?? '');
        $type        = $_POST['type']             ?? '';
        $location    = trim($_POST['location']    ?? '');
        $description = trim($_POST['description'] ?? '');
        $reporter    = trim($_POST['reporter']    ?? '');
        $status      = $_POST['status']           ?? '';

        // Basic validation
        if (!$item_name || !$type || !$location || !$status) {
            $error = 'Please fill in all required fields.';
        }
    }

    // Handle image replace if provided
    if (!$error && !empty($_FILES['image']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (in_array($ext, $allowed, true)) {
            $newImage = uniqid('', true) . '.' . $ext;
            $dest     = __DIR__ . '/../assets/uploads/' . $newImage;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $error = 'Failed to upload new image.';
            } else {
                // Delete old
                @unlink(__DIR__ . '/../assets/uploads/' . ($_POST['old_image'] ?? ''));
            }
        } else {
            $error = 'Only JPG, PNG & GIF allowed.';
        }
    }

    // If no errors, update DB
    if (!$error) {
        $imageToSave = $newImage ?? ($_POST['old_image'] ?? null);
        $stmt = $mysqli->prepare("
            UPDATE reports SET
              item_name   = ?,
              type        = ?,
              location    = ?,
              description = ?,
              reporter    = ?,
              status      = ?,
              image       = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            'sssssssi',
            $item_name,
            $type,
            $location,
            $description,
            $reporter,
            $status,
            $imageToSave,
            $id
        );
        if ($stmt->execute()) {
            header('Location: list.php');
            exit;
        } else {
            $error = 'DB error: ' . $stmt->error;
        }
    }
}

// On GET or on validation error, fetch current record
if ($_SERVER['REQUEST_METHOD'] === 'GET' || $error) {
    $stmt = $mysqli->prepare("
        SELECT item_name, type, location, description, reporter, status, image
          FROM reports
         WHERE id = ?
    ");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $report = $stmt->get_result()->fetch_assoc();
    if (!$report) {
        header('Location: list.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Report #<?= $id ?></title>
  <link
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    rel="stylesheet"
  >
</head>
<body>

  <div class="container my-5">
    <h1 class="h3 mb-4">Edit Report #<?= $id ?></h1>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
      <input type="hidden" name="old_image" value="<?= htmlspecialchars($report['image']) ?>">

      <!-- Item Name -->
      <div class="form-group">
        <label>Item Name *</label>
        <input
          type="text"
          name="item_name"
          class="form-control"
          required
          value="<?= htmlspecialchars($_POST['item_name'] ?? $report['item_name']) ?>"
        >
      </div>

      <!-- Type -->
      <div class="form-group">
        <label>Type *</label>
        <select name="type" class="form-control" required>
          <option value="">— Select —</option>
          <option value="lost"  <?= ((($_POST['type']   ?? $report['type']) === 'lost')  ? 'selected' : '') ?>>Lost</option>
          <option value="found" <?= ((($_POST['type']   ?? $report['type']) === 'found') ? 'selected' : '') ?>>Found</option>
        </select>
      </div>

      <!-- Location -->
      <div class="form-group">
        <label>Location *</label>
        <input
          type="text"
          name="location"
          class="form-control"
          required
          value="<?= htmlspecialchars($_POST['location'] ?? $report['location']) ?>"
        >
      </div>

      <!-- Description -->
      <div class="form-group">
        <label>Description</label>
        <textarea
          name="description"
          class="form-control"
        ><?= htmlspecialchars($_POST['description'] ?? $report['description']) ?></textarea>
      </div>

      <!-- Reporter -->
      <div class="form-group">
        <label>Reporter</label>
        <input
          type="text"
          name="reporter"
          class="form-control"
          value="<?= htmlspecialchars($_POST['reporter'] ?? $report['reporter']) ?>"
        >
      </div>

      <!-- Status -->
      <div class="form-group">
        <label>Status *</label>
        <select name="status" class="form-control" required>
          <?php
            $states = [
              'pending'   => 'Pending',
              'in_review' => 'In Review',
              'resolved'  => 'Resolved'
            ];
            $cur = $_POST['status'] ?? $report['status'];
            foreach ($states as $val => $label):
          ?>
            <option value="<?= $val ?>" <?= ($cur === $val ? 'selected' : '') ?>>
              <?= $label ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Current Image Preview -->
      <?php if ($report['image']): ?>
        <div class="form-group">
          <label>Current Image</label><br>
          <img
            src="../assets/uploads/<?= htmlspecialchars($report['image']) ?>"
            class="img-thumbnail"
            style="width: 120px;"
            alt="Current"
          >
        </div>
      <?php endif; ?>

      <!-- Replace Image -->
      <div class="form-group">
        <label>Replace Image</label>
        <input type="file" name="image" accept="image/*" class="form-control-file">
      </div>

      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="list.php" class="btn btn-secondary">Cancel</a>
    </form>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
