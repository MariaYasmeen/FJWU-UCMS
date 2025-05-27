<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in and is a faculty
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: /ucms/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $faculty_id = $_SESSION['user_id'];
    $complaint_id = intval($_POST['complaint_id']);
    $status = $_POST['status'];
    $comment = trim($_POST['comment']);

    // Update complaint status
    $update_sql = "UPDATE complaints SET status = ?, updated_at = NOW() WHERE id = ? AND assigned_to = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $status, $complaint_id, $faculty_id);
    
    if ($stmt->execute()) {
        // Add comment if provided
        if (!empty($comment)) {
            $comment_sql = "INSERT INTO faculty_complaints (complaint_id, faculty_id, status, comment) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($comment_sql);
            $stmt->bind_param("iiss", $complaint_id, $faculty_id, $status, $comment);
            $stmt->execute();
        }
        
        $_SESSION['success'] = "Complaint status updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating complaint status.";
    }
}

header("Location: view_complaint.php?id=" . $complaint_id);
exit(); 