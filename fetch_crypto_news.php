<?php
header('Content-Type: application/json');
//Defines the URL to fetch news articles. The URL includes
//tickers=BTC,ETH: Specifies which cryptocurrencies to fetch news for
$url = "https://cryptonews-api.com/api/v1?tickers=BTC,ETH&items=10&token=e40a3b3f3e354f05a3fb3c5c38e36283";
// Initializes a new cURL session.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($http_code == 200) {
    echo $response;
} else {
    $error_message = curl_error($ch);
    echo json_encode([
        "error" => "Failed to fetch news", 
        "http_code" => $http_code, 
        "curl_error" => $error_message,
        "suggestion" => "Check API key or try another news source."
    ]);
}
//Closes the cURL session to free up resources.
curl_close($ch);
?>

<script>
    //articles: Array of news articles.
    //source: Source of the news (e.g., "Crypto News API" or "RSS Feed").
function displayNews(articles, source) {
    if (articles && articles.length > 0) {
        let newsHtml = '';
        articles.forEach(article => {
            newsHtml += `
                <div class="news-card">
                    <h2>${article.title}</h2>
                    <p>${article.description || 'No description available.'}</p>
                    <a href="${article.link || article.url}" target="_blank">Read more</a>
                </div>
            `;
        });
        $('#news-container').html(newsHtml);
    } else {
        $('#news-container').html(`<p>No articles found from ${source}.</p>`);
    }
}

$(document).ready(function() {
    // Toggle true or false to switch between RSS and API
    fetchCryptoNews(true); // Set to true for RSS feed, false for API
});
</script>