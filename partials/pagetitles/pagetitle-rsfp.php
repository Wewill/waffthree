<?php

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##PAGETITLE '.$args.'</code>':'');

// Every passed args
//var_dump( $args );  

// Get page meta fields 
global $page_atts;
//print_r($page_atts);

// Lightness threshold
$lightness_threshold = 130;

// To css 
$title 							= ( isset($page_atts['title']) && $page_atts['title'] != '' )?do_shortcode($page_atts['title']):single_post_title('', false);
$header_color 					= ( isset($page_atts['header_color']) && $page_atts['header_color'] != '' )?'style="background-color:'. $page_atts['header_color'].'!important;"':'';
$header_color_class				= 'contrast--light';
$header_section_title_color 	= 'color-dark';
$header_link_color 				= 'link-dark';

if ( isset($page_atts['header_color']) && $page_atts['header_color'] != '' ) {
	$rgb = WaffTwo\Core\waff_HTMLToRGB($page_atts['header_color']);
	$hsl = WaffTwo\Core\waff_RGBToHSL($rgb);
	if($hsl->lightness < $lightness_threshold) {
		$header_color_class 			= 'contrast--dark';
		$header_section_title_color 	= 'color-light'; //color-white	
		$header_link_color 				= 'link-light'; //link-white	
	}
}


// Post Thumbnail

$featured_img_urls = array();
//Pages
$page_featured_sizes = array(
	'thumbnail',
	'full',
	'page-featured-image', 
	'page-featured-image-x2',
	'page-featured-image-fancy', 
	'page-featured-image-fancy-x2',
	'page-featured-image-modern', 
	'page-featured-image-modern-x2',
	'page-featured-image-m', 
	'page-featured-image-m-x2',
	'page-featured-image-m-modern', 
	'page-featured-image-m-modern-x2',
	'page-featured-image-s', 
	'page-featured-image-s-x2',
);
// Posts & films
$post_featured_sizes = array(
	'thumbnail',
	'full',
	'post-featured-image', 
	'post-featured-image-x2',
	'post-featured-image-m', 
	'post-featured-image-m-x2',
	'post-featured-image-s', 
	'post-featured-image-s-x2',
);

// Switch post type 
switch (get_post_type()) {
	case ( 'post' ) :
	    	$selected_featured_sizes = $post_featured_sizes;
	break;
	case ( 'page' ) :
	    	$selected_featured_sizes = $page_featured_sizes;
	break;
	case ( 'directory' ) :
			$selected_featured_sizes = $post_featured_sizes;
	break;
	case ( 'farm' ) :
			$selected_featured_sizes = $post_featured_sizes;
	break;
	/*case ( 'homeslide' === $pt ) :
	    	$selected_featured_sizes = 
	break;*/
	default:
	    	$selected_featured_sizes = $page_featured_sizes;
	break;
}

