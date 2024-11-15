function fetchExchangesData() {
    //is the HTML element where exchange data will be displayed. Initially, a "Loading data..." message is shown while data is being fetched from external APIs.
    const exchangesContainer = document.getElementById('exchanges-container');
    exchangesContainer.innerHTML = '<p>Loading data...</p>';
//Some exchanges don’t have a direct mapping between their name and a cryptocurrency symbol
    const symbolOverrides = {
        "Binance": "bnb",
        "Coinbase Pro": "btc",
        "Crypto.com Exchange": "cro",
        "DigiFinex": "btc",
        "Kraken": "btc",
        "Kucoin": "kcs",
        "Huobi": "ht",
        "Gate": "gt",
        "Bitfinex": "leo",
       
    };
// tries to find a coin symbol associated with a given exchange.
    function findSymbol(exchangeName, marketData) {
        if (symbolOverrides[exchangeName]) return symbolOverrides[exchangeName];
        for (let coin of marketData) {
            if (exchangeName.toLowerCase().includes(coin.name.toLowerCase())) {
                return coin.symbol;
            }
        }
        return null;
    }
//list of exchanges from CoinCap. After receiving the data, it sorts the exchanges by rank (ascending), so that higher-ranked exchanges appear first.
    fetch("https://api.coincap.io/v2/exchanges")
        .then(response => response.json())
        .then(exchangeData => {
            const sortedExchanges = exchangeData.data.sort((a, b) => a.rank - b.rank);
// market data for the top 100 cryptocurrencies from CoinGecko.
//priceMap: A priceMap object is created to store prices and market cap ranks for quick lookups based on symbol.
            fetch("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=100&page=1&sparkline=false")
                .then(response => response.json())
                .then(marketData => {
                    exchangesContainer.innerHTML = '';
                    const priceMap = {};
                    marketData.forEach(coin => {
                        priceMap[coin.symbol] = {
                            price: coin.current_price,
                            rank: coin.market_cap_rank,
                        };
                    });
                    // processes the top 20 exchanges by rank and renders an HTML card for each one.
                    sortedExchanges.slice(0, 20).forEach((exchange) => {
                        const volume = exchange.volumeUsd24Hr ? `$${parseFloat(exchange.volumeUsd24Hr).toFixed(2)}` : "N/A";
                        let potentialSymbol = findSymbol(exchange.name, marketData);
                        let price = "N/A";
                        let rank = `Rank: ${exchange.rank}`; // Use exchange's own rank directly

                        if (priceMap[potentialSymbol]) {
                            price = `$${priceMap[potentialSymbol].price.toFixed(2)}`;
                        }

                        const exchangeCard = `
                            <div class="exchange-card">
                                <div class="exchange-name">${exchange.name}</div>
                                <div class="exchange-rank">${rank}</div>
                                <div class="exchange-price">Price: ${price}</div>
                                <a href="${exchange.exchangeUrl || '#'}" target="_blank" class="exchange-link">Visit Website</a>
                            </div>
                        `;
                        exchangesContainer.innerHTML += exchangeCard;
                    });
                })
                // If fetching CoinGecko market data fails, an error message is displayed in the container.
                .catch(error => {
                    console.error("Error fetching market data:", error);
                    exchangesContainer.innerHTML = "<p>Error loading market data.</p>";
                });
        })
        .catch(error => {
            console.error("Error fetching exchange data:", error);
            exchangesContainer.innerHTML = "<p>Error loading exchange data.</p>";
        });
}

// The function fetchExchangesData is called once to load data initially and then every 60 seconds to keep the data updated.
fetchExchangesData();
setInterval(fetchExchangesData, 60000);
