<?php
/*
Template Name: Success Page
*/
get_header();
?>

<section class="success-section">
    <div class="success-message">
        <h1>Thank You for Your Purchase!</h1>
        <p>
            We appreciate your business! If you have any questions, please email
            <a href="mailto:melody@melodyraejones.com">melody@melodyraejones.com</a>.
        </p>
        <p id="userDetails"></p>
        <p class="meditation-journey">
            Start your meditation journey now by accessing your programs.
        </p>
        <a href="<?php echo esc_url(home_url('/audio-files/')); ?>" class="btn btn--full btn-start-meditation">Start Meditation Journey</a>
    </div>
</section>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');

    fetch(`/wp-json/mrj/v1/session?session_id=${sessionId}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        let userDetailsHtml = `Order placed by ${data.username} (${data.email})`;
        if (data.products && Array.isArray(data.products)) {
            userDetailsHtml += '<ul>';
            data.products.forEach(product => {
                // Assuming product.productId is directly accessible
                userDetailsHtml += `<li>${product.name} - Total: $${product.price} - Product Id: ${product.productId}</li>`;
            });
            userDetailsHtml += '</ul>';
        } else {
            userDetailsHtml += '<p>No product details available.</p>';
        }

        document.getElementById('userDetails').innerHTML = userDetailsHtml;
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
});
</script>

<?php
get_footer();
?>

<style>
/* Full height section */
.success-section {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100vh;
    background-color: #f5f5f5;
    text-align: center;
    padding: 2rem;
}

.success-message {
    max-width: 600px;
    background: #fff;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.success-message h1 {
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
}

.success-message p {
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.success-message a {
    color: #643482;
    text-decoration: none;
}

.btn.btn--full {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.8rem 1.2rem;
    font-size: 1.2rem;
    color: #fff;
    background-color: #643482;
    border-radius: 5px;
    text-decoration: none;
}

.btn.btn--full:hover {
    background-color: #5b3177;
}

.meditation-journey {
    font-size: 1.2rem;
    margin-top: 1.5rem;
    color: #333;
}
</style>
