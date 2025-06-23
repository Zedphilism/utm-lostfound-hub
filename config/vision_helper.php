function getVisionLabels($imagePath) {
    $apiKey = getenv('GOOGLE_API_KEY') ?: getenv('GOOGLE_VISION_API_KEY');
    if (!$apiKey) {
        return 'Auto-tag unavailable (no API key)';
    }

    $imageData = file_get_contents($imagePath);
    if (!$imageData) {
        return 'Image not readable';
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
    $labels = [];

    if (isset($result['responses'][0]['labelAnnotations'])) {
        foreach ($result['responses'][0]['labelAnnotations'] as $annotation) {
            $labels[] = $annotation['description'];
        }
    }

    return !empty($labels) ? implode(', ', $labels) : 'No tags detected';
}
