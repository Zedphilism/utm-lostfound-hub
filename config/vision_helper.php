function getVisionLabels($filePath) {
    $apiKey = 'AIzaSyAV_Doaljk_BZ74hEdN4mWcCfcbV6agXmg';
    $url = 'https://vision.googleapis.com/v1/images:annotate?key=' . $apiKey;

    $imageData = base64_encode(file_get_contents($filePath));

    $payload = json_encode([
        "requests" => [
            [
                "image" => ["content" => $imageData],
                "features" => [["type" => "LABEL_DETECTION", "maxResults" => 10]]
            ]
        ]
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $response = curl_exec($ch);
    curl_close($ch);

    $labels = [];
    if ($response) {
        $result = json_decode($response, true);
        foreach ($result['responses'][0]['labelAnnotations'] ?? [] as $label) {
            $labels[] = $label['description'];
        }
    }
    return implode(', ', $labels);
}
