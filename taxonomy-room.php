<?php
/**
 * File: archive.php (for archives and blog landing).
 *
 * @package Waff
 */

// Debut Relocate 
global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;

// Relocate
$relocate = false;
$shownotice = true;

// Get user loggued in
$user = wp_get_current_user();
$allowed_roles = array('fifam_editor', 'fifam_admin', 'fifam_subscriber', 'administrator');

// Redirect if option current content online 
// Don\'t do any of that if USER is loggued / THEN write it ( below) 
if( $current_edition_films_are_online == false ){
	if ( empty(array_intersect($allowed_roles, $user->roles )) )
		$relocate = true;
} 

if ( $current_edition_films_are_online == true ) 
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
		<p class="d-none d-sm-block" style="text-align: center;">You can see this content only because you are loggued to the website and you are an administrator or editor. Otherwise it will show only if the current edition match selected edition and if push online option is true and if film status is approved or programmed.</p>
		<p class="d-none d-sm-block" style="text-align: center;"><em>Vous pouvez voir ce contenu uniquement parce que vous êtes connecté au site web et que vous êtes administrateur ou éditeur. Sinon, il ne sera affiché que si l'édition actuelle correspond à l'édition sélectionnée et si l'option de mise en ligne est cochée et si le status de film est approuvé ou programmé.</em></p>
		<pre class="d-none d-sm-block" style="border: .15rem solid rgba(255, 255, 255, 0.75);line-height: 1.5;padding: 2rem 2rem;text-align: left;"><strong>Current edition : </strong><?php echo $current_edition_id; ?><br/><strong>Option : films are online : </strong><?php var_dump($current_edition_films_are_online);?><strong>User loggued in : </strong><?php echo $user->roles[0]; ?></pre>
	</div>
<!-- End: Notice for allowed roles -->
</div>
<?php 
}

get_header();

//Go\page_title();

global $room_id, $room_slug, $room_parent_tax, $room_title_color, $room_color, $_room_color;
$counts 						= 0;
$current_room 					= get_queried_object();
$current_room_id 				= get_queried_object_id();
$current_room_slug 				= $current_room->slug;

$room_description 				= term_description($room_id);
$room_content 					= get_term_meta( $room_id, 'wpcf-r-content', true ); 

$room_textcolor						= (( $_room_color != '' )?'style="color:'.$_room_color.'"':'');

global $current_edition, $previous_editions, $current_edition_id, $current_edition_slug, $current_edition_films_are_online;
$edition = $current_edition; // Local 
$edition_start_date 		= get_term_meta($edition->term_id, 'wpcf-e-start-date', True);
$edition_end_date 			= get_term_meta($edition->term_id, 'wpcf-e-end-date', True);
$edition_start_date 		= date('Y-m-d', $edition_start_date);
$edition_end_date 			= date('Y-m-d', $edition_end_date);
$edition_start_date 		= new DateTime($edition_start_date);
$edition_end_date 			= new DateTime($edition_end_date);

setlocale(LC_TIME, 'fr_FR.UTF8');
$current_date = clone $edition_start_date;
$fd = "Y-m-d";
$day_count = $edition_end_date->diff($edition_start_date)->days;

global $attributes;
$attributes = array(
	'wrapper' 		=> 'div', // div / li
	'title_wrapper' => 'h5', // h5 / h6
	// section + projection : div
	// Related-sections : li
	'parent' 		=> 'projection', // film / projection
	// section : film
	// Projection in fiche film : projection
	// Related-sections : film
	'class' 		=> 'card film-card flex-row flex-wrap col-md-5 bg-light my-2 border-0 h-280-px shadow-sm card-dark',
	// section : card film-card flex-row flex-wrap col-md-6 bg-light my-2 border-0 h-280-px shadow-sm card-dark
	// Projection in fiche film : card film-card flex-row flex-wrap col-4 --bg-custom mx-2 my-0 border-0 h-300-px shadow-sm --card-white --p-0
	// Related-sections : card film-card --flex-row flex-wrap bg-light border-0 h-200-px shadow-sm card-dark
	// room + gazette index : card film-card flex-row flex-wrap col-md-5 bg-light my-2 border-0 h-280-px shadow-sm card-dark
	'image_class' => '--w-100 h-280-px fit-image',
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
	'show_sections' => 'true', // string = false / true
	'show_cats' 	=> 'true', // string = false / true
	'show_excerpt' 	=> 'true', // string = false / true
	'excerpt_length' => '100',
	// section : 100
	// Projection in fiche film : 80
	// Related-sections : 60
	// room + gazette index : 100
	'show_rooms' 	=> 'false', // string = false / true
	'items' 		=> '@film_projection.parent', // string = @film_projection.parent / empty
);

