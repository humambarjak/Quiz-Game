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
    <title>Crypto Dashboard</title>
    <link rel="icon" href="data:,"> <!-- Add this line to remove the favicon error -->
    <link rel="stylesheet" href="styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mustache@4.2.0/mustache.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="dark-theme">
    <div id="username" style="display: none;"><?php echo $_SESSION['username']; ?></div>

    <h1 class="dashboard-title">Welcome to the Home page <?php echo $_SESSION['username']; ?>!</h1>

    <div class="crypto-dashboard">
        <table class="futuristic-table">
            <thead>
                <tr>
                    <th>Short</th>
                    <th>Coin</th>
                    <th>Price</th>
                    <th>Price (EUR)</th> <!-- New EUR column -->
                    <th>Market Cap</th>
                    <th>% 24hr</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="crypto-data"></tbody>
        </table>
    </div>

    <!-- Mustache Template for Crypto Rows -->
    <script id="crypto-template" type="text/template">
    <tr>
        <td><img src="{{image}}" alt="{{symbol}}" style="width: 20px; height: 20px;"> {{symbol}}</td>
        <td>{{name}}</td>
        <td>{{price}}</td> <!-- USD Price -->
        <td>{{priceEur}}</td> <!-- EUR Price -->
        <td>{{marketCap}}</td>
        <td>{{priceChange}}%</td>
        <td>
            <button class="more-info-btn" onclick="showMoreInfo('{{symbol}}', '{{name}}', '{{price}}', '{{marketCap}}', '{{volume}}', '{{supply}}')">More Info</button>

            <button class="buy-btn" onclick="buyCrypto('{{symbol}}', '{{name}}', '{{price}}')">Buy</button>



            </td>
        </tr>
    </script>

        <!-- Modal for More Info -->
    <div id="infoModal" class="modal">
        <div class="modal-content futuristic-card">
            <span class="close-info">&times;</span>
            <h2 id="modalCryptoName"></h2>
            <p id="modalPrice"></p>
            <p id="modalMarketCap"></p>
            <p id="modalVolume"></p>
            <p id="modalSupply"></p>
            <!-- Canvas for Chart.js -->
            <canvas id="coinChart"></canvas>
        </div>
    </div>


    <!-- Buy Modal (for "Buy" button) -->
    <div id="buyModal" class="modal">
        <div class="modal-content futuristic-card">
            <span class="close-buy">&times;</span>
            <h2>Buying: <span id="modalCryptoBuyName"></span></h2>
            <label for="buyAmount">Amount to Buy:</label>
            <input type="number" id="buyAmount" min="1" placeholder="Enter amount" class="futuristic-input">
            <button onclick="confirmBuy()" class="futuristic-button">Confirm Purchase</button>
        </div>
    </div>

    <!-- Purchase Confirmation Modal -->
    <div id="purchaseModal" class="purchase-modal">
        <div class="modal-content futuristic-card">
            <span class="close-purchase">&times;</span>
            <h2 id="purchaseModalTitle">Purchase Successful!</h2>
            <p id="purchaseModalMessage"></p>
        </div>
    </div>

    <script src="dashboard.js"></script>
</body>
</html>
