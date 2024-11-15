<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

include 'db_connection.php';

$symbol = $_POST['symbol'];
$name = $_POST['name'];
$price = $_POST['price'];
$amount = $_POST['amount'];
$username = $_SESSION['username'];

// Save purchase to database
$sql = "INSERT INTO purchases (username, coin_symbol, coin_name, price, amount) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssd", $username, $symbol, $name, $price, $amount);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Database error']);
}

$stmt->close();
$conn->close();
?>
