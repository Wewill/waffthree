<?php
/**
 * File: archive.php (for archives and blog landing).
 *
 * @package Waff
 */
// use function Go\hex_to_hsl;
// use function WaffTwo\Core\waff_HTMLToRGB;

// Lightness threshold
$lightness_threshold = 130;

// Debut Relocate 
global $current_edition, $previous_editions, $current_edition_id, $current_edition_slug, $current_edition_films_are_online;

// Relocate
$relocate = false;
$shownotice = true;

// Get user loggued in
$user = wp_get_current_user();
$allowed_roles = array('fifam_editor', 'fifam_admin', 'fifam_subscriber', 'administrator');

// Get current_edition_films_are_online option
/*$current_edition_films_are_online = get_option('current_edition_films_are_online');
$current_edition_films_are_online = ( !empty($current_edition_films_are_online) && $current_edition_films_are_online == 1)?true:false; */

// Get select edition term
$tax = $wp_query->get_queried_object();
$selected_edition = get_term_meta($tax->term_id,'wpcf-select-edition',true); 

// Search into terms if one is the current edition
/*$terms = get_terms( 'edition', array('hide_empty' => false));
foreach ( $terms as $term ) {
	$meta = get_term_meta($term->term_id, 'wpcf-e-current-edition', true);
	if ( isset($meta) && $meta == 1)
		$current_edition_id = $term->term_id;
}*/

// Redirect if not current edition section
// Don\'t do any of that if USER is loggued / THEN write it ( below) 
if ( $selected_edition != $current_edition_id ) {
	if ( empty(array_intersect($allowed_roles, $user->roles )) )
		$relocate = true;
} 

// Redirect if option current content online 
// Don\'t do any of that if USER is loggued / THEN write it ( below) 
if( $current_edition_films_are_online == false ){
	if ( empty(array_intersect($allowed_roles, $user->roles )) )
		$relocate = true;
} 

if ( 	$selected_edition == $current_edition_id
		&& $current_edition_films_are_online == true
	) 
	$shownotice = false;


if( $relocate == true ){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".get_bloginfo('url'));
	exit();
	//echo 'JE REDIRIGE';
}

// Write overaccess for TEAM ($allowed_roles)  
if( array_intersect($allowed_roles, $user->roles ) &&  $shownotice == true ) { 
?> 
<div class="admin-notice" style="background: hsl(275, 100%, 50%)!important;">
<!-- Notice for allowed roles -->
	<div class="admin-notice-content" style="color: #FFF!important;padding-top:2%;padding-bottom:3%;padding-left:30%;padding-right:30%;">
		<h4 style="text-align: center;font-size: 1.4em;color: #FFF;margin-bottom: 1em;">Hidden content / Contenu caché</h4>
		<p style="text-align: center;">You can see this content only because you are loggued to the website and you are an administrator or editor. Otherwise it will show only if the current edition match selected edition and if push online option is true and if film status is approved or programmed.</p>
		<p style="text-align: center;"><em>Vous pouvez voir ce contenu uniquement parce que vous êtes connecté au site web et que vous êtes administrateur ou éditeur. Sinon, il ne sera affiché que si l'édition actuelle correspond à l'édition sélectionnée et si l'option de mise en ligne est cochée et si le status de film est approuvé ou programmé.</em></p>
		<pre style="border: .15rem solid rgba(255, 255, 255, 0.75);line-height: 1.5;padding: 2rem 2rem;text-align: left;"><strong>Selected edition : </strong><?php echo $selected_edition; ?><br/><strong>Current edition : </strong><?php echo $current_edition_id; ?><br/><strong>Option : films are online : </strong><?php var_dump($current_edition_films_are_online);?><strong>User loggued in : </strong><?php echo $user->roles[0]; ?></pre>
	</div>
<!-- End: Notice for allowed roles -->
</div>
<?php 
}

get_header();

//Go\page_title();
// error_log('get_queried_object_id');
// error_log(get_queried_object_id());

if ( function_exists('get_counts') )
	$counts = get_counts('section', get_queried_object_id(), null);

// Order query > see waff_functions

