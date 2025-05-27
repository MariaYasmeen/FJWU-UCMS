<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Check if category_id is provided
if (!isset($_GET['category_id'])) {
    http_response_code(400);
    exit('Category ID is required');
}

$category_id = (int)$_GET['category_id'];

// Get subcategories for the selected category
$sql = "SELECT id, name, description FROM subcategories WHERE category_id = ? ORDER BY name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

$subcategories = [];
while ($row = $result->fetch_assoc()) {
    $subcategories[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($subcategories); 