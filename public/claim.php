<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/email.php';

$report_id = $_GET['id'] ?? null;
$success = false;
$error = '';

if (!$report_id) {
    die("No item ID provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $claimant_name = trim($_POST['claimant_name'] ?? '');
    $contact_info  = trim($_POST['contact_info'] ?? '');
    $justification = trim($_POST['justification'] ?? '');

    if ($claimant_name && $contact_info && $justification) {
        $stmt = $mysqli->prepare(
            "INSERT INTO claims (report_id, claimant_name, contact_info, justification)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('isss', $report_id, $claimant_name, $contact_info, $justification);
        $stmt->execute();
        $success = true;

        // Send email to admin
        $adminEmail = 'teamwp833@gmail.com'; // Replace with admin email
        $subject = "New Claim for Item ID $report_id";
        $body = "
            <strong>Claimant:</strong> $claimant_name<br>
            <strong>Contact:</strong> $contact_info<br>
            <strong>Reason:</strong><br>" . nl2br(htmlspecialchars($justification));
        sendEmail($adminEmail, $subject, $body);
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Claim Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-xl mx-auto bg-white shadow p-6 mt-10 rounded">

  <h1 class="text-xl font-semibold mb-4">Claim This Item</h1>

  <?php if ($success): ?>
    <p class="text-green-600">Your claim has been submitted. We will contact you if it's approved.</p>
  <?php elseif ($error): ?>
    <p class="text-red-600"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Your Name</label>
      <input type="text" name="claimant_name" required class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Contact Info</label>
      <input type="text" name="contact_info" required class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Why do you believe this item is yours?</label>
      <textarea name="justification" rows="4" required class="w-full border px-3 py-2 rounded"></textarea>
    </div>
    <div>
      <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700">
        Submit Claim
      </button>
    </div>
  </form>

</div>
</body>
</html>
