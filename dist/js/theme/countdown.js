"use strict"; // Apply strict mode to the entire script

/**
 * Countdown javascript functions file.
 * Called by custom-wp-widget-counter.php
 */

// Utility to run a function when the document is ready
const docReady = (fn) => {
  if (document.readyState !== 'loading') {
    setTimeout(fn, 1); // Ensure asynchronous execution
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
};

// Main function to be executed once the document is ready
docReady(() => {
  // Calculates time remaining until a specified endtime
  const getTimeRemaining = (endtime) => {
    const adjustment = 18 * 3600 * 1000; // 18 hours in milliseconds
    const now = new Date();
    const total = Date.parse(endtime) + adjustment - now.getTime();
    
    const days = Math.floor(total / (1000 * 60 * 60 * 24));
    const hours = Math.floor((total / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((total / (1000 * 60)) % 60);

    return { total, days, hours, minutes };
  };

  // Initializes the countdown clock
  const initializeClock = (id, endtime) => {
    const clock = document.getElementById(id);
    if (!clock) return; // Exit if the clock element is not found

    const daysSpan = clock.querySelector('.days');
    const hoursSpan = clock.querySelector('.hours');
    const minutesSpan = clock.querySelector('.minutes');

    // Updates clock elements with the calculated time remaining
    const updateClock = () => {
      const t = getTimeRemaining(endtime);

      daysSpan.textContent = t.days;
      hoursSpan.textContent = ('0' + t.hours).slice(-2);
      minutesSpan.textContent = ('0' + t.minutes).slice(-2);

      if (t.total <= 0 && timeinterval) {
        clearInterval(timeinterval);
      }
    };

    updateClock(); // Run once immediately to avoid delay
    const timeinterval = setInterval(updateClock, 1000);
  };

  // Example usage
  // Initialize the clock with the 'id' and 'date' from `countdownVars`
  if (countdownVars) {
    initializeClock(countdownVars['id'], countdownVars['date']);
  }
});
