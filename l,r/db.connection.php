<?php
// Database connection settings
$host = 'localhost';
$user = 'roshan';
$password = 'password'; // Replace with your MySQL password
$dbname = 'user_database'; // Replace with your database name
$port = 4306;

// Establish database connection
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
