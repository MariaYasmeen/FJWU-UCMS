<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

// Create connection without database
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read and execute SQL file
$sql = file_get_contents('database.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Database setup completed successfully!";
} else {
    echo "Error setting up database: " . $conn->error;
}

$conn->close();
?> 