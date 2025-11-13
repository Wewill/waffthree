<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks\Block
 */

namespace WaffTwo\Blocks\Block;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;

function wa_playlist_callback( $attributes ) {
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST ?? true;

	//print_r($attributes);
	//global $current_edition_id, $current_edition_films_are_online;
	//https://codepen.io/tommydunn/pen/rNxQLNq?editors=1010

	// No data no render.
	if ( empty( $attributes['data'] ) ) return;

	// Unique HTML ID if available.
	$id = '';
	if ( $attributes['name'] ) {
		$id = $attributes['name'] . '-';
	} elseif (  $attributes['data']['name'] ) {
		$id = $attributes['data']['name'] . '-';
	}
	$id .= ( $attributes['id'] && $attributes['id'] !== $attributes['name']) ? $attributes['id'] : wp_generate_uuid4();
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}


	// Custom CSS class name.
	$themeClass = 'playlist mt-5 mt-sm-10 mb-5 mb-sm-10 contrast--light';
	$class = $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	?>
	<?php /* #Playlist */ ?>
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
			<hgroup class="text-center">
				<h6 class="headline d-inline-block"><?= mb_get_block_field( 'waff_pl_title' ) ?></h6>
			</hgroup>

			<?php if ( mb_get_block_field( 'waff_pl_leadcontent' ) ) : ?>
			<p class="lead mt-1 mt-sm-3 mb-1 mb-sm-3 text-center w-75 mx-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_pl_leadcontent' )) ?></p>
			<?php endif; ?>

			<?php if ( mb_get_block_field( 'waff_pl_content' ) ) : ?>
			<div class="mt-0 mt-sm-2 mb-1 mb-sm-3 text-center w-75 mx-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_pl_content' )) ?></div>
			<?php endif; ?>

			<?php $classes = ''; if ( mb_get_block_field( 'waff_pl_videos' ) ) :
				$classes 	.= (mb_get_block_field( 'waff_pl_autoplay' ))?' autoplay':'';
				$classes 	.= (mb_get_block_field( 'waff_pl_fullwidth' ))?' fullwidth':'';
			?>
			<div id="<?= ( $attributes['id'] ?? '' ) ?>" class="slider-youtube slick-dark<?= $classes; ?>">

				<?php $idx = 0; foreach ( mb_get_block_field( 'waff_pl_videos' ) as $video_link) :
					$playlist = ( mb_get_block_field( 'waff_pl_playlist' ) != '' )?'&listType=playlist&list='.esc_attr(mb_get_block_field( 'waff_pl_playlist' )):'';
					$idx++;
				?>
					<div class="item youtube-sound">
						<iframe id="<?= $idx; ?>" class="embed-player slide-media" src="https://www.youtube.com/embed<?= esc_attr(parse_url($video_link, PHP_URL_PATH)); ?>?enablejsapi=1&controls=0&fs=0&iv_load_policy=3&rel=0&showinfo=0&loop=1&start=1&origin=https://www.fifam.fr<?= $playlist ?>" frameborder="0" allowfullscreen seamless sandbox="allow-scripts allow-same-origin" allow="autoplay"></iframe>
						<div class="slick-button">
							<a class="btn btn-sm" href="<?= $video_link; ?>"><?= __('See video', 'waff'); ?></a>
						</div>
					</div>
				<?php endforeach; ?>

			</div>
			<?php endif; ?>

		</div>
		<?php /* Local styles */ ?>
		<style>
			.slider-youtube .item {
				opacity: 1;
				filter: blur(0);
				background: #000;
				<?= ( mb_get_block_field( 'waff_pl_fullwidth' )?'height: 50vh;min-height:400px;':'max-height: 200px;'); ?>
				border-radius:5px;
				margin:10px;
			}

			.slider-youtube .item:not(.slick-current) {
				opacity: 0.4;
				transition: opacity 1s;
				border-radius:5px;
				filter: blur(1px);
			}

			.slider-youtube .item iframe {
				width: 100%;
				<?= ( mb_get_block_field( 'waff_pl_fullwidth' )?'height: 50vh;min-height:400px;':'height: 200px;'); ?>
				min-width: 300px;
				<?= ( mb_get_block_field( 'waff_pl_autoplay' )?'pointer-events: none;':''); ?> /* Can slide */
				border-radius:5px;
			}

			.slick-button {
				position: relative;
				bottom: 60px;
				text-align: center;
				z-index: 9999;
			}

			.slick-button a {
				color: white;
				background-color: black;
			}
		</style>
		<?php /* Local scripts */ ?>
		<script>
			// Loads the IFrame Player API code asynchronously.
			var tag = document.createElement('script');

			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

			// POST commands to YouTube or Vimeo API
			function postMessageToPlayer(player, command){
				if (player == null || command == null) return;
				player.contentWindow.postMessage(JSON.stringify(command), "*");
			}

			// INIT
			var slideWrapper = jQuery(".slider-youtube"),
					iframes = slideWrapper.find('.embed-player');

			var autoplay = false;
			var autoplay = document.getElementById('<?= ( $attributes['id'] ?? '' ) ?>').classList.contains('autoplay');
			//console.log(autoplay);

			// When the slide is changing
			function playPauseVideo(slick, control){
				var currentSlide, slideType, startTime, player, video;

				currentSlide = slick.find(".slick-current");
				slideType = currentSlide.attr("class").split(" ")[1];
				player = currentSlide.find("iframe").get(0);
				startTime = currentSlide.data("video-start");

				console.log(slideType);

				if (slideType === "vimeo") {
					switch (control) {
					case "play":
						if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
						currentSlide.addClass('started');
						postMessageToPlayer(player, {
							"method": "setCurrentTime",
							"value" : startTime
						});
						}
						postMessageToPlayer(player, {
						"method": "play",
						"value" : 1
						});
						break;
					case "pause":
						postMessageToPlayer(player, {
						"method": "pause",
						"value": 1
						});
						break;
					}
				} else if (slideType === "youtube-sound") {
					switch (control) {
					case "play":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "playVideo"
						});
						break;
					case "pause":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "pauseVideo"
						});
						break;
					}
				}  else if (slideType === "youtube") {
					switch (control) {
					case "play":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "mute"
						});
						postMessageToPlayer(player, {
						"event": "command",
						"func": "playVideo"
						});
						break;
					case "pause":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "pauseVideo"
						});
						break;
					}
				} else if (slideType === "video") {
					video = currentSlide.children("video").get(0);
					if (video != null) {
					if (control === "play"){
						video.play();
					} else {
						video.pause();
					}
					}
				}
			}

			// Resize player
			// function resizePlayer(iframes, ratio) {
			// 	if (!iframes[0]) return;
			// 	var win = jQuery(".slider-youtube"),
			// 		width = win.width(),
			// 		playerWidth,
			// 		height = win.height(),
			// 		playerHeight,
			// 		ratio = ratio || 16/9;

			// 	iframes.each(function(){
			// 		var current = jQuery(this);
			// 		if (width / ratio < height) {
			// 		playerWidth = Math.ceil(height * ratio);
			// 		current.width(playerWidth).height(height).css({
			// 			left: (width - playerWidth) / 2,
			// 			top: 0
			// 			});
			// 		} else {
			// 		playerHeight = Math.ceil(width / ratio);
			// 		current.width(width).height(playerHeight).css({
			// 			left: 0,
			// 			top: (height - playerHeight) / 2
			// 		});
			// 		}
			// 	});
			// }

			// DOM Ready
			jQuery(function($) {
				// Initialize
				slideWrapper.on("init", function(slick){
					console.log('init /  autoplay : ' + autoplay );
					slick = $(slick.currentTarget);
					setTimeout(function(){
						if ( autoplay == true ) { playPauseVideo(slick,"play"); }
					}, 1000);
					//resizePlayer(iframes, 16/9);
				});
				slideWrapper.on("beforeChange", function(event, slick) {
					console.log('beforeChange /  autoplay : ' + autoplay );
					slick = $(slick.$slider);
					playPauseVideo(slick,"pause");
				});
				slideWrapper.on("afterChange", function(event, slick) {
					console.log('afterChange /  autoplay : ' + autoplay );
					slick = $(slick.$slider);
					if ( autoplay == true ) { playPauseVideo(slick,"play"); }
				});
				/*slideWrapper.on("lazyLoaded", function(event, slick, image, imageSource) {
					lazyCounter++;
					if (lazyCounter === lazyImages.length){
					lazyImages.addClass('show');
					// slideWrapper.slick("slickPlay");
					}
				});*/

				//start the slider
				slideWrapper.slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					dots: true,
					infinite: true,
					<?= ( !mb_get_block_field( 'waff_pl_fullwidth' ))?'centerMode: true,':''  ?>
					// centerMode: true,
					<?= ( !mb_get_block_field( 'waff_pl_fullwidth' ))?'centerPadding: \'50px\',':'' ?>
					// centerPadding: '50px',
					<?= ( !mb_get_block_field( 'waff_pl_fullwidth' ))?'variableWidth: true,':'' ?>
					// variableWidth: true,
					//fade:true,
					//autoplaySpeed:4000,
					//autoplay: true,
					//speed:600,
					//lazyLoad:"progressive",
					cssEase:"cubic-bezier(0.87, 0.03, 0.41, 0.9)"
				});
			});

			// Resize event
			// jQuery(window).on("resize.slickVideoPlayer", function(){
			// 	resizePlayer(iframes, 16/9);
			// });
		</script>
	</section>
	<?php /* END: #Playlist */ ?>
	<?php
}
