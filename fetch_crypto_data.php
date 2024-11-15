<?php
session_start();
if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

// Set the API URL
$api_url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=7&page=1&sparkline=false';

// Initialize a cURL session
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $api_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, ['User-Agent: PHP/' . phpversion()]);

// Execute cURL request and fetch the response
$crypto_data = curl_exec($curl);

// Check for any errors in the cURL request
if ($crypto_data === false) {
    echo json_encode(['error' => 'cURL Error: ' . curl_error($curl)]);
    curl_close($curl);
    exit();
}

// Close the cURL session
curl_close($curl);

// Return the data as JSON
header('Content-Type: application/json');
echo $crypto_data;
?>
