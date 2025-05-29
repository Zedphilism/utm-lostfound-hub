<?php
// File: public/add.php
require __DIR__ . '/../config/config.php';

// Initialize form values & errors
$errors      = [];
$item_name   = '';
$type        = '';
$location    = '';
$description = '';
$reporter    = '';
$image_path  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Sanitize
    $item_name   = trim($_POST['item_name']   ?? '');
    $type        = $_POST['type']             ?? '';
    $location    = trim($_POST['location']    ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter']    ?? '');

    // 2) Validate
    if ($item_name === '')   $errors[] = 'Item name is required.';
    if (!in_array($type, ['lost','found'], true)) $errors[] = 'Please select Lost or Found.';
    if ($location === '')    $errors[] = 'Location is required.';
    if ($reporter === '')    $errors[] = 'Your name is required.';

    // 3) Handle optional image upload
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Image must be JPG, PNG, or GIF.';
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Image upload failed.';
        } else {
            $dest_dir = __DIR__ . '/../assets/uploads/';
            if (!is_dir($dest_dir)) mkdir($dest_dir, 0755, true);
            $filename = uniqid('', true) . '.' . $ext;
            $target   = $dest_dir . $filename;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $errors[] = 'Could not save uploaded image.';
            } else {
                $image_path = 'assets/uploads/' . $filename;
            }
        }
    }

    // 4) If OK, insert & redirect
    if (empty($errors)) {
        $stmt = $mysqli->prepare("
            INSERT INTO reports
              (item_name, type, location, description, reporter, status, image_path, date_reported)
            VALUES
              (?, ?, ?, ?, ?, 'pending', ?, NOW())
        ");
        $stmt->bind_param(
            'ssssss',
            $item_name, $type, $location,
            $description, $reporter, $image_path
        );
        $stmt->execute();
        header('Location: index.php');
        exit;
    }
}

// 5) Render form in your public layout
$pageTitle = 'Report an Item';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold mb-4">Report an Item</h1>

  <?php if ($errors): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      <ul class="list-disc pl-5">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="space-y-4">
    <div>
      <label class="block mb-1 font-medium">Item Name *</label>
      <input
        type="text" name="item_name" required
        value="<?= htmlspecialchars($item_name) ?>"
        class="border p-2 rounded w-full"
      >
    </div>

    <div>
      <label class="block mb-1 font-medium">Type *</label>
      <select name="type" required class="border p-2 rounded w-full">
        <option value="">— Select —</option>
        <option value="lost"  <?= $type==='lost'  ? 'selected' : '' ?>>Lost</option>
        <option value="found" <?= $type==='found' ? 'selected' : '' ?>>Found</option>
      </select>
    </div>

    <div>
      <label class="block mb-1 font-medium">Location *</label>
      <input
        type="text" name="location" required
        value="<?= htmlspecialchars($location) ?>"
        class="border p-2 rounded w-full"
      >
    </div>

    <div>
      <label class="block mb-1 font-medium">Description</label>
      <textarea
        name="description" rows="4"
        class="border p-2 rounded w-full"
      ><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div>
      <label class="block mb-1 font-medium">Your Name *</label>
      <input
        type="text" name="reporter" required
        value="<?= htmlspecialchars($reporter) ?>"
        class="border p-2 rounded w-full"
      >
    </div>

    <div>
      <label class="block mb-1 font-medium">Image (optional)</label>
      <input type="file" name="image" class="border p-2 rounded w-full">
    </div>

    <div class="pt-4">
      <button
        type="submit"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded"
      >
        Submit Report
      </button>
      <a href="index.php" class="ml-4 text-gray-700 hover:underline">Cancel</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
