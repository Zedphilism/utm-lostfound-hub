<?php
/**
 * vision_helper.php
 * Fungsi untuk ambil label objek dari Google Vision API.
 * Digunakan dalam projek Lost & Found Assistant
 */

function getVisionLabels($imagePath) {
    // Ambil API Key dari environment
    $apiKey = getenv('GOOGLE_API_KEY') ?: getenv('GOOGLE_VISION_API_KEY');

    // Jika tiada API key
    if (!$apiKey) {
        return 'Auto-tag unavailable (no API key)';
    }

    // Baca fail gambar
    $imageData = file_get_contents($imagePath);
    if (!$imageData) {
        return 'Image not readable: ' . $imagePath;
    }

    // Sediakan payload JSON
    $encodedImage = base64_encode($imageData);
    $json = json_encode([
        'requests' => [[
            'image' => ['content' => $encodedImage],
            'features' => [['type' => 'LABEL_DETECTION', 'maxResults' => 5]],
        ]],
    ]);

    // Hantar permintaan ke Google Vision
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

    // Decode response
    $result = json_decode($response, true);
    $labels = [];

    // Semak dan ekstrak label
    if (isset($result['responses'][0]['labelAnnotations'])) {
        foreach ($result['responses'][0]['labelAnnotations'] as $annotation) {
            $labels[] = $annotation['description'];
        }
    }

    // Hantar semula hasil
    return !empty($labels) ? implode(', ', $labels) : 'No tags detected';
}
