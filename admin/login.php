<?php
// File: admin/login.php
require __DIR__ . '/../config/config.php';

// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, go to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Fetch from admin_users table
        $stmt = $mysqli->prepare(
            "SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1"
        );
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash);
        if ($stmt->fetch() && password_verify($password, $hash)) {
            // Login success
            $_SESSION['user_id']  = $id;
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .utm-header { background-color: rgb(82, 127, 224); padding: 20px 0; }
    .utm-header img { height: 60px; }
    .utm-card   { border-top: 5px solid #A31F34; }

    /* new: UTM-themed public-site button */
    .btn-public {
      display: block;
      width: 100%;
      margin-top: 1rem;
      padding: .375rem .75rem;
      background-color: rgb(82,127,224);
      border: 2px solid #A31F34;
      color: #fff;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
    }
    .btn-public:hover {
      background-color: #6a9ee0;
      text-decoration: none;
    }
  </style>
</head>
<body class="bg-light">
  <header class="utm-header text-center mb-4">
    <img src="../assets/images/utm-logo.png" alt="UTM Logo">
  </header>

  <div class="container" style="max-width: 400px;">
    <div class="card utm-card shadow-sm">
      <div class="card-body">
        <h4 class="card-title mb-4 text-center">Admin Login</h4>

        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Log In</button>

          <a href="../public/index.php" class="btn-public">
            Return to homepage
          </a>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
