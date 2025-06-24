<?php
/**
 * vision_helper.php
 * Fungsi untuk ambil label objek dari Google Vision API.
 * Digunakan dalam projek Lost & Found Assistant
 */

// ❌ Buang echo yang ganggu output HTML
// echo "✅ vision_helper.php berjaya dimuat<br>";

function getVisionLabels($imagePath) {
    $logFile = __DIR__ . '/vision_log.txt';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " 🟢 getVisionLabels() called\n", FILE_APPEND);

    $apiKey = getenv('GOOGLE_API_KEY') ?: getenv('GOOGLE_VISION_API_KEY');

    if (!$apiKey) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " ❌ No API key found\n", FILE_APPEND);
        return 'Auto-tag unavailable (no API key)';
    }

    $imageData = file_get_contents($imagePath);
    if (!$imageData) {
        file_put_contents($logFile, date('Y-m-d H:i:s') . " ❌ Failed to read image: $imagePath\n", FILE_APPEND);
        return 'Image not readable: ' . $imagePath;
    }

    $encodedImage = base64_encode($imageData);
    $json = json_encode([
        'requests' => [[
            'image' => ['content' => $encodedImage],
            'features' => [['type' => 'LABEL_DETECTION', 'maxResults' => 5]],
        ]],
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
    curl_close($ch);

    // Log permintaan
    file_put_contents($logFile, date('Y-m-d H:i:s') . " 🔄 Request sent to: $url\n", FILE_APPEND);
    file_put_contents($logFile, "📥 Raw API Response:\n" . $response . "\n", FILE_APPEND);

    $result = json_decode($response, true);
    $labels = [];

    if (isset($result['responses'][0]['labelAnnotations'])) {
        foreach ($result['responses'][0]['labelAnnotations'] as $annotation) {
            $labels[] = $annotation['description'];
        }
    }

    $finalResult = !empty($labels) ? implode(', ', $labels) : 'No tags detected';
    file_put_contents($logFile, "✅ Labels Detected: $finalResult\n\n", FILE_APPEND);

    return $finalResult;
}
