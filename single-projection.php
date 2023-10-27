<?php
/**
 * Partial: single-film.php
 * Display permalinks or full articles
 *
 * @package WAFF
 */

// Debut Relocate 
global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;

// Relocate
$relocate = false;
$shownotice = true;

// Get user loggued in
$user = wp_get_current_user();
$allowed_roles = array('fifam_editor', 'fifam_admin', 'fifam_subscriber', 'administrator');

// Get current_edition_films_are_online option
//wp_die('<pre>'.$current_edition_films_are_online.'</pre>');

// Get selected edition term
$terms = get_the_terms( get_the_ID(), 'edition' );
$selected_edition = $terms[0]->term_id; 
$selected_editions = array();
foreach ($terms as $term) {
	$selected_editions[] = $term->term_id;
}

// Search into terms if one is the current edition
/*$terms = get_terms( 'edition', array('hide_empty' => false));
foreach ( $terms as $term ) {
	$meta = get_term_meta($term->term_id, 'wpcf-e-current-edition', true);
	if ( isset($meta) && $meta == 1)
		$current_edition_id = $term->term_id;
}*/
//wp_die('<pre>'.$current_edition_id.' / ' . $selected_edition . '</pre>');

// Get No edition
$noedition = get_query_var( 'noedition', null );

// Redirect if not current edition section
// Don\'t do any of that if USER is loggued / THEN write it ( below) 
//if ( $selected_edition != $current_edition_id ) {
if ( !in_array($current_edition_id, $selected_editions) ) {
	//echo '#####';
	if ( empty(array_intersect($allowed_roles, $user->roles )) )
		$relocate = true;
} 

// Redirect if option current content online 
// Don\'t do any of that if USER is loggued / THEN write it ( below) 
if( $current_edition_films_are_online == false ){
	//echo '%%%%%%';
	if ( empty(array_intersect($allowed_roles, $user->roles )) )
		$relocate = true;
} 

// Redirect if film status don't fit 
// Don\'t do any of that if USER is loggued / THEN write it ( below) 
if( !in_array($status, $allowed_status) ){
	//echo '******';
	if ( empty(array_intersect($allowed_roles, $user->roles )) )
		$relocate = true;
} 

if( $noedition == 1 ) {
	$relocate = false;
	$shownotice = false;
} 


/*
echo '###DEBUG###';
echo '<br/>In allowed status=';
echo in_array($status, $allowed_status);
echo '<br/>Section In allowed edition=';
echo ($selected_edition == $current_edition_id)?'YES':'NO'; 
echo '<br/>Sections In allowed edition=';
echo in_array($current_edition_id, $selected_editions); 
echo '<br/>Online option checked=';
echo ($current_edition_films_are_online == true)?'YES':'NO';
echo '<br/>ROLES:<br/>user=';
print_r($user->roles);
echo '<br/>allowed=';
print_r($allowed_roles);
echo '<br/>In allowed users=';
print_r(array_intersect($allowed_roles, $user->roles ));
echo '<br/>In allowed edition / empty ? =';
echo empty(array_intersect($allowed_roles, $user->roles ));
*/

if ( 	
	in_array($status, $allowed_status) 
	&& in_array($current_edition_id, $selected_editions)
	&& $current_edition_films_are_online == true
) 
	$shownotice = false;

/*
echo '<br/>RESULT:';
echo '<br/>notice=';
echo ($shownotice == true)?'YES':'NO';
echo '<br/>relocate=';
echo ($relocate == true)?'YES':'NO';
*/

/*
Bon status 			+ Bon edition 			+ film online > relocate false show notice false 
Bon status 			+ Pas bon edition 		+ film online > relocate true show notice true 
Pas Bon status 		+ Bon edition 			+ film online > relocate true show notice true 
Pas Bon status 		+ Pas Bon edition 		+ film online > relocate true show notice true 

Bon status 			+ Bon edition 			+ film offline > relocate true show notice true 
Bon status 			+ Pas bon edition 		+ film offline > relocate true show notice true 
Pas Bon status 		+ Bon edition 			+ film offline > relocate true show notice true 
Pas Bon status 		+ Pas Bon edition 		+ film offline > relocate true show notice true 
*/
		
if( $relocate == true ){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".get_bloginfo('url'));
	exit();
	//echo 'JE REDIRIGE';
}
// Fin Relocate 

