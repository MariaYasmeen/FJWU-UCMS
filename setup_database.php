<?php
// Database connection parameters
$host = 'localhost';
$username = 'root';
$password = '';

// Create connection without database
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS ucms_db";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db("ucms_db");

// Read the SQL file
$sql = file_get_contents('database.sql');

// Split the SQL file into individual queries
$queries = array_filter(array_map('trim', explode(';', $sql)));

// Execute each query
foreach ($queries as $query) {
    if (!empty($query)) {
        if ($conn->query($query) === TRUE) {
            echo "Query executed successfully: " . substr($query, 0, 50) . "...<br>";
        } else {
            echo "Error executing query: " . $conn->error . "<br>";
            echo "Query: " . $query . "<br>";
        }
    }
}

echo "<br>Database setup completed! You can now <a href='index.php'>go to the homepage</a>.";
?> 