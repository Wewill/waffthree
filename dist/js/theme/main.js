/*
	-------------------------------------------------
	JS to CSS calculate real VH
	-------------------------------------------------
*/

// VH hack 
// First we get the viewport height and we multiple it by 1% to get a value for a vh unit
let vh = window.innerHeight * 0.01;
// Then we set the value in the --vh custom property to the root of the document
document.documentElement.style.setProperty('--vh', `${vh}px`);
document.documentElement.style.setProperty('--vh40', `${Math.round(vh*40)}px`)
document.documentElement.style.setProperty('--vh50', `${Math.round(vh*50)}px`)
document.documentElement.style.setProperty('--vh60', `${Math.round(vh*60)}px`)
document.documentElement.style.setProperty('--vh100', `${window.innerHeight}px`)

/*
	-------------------------------------------------
	Dom ready
	-------------------------------------------------
*/		
		
//jQuery(function() { 		
jQuery(document).ready(function() {
	// When dom is ready 
	console.log('#Dom is ready');
		
	/*
		-------------------------------------------------
		Init animations
		-------------------------------------------------
		https://michalsnik.github.io/aos/
	*/
	AOS.init({
		offset: 	0, // offset (in px) from the original trigger point
		disable: 	'mobile', //disabled on mobiles 
		once: 		true, // play once only
	});


	/*
		-------------------------------------------------
		Init Lazy loading of images
		-------------------------------------------------
		http://jquery.eisbehr.de/lazy/example_use-srcset-and-sizes
	*/
    jQuery('.lazy').Lazy({ effect: 'fadeIn'});


	/*
		-------------------------------------------------
		Init tootltip & popovers
		-------------------------------------------------
		http://jquery.eisbehr.de/lazy/example_use-srcset-and-sizes
	*/ 
	// jQuery('[data-toggle="tooltip"]').tooltip(); 
	// jQuery('[data-toggle="popover"]').popover(); 
	// Tooltip(s)
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
	});
	// Popover(s)
	var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
	var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
		return new bootstrap.Popover(popoverTriggerEl)
	});
	// Collapse(s) 
	// var collapseElementList = [].slice.call(document.querySelectorAll('.collapse'))
	// var collapseList = collapseElementList.map(function (collapseEl) {
		// return new bootstrap.Collapse(collapseEl)
	// });


	/*
		-------------------------------------------------
		Close modal
		-------------------------------------------------
	*/ 
	jQuery('#programmationModal').on('shown.bs.modal', function(e){
		console.log('#programmationModal: Show');
		jQuery('.toggle-programmation').on('click', function(e){
			e.preventDefault();
			jQuery('#programmationModal').trigger('click');
		});
	});

	/*
		-------------------------------------------------
		Init fit content via fitty v2.3.2 / Edition Badge
		-------------------------------------------------
		https://npm.io/package/fitty
	*/
	var fitEditionBadge = fitty('.edition-badge');
	if (fitEditionBadge.length) {
		// Add Listener 
		fitEditionBadge.forEach(fits => fits.element.addEventListener('fit', function(e) {
			console.log('#FITTY: Fit Edition badges');
			document.querySelectorAll(".fit-hide").forEach(el => el.remove());
		}));
	
		// In a popup > force refit
		jQuery('#navbarToggleExternalContent').on('show.bs.collapse', function () {
			console.log('#FITTY: Show edition badge');
			fitEditionBadge.forEach(fits => fits.fit());		
		});
	}
	
	/*
		-------------------------------------------------
		Add navbar class to body when toggled 
		-------------------------------------------------
	*/

	// var _offcanvas = document.getElementById('offcanvas')
	// var bsOffcanvas = new bootstrap.Offcanvas(_offcanvas)

	document.body.classList.add('navbar-external-close');
	var _navbarToggleExternalContent = document.getElementById('navbarToggleExternalContent');
	if (_navbarToggleExternalContent != null) {
		_navbarToggleExternalContent.addEventListener('show.bs.collapse', function () {
			document.body.className = document.body.className.replace('navbar-external-close','navbar-external-open');
			// bsOffcanvas.show();
			// document.getElementById(_offcanvas).classList.add('show');
			// document.body.classList.add('offcanvas-active');
		});
		_navbarToggleExternalContent.addEventListener('hide.bs.collapse', function () {
			document.body.className = document.body.className.replace('navbar-external-open','navbar-external-close');
			// bsOffcanvas.hide();
			// document.getElementById(_offcanvas).classList.remove('show');
			// document.body.classList.aremovedd('offcanvas-active');
		});
	}

	/*
		-------------------------------------------------
		Init Programmation scroll spy
		-------------------------------------------------
		http://jquery.eisbehr.de/lazy/example_use-srcset-and-sizes
	*/
	var nav = jQuery('#navProgrammationModal');
	var link = jQuery('#navProgrammationModal a');
	var scroll = jQuery('#programmationModalToScroll');
	var offset = 72.015625;
	
	// Move to specific section when click on menu link
	link.on('click', function(e) {
		var target = jQuery(jQuery(this).attr('href')); // Get .scrollspy ID		
		/* console.log(target); console.log('#SCROLLSPY position:' + target.position().top); console.log('#SCROLLSPY offset:' + target.offset().top); console.log('#SCROLLSPY scrollTop:' + scroll.scrollTop()); console.log('#SCROLLSPY OK position + scrolltop - offset:' + (( target.position().top + scroll.scrollTop() ) - offset ) ); */

		jQuery('#programmationModalToScroll').animate({
			scrollTop: (( target.position().top + scroll.scrollTop() ) - offset )
		}, 300);
		link.removeClass('active');
		jQuery(this).addClass('active');
		e.preventDefault();
	});
	
	
	jQuery('#programmationModal').on('shown.bs.modal', function(e){
	  // Run the scrNav when scroll
	  scroll.on('scroll', function(encodeURI){
		e.preventDefault();
		// Listening to the scroll 
		//console.log('#SCROLLSPY scroll scrollTop:' + jQuery(this).scrollTop());
	    scrNav();
	  });
	  
	  // scrNav function 
	  // Change active dot according to the active section in the window
	  function scrNav() {
	    var sTop = scroll.scrollTop();
	    jQuery('.scrollspy').each(function() {
	      var id = jQuery(this).attr('id'), // Get .scrollspy ID
	          coffset = (( jQuery(this).position().top + scroll.scrollTop() ) - offset ),
	          height = jQuery(this).parent().parent().height();
		  //console.log('#SCROLLPSY/SCRNAV for:' + id +' / sTop:'+ sTop +' / coffset:'+ coffset +' / height:'+ height )
	      if(sTop+1 >= Math.round(coffset) && sTop+1 < Math.round(coffset) + Math.round(height)) {
	        link.removeClass('active');
	        nav.find('[data-scroll="' + id + '"]').addClass('active');
	      }
	    });
	  }
	  scrNav();
	});


	/*
		-------------------------------------------------
		Init Toggle Affix Navbar 
		-------------------------------------------------
		Permet de laisser la navbar fixée 
	*/
	// Function
	var toggleAffix = function(affixElement, scrollElement, wrapper) {
		var height = affixElement.outerHeight(),
		    top = wrapper.offset().top + (window.innerHeight/2) - height;
		
		if (scrollElement.scrollTop() >= top){
		    //wrapper.height(height);
		    affixElement.addClass("affix");
			document.body.classList.add('navbar-affix');
		} else {
		    //wrapper.height('auto');
		    affixElement.removeClass("affix");
			document.body.classList.remove('navbar-affix');
		}
	};
  
	jQuery('[data-toggle="affix"]').each(function() {
		var ele = jQuery(this),
		    wrapper = jQuery('<div></div>');
		ele.before(wrapper);
		
		var wrapper = jQuery('#slick-homeslide, #pagetitle, #pageheader');
		jQuery(window).on('scroll resize', function() {
		    toggleAffix(ele, jQuery(this), wrapper);
		});
		
		// init
		toggleAffix(ele, jQuery(window), wrapper);
	});


	/*
		-------------------------------------------------
		Make responsive VH / fix it for mobiles 
		-------------------------------------------------
		http://jquery.eisbehr.de/lazy/example_use-srcset-and-sizes
	*/
	// Function
  	// Fix vh-100 bug for img-shifted
	var fixVH = function(ele, src, trg, e) {
		var eleHeight = ele.outerHeight(),
		srcHeight = src.outerHeight(),
		trgHeight = trg.outerHeight();
		
		var windowInnerW = window.innerWidth;
		console.log('#fixVH:' + windowInnerW + ' / srcHeight:' + srcHeight + ' / trgHeight:' + trgHeight + ' / event:' + e );
//		console.log(src);
		
		if ( window.innerWidth < 768 ) {
			jQuery(trg).attr('style', 'height:'+windowInnerW+'px!important');
		} else {
			if ( srcHeight > trgHeight ) {
				jQuery(trg).attr('style', 'height:'+srcHeight+'px!important');
			}
		}
	}
	// 50
	jQuery('.fix-vh-50').each(function() {
		var ele = jQuery(this) // Section el wrapper
		var src = jQuery(this).find('.min-h-50'); // Source
		var trg = jQuery(this).find('.vh-50'); // Target
		
		jQuery(window).on('resize', function() {
		    fixVH(ele, src, trg, 'resize');
		});
		
		// init
		setTimeout( function() { fixVH(ele, src, trg, 'init') }, 100);
	});
	//100
	jQuery('.fix-vh-100').each(function() {
		var ele = jQuery(this) // Section el wrapper
		var src = jQuery(this).find('.min-h-100'); // Source
		var trg = jQuery(this).find('.vh-100'); // Target
		
		jQuery(window).on('resize', function() {
		    fixVH(ele, src, trg, 'resize');
		});
		
		// init
		setTimeout( function() { fixVH(ele, src, trg, 'init') }, 100);
	});


	/*
		-------------------------------------------------
		Init Slick sliders 
		-------------------------------------------------
		https://kenwheeler.github.io/slick/
	*/
	// Home slide > Master Slick Slide 
	jQuery('#slick-homeslide .slider-nav').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		asNavFor: '#slick-homeslide  .slider-for',
		arrows: true,
		dots: false,
		//vertical:true,
		focusOnSelect: true,
		pauseOnFocus: false, 
		pauseOnHover: false,
		autoplay: true,
		autoplaySpeed: 5000
	});
	
	// Home slide > As for			
	jQuery('#slick-homeslide .slider-for').slick({
	  slidesToShow: 1,
	  slidesToScroll: 1,
	  arrows: false,
	  dots: false,
	  vertical: true,
	  asNavFor: '#slick-homeslide  .slider-nav'
	});
	
	// Home slide > Events
	jQuery('.slider-for').on('afterChange', function(event,slick,i){ 
	    jQuery('.slider-list li').removeClass('slick-current').removeClass('active'); 
	    jQuery('.slider-list li').eq(i).addClass('slick-current').addClass('active');
	}); 
	//set active class to first slide 
	jQuery('.slider-list li').eq(0).addClass('slick-current').addClass('active'); 
	
	//Clicks 
	jQuery('.slider-list li').click(function(e){
	    e.preventDefault();
		jQuery('#slick-homeslide .slider-nav').slick('slickGoTo', parseInt(jQuery(this).index()), false);
	});
	 
	
	// Carousel > master Slick Slide 				
	jQuery('.slick-carousel').slick({
		arrows: true,
		dots: false,
		buttons: false,
		centerMode: true,
		centerPadding: '200px',
		infinite: true,
		speed: 200,
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true, 
		autoplaySpeed: 3000,
		focusOnSelect:true,
		lazyLoad: 'ondemand',
		appendArrows: '.slick-carousel-arrows',
		
		responsive: [
		    {
		      breakpoint: 1200,
		      settings: {
				centerPadding: '100px',
		      }
		    },
		    {
		      breakpoint: 992,
		      settings: {
				centerPadding: '0px',
		      }
		    },
		    {
		      breakpoint: 768,
		      settings: {
				centerPadding: '0px',
		       }
		    } 
		]
	});
	
	// Samedays > master Slick Slide 
	// Instead of # > Views integration 03022021	
	jQuery('.slick-carousel-samedays').slick({
		arrows: true,
		dots: true,
		buttons: false,
		//centerMode: true,
		//centerPadding: '200px',
		infinite: true,
		speed: 200,
		slidesToShow: 4,
		slidesToScroll: 1,
		autoplay: true, 
		autoplaySpeed: 3000,
		focusOnSelect:true,
		lazyLoad: 'ondemand',
		appendArrows: '.slick-carousel-samedays-arrows',
		
		responsive: [
		    {
		      breakpoint: 1200,
		      settings: {
				slidesToShow: 3,
		      }
		    },
		    {
		      breakpoint: 992,
		      settings: {
				slidesToShow: 2,
		      }
		    },
		    {
		      breakpoint: 768,
		      settings: {
				slidesToShow: 1,
		       }
		    } 
		]
	});
	
	jQuery('.slick-carousel-samedays .slick-active').last().css('opacity', '0.5'); // Instead of # > Views integration 03022021


	// Partners Slick Slide 
	jQuery('#slick-partners').slick({
		arrows: false,
		infinite: true,
		focusOnSelect: true,
		pauseOnFocus: false, 
		pauseOnHover: false,
		slidesToShow: 15,
		slidesToScroll: 1,
		autoplay: true, 
		autoplaySpeed: 3000,
		//focusOnSelect:true,
		lazyLoad: 'ondemand',
		responsive: [
		    {
		      breakpoint: 1400,
		      settings: {
				slidesToShow: 12,
				slidesToScroll: 1,
		      }
		    },
		    {
		      breakpoint: 1200,
		      settings: {
				slidesToShow: 8,
				slidesToScroll: 3,
		      }
		    },
		    {
		      breakpoint: 992,
		      settings: {
				slidesToShow: 6,
				slidesToScroll: 3,
		      }
		    },
		    {
		      breakpoint: 768,
		      settings: {
				slidesToShow: 4,
				slidesToScroll: 4,
		      }
		    } 
		]
	});
			
	// Flash Slick Slide 
	// When the page has loaded
	jQuery('#flash').fadeOut().removeClass('d-none').fadeIn(600);
	
	
	jQuery('#flash').slick({
		slidesToShow: 1,
		slidesToScroll: 1, 
		infinite: true, 
		arrows: false,
		dots: false,
		buttons: false,
		autoplay: true, 
		autoplaySpeed: 6000
	});
	
	/*
		-------------------------------------------------
		Init Stacked cards 
		-------------------------------------------------
		https://kenwheeler.github.io/slick/
	*/
	var $card = jQuery('.stacked-cards .card');
	var lastCard = jQuery(".stacked-cards .card-list .card").length - 1;
	var running = false;
	
	jQuery('#stacked-cards-next').click(function(){ 
		running = true;
		var prependList = function() {
			if( jQuery('.stacked-cards .card').hasClass('activeNow') ) {
				var $slicedCard = jQuery('.stacked-cards .card').slice(lastCard).removeClass('transformThis activeNow');
				jQuery('.stacked-cards ul').prepend($slicedCard);
				running = false;
			}
		}
		jQuery('.stacked-cards li').last().removeClass('transformPrev').addClass('transformThis').prev().addClass('activeNow');
		setTimeout(function(){prependList(); }, 150);
	});
	
	jQuery('#stacked-cards-prev').click(function() {
		running = true;
		var appendToList = function() {
			if( jQuery('.stacked-cards .card').hasClass('activeNow') ) {
				var $slicedCard = jQuery('.stacked-cards .card').slice(0, 1).addClass('transformPrev');
				jQuery('.stacked-cards .card-list').append($slicedCard);
				running = false;
			}}
		
				jQuery('.stacked-cards li').removeClass('transformPrev').last().addClass('activeNow').prevAll().removeClass('activeNow');
		setTimeout(function(){appendToList();}, 150);
	});

	var cardsAutoPlay = setInterval(function() {
		if (running == false) {
			running = true;
			var prependList = function() {
				if( jQuery('.stacked-cards .card').hasClass('activeNow') ) {
					var $slicedCard = jQuery('.stacked-cards .card').slice(lastCard).removeClass('transformThis activeNow');
					jQuery('.stacked-cards ul').prepend($slicedCard);
					running = false;
				}
			}
			jQuery('.stacked-cards li').last().removeClass('transformPrev').addClass('transformThis').prev().addClass('activeNow');
			setTimeout(function(){prependList(); }, 150);
		}
	}, 3000);

	/*
		-------------------------------------------------
		Collapse on hover main-nav  
		-------------------------------------------------
	*/
	function toggleCollapse (e) {
		const _d = jQuery(e.target),
			_t = jQuery(e.target).attr('aria-controls'),
			_m = jQuery('.collapse-menu#' + _t),
			_mp = jQuery(_d).parents('nav'),
			_sp = jQuery(_m).parents('nav'),
			_rp = jQuery(_d).parents('.row');
			
		const _a = jQuery('nav .collapse-menu.show'),
			  _h = _a.length;

			
		// console.log('toggleCollapse:' + _t + ' / '+ e.type + ' how many hover ? ' + _h + ' row is hover ? '+ _rp.is(':hover') + ' / ' + _d.is(':hover'));
		/*console.log( _d );
		console.log( _m );
		console.log( _mp );
		console.log( _sp );
		console.log( _rp );
		console.log( _a );
		console.log( _h );*/
		
		if ( _h > 0 && _rp.is(':hover') && e.type === 'mouseenter' ) {
			//console.log('##DOIT DISPARAITRE');
			_a.toggleClass('show', false);
		}
		
		time = setTimeout(function(){
			//console.log('toggleCollapse:timeout:' + _h + ' / ' + e.type + ' / ' + _rp.is(':hover') + ' / ' + _d.is(':hover'));
			const shouldOpen = e.type !== 'click' && ( _d.is(':hover') || _rp.is(':hover') ) ;
			const expanded = _d.attr('aria-expanded') == 'true' && ( _d.is(':hover') || _rp.is(':hover') ) ;
			//_m.toggleClass('show', shouldOpen);
			//console.log('shouldOpen:' + shouldOpen );
			if ( shouldOpen === true ) {
				//console.log('expanded:' + expanded);
				if ( expanded === false && _h < 0) {
					_m.collapse('show');
					_d.toggleClass('show', true);
				} else {
					_a.toggleClass('show', false);
					_m.toggleClass('show', shouldOpen)
					_d.toggleClass('show', shouldOpen);
				}
			} else {
				_a.toggleClass('show', false);
				_m.collapse('hide');
				_d.toggleClass('show', false);
			}
			jQuery(_d).attr('aria-expanded', shouldOpen);
			jQuery(_rp).on('mouseleave',isRowHover);
		}, ( e.type === 'mouseleave' && !_rp.is(':hover') ) ? 3000 : 10);
		
		//( e.type === 'mouseleave' && _rp.is(':hover') && _h > 0 )
	}
	
	function isRowHover (e) {
		const _rp = jQuery(e.target),
			_a = jQuery('nav .collapse-menu.show'),
			_h = _a.length;
			
		//console.log('isRowHover:' + _h + ' / ' + e.type + ' / ' + _rp.is(':hover'))

		setTimeout(function(){
			//console.log('isRowHover:timeout:' + _h + ' / ' + e.type + ' / ' + _rp.is(':hover'));
			if ( _h > 0 && _rp.is(':hover') === false && e.type === 'mouseout' ) {
				//console.log('##DOIT DISPARAITRE DEFINITIF');
				//_a.toggleClass('show', false);
				_a.collapse('hide');
			}
		}, 3000);
	}
	
	jQuery('body')
	  .on('mouseenter mouseleave','nav#main-nav a',toggleCollapse)
	  //.on('mouseenter mouseleave','nav#main-nav',isRowHover)
	  //.on('mouseenter mouseleave','nav#sub-nav',isRowHover)
	  //.on('click', '.dropdown-menu a', toggleCollapse);

}); // End dom ready