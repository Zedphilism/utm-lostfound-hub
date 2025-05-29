<?php
// File: admin/login.php
require __DIR__ . '/../../config/config.php';


// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $mysqli->prepare(
            "SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1"
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash);
        if ($stmt->fetch() && password_verify($password, $hash)) {
            $_SESSION['user_id'] = $id;
            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-100 text-gray-900">
  <div class="max-w-md mx-auto mt-20 bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Admin Login</h1>
    <?php if ($error): ?>
      <div class="mb-4 text-red-600"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-4">
        <label class="block mb-1">Username</label>
        <input type="text" name="username" class="w-full border p-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block mb-1">Password</label>
        <input type="password" name="password" class="w-full border p-2 rounded" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
        Login
      </button>
    </form>
  </div>
</body>
</html>
