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
$header_section_title_color 	= 'text-dark color-dark';
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
	case ( 'film' ) :
	    	$selected_featured_sizes = $post_featured_sizes;
	break;
	case ( 'post' ) :
	    	$selected_featured_sizes = $post_featured_sizes;
	break;
	case ( 'page' ) :
	    	$selected_featured_sizes = $page_featured_sizes;
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

<?php if ( $args == 'blog' ) : ?>

	<!-- #pagetitle : Blog -->
	<section id="pagetitle" class="pt-12 pt-md-20 pb-14 --contrast--light container-10 container-left <?= $header_color_class ?>" <?= $header_color ?> --data-aos="fade-up">
		<div class="jumbotron">
		    <div class="container-fluid container-10 container-left">
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
	<section id="pagetitle" class="pt-12 pt-md-20 pb-14 contrast--light container-10 container-left " <?= $page_atts['post_color_class']?>>
		<div class="jumbotron">
		    <div class="container-fluid container-10 container-left">
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
	<section id="pageheader" class="mt-0 mb-0 contrast--light container-10 container-left" data-aos="fade-up" data-aos-id="pageheader">
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

<?php elseif ( $args == 'projection' ) : ?>

	<?php 
		$projection_date 					= get_post_meta( $post->ID, 'wpcf-p-date', true ); 
		$projection_start_and_stop_time 	= get_post_meta( $post->ID, 'wpcf-p-start-and-stop-time', true ); 
		$projection_date_string = wp_kses(
			sprintf(
				'<time datetime="%1$s">%2$s</time>',
				esc_attr( $projection_date ),
				( function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() == 'en' )?date_i18n( 'l M jS, Y', $projection_date) : date_i18n('Y-m-d', $projection_date)
			),
			array_merge(
				wp_kses_allowed_html( 'post' ),
				array(
					'time' => array(
						'datetime' => true,
					),
				)
			)
		);
	?>

	<!-- #pagetitle : Post -->
	<section id="pagetitle" class="pt-12 pt-md-20 pb-14 contrast--light container-10 container-left bg-color-bg" <?= $page_atts['post_color_class']?>>
		<div class="jumbotron">
			<div class="container-fluid container-10 container-left">
				<hgroup>
					<?php if ( $args != '' ) printf('<h5 class="subline text-muted mb-1">%s</h5>', sanitize_text_field(ucfirst($args))); ?>
					<a href="<?= esc_url($partenaire_link) ?>"><h2 class="title --subline mb-0"><?php single_post_title(); ?></h2></a>
					<ul class="list-unstyled mt-2 mb-2">
						<?php if ( $projection_date != '' ) echo '<li class="subline opacity-75 pb-1"><span class="headline medium">'.$projection_date_string.'</span></li>'; ?>
						<?php if ( $projection_start_and_stop_time['begin'] != '' ) echo '<li class="subline opacity-50 pb-1">'.esc_html(__('[:fr]De[:en]From[:]')).' <span class="headline medium">'.$projection_start_and_stop_time['begin'].'</span></li>'; ?>
						<?php if ( $projection_start_and_stop_time['end'] != '' ) echo '<li class="subline opacity-50 pb-1">'.esc_html(__('[:fr]à[:en]To[:]')).' <span class="headline medium">'.$projection_start_and_stop_time['end'].'</span></li>'; ?>
					</ul>
					<?= WaffTwo\waff_entry_meta_header(); ?>
				</hgroup>
			</div>
		</div>
	</section>
	<!-- END: #pagetitle -->

	<!-- #pageheader -->
	<?php if ( is_singular() && has_post_thumbnail() ) { ?>
	<section id="pageheader" class="mt-0 mb-0 contrast--light container-10 container-left bg-light" data-aos="fade-up" data-aos-id="pageheader">
		<figure class="" title="<?php echo esc_attr($featured_img_description); ?>">
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
			<data-img src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-600-px" style="object-fit: cover; width: 100%;"></data-img> <!-- style="height: 600px;" -->
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

<?php elseif ( $args == 'film' ) : ?>
	<?php 
		$film_french_title 		= get_post_meta( $post->ID, 'wpcf-f-french-operating-title', true ); 
		$film_gif 				= get_post_meta( $post->ID, 'wpcf-f-film-gif', true ); 
		$film_length 			= get_post_meta( $post->ID, 'wpcf-f-movie-length', true ); 
		$film_length_seconds	= get_post_meta( $post->ID, 'wpcf-f-movie-length-seconds', true );
		$film_teaser_url 		= get_post_meta( $post->ID, 'wpcf-f-teaser-urls', true ); 
		$film_online 			= get_post_meta( $post->ID, 'wpcf-f-is-available-online', true ); 
		$film_online_url 		= get_post_meta( $post->ID, 'wpcf-f-online-url', true ); 
		$film_ticketing_url 	= get_post_meta( $post->ID, 'wpcf-f-ticketing-url', true ); 

		$film_length 			= (( $film_length != '0' && $film_length != '' )?$film_length = sprintf(' <span class="--length --light subline-length subline-4">%s\'<span class="op-3">%s</span></span>', esc_attr($film_length), (( $film_length_seconds != 0 && $film_length_seconds != '' )?esc_attr($film_length_seconds . '"'):'') ):'');  
		$film_awards 			= get_the_terms($post->ID, 'award'); 
	?>
	<!-- #pagetitle : Film -->
	<section id="pagetitle" class="mt-12 mt-md-20 mb-14 contrast--light">
		<div class="jumbotron">
		    <div class="container-fluid">
				<hgroup>
					<h1 class="title mb-0"><?= (( $film_french_title != '' )?sanitize_text_field($film_french_title):sanitize_text_field($title)); ?><?= $film_length ?> <?php echo do_shortcode('[wacp_favorite_star film_id="'.$post->ID.'"]'); ?></h1>
					<?php if ( $film_french_title != '' ) printf('<h5 class="subline-4 text-muted mb-1">%s</h5>', sanitize_text_field($title)); ?>
					<?= WaffTwo\waff_entry_meta_header(); ?>
					
				</hgroup>
		    </div>
		</div>
	</section>
	<!-- END: #pagetitle -->
	
	<!-- #pageheader -->
	<section id="pageheader" class="mt-10 mb-0 mb-sm-10 contrast--light" data-aos="fade-up" data-aos-id="pageheader">
		<figure title="<?php echo esc_attr($featured_img_description); ?>">
		    <picture class="lazy">
			<?php if ( $film_gif != '' ) : ?>
			<img src="<?= $film_gif; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-sm-600-px" style="object-fit: cover; width: 100%;"></img>
			<?php else : ?>
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
			<data-img id="filmpicture" src="<?= $featured_img_urls['thumbnail']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid h-sm-600-px" style="object-fit: cover; width: 100%;"></data-img>
			<?php endif; ?>
			</picture>
			<?php if ( $featured_img_caption || $featured_img_description ) : ?>
			<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
			<?php endif; /* If captions */ ?>
			<!--
			Sizes :
			<?php print_r($featured_img_urls); ?>  
			-->
		</figure>

		<!-- Play -->
		<?php if ( $film_teaser_url != '' ) : ?>
			<div class="position-absolute top-0 h-100 w-100 btn_holder">
				<a href="<?= esc_url($film_teaser_url) ?>?rel=0&amp;showinfo=0" class="btn white play" target="_blank" data-fancybox="header_<?= $post->ID; ?>_fancybox"><i class="fas fa-play"></i></a>
			</div>
		<?php endif; /* If film_teaser_url */ ?>

		<!-- Awards image -->
		<?php if ( count($film_awards) > 0 ) : ?>
			<div class="position-absolute top-0 start-0 mt-4 mr-6 me-6">
				<?php foreach( $film_awards as $award ) :
					$award_image 						= get_term_meta( $award->term_id, 'wpcf-a-light-image', true ); 
				?>
				<img src="<?= esc_url($award_image) ?>" alt="<?= $award->name; ?>" title="<?= $award->name; ?>" width="200"/>
				<?php endforeach; /* If film_awards */ ?>
			</div>
		<?php endif; /* If film_awards */ ?>

		<!-- Ticketing -->
		<?php if ( $film_ticketing_url != '' ) : ?>
			<div class="position-absolute top-0 end-0 container-fluid px-0">
				<div class="row g-0 align-items-center">
					
					<!-- Col -->
					<div class="col-12 col-md-10 d-none d-sm-block">
						<!-- Flex -->
						<div class="d-flex justify-content-between--- align-items-center">
							<!--<div class="mr-2 me-2 --ml-3 --ms-3 m-gutter-l flash-title headline text-nowrap ">Billetterie <span class="sr-only">Réserver ma place grâce à la billeterie en ligne</span></div>-->
						</div>
						<!-- End Flex -->
					</div>
					
					<!-- Col -->
					<div class="col-12 col-md-2 bg-action-2 text-center text-dark link-dark">
						<div class="p-2"><a href="<?= esc_url($film_ticketing_url) ?>" target="_blank" class="prog-title --headline h5 link my-2"><i class="bi bi-ticket me-2"></i><?= esc_html(__('[:fr]Réserver ma place[:en]Book my ticket[:]')); ?></a></div>
					</div>	
				
				</div> 
			</div>
		<?php endif; /* If film_ticketing_url */ ?>	

		<!-- Projection Ticketing > see waff-functions -->
		<?php if ( do_shortcode( '[film_has_projections filmid="'.$post->ID.'" meta="wpcf-p-ticketing-url"]' ) != '' ) : ?>
			<div class="position-absolute top-0 end-0 container-fluid px-0">
				<div class="row g-0 align-items-center">
					
					<!-- Col -->
					<div class="col-12 col-md-10 d-none d-sm-block">
						<!-- Flex -->
						<div class="d-flex justify-content-between--- align-items-center">
							<!--<div class="mr-2 me-2 --ml-3 --ms-3 m-gutter-l flash-title headline text-nowrap ">Billetterie <span class="sr-only">Réserver ma place grâce à la billeterie en ligne</span></div>-->
						</div>
						<!-- End Flex -->
					</div>
					
					<!-- Col -->
					<div class="col-12 col-md-2 bg-action-2 text-center text-dark link-dark">
						<div class="p-2"><a href="#all_projections" class="prog-title --headline h5 link my-2"><i class="bi bi-ticket me-2"></i><?= esc_html(__('[:fr]Réserver une séance[:en]Book a ticket[:]')); ?></a></div>
					</div>	
				
				</div> 
			</div>
		<?php endif; /* If film_ticketing_url */ ?>	

		<!-- Vote -->
		<?php 
		/* Get film section terms */ 
		$film_sections = get_the_terms( $post->ID, 'section' );
		// Get section get_term_link
		if ( $film_sections && ! is_wp_error( $film_sections ) ) {
			$film_section = $film_sections[0]; // Get first term only
			$film_section_link = get_term_link( $film_section->term_id, 'section' );
		}
		?>
		<?php if ( strpos( $film_section_link, 'long' ) !== false && strpos( $film_section_link, 'competition' ) !== false ) : ?>
			<div class="position-absolute bottom-0 end-0 container-fluid px-0">
				<div class="row g-0 align-items-center">
					
					<!-- Col -->
					<div class="col-12 col-md-10 d-none d-sm-block">
						<!-- Flex -->
						<div class="d-flex justify-content-between--- align-items-center">
							<div class="mr-2 me-2 --ml-3 --ms-3 m-gutter-l flash-title headline text-nowrap">Vote du public <span class="sr-only">Voter et noter votre film préféré en compétition </span></div>
						</div>
						<!-- End Flex -->
					</div>
					
					<!-- Col -->
					<div class="col-12 col-md-2 bg-black text-center text-light link-light">
						<div class="p-2"><a href="http://vote.fifam.fr" target="_blank" class="prog-title --headline h5 link my-2"><i class="icon icon-premiere-2 me-2"></i><?= esc_html(__('[:fr]Voter pour ce film[:en]Vote for this film[:]')); ?></a></div>
					</div>	
				
				</div> 
			</div>
		<?php endif; /* If vote */ ?>	

		
	</section>
	<!-- END: #pageheader -->

<?php elseif ( $args == 'jury' ) : ?>
	<?php 
		$jury_description 	= get_post_meta( $post->ID, 'wpcf-j-description', true ); 
		$jury_master 		= get_post_meta( $post->ID, 'wpcf-j-master', true ); 
	?>
	<!-- #pagetitle : Jury -->
	<section id="pagetitle" class="mt-12 mt-md-20 mb-14 contrast--light">
		<div class="jumbotron">
		    <div class="container-fluid">
				<hgroup>
					<h1 class="title mb-0 <?= $header_section_title_color ?>"><?= sanitize_text_field($title) ?></h1>
					<?php if ( $jury_description != '' ) printf('<h5 class="subline-4 text-muted mb-1">%s</h5>', do_shortcode(sanitize_text_field($jury_description))); ?>
					<ul class="list-unstyled mt-2 mb-2">
						<?php if ( $jury_master != '' ) echo '<li class="subline opacity-75"><span class="headline medium">'.esc_html(__('[:fr]Président du jury[:en]Jury master[:]')).'</span></li>'; ?>
					</ul>
					<?= WaffTwo\waff_entry_meta_header(); ?>
				</hgroup>
		    </div>
		</div>
	</section>
	<!-- END: #pagetitle -->
	
	<!-- #pageheader -->
	<?php if ( is_singular() && has_post_thumbnail() ) { ?>
	<section id="pageheader" class="<?= (($page_atts['header_style']=='normal')?'normal-header mt-md-9':'full-header mt-md-18'); ?> --pb-9 contrast--light has_post_thumbnail" data-aos="fade-up" data-aos-id="pageheader">
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
	<?php } /* is_singular + has_post_thumbnail */ ?>
	<!-- END: #pageheader -->

<?php elseif ( $args == 'section' ) : ?>
	<?php 
		global $section_title_color, $section_color; /* Pass to next php templates */ 

		$section_id 						= get_queried_object_id();
		$section_color 						= get_term_meta( $section_id, 'wpcf-s-color', true ); 
		$section_color_class				= 'contrast--light';
		$section_title_color 				= 'color-dark'; //color-black
		if ( $section_color != '' ) {
			$rgb = WaffTwo\Core\waff_HTMLToRGB($section_color);
			$hsl = WaffTwo\Core\waff_RGBToHSL($rgb);
			if($hsl->lightness < $lightness_threshold) {
				$section_title_color 		= 'color-light'; //color-white
				$section_color_class 		= 'contrast--dark';
			}
			$section_color = sprintf('style="background-color:%s;"', esc_attr($section_color) );
		} else {
			$section_color 					= '';  
		}
		$section_image 						= get_term_meta( $section_id, 'wpcf-s-image', true ); 
		$section_image_ID 					= WaffTwo\Core\waff_get_image_id_by_url($section_image);
		$featured_img_caption 				= wp_get_attachment_caption($section_image_ID); // ADD WIL                    
		$thumb_img 							= get_post( $section_image_ID ); // Get post by ID
		$featured_img_description 			= $thumb_img->post_content; // Display Description
		if ( function_exists( 'types_render_termmeta' ) ) {
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'term_id' => $section_id ) ); 
			//$section_images[] 				= types_render_termmeta( 's-image', array( "alt" => "blue bird", "width" => "300", "height" => "200", "proportional" => "true" ) );
			$section_image 						= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-x2', 'alt' => esc_html($featured_img_caption), 'style' => 'height: 600px; object-fit: cover; width: 100%;', 'class' => 'img-fluid' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-m-x2' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-m' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-s-x2' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-s' ) );
		}
		$section_credits_image 				= get_term_meta( $section_id, 'wpcf-s-credits-image', true ); 
		$section_featured_film_1 			= get_term_meta( $section_id, 'wpcf-s-featured-film-1', true ); 
		$section_featured_film_2 			= get_term_meta( $section_id, 'wpcf-s-featured-film-2', true ); 

		// Méthode pour générer à la volée > les images sont mises en caches dans /uploads/ avec prefix ex : wpv-300x300_center_center 
		//1
		if ( $section_featured_film_1 != '') :
		$section_featured_film_1_imgs 	= array();
		$section_featured_film_1_imgs['300x300'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_1."' size='custom' width='300' height='300' crop='true' output='url']" );
		$section_featured_film_1_imgs['500x300'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_1."' size='custom' width='500' height='300' crop='true' output='url']" );
		$section_featured_film_1_imgs['1000x600'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_1."' size='custom' width='1000' height='600' crop='true' output='url']" );
		//$section_featured_film_1_imgs['2000x1200'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_1."' size='custom' width='2000' height='1200' crop='true' output='url']" );
		$section_featured_film_1_title 		= get_the_title( $section_featured_film_1 ); 
		$section_featured_film_1_subtitle 	= get_post_meta( $section_featured_film_1, 'wpcf-f-french-operating-title', true );
		$section_featured_film_1_subtitle 	= (( $section_featured_film_1_subtitle != '' )?' ('.$section_featured_film_1_subtitle.')':'');
		$section_featured_film_1_length 	= get_post_meta( $section_featured_film_1, 'wpcf-f-movie-length', true ); 
		$section_featured_film_1_length = (( $section_featured_film_1_length != 0 && $section_featured_film_1_length != '' )?$section_featured_film_1_length = sprintf(' <span class="length light">%d\'</span>', esc_attr($section_featured_film_1_length) ):'');  
		endif;

		//2 
		if ( $section_featured_film_2 != '') :
		$section_featured_film_2_imgs 	= array();
		$section_featured_film_2_imgs['300x300'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_2."' size='custom' width='300' height='300' crop='true' output='url']" );
		$section_featured_film_2_imgs['500x300'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_2."' size='custom' width='500' height='300' crop='true' output='url']" );
		$section_featured_film_2_imgs['1000x600'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_2."' size='custom' width='1000' height='600' crop='true' output='url']" );
		//$section_featured_film_2_imgs['2000x1200'] = do_shortcode( "[wpv-post-featured-image item='".$section_featured_film_2."' size='custom' width='2000' height='1200' crop='true' output='url']" );
		$section_featured_film_2_title 		= get_the_title( $section_featured_film_2 ); 
		$section_featured_film_2_subtitle 	= get_post_meta( $section_featured_film_2, 'wpcf-f-french-operating-title', true ); 
		$section_featured_film_2_subtitle 	= (( $section_featured_film_2_subtitle != '' )?' ('.$section_featured_film_2_subtitle.')':'');
		$section_featured_film_2_length 	= get_post_meta( $section_featured_film_2, 'wpcf-f-movie-length', true ); 
		$section_featured_film_2_length = (( $section_featured_film_2_length != 0 && $section_featured_film_2_length != '' )?$section_featured_film_2_length = sprintf(' <span class="length light">%d\'</span>', esc_attr($section_featured_film_2_length) ):'');  
		endif;
		
	?>
	<!-- #pagetitle = section -->
	<section id="pagetitle" class="pt-12 pt-md-20 pb-14 <?= esc_attr($section_color_class) ?>" <?= $section_color ?>> <!-- ?? pt-md-18 pb-9-->
		<div class="jumbotron">
		    <div class="container-fluid container-10 container-left">
				<hgroup data-aos="fade-down">
					<h1 class="title mb-0 <?= esc_attr($section_title_color) ?>"><?= sanitize_text_field(single_term_title()) ?></h1>
					<?= WaffTwo\waff_entry_meta_header(); ?>
				</hgroup>
		    </div>
		</div>
	</section>
	
	<!-- #pageheader -->
	<section id="pageheader" class="--mt-10 mt-0 --mb-10 contrast--light" data-aos="fade-up" data-aos-id="pageheader">
		<figure title="<?php echo esc_attr($featured_img_description); ?>">
		    <picture class="lazy">
			<!-- 3800x1200 > 1900x600 -->
			<?= $section_image ?>
			</picture>
			<?php if ( $featured_img_caption || $featured_img_description ) : ?>
			<figcaption><strong>© <?= esc_html($featured_img_caption); ?></strong> <?= esc_html($featured_img_description); ?></figcaption>
			<?php elseif ( $section_credits_image ) : ?>
			<figcaption><strong>© <?= esc_html($section_credits_image); ?></strong></figcaption>
			<?php endif; /* If captions */ ?>
		</figure>
		<?php if ( $section_featured_film_1 || $section_featured_film_2 ) : ?>
		<div class="container-fluid px-0">
			<div class="row g-0">
				<?php if ( $section_featured_film_1 ) : ?>
				<figure class="col">
					<picture class="lazy">
					<source media="(min-width: 990px)" srcset="<?= $section_featured_film_1_imgs['1000x600'] ?>" type="image/jpeg"> <!--  <?= $section_featured_film_1_imgs['2000x1200'] ?> 2x, -->
					<source media="(min-width: 590px)" srcset="<?= $section_featured_film_1_imgs['500x300'] ?>" type="image/jpeg">
					<img src="<?= $section_featured_film_1_imgs['300x300'] ?>" alt="<?= $section_featured_film_1_title ?>" class="img-fluid" style="height: 300px; object-fit: cover; width: 100%;">
					</picture>
					<figcaption><h6 class="d-inline"><?= $section_featured_film_1_title ?><?= $section_featured_film_1_subtitle ?><?= $section_featured_film_1_length ?></h6></figcaption>
				</figure>
				<?php endif; ?>
				<?php if ( $section_featured_film_2 ) : ?>
				<figure class="col">
					<picture class="lazy">
					<source media="(min-width: 990px)" srcset="<?= $section_featured_film_2_imgs['1000x600'] ?>" type="image/jpeg"> <!--  <?= $section_featured_film_2_imgs['2000x1200'] ?> 2x, -->
					<source media="(min-width: 590px)" srcset="<?= $section_featured_film_2_imgs['500x300'] ?>" type="image/jpeg">
					<img src="<?= $section_featured_film_2_imgs['300x300'] ?>" alt="<?= $section_featured_film_2_title ?>" class="img-fluid" style="height: 300px; object-fit: cover; width: 100%;">
					</picture>
					<figcaption><h6 class="d-inline"><?= $section_featured_film_2_title ?><?= $section_featured_film_2_subtitle ?><?= $section_featured_film_2_length ?></h6></figcaption>
				</figure>
				<?php endif; ?>
			</div>
		</div>
		<?php endif; /* If fetured films */ ?>
	</section>
	<!-- END: #pageheader -->

