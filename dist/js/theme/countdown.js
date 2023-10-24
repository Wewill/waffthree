function docReady(fn) {
  // see if DOM is already available
  if (document.readyState === "complete" || document.readyState === "interactive") {
      // call on next available tick
      setTimeout(fn, 1);
  } else {
      document.addEventListener("DOMContentLoaded", fn);
  }
}

docReady(function() {
  function getTimeRemaining(endtime) {
      const twentyHoursMoreThanMidnight = 18*3600*1000; // in ms 
      const total = Date.parse(endtime) + twentyHoursMoreThanMidnight - Date.parse(new Date());
      console.info("#Countdown > countdownVars > getTimeRemaining::", new Date(endtime), new Date(Date.parse(endtime) + twentyHoursMoreThanMidnight), total );
      //const seconds = Math.floor((total / 1000) % 60);
      const minutes = Math.floor((total / 1000 / 60) % 60);
      const hours = Math.floor((total / (1000 * 60 * 60)) % 24);
      const days = Math.floor(total / (1000 * 60 * 60 * 24));

      return {
        total,
        days,
        hours,
        minutes,
        //seconds
      };
    }


    function initializeClock(id, endtime) {
      const clock = document.getElementById(id);
      const daysSpan = clock.querySelector('.days');
      const hoursSpan = clock.querySelector('.hours');
      const minutesSpan = clock.querySelector('.minutes');
      //const secondsSpan = clock.querySelector('.seconds');

      function updateClock() {
        const t = getTimeRemaining(endtime);

        daysSpan.innerHTML = t.days;
        hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
        minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
        //secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

        if (t.total <= 0) {
          if (timeinterval != null) {
            clearInterval(timeinterval);
          }
        }

      }

      const timeinterval = setInterval(updateClock, 1000);
      updateClock();
    }

    //const deadline = new Date(Date.parse(new Date()) + 15 * 24 * 60 * 60 * 1000);
    //const deadline = 'November 13 2020 10:00:00 GMT+0200';

    initializeClock(countdownVars['id'], countdownVars['date']);
  });