if ( is_singular() && has_post_thumbnail() ) { 
    $featured_img_id     		= get_post_thumbnail_id();
	$featured_img_url_full 		= get_the_post_thumbnail_url();
	foreach ($selected_featured_sizes as $size) {
		//$featured_img_url = get_the_post_thumbnail_url($size); //KO = full
		//$featured_img_url = wp_get_attachment_url( $featured_img_id, $size ); // KO = full
		$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
		$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full; 
	}
	$alt = get_post_meta ( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
    $featured_img_caption = wp_get_attachment_caption($featured_img_id); // ADD WIL                    
    $thumb_img = get_post( $featured_img_id ); // Get post by ID
	$featured_img_description =  $thumb_img->post_content; // Display Description
}

/*
	Headers 
	
	
	
	<!-- NO #pagetitle -->
	<section class="mt-md-9 pb-9 contrast--white bg-warning">
		Du contenu apres un header affix
	</section>
	
	<section class="mt-md-18 pb-9 contrast--white bg-info d-none">
		Du contenu apres un header non affix
	</section>
	
	<section class="pt-md-9 pb-9 contrast--white bg-danger d-none">
		Du contenu naked apres un header affix
	</section>
	
	<section class="pt-md-18 pb-9 contrast--white bg-success d-none">
		Du contenu naked apres un header non affix
	</section>
		
*/
?>

<!-- @TODO Farmers / structures / etc -->
<?php if ( $args == 'blog' ) : ?>

	<!-- #pagetitle : Blog -->
	<section id="pagetitle" class="pt-10 --pt-md-20 pt-md-14 pb-10 --contrast--light --container-10 --container-left <?= $header_color_class ?>" <?= $header_color ?> --data-aos="fade-up">
		<div class="jumbotron">
		    <div class="container-fluid --container-10 --container-left">
				<hgroup data-aos="fade-down">
					<h1 class="<?= $header_section_title_color ?>"><?= sanitize_text_field($title) ?></h1>
					<?php if ( $page_atts['subtitle'] != '' ) echo '<h5 class="opacity-75 '.$header_section_title_color.'">'.do_shortcode(sanitize_text_field($page_atts['subtitle'])).'</h5>'; ?>
				</hgroup>
		    </div>
		</div>
	</section>
	<!-- END: #pagetitle -->

<?php elseif ( $args == 'post' ) : ?>

	<!-- #pagetitle : Post -->
	<section id="pagetitle" class="pt-10 --pt-md-20 pt-md-14 pb-10 contrast--light --container-10 --container-left " <?= $page_atts['post_color_class']?>>
		<div class="jumbotron">
		    <div class="container-fluid --container-10 --container-left">
				<hgroup data-aos="fade-down">
					<h1 class="title mb-0"><?php single_post_title(); ?></h1>
					<?= WaffTwo\waff_entry_meta_header(); ?>
				</hgroup>
		    </div>
		</div>
	</section>
	<!-- END: #pagetitle -->
	
	<!-- #pageheader -->
	<?php if ( is_singular() && has_post_thumbnail() ) { ?>
	<section id="pageheader" class="mt-0 mb-0 contrast--light --container-10 --container-left" data-aos="fade-up" data-aos-id="pageheader">
		<figure title="<?php echo esc_attr($featured_img_description); ?>">
		    <picture class="lazy">
			<!-- 3800x1200 > 1900x600 -->
		    <data-src media="(min-width: 990px)"
		            srcset="<?= $featured_img_urls['post-featured-image-x2']; ?> 2x,
		                    <?= $featured_img_urls['post-featured-image']; ?>" type="image/jpeg"></data-src>
		    <data-src media="(min-width: 590px)"
		            srcset="<?= $featured_img_urls['post-featured-image-m-x2']; ?> 2x,
		            		<?= $featured_img_urls['post-featured-image-m']; ?>" type="image/jpeg"></data-src>
			<data-src media="(min-width: 380px)"
					srcset="<?= $featured_img_urls['post-featured-image-s-x2']; ?> 2x,
							<?= $featured_img_urls['post-featured-image-s']; ?>" type="image/jpeg"></data-src>
			<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-sm-600-px" style="object-fit: cover; width: 100%;"></data-img> <!-- style="height: 600px;" -->
			</picture>
			<?php if ( $featured_img_caption || $featured_img_description ) : ?>
			<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
			<?php endif; /* If captions */ ?>
			<!--
			Sizes :
			<?php print_r($featured_img_urls); ?>  
			-->
		</figure>
	</section>
	<?php } /* is_singular + has_post_thumbnail */ ?>
	<!-- END: #pageheader -->

<?php elseif ( $args == 'partenaire' ) : ?>

	<?php 
		$partenaire_link 	= get_post_meta( $post->ID, 'wpcf-p-link', true ); 
	?>

	<!-- #pagetitle : Post -->
	<section id="pagetitle" class="pt-12 pt-md-20 pb-14 contrast--light container-10 container-left bg-color-bg" <?= $page_atts['post_color_class']?>>
		<div class="jumbotron">
			<div class="container-fluid container-10 container-left">
				<hgroup>
					<?php if ( $args != '' ) printf('<h5 class="subline text-muted mb-1">%s</h5>', sanitize_text_field(ucfirst($args))); ?>
					<a href="<?= esc_url($partenaire_link) ?>"><h2 class="title --subline mb-0"><?php single_post_title(); ?></h2></a>
					<?= WaffTwo\waff_entry_meta_header(); ?>
				</hgroup>
			</div>
		</div>
	</section>
	<!-- END: #pagetitle -->

	<!-- #pageheader -->
	<?php if ( is_singular() && has_post_thumbnail() ) { ?>
	<section id="pageheader" class="mt-0 mb-0 contrast--light container-10 container-left bg-light" data-aos="fade-up" data-aos-id="pageheader">
		<figure class="col-5" title="<?php echo esc_attr($featured_img_description); ?>">
			<picture class="lazy">
			<!-- 3800x1200 > 1900x600 -->
			<data-src media="(min-width: 990px)"
					srcset="<?= $featured_img_urls['page-featured-image-x2']; ?> 2x,
							<?= $featured_img_urls['page-featured-image']; ?>" type="image/jpeg"></data-src>
			<data-src media="(min-width: 590px)"
					srcset="<?= $featured_img_urls['page-featured-image-m-x2']; ?> 2x,
							<?= $featured_img_urls['page-featured-image-m']; ?>" type="image/jpeg"></data-src>
			<data-src media="(min-width: 380px)"
					srcset="<?= $featured_img_urls['page-featured-image-s-x2']; ?> 2x,
							<?= $featured_img_urls['page-featured-image-s']; ?>" type="image/jpeg"></data-src>
			<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-sm-600-px" style="object-fit: cover; width: 100%;"></data-img> <!-- style="height: 600px;" -->
			</picture>
			<?php if ( $featured_img_caption || $featured_img_description ) : ?>
			<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
			<?php endif; /* If captions */ ?>
			<!--
			Sizes :
			<?php print_r($featured_img_urls); ?>  
			-->
		</figure>
	</section>
	<?php } /* is_singular + has_post_thumbnail */ ?>
	<!-- END: #pageheader -->

<?php elseif ( $args == 'directory' ) : ?>

	<?php 
		$prefix = 'd_';

		$d_general_subtitle 				= get_post_meta( $post->ID, $prefix . 'general_subtitle', true ); 
		$d_general_introduction 			= get_post_meta( $post->ID, $prefix . 'general_introduction', true ); 
		/* <?php if ( $args != '' ) printf('<h5 class="subline text-muted mb-1">%s</h5>', sanitize_text_field(ucfirst($args)));?> 
		esc_html(__('[:fr]De[:en]From[:]')) */

		// $d_medias_video_link 				= get_post_meta( $post->ID, 'd_medias_video_link', true ); 
		// $d_medias_video 						= get_post_meta( $post->ID, 'd_medias_video', true );

		$d_medias_video_links 				= rwmb_meta( $prefix . 'medias_video_link', array('limit' => 1), $post->ID);
		$d_medias_video_link 				= reset($d_medias_video_links); // Recursive field

		$d_medias_videos 					= rwmb_meta( $prefix . 'medias_video', array('limit' => 1), $post->ID);
		$d_medias_video 					= reset($d_medias_videos); // Recursive field


		$_d_stage_opentostage   			= rwmb_get_field_settings( $prefix . 'stage_opentostage' );
		$options_d_stage_opentostage 		= $_d_stage_opentostage['options'];
		$d_stage_opentostage 				= rwmb_meta( $prefix . 'stage_opentostage', $post->ID); // Array ( [0] => visite_libre [1] => visite_collective )

		$_d_stage_opentovisit  				= rwmb_get_field_settings( $prefix . 'stage_opentovisit' );
		$options_d_stage_opentovisit 		= $_d_stage_opentovisit['options'];
		$d_stage_opentovisit 				= rwmb_meta( $prefix . 'stage_opentovisit', $post->ID); // Array ( [0] => visite_libre [1] => visite_collective )
		
		/* Contact form link */
		$contact_slug = "contact";
		$contact_page_object = get_page_by_path( $contact_slug );
		$contact_permalink = get_permalink( $contact_page_object->ID );
	?>
	
	<!-- #pageheader -->
	<section id="pageheader" class="mt-0 mb-0 contrast--light vh-100 position-relative fancy-header is-formatted" data-aos="fade-up" data-aos-id="pageheader">
		<div class="container-fluid px-0">
			<div class="row g-0 justify-content-between align-items-center vh-100"><!-- .vh-100 hack >> see styles.css / specific-rsfp > vh-50 until md -->
				
				<?php if ( is_singular() && has_post_thumbnail() ) { ?>
				<div class="header-image col-md-6 col-lg-5 bg-color-layout h-100 ---- img-shifted shift-right" data-aos="fade-down" data-aos-delay="200">
					
					<!-- Image -->  
					<?php if (empty($d_medias_video)): ?>
						<figure title="<?php echo esc_attr($featured_img_description); ?>">
							<picture class="contrast--light overflow-hidden h-100 lazy" data-aos="fade-up" data-aos-delay="200">
							<!-- 3800x1200 > 1900x600 -->
							<data-src media="(min-width: 990px)"
									srcset="<?= $featured_img_urls['post-featured-image-x2']; ?> 2x,
											<?= $featured_img_urls['post-featured-image']; ?>" type="image/jpeg"></data-src>
							<data-src media="(min-width: 590px)"
									srcset="<?= $featured_img_urls['post-featured-image-m-x2']; ?> 2x,
											<?= $featured_img_urls['post-featured-image-m']; ?>" type="image/jpeg"></data-src>
							<data-src media="(min-width: 380px)"
									srcset="<?= $featured_img_urls['post-featured-image-s-x2']; ?> 2x,
											<?= $featured_img_urls['post-featured-image-s']; ?>" type="image/jpeg"></data-src>
							<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid vh-100 fit-image w-100"></data-img>
							</picture>
							<?php if ( $featured_img_caption || $featured_img_description ) : ?>
							<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
							<?php endif; /* If captions */ ?>
							<!--
							Sizes :
							<?php print_r($featured_img_urls); ?>  
							-->
						</figure>
					<?php endif; ?>

					<!-- Video -->  
					<?php if (!empty($d_medias_video)): ?>
						<!-- <pre><?= print_r($d_medias_video); ?></pre> -->
						<figure class="wp-block-video h-100 d-flex flex-center">
							<video class="w-auto h-100" autoplay loop muted playsinline src="<?= $d_medias_video['src']; ?>"><!-- poster="<?= $d_medias_video['image']['src']; ?>" --></video>
						</figure>
					<?php endif; ?>

					<!-- Play -->
					<?php if (!empty($d_medias_video_link)): ?>
						<div class="absolute position-absolute top-0 h-100 w-100 btn_holder">
							<a class="btn action-1 --color-light play" data-fancybox="pagetitle_fancybox_<?= $post->ID; ?>" href="<?= $d_medias_video_link; ?>" target="_blank"><i class="bi bi-play-fill h3 ms-1"></i></a>
						</div>
					<?php endif; ?>
				</div>
				<?php } /* is_singular + has_post_thumbnail */ ?>

				<div class="header-content col-md overflow-hidden bg-color-bg h-100 d-flex flex-column justify-content-between align-items-start p-3 ps-lg-5 pe-lg-5 pb-lg-5 pt-md-17 pt-lg-20" data-aos="fade-left">
					
					<hgroup>
						<?= WaffTwo\waff_entry_meta_header(); ?>
						<h2 class="my-3"><?php single_post_title(); ?></h2>
					</hgroup>
					
					<div>
						<?php if ($d_general_subtitle) printf('<p class="lead fw-bold lg-reset-fontsize mb-1 mb-lg-3 mb-xl-5">%s</p>', esc_html($d_general_subtitle)); ?>
						<?php if ($d_general_introduction) printf('<div class="lead lg-reset-fontsize">%s</div>', preg_replace('/<p>\s*<\/p>/', '', apply_filters('the_content', WaffTwo\Core\waff_do_markdown($d_general_introduction)))); ?>
					</div>

					<?php if (!empty($d_stage_opentovisit) || !empty($d_stage_opentostage)) { ?>
						<div class="d-flex align-items-center justify-content-center p-2 py-md-3 px-md-4 py-xl-4 px-xl-5 bg-body rounded-4 shadow">
							<?php if (!empty($d_stage_opentovisit)): ?>
							<div class="d-lg-flex d-inline-block align-items-center px-1 px-lg-0">
								<i class="bi bi-house-heart flex-shrink-0 me-2 me-md-3 h2 md-reset-fontsize text-action-1"></i>
								<div>
								<h6 class="fw-bold text-action-1 my-2 my-lg-3"><?= esc_html__( 'Visit farm', 'waff' ); ?></h6>
								<p class="mb-0 small-lg"><span class="visually-hidden"><?= esc_html__( 'Farm is open to visit :', 'waff' ); ?></span><?= WaffTwo\Core\waff_implode_options($d_stage_opentovisit, $options_d_stage_opentovisit, ', '); ?></p>
								<a class="btn btn-action-1 btn-sm btn-transition-scale mt-3 --flex-fill --w-100 px-2 py-0" href="<?= esc_url(add_query_arg(array('form_type' => 'visit', 'ID' => $post->ID),$contact_permalink)) ?>"><?= esc_html__( 'Book a visit', 'wa-rsfp' ); ?></a>
								</div>
							</div>
							<div class="d-none d-lg-flex align-items-center justify-content-center px-2 px-md-4 px-xl-5">
								<span class="bullet bullet-action-2 ms-0"></span>
							</div>
							<?php endif; ?>

							<?php if (!empty($d_stage_opentostage)): ?>
							<div class="d-lg-flex d-inline-block align-items-center px-1 px-lg-0">
								<i class="bi bi-highlighter flex-shrink-0 me-2 me-md-3 h2 md-reset-fontsize text-action-1"></i>
								<div>
								<h6 class="fw-bold text-action-1 my-2 my-lg-3"><?= esc_html__( 'Open to stage', 'waff' ); ?></h6>
								<p class="mb-0 small-lg"><span class="visually-hidden"><?= esc_html__( 'Farm is open to stage :', 'waff' ); ?></span><?= WaffTwo\Core\waff_implode_options($d_stage_opentostage, $options_d_stage_opentostage, ', '); ?></p>
								<a class="btn btn-action-1 btn-sm btn-transition-scale mt-3 --flex-fill --w-100 px-2 py-0" href="<?= esc_url(add_query_arg(array('form_type' => 'stage', 'ID' => $post->ID),$contact_permalink)) ?>"><?= esc_html__( 'Apply to a stage', 'wa-rsfp' ); ?></a>
								</div>
							</div>
							<!-- <div class="d-none d-lg-flex align-items-center justify-content-center px-5">
								<span class="bullet bullet-action-2 ms-0"></span>
							</div> -->
							<?php endif; ?>

							<!-- <div class="d-lg-flex d-inline-block align-items-center">
								<i class="bi bi-cloud-arrow-down flex-shrink-0 me-2 me-md-3 h2 md-reset-fontsize text-action-1"></i>
								<div>
								<h6 class="fw-bold --text-action-1"><?= esc_html__( 'Download', 'waff' ); ?></h6>
								<p class="mb-0 small-lg"><span class="badge bg-action-2">Bientôt disponible...</span></p>
								</div>
							</div> -->
						</div>
					<?php } ?>

				</div>
		
				<!-- Mouse down -->
				<!-- <div class="scroll-downs position-absolute bottom-0 start-45 mb-4">
					<div class="mousey">
						<div class="scroller"></div>
					</div>
				</div> -->

			</div>
		</div>
	</section>
	<!-- END: #pageheader -->

<?php elseif ( $args == 'farm' ) : ?>

	<?php 
		$prefix = 'f_';
		$f_more_testimony 					= get_post_meta( $post->ID, $prefix . 'more_testimony', true ); 
	?>
	
	<!-- #pageheader -->
	<section id="pageheader" class="mt-0 mb-0 contrast--light h-100 lg-vh-50 position-relative split-header is-formatted" data-aos="fade-up" data-aos-id="pageheader">
		<div class="container-fluid px-0">
			<div class="row g-0 justify-content-between align-items-center h-100 lg-vh-50"><!-- .vh-50 hack >> see styles.css / specific-rsfp > vh-50 until md -->
				
				<?php if ( is_singular() && has_post_thumbnail() ) { ?>
				<div class="header-image col-md-6 col-lg-5 bg-color-layout h-100 ---- img-shifted shift-right" data-aos="fade-down" data-aos-delay="200">
					
					<!-- Image -->  
					<figure title="<?php echo esc_attr($featured_img_description); ?>">
						<picture class="contrast--light overflow-hidden h-100 lazy" data-aos="fade-up" data-aos-delay="200">
						<!-- 3800x1200 > 1900x600 -->
						<data-src media="(min-width: 990px)"
								srcset="<?= $featured_img_urls['post-featured-image-x2']; ?> 2x,
										<?= $featured_img_urls['post-featured-image']; ?>" type="image/jpeg"></data-src>
						<data-src media="(min-width: 590px)"
								srcset="<?= $featured_img_urls['post-featured-image-m-x2']; ?> 2x,
										<?= $featured_img_urls['post-featured-image-m']; ?>" type="image/jpeg"></data-src>
						<data-src media="(min-width: 380px)"
								srcset="<?= $featured_img_urls['post-featured-image-s-x2']; ?> 2x,
										<?= $featured_img_urls['post-featured-image-s']; ?>" type="image/jpeg"></data-src>
						<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid vh-50 fit-image w-100"></data-img>
						</picture>
						<?php if ( $featured_img_caption || $featured_img_description ) : ?>
						<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
						<?php endif; /* If captions */ ?>
						<!--
						Sizes :
						<?php print_r($featured_img_urls); ?>  
						-->
					</figure>
				</div>
				<?php } /* is_singular + has_post_thumbnail */ ?>

				<div class="header-content col-md overflow-hidden bg-color-bg h-100 d-flex flex-column justify-content-between align-items-start p-3 ps-lg-5 pe-lg-5 pb-lg-5 pt-lg-5" data-aos="fade-left">
					
					<hgroup class="mt-10">
						<?= WaffTwo\waff_entry_meta_header(); ?>
						<h2 class=""><?php single_post_title(); ?></h2>
					</hgroup>

					<?php if ($f_more_testimony) printf('<div class="lead lg-reset-fontsize">%s</div>', preg_replace('/<p>\s*<\/p>/', '', apply_filters('the_content', WaffTwo\Core\waff_do_markdown($f_more_testimony)))); ?>

				</div>
		
				<!-- Mouse down -->
				<!-- <div class="scroll-downs position-absolute bottom-0 start-45 mb-4">
					<div class="mousey">
						<div class="scroller"></div>
					</div>
				</div> -->

			</div>
		</div>
	</section>
	<!-- END: #pageheader -->

<?php else: ?>	

	<!-- #NOARGS ( ≠ film / post / section ... ) / Header style : <?= $page_atts['header_style'] ?> -->

	<?php if ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('modern') ) ): ?>

	<!-- #pagetitle : Modern -->
	<section id="pagetitle" class="modern-header mt-0 mb-0 --contrast--light bg-action-1 rounded-start-4 <?= $header_color_class ?>" <?= $header_color ?> data-aos="fade-up" data-aos-id="pagetitle">
		<div class="jumbotron p-0">
		    <div class="container-fluid">
				<div class="row pt-sm-20 --pt-15 pt-10">
					<div class="col-sm-7 col-12 zi-5">
						<hgroup data-aos="fade-down">
							<h1 class="<?= $header_section_title_color ?>"><?= sanitize_text_field($title) ?></h1>
							<?php if ( $page_atts['subtitle'] != '' ) echo '<h5 class="opacity-75 '.$header_section_title_color.'">'.do_shortcode(sanitize_text_field($page_atts['subtitle'])).'</h5>'; ?>
						</hgroup>
					</div>
				</div>
				<div class="row <?= (( has_post_thumbnail() )?'mt-n':'mt-15') ?> align-items-end g-0 f-w">
					<div class="col-sm-5 col-6 delayed-anchors-aos" data-aos="fade-right" data-aos-delay="200">
						<?php if ( !empty($page_atts['anchors']) ): ?>
						<ul class="p-gutter-l list-unstyled pb-8 <?= $header_section_title_color ?>">
							<?php
								foreach ($page_atts['anchors'] as $key => $value) {
									echo '<li class="lead '.$header_link_color.' animated-underline"><a href="#'.sanitize_title($value).'">'.$value.'</a></li>';
								}
							?>
						</ul>
						<?php endif; ?>
					</div>
					<div class="col-sm-7 col-6 h-600-px h-sm-600-px rounded-start-4 rounded-bottom-left-0" > <!-- data-aos="show-lazy" data-aos-delay="1000" data-aos-duration="3000" -->
						<?php if ( has_post_thumbnail() ): ?>
						<figure title="<?php echo esc_attr($featured_img_description); ?>" style="background-color:<?= $page_atts['header_color'] ?>;">
						    <picture class="lazy show-img-when-loaded duotone-<?= get_post_thumbnail_id() ?>">
							<!-- 1200x900 > 800x600 (1600x1100 > 800x550) -->
						    <data-src media="(min-width: 990px)"
						            srcset="<?= $featured_img_urls['page-featured-image-modern-x2']; ?> 2x,
						                    <?= $featured_img_urls['page-featured-image-modern']; ?>" type="image/jpeg"></data-src>
						    <data-src media="(min-width: 590px)"
						            srcset="<?= $featured_img_urls['page-featured-image-modern-m-x2']; ?> 2x,
						            		<?= $featured_img_urls['page-featured-image-modern-m']; ?>" type="image/jpeg"></data-src>
							<data-src media="(min-width: 380px)"
									srcset="<?= $featured_img_urls['page-featured-image-s-x2']; ?> 2x,
											<?= $featured_img_urls['page-featured-image-s']; ?>" type="image/jpeg"></data-src>
							<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-sm-600-px rounded-start-4 rounded-bottom-left-0" style="object-fit: cover; width: 100%;"></data-img>
							</picture>
							<?php if ( $featured_img_caption || $featured_img_description ) : ?>
							<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
							<?php endif; /* If captions */ ?>
							<!--
							Sizes :
							<?php print_r($featured_img_urls); ?>  
							-->
						</figure>
						<?php if ( $page_atts['header_color'] != '' && $page_atts['header_image_style'] != '' && $page_atts['header_image_style'] == 1 ) { ?>
						<style scoped>
							.duotone-<?= get_post_thumbnail_id() ?> img {
								filter: grayscale(100%) contrast(1);
								mix-blend-mode: screen;
								background-color: <?= $page_atts['header_color'] ?>;
								opacity: 0;
							}
						</style>
						<?php } ?>
						<?php else: ?>
							<div class="alert alert-warning" role="alert"><?= esc_html__( 'You need to choose a thumbnail image', 'waff' ); ?></div>
						<?php endif; ?>				
					</div>
				</div>
		    </div>
		</div>
	</section>
	<!-- END: #pagetitle -->

	<?php elseif ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('split') ) ): ?>

	<!-- NO #pagetitle : Split -->
	<!-- #pageheader : Split -->
	<section id="pageheader" class="mt-0 mb-0 contrast--light h-100 lg-h-100 lg-vh-50 position-relative split-header is-formatted" data-aos="fade-down" data-aos-id="pageheader">
		<div class="container-fluid px-0">
			<div class="row g-0 justify-content-between align-items-center lg-h-100 lg-vh-50"><!-- .vh-50 hack >> see styles.css / specific-rsfp > vh-50 until md -->
				
				<?php if ( is_singular() && has_post_thumbnail() ) : ?>
				<div class="header-image col-md-6 col-lg-5 bg-color-layout h-100 ---- img-shifted shift-right <?= $header_color_class ?>" <?= $header_color ?>>
					<!-- Image -->  
					<figure title="<?php echo esc_attr($featured_img_description); ?>">
						<picture class="contrast--light overflow-hidden h-100 lazy show-img-when-loaded duotone-<?= get_post_thumbnail_id() ?>">
						<!-- 3800x1200 > 1900x600 -->
						<data-src media="(min-width: 990px)"
								srcset="<?= $featured_img_urls['page-featured-image-x2']; ?> 2x,
										<?= $featured_img_urls['page-featured-image']; ?>" type="image/jpeg"></data-src>
						<data-src media="(min-width: 590px)"
								srcset="<?= $featured_img_urls['page-featured-image-m-x2']; ?> 2x,
										<?= $featured_img_urls['page-featured-image-m']; ?>" type="image/jpeg"></data-src>
						<data-src media="(min-width: 380px)"
								srcset="<?= $featured_img_urls['page-featured-image-s-x2']; ?> 2x,
										<?= $featured_img_urls['page-featured-image-s']; ?>" type="image/jpeg"></data-src>
						<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid vh-50 fit-image w-100"></data-img>
						</picture>
						<?php if ( $featured_img_caption || $featured_img_description ) : ?>
						<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
						<?php endif; /* If captions */ ?>
						<!--
						Sizes :
						<?php print_r($featured_img_urls); ?>  
						-->
					</figure>
					<?php if ( $page_atts['header_color'] != '' && $page_atts['header_image_style'] != '' && $page_atts['header_image_style'] == 1 ) { ?>
					<style scoped>
						.duotone-<?= get_post_thumbnail_id() ?> img {
							filter: grayscale(100%) contrast(1);
							mix-blend-mode: screen;
							background-color: <?= $page_atts['header_color'] ?>;
							opacity: 0;
						}
					</style>
					<?php } ?>
					<?php else: ?>
						<div class="alert alert-warning" role="alert"><?= esc_html__( 'You need to choose a thumbnail image', 'waff' ); ?></div>
					<?php endif;  /* is_singular + has_post_thumbnail */?>				
				</div>

				<div class="header-content col-md overflow-hidden bg-color-bg h-100 d-flex flex-column justify-content-between align-items-start p-3 ps-lg-5 pe-lg-5 pb-lg-5 pt-lg-5 <?= $header_color_class ?>" <?= $header_color ?> --data-aos="fade-left">
					<!-- Titles -->
					<hgroup>
						<?= WaffTwo\waff_entry_meta_header(); ?>
						<?php if ( $page_atts['subtitle'] != '' ) echo '<h6 class="headline d-inline-block my-3 '.$header_section_title_color.'">'.do_shortcode(sanitize_text_field($page_atts['subtitle'])).'</h6>'; ?>
						<h1 class="<?= $header_section_title_color ?>"><?= sanitize_text_field($title) ?></h1>
					</hgroup>

					<!--  Anchors -->
					<div class="col-sm-5 col-6 delayed-anchors-aos" data-aos="fade-right" data-aos-delay="200">
						<?php if ( !empty($page_atts['anchors']) ): ?>
						<ul class="list-unstyled pb-0 <?= $header_section_title_color ?>">
							<?php
								foreach ($page_atts['anchors'] as $key => $value) {
									echo '<li class="lead '.$header_link_color.' animated-underline"><a href="#'.sanitize_title($value).'">'.$value.'</a></li>';
								}
							?>
						</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- END: #pageheader -->
 
	<?php elseif ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('fancy') ) ): ?>

	<!-- NO #pagetitle : Fancy -->
	<!-- #pageheader -->
	<section id="pageheader" class="fancy-header mt-0 --mb-10 --mb-0 contrast--light <?= $header_color_class ?>" <?= $header_color ?> data-aos="fade-up" data-aos-id="pageheader">
		<?php if ( has_post_thumbnail() ): ?>
		<figure title="<?php echo esc_attr($featured_img_description); ?>" style="background-color:<?= $page_atts['header_color'] ?>;">
		    <picture class="lazy duotone-<?= get_post_thumbnail_id() ?>">
			<!-- 3800x2400 > 1900x1200 -->
		    <data-src media="(min-width: 990px)"
		            srcset="<?= $featured_img_urls['page-featured-image-fancy-x2']; ?> 2x,
		                    <?= $featured_img_urls['page-featured-image-fancy']; ?>" type="image/jpeg"></data-src>
		    <data-src media="(min-width: 590px)"
		            srcset="<?= $featured_img_urls['page-featured-image-m-x2']; ?> 2x,
		            		<?= $featured_img_urls['page-featured-image-m']; ?>" type="image/jpeg"></data-src>
			<data-src media="(min-width: 380px)"
		            srcset="<?= $featured_img_urls['page-featured-image-s-x2']; ?> 2x,
		            		<?= $featured_img_urls['page-featured-image-s']; ?>" type="image/jpeg"></data-src>
			<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid" style="height:  calc(100vh - 38px); object-fit: cover; width: 100%;"></data-img>
			</picture>
			<?php if ( $featured_img_caption || $featured_img_description ) : ?>
			<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
			<?php endif; /* If captions */ ?>
			<!--
			Sizes :
			<?php print_r($featured_img_urls); ?>  
			-->
		</figure>
		<?php if ( $page_atts['header_color'] != '' && $page_atts['header_image_style'] != '' && $page_atts['header_image_style'] == 1 ) { ?>
		<style scoped>
			.duotone-<?= get_post_thumbnail_id() ?> img {
				filter: grayscale(100%) contrast(1);
				mix-blend-mode: screen;
				background-color: <?= $page_atts['header_color'] ?>;
			}
		</style>
		<?php } ?>
		<?php else: ?>
			<div class="alert alert-warning" role="alert"><?= esc_html__( 'You need to choose a thumbnail image', 'waff' ); ?></div>
		<?php endif; ?>				
	</section>
	<!-- END: #pageheader -->
	
	
	<?php elseif ( empty($page_atts['header_style']) || in_array($page_atts['header_style'], array('normal', 'full') ) ): ?>


	<!-- NO #pagetitle : Normal ( petit logo ) & Full ( grand logo ) -->
	<!-- #pageheader -->
	<?php if ( is_singular() && has_post_thumbnail() ) { ?>
	<section id="pageheader" class="<?= (($page_atts['header_style']=='normal')?'normal-header --mt-md-9':'full-header --mt-md-18'); ?> --pb-9 contrast--light has_post_thumbnail" data-aos="fade-up" data-aos-id="pageheader">
		<figure title="<?php echo esc_attr($featured_img_description); ?>" style="background-color:<?= $page_atts['header_color'] ?>;">
			<picture class="lazy duotone-<?= get_post_thumbnail_id() ?>">
			<!-- 3800x1200 > 1900x600 -->
		    <data-src media="(min-width: 990px)"
		            srcset="<?= $featured_img_urls['page-featured-image-x2']; ?> 2x,
		                    <?= $featured_img_urls['page-featured-image']; ?>" type="image/jpeg"></data-src>
		    <data-src media="(min-width: 590px)"
		            srcset="<?= $featured_img_urls['page-featured-image-m-x2']; ?> 2x,
		            		<?= $featured_img_urls['page-featured-image-m']; ?>" type="image/jpeg"></data-src>
			<data-src media="(min-width: 380px)"
					srcset="<?= $featured_img_urls['page-featured-image-s-x2']; ?> 2x,
							<?= $featured_img_urls['page-featured-image-s']; ?>" type="image/jpeg"></data-src>
			<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-sm-600-px" style="object-fit: cover; width: 100%;"></data-img>
			</picture>
			<?php if ( $featured_img_caption || $featured_img_description ) : ?>
			<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
			<?php endif; /* If captions */ ?>
			<!--
			Sizes :
			<?php print_r($featured_img_urls); ?>  
			-->
		</figure>
		<?php if ( $page_atts['header_color'] != '' && $page_atts['header_image_style'] != '' && $page_atts['header_image_style'] == 1 ) { ?>
		<style scoped>
			.duotone-<?= get_post_thumbnail_id() ?> img {
				filter: grayscale(100%) contrast(1);
				mix-blend-mode: screen;
				background-color: <?= $page_atts['header_color'] ?>;
			}
		</style>
		<?php } ?>
	</section>
	<?php } else { ?>
	<section id="pageheader" class="<?= (($page_atts['header_style']=='normal')?'normal-header --mt-md-4':'full-header --mt-md-9'); ?> --pb-9 contrast--light no_post_thumbnail"></section>
	<?php } /* is_singular + has_post_thumbnail */ ?>
	<!-- END: #pageheader -->
	
	<?php endif; //Header style ?>
	
<?php endif; // Args ?>