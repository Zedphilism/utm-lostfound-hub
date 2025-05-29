<?php
// File: admin/add.php
require __DIR__ . '/../../config/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $type = $_POST['type'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $reporter = $_POST['reporter'] ?? '';
    $submitted_by = 'admin';

    $stmt = $mysqli->prepare("
        INSERT INTO reports (item_name, type, location, description, reporter, submitted_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('ssssss', $item_name, $type, $location, $description, $reporter, $submitted_by);
    $stmt->execute();

    header('Location: /public/admin/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Item</title>
  <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-xl mx-auto mt-10 bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Add Lost/Found Item</h1>
    <form method="post">
      <div class="mb-4">
        <label class="block mb-1">Item Name</label>
        <input type="text" name="item_name" class="w-full border p-2 rounded" required>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Type</label>
        <select name="type" class="w-full border p-2 rounded" required>
          <option value="lost">Lost</option>
          <option value="found">Found</option>
        </select>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Location</label>
        <input type="text" name="location" class="w-full border p-2 rounded" required>
      </div>
      <div class="mb-4">
        <label class="block mb-1">Description</label>
        <textarea name="description" class="w-full border p-2 rounded"></textarea>
      </div>
      <div class="mb-4">
        <label class="block mb-1
