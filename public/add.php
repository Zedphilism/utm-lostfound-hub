<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/cloudinary.php';
// require_once __DIR__ . '/../config/vision_helper.php'; // disable sementara untuk debug
use Cloudinary\Api\Upload\UploadApi;

session_start();

$success = false;
$error = '';
$vision_labels = '';

/* ✅ MASUKKAN FUNGSI SECARA LANGSUNG UNTUK TEST */
function getVisionLabels($imagePath) {
    $apiKey = getenv('GOOGLE_API_KEY');
    if (!$apiKey) {
        error_log("❌ GOOGLE_API_KEY is empty!");
        return '';
    }

    $imageData = file_get_contents($imagePath);
    if (!$imageData) {
        error_log("❌ Failed to read image at $imagePath");
        return '';
    }

    $encodedImage = base64_encode($imageData);
    $json = json_encode([
        'requests' => [[
            'image' => ['content' => $encodedImage],
            'features' => [['type' => 'LABEL_DETECTION', 'maxResults' => 5]],
        ]]
    ]);

    $url = 'https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => $json,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("🔄 Vision API Response Code: $httpCode");
    error_log("🧠 Raw Response: $response");

    $result = json_decode($response, true);
    if (isset($result['responses'][0]['labelAnnotations'])) {
        $labels = array_column($result['responses'][0]['labelAnnotations'], 'description');
        return implode(', ', $labels);
    }

    return '';
}
/* Tamat fungsi */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name   = trim($_POST['item_name'] ?? '');
    $type        = $_POST['type'] ?? 'lost';
    $location    = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $reporter    = trim($_POST['reporter'] ?? 'Anonymous');
    $image_path  = '';

    if (!empty($_FILES['image']['tmp_name'])) {
        try {
            $result = (new UploadApi())->upload($_FILES['image']['tmp_name']);
            $image_path = $result['secure_url'];

            $tempImage = tempnam(sys_get_temp_dir(), 'vision_');
            file_put_contents($tempImage, file_get_contents($image_path));

            if (function_exists('getVisionLabels')) {
                $vision_labels = getVisionLabels($tempImage);
                error_log("✅ Vision Labels: " . $vision_labels);
            } else {
                error_log("❌ Function getVisionLabels() not found");
                $error = 'Internal error: Vision function not found.';
            }

            unlink($tempImage);
        } catch (Exception $e) {
            $error = 'Image processing failed: ' . $e->getMessage();
        }
    }

    if (!$error) {
        $stmt = $mysqli->prepare(
            "INSERT INTO reports (item_name, type, location, description, reporter, image_path, vision_labels, submitted_by)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'public')"
        );
        $stmt->bind_param('sssssss', $item_name, $type, $location, $description, $reporter, $image_path, $vision_labels);
        $stmt->execute();
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report Lost/Found Item</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="max-w-xl mx-auto mt-10 bg-white shadow p-6 rounded">
  <h1 class="text-xl font-semibold mb-4">Report a Lost/Found Item</h1>

  <?php if ($success): ?>
    <p class="text-green-600 mb-4">Your report has been submitted.</p>
    <?php if (!empty($vision_labels)): ?>
      <p class="text-sm text-gray-600"><strong>Detected Tags:</strong> <?= htmlspecialchars($vision_labels) ?></p>
    <?php endif; ?>
  <?php elseif ($error): ?>
    <p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="space-y-4">
    <div>
      <label class="block text-sm font-medium">Item Name</label>
      <input type="text" name="item_name" required class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Type</label>
      <select name="type" class="w-full border px-3 py-2 rounded">
        <option value="lost">Lost</option>
        <option value="found">Found</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium">Location</label>
      <input type="text" name="location" required class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Description</label>
      <textarea name="description" rows="3" class="w-full border px-3 py-2 rounded"></textarea>
    </div>
    <div>
      <label class="block text-sm font-medium">Your Name</label>
      <input type="text" name="reporter" placeholder="Optional" class="w-full border px-3 py-2 rounded">
    </div>
    <div>
      <label class="block text-sm font-medium">Upload Image</label>
      <input type="file" name="image" accept="image/*" class="block w-full text-sm text-gray-700">
    </div>
    <div>
      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Submit Report</button>
      <a href="/index.php" class="ml-4 text-sm text-blue-600 hover:underline">← Back to Home</a>
    </div>
  </form>
</div>
</body>
</html>
