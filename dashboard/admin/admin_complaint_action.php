<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}
if (!isset($_POST['action'], $_POST['complaint_id']) || !is_numeric($_POST['complaint_id'])) {
    http_response_code(400);
    exit('Invalid request');
}
$complaint_id = intval($_POST['complaint_id']);
$action = $_POST['action'];
if ($action === 'assign' && isset($_POST['faculty_id'])) {
    $faculty_id = $_POST['faculty_id'] ? intval($_POST['faculty_id']) : null;
    $stmt = $conn->prepare("UPDATE complaints SET assigned_to = ? WHERE id = ?");
    $stmt->bind_param("ii", $faculty_id, $complaint_id);
    if ($stmt->execute()) {
        echo 'Faculty assigned.';
    } else {
        http_response_code(500);
        echo 'Failed to assign faculty.';
    }
    exit;
}
if ($action === 'status' && isset($_POST['status'])) {
    $status = $_POST['status'];
    $allowed = ['pending', 'in_progress', 'resolved'];
    if (!in_array($status, $allowed)) {
        http_response_code(400);
        exit('Invalid status');
    }
    $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $complaint_id);
    if ($stmt->execute()) {
        echo 'Status updated.';
    } else {
        http_response_code(500);
        echo 'Failed to update status.';
    }
    exit;
}
if ($action === 'comment' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    // Add admin_comment column if not exists
    $conn->query("ALTER TABLE complaints ADD COLUMN admin_comment TEXT NULL");
    $stmt = $conn->prepare("UPDATE complaints SET admin_comment = ? WHERE id = ?");
    $stmt->bind_param("si", $comment, $complaint_id);
    if ($stmt->execute()) {
        echo 'Comment saved.';
    } else {
        http_response_code(500);
        echo 'Failed to save comment.';
    }
    exit;
}
echo 'No action performed.'; 