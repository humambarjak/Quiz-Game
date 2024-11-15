<?php
session_start();
// Check if the user is logged in; if not, redirect to the login page
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
    <title>Your Crypto Wallet</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/mustache@4.2.0/mustache.min.js"></script>
</head>
<body class="dark-theme">

<div id="username" style="display: none;"><?php echo $_SESSION['username']; ?></div>

<h1 class="dashboard-title">Your Crypto Wallet</h1>

<div class="crypto-wallet">
    <table class="futuristic-table">
        <thead>
            <tr>
                <th>Short</th>
                <th>Coin</th>
                <th>Amount</th>
                <th>Price</th>
                <th>Total Value</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="wallet-data"></tbody>
    </table>
</div>

<!-- Mustache Template for Wallet Data Rows -->
<script id="wallet-template" type="text/template">
    <tr>
        <td>{{symbol}}</td>
        <td>{{name}}</td>
        <td>{{amount}}</td>
        <td>{{price}}</td>
        <td>{{totalValue}}</td>
        <td><button class="sell-btn" onclick="sellCrypto('{{symbol}}', '{{name}}', {{amount}}, {{index}})">Sell</button></td>
    </tr>
</script>

<!-- Modal for selling crypto -->
<div id="sellModal" class="modal">
    <div class="modal-content futuristic-card">
        <span class="close-sell">&times;</span>
        <h2>Sell <span id="sellCryptoName"></span></h2>
        <label for="sellAmount">Amount to Sell:</label>
        <input type="number" id="sellAmount" min="1" step="any" class="futuristic-input">
        <button onclick="confirmSell()" class="futuristic-button">Confirm Sell</button>
    </div>
</div>

<!-- Sell Confirmation Modal -->
<div id="sellConfirmationModal" class="purchase-modal">
    <div class="modal-content">
        <span class="close-sell-confirmation">&times;</span>
        <h2>Sell Confirmation</h2>
        <p id="sellConfirmationModalMessage"></p>
    </div>
</div>

<script>

  // Map symbols to CoinCap-supported asset IDs
const symbolMap = {
    BTC: "bitcoin",
    ETH: "ethereum",
    USDT: "tether",
    BNB: "binance-coin",
    SOL: "solana",
};

function loadWallet() {
    let username = document.getElementById("username").innerText;
    let wallet = JSON.parse(localStorage.getItem(`wallet_${username}`)) || [];

    // Fetch the latest prices and update wallet items
    updatePrices(wallet).then(updatedWallet => {
        const template = document.getElementById("wallet-template").innerHTML;
        
        // Render wallet items with updated prices
        let renderedHtml = updatedWallet.map((item, index) => {
            // Check if price is valid, default to 0 if not
            const price = item.price != null ? item.price : 0;
            let totalValue = (price * item.amount).toFixed(2);

            return Mustache.render(template, {
                symbol: item.symbol.toUpperCase(),
                name: item.name,
                amount: item.amount,
                price: `$${price.toFixed(2)}`, // Safely apply toFixed on price
                totalValue: `$${totalValue}`,
                index: index
            });
        }).join('');

        document.getElementById('wallet-data').innerHTML = renderedHtml;
        // Save updated wallet back to local storage
        localStorage.setItem(`wallet_${username}`, JSON.stringify(updatedWallet));
    }).catch(error => console.error("Error updating prices:", error));
}


// Function to fetch prices from the API and update wallet items
function updatePrices(wallet) {
    const symbols = wallet.map(item => symbolMap[item.symbol] || item.symbol.toLowerCase());
    const apiUrl = `https://api.coingecko.com/api/v3/simple/price?ids=${symbols.join(",")}&vs_currencies=usd`;

    return fetch(apiUrl)
        .then(response => response.json())
        .then(priceData => {
            return wallet.map(item => {
                const symbol = symbolMap[item.symbol] || item.symbol.toLowerCase();
                if (priceData[symbol]) {
                    item.price = priceData[symbol].usd || 0;
                }
                return item;
            });
        });
}

// Function to handle selling cryptocurrency
function sellCrypto(symbol, name, amount, index) {
    selectedSellCoin = { symbol, name, amount };
    selectedSellIndex = index;

    document.getElementById("sellCryptoName").innerText = `${name} (${symbol.toUpperCase()})`;
    document.getElementById("sellAmount").value = '';
    document.getElementById("sellAmount").max = amount; // Set max limit to available amount
    document.getElementById("sellModal").style.display = "block";
}

// Confirm the sell and update the wallet
function confirmSell() {
    let sellAmount = parseFloat(document.getElementById("sellAmount").value);

    if (sellAmount > 0 && sellAmount <= selectedSellCoin.amount) {
        // Show the confirmation message immediately
        showSellConfirmationModal(`Sold ${sellAmount} units of ${selectedSellCoin.name}`);
        
        // Close the sell modal instantly
        document.getElementById("sellModal").style.display = "none";

        let username = document.getElementById("username").innerText;
        let wallet = JSON.parse(localStorage.getItem(`wallet_${username}`)) || [];

        let coinInWallet = wallet[selectedSellIndex];
        if (coinInWallet) {
            if (sellAmount < coinInWallet.amount) {
                coinInWallet.amount -= sellAmount;
            } else {
                wallet.splice(selectedSellIndex, 1);
            }

            // Update the local storage after adjusting the wallet
            localStorage.setItem(`wallet_${username}`, JSON.stringify(wallet));

            // Refresh the wallet display
            loadWallet();
        }
    } else {
        showSellConfirmationModal("Please enter a valid amount to sell.");
    }
}


function showSellConfirmationModal(message) {
    var modal = document.getElementById('sellConfirmationModal');
    var modalMessage = document.getElementById('sellConfirmationModalMessage');
    modalMessage.textContent = message;
    modal.style.display = 'block';

    // Adjust the timer for faster auto-close (e.g., 1500 ms for 1.5 seconds)
    setTimeout(() => modal.style.display = 'none', 1500);
}


// Close button for the sell modal
document.querySelector(".close-sell").onclick = function () {
    document.getElementById("sellModal").style.display = "none";
};

// Close button for the sell confirmation modal
document.querySelector(".close-sell-confirmation").onclick = function () {
    document.getElementById("sellConfirmationModal").style.display = "none";
};

// Close modals when clicking outside of them
window.onclick = function (event) {
    if (event.target == document.getElementById("sellModal")) {
        document.getElementById("sellModal").style.display = "none";
    }
    if (event.target == document.getElementById("sellConfirmationModal")) {
        document.getElementById("sellConfirmationModal").style.display = "none";
    }
};


// Call loadWallet on page load to initialize
window.onload = loadWallet;

</script>
</body>
</html>