// Section loop 
if ( have_posts() ) {

	/*$results = get_view_query_results(54339, $post_in, $current_user, $args = array(
		'wpvsection' => get_queried_object()->slug,
		'wpvedition' => $current_edition_slug,
	));	
	print_r($results);*/


	global $section_title_color, $section_color; /* From pagetitles/content php partial */ 

	$section_id 						= get_queried_object_id();
	$section_description 				= term_description($section_id);
	//print_r('<pre>'.htmlspecialchars($section_description).'</pre>');
	$section_content 					= get_term_meta( $section_id, 'wpcf-s-content', true ); 
	//print_r('<pre>'.$section_content.'</pre>');
	//print_r('<pre>'.htmlspecialchars($section_content).'</pre>');
	//print_r('<pre>'.strip_tags($section_content).'</pre>');
	$section_additionnal_content 		= get_term_meta( $section_id, 'wpcf-s-additionnal-content', true ); 

	$filmography = '';
	$section_filmography_of 			= get_term_meta( $section_id, 'wpcf-s-filmography-of', true ); 
	$section_filmography				= get_term_meta( $section_id, 'wpcf-s-filmography', false ); 
	$section_filmography_count			= count($section_filmography);
	$section_filmography_index = 0;
	foreach( $section_filmography as $key => $value) {
		if ( $value['film'] != '' ) {
			$filmography .=
			sprintf('<span class="lead impact">%s</span>&nbsp;<span class="subline">%s</span>%s',
			WaffTwo\Core\waff_do_markdown(strip_tags(esc_html($value['film']))),
			esc_html($value['year']),
			((++$section_filmography_index !== $section_filmography_count)?' • ':'')
			);
		}
	}
	$section_edition					= get_term( $selected_edition, 'edition' );
	$section_edition					= (( !empty($section_edition) )?__('Edition', 'waff').' '.$section_edition->name:'');

	$section_godparents					= get_term_meta( $section_id, 'wpcf-s-godparent', false ); 

	?>
	<!-- Punchlines -->
	<div class="row punchlines" data-section-id="<?= $section_id ?>">
	
		<div class="col-12 col-lg-10">

			<!-- Godparents -->
			<?php if ( !empty($section_godparents) ) : ?> 
				<?php foreach ($section_godparents as $section_godparent) :
						if ( $section_godparent != null ) : 
							$sg_metas 			= get_post_meta( $section_godparent ); 

							$sg_picture 			= get_post_meta( $section_godparent, 'wpcf-c-picture', true ); 
							$sg_image_ID 			= WaffTwo\Core\waff_get_image_id_by_url($sg_picture);
							$img_caption 			= wp_get_attachment_caption($sg_image_ID); // ADD WIL                    
							$thumb_img 				= get_post( $sg_image_ID ); // Get post by ID
							$img_description 		= $thumb_img->post_content; // Display Description

							if ( function_exists( 'types_render_field' ) ) {
								$sg_picture = types_render_field( 'c-picture', array( 
										'item' => $section_godparent, 
										'width' => '40', 'height' => '40', 'proportional' => 'false', 
										'alt' => esc_html((( !empty($sg_metas['wpcf-c-surname']) )?$sg_metas['wpcf-c-surname'][0]:$sg_metas['wpcf-c-lastname'][0] . ' ' . $sg_metas['wpcf-c-firstname'][0])), 
										'style' => 'object-fit: cover; width: 100%;', 
										'class' => 'attachment-custom aligncenter rounded-circle align-center --w-40-px img-fluid')
								); // Fixing non rounded img issue : removing w-40-px and style : height: 40px;
							}
					?> 
						<div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
							<div class="w-100 d-flex justify-content-center justify-content-md-start mt-4 mt-md-0">
								<span>
										<span class="badge text-wrap color-black text-dark text-left text-start text-uppercase normal" style="max-width: 7rem;"><small><?php _e('Godfather', 'waff')?>  &<strong class="bold d-block"><?php _e('Godmother', 'waff')?>  </strong></small></span>
								</span>
							</div>
							<div class="w-30 d-flex flex-columns align-items-center justify-content-center">
								<!-- s-godparent picture-->
								<span class="me-2">
									<figure title="<?php echo esc_attr($img_description); ?>">
										<picture class="lazy">
										<!-- 3800x1200 > 1900x600 -->
										<?= $sg_picture ?>
										</picture>
										<?php if ( $img_caption || $img_description ) : ?>
										<figcaption><strong>© <?= esc_html($img_caption); ?></strong> <?= esc_html($img_description); ?></figcaption>
										<?php elseif ( $sg_metas['wpcf-c-photo-credits'][0] ) : ?>
										<figcaption><strong>© <?= esc_html($sg_metas['wpcf-c-photo-credits'][0]); ?></strong></figcaption>
										<?php endif; /* If captions */ ?>
									</figure>
								</span>
								<!-- s-godparent name -->
								<span class="w-100-px text-wrap text-center">
									<span class="heading heavy"><?= (( !empty($sg_metas['wpcf-c-surname']) )?$sg_metas['wpcf-c-surname'][0]:$sg_metas['wpcf-c-lastname'][0] . ' ' . $sg_metas['wpcf-c-firstname'][0]) ?></span>
								</span>
							</div>
							<div class="w-100 d-flex justify-content-center justify-content-md-end mt-4 mt-md-0">
								<!-- s-godparent metas -->
								<span>
									<?php if ( !empty($sg_metas['wpcf-c-organization']) || !empty($sg_metas['wpcf-c-structure']) || !empty($sg_metas['wpcf-c-position'])) : ?> 
										<span class="badge text-wrap color-black text-dark text-left text-start text-uppercase normal" style="max-width: 7rem;"><small><?= $sg_metas['wpcf-c-organization'][0] ?> <?= $sg_metas['wpcf-c-structure'][0] ?> <strong class="bold d-block"><?= $sg_metas['wpcf-c-position'][0] ?></strong></small></span>
									<?php endif; ?>

									<?php if ( !empty($sg_metas['wpcf-c-birthdate']) ) : ?> 
										<span class="badge text-wrap color-black text-dark text-left text-start text-uppercase normal" style="max-width: 7rem;"><small><?php _e('Birthdate', 'waff')?> <strong class="bold d-block"><?= types_render_field('c-birthdate', array( 'item' => $section_godparent) ) ?></strong></small></span>
									<?php endif; ?>

									<?php if ( !empty($sg_metas['wpcf-c-country']) ) : ?> 
										<span class="badge text-wrap color-black text-dark text-left text-start text-uppercase normal" style="max-width: 7rem;"><small><?php _e('Country', 'waff')?>  <strong class="bold d-block"><?= $sg_metas['wpcf-c-country'][0] ?></strong></small></span>
									<?php endif; ?>
								</span>
							</div>
						</div>
						<div class="mb-8">
							<p class="lead text-center">
							<?= (( !empty($sg_metas['wpcf-c-biofilmography-french']) )?$sg_metas['wpcf-c-biofilmography-french'][0]:'' ) ?>
							<?= (( !empty($sg_metas['wpcf-c-biofilmography-english']) )?'<em>' . $sg_metas['wpcf-c-biofilmography-english'][0] . '</em>':'' ) ?>						
							</p>
						</div>
				<?php endif; endforeach; ?>
			<?php endif; ?>

			<!-- Titles -->
			<p>
			<span class="subline"><?= sanitize_text_field(single_term_title()) ?></span>
			<span class="float-right float-end">
				<mark class="subline align-text-bottom"><?= $section_edition ?></mark>
			</span>
			</p>
			<!-- Description -->
			<?php if ( strlen(strip_tags($section_description)) > 0 ) : ?> 
				<p class="lead light pt-4 pb-4"><?= WaffTwo\Core\waff_do_markdown(strip_tags($section_description)) ?></p>
			<?php else : ?>
				<?php echo apply_filters('the_content', WaffTwo\Core\waff_do_markdown($section_content)); ?>
			<?php endif; ?>
				
			<!-- Buttons -->
			<p>
			<?php if ( strlen(strip_tags($section_content)) > 0 && strlen(strip_tags($section_description)) > 0 ) : ?> 
			<a class="btn btn-outline-dark rounded-0" data-toggle="collapse" data-bs-toggle="collapse" href="#collapseContent" role="button" aria-expanded="false" aria-controls="collapseContent"><i class="fas fa-ellipsis-h"></i> <?= __('Read more', 'waff') ?></a>
			<?php endif; ?>
			<?php if ( $section_additionnal_content != '' ) : ?> 
			<a href="<?= $section_additionnal_content ?>" class="btn btn-outline-dark rounded-0"><?= __('Download in', 'waff') ?> *.pdf</a>
			<?php endif; ?>
			<a href="<?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?get_site_url().'/festival-programmation':'#'; ?>" class="btn btn-action-1 rounded-0"><?= __('Planning', 'waff') ?></a>
			</p>

			<!-- Section content -->
			<?php if ( strlen(strip_tags($section_content)) > 0 && strlen(strip_tags($section_description)) > 0 ) : ?> 
			<div class="section-content collapse" id="collapseContent">
				<div class="card card-body">
					<?php echo apply_filters('the_content', WaffTwo\Core\waff_do_markdown($section_content)); ?>
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>

	<!-- List -->
	<section id="section" class="mt-md-10 mb-md-10 mt-5 mb-0 contrast--light <?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'no-bg':'bg-bgcolor'; ?> f-w">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">
				<div class="col-md-5 <?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'no-bg':'bg-light'; ?> p-4" data-aos="fade-right">
					<h6 class="headline d-inline">Les films</h6>
				</div>
				<div class="col-md-7"></div>
			</div>
			<div class="row g-0 align-items-center <?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'border-bottom border-top mb-4':'no-border'; ?> ">
				<div class="col-md-5 p-4 <?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'border-end':'no-border'; ?>" data-aos="fade-right">
					<p class="--text-muted text-black position-sticky sticky-top mb-0">
						<!-- <small class="d-block"><strong><?= $wp_query->post_count ?> films</strong></small> -->
						<?php if ( isset($counts['films']) && $counts['films'] != '0' ) 
							print( '<small class="d-block"><strong>' . sprintf( _n( '%s film', '%s films', $counts['films'], 'waff' ), $counts['films'] ) . '</strong></small>'); ?>
						<?php if ( isset($counts['wpcf-p-is-guest']) && $counts['wpcf-p-is-guest'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-guest mr-1 f-12"></i> ' . sprintf( _n( '%s with guest', '%s with guest\'s', $counts['wpcf-p-is-guest'], 'waff' ), $counts['wpcf-p-is-guest'] ) . '</small>'); ?>
						<?php if ( isset($counts['wpcf-p-is-debate']) && $counts['wpcf-p-is-debate'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-mic mr-1 f-12"></i> ' . sprintf( _n( '%s with debate', '%s with debate\'s', $counts['wpcf-p-is-debate'], 'waff' ), $counts['wpcf-p-is-debate'] ) . '</small>'); ?>
						<?php if ( isset($counts['wpcf-p-young-public']) && $counts['wpcf-p-young-public'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-young mr-1 f-12"></i> ' . sprintf( _n( '%s parent-children', '%s parent-children\'s', $counts['wpcf-p-young-public'], 'waff' ), $counts['wpcf-p-young-public'] ) . '</small>'); ?>
						<?php if ( isset($counts['wpcf-p-highlights']) && $counts['wpcf-p-highlights'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-sun mr-1 f-12"></i> ' . sprintf( _n( '%s highlight', '%s highlights', $counts['wpcf-p-highlights'], 'waff' ), $counts['wpcf-p-highlights'] ) . '</small>'); ?>
						<?php if ( isset($counts['wpcf-f-promote']) && $counts['wpcf-f-promote'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-ok mr-1 f-12"></i> ' . sprintf( _n( '%s favorite', '%s favorites', $counts['wpcf-f-promote'], 'waff' ), $counts['wpcf-f-promote'] ) . '</small>'); ?>
						<!-- <small class="d-block">6 compétitons</small>-->
					</p>
				</div>
				<?php if ( $section_filmography_index > 0 ) : ?> 
				<div class="col-md-7 bg-bgcolor-lighten h-100 <?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'':$section_title_color ?>" <?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'':$section_color ?>>
					<div class="row p-4">
						<div class="col-sm-4">
							<p class="<?= (defined('WAFF_THEME') && WAFF_THEME == 'DINARD')?'':$section_title_color ?>">
								<small class="d-block"><?= __('Selective filmography', 'waff') ?></small>
								<small class="d-block"><strong><?= $section_filmography_of ?></strong></small>
							</p>
						</div>
						<div class="col-sm-8">
							<p><?= $filmography ?></p>
						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<div class="row g-0 align-items-center py-2 offset-md-2">
				<!-- FILM CARD -->
					<?php
					global $attributes;
					// Start the Loop.
					while ( have_posts() ) :
						the_post();
						//get_template_part( 'partials/content', '' );
						$promote 	= get_post_meta($post->ID, 'wpcf-f-promote', true);
						$film_color = rwmb_meta( 'waff_film_color', array(), $post->ID );
						$film_color_class = 'contrast--light card-dark';
						if ( isset($promote) && $promote=='1' && isset($film_color) && $film_color != '' ) {
							$rgb = WaffTwo\Core\waff_HTMLToRGB($film_color);
							$hsl = WaffTwo\Core\waff_RGBToHSL($rgb);
							if($hsl->lightness < $lightness_threshold)
								$film_color_class = 'contrast--dark card-light';
						}
					
						// print_r(var_dump($promote));
						// print_r(var_dump($film_color));
						$attributes = array(
							'wrapper' 		=> 'div', // div / li
							'title_wrapper' => (($promote=='1')?'h3':'h5'), // h5 / h6
							// section + projection : div
							// Related-sections : li
							'parent' 		=> 'film', // film / projection
							// section : film
							// Projection in fiche film : projection
							// Related-sections : film
							'class' 		=> 'card film-card flex-row flex-wrap '.(($promote=='1')?'col-md-12 h-520-px':'col-md-6 h-280-px').' bg-light my-2 border-0 shadow-sm '.$film_color_class,
							// section : card film-card flex-row flex-wrap col-md-6 bg-light my-2 border-0 h-280-px shadow-sm card-dark
							// Projection in fiche film : card film-card flex-row flex-wrap col-4 --bg-custom mx-2 my-0 border-0 h-300-px shadow-sm --card-white --p-0
							// Related-sections : card film-card --flex-row flex-wrap bg-light border-0 h-200-px shadow-sm card-dark
							'image_class' => '--w-100 '.(($promote=='1')?'h-520-px':'h-280-px').' fit-image',
							// section : w-100 h-280-px fit-image
							// Projection in fiche film : w-100 h-600-px fit-image
							// Related-sections : w-100 --h-100 h-200-px fit-image
							'image_width' => 'w-60',
							// section : w-60
							// Projection in fiche film : w-50 float-left
							// Related-sections : w-150-px
							'body_width' => 'w-40',
							// section : w-40
							// Projection in fiche film : w-50 h-100
							// Related-sections : w-250-px
							'show_sections' => 'false', // string = false / true
							'show_cats' 	=> 'true', // string = false / true
							'show_excerpt' 	=> 'true', // string = false / true
							'excerpt_length' => '100',
							// section = room : 100
							// Projection in fiche film : 80
							// Related-sections : 60
							'show_rooms' 	=> 'false', // string = false / true
							'items' 		=> '', // string = @film_projection.parent / empty
							// Parent items 
							// Color
							'film_color'	=> (($promote=='1' && $film_color != '')?$film_color:''),
						);
						$subdomain = substr($_SERVER['SERVER_NAME'],0,4);
						$view_id = ( $subdomain == 'dev2.' || $subdomain == 'www.' )?54057:44405;
						if ( defined('WAFF_THEME') && WAFF_THEME == 'DINARD' )
							$view_id = 670;
						echo render_view_template( $view_id, $post ); // ID de la vue Film card / film-card
					endwhile;
					?>					
				<!-- END FILM CARD -->
				</div>
				<?php
				// Previous/next page navigation.
				get_template_part( 'partials/pagination' );
				?>
		</div>
	</section>
	<?php
	// Reset 
	wp_reset_postdata(); 

} else {

	print('<p class="lead"><strong>I’ll be back !</strong> Oui il semblerait que cette section ne contiennent pas encore de films...</p>');
	print('<div class="wp-block-button aligncenter is-style-circular"><a class="wp-block-button__link wp-element-button" href="/ledition">Consulter l\'édition</a></div>');
	
	// Write overaccess for TEAM ($allowed_roles)  
	if( array_intersect($allowed_roles, $user->roles ) &&  $shownotice == true ) { 
	?> 
	<div class="admin-notice" style="background: hsl(275, 100%, 50%)!important;margin-top:2rem;">
	<!-- Notice for allowed roles -->
		<div class="admin-notice-content" style="color: #FFF!important;padding-top:2%;padding-bottom:3%;padding-left:30%;padding-right:30%;">
			<h4 style="text-align: center;font-size: 1.4em;color: #FFF;margin-bottom: 1em;">Hidden content / Contenu caché</h4>
			<p style="text-align: center;">You can see this content only because you are loggued to the website and you are an administrator or editor. Otherwise it will show only if the current edition match selected edition and if push online option is true and if film status is approved or programmed.</p>
			<p style="text-align: center;"><em>Vous pouvez voir ce contenu uniquement parce que vous êtes connecté au site web et que vous êtes administrateur ou éditeur. Sinon, il ne sera affiché que si l'édition actuelle correspond à l'édition sélectionnée et si l'option de mise en ligne est cochée et si le status de film est approuvé ou programmé.</em></p>
			<pre style="border: .15rem solid rgba(255, 255, 255, 0.75);line-height: 1.5;padding: 2rem 2rem;text-align: left;"><strong>A vérifier : </strong><br/>— La section est parent de la catégorie édition<br/>— La section a bien été rattachée à une édition<br/>— La section contient des films<br/>— Les films sont de statut Approuvés ou Programmés</pre>
		</div>
	<!-- End: Notice for allowed roles -->
	</div>
	<?php 
	}


	// If no content, include the "No posts found" template.
	// get_template_part( 'partials/content', 'none' );
}

get_footer();