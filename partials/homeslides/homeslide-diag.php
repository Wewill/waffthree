<?php
$prefix 	= 'waff_homeslide_';
$random = 0;
$slide_count    = intval( 10 );

$slide_order_by 	= $random == '1' ? 'rand' : 'menu_order, post_date';
$args             = array(
    'post_type'             => 'homeslide',
    'post_status'           => 'publish',
    //'homeslide-category' 	=> $slide_category,
    'posts_per_page'        => $slide_count,
    //'post__in'            => $post_array,
    'orderby'				=> $slide_order_by,
);

$homeslide_slides 	= new WP_Query( $args );
$slides_count       = $homeslide_slides->found_posts;
$slide_nb 			= 0;
$slide_images 		= array();
$slide_colors 		= array();
?>

<?php if ( $homeslide_slides->have_posts() ) : ?>

<!-- #slick-homeslide -->
<section id="slick-homeslide" class="contrast--light position-relative has-gradient" data-aos="fade-down">
	<div class="container-fluid px-0">

		<!-- slick-homeslide content -->
		<div class="row g-0 align-items-center vh-100 min-h-600-px"> <!-- .vh-100 hack--> 

			<div class="col-12 col-sm-6 zi-2" data-aos="fade-right">
				<!-- slick-homeslide content -->
				<div class="slider-for">
					<?php 
					$slide_nb 			= 0;
					while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
							$slide_nb ++; 
							$slide_id 	= $post->ID;
							$slide_title = get_the_title();
							$_slide_title = str_replace('<br/>', '&nbsp;<WBR>', $slide_title); //&nbsp;<WBR>
							$_slide_title = str_replace('<br>', '&nbsp;<WBR>', $slide_title); //&nbsp;<WBR>
							$_slide_title = str_replace('<>', '<br/>', $slide_title); //&nbsp;<WBR> >>> This one is working great 

							$content 	= apply_filters('the_content', $post->post_content);
							$content 	= force_balance_tags( html_entity_decode( wp_trim_words( htmlentities($content), 20, '...' ) ) );

							$label 		= rwmb_meta( $prefix . 'label' , array(), $slide_id);
							$mode 		= rwmb_meta( $prefix . 'slide_mode' , array(), $slide_id);
							$text_mode 	= rwmb_meta( $prefix . 'text_slide_mode' , array(), $slide_id);

							$class 		= ( $mode == 'dark' )?'text-light link-light':'text-dark link-dark'; //|| $color == ''  //text-white link-white
							$class 		= ( $text_mode == 'light' )?'text-light link-light':$class; //|| $color == '' 
							$class 		= ( $text_mode == 'dark' )?'text-dark link-dark':$class; //|| $color == '' 

							$url		= rwmb_meta( $prefix . 'slide_url' , array(), $slide_id);
							$color 		= rwmb_meta( $prefix . 'slide_color' , array(), $slide_id);
							$style 		= ( $color )?'style="background-image:linear-gradient(to right, '.Go\hex_to_rgb($color, '0.95').', '.Go\hex_to_rgb($color, '0.000000000001').'"':'';							
					?>

						<!-- Slide <?= $slide_nb ?> -->
						<div class="d-flex flex-column justify-content-end bg-gradient-action-1 border-0 --text-dark vh-100 min-h-600-px position-relative" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" data-slide-title="<?= $slide_title; ?>" <?= $style; ?>>
							<div class="ps-3 ps-md-5 pt-4"><?= (($label != '')?'<h6 class="headline d-inline '.$class.'">'.$label.'</h6>':''); ?></div>
							<div class="ps-3 ps-md-5 py-1">
								<h1 class="f-20 <?= $class ?> w-60"><?= $_slide_title; ?></h1>
							</div>
							<div class="ps-3 ps-md-5 pb-3 --pt-1 opacity-50">
								<div class="mb-1 d-none d-sm-block <?= $class ?> w-60"><?= $content; ?></div>
								<?= (($url != '')?'<a href="'.$url.'" class="card-link '.$class.' stretched-link d-block pt-3"><i class="icon icon-arrow"></i></a>':''); ?>
							</div>
						</div>	
							
					<?php endwhile; ?>

				</div>
				<!-- end: slick-homeslide content -->
			</div>

		</div>
		<!-- end: slick-homeslide content -->

		<!-- slick-homeslide images-->
		<div class="position-absolute top-0 start-0 vh-100 min-h-600-px w-100 overflow-hidden">

			<!-- Images-->
			<div class="slider-nav">
				<?php while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
					$slide_nb ++; 
					$slide_id 	= $post->ID;
					$mode 		= rwmb_meta( $prefix . 'slide_mode' , array(), $slide_id);
					$mode 		= ($mode=='dark')?'contrast--dark':'contrast--light';
					$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full');
					$slide_images[$slide_nb] = array( 'full' => $featured_img_url );
					$videos 		= rwmb_meta( $prefix . 'video' , array('limit' => 1), $slide_id);
					$video 			= reset($videos);
				?>
				<!-- Slide <?= $slide_nb ?> -->
				<div class="img-shifted shift-left vh-100 <?= $mode; ?>">
					<div data-index="<?= $slide_nb ?>" class="slider-item-<?= $slide_nb ?> bg-image bg-cover bg-position-center-center" data-style="background-image: url('<?= $featured_img_url; ?>');">
						<?php if (!empty($video)): ?>
							<figure class="wp-block-video h-100">
								<video class="w-auto h-100 bg-cover" autoplay loop playsinline src="<?= $video['src']; ?>"></video>
							</figure>
						<?php endif; ?>
					</div>
				</div>
				<?php endwhile; ?>

			</div>

			<!-- Images sources-->
			<style scoped type="text/css">
					/*S = 798x755 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['full']; ?>') }
					<?php endforeach; ?>
				@media (min-resolution: 192dpi) {
					/*Sx2 = 1596x1510 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['full']; ?>') }
					<?php endforeach; ?>
				}
				
				@media (min-width: 769px) {
					/*M = 1400x1325 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['full']; ?>') }
					<?php endforeach; ?>
				}
				@media (min-width: 769px) and (min-resolution: 192dpi) {
					/*Mx2 = 2800x2650 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['full']; ?>') }
					<?php endforeach; ?>
				}
				
				@media (min-width: 1400px) {
					/*XL = 1960x1855*/
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['full']; ?>') }
					<?php endforeach; ?>
				}
				@media (min-width: 1400px) and (min-resolution: 192dpi) {
					/*XLx2 = 3920x3710 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['full']; ?>') }
					<?php endforeach; ?>
				}
			</style>	
			
		</div>
		<!-- end: slick-homeslide images-->
			
		<!-- slick-homeslide nav-->
		<div class="position-absolute bottom-0 start-0 p-5 mb-10 zi-2 slick-homeslide-list-items d-none">

			<!-- Slide nav list -->
			<div class="btn-group m-0 slider-list rounded-full" role="group" aria-label="Slider navigation">
				<?php 
				$slide_nb 			= 0;
				while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
					$slide_nb ++; 
					$slide_id 	= $post->ID;
					$slide_title = get_the_title();
					$slide_title = str_replace('<br/>', ' ', $slide_title);
					$slide_title = str_replace('<br>', ' ', $slide_title);
					$color 		= rwmb_meta( $prefix . 'slide_color' , array(), $slide_id);
					$slide_colors[$slide_nb] = $color;
					$style 		= ( $color )?'style="background-color:'.$color.'!important;"':'';
				?>
				<li id="slide-list-<?= $slide_nb ?>" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" class="btn btn-color-light px-4 py-3 btn-heading"><span class="bullet"></span> <?= $slide_title; ?></li>
				<?php endwhile; ?>
			</div>
			<style scoped type="text/css">
				<?php foreach ($slide_colors as $slide => $color) : ?>
				#slide-list-<?= $slide; ?>.active {color: <?= $color ?> !important; background-color: white;}
				#slide-list-<?= $slide; ?>.active .bullet {background-color: <?= $color ?> !important;}
				<?php endforeach; ?>
			</style>

		</div>
		<!-- end: slick-homeslide nav-->

		<!-- Mouse down -->
		<div class="scroll-downs position-absolute bottom-0 start-50 mb-3"><div class="mousey"><div class="scroller"></div></div></div>

		<div class="position-absolute top-0 start-0 zi-2 w-100 h-100">
			<div class="d-flex align-items-center justify-content-between h-100">
				<!-- Logo -->
				<div class="flex-equal text-start">
					<div class="d-flex flex-column w-75">
						<a class="bg-action-3 color-action-1 py-3 px-5 py-sm-4 px-sm-9 rounded-start rounded-pill text-white h4 text-end" href="#">Prendre rendez-vous <i class="bi bi-alarm"></i></a>
						<a class="bg-color-gray py-3 px-5 py-sm-4 px-sm-9 rounded-start rounded-pill text-white h4 text-end" href="#">Régler en ligne <i class="icon icon"></i></a>
						<a class="bg-action-2 py-3 px-5 py-sm-4 px-sm-9 rounded-start rounded-pill text-white h4 text-end" href="#">Consulter mes résultats <i class="icon icon"></i></a>				
					</div>
				</div>

				<!-- Logo -->
				<div class="m-2 ms-4 me-4 flex-center d-none d-md-block" data-aos="fade-down" data-aos-delay="100">
					<div class="logo"><img src="<?= get_stylesheet_directory_uri(); ?><?= get_theme_mod( 'svglogo_light_url' ); ?>" width="350" height="100%"></div>
					<?php /* Go\display_site_branding( array( 'description' => false ) ); */ ?>
				</div>

				<!-- Shape -->
				<div class="flex-equal text-end d-none d-lg-block">
					<img src="<?= get_stylesheet_directory_uri(); ?>/dist/images/forme_diag.png"  width="300" height="100%" />
				</div>
			</div>
		</div>


	</div>
</section>

<?php endif; // have posts ?>