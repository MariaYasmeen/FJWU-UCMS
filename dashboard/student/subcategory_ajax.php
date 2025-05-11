<?php
require_once '../../includes/db.php';
if (!isset($_GET['category_id']) || !is_numeric($_GET['category_id'])) {
    echo '<option value="">Select Sub-Category</option>';
    exit;
}
$category_id = intval($_GET['category_id']);
$result = $conn->query("SELECT id, name FROM complaint_subcategories WHERE category_id = $category_id ORDER BY name");
if ($result && $result->num_rows > 0) {
    echo '<option value="">Select Sub-Category</option>';
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
    }
} else {
    echo '<option value="">No subcategories available</option>';
} 