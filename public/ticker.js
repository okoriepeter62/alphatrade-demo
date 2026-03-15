
let track = document.getElementById("tickerTrack");
let ticker = document.getElementById("ticker");

let speed = 1;  
let position = 0;
let paused = false;

// Duplicate content for infinite loop
track.innerHTML += track.innerHTML;

// Set track width
track.style.width = track.scrollWidth + "px";

function moveTicker() {
    if (!paused) {
        position -= speed;
        track.style.transform = `translateX(${position}px)`;

        if (Math.abs(position) >= track.scrollWidth / 2) {
            position = 0;
        }
    }
    requestAnimationFrame(moveTicker);
}

// Pause on hover
ticker.addEventListener("mouseenter", () => paused = true);
ticker.addEventListener("mouseleave", () => paused = false);

moveTicker();



const items = document.querySelectorAll(".faq-item");

items.forEach(item => {
  item.querySelector(".faq-question").addEventListener("click", () => {

    // Close all others
    items.forEach(i => {
      if (i !== item) i.classList.remove("active");
      i.querySelector(".faq-answer").style.display = "none";
    });

    // Toggle clicked one
    item.classList.toggle("active");

    const answer = item.querySelector(".faq-answer");
    answer.style.display = item.classList.contains("active") ? "block" : "none";
  });
});


