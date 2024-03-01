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
    //'post__in'              => $post_array,
    'orderby'				=> $slide_order_by,
);

$homeslide_slides 	= new WP_Query( $args );
$slides_count       = $homeslide_slides->found_posts;
$slide_nb 			= 0;
$slide_images 		= array();
$slide_colors 		= array();

// Homeslide image 
$homeslide_images = WaffTwo\Theme\waff_get_theme_homeslide_background();
$homeslide_image = reset( $homeslide_images );

?>

<?php if ( $homeslide_slides->have_posts() ) : ?>
<!-- #slick-homeslide -->
<section id="slick-homeslide" class="mb-0 mb-sm-10 mb-lg-7 contrast--light ">
	<div class="container-fluid px-0">
		<div class="row g-0 align-items-center vh-100"> <!-- .vh-100 hack--> 
			<div class="col-lg order-last overflow-hidden" data-aos="fade-right">
				
				<!-- slick-homeslide images-->
				<div class="slider-nav">
					<?php while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
						$slide_nb ++; 
				    	$slide_id 		= $post->ID;
				    	$mode 			= rwmb_meta( $prefix . 'slide_mode' , array(), $slide_id);
				    	$mode 			= ($mode=='dark')?'contrast--dark':'contrast--light';
						// BEGIN Fix full sizes from #43 
						// Post Thumbnail
						$featured_img_urls = array();
						$homeslide_image_sizes = array( 
							'homeslide-featured-image', 
							'homeslide-featured-image-x2',
							'homeslide-featured-image-m', 
							'homeslide-featured-image-m-x2',
							'homeslide-featured-image-s', 
							'homeslide-featured-image-s-x2',
						); 		
						$selected_featured_sizes = $homeslide_image_sizes;
						if ( has_post_thumbnail($post) ) {  //is_singular() &&
							$featured_img_id     		= get_post_thumbnail_id($post);
							$featured_img_url_full 		= get_the_post_thumbnail_url($post);
							foreach ($selected_featured_sizes as $size) {
								$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
								$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full; 
							}
						}
						$featured_img_caption = $post->post_title;

						//$slide_images[$slide_nb] = array( 'full' => $featured_img_url_full, 'post-featured-image-s' => $s, 'post-featured-image-m' => $m, 'post-featured-image' => $x );
						$slide_images[$slide_nb] = $featured_img_urls;
						// END Fix full sizes from #43
				    	$videos 		= rwmb_meta( $prefix . 'video' , array('limit' => 1), $slide_id);
						$video 			= reset($videos);
					?>
					<!-- Slide <?= $slide_nb ?> -->
					<div class="img-shifted shift-left vh-100 <?= $mode; ?>">
						<div data-index="<?= $slide_nb ?>" class="slider-item-<?= $slide_nb ?> bg-image bg-cover bg-position-center-center rounded-top-4 rounded-top-left-0" data-style="background-image: url('<?= $featured_img_url; ?>');">
						<?php if (!empty($video)): ?>
							<figure class="wp-block-video h-100">
								<video class="w-auto h-100 bg-cover" autoplay loop playsinline src="<?= $video['src']; ?>"></video>
							</figure>
						<?php endif; ?>
						</div>
					</div>
					<?php endwhile; ?>

				</div>

				<!-- Special RSFP -->
				<div class="position-absolute top-50 end-0 translate-middle-y opacity-50 --op-2"><img src="<?= $homeslide_image['url']; ?>" /></div>

				<!-- Images sources-->
				<style scoped type="text/css">
					/*S = 798x755 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['homeslide-featured-image-s']; ?>') }
					<?php endforeach; ?>
				@media (min-resolution: 192dpi) {
					/*Sx2 = 1596x1510 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['homeslide-featured-image-s-x2']; ?>') }
					<?php endforeach; ?>
				}
				
				@media (min-width: 769px) {
					/*M = 1400x1325 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['homeslide-featured-image-m']; ?>') }
					<?php endforeach; ?>
				}
				@media (min-width: 769px) and (min-resolution: 192dpi) {
					/*Mx2 = 2800x2650 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['homeslide-featured-image-m-x2']; ?>') }
					<?php endforeach; ?>
				}
				
				@media (min-width: 1400px) {
					/*XL = 1960x1855*/
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['homeslide-featured-image']; ?>') }
					<?php endforeach; ?>
				}
				@media (min-width: 1400px) and (min-resolution: 192dpi) {
					/*XLx2 = 3920x3710 */
					<?php foreach ($slide_images as $slide => $image) : ?>
					.slider-nav .slider-item-<?= $slide ?>.bg-image { background-image: url('<?= $image['homeslide-featured-image-x2']; ?>') }
					<?php endforeach; ?>
				}
			</style>	
					
			</div>
			<div class="col-lg-5 col-xl-5 order-first" data-aos="fade-down">

				<!-- slick-homeslide content -->
				<div class="slider-for">
					
					<?php 
					$slide_nb 			= 0;
					while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
							$slide_nb ++; 
					    	$slide_id 		= $post->ID;
					    	$slide_title 	= get_the_title();
					    	$slide_title 	= str_replace('<br/>', '&nbsp;<WBR>', $slide_title); //&nbsp;<WBR>
							$content 		= apply_filters('the_content', $post->post_content);
							$content 		= wp_strip_all_tags($content);
							if ( strlen($content) > 160 ) {
								$content = substr($content, 0, 160);
								$content = substr($content, 0, strrpos($content, ' ')) . '...';
							}
							$label 			= rwmb_meta( $prefix . 'label' , array(), $slide_id);
							$mode 			= rwmb_meta( $prefix . 'slide_mode' , array(), $slide_id);
					    	$class 			= ( $mode == 'dark' || $color == '' )?'text-light color-light link-light':'text-dark color-dark link-dark';
							$url			= rwmb_meta( $prefix . 'slide_url' , array(), $slide_id);
					    	$color 			= rwmb_meta( $prefix . 'slide_color' , array(), $slide_id);
					    	$style 			= ( $color )?'style="background-color:'.$color.'!important;"':'';
					    	// $show_content	= rwmb_meta( $prefix . 'show_content' , array(), $slide_id);
					?>
		
						<!-- Slide <?= $slide_nb ?> -->
						<div class="d-flex flex-column justify-content-center justify-content-lg-between bg-color-dark text-white vh-100 position-relative rounded-bottom-4 rounded-bottom-right-0" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" data-slide-title="<?= $slide_title; ?>" <?= $style; ?>>
							<div class="px-6 pt-16"><?= (($label != '')?'<h6 class="headline d-inline '.$class.'">'.$label.'</h6>':''); ?></div>
							<div class="px-6 py-2">
								<h1 class="h2 --display-3 <?= $class ?>"><?= WaffTwo\Core\waff_do_markdown($slide_title); ?></h1>
							</div>
							<div class="px-6 pb-3">
								<div class="mb-1 d-none d-sm-block <?= $class ?>"><?= do_shortcode($content); ?></div>
								<?= (($url != '')?'<a href="'.$url.'" class="card-link '.$class.' stretched-link d-block"><i class="icon icon-arrow"></i></a>':''); ?>
							</div>
						</div>	
							
		            <?php endwhile; ?>

				</div>
				
			</div>
			<!-- <div class="col-lg-2 --vh-100 h-100 order-last d-flex flex-column slick-homeslide-list-items justify-content-end" --data-aos="fade-left" --data-aos-offset="-40">
				<div class="p-0 m-0 --mt-auto">
					<ul class="list-group list-group-flush m-0 slider-list">
						<?php 
						$slide_nb 			= 0;
						while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
							$slide_nb ++; 
							$slide_id 	= $post->ID;
					    	$slide_title = get_the_title();
					    	$slide_title = str_replace('<br/>', ' ', $slide_title);
					    	$color 		= rwmb_meta( $prefix . 'slide_color' , array(), $slide_id);
							$slide_colors[$slide_nb] = $color;
					    	$style 		= ( $color )?'style="background-color:'.$color.'!important;"':'';
					    ?>
						<li id="slide-list-<?= $slide_nb ?>" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" class="list-group-item"><i class="icon icon-down-left"></i> <?= $slide_title; ?></li>
						<?php endwhile; ?>
					</ul>
					<style type="text/css">
						<?php foreach ($slide_colors as $slide => $color) : ?>
						#slide-list-<?= $slide; ?>.active {color: <?= $color ?> !important;}
						<?php endforeach; ?>
					</style>
				</div>
			</div> -->
			
			<!-- Mouse down -->
			<!-- <div class="scroll-downs"><div class="mousey"><div class="scroller"></div></div></div> -->
			<div class="scroll-downs position-absolute bottom-0 start-45 mb-4"><div class="mousey"><div class="scroller"></div></div></div>

			<!-- Contextual menu -->
			<div class="position-absolute --top-50 bottom-10 start-50 translate-middle d-flex justify-content-between px-6 pe-20">
				
				<div>
					<span class="bullet bullet-light ml-0"></span>
					<h5 class="color-light">S'installer paysan.ne,<br/>
						pourquoi pas moi ?</h5>
				</div>
				<div>
					<span class="bullet bullet-light ml-0"></span>
					<h5 class="color-light">Découvrir<br/>
						des savoir-faire</h5>
				</div>
				<div>
					<span class="bullet bullet-light ml-0"></span>
					<h5 class="color-light">Visiter une ferme</h5>
				</div>
				<div>
					<span class="bullet bullet-light ml-0"></span>
					<h5 class="color-light">Se faire<br/>
						accompagner</h5>
				</div>
				<div>
					&nbsp;
				</div>

			</div>

		</div>
	</div>
</section>
<?php endif; // have posts ?>
