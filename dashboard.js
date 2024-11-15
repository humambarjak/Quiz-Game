// Map symbols to CoinCap-supported asset IDs
const symbolMap = {
    BTC: "bitcoin",
    ETH: "ethereum",
    USDT: "tether",
    BNB: "binance-coin",
    SOL: "solana",
    USDC: "usd-coin",
    XRP: "ripple",
};
// Stores the current chart instance, allowing it to be destroyed and replaced when a new coin's historical data is loaded.
let chartInstance = null;
let selectedCoin = null;//Holds information about the coin currently selected for purchasing.

// Function to fetch cryptocurrency data and render it
function fetchCryptoData() {
    const fetchUSD = fetch("https://api.coincap.io/v2/assets?limit=10")
        .then(response => response.json());

    const fetchEUR = fetch("https://api.coincap.io/v2/rates/euro")
        .then(response => response.json());

    Promise.all([fetchUSD, fetchEUR]).then(([usdData, eurRate]) => {
        const eurConversionRate = eurRate.data.rateUsd;
        const template = document.getElementById("crypto-template").innerHTML;

        const rendered = usdData.data.map(coin => Mustache.render(template, {
            symbol: coin.symbol,
            name: coin.name,
            image: `https://assets.coincap.io/assets/icons/${coin.symbol.toLowerCase()}@2x.png`,
            price: `$${parseFloat(coin.priceUsd).toFixed(2)}`,
            priceEur: `€${(parseFloat(coin.priceUsd) / eurConversionRate).toFixed(2)}`,
            marketCap: `$${parseFloat(coin.marketCapUsd).toLocaleString()}`,
            priceChange: parseFloat(coin.changePercent24Hr).toFixed(2),
        })).join('');
        document.getElementById("crypto-data").innerHTML = rendered;
    })
    .catch(error => console.error("Error fetching data:", error));
}

// Call fetchCryptoData to load data
fetchCryptoData();

// Function to fetch and display historical data in Chart.js
function fetchHistoricalData(coinSymbol) {
    const assetId = symbolMap[coinSymbol.toUpperCase()] || coinSymbol.toLowerCase();
    $.ajax({
        url: `https://api.coincap.io/v2/assets/${assetId}/history?interval=d1`,
        type: 'GET',
        success: function(response) {
            if (response && response.data) {
                let prices = response.data.map(price => parseFloat(price.priceUsd));
                let labels = response.data.map(price => {
                    let date = new Date(price.time);
                    return `${date.getMonth() + 1}/${date.getDate()}`;
                });

                if (chartInstance) {
                    chartInstance.destroy();
                }

                const ctx = document.getElementById("coinChart").getContext("2d");
                chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Price (USD)',
                            data: prices,
                            borderColor: 'rgb(75, 192, 192)',
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { display: true, title: { display: true, text: 'Date' } },
                            y: { display: true, title: { display: true, text: 'Price (USD)' } }
                        }
                    }
                });
            } else {
                console.warn("No historical data available for", assetId);
            }
        },
        error: function(error) {
            console.error("Error fetching historical data:", error);
        }
    });
}

// Function to show the "More Info" modal and display the chart
//This function populates a modal with coin details and shows it. It also calls fetchHistoricalData to load historical data for the selected coin.
function showMoreInfo(coinSymbol, coinName, price, marketCap, volume, supply) {
    document.getElementById("modalCryptoName").innerText = `${coinName} (${coinSymbol.toUpperCase()})`;
    document.getElementById("modalPrice").innerText = `Price: ${price}`;
    document.getElementById("modalMarketCap").innerText = `Market Cap: ${marketCap}`;
    document.getElementById("modalVolume").innerText = `Volume: ${volume}`;
    document.getElementById("modalSupply").innerText = `Supply: ${supply}`;
    document.getElementById("infoModal").style.display = "block";
    fetchHistoricalData(coinSymbol.toLowerCase());
}

// Opens a modal to buy a cryptocurrency, storing the selected coin's information in selectedCoin.
function buyCrypto(symbol, name, price) {
    selectedCoin = { symbol, name, price: parseFloat(price) };
    document.getElementById("modalCryptoBuyName").textContent = name;
    document.getElementById("buyAmount").value = ""; // Reset input field
    document.getElementById("buyModal").style.display = "block"; // Show the buy modal
}

// Processes a buy action by adding the purchased amount to the local storage (simulating a wallet) for the logged-in user.
// Confirm purchase action
function confirmBuy() {
    let amount = parseFloat(document.getElementById("buyAmount").value);
    if (amount > 0 && selectedCoin) {
        const username = document.getElementById("username").innerText;
        let wallet = JSON.parse(localStorage.getItem(`wallet_${username}`)) || [];

        // Check if the coin already exists in the wallet
        let existingCoin = wallet.find(coin => coin.symbol === selectedCoin.symbol);

        if (existingCoin) {
            existingCoin.amount += amount;
        } else {
            wallet.push({
                symbol: selectedCoin.symbol,
                name: selectedCoin.name,
                price: selectedCoin.price, // Ensure this is correctly set
                amount: amount
            });
        }

        // Save the updated wallet back to localStorage
        localStorage.setItem(`wallet_${username}`, JSON.stringify(wallet));

        // Display purchase confirmation
        showPurchaseModal(`Purchased ${amount} units of ${selectedCoin.name}`);
        document.getElementById("buyModal").style.display = "none";
        loadWallet(); // Refresh wallet display
    } else {
        showPurchaseModal("Please enter a valid amount to buy.");
    }
}

// Display purchase confirmation modal
function showPurchaseModal(message) {
    document.getElementById("purchaseModalMessage").textContent = message;
    document.getElementById("purchaseModal").style.display = "block";

    // Auto-close after 3 seconds
    setTimeout(() => document.getElementById("purchaseModal").style.display = "none", 1500);
}

// Event listeners to close modals
document.querySelector(".close-buy").onclick = () => document.getElementById("buyModal").style.display = "none";
document.querySelector(".close-purchase").onclick = () => document.getElementById("purchaseModal").style.display = "none";

// Loads the user's wallet data from local storage and renders it in a table.
// Load wallet data for display
function loadWallet() {
    const username = document.getElementById("username").innerText;
    const wallet = JSON.parse(localStorage.getItem(`wallet_${username}`)) || [];
    let tableBody = '';

    wallet.forEach(function(item) {
        const totalValue = (item.price * item.amount).toFixed(2);
        tableBody += `<tr>
            <td>${item.symbol.toUpperCase()}</td>
            <td>${item.name}</td>
            <td>${item.amount}</td>
            <td>${item.price}</td>
            <td>${totalValue}</td>
            <td><button class="sell-btn" onclick="sellCrypto('${item.symbol}', ${item.amount})">Sell</button></td>
        </tr>`;
    });

    document.getElementById('wallet-data').innerHTML = tableBody;
}

// Close modals when clicking outside of them
window.onclick = function(event) {
    const infoModal = document.getElementById("infoModal");
    const buyModal = document.getElementById("buyModal");
    if (event.target == infoModal) {
        infoModal.style.display = "none";
    }
    if (event.target == buyModal) {
        buyModal.style.display = "none";
    }
};

// This function saves a transaction by sending data to record_transaction.php. This records each transaction for persistence.
function saveTransaction(coinSymbol, coinName, transactionType, price, amount) {
    $.post("record_transaction.php", {
        coin_symbol: coinSymbol,
        coin_name: coinName,
        transaction_type: transactionType,
        price: price,
        amount: amount
    })
    .done(function(response) {
        // Handle successful response
        console.log("Transaction saved:", response);
        loadWallet(); // Refresh wallet after transaction
    })
    .fail(function(xhr, status, error) {
        console.error("Error saving transaction:", error);
    });
}


$(document).ready(function() {
    fetchCryptoData();
    setInterval(fetchCryptoData, 300000);  // Refresh every 5 minutes

    $(".close-info").click(function () {
        document.getElementById("infoModal").style.display = "none";
    });

    $(".close-buy").click(function () {
        document.getElementById("buyModal").style.display = "none";
    });
});
