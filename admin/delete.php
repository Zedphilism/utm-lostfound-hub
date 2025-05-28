<?php
// File: admin/delete.php

// 1) Bring in your DB connection
require __DIR__ . '/../config/config.php';

// 2) Validate & grab the “id” query parameter
if (empty($_GET['id']) || !ctype_digit($_GET['id'])) {
    // bad or missing id ⇒ go back to the list in the same folder
    header('Location: list.php');
    exit;
}
$id = (int) $_GET['id'];

// 3) Prepare & execute the DELETE against the `reports` table
$stmt = $mysqli->prepare("DELETE FROM reports WHERE id = ?");
if (! $stmt) {
    // prepare failed — log & redirect with an error flag
    error_log("DELETE prepare failed: " . $mysqli->error);
    header('Location: list.php?error=delete');
    exit;
}

$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

// 4) On success, redirect back to list.php in this same folder,
//    attaching a “deleted=1” flag so you can show “Item deleted” if you like.
header('Location: list.php?deleted=1');
exit;