// Write overaccess for TEAM ($allowed_roles)  
if( array_intersect($allowed_roles, $user->roles ) &&  $shownotice == true ) { 
?> 
<div class="admin-notice" style="background: hsl(275, 100%, 50%)!important;">
<!-- Notice for allowed roles -->
	<div class="admin-notice-content" style="color: #FFF!important;padding-top:2%;padding-bottom:3%;padding-left:30%;padding-right:30%;">
		<h4 style="text-align: center;font-size: 1.4em;color: #FFF;margin-bottom: 1em;">Hidden content / Contenu caché</h4>
		<p class="d-none d-sm-block" style="text-align: center;">You can see this content only because you are loggued to the website and you are an administrator or editor. Otherwise it will show only if the current edition match selected edition and if push online option is true and if film status is approved or programmed.</p>
		<p class="d-none d-sm-block" style="text-align: center;"><em>Vous pouvez voir ce contenu uniquement parce que vous êtes connecté au site web et que vous êtes administrateur ou éditeur. Sinon, il ne sera affiché que si l'édition actuelle correspond à l'édition sélectionnée et si l'option de mise en ligne est cochée et si le status de film est approuvé ou programmé.</em></p>
		<pre class="d-none d-sm-block" style="border: .15rem solid rgba(255, 255, 255, 0.75);line-height: 1.5;padding: 2rem 2rem;text-align: left;"><strong>Projection selected edition : </strong><?php echo $selected_edition; ?><br/><strong>Projection selected editions : </strong><?php echo implode(',', $selected_editions); ?><br/><strong>Current edition : </strong><?php echo $current_edition_id; ?><br/><strong>Option : films are online : </strong><?php var_dump($current_edition_films_are_online);?><strong>User loggued in : </strong><?php echo $user->roles[0]; ?></pre>
	</div>
<!-- End: Notice for allowed roles -->
</div>
<?php 
}

// Header
get_header();

// Start the Loop.
while ( have_posts() ) :
	the_post();
	// Display content 
	get_template_part( 'partials/content', 'projection' );

	// Output 
	$output = [];
	$orders = [];

	// Get post id & type
	$postid = get_the_ID();
	$posttype = get_post_type();
	// echo('##postid<pre>'); print_r($postid);  echo('</pre>');
	// echo('##posttype<pre>'); print_r($posttype);  echo('</pre>');

	// ********************
	// Check if has_film
	$relationship		= 'film';
	$forposttype 		= 'projection';
	$identification 	= 'film_projection'; // > Toolset 3.0
	if ( $identification != '' ) 
		$has_relationship = toolset_get_relationship( $identification ); // DINARD
	else
		$has_relationship = toolset_get_relationship( array( $relationship, $forposttype ) ); //FIFAM / KO DINARD // FIFAM Old < 2.3 
	// echo('##has_relationship<pre>'); print_r($has_relationship);  echo('</pre>');

	if ( $has_relationship ) {
		$parent = $has_relationship['roles']['parent']['types'][0];
		$child = $has_relationship['roles']['child']['types'][0];
		$origin = ( $parent == $posttype ) ? 'child' : 'parent';
		$returning = ( $parent == $posttype ) ? 'parent' : 'child';
		// echo('##parent<pre>'); print_r($parent);  echo('</pre>');
		// echo('##origin<pre>'); print_r($origin);  echo('</pre>');
		// echo('##returning<pre>'); print_r($returning);  echo('</pre>');
		
		//Get connected posts // FIFAM Old < 2.3 
		// $connections = toolset_get_related_posts( $postid, array($relationship,$forposttype), $origin, 9999, 0, array(), 'post_id', 'other', null, 'ASC', true, $count_connections );
		// if ( !empty($connections) )  $has_projections = true;

		// Get connected posts // DINARD New > 2.3 
		$connections = toolset_get_related_posts(
			$postid, //query_by_elements : single ID or array( 'parent' => $films_in_section_results ), //query_by_elements
			$identification, //relationship
			array(
				'query_by_role' => $returning, // Origin post role / query_by_role: Name of the element role to query by. This argument is required if a single post is provided in $query_by_elements, and in other cases, it must not be present at all. Accepted values: 'parent' | 'child' | 'intermediary'.
				'role_to_return' => 'all', // Role of posts to return : 'parent' | 'child' | 'intermediary' | 'all'
				'return' => 'post_object', // Return array of IDs (post_id) or post objects (post_object)
				'limit' => 999, // Max number of results
				'offset' => 0, // Starting from
				// 'orderby' => 'title', 
				// 'order' => 'ASC',
				'need_found_rows' => false, // also return count of results
				'args' => null // Array for adding meta queries etc.
			)
		);
		//retrieve parent post ID from a one to many relationsship when knowing the child post-ID
		//$connections = toolset_get_related_posts( $postid, $identification, 'parent' );
		// echo('##connections<pre>'); print_r($connections); echo('</pre>');

		// Finally, we just have to check if we have connections 
		if ( !empty($connections) && count($connections) > 0 ) {
			// $has_projections 		= true;
			// $has_youngpublic		= false;
			// $has_highlight			= false;
			// $has_guest				= false;
			// $has_debate				= false; //ADDED #43
			// $has_tag				= false;
			// $guests					= array();

			foreach($connections as $index => $connection) {
				$output[] = $connection["parent"]->ID;
			}
		}
	}
	
	// ********************
	// Check if has_programs
	$relationship		= 'film';
	$forposttype 		= 'projection';
	$identification 	= 'films-projection'; // > Toolset 3.0
	if ( $identification != '' ) 
		$has_relationship = toolset_get_relationship( $identification ); // DINARD
	else
		$has_relationship = toolset_get_relationship( array( $relationship, $forposttype ) ); //FIFAM / KO DINARD // FIFAM Old < 2.3 
	// echo('##has_relationship<pre>'); print_r($has_relationship);  echo('</pre>');

	if ( $has_relationship ) {
		$parent = $has_relationship['roles']['parent']['types'][0];
		$child = $has_relationship['roles']['child']['types'][0];
		$origin = ( $parent == $posttype ) ? 'child' : 'parent';
		$returning = ( $parent == $posttype ) ? 'parent' : 'child';
		// echo('##parent<pre>'); print_r($parent);  echo('</pre>');
		// echo('##origin<pre>'); print_r($origin);  echo('</pre>');
		// echo('##returning<pre>'); print_r($returning);  echo('</pre>');
		
		//Get connected posts // FIFAM Old < 2.3 
		// $connections = toolset_get_related_posts( $postid, array($relationship,$forposttype), $origin, 9999, 0, array(), 'post_id', 'other', null, 'ASC', true, $count_connections );
		// if ( !empty($connections) )  $has_projections = true;

		// Get connected posts // DINARD New > 2.3 
		$connections = toolset_get_related_posts(
			$postid, //query_by_elements : single ID or array( 'parent' => $films_in_section_results ), //query_by_elements
			$identification, //relationship
			array(
				'query_by_role' => $returning, // Origin post role / query_by_role: Name of the element role to query by. This argument is required if a single post is provided in $query_by_elements, and in other cases, it must not be present at all. Accepted values: 'parent' | 'child' | 'intermediary'.
				'role_to_return' => 'all', // Role of posts to return : 'parent' | 'child' | 'intermediary' | 'all'
				'return' => 'post_object', // Return array of IDs (post_id) or post objects (post_object)
				'limit' => 999, // Max number of results
				'offset' => 0, // Starting from
				// 'orderby' => 'title', 
				// 'order' => 'ASC',
				'need_found_rows' => false, // also return count of results
				'args' => null // Array for adding meta queries etc.
			)
		);
		//retrieve parent post ID from a one to many relationsship when knowing the child post-ID
		//$connections = toolset_get_related_posts( $postid, $identification, 'parent' );
		// echo('##connections<pre>'); print_r($connections); echo('</pre>');

		// Finally, we just have to check if we have connections 
		if ( !empty($connections) && count($connections) > 0 ) {
			// $has_projections 		= true;
			// $has_youngpublic		= false;
			// $has_highlight			= false;
			// $has_guest				= false;
			// $has_debate				= false; //ADDED #43
			// $has_tag				= false;
			// $guests					= array();

			foreach($connections as $index => $connection) {
				$output[] = $connection["parent"]->ID;
				$orders[] = get_post_meta($connection["intermediary"]->ID, "wpcf-f-p-film-order", true) ;
			}
		}
	}

	// echo('##output<pre>'); print_r($output);  echo('</pre>');
	// echo('##orders<pre>'); print_r($orders);  echo('</pre>');

	// Then orders by wpcf-f-p-film-order
	$ordered = [];
	foreach($output as $k => $v) 
		$ordered[$orders[$k]] = $v;
	ksort($ordered);
	// echo('##ordered<pre>'); print_r($ordered);  echo('</pre>');

?>
	<section class="row g-0 align-items-center py-2 --offset-md-2 col-10">
	<!-- FILM CARD -->
		<?php
		global $attributes;
		// Start the Loop.
		foreach ( $ordered as $k => $film_ID ) :
			$promote 	= get_post_meta($film_ID, 'wpcf-f-promote', true);
			$film_color = rwmb_meta( 'waff_film_color', array(), $film_ID );
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
			echo render_view_template( $view_id, $film_ID ); // ID de la vue Film card / film-card
		endforeach;
		?>					
	<!-- END FILM CARD -->
	</section>
	<?php
	// Previous/next page navigation.
	get_template_part( 'partials/pagination' );
	



	// If comments are open or we have at least one comment, load up the comment template.
	/*if ( comments_open() || get_comments_number() ) {
		comments_template();
	}*/

endwhile;

get_footer();