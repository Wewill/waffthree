<?php 
class WP_Widget_Programmation extends WP_Widget {

	protected $registered = false;

    protected $default_instance = array(
        'title'   	=> '',
        'text'  	=> 'La programmation de l\'édition est en cours de remplissage. Rendez-vous quelques jours avant l\'ouverture de l\'édition pour la découvrir !',
        'classes' 	=> '',
    );  
	function __construct() {
		$widget_ops  = array(
			'classname'                   => 'widget_programmation',
			'description'                 => __( 'Print projections block.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'projections', __( '(WA) Programmation', 'waff' ), $widget_ops, $control_ops );

		add_action( 'wp_ajax_widget_programmation_ajax_html', [$this, 'widget_programmation_ajax_html']);
		add_action( 'wp_ajax_nopriv_widget_programmation_ajax_html', [$this, 'widget_programmation_ajax_html']);
	}
	  
	public function widget_programmation_ajax_html() {

		global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;
			// Rooms
			$args = array(
				'taxonomy' => 'room',
				'posts_per_page' => -1,
				'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
				'hide_empty' => false,
				//'hierarchical' => false,
				'parent' => 0,
				'meta_query' => array(
					array(
						'key' => 'wpcf-r-hide-in-website',
						'compare' => 'NOT EXISTS',
					),
				),
			);
			$the_rooms = get_terms( $args );
			$rooms = array();
			//echo '<pre>',print_r($the_rooms,1),'</pre>';
			if ( !empty($the_rooms) ) {
				foreach($the_rooms as $key => $room) {
					$is_hidden 			= get_term_meta( $room->term_id, 'wpcf-r-hide-in-website', true );
					//$is_parent_hidden 	= get_term_meta( $room->parent, 'wpcf-r-hide-in-website', true );
					if ( (boolean) $is_hidden === true ) //(boolean) $is_parent_hidden === true ||
						continue;

					/*
					echo '<br/> ####';
					echo $room->term_id;
					echo $room->name;
					echo var_dump($is_hidden);
					echo var_dump($is_parent_hidden);
					echo var_dump((boolean)$is_hidden);
					echo var_dump((boolean)$is_parent_hidden);
					echo $room->parent;
					echo var_dump((boolean)( $room->parent != 0));
					*/

					// Store rooms parent level 
					$rooms[$room->term_id] = (array) $room;
			
					// If existing, store room child level
					$roomargs = array(
						'taxonomy' => 'room',
						'posts_per_page' => -1,
						'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
						'hide_empty' => true,
						//'hierarchical' => false,
						'parent' => $room->term_id,
						'meta_query' => array(
							array(
								'key' => 'wpcf-r-hide-in-website',
								'compare' => 'NOT EXISTS',
							),
						),
					);
					$the_room = get_terms( $roomargs );
					if ( !empty($the_room) ) {
						foreach($the_room as $key => $r) {
							$r_is_hidden 			= get_term_meta( $r->term_id, 'wpcf-r-hide-in-website', true );
							if ( (boolean) $r_is_hidden === true ) 
								continue;
	
							$rooms[$r->parent]['room'][$r->term_id] = (array) $r;
							$rooms[$r->parent]['room'][$r->term_id]['projections'] = array();
						}
					} 
				}
			}
			//echo '<pre>',print_r($rooms,1),'</pre>';

			// Days 
			setlocale(LC_TIME, 'fr_FR.UTF8');
			$today = getdate();
			//echo '<pre>',print_r($today,1),'</pre>';
			$edition_start_date_meta = get_term_meta($current_edition->term_id, 'wpcf-e-start-date', True);
			$edition_end_date_meta = get_term_meta($current_edition->term_id, 'wpcf-e-end-date', True);
			$edition_start_date = date('d', $edition_start_date_meta);//Y-m-d
			$edition_end_date = date('d', $edition_end_date_meta);
			$count = 1;
			$the_days = array();
			for ($day = $edition_start_date; $day <= $edition_end_date; $day++) { //$day <= $edition_end_date+1
				$d = (($edition_start_date_meta-82800) + (60 * 60 * (24 * $count-1)));
				$d1 = (($edition_start_date_meta-82800) + (60 * 60 * (24 * $count)));

				$the_days[$d] = array(
					'day' 			=> $d,
					'weekday' 		=> date_i18n('l', $d),
					'day_number' 	=> $day,
					'day_count' 	=> $count,
					'is_active' 	=> (($day >= $edition_end_date)?'unactive':'active'),
					'is_today' 		=> (($today[0] > $d && $today[0] < $d1 )?true:false),
					'rooms'			=> $rooms,
				);
				$count++;
			}
			//echo '<pre>',print_r($the_days,1),'</pre>';
			?>


			<?php
			// Projections
			$args = array(
				'post_type' => 'projection',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				// In edition
				'tax_query' => array(
					'relation' => 'AND',
					array (
						'taxonomy' => 'edition',
						'field' => 'term_id',
						'terms' => array($current_edition_id),
					)
				),
				// Order by 
				'orderby'  => array( 'meta_value_num' => 'ASC', 'menu_order' => 'DESC', 'date' => 'DESC' ),
				'meta_key' => 'wpcf-p-start-and-stop-time__begin',		
			);
			$the_projections = array();
			$projections = new WP_Query( $args ); 
?>

			<?php while ( $projections->have_posts() ) : $projections->the_post(); ?>

			
				<?php 
					$id 					= (( isset($post->ID) )?$post->ID:get_the_ID());
					$title 					= (( isset($post->post_title) )?$post->post_title:get_the_title());
					$p_date 				= types_render_field( 'p-date', array() );
					$p_timestamp 			= types_render_field( 'p-date', array('output'=>'raw') );
					$p_start_and_stop_time 	= types_render_field( 'p-start-and-stop-time', array() );
					$p_start_and_stop_time_raw = get_post_meta( $id, 'wpcf-p-start-and-stop-time', true );
					$p_start_and_stop_time__begin = get_post_meta( $id, 'wpcf-p-start-and-stop-time__begin', true );
					$p_is_guest 			= types_render_field( 'p-is-guest', array() );
					$p_e_guest_contact 		= types_render_field( 'p-e-guest-contact', array() ); // 'item' => $id not needed // Issued since #43
					$p_e_guest_contact_raw  = get_post_meta( $id, 'wpcf-p-e-guest-contact', false ); // Working #43
					$p_guest_name 			= types_render_field( 'p-guest-name', array() );
					$p_is_debate 			= types_render_field( 'p-is-debate', array() ); //#43
					$p_young_public 		= types_render_field( 'p-young-public', array() );
					$p_highlights 			= types_render_field( 'p-highlights', array() );
					$p_translator 			= types_render_field( 'p-translator', array() );
					$p_tag 					= types_render_field( 'p-tag', array() );
					$p_hide_projection_title= types_render_field( 'p-hide-projection-title', array() ); //#43

					// Get terms
					$p_rooms 				= get_the_terms( $id, 'room' );
					$p_tags 				= get_the_terms( $id, 'post_tag' );
					
					// Check if projection has_film
					$relationship 			= 'film';
					$forposttype 			= 'projection';
					$count_connections 		= 0;
					$has_relationship 		= toolset_get_relationship( array( $relationship, $forposttype ) );
					$has_film 				= false;

					if ( $has_relationship ) {
						$parent = $has_relationship['roles']['parent']['types'][0];
						$child = $has_relationship['roles']['child']['types'][0];
						$origin = ( $parent == $forposttype ) ? 'parent' : 'child';
						// Get connected posts
						$connections = toolset_get_related_posts( $id, array($relationship,$forposttype), $origin, 9999, 0, array(), 'post_id', 'other', null, 'ASC', true, $count_connections );
						if ( !empty($connections) )  $has_film = true;
					}

					// Get film meta values
					$f_id						= (( true === $has_film )?$connections[0]:0);
					$f_title 					= (( get_the_title($f_id) && true === $has_film )?get_the_title($f_id):'');
					$f_french_operating_title 	= get_post_meta( $f_id, 'wpcf-f-french-operating-title', true );
					$f_movie_length 			= get_post_meta( $f_id, 'wpcf-f-movie-length', true );
					$f_author 					= get_post_meta( $f_id, 'wpcf-f-author', true ); //#43
					$f_production_year 			= get_post_meta( $f_id, 'wpcf-f-production-year', true ); //#43
					$f_available_formats 		= get_post_meta( $f_id, 'wpcf-f-available-formats', true ); //#43

					$f_country_ 				= get_post_meta( $f_id, 'wpcf-f-country', true ); //#43
					$f_co_production_country_ 	= get_post_meta( $f_id, 'wpcf-f-co-production-country', true ); //#43
					$f_country 					= types_render_field( 'f-country', array('item' => $f_id) ); //#43
					$f_co_production_country 	= types_render_field( 'f-co-production-country', array('item' => $f_id) ); //#43

					$f_premiere_ 				= get_post_meta( $f_id, 'wpcf-f-premiere', true ); //#43
					$f_catalog_tag_ 			= get_post_meta( $f_id, 'wpcf-f-catalog-tag', true ); //#43
					$f_premiere 				= types_render_field( 'f-premiere', array('item' => $f_id) ); //#43
					$f_catalog_tag 				= types_render_field( 'f-catalog-tag', array('item' => $f_id) ); //#43

					$f_poster 						= get_post_meta( $f_id, 'wpcf-f-film-poster', true ); //#43
					$f_poster_id 					= WaffTwo\Core\waff_get_image_id_by_url($f_poster);
					$f_poster_url 					= wp_get_attachment_image_url( $f_poster_id, "film-poster" ); // OK
					$f_poster_img 					= wp_get_attachment_image( $f_poster_id, "film-poster", "", array( "class" => "img-responsive" ) ); // OK

					$f_featured_img 				= get_the_post_thumbnail( $f_id, 'film-poster');
					$f_poster_img					= ( $f_poster != '') ? $f_poster_img : $f_featured_img;

					// Not working 
					//echo $f_poster 				= types_render_field( 'f-film-poster', array( 'output' => 'raw', 'item' => $f_id, 'size' => 'thumbnail', 'alt' => esc_html($f_title), 'style' => 'height: 150px; object-fit: cover;', 'class' => 'img-fluid' ) ); //#43
					//echo $f_poster 				= do_shortcode( "[wpv-post-featured-image item='".$f_poster."' size='custom' width='50' height='50' crop='true' output='url']" );

					// Get terms
					$f_sections 				= get_the_terms( $f_id, 'section' );
					//echo '<pre>',print_r(array($id,$title,$p_date,$p_timestamp,$p_start_and_stop_time,$p_start_and_stop_time_raw,$p_start_and_stop_time__begin,$p_rooms,$p_is_guest,$p_e_guest_contact,$p_e_guest_contact_raw,$p_young_public,$p_highlights,$p_translator,$p_tag,$has_film,$count_connections,$connections,$f_id,$f_title,$f_french_operating_title,$f_movie_length,$f_sections),1),'</pre>';

					$the_projections[$id] = array(
						'p_id' 								=> $id,
						'p_title' 							=> $title, //1 
						'p_date' 							=> $p_date,
						'p_timestamp' 						=> $p_timestamp,
						'p_start_and_stop_time' 			=> $p_start_and_stop_time,
						'p_start_and_stop_time_raw' 		=> $p_start_and_stop_time_raw,
						'p_start_and_stop_time__begin' 		=> $p_start_and_stop_time__begin,
						'p_rooms' 							=> $p_rooms,
						'p_tags' 							=> $p_tags, //43
						'p_is_guest' 						=> $p_is_guest,
						'p_e_guest_contact' 				=> $p_e_guest_contact, //9
						'p_e_guest_contact_raw' 			=> $p_e_guest_contact_raw,
						'p_guest_name' 						=> $p_guest_name, //#43
						'p_is_debate' 						=> $p_is_debate, //#43
						'p_young_public' 					=> $p_young_public,
						'p_highlights' 						=> $p_highlights,
						'p_translator' 						=> $p_translator,
						'p_tag' 							=> $p_tag,
						'p_hide_projection_title' 			=> $p_hide_projection_title, //#43
						'p_has_film' 						=> $has_film,
						'p_count_connections' 				=> $count_connections,
						'p_connections' 					=> $connections, // 0
						'f_id' 								=> $f_id,
						'f_title' 							=> $f_title,
						'f_french_operating_title' 			=> $f_french_operating_title,
						'f_movie_length' 					=> $f_movie_length,
						'f_sections' 						=> $f_sections,
						'f_country' 						=> $f_country, //#43
						'f_co_production_country' 			=> $f_co_production_country, //#43
						'f_author' 							=> $f_author, //#43
						'f_production_year' 				=> $f_production_year, //#43
						'f_poster' 							=> $f_poster, //#43
						'f_poster_img' 						=> $f_poster_img, //#43
						'f_premiere' 						=> $f_premiere, //#43
						'f_catalog_tag' 					=> $f_catalog_tag, //#43
						'f_premiere_' 						=> $f_premiere_, //#43
						'f_catalog_tag_' 					=> $f_catalog_tag_, //#43
						'f_available_formats' 				=> $f_available_formats, //#43
					);

					// Final Loop
					foreach($the_days as $key => $the_day) {
						if ( $key == $p_timestamp ) {

							$the_days[$p_timestamp]['has_films'] = true;

							foreach($p_rooms as $p_room) {
								$the_days[$p_timestamp]['rooms'][$p_room->parent]['has_films'] = true;
								$the_days[$p_timestamp]['rooms'][$p_room->parent]['room'][$p_room->term_id]['has_films'] = true;
								$the_days[$p_timestamp]['rooms'][$p_room->parent]['room'][$p_room->term_id]['projections'][] = $the_projections[$id]; 
							}
						}
					}
				?>


				<?php endwhile; wp_reset_postdata();
				//echo '<pre>',print_r($the_projections,1),'</pre>';
				//echo '<pre>',print_r($the_days,1),'</pre>';

				// Render HTML 
				print('<!--BEGIN: Modal AJAX-->');
				
				foreach($the_days as $key => $the_day) {

					//echo '<pre>',print_r($the_day,1),'</pre>';

					//Days 
					if ( $the_day['has_films'] === true ) {

						printf('<!-- Day -->
						<div class="row g-0 --mb-4">
							<div class="col-md-2 col-day bg-color-dark px-2 px-sm-6 --py-4 pt-3 pb-3 w-sm-100">
								<a class="scrollspy text-white" id="day%d" data-count="%d" data-ts="%d">
									<span class="subline h4 --subline-4">%s</span>
									<span class="display-2 d-block mt-2">%s</span>
								</a>
							</div>',
							esc_html($the_day['day_number']),
							esc_html($the_day['day_count']),
							esc_html($the_day['day']),
							esc_html($the_day['weekday']),
							esc_html($the_day['day_number'])
						);

						print('<!-- Rooms -->
							<div class="col-md-10 p-0">');

						// Rooms
						foreach($the_day['rooms'] as $key => $the_day_rooms) {
							if ( array_key_exists('has_films', $the_day_rooms) && $the_day_rooms['has_films'] === true ) {
							
								// Room 
								foreach($the_day_rooms['room'] as $key => $the_day_room) {

									//echo '<pre>'.print_r($the_day_room,1).'</pre>';

									if ( array_key_exists('has_films', $the_day_rooms) && $the_day_room['has_films'] === true ) {

										$r_use_parent_room_title = get_term_meta( $the_day_room['term_id'], 'wpcf-r-use-parent-room-title', true );
										$r_use_parent_room_title = ($r_use_parent_room_title == '1')?true:false;
										
										printf('<!-- Room  -->
										<div class="d-flex flex-column flex-lg-row w-100">
											<div class="col-md-3 col-room bg-color-gray px-6 py-4 pt-4 pb-2">
												<div class="room-list">
													<a href="%s" class="room-item">%s</a>
													<a href="%s" class="parentroom-item %s">%s</a>
												</div>
											</div>',
											(($r_use_parent_room_title === false)?(($the_day_room['slug'])?get_term_link($the_day_room['slug'], 'room'):'ERROR'):(($the_day_rooms['slug'])?get_term_link($the_day_rooms['slug'], 'room'):'ERROR')),
											(($r_use_parent_room_title === false)?(($the_day_room['name'])?esc_html($the_day_room['name']):'ERROR'):(($the_day_rooms['name'])?esc_html($the_day_rooms['name']):'ERROR')),
											(($the_day_rooms['slug'])?get_term_link($the_day_rooms['slug'], 'room'):'ERROR'),
											(($r_use_parent_room_title === false)?'':'d-none'),
											(($the_day_rooms['name'])?esc_html($the_day_rooms['name']):'ERROR')
										);

										print('<!-- Film --><div class="col-md-9 col-films bg-light px-6 py-4 pt-4 pb-2 text-black text-dark color-dark">
													<dl class="row">');
										// Film 
										foreach($the_day_room['projections'] as $key => $the_day_room_projections) {
											//if ( $the_day_room_projections['has_films'] === true ) {
												
												// Tags
												$html_p_tags = '';
												if (array_key_exists('p_tags', $the_day_room_projections) && is_array($the_day_room_projections['p_tags'])) foreach($the_day_room_projections['p_tags'] as $p_tag) {
													$html_p_tags .= sprintf('<a href="%s" class="category-item">%s</a>',
													get_term_link($p_tag->slug, 'post_tag'),
													$p_tag->name
													);
												}

												// Sections
												$html_f_section = '';
												$last_f_section_color = '';
												if (array_key_exists('f_sections', $the_day_room_projections) && is_array($the_day_room_projections['f_sections'])) foreach($the_day_room_projections['f_sections'] as $f_section) {
													$f_section_color = get_term_meta( $f_section->term_id, 'wpcf-s-color', true );
													if ( $f_section_color != '' ) $last_f_section_color = $f_section_color;
													$f_section_edition = get_term_meta( $f_section->term_id, 'wpcf-select-edition', true );
													if ( $current_edition_id == $f_section_edition ) // Only current edition sections
													$html_f_section .= sprintf('<a href="%s" %s class="dot-section" data-bs-toggle="tooltip" data-bs-container=".modal" data-bs-title="%s" data-bs-original-title="" title="">•</a>',
													get_term_link($f_section->slug, 'section'),
													(( $f_section_color != '' )?'style="color: '.$f_section_color.';"':''),
													$f_section->name
													);
												}

												// Print tags
												$html_f_tags = '';
												// var_dump( $the_day_room_projections['p_young_public'] );

												// Contact list for guests 
												$contact_list = '';
												if ( $the_day_room_projections['p_guest_name'] != '')
													$contact_list = '<strong>' . $the_day_room_projections['p_guest_name'] . '</strong> › ';

												if ( !empty($the_day_room_projections['p_e_guest_contact_raw']) && $the_day_room_projections['p_e_guest_contact_raw'][0] != '' )
													foreach($the_day_room_projections['p_e_guest_contact_raw'] as $key => $c_id) {
														$c_firstname = get_post_meta( $c_id, 'wpcf-c-firstname', true );
														$c_lastname = get_post_meta( $c_id, 'wpcf-c-name', true );
														$c_surname = get_post_meta( $c_id, 'wpcf-c-surname', true );
														$contact_list .= (($c_surname!='')?$c_surname:$c_firstname . ' <strong>' . $c_lastname . '</strong>') . (($key != count($the_day_room_projections['p_e_guest_contact_raw'])-1 )?', ':'');
													}
												
												// Icons 
												if ( $the_day_room_projections['p_young_public'] != '' ) 	$html_f_tags .= ' <i class="icon icon-young" data-bs-toggle="tooltip" data-bs-container=".modal-body" title="Parents-enfants"></i>'; 
												if ( $the_day_room_projections['p_is_guest'] != '' ) 		$html_f_tags .= ' <i class="icon icon-guest" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="<em>En présence de</em> ・ '.esc_html( $contact_list ).'"></i>'; 
												if ( $the_day_room_projections['p_is_debate'] != '' ) 		$html_f_tags .= ' <i class="icon icon-mic" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="Séance avec débat"></i>'; 
												if ( $the_day_room_projections['p_highlights'] != '' )		$html_f_tags .= ' <i class="icon icon-sun" data-bs-toggle="tooltip" data-bs-container=".modal-body" title="Temps-fort"></i>'; 
												if ( $the_day_room_projections['f_premiere_'] != '' ) 		$html_f_tags .= ' <i class="icon icon-premiere" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="Première '.$the_day_room_projections['f_premiere'].'"></i>'; 
												if ( $the_day_room_projections['f_catalog_tag_'] == 7 )		$html_f_tags .= ' <i class="icon icon-avantpremiere" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="'.$the_day_room_projections['f_catalog_tag'].'"></i>'; 
												
												// Formats 
												//print_r($the_day_room_projections['f_available_formats']);
												if ( !empty($the_day_room_projections['f_available_formats']) ) {
													//$format = $the_day_room_projections['f_available_formats'][0];
													$format = $the_day_room_projections['f_available_formats'];

													if ($format['vost'] == 'en' && $format['vost'] != '' && $format['vost'] != 'N/A') {
														$html_f_tags .= ' <i class="icon icon-vosta text-action-3" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="VOSTA"></i>'; 
													}

													if ($format['vo'] != '' && $format['vo'] != 'N/A') {
														$html_f_tags .= ' <i class="icon icon-vo text-action-3" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="VO"></i> <span class="article text-action-3" style="line-height:1;">' . strtoupper($format['vo']) . '</span>'; 
													}
												}

												// Projection tag
												if ( $the_day_room_projections['p_tag'] != '' ) 			$html_f_tags .= ' <i class="icon icon-warning text-danger" data-bs-toggle="tooltip" data-bs-html="true" data-bs-container=".modal-body" title="'.$the_day_room_projections['p_tag'].'"></i>'; 

												// Get films to create programs 
												// > see waff_functions
												$has_program = false;
												if ( function_exists('func_get_programs') && $the_day_room_projections['p_id'] ) {
													$program = '';
													$program_length = 0;
													$films = func_get_programs(array('output' => 'array'), '', '', $the_day_room_projections['p_id']);
													if ( !empty($films) ) {
														$has_program = true;
														foreach( $films as $k => $p_f_id ) {
															$p_f_title 						= (( get_the_title($p_f_id) )?get_the_title($p_f_id):'');
															$p_f_french_operating_title 	= get_post_meta( $p_f_id, 'wpcf-f-french-operating-title', true );
															$p_f_movie_length 				= get_post_meta( $p_f_id, 'wpcf-f-movie-length', true );
															$p_f_author 					= get_post_meta( $p_f_id, 'wpcf-f-author', true ); //#43
															$p_f_production_year 			= get_post_meta( $p_f_id, 'wpcf-f-production-year', true ); //#43
															$p_f_available_formats 			= get_post_meta( $p_f_id, 'wpcf-f-available-formats', true ); //#43
										
															$p_f_country_ 					= get_post_meta( $p_f_id, 'wpcf-f-country', true ); //#43
															$p_f_co_production_country_ 	= get_post_meta( $p_f_id, 'wpcf-f-co-production-country', true ); //#43
															$p_f_country 					= types_render_field( 'f-country', array('item' => $p_f_id) ); //#43
															$p_f_co_production_country 		= types_render_field( 'f-co-production-country', array('item' => $p_f_id) ); //#43
										
															$p_f_premiere_ 					= get_post_meta( $p_f_id, 'wpcf-f-premiere', true ); //#43
															$p_f_catalog_tag_ 				= get_post_meta( $p_f_id, 'wpcf-f-catalog-tag', true ); //#43
															$p_f_premiere 					= types_render_field( 'f-premiere', array('item' => $p_f_id) ); //#43
															$p_f_catalog_tag 				= types_render_field( 'f-catalog-tag', array('item' => $p_f_id) ); //#43
										
															$p_f_poster 					= get_post_meta( $p_f_id, 'wpcf-f-film-poster', true ); //#43
															$p_f_poster_id 					= WaffTwo\Core\waff_get_image_id_by_url($p_f_poster);
															$p_f_poster_url 				= wp_get_attachment_image_url( $p_f_poster_id, "film-poster" ); // OK
															$p_f_poster_img 				= wp_get_attachment_image( $p_f_poster_id, "film-poster", "", array( "class" => "img-responsive" ) ); // OK
										
															$p_f_featured_img 				= get_the_post_thumbnail( $p_f_id, 'film-poster');
															$p_f_poster_img					= ( $p_f_poster != '') ? $p_f_poster_img : $p_f_featured_img;
										
															// Get terms
															$p_f_sections 				= get_the_terms( $p_f_id, 'section' );
															$html_p_f_section = '';
															$last_p_f_section_color = '';
															if (is_array($p_f_sections)) foreach($p_f_sections as $p_f_section) {
																$p_f_section_color = get_term_meta( $p_f_section->term_id, 'wpcf-s-color', true );
																if ( $p_f_section_color != '' ) $last_p_f_section_color = $p_f_section_color;
																$p_f_section_edition = get_term_meta( $p_f_section->term_id, 'wpcf-select-edition', true );
																if ( $current_edition_id == $p_f_section_edition ) // Only current edition sections
																$html_p_f_section .= sprintf('<a href="%s" %s class="dot-section" data-bs-toggle="tooltip" data-bs-container=".modal" data-bs-title="%s" data-bs-original-title="" title="">•</a>',
																get_term_link($p_f_section->slug, 'section'),
																(( $p_f_section_color != '' )?'style="color: '.$p_f_section_color.';"':''),
																$p_f_section->name
																);
															}																		
															$program .= sprintf('
																	<span class="last_f_section_color" %s>
																		<a href="%s" class="title %s">%s</a>
																		%s
																	</span>
																	%s
																	%s
																	%s
																	%s
																	%s
																	<!-- Program sep-->
																	%s',
															(( $last_p_f_section_color != '' )?'style="color: '.$last_p_f_section_color.';"':''),
															(( $p_f_title != '' )?get_permalink( $p_f_id ):get_permalink( $p_f_id )),
															(( $p_f_title != '' )?'text-link btn-link disabled':'text-link'),
															esc_html(( $p_f_french_operating_title != '' )?$p_f_french_operating_title.' ('.$p_f_title.')':$p_f_title),
															(( $p_f_author != '' )?'&nbsp;<span class="article">DE</span>&nbsp;<span class="director">'.$p_f_author['lastname'].' '.$p_f_author['firstname'].'</span>':''),
															(( $p_f_country != '' )?'・ <span class="country">'.$p_f_country.'</span>':''),
															(( $p_f_co_production_country != '' )?'・ <span class="co_production_country">'.$p_f_co_production_country.'</span>':''),
															(( $p_f_production_year != '' )?'・ <span class="year muted">'.$p_f_production_year.'</span>':''),
															(( $p_f_movie_length != '' )?'・ <span class="length">'.$p_f_movie_length.'\'</span>':''),
															$html_p_f_section,
															// Program sep 
															( $k < (count($films)) )?' <span class="sep display h5 bold op-5">+</span> ':'',
															);
															$program_length +=  (int) $p_f_movie_length;
														} // End foreach
													} // End if films 
												} // End if func
												//print_r($films);
												
												/*echo '<code>';
												echo '#p_id=' . $the_day_room_projections['p_id'];
												echo '#f_id=' . $the_day_room_projections['f_id'];
												echo '#f_french_operating_title=' . $the_day_room_projections['f_french_operating_title'];
												echo '#f_title=' . $the_day_room_projections['f_title'];
												echo '#p_title=' . $the_day_room_projections['p_title'];
												echo '#p_count_connections=' . $the_day_room_projections['p_count_connections'];
												echo '#p_has_film=' . $the_day_room_projections['p_has_film'];
												echo '#has_program=' . var_dump($has_program);
												echo '</code>';*/
												//print_r($the_day_room_projections);

												$_f_title = esc_html( ( $the_day_room_projections['f_french_operating_title'] != '' )?$the_day_room_projections['f_french_operating_title'].' ('.$the_day_room_projections['f_title'].')':$the_day_room_projections['f_title']);
												$_p_title = esc_html( ( $the_day_room_projections['p_hide_projection_title'] != 1 )?$the_day_room_projections['p_title']:'' );
												//echo '#_f_title=' . $_f_title;
												//echo '#_p_title=' . $_p_title;

												// Print film
												printf('
												<dd class="col-10 mb-3" data-p-id="%d">
													<p class="length"><span class="">%s</span> <span class="normal op-5"> › %s</span></p>
													<p>
														<span class="last_f_section_color" %s>
															<a href="%s" class="title %s">%s</a>
															%s
														</span>
														%s
														%s
														%s
														%s
														<!-- Program -->
														%s
														%s
														<!-- Section & tag -->
														%s
														%s
														<!-- Post tag -->
														<div class="category-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>
													</p>
												</dd>
												<dt class="%s" data-p-id="%d"><a href="%s">%s</a></dt>
												<hr class="--bg-light op-1 %s"/>',
												esc_attr( $the_day_room_projections['p_id'] ),
												esc_html( $the_day_room_projections['p_start_and_stop_time_raw']['begin'] ),
												esc_html( $the_day_room_projections['p_start_and_stop_time_raw']['end'] ),
												(( $last_f_section_color != '' )?'style="color: '.$last_f_section_color.';"':''),
												(( $the_day_room_projections['f_id'] != 0 || $the_day_room_projections['p_count_connections'] == 0 || $the_day_room_projections['p_has_film'] == '' )?get_permalink( $the_day_room_projections['p_id'] ):get_permalink( $the_day_room_projections['f_id'] )),
												(( $the_day_room_projections['f_id'] != 0 || $the_day_room_projections['p_count_connections'] == 0 || $the_day_room_projections['p_has_film'] == '' )?'text-link btn-link disabled':'text-link'),
												// Film title or projection title
												(( $has_program === true )?'<i class="icon icon-play me-2 f-12"></i>':'') .
												(( $the_day_room_projections['f_id'] != 0  )?$_f_title:$_p_title.( ( $has_program === false )?'<i class="icon icon-down-right-light me-2 ms-2"></i>':'' ) ),
												//
												(( $the_day_room_projections['f_author'] != '' )?'&nbsp;<span class="article">DE</span>&nbsp;<span class="director">'.$the_day_room_projections['f_author']['lastname'].' '.$the_day_room_projections['f_author']['firstname'].'</span>':''),
												(( $the_day_room_projections['f_country'] != '' )?'・ <span class="country">'.$the_day_room_projections['f_country'].'</span>':''),
												(( $the_day_room_projections['f_co_production_country'] != '' )?'・ <span class="co_production_country">'.$the_day_room_projections['f_co_production_country'].'</span>':''),
												(( $the_day_room_projections['f_production_year'] != '' )?'・ <span class="year muted">'.$the_day_room_projections['f_production_year'].'</span>':''),
												(( $the_day_room_projections['f_movie_length'] != '' )?'・ <span class="length">'.$the_day_room_projections['f_movie_length'].'\'</span>':''),
												//
												(( $has_program === true )?(( $the_day_room_projections['p_hide_projection_title'] != 1 )?' — ':'').'<span class="program">'.$program.'</span>':''),
												(( $has_program === true && $program_length != 0 )?'・ <span class="length bold">( '.$program_length.'\' )</span>':''),
												//
												$html_f_section,
												$html_f_tags,
												//
												esc_html__( 'Taggued', 'waff' ),
												$html_p_tags,
												//
												($the_day_room_projections['f_poster_img'] == '')?'d-none':'col-2 mb-2',
												esc_attr( $the_day_room_projections['p_id'] ),
												get_permalink( $the_day_room_projections['f_id'] ),
												$the_day_room_projections['f_poster_img'],
												($key == count($the_day_room['projections'])-1)?'d-none':''
												);
												

											//}
										}

										print('</dl>
												</div><!-- END: Film -->');

										print('</div><!-- END: Room -->');

									}
								}
							}
						}

						print('</div><!-- END: Rooms --> ');
						print('</div><!-- END: Day-->');

					}
				}
				print('<!-- End: Modal AJAX -->');
		?>
												
		<?php 
		wp_die();
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title 					= apply_filters( 'widget_title', $instance['title'] );
		$text                  	= ! empty( $instance['text'] ) ? $instance['text'] : '';
		  
        // Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_programmation '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// before and after widget arguments are defined by themes
		// echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		  
		// This is where you run the code and display the output
		global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;

		// Get user loggued in
		$user = wp_get_current_user();
		$allowed_roles = array('fifam_editor', 'fifam_admin', 'fifam_subscriber', 'administrator');

		// Do not load widget modal if films are offline excepted for ADMINS
		if ( $current_edition_films_are_online || !empty(array_intersect($allowed_roles, $user->roles )) ) :	

		// Days 
		setlocale(LC_TIME, 'fr_FR.UTF8');
		$today = getdate();
		//echo '<pre>',print_r($today,1),'</pre>';
		$edition_start_date_meta = get_term_meta($current_edition->term_id, 'wpcf-e-start-date', True);
		$edition_end_date_meta = get_term_meta($current_edition->term_id, 'wpcf-e-end-date', True);
		$edition_start_date = date('d', $edition_start_date_meta);//Y-m-d
		$edition_end_date = date('d', $edition_end_date_meta);
		$count = 1;
		$the_days = array();
		for ($day = $edition_start_date; $day <= $edition_end_date; $day++) { //$day <= $edition_end_date+1
			$d = (($edition_start_date_meta-82800) + (60 * 60 * (24 * $count-1)));
			$d1 = (($edition_start_date_meta-82800) + (60 * 60 * (24 * $count)));

			$the_days[$d] = array(
				'day' 			=> $d,
				'weekday' 		=> date_i18n('l', $d),
				'day_number' 	=> $day,
				'day_count' 	=> $count,
				'is_active' 	=> (($day >= $edition_end_date)?'unactive':'active'),
				'is_today' 		=> (($today[0] > $d && $today[0] < $d1 )?true:false),
				'rooms'			=> $rooms,
			);
			$count++;
		}
		//echo '<pre>',print_r($the_days,1),'</pre>';
		?>

		<!-- Programmation Widget  -->
		<div class="modal fade widget_programmation current_edition_films_are_online <?= $instance['classes'] ?>" id="programmationModal" tabindex="-1" role="dialog" aria-labelledby="programmationModalLabel" aria-hidden="true">
			<div class="modal-dialog m-0 max-w-100 ml-auto" role="document">
				<div class="modal-content bg-transparent border-0 rounded-0 color-light text-white" style="overflow: hidden;height: 100vh; position: relative;">
					<div class="modal-header sticky-top container-fluid">
						<div class="row g-0 align-items-center">
							<div class="col-md-2 --col-md-5 --col-lg-7 d-none d-md-block"> <!-- ICI Deplacer -->
								<a class="close-icon color-light lead ml-5" data-bs-dismiss="modal" aria-label="Close">
									<svg width="2.5em" height="2.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
									<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
									</svg>
								</a>
							</div>
							<div class="col-md-10 --col-md-7 --col-lg-5 p-0 col-days order-1">
								<div class="bg-action-1 text-center text-white link-light d-none d-xl-block">
									<div class="p-2"><a class="prog-title headline" data-bs-dismiss="modal" aria-label="Close" id="programmationModalLabel"><?= esc_html__( 'Programmation', 'waff' ) ?></a></div>
								</div>
								<div class="d-flex justify-content-between align-items-stretch align-self-stretch days nav" id="navProgrammationModal" role="tablist">
									<?php foreach($the_days as $the_day) {
										printf('<a data-scroll="day%s" href="#day%s" class="flex-fill display text-center bg-white color-dark day nav-item nav-link active">%s<i class="icon icon-down"></i></a>',
										$the_day['day_number'],
										$the_day['day_number'],
										$the_day['day_number']
										);
									} ?>
								</div>	
							</div>
						</div>
					</div>
					<!-- END: Modal header-->

					<div class="modal-body container-fluid" style="position: relative;overflow-x: hidden;overflow-y: scroll;height: 100%;" id="programmationModalToScroll">
						<div id="programmationModalAjax">
							<div class="d-flex flex-center vh-70">
								<div class="d-flex flex-column align-items-center">
									<div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status">
										<span class="visually-hidden">Loading...</span>
									</div>
									<div class="subline-4 text-center d-block mt-4"><?= esc_html__( 'Loading...', 'waff' ) ?></div>
								</div>
							</div>
						</div>
					</div>
					<!-- End: Modal body -->

					<div class="modal-footer container-fluid d-none d-xl-flex">
					<div class="ml-4 flex-grow-1"><span class="headline"><?= esc_html__( 'Programmation', 'waff' ) ?></span></div>
						<?php if (get_theme_mod( 'planning_url')) : ?><a href="<?php WaffTwo\display_planning_url(); ?>" class="btn btn-link text-decoration-none link-light color-light mx-4 d-flex align-items-center"><i class="icon icon-time f-24 lh-48"></i> <span class="subline --d-block ml-2 mb-1"><?= esc_html__( 'Planner', 'waff' ) ?> *.pdf</span></a><?php endif; ?>
						<?php if (get_theme_mod( 'booklet_url')) : ?><a href="<?php WaffTwo\display_booklet_url(); ?>" class="btn btn-link text-decoration-none link-light color-light mx-4 d-flex align-items-center"><i class="icon icon-brochure f-24 lh-48"></i> <span class="subline --d-block ml-2 mb-1"><?= esc_html__( 'Booklet', 'waff' ) ?> *.pdf</span></a><?php endif; ?>
						<?php if (get_theme_mod( 'catalog_url')) : ?><a href="<?php WaffTwo\display_catalog_url(); ?>" class="btn btn-link text-decoration-none link-light color-light mx-4 d-flex align-items-center"><i class="icon icon-catalogue f-24 lh-48"></i> <span class="subline --d-block ml-2 mb-1"><?= esc_html__( 'Book', 'waff' ) ?></span></a><?php endif; ?>
						<button type="button" class="btn btn-outline-light mr-4" data-bs-dismiss="modal"><?= esc_html__( 'Close', 'waff' ) ?></button>
					</div>
					<!-- End: Modal body -->
				</div>
			<!-- End: Modal content -->
			</div>
			<!-- End: Modal dialog -->
		</div>
		<!-- END: Modal --> 
		
		<!-- Mobile -->
		<!-- .programmation-button Fixed programmation button for mobile -->
		<div class="fixed-bottom d-xl-none d-flex zi-max" style="left: auto;">
			<div class="bg-color-gray text-center text-white link-light d-inline-block when-modal-is-open">
				<?php if (get_theme_mod( 'planning_url')) : ?><div class="prog-title p-2 px-2 headline light border-0 d-inline-block"><a href="<?php WaffTwo\display_planning_url(); ?>" class="link"><i class="icon icon-time pr-0 f-12"></i> <?= esc_html__( 'Planning', 'waff' ) ?></a></div><?php endif; ?>
				<?php if (get_theme_mod( 'booklet_url')) : ?><div class="prog-title p-2 px-2 headline light border-0 d-inline-block"><a href="<?php WaffTwo\display_booklet_url(); ?>" class="link"><i class="icon icon-brochure pr-0 f-12"></i> <?= esc_html__( 'Booklet', 'waff' ) ?></a></div><?php endif; ?>
				<?php if (get_theme_mod( 'catalog_url')) : ?><div class="prog-title p-2 px-2 headline light border-0 d-inline-block"><a href="<?php WaffTwo\display_catalog_url(); ?>" class="link"><i class="icon icon-catalogue pr-0 f-12"></i> <?= esc_html__( 'Book', 'waff' ) ?></a></div><?php endif; ?>
			</div>
			<div class="bg-action-1 text-center text-white link-light d-inline-block">
				<div class="prog-title p-2 px-3 headline border-0"><a class="link toggle-programmation" data-bs-toggle="modal" data-bs-target="#programmationModal" aria-expanded="false" aria-controls="programmationModal"><i class="fas fa-bolt px-1 d-none"></i> <?= esc_html__( 'Programmation', 'waff' ) ?></a></div>
			</div>	
		</div>
		<!-- END: .programmation-button  -->

		<?php else : ?>

		<div class="modal fade widget_programmation current_edition_films_are_offline <?= $instance['classes'] ?>" id="programmationModal" tabindex="-1" role="dialog" aria-labelledby="programmationModalLabel" aria-hidden="true">
			<div class="modal-dialog m-0 max-w-100 ml-auto" role="document">
					<div class="modal-content bg-transparent border-0 rounded-0 color-light text-white" style="overflow: hidden;height: 100vh; position: relative;">
						<div class="modal-header sticky-top container-fluid">
							<div class="row g-0 align-items-center">
								<div class="col-md-5 col-lg-7 d-none d-md-block">
									<a class="close-icon color-light lead ml-5" data-bs-dismiss="modal" aria-label="Close">
										<svg width="2.5em" height="2.5em" viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
										</svg>
									</a>
								</div>
								<div class="col-md-7 col-lg-5 p-0 col-days order-1">
									<div class="bg-action-1 text-center text-white link-light d-none d-xl-block">
										<div class="p-2"><a class="prog-title headline" data-bs-dismiss="modal" aria-label="Close" id="programmationModalLabel"><?= esc_html__( 'Programmation', 'waff' ) ?></a></div>
									</div>	
								</div>
							</div>
						</div>
						<div class="modal-body d-flex align-items-center justify-content-center" style="position: relative;overflow-x: hidden;overflow-y: scroll;height: 100%;" id="">
							<p><?= $text ?></p>
						</div>
						<div class="modal-footer">
							<div class="ml-4 flex-grow-1"><span class="headline"><?= esc_html__( 'Programmation', 'waff' ) ?></span></div>
							<button type="button" class="btn btn-light mr-2 mb-2"><?= esc_html__( 'View archives', 'waff' ) ?></button>
							<button type="button" class="btn btn-light mr-2 mb-2"><?= esc_html__( 'View blog', 'waff' ) ?></button>
							<button type="button" class="btn btn-outline-light mr-4 mb-2" data-bs-dismiss="modal"><?= esc_html__( 'Close', 'waff' ) ?></button>
						</div>
					</div>
				</div>
			</div>
			
		<?php endif; // $current_edition_films_are_online ?> 

		<!-- END: Programmation Widget  -->
			
		<?php
		// echo $args['after_widget'];
	}
	          
	// Widget Backend 
	public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, $this->default_instance );
	    if ( !isset($instance['classes']) )
	        $instance['classes'] = null;

		/** This filter is documented in wp-includes/class-wp-editor.php */
		$text = apply_filters( 'the_editor_content', $instance['text'], $default_editor );

		// Reset filter addition.
		if ( user_can_richedit() ) {
			remove_filter( 'the_editor_content', 'format_for_editor' );
		}

		// Prevent premature closing of textarea in case format_for_editor() didn't apply or the_editor_content filter did a wrong thing.
		$escaped_text = preg_replace( '#</textarea#i', '&lt;/textarea', $text );

		// Widget admin form
		?>
		<p style="display: none;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'waff' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:', 'waff' ); ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
		</p>
	    <p>
		    <label for="<?php echo $this->get_field_id('classes'); ?>"><?php esc_html_e('Classes:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" class="widefat classes sync-input" value="<?php echo esc_attr( $instance['classes'] ); ?>"/>
		</p>
		<?php 
	}
	      
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$new_instance = wp_parse_args(
			(array) $new_instance,
			$this->default_instance
		);

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['classes'] = sanitize_text_field( $new_instance['classes'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		return $instance;
	}
	 
// Class projections ends here
} 
 
 
// Register and load the widget
function WP_Widget_Programmation_init() {
    register_widget( 'WP_Widget_Programmation' );
}
add_action( 'widgets_init', 'WP_Widget_Programmation_init' );
