export function initializeDirectCheckout() {
  const buyNowButtons = document.querySelectorAll(".btn-buy");

  buyNowButtons.forEach((button) => {
    button.addEventListener("click", async (e) => {
      e.preventDefault();
      const productId = button.dataset.id;

      try {
        const response = await fetch("/wp-json/mrj/v1/direct-checkout", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-WP-Nonce": mrjData.nonce,
          },
          body: JSON.stringify({ product_id: productId }),
        });

        const data = await response.json();
        if (data.url) {
          window.location.href = data.url;
        } else {
          console.error("Failed to start checkout:", data.error);
        }
      } catch (error) {
        console.error("Error:", error);
      }
    });
  });
}
