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
