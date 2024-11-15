<?php
session_start();
require 'database_connection.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

try {
    // Remove or comment out these debug messages
    // echo "Database connection is successful!<br>";

    // Fetch transactions for the logged-in user
    $sql = "SELECT * FROM transactions WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Remove or comment out this debug message
    // echo "Transactions fetched successfully:<br>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-theme">
    <h1 class="dashboard-title">Transaction History for <?php echo htmlspecialchars($username); ?></h1>

    <?php if ($transactions): ?>
        <div class="crypto-wallet">
            <table class="futuristic-table">
                <thead>
                    <tr>
                        <th>Transaction Type</th>
                        <th>Coin</th>
                        <th>Amount</th>
                        <th>Price</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($transaction['transaction_type']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['coin_name']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['amount']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['price']); ?></td>
                            <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No transactions found.</p>
    <?php endif; ?>
</body>
</html>
