<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ucms_db';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>
