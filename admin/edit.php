<?php
// File: admin/edit.php
require __DIR__ . '/../config/config.php';

// — Session & auth guard —
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// — Get & validate ID —
$id = $_GET['id'] ?? '';
if (!ctype_digit($id)) {
    header('Location: list.php');
    exit;
}

// — Fetch existing record —
$sql = "
    SELECT
      item_name,
      type,
      location,
      description,
      reporter,
      status,
      image_path
    FROM reports
    WHERE id = ?
    LIMIT 1
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result(
    $item_name,
    $type,
    $location,
    $description,
    $reporter,
    $status,
    $existing_image_path
);
if (!$stmt->fetch()) {
    // no such ID
    header('Location: list.php');
    exit;
}
$stmt->close();

$errors = [];

// — Handle form submission —
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) sanitize
    $item_name   = trim($_POST['item_name']   ?? '');
    $type        = $_POST['type']             ?? '';
    $location    = trim($_POST['location']    ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter']    ?? '');
    $status      = $_POST['status']           ?? '';

    // 2) validate
    if ($item_name === '')   $errors[] = 'Item name is required.';
    if (!in_array($type, ['lost','found'], true)) $errors[] = 'Please select Lost or Found.';
    if ($location === '')    $errors[] = 'Location is required.';
    if ($reporter === '')    $errors[] = 'Reporter name is required.';
    if (!in_array($status, ['pending','in review','resolved'], true)) {
        $errors[] = 'Invalid status selected.';
    }

    // 3) handle optional image replacement
    //    start with the existing path
    $image_path = $existing_image_path;
    //    only if the user actually chose a file…
    if (
        isset($_FILES['image'])
        && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Error uploading image.';
        } else {
            $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Image must be JPG, PNG, or GIF.';
            } else {
                $uploads = __DIR__ . '/../assets/uploads/';
                if (!is_dir($uploads)) {
                    mkdir($uploads, 0755, true);
                }
                $newName = uniqid('img_', true) . '.' . $ext;
                $target  = $uploads . $newName;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $image_path = 'assets/uploads/' . $newName;
                } else {
                    $errors[] = 'Failed to save the new image.';
                }
            }
        }
    }

    // 4) if no errors, update and redirect
    if (empty($errors)) {
        $upd = "
            UPDATE reports SET
              item_name   = ?,
              type        = ?,
              location    = ?,
              description = ?,
              reporter    = ?,
              status      = ?,
              image_path  = ?
            WHERE id = ?
        ";
        $u = $mysqli->prepare($upd);
        $u->bind_param(
            'sssssssi',
            $item_name,
            $type,
            $location,
            $description,
            $reporter,
            $status,
            $image_path,
            $id
        );
        $u->execute();
        header('Location: list.php');
        exit;
    }
}

// — Render —
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-6">Edit Report #<?= htmlspecialchars($id) ?></h1>

  <?php if ($errors): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      <ul class="list-disc pl-5">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form
    method="post"
    enctype="multipart/form-data"
    class="space-y-4"
  >
    <div>
      <label class="block font-medium mb-1">Item Name *</label>
      <input
        type="text" name="item_name" required
        value="<?= htmlspecialchars($item_name) ?>"
        class="border p-2 rounded w-full"
      >
    </div>

    <div>
      <label class="block font-medium mb-1">Type *</label>
      <select name="type" required class="border p-2 rounded w-full">
        <option value="">— Select —</option>
        <option value="lost"  <?= $type==='lost'  ? 'selected' : '' ?>>Lost</option>
        <option value="found" <?= $type==='found' ? 'selected' : '' ?>>Found</option>
      </select>
    </div>

    <div>
      <label class="block font-medium mb-1">Location *</label>
      <input
        type="text" name="location" required
        value="<?= htmlspecialchars($location) ?>"
        class="border p-2 rounded w-full"
      >
    </div>

    <div>
      <label class="block font-medium mb-1">Description</label>
      <textarea
        name="description" rows="4"
        class="border p-2 rounded w-full"
      ><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div>
      <label class="block font-medium mb-1">Reporter *</label>
      <input
        type="text" name="reporter" required
        value="<?= htmlspecialchars($reporter) ?>"
        class="border p-2 rounded w-full"
      >
    </div>

    <div>
      <label class="block font-medium mb-1">Status *</label>
      <select name="status" required class="border p-2 rounded w-full">
        <option value="pending"   <?= $status==='pending'   ? 'selected' : '' ?>>Pending</option>
        <option value="in review" <?= $status==='in review' ? 'selected' : '' ?>>In Review</option>
        <option value="resolved"  <?= $status==='resolved'  ? 'selected' : '' ?>>Resolved</option>
      </select>
    </div>

    <div>
      <label class="block font-medium mb-1">Replace Image</label>
      <input type="file" name="image" class="border p-2 rounded w-full">
      <?php if ($existing_image_path): ?>
        <img 
          src="../<?= htmlspecialchars($existing_image_path) ?>"
          alt="Current" 
          class="mt-2 max-h-40"
        >
      <?php endif; ?>
    </div>

    <div class="pt-4">
      <button
        type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
      >
        Save Changes
      </button>
      <a
        href="list.php"
        class="ml-4 text-gray-700 hover:underline"
      >
        Cancel
      </a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
