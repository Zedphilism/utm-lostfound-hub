function matchLostItems($vision_labels, $mysqli) {
    $labels = explode(',', $vision_labels);
    $matches = [];

    foreach ($labels as $label) {
        $label = trim($label);
        $query = "SELECT * FROM reports WHERE type='lost' AND vision_labels LIKE ? AND status='unclaimed'";
        $stmt = $mysqli->prepare($query);
        $like = '%' . $label . '%';
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $matches[] = $row;
        }
    }

    return $matches;
}
