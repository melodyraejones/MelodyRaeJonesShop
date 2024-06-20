// modules/countdownTimer.js
export function initializeCountdownTimers() {
  document.addEventListener("DOMContentLoaded", function () {
    const posts = document.querySelectorAll(".weekly-zen-post");

    posts.forEach((post) => {
      const expirationTime = post.getAttribute("data-expiration-time");
      const countdownElement = post.querySelector(".countdown-timer");

      if (expirationTime && countdownElement) {
        const expirationDate = new Date(parseInt(expirationTime) * 1000);
        const interval = setInterval(() => {
          const now = new Date().getTime();
          const distance = expirationDate - now;

          if (distance < 0) {
            clearInterval(interval);
            countdownElement.innerHTML = "This post has expired";
            post.style.display = "none"; // Optionally hide the post
          } else {
            const hours = Math.floor(
              (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            const minutes = Math.floor(
              (distance % (1000 * 60 * 60)) / (1000 * 60)
            );
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `${hours}h ${minutes}m ${seconds}s`;
          }
        }, 1000);
      }
    });
  });
}
