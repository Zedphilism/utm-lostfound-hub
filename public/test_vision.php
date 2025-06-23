<?php
require_once __DIR__ . '/../config/vision_helper.php';

$imagePath = __DIR__ . '/upload/BOOK.jpg';

// Debug semak fail wujud
if (!file_exists($imagePath)) {
    die("❌ Gambar tidak dijumpai: $imagePath");
}

// Panggil API
$labels = getVisionLabels($imagePath);

// Debug response
echo "<h2>Hasil Google Vision API:</h2>";
echo "<pre>$labels</pre>";

// Jika perlu debug lebih lanjut (optional)
if ($labels === 'No tags detected' || str_contains($labels, 'unavailable')) {
    echo "<p style='color:red;'>❌ Tiada tag dikesan. Mungkin API gagal atau tiada objek dikenalpasti.</p>";
} else {
    echo "<p style='color:green;'>✅ Auto-tag berjaya!</p>";
}
?>
