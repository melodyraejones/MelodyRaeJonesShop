// Our modules / classes
// import MobileMenu from "./modules/MobileMenu";

// Instantiate a new object using our modules/classes
// const mobileMenu = new MobileMenu()
// const heroSlider = new HeroSlider()
// main.js
// This is your test publishable API key.
import MyCart from "./modules/cart";
// import MelsFavoritesCart from "./modules/melsFavoritesCart";
import { initializeDirectCheckout } from "./modules/direct-checkout";
import "./modules/audio";
import { initializeCountdownTimers } from "./modules/timer";

const myCart = new MyCart();
// const melFavCart = new MelsFavoritesCart();

initializeDirectCheckout();
initializeCountdownTimers();
// Mobile navigation
const btnNavEl = document.querySelector(".btn-mobile-nav");
const headerEl = document.querySelector(".main-header");

btnNavEl.addEventListener("click", function () {
  headerEl.classList.toggle("nav-open");
});

document.addEventListener("DOMContentLoaded", function () {
  var downloadLink = document.getElementById("auto-download-link");
  if (downloadLink) {
    downloadLink.click();
  }

  var audioElements = document.querySelectorAll(".music-player audio");
  audioElements.forEach(function (audio) {
    audio.addEventListener("play", function () {
      document.querySelector(".audio-title-container").classList.add("playing");
    });
    audio.addEventListener("pause", function () {
      document
        .querySelector(".audio-title-container")
        .classList.remove("playing");
    });
    audio.addEventListener("ended", function () {
      document
        .querySelector(".audio-title-container")
        .classList.remove("playing");
    });
  });
});
