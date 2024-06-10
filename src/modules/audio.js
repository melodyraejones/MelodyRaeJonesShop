document.addEventListener("DOMContentLoaded", function () {
  let progress = document.querySelector("#progress");
  let introAudio = document.getElementById("intro-audio");
  let mainAudio = document.getElementById("main-audio");
  let disclaimerAudio = document.getElementById("disclaimer-audio");
  let playBtn = document.querySelector(".playIcon");
  let forwardBtn = document.querySelector(".dashicons-controls-forward");
  let backwardBtn = document.querySelector(".dashicons-controls-back");
  let ctrlIcon = document.querySelector("#ctrlIcon");
  let backBtn = document.querySelector(".back");
  // Only run the script if the page has audio controls

  backBtn.addEventListener("click", () => {
    let url = backBtn.getAttribute("data-url");
    window.location.href = url;
  });
  if (introAudio && mainAudio && disclaimerAudio && ctrlIcon) {
    let currentAudio = introAudio; // Start with intro audio as the default

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

      if (direction === "forward") {
        if (currentAudio === introAudio) currentAudio = mainAudio;
        else if (currentAudio === mainAudio) currentAudio = disclaimerAudio;
        else if (currentAudio === disclaimerAudio) currentAudio = introAudio;
      } else {
        if (currentAudio === introAudio) currentAudio = disclaimerAudio;
        else if (currentAudio === mainAudio) currentAudio = introAudio;
        else if (currentAudio === disclaimerAudio) currentAudio = mainAudio;
      }

      addAudioListeners(); // Add event listeners to the new current audio
      updateProgress(); // Update progress bar to match new audio
      currentAudio.play(); // Play the new audio
    }
  }
});

document.addEventListener("DOMContentLoaded", function () {
  // Select the hamburger menu icon
  const menuIcon = document.querySelector(".dashicons-download");

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
  menuIcon.addEventListener("click", function () {
    // Get audio elements
    const introAudio = document.querySelector("#intro-audio source");
    const mainAudio = document.querySelector("#main-audio source");
    const disclaimerAudio = document.querySelector("#disclaimer-audio source");

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
  });
});
