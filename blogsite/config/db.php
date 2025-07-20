<?php
// Database credentials
$host = 'localhost';
$dbname = 'blogging_website'; // change this if you named your DB differently
$username = 'root';
$password = ''; // XAMPP default (no password)

// Create a new MySQLi connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
