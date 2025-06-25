function getImageLabels($imagePath) {
    // 1. Get the API key
    $apiKey = getenv('GOOGLE_API_KEY');
    if (!$apiKey) {
        return "Error: No API key set";
    }

    // 2. Read and encode the image
    $imageData = file_get_contents($imagePath);
    if (!$imageData) {
        return "Error: Unable to read image.";
    }
    $base64Image = base64_encode($imageData);

    // 3. Build the Vision API request payload
    $postData = json_encode([
        "requests" => [[
            "image" => ["content" => $base64Image],
            "features" => [[
                "type" => "LABEL_DETECTION",
                "maxResults" => 10
            ]]
        ]]
    ]);

    // 4. Call the Google Vision API
    $url = "https://vision.googleapis.com/v1/images:annotate?key=$apiKey";
    $context = stream_context_create([
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type: application/json",
            "content" => $postData
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        return "Error: API call failed.";
    }

    // 5. Parse the response
    $result = json_decode($response, true);
    if (!isset($result['responses'][0]['labelAnnotations'])) {
        return "Error: No labels found.";
    }

    $labels = array_map(function ($label) {
        return $label['description'];
    }, $result['responses'][0]['labelAnnotations']);

    return implode(', ', $labels); // Return as comma-separated string
}