if ( have_posts() ) {

	if ( function_exists('get_counts') )
		$counts = get_counts('room', $current_room_id);

	/* Count for all days */
	// 35100 = nombre-de-films-projetes-room
	/*$results = get_view_query_results(35100, $post_in, $current_user, $args = array(
		'datename' => $date, 
		'date' => $current_date->getTimestamp(),
		'wpvroomslug' => $current_room_slug,
		'wpvroomid' => $current_room_id,
		'wpvroomparentid' => $room_parent_tax->term_id,
		'wpvedition' => $current_edition_slug,
	));	*/
	//print_r($results);

	?>
	<!-- Room content -->
	<?php if ( strlen(strip_tags($room_description)) > 0 || strlen(strip_tags($room_content)) ) : ?> 
	<section class="room-content">
		<div class="row">
			<div class="col-sm-2" <?= $room_textcolor ?> data-aos="fade-right">
				<p class="subline text-left opacity-75 mb-0">Édition <?= $current_edition_slug ?></p>
				<?php if ( isset($counts['projections']) && $counts['projections'] != '0' ) {
						print( '<span class="heading-4 mt-0"><strong class="count">' . sprintf( _n( '%s', '%s', $counts['projections'], 'waff' ), $counts['projections'] ) . '</strong></span>');
						print( '<p class="w-50">' . _n( 'projection screened in this room', 'projections screened in this room', $counts['projections'], 'waff' ) . '</p>');
				} ?> 
				<?php /* if ( isset($counts['films']) && $counts['films'] != '0' ) {
						print( '<span class="heading-3 mt-0"><strong class="count">' . sprintf( _n( '%s', '%s', $counts['films'], 'waff' ), $counts['films'] ) . '</strong></span>');
						print( '<p class="w-50">' . _n( 'film screened in this room', 'films screened in this room', $counts['films'], 'waff' ) . '</p>');
				} */ ?> 
			</div>

			<div class="col-sm-2" data-aos="fade-right">
				<p class="--text-muted text-black position-sticky sticky-top --mb-0">
						<!-- <small class="d-block"><strong><?= $wp_query->post_count ?> films</strong></small> -->
						<?php if ( isset($counts['films']) && $counts['films'] != '0' ) 
							print( '<small class="d-block"><strong>' . sprintf( _n( '%s film', '%s films', $counts['films'], 'waff' ), $counts['films'] ) . '</strong></small>'); ?>
						<?php /*if ( isset($counts['projections']) && $counts['projections'] != '0' ) 
							print( '<small class="d-block"><strong>' . sprintf( _n( '%s projection', '%s projections', $counts['projections'], 'waff' ), $counts['projections'] ) . '</strong></small>'); */ ?>
						<?php if ( isset($counts['events']) && $counts['events'] != '0' ) 
							print( '<small class="d-block"><strong>' . sprintf( _n( '%s event', '%s events', $counts['events'], 'waff' ), $counts['events'] ) . '</strong></small>'); ?>
						<?php if ( isset($counts['programs']) && $counts['programs'] != '0' ) 
							print( '<small class="d-block"><strong>' . sprintf( _n( '%s program', '%s programs', $counts['programs'], 'waff' ), $counts['programs'] ) . '</strong></small>'); ?>
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
						<!-- #44 -->
						<?php if ( isset($counts['wpcf-f-premiere']) && $counts['wpcf-f-premiere'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-premiere mr-1 f-12"></i> ' . sprintf( _n( '%s premiere', '%s premieres', $counts['wpcf-f-premiere'], 'waff' ), $counts['wpcf-f-premiere'] ) . '</small>'); ?>
						<?php if ( isset($counts['wpcf-f-avant-premiere']) && $counts['wpcf-f-avant-premiere'] != '0' ) 
							print( '<small class="d-block"><i class="icon icon-avantpremiere mr-1 f-12"></i> ' . sprintf( _n( '%s avant-premiere', '%s avant-premieres', $counts['wpcf-f-avant-premiere'], 'waff' ), $counts['wpcf-f-avant-premiere'] ) . '</small>'); ?>
						<!-- EX: <small class="d-block">6 compétitons</small>-->
				</p>
			</div>

			<div class="col-sm-7" data-aos="fade-left">
				<?php if ( strlen(strip_tags($room_description)) > 0 ) : ?>
				<p class="lead"><?php echo WaffTwo\Core\waff_do_markdown( WaffTwo\Core\waff_clean_alltags($room_description) ); ?></p>
				<?php endif; ?>
				<?php if ( strlen(strip_tags($room_content)) > 0 ) : ?>
				<?php echo apply_filters('the_content', WaffTwo\Core\waff_do_markdown($room_content)); ?>
				<?php endif; ?>
			</div>
		</div>
	</section>




	


	<?php endif; ?>

	<?php
	/* STATS */
	//echo do_shortcode('[wpv-view name="nombre-de-films-projetes-room" wpvroomslug="'.$current_room_slug.'" wpvroomid="'.$current_room_id.'" wpvroomparentid="'.$room_parent_tax->term_id.'" wpvedition="'.$current_edition_slug.'"]');

	/* Loop day by day */
	while ($current_date >= $edition_start_date AND $current_date <= $edition_end_date) :
		$w = $current_date->format('w') - 1;
		if ($w < 0)
			$w = 6;
		$date 				= strftime("%A %d %B %G", strtotime($current_date->format('Y-m-d')));
		$date_day			= ucfirst(strftime("%A", strtotime($current_date->format('Y-m-d'))));
		$date_day_number	= strftime("%d", strtotime($current_date->format('Y-m-d')));

		// Print only if we got results to this view 
		// 25580 = projection-jour-room FIFAM
		// 676 = projection-jour-room DINARD
		$subdomain = substr($_SERVER['SERVER_NAME'],0,4);
		$view_id = ( $subdomain == 'dev2.' || $subdomain == 'www.' )?25580:0;
		if ( defined('WAFF_THEME') && WAFF_THEME == 'DINARD' )
			$view_id = 676;
		$results = get_view_query_results($view_id, $post_in , $current_user, $args = array(
			'datename' => $date, 
			'date' => $current_date->getTimestamp(),
			'wpvroomslug' => $current_room_slug,
			'wpvedition' => $current_edition_slug,
		));

		if ( count($results) > 0 ) :
			if ( function_exists('get_counts') )
			$counts = get_counts('room', $current_room_id, wp_list_pluck( $results, 'ID' ) );
		?>
		<!-- List -->
		<section id="room" class="mt-4 mt-md-10 mb-10 contrast--light bg-bgcolor f-w">
			<div class="container-fluid px-0">
				<div class="row g-0 align-items-center">
					<div class="col-md-3 bg-light p-4" data-aos="fade-right">
						<h6 class="headline d-inline">Les films</h6>
						<h5 class="d-inline float-right mt-0 mb-0" data-bs-toggle="tooltip" data-toggle="tooltip" title="Le <?= $date ?>">
							<?= $date_day ?> <span class="thin"><?= $date_day_number ?></span> <i class="icon icon-down-right-light"></i>
						</h5>
					</div>
					<div class="col-md-7"></div>
					<div class="col-md-2"></div>
				</div>
				<div class="row g-0 align-items-center">
					<div class="col-md-3 p-4" data-aos="fade-right">
						<p class="--text-muted text-black position-sticky sticky-top mb-0">
							<!-- <small class="d-block"><strong><?= count($results) ?> films</strong></small> -->
							<?php if ( isset($counts['films']) && $counts['films'] != '0' ) 
								print( '<small class="d-block"><strong>' . sprintf( _n( '%s film', '%s films', $counts['films'], 'waff' ), $counts['films'] ) . '</strong></small>'); ?>
							<?php if ( isset($counts['projections']) && $counts['projections'] != '0' ) 
								print( '<small class="d-block"><strong>' . sprintf( _n( '%s projection', '%s projections', $counts['projections'], 'waff' ), $counts['projections'] ) . '</strong></small>');  ?>
							<?php if ( isset($counts['events']) && $counts['events'] != '0' ) 
								print( '<small class="d-block"><strong>' . sprintf( _n( '%s event', '%s events', $counts['events'], 'waff' ), $counts['events'] ) . '</strong></small>'); ?>
							<?php if ( isset($counts['programs']) && $counts['programs'] != '0' ) 
								print( '<small class="d-block"><strong>' . sprintf( _n( '%s program', '%s programs', $counts['programs'], 'waff' ), $counts['programs'] ) . '</strong></small>'); ?>
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
							<!-- #44 -->
							<?php if ( isset($counts['wpcf-f-premiere']) && $counts['wpcf-f-premiere'] != '0' ) 
								print( '<small class="d-block"><i class="icon icon-premiere mr-1 f-12"></i> ' . sprintf( _n( '%s premiere', '%s premieres', $counts['wpcf-f-premiere'], 'waff' ), $counts['wpcf-f-premiere'] ) . '</small>'); ?>
							<?php if ( isset($counts['wpcf-f-avant-premiere']) && $counts['wpcf-f-avant-premiere'] != '0' ) 
								print( '<small class="d-block"><i class="icon icon-avantpremiere mr-1 f-12"></i> ' . sprintf( _n( '%s avant-premiere', '%s avant-premieres', $counts['wpcf-f-avant-premiere'], 'waff' ), $counts['wpcf-f-avant-premiere'] ) . '</small>'); ?>
							<!-- EX: <small class="d-block">6 compétitons</small>-->
						</p>
					</div>
					<?php if ( count($counts['guests']) > 0 ) : ?>
					<div class="col-md-7 bg-bgcolor-lighten h-100">
						<div class="row p-4">
							<div class="col-sm-4">
								<p class="mb-0 mb-sm-4"><small><strong>Les invité.e.s du jour</strong></small></p>
							</div>
							<div class="col-sm-8">
								<p>
								<?php
									$i=0;
									$rendered_guests = array();
									foreach($counts['guests'] as $projection) {
										$guest 				= get_post_meta( $projection, 'wpcf-p-e-guest-contact', false );
										$_guest_name 		= get_post_meta( $projection, 'wpcf-p-guest-name', true );
										if ( $_guest_name != '' )
											$rendered_guests[] = array('g_fullname' => $_guest_name);

										// Loop all guest to process datas 
										foreach($guest as $g) {
											$g_picture 		= get_post_meta( $g, 'wpcf-c-picture', true );
											$g_lastname 	= get_post_meta( $g, 'wpcf-c-name', true );
											$g_firstname	= get_post_meta( $g, 'wpcf-c-firstname', true );
											if ( $g_firstname == '' && $g_lastname == '')
												continue;
											$g_surname 		= get_post_meta( $g, 'wpcf-c-surname', true );
											$g_country 		= get_post_meta( $g, 'wpcf-c-country', true );

											$g_organization = get_post_meta( $g, 'wpcf-c-organization', true );
											$g_structure 	= get_post_meta( $g, 'wpcf-c-structure', true );
											$g_position 	= get_post_meta( $g, 'wpcf-c-position', true );

											$g_fullname 	= (($g_surname!='')?$g_surname:'<strong>'.$g_firstname.'</strong> '.$g_lastname.'');
											$g_fullposition = (($g_position!='')?$g_position:$g_organization.' '.$g_structure);
											$g_picture 		= (($g_picture!='')?'<img src="'.$g_picture.'" class="pr-2 align-baseline" alt="'.$g_fullname.'">':'<!-- !¡ No contact c-picture -->');	
											$i++;

											if ( function_exists( 'types_render_field' ) ) {
												$g_picture = types_render_field( 'c-picture', array( 
														'item' => $g, 
														'width' => '28', 'height' => '28', 'proportional' => 'false', 
														'alt' => esc_html($g_fullname), 
														'style' => '', 
														'class' => 'pr-2 --align-baseline align-top img-fluid')
												);
											}

											/*printf('%s<span class="lead impact">%s</span>&nbsp;<span class="subline">%s</span>%s',
												$g_picture,
												$g_fullname,
												$g_fullposition,
												(($i < count($counts['guests']))?' • ':'')
											); > push to array to avoid • before empty guest */

											// $rendered_guests[$i]['g_picture'] 		= $g_picture;
											// $rendered_guests[$i]['g_fullname'] 		= $g_fullname;
											// $rendered_guests[$i]['g_fullposition'] 	= $g_fullposition;
											$rendered_guests[] = array ('g_picture' => $g_picture,  'g_fullname' => $g_fullname, 'g_fullposition' => $g_fullposition);
										}								
									}
									// Final Loop all processed guests to avoid empty guests 
									$i=0;
									foreach($rendered_guests as $g) {
										$i++;
										printf('%s<span class="lead impact">%s</span>&nbsp;<span class="subline">%s</span>%s',
											$g['g_picture'],
											$g['g_fullname'],
											$g['g_fullposition'],
											(($i < count($rendered_guests))?' •  ':'')
										);
									}
								?>
								</p>
							</div>
						</div>
					</div>
					<?php endif; ?> 
					<div class="col-md-2"></div>
				</div>
				<div class="row g-0 align-items-center py-2 --offset-md-2">
					<!-- FILM-CARD CALLED BY A VIEW -->
					<?php
					// Then, print if we found results
					// 25580 = projection-jour-room
					$args = array(
						'id' => $view_id,
						'datename' => $date, 
						'date' => $current_date->getTimestamp(),
						'wpvroomslug' => $current_room_slug,
						'wpvedition' => $current_edition_slug,
					);
					echo render_view( $args );
					//echo do_shortcode('[wpv-view name="projection-jour-room" datename="'.$date.'" date="'.$current_date->getTimestamp().'" wpvroomslug="'.$room_slug.'" wpvedition="'.$current_edition_slug.'"]'); 
					?>
					<!-- FIN FILM CARD -->
				</div>
			</div>
		</section>
		<?php
		endif;
	$current_date->add(new DateInterval('P1D'));
	endwhile;


	// Start the Loop.
	/*while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/content', 'excerpt' );
	endwhile;*/

	// Previous/next page navigation.
	//get_template_part( 'partials/pagination' );

} else {

	// If no content, include the "No posts found" template.
	get_template_part( 'partials/content', 'none' );
}

get_footer();