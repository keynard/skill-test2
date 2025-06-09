<?php

// Database connection settings
$host = "localhost";    // Database host
$user = "root";         // Database username
$pass = "";             // Database password
$db = "clinic_db";      // Database name

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    // If connection failed, display error and stop script
    die("Connection failed: " . $conn->connect_error);
}
?>