"use strict"; // Apply strict mode to the entire script

/**
 * Logo-invert considering scroll position and section class functions file.
 * Call by custom-wp-widget-counter.php
 */

let fps = 2;

// Detect request animation frame
let scroll = window.requestAnimationFrame
  || window.webkitRequestAnimationFrame
  || window.mozRequestAnimationFrame
  || window.msRequestAnimationFrame
  || window.oRequestAnimationFrame
  // IE Fallback, you can even fallback to onscroll
  || function(callback){ window.setTimeout(callback, 1000/fps) };
  let lastPosition = -1;

// my Variables
let lastSection = false;
let replaceItemTop = -1;
let replaceItemBottom = -1;
let replaceItemHeight = -1;

//Start at items Height
let itemHeight = 0; // FIFAM 140 / DINARD 0
let body = document.querySelector("body");
let hasHeight = body.classList.contains('waff-theme-fifam');
if ( hasHeight ) { 
	itemHeight = 140;
} else {
	itemHeight = 0;
}
// console.log("#LOGOINVERT:: itemHeight", itemHeight);

// my sections to calculate stuff
let sections = document.querySelectorAll('main, main > .row, section, header, footer');
let replaceContainer = document.querySelectorAll('.js-replace');
let headerContainer = document.querySelectorAll('header.masthead');
let replaceItem = document.querySelectorAll('.js-replace__item');

 
// The Scroll Function
function loop(){
//    console.info("matchMedia::", window.matchMedia("(max-width:576px)"));
   let isMobilewidth = window.matchMedia("(max-width:576px)");

  // Set a frame rate 
  setTimeout(function() {
	  //let top = window.scrollY;
	  console.info("matchMedia:: setTimeout");

	  if (replaceItem.length > 0) {
	    // get top position of item from container, because image might not have loaded
	    replaceItemTop = parseInt(replaceContainer[0].getBoundingClientRect().top);
	    replaceItemHeight = replaceItem[0].offsetHeight;
	    replaceItemBottom = replaceItemTop + replaceItemHeight;
	  }
	
	  let sectionTop = -1;
	  let sectionBottom = -1;
	  let currentSectionIsDark = null;
	  
	  // Fire when needed
	  if (lastPosition == window.scrollY) {
	    if ( isMobilewidth.matches === false ) scroll(loop);
	    return false;
	  } else {
	    lastPosition = window.scrollY;
	
	  // Your Function
	  Array.prototype.forEach.call(sections, function(el, i){
	    sectionTop = parseInt(el.getBoundingClientRect().top);
	    sectionBottom = parseInt(el.getBoundingClientRect().bottom);

		// console.log('%c #LOGOINVERT:: --- section id + isDark? + top + index', 'color:orange;', el.id, el.classList.contains('contrast--dark'), sectionTop, i);
		// console.log('%c #LOGOINVERT:: Active section', ((sectionTop+itemHeight) <= replaceItemBottom) && ((sectionBottom+itemHeight) > replaceItemTop)?'color:lime;':'color:gray;', sectionTop+itemHeight, replaceItemBottom, sectionBottom+itemHeight, replaceItemTop);
		// active section for contrast 
	    if ( ((sectionTop+itemHeight) <= replaceItemBottom) && ((sectionBottom+itemHeight) > replaceItemTop) ) {
			// check if current section has bg
			currentSectionIsDark = el.classList.contains('contrast--dark');

			// switch class depending on background image
			if ( currentSectionIsDark ) { 
			replaceContainer[0].classList.remove('js-replace--reverse');
			headerContainer[0].classList.add('contrast--reverse');
			// Handle fifam case 
			headerContainer[0].classList.remove('navbar-light'); // Why ? Because fifam does not change color on home homeslide 
			headerContainer[0].classList.add('navbar-dark'); // Handle this case @TODO FIFAM 
			} else {
			replaceContainer[0].classList.add('js-replace--reverse')
			headerContainer[0].classList.remove('contrast--reverse')
			// Handle fifam case 
			headerContainer[0].classList.remove('navbar-dark'); // Why ? Because fifam does not change color on home homeslide 
			headerContainer[0].classList.add('navbar-light'); // Handle this case @TODO FIFAM 
			}
		}
	
	    // if active Section hits replace area
	    if ( (replaceItemTop < sectionTop) && ( sectionTop <= replaceItemBottom) ) {
	      // animate only, if section background changed
	      if (currentSectionIsDark != lastSection)  {
	        //document.documentElement.style.setProperty('--replace-offset', 100 / replaceItemHeight * parseInt(sectionTop - replaceItemTop) + '%');
	        document.documentElement.style.setProperty('--waff-logo-invert-replace-offset', 100 / replaceItemHeight * parseInt(sectionTop - replaceItemTop) + '%'); //WAFFTWO 2
	      }
	    }
	    // end active section in replace area
	
	    // if section above replace area
	    if ( replaceItemTop >= sectionTop ) {
	      // set offset to 0 if you scroll too fast
	      //document.documentElement.style.setProperty('--replace-offset', 0 + '%');
	      document.documentElement.style.setProperty('--waff-logo-invert-replace-offset', 0 + '%'); //WAFFTWO 2
	      // set last section to current section
	      lastSection = currentSectionIsDark;
	    }
	
	  }); 
	
	}
	
	// Recall the loop
	if ( isMobilewidth.matches === false ) scroll(loop); // Do not recall if mobile. 
  }, 1000 / fps); // Limit fps
  // END: setTimeout
}

// Call the loop for the first time
loop();

// Call the loop on resize
window.onresize = function(event) {
	loop();
};

// Call the loop after AOS animation if the first section appear and it's white
document.addEventListener('aos:in:pagetitle', ({ detail }) => {
	setTimeout(function(){
		// Reset position to fake a scroll
		lastPosition = -1;
		// Call the loop  
		loop();
	}, 600);
});
document.addEventListener('aos:out:pagetitle', ({ detail }) => {
  // Reset position to fake a scroll
  lastPosition = -1;
  // Call the loop  
  loop();
});
document.addEventListener('aos:in:pageheader', ({ detail }) => {
	setTimeout(function(){
		// Reset position to fake a scroll
		lastPosition = -1;
		// Call the loop  
		loop();
	}, 600);
});
document.addEventListener('aos:out:pageheader', ({ detail }) => {
  // Reset position to fake a scroll
  lastPosition = -1;
  // Call the loop  
  loop();
});

// Call the loop after an slick slider animation on init and on change
let slickHomeslideChange = function(slick,i) {
  let elSlide = jQuery(slick.$slides[i]);
  // Check classes 
  if( elSlide.hasClass('contrast--dark') ) {
  	jQuery('section#slick-homeslide').removeClass('contrast--light').addClass('contrast--dark');
  } else {
  	jQuery('section#slick-homeslide').removeClass('contrast--dark').addClass('contrast--light');

  }	
  // Reset position to fake a scroll
  lastPosition = -1;
  // Call the loop  
  loop();
};

// Home slide > Events
jQuery('section#slick-homeslide .slider-nav').on('init', function(event,slick){
	slickHomeslideChange(slick,0);
});

jQuery('section#slick-homeslide .slider-nav').on('afterChange', function(event,slick,i){
	slickHomeslideChange(slick,i);
});