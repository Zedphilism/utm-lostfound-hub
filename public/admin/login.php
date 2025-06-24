<?php
// File: admin/login.php
require __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 text-gray-900">

  <!-- Shared top nav -->
  <?php include __DIR__ . '/../../includes/nav.php'; ?>

  <!-- Main login box with UTM logo header -->
  <div class="max-w-md mx-auto mt-20 bg-white rounded shadow overflow-hidden">
    <!-- Blue header with UTM logo -->
    <div class="bg-blue-900 p-4 flex justify-center">
      <img src="/assets/images/utm-logo.png" alt="UTM Logo" class="h-12 object-contain" />
    </div>

    <!-- Login form content -->
    <div class="p-6">
      <h1 class="text-2xl font-bold mb-4">Admin Login</h1>
      <?php if ($error): ?>
        <div class="mb-4 text-red-600"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post">
        <div class="mb-4">
          <label class="block mb-1">Username</label>
          <input type="text" name="username" class="w-full border p-2 rounded" required />
        </div>
        <div class="mb-4">
          <label class="block mb-1">Password</label>
          <input type="password" name="password" class="w-full border p-2 rounded" required />
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">
          Login
        </button>
      </form>
    </div>
  </div>

  <!-- Shared footer -->
  <?php include __DIR__ . '/../../includes/footer.php'; ?>
</body>
</html>
