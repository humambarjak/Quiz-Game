<?php
// database_connection.php

$host = 'localhost';          // Database host, often 'localhost'
$dbname = 'crypto-wallet';    // Your database name
$username = 'root';           // Your database username
$password = '';               // Your database password

try {
    // Create a PDO instance (connect to the database)
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error mode to exception to handle errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("Database connection failed: " . $e->getMessage());
}
?>
