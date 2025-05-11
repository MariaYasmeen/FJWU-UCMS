<?php
session_start(); // Start the session to access logged-in user info

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php"); // Redirect to login page if not logged in
    exit();
}

include('../includes/header.php');
include('../includes/navbar.php');

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "ucms_db"; 

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Close the database connection
$conn->close();

// Determine the role and include the corresponding profile view
$role = $user['role'];

switch ($role) {
    case 'student':
        include('CR-profile.php');
        break;
    case 'faculty':
        include('faculty-profile.php');
        break;
    case 'admin':
        include('admin-profile.php');
        break;
    default:
        echo "Invalid role.";
        break;
}
?>

