<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include 'navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto News</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Add jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="dark-theme">
<h1 class="page-title">Crypto News</h1>

    <div class="news-section futuristic-card">
       
        <div id="news-container">
            <!-- News articles will be dynamically inserted here -->
        </div>
    </div>
    <script src="news.js"></script> <!-- Link to the new JavaScript file for handling news display -->
</body>
</html>
