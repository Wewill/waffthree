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


?>

<?php if ( $homeslide_slides->have_posts() ) : ?>
<!-- #slick-homeslide -->
<section id="slick-homeslide" class="mb-0 mb-sm-10 mb-lg-7 contrast--light ">
	<div class="container-fluid px-0">
		<div class="row g-0 align-items-center vh-100"> <!-- .vh-100 hack--> 
			<div class="col-lg order-2 order-lg-first overflow-hidden" data-aos="fade-right">
				
				<!-- slick-homeslide images-->
				<div class="slider-nav">
					<?php while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
						$slide_nb ++; 
				    	$slide_id 		= $post->ID;
				    	$mode 			= rwmb_meta( $prefix . 'slide_mode' , array(), $slide_id);
				    	$mode 			= ($mode=='dark')?'contrast--dark':'contrast--light';
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

				<style scoped>
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
			<div class="col-lg-5 col-xl-3 order-1 order-lg-2" data-aos="fade-down">

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
						<div class="d-flex flex-column justify-content-center justify-content-lg-between bg-color-dark text-white vh-100 position-relative" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" data-slide-title="<?= $slide_title; ?>" <?= $style; ?>>
							<div class="px-4 pt-4"><?= (($label != '')?'<h6 class="headline d-inline '.$class.'">'.$label.'</h6>':''); ?></div>
							<div class="px-4 py-2">
								<h1 class="display-3 <?= $class ?>"><?= $slide_title; ?></h1>
							</div>
							<div class="px-4 pb-3">
								<div class="mb-1 d-none d-sm-block <?= $class ?>"><?= do_shortcode($content); ?></div>
								<?= (($url != '')?'<a href="'.$url.'" class="card-link '.$class.' stretched-link d-block"><i class="icon icon-arrow"></i></a>':''); ?>
							</div>
						</div>	
							
		            <?php endwhile; ?>

				</div>
				
			</div>
			<div class="col-lg-2 --vh-100 h-100 order-last d-flex flex-column slick-homeslide-list-items justify-content-end" --data-aos="fade-left" --data-aos-offset="-40">
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
			</div>
			
			<!-- Mouse down -->
			<!-- <div class="scroll-downs"><div class="mousey"><div class="scroller"></div></div></div> -->
			<div class="scroll-downs position-absolute bottom-0 start-50 mb-3"><div class="mousey"><div class="scroller"></div></div></div>
		</div>
	</div>
</section>
<?php endif; // have posts ?>
