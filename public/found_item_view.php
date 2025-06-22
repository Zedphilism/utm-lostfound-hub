require_once __DIR__ . '/../helpers/match_helper.php';

$matches = matchLostItems($foundItem['vision_labels'], $mysqli);

if (!empty($matches)) {
    echo "<h3>Possible Matches:</h3><ul>";
    foreach ($matches as $match) {
        echo "<li><strong>{$match['item_name']}</strong> - {$match['location']}</li>";
    }
    echo "</ul>";
}
