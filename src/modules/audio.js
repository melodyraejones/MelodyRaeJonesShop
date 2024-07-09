document.addEventListener("DOMContentLoaded", function () {
  let progress = document.querySelector("#progress");
  let introAudio = document.getElementById("intro-audio");
  let mainAudio = document.getElementById("main-audio");
  let disclaimerAudio = document.getElementById("disclaimer-audio");
  let optionalAudio = document.getElementById("optional-audio");
  let playBtn = document.querySelector(".playIcon");
  let forwardBtn = document.querySelector(".dashicons-controls-forward");
  let backwardBtn = document.querySelector(".dashicons-controls-back");
  let ctrlIcon = document.querySelector("#ctrlIcon");
  let backBtn = document.querySelector(".back");

  // Only run the script if the page has audio controls

  backBtn?.addEventListener("click", () => {
    let url = backBtn.getAttribute("data-url");
    window.location.href = url;
  });

  if (
    (introAudio || mainAudio || disclaimerAudio || optionalAudio) &&
    ctrlIcon
  ) {
    let currentAudio =
      introAudio || mainAudio || disclaimerAudio || optionalAudio; // Start with the first available audio as the default

    function updateProgress() {
      progress.max = currentAudio.duration;
      progress.value = currentAudio.currentTime;
    }

    function updateTime() {
      progress.value = currentAudio.currentTime;
    }

    function updatePlayState() {
      ctrlIcon.classList.add("dashicons-controls-pause");
      ctrlIcon.classList.remove("dashicons-controls-play");
    }

    function updatePauseState() {
      ctrlIcon.classList.remove("dashicons-controls-pause");
      ctrlIcon.classList.add("dashicons-controls-play");
    }

    function addAudioListeners() {
      currentAudio.addEventListener("timeupdate", updateTime);
      currentAudio.addEventListener("play", updatePlayState);
      currentAudio.addEventListener("pause", updatePauseState);
    }

    function removeAudioListeners() {
      currentAudio.removeEventListener("timeupdate", updateTime);
      currentAudio.removeEventListener("play", updatePlayState);
      currentAudio.removeEventListener("pause", updatePauseState);
    }

    currentAudio.onloadedmetadata = updateProgress; // Initialize metadata for the first audio
    addAudioListeners(); // Add listeners to the initial audio

    playBtn.addEventListener("click", () => {
      if (currentAudio.paused) {
        currentAudio.play();
      } else {
        currentAudio.pause();
      }
    });

    progress.addEventListener("change", () => {
      currentAudio.currentTime = progress.value;
    });

    forwardBtn.addEventListener("click", () => {
      switchAudio("forward");
    });

    backwardBtn.addEventListener("click", () => {
      switchAudio("backward");
    });

    function switchAudio(direction) {
      removeAudioListeners(); // Remove event listeners from the current audio
      currentAudio.pause();

      const audioOrder = [
        introAudio,
        mainAudio,
        disclaimerAudio,
        optionalAudio,
      ].filter(Boolean);
      const currentIndex = audioOrder.indexOf(currentAudio);

      if (direction === "forward") {
        currentAudio = audioOrder[(currentIndex + 1) % audioOrder.length];
      } else {
        currentAudio =
          audioOrder[
            (currentIndex - 1 + audioOrder.length) % audioOrder.length
          ];
      }

      addAudioListeners(); // Add event listeners to the new current audio
      updateProgress(); // Update progress bar to match new audio
      currentAudio.play(); // Play the new audio
    }
  }

  // Function to download a file
  function downloadFile(url, fileName) {
    const a = document.createElement("a");
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  // Attach click event to the menu icon
  const menuIcon = document.querySelector(".dashicons-download");
  menuIcon?.addEventListener("click", function () {
    // Get audio elements
    const introAudio = document.querySelector("#intro-audio source");
    const mainAudio = document.querySelector("#main-audio source");
    const disclaimerAudio = document.querySelector("#disclaimer-audio source");
    const optionalAudio = document.querySelector("#optional-audio source");

    // Download audio files if they exist
    if (introAudio) {
      downloadFile(introAudio.src, "intro_audio.mp3");
    }
    if (mainAudio) {
      downloadFile(mainAudio.src, "main_audio.mp3");
    }
    if (disclaimerAudio) {
      downloadFile(disclaimerAudio.src, "disclaimer_audio.mp3");
    }
    if (optionalAudio) {
      downloadFile(optionalAudio.src, "optional_audio.mp3");
    }
  });
});
