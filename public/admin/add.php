<?php
require __DIR__ . '/../../config/config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Lost/Found Item</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

  <!-- Nav -->
  <div class="flex justify-between items-center px-6 pt-4">
    <a href="/admin/index.php" class="text-sm text-blue-600 hover:underline">
      ← Back to Dashboard
    </a>
    <a href="/index.php" class="text-sm text-blue-600 hover:underline">
      Public View →
    </a>
  </div>

  <!-- Header -->
  <div class="bg-blue-600 text-white p-4 mt-4">
    <h1 class="text-xl font-semibold">Add Lost/Found Item</h1>
  </div>

  <!-- Form -->
  <div class="max-w-xl mx-auto mt-6 bg-white p-6 shadow rounded">
    <form method="post" action="submit.php" enctype="multipart/form-data">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Item Name</label>
        <input type="text" name="item_name" class="w-full border p-2 rounded" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Type</label>
        <select name="type" class="w-full border p-2 rounded">
          <option value="lost">Lost</option>
          <option value="found">Found</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Location</label>
        <input type="text" name="location" class="w-full border p-2 rounded" required />
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Description</label>
        <textarea name="description" class="w-full border p-2 rounded" rows="4"></textarea>
      </div>
      <div class="mb-4">
        <label class="block mb-1 font-medium">Image</label>
        <input type="file" name="image" class="w-full" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">
        Submit Report
      </button>
    </form>
  </div>
</body>
</html>
