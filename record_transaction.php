<?php
session_start();
require 'database_connection.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$username = $_SESSION['username'];
$coin_symbol = $_POST['coin_symbol'];
$coin_name = $_POST['coin_name'];
$transaction_type = $_POST['transaction_type'];
$price = $_POST['price'];
$amount = $_POST['amount'];
$total_value = $_POST['total_value'];

// Log the received data for debugging
error_log("Received data: username=$username, coin_symbol=$coin_symbol, transaction_type=$transaction_type, price=$price, amount=$amount, total_value=$total_value");

// Insert the transaction into the database
$stmt = $conn->prepare("INSERT INTO transactions (username, coin_symbol, coin_name, transaction_type, price, amount, total_value) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$username, $coin_symbol, $coin_name, $transaction_type, $price, $amount, $total_value]);


echo json_encode(['success' => true]);
?>