<?php elseif ( $args == 'room' ) : ?>
	<?php 
		global $room_id, $room_slug, $room_parent_tax, $room_title_color, $room_color, $_room_color; /* Pass to next php templates */ 
		/* Look for a parent term if existing */
		$room_tax 						= $wp_query->get_queried_object();
		$room_display_child				= get_term_meta( get_queried_object_id(), 'wpcf-r-show-child-content', true ); 
		$room_parent_tax = get_term_by('term_id', $room_tax->parent, 'room');
		if ( !empty( $room_parent_tax ) && !is_wp_error( $room_parent_tax ) && $room_display_child == '0' ) :
			$room_id					= $room_parent_tax->term_id;
			$room_slug 					= $room_parent_tax->slug;
		else :
			$room_id 					= get_queried_object_id();
			$room_slug 					= $room_tax->slug;
		endif;
		$room_color = $_room_color		= get_term_meta( $room_id, 'wpcf-r-color', true ); 
		$room_color_class				= 'contrast--light';
		$room_title_color 				= 'color-light'; //color-white
		if ( $room_color != '' ) {
			$rgb = WaffTwo\Core\waff_HTMLToRGB($room_color);
			$hsl = WaffTwo\Core\waff_RGBToHSL($rgb);
			if($hsl->lightness < $lightness_threshold) {
				$room_color_class 		= 'contrast--dark';
				$room_title_color 		= 'color-light'; //color-white
			} else {
				$room_color_class 		= 'contrast--white';
				$room_title_color 		= 'color-dark'; //color-black
			}
			$room_color = sprintf('style="background-color:%s;"', esc_attr($room_color) );
		} else {
			$room_color 					= '';  
		}
		$room_image 						= get_term_meta( $room_id, 'wpcf-r-image', true ); 
		$room_image_ID 						= WaffTwo\Core\waff_get_image_id_by_url($room_image);
		$room_image_caption 				= wp_get_attachment_caption($room_image_ID); // ADD WIL                    
		$room_thumb_img 					= get_post( $room_image_ID ); // Get post by ID
		$room_image_description 			= $room_thumb_img->post_content; // Display Description
		if ( function_exists( 'types_render_field' ) ) {
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'term_id' => $section_id ) ); 
			//$section_images[] 				= types_render_termmeta( 's-image', array( "alt" => "blue bird", "width" => "300", "height" => "200", "proportional" => "true" ) );
			$room_image 						= types_render_termmeta( 'r-image', array( 'term_id' => $room_id ,'size' => 'post-featured-image-x2', 'alt' => esc_html($room_image_caption), 'style' => 'object-fit: cover; width: 100%;', 'class' => 'img-fluid h-sm-600-px' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-m-x2' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-m' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-s-x2' ) );
			//$section_images[] 				= types_render_termmeta( 's-image', array( 'size' => 'post-featured-image-s' ) );
		}

		$room_address 				= get_term_meta( $room_id, 'wpcf-r-adress', true ); 
		$room_maximum 				= get_term_meta( $room_id, 'wpcf-r-maximum-capacity', true ); 
		$room_gauge 				= get_term_meta( $room_id, 'wpcf-r-gauge-capacity', true ); 
		$room_pmr 					= get_term_meta( $room_id, 'wpcf-r-pmr-capacity', true ); 
		$room_openings 				= get_term_meta( $room_id, 'wpcf-r-opening-days-hours', true ); 

		$room_openings = json_decode($room_openings);
		$day = 0;
		$days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
		$openings = '';
		$_keys = array();
		foreach($room_openings as $key => $value) {
			if ( $value->isActive == 1 ) {
				if ( $room_openings[$key+1]->timeFrom != $value->timeFrom || $room_openings[$key+1]->timeTill != $value->timeTill ) {
					//echo '≠';
					if ( count($_keys) > 0 ) {
						$openings .= '<li class="--subline"><strong>Du '.$days[$_keys[0]].' au '.$days[$key].'</strong> de '.$value->timeFrom.' à '.$value->timeTill.'</li>';
						$_keys = array();
					} else {
						$openings .= '<li class="--subline"><strong>Le '.$days[$key].'</strong> de '.$value->timeFrom.' à '.$value->timeTill.'</li>';
					}
				} else {
					//echo '=';
					$_keys[] = $key;
				}
			}
		}		
	?>	<!-- #pagetitle : Modern -->
	<section id="pagetitle" class="mt-0 mt-md-9 mb-0 --contrast--light bg-action-1 modern-header <?= esc_attr($room_color_class) ?>" <?= $room_color ?> data-aos="fade-up" data-aos-id="pagetitle">
	<div class="jumbotron">
		<div class="container-fluid">
			<div class="row pt-sm-20 pt-15">
				<div class="col-sm-7 col-12 zi-5">
					<hgroup data-aos="fade-down">
						<h1 class="heading-3 title mb-2 <?= esc_attr($room_title_color) ?>"><?= sanitize_text_field(single_term_title()) ?></h1>
						<?= WaffTwo\waff_entry_meta_header(); ?>	
					</hgroup>
				</div>
			</div>
			<div class="row <?= (($room_image != '')?'mt-n':'mt-4') ?> align-items-end g-0 f-w">
				<div class="col-sm-5 col-12">
					<?php if ( $room_address != '' ) echo '<h6 class="m-gutter-l headline d-inline '.$room_title_color.'">'.$room_address.'</h6>'; ?>
					<ul class="p-gutter-l list-unstyled mt-3 mb-2 <?= $room_title_color ?>">
						<?= $openings ?>
					</ul>
					<ul class="p-gutter-l list-unstyled mt-2 mb-8 <?= $room_title_color ?>">
						<?php if ( $room_gauge != '' ) echo '<li class="subline opacity-75"><strong>Capacité</strong> <span class="headline medium">'.$room_gauge.'</span></li>'; ?>
						<?php if ( $room_maximum != '' ) echo '<li class="subline opacity-75"><strong>Capacité max.</strong> <span class="headline medium">'.$room_maximum.'</span></li>'; ?>
						<?php if ( $room_pmr != '' ) echo '<li class="subline opacity-75"><strong>Places PMR</strong> <span class="headline medium">'.$room_pmr.'</span></li>'; ?>
					</ul>
				</div>
				<div class="col-sm-7 col-12">
					<figure title="<?php echo esc_attr($room_image_description); ?>" style="background-color:<?= $_room_color ?>;">
						<picture class="lazy duotone-<?= $room_id ?>">
						<!-- 1200x900 > 800x600 (1600x1100 > 800x550) -->
							<?= $room_image ?>
						</picture>
						<?php if ( $room_image_caption || $room_image_description ) : ?>
						<figcaption><strong>© <?= esc_html($room_image_caption); ?></strong> <?= esc_html($room_image_description); ?></figcaption>
						<?php endif; /* If captions */ ?>
					</figure>
					<style scoped>
						.duotone-<?= $room_id ?> img {
							filter: grayscale(100%) contrast(1);
							mix-blend-mode: screen;
							background-color: <?= $_room_color ?>;
						}
					</style> 
				</div>
			</div>
		</div>
	</div>
</section>
<!-- END: #pagetitle -->

<?php else: ?>	

	<!-- #NOARGS ( ≠ film / post / section ... ) / Header style : <?= $page_atts['header_style'] ?> -->

	<?php if ( !empty($page_atts['header_style']) && in_array($page_atts['header_style'], array('modern') ) ): ?>

	<!-- #pagetitle : Modern -->
	<section id="pagetitle" class="modern-header mt-0 mt-md-9 mb-0 --contrast--light bg-action-1 <?= $header_color_class ?>" <?= $header_color ?> data-aos="fade-up" data-aos-id="pagetitle">
		<div class="jumbotron">
		    <div class="container-fluid">
				<div class="row pt-sm-20 pt-15">
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
					<div class="col-sm-7 col-6">
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
	<section id="pageheader" class="mt-0 mb-0 contrast--light h-100 lg-vh-50 position-relative split-header is-formatted" data-aos="fade-down" data-aos-id="pageheader">
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

				<div class="header-content col-md overflow-hidden bg-color-bg h-100 d-flex flex-column justify-content-between align-items-start p-3 ps-lg-5 pe-lg-5 pb-lg-5 pt-lg-20 <?= $header_color_class ?>" <?= $header_color ?> --data-aos="fade-left">
					<!-- Titles -->
					<hgroup>
						<?= WaffTwo\waff_entry_meta_header(); ?>
						<!-- <?php if ( $page_atts['subtitle'] != '' ) echo '<h6 class="headline d-inline-block my-3 '.$header_section_title_color.'">'.do_shortcode(sanitize_text_field($page_atts['subtitle'])).'</h6>'; ?> -->
						<h1 class="<?= $header_section_title_color ?>"><?= sanitize_text_field($title) ?></h1>
						<?php if ( $page_atts['subtitle'] != '' ) echo '<h5 class="opacity-75 '.$header_section_title_color.'">'.do_shortcode(sanitize_text_field($page_atts['subtitle'])).'</h5>'; ?>
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
	<section id="pageheader" class="fancy-header mt-0 --mb-10 mb-0 contrast--light <?= $header_color_class ?>" <?= $header_color ?> data-aos="fade-up" data-aos-id="pageheader">
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
	<section id="pageheader" class="<?= (($page_atts['header_style']=='normal')?'normal-header mt-md-9':'full-header mt-md-18'); ?> --pb-9 contrast--light has_post_thumbnail" data-aos="fade-up" data-aos-id="pageheader">
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
	<section id="pageheader" class="<?= (($page_atts['header_style']=='normal')?'normal-header mt-md-4':'full-header mt-md-9'); ?> --pb-9 contrast--light no_post_thumbnail"></section>
	<?php } /* is_singular + has_post_thumbnail */ ?>
	<!-- END: #pageheader -->
	
	<?php endif; //Header style ?>
	
<?php endif; // Args ?>