function fetchRSSFeed() {
    $.get('https://api.rss2json.com/v1/api.json', {
        rss_url: 'https://www.coindesk.com/arc/outboundfeeds/rss/',
        api_key: '' // Optional but not required for low volume
    }, function(response) {
        if (response.status === 'ok' && response.items.length > 0) {
            const newsContainer = $('#news-container');
            let newsHtml = '';
            response.items.forEach(article => {
                newsHtml += `
                    <div class="news-card">
                        <h2>${article.title}</h2>
                        <p>${article.description || 'No description available.'}</p>
                        <a href="${article.link}" target="_blank">Read more</a>
                    </div>
                `;
            });
            newsContainer.html(newsHtml);
        } else {
            $('#news-container').html("<p>No news articles found or an error occurred.</p>");
        }
    }).fail(function() {
        $('#news-container').html("<p>Failed to load news articles.</p>");
    });
}

$(document).ready(function() {
    fetchRSSFeed();
});
