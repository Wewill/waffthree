<?php
use function WaffTwo\Core\waff_HTMLToRGB as waff_HTMLToRGB; 
use function WaffTwo\Core\waff_RGBToHSL as waff_RGBToHSL; 

// Lightness threshold
$lightness_threshold = 130;

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

// Homeslide background image
$homeslide_images = WaffTwo\Theme\waff_get_theme_homeslide_background();
$homeslide_image = ( !empty($homeslide_images) ) ? reset($homeslide_images) : false;

?>

<?php if ( $homeslide_slides->have_posts() ) : ?>
<!-- #slick-homeslide -->
<section id="slick-homeslide" class="contrast--light">
	<div class="container-fluid px-0">
		<div class="row g-0 align-items-center vh-75 bg-action-2 position-relative"> <!-- .vh-100 hack--> 
			
			<div class="col-lg-9 offset-lg-3 order-2 order-lg-last overflow-hidden position-relative" data-aos="fade-left">
				
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
					<div class="img-shifted shift-left vh-75 <?= $mode; ?>">
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

				<!-- Special GOLFS -->
				<?php if ( $homeslide_image ) : ?>
				<div class="position-absolute top-50 start-0 translate-middle-y h-100 no-drag"><img class="h-100 bg-cover bg-position-center-center img-fluid no-drag" src="<?= $homeslide_image['url']; ?>" /></div>
				<?php endif; ?>

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

			<div class="col-lg-5 col-xl-5 position-absolute top-0 left-0 z-1 order-1 order-lg-first zi-5" data-aos="fade-up">

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
					    	$class 			= ( $mode == 'dark' || $color == '' )?'text-color-light color-color-light link-color-light':'text-color-main color-color-main link-color-main';
							$url			= rwmb_meta( $prefix . 'slide_url' , array(), $slide_id);
					    	$color 			= rwmb_meta( $prefix . 'slide_color' , array(), $slide_id);
					    	// $style 			= ( $color )?'style="background-color:'.$color.'!important;"':'';
					    	$style 			= ( $color )?'style="color:'.$color.'!important;"':'';
					    	// $show_content	= rwmb_meta( $prefix . 'show_content' , array(), $slide_id);
					?>
		
						<!-- Slide <?= $slide_nb ?> -->
						<div class="d-flex flex-column justify-content-center vh-75" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" data-slide-title="<?= $slide_title; ?>">
							<?php if ( $label != '' ) : ?>
							<div class="ps-6 ps-lg-9 pe-3 pe-lg-6 pt-3 pb-4"><?= (($label != '')?'<h6 class="headline d-inline fw-light '.$class.'" '.$style.'>'.$label.'</h6>':''); ?></div>
							<?php endif; ?>
							<div class="ps-6 ps-lg-9 pe-3 pe-lg-6 py-2">
								<h1 class="h2 fw-semibold <?= $class ?>" <?= $style ?>><?= WaffTwo\Core\waff_do_markdown($slide_title); ?></h1>
							</div>
							<?php if ( $content != '' ) : ?>
							<div class="ps-6 ps-lg-9 pe-3 pe-lg-6 pt-4 pb-3">
								<div class="mb-1 d-none d-sm-block <?= $class ?>"><?= do_shortcode($content); ?></div>
								<?= (($url != '')?'<a href="'.$url.'" class="card-link '.$class.' stretched-link d-block"><i class="icon icon-arrow"></i></a>':''); ?>
							</div>
							<?php endif; ?>
						</div>	
							
		            <?php endwhile; ?>

				</div>
				
			</div>
			
			<div class="zi-5 position-absolute top-50 left-0 translate-middle-y order-last d-flex flex-column slick-homeslide-list-position --slick-homeslide-list-items justify-content-end" data-aos="fade-left">
				<div class="p-0 m-0 --mt-auto">
					<ul class="--list-group list-group-flush ps-4 m-0 slider-list">
						<?php 
						$slide_nb 			= 0;
						while ( $homeslide_slides->have_posts() ) : $homeslide_slides->the_post(); 
							$slide_nb ++; 
							$slide_id 	= $post->ID;
							$slide_title = get_the_title();
							$slide_title = str_replace('<br/>', ' ', $slide_title);
						?>
						<li id="slide-list-<?= $slide_nb ?>" data-post-id="<?= $slide_id; ?>" data-slide-id="<?= $slide_nb; ?>" class="list-group-item position-relative">
							<i class="icon icon-down-left"></i>
							<div class="progress-bar"></div>
							<span class="slide-title bg-gradient-color-main text-light"><?= $slide_title; ?></span>
						</li>
						<?php endwhile; ?>
					</ul>
					<style type="text/css">
						.slider-list {
							display:inline-block;
						}
						.slider-list .list-group-item {
							position: relative;
							padding: 0;
							margin: 0;
							height: 40px;
							cursor: pointer;
						}
						.slider-list .list-group-item .progress-bar {
							position: absolute;
							top: 0;
							left: 0;
							width: 5px;
							height: 100%;
							background-color: rgba(255, 255, 255, 0.2);
							transition: background-color 0.3s;
						}
						.slider-list .list-group-item:first-of-type .progress-bar {
							border-top-left-radius: 5px;
							border-top-right-radius: 5px;
						}
						.slider-list .list-group-item:last-child .progress-bar{
							border-bottom-left-radius: 5px;
							border-bottom-right-radius: 5px;
						}
						.slider-list .list-group-item.active .progress-bar {
							background-color: rgba(255, 255, 255, 1);
						}

						.slider-list .list-group-item .slide-title {
							position: absolute;
							top: 50%;
							left: 10px;
							transform: translateY(-50%);
							white-space: nowrap;
							overflow: hidden;
							text-overflow: ellipsis;
							max-width: 0;
							transition: max-width 0.3s, padding-left 0.3s, padding-right 0.3s;
						}
						.slider-list .list-group-item:hover .slide-title {
							padding-left:10px;
							padding-right:10px;
							max-width: 200px;
						}
					</style>
				</div>
			</div>
			
			<!-- Mouse down -->
			<!-- <div class="scroll-downs"><div class="mousey"><div class="scroller"></div></div></div> -->
			<div class="scroll-downs position-absolute bottom-0 start-50 translate-middle-x mb-4 zi-5"><div class="mousey"><div class="scroller"></div></div></div>

			<!-- Contextual menu -->
			<div class="position-absolute mb-10 bottom-0 start-50 translate-middle-x d-none d-sm-flex justify-content-between px-3 px-lg-6 pe-lg-20 zi-5">

				<?php if ( WaffTwo\Theme\waff_get_theme_homeslide_content() ) :  ?>
			
				<?php foreach (WaffTwo\Theme\waff_get_theme_homeslide_content() as $contents) : ?>
					<div>
						<span class="bullet bullet-light ms-0"></span>
						<h5 class="color-light small-sm"><?= esc_html($contents[0]); ?><br/>
							<?= esc_html($contents[1]); ?></h5>
					</div>
				<?php endforeach; ?>

				<?php else :  ?>

				<div>
					&nbsp;
				</div>
				
				<?php endif;  ?>

			</div>

		</div>
	</div>
</section>

<!-- #slick-breaking -->
<section id="slick-breaking" class="mt-0 mb-0 mb-lg-7 contrast--light ---- mt-n2 pt-2">
	<div class="container-fluid px-0">
		<style scoped>
			@media (max-width: 768px) {
				.w-90 {
					width: 70% !important;
				}
			}
		</style>
		<!-- News -->
		<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 align-items-stretch gx-4 --py-5 offset-3 position-absolute top-0 start-0 w-90 z-2" style="height:calc(80% + 3.5rem)!important;">
			<?php
			$recent_posts = new WP_Query(array(
				'posts_per_page' => 4,
				'post_status' => 'publish',
			));
			if ($recent_posts->have_posts()) :
				$delay = 400;
				while ($recent_posts->have_posts()) : $recent_posts->the_post(); 
					$post_id 					= get_the_ID();
					// Post Color
					$post_color 				= rwmb_meta('_waff_bg_color_metafield', array(), $post_id);
					$post_color 				= ($post_color != '') ? $post_color : 'var(--waff-action-2)';
					$rgb_post_color				= waff_HTMLToRGB($post_color);
					$post_title_color 			= 'text-white';
					// Check if the color is dark or light
					if ( $post_color && $post_color != '' && $post_color != 'var(--waff-action-2)' ) { // Si $post_color n'est pas vide
						$hsl = waff_RGBToHSL($rgb_post_color); // Accepte un INTEGER
						if($hsl->lightness > $lightness_threshold) {
							$post_title_color 			= 'text-dark';
						}
					}
					// Post Thumbnail
					$thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
					$background_style = $thumbnail_url ? "background-image: url('$thumbnail_url');" : "background-color: $post_color;";
			?>
					<div class="col <?php if ($delay === 600) echo 'd-none d-md-block'; ?> <?php if ($delay === 800) echo 'd-none d-lg-block'; ?> <?php if ($delay === 1000) echo 'd-none d-xl-block'; ?>" data-aos="fade-down" data-aos-delay="<?= $delay; ?>">
						<div class="card h-80 overflow-hidden rounded-4 shadow-lg border-0 ---- bg-cover bg-position-center-center" style="<?= $background_style; ?>">
							<div class="card-img-overlay <?= $thumbnail_url ? 'bg-gradient-action-2' : '' ?>">
								<div class="d-flex flex-column justify-content-between h-100 p-3 pb-2 text-shadow-1 <?= $thumbnail_url ? $post_title_color : 'text-white' ?>">
									<div></div>
									<h5 class="<?= $thumbnail_url ? $post_title_color : 'text-white' ?>"><a href="<?php the_permalink(); ?>" class="stretched-link"><?php the_title(); ?></a></h5>
									<ul class="d-flex list-unstyled m-0">
										<li class="me-auto subline"><a href="<?php the_permalink(); ?>">Lire la suite <i class="bi bi-chevron-right"></i></a></li>
										<li class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> <small><?php echo str_replace('minutes', 'mins', human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'); ?></small></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?php
					$delay += 200;
				endwhile;
			endif;
			wp_reset_postdata();
			?>
		</div>

		<!-- Categories -->
		<div class="row g-0 align-items-between justify-content-start vh-25 bg-color-main position-relative"> <!-- .vh-100 hack--> 

			<div class="col-3 d-flex flex-center h-75" data-aos="fade-down" data-aos-delay="200">
				<h6 class="headflat text-white m-0 text-center">Dernières actualités</h6>
			</div>

			<ul class="d-flex justify-content-around list-group list-group-horizontal --list-group-flush list-breaking m-0 w-100 bg-white pt-2 overflow-scroll">
				<?php
				$categories = get_categories();
				foreach ($categories as $category) {
					echo '<li class="list-group-item text-center"><a class="headflat" href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></li>';
				}
				?>
			</ul>

		</div>
	</div>
</section>
<?php endif; // have posts ?>