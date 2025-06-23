<?php
/**
 * vision_helper.php
 * Fungsi untuk ambil label objek dari Google Vision API.
 * Digunakan dalam projek Lost & Found Assistant
 */

function getVisionLabels($imagePath) {
    $apiKey = getenv('GOOGLE_API_KEY') ?: getenv('GOOGLE_VISION_API_KEY');

    if (!$apiKey) {
        return 'Auto-tag unavailable (no API key)';
    }

    $imageData = file_get_contents($imagePath);
    if (!$imageData) {
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

    $result = json_decode($response, true);
    file_put_contents(__DIR__ . '/../vision_log.txt', "== GOOGLE VISION RAW ==\n" . print_r($result, true) . "\n\n", FILE_APPEND);

    $labels = [];
    if (isset($result['responses'][0]['labelAnnotations'])) {
        foreach ($result['responses'][0]['labelAnnotations'] as $annotation) {
            $labels[] = $annotation['description'];
        }
    }

    $finalResult = !empty($labels) ? implode(', ', $labels) : 'No tags detected';
    file_put_contents(__DIR__ . '/../vision_log.txt', "🧠 Labels returned: " . $finalResult . "\n\n", FILE_APPEND);
    return $finalResult;
}
