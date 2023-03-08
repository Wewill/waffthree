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

// Get status
$status = get_post_meta($post->ID, '_status', true);
$allowed_status = array('approved', 'programmed');	
//wp_die('<pre>'.var_dump($status).'</pre>');

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
		<p style="text-align: center;">You can see this content only because you are loggued to the website and you are an administrator or editor. Otherwise it will show only if the current edition match selected edition and if push online option is true and if film status is approved or programmed.</p>
		<p style="text-align: center;"><em>Vous pouvez voir ce contenu uniquement parce que vous êtes connecté au site web et que vous êtes administrateur ou éditeur. Sinon, il ne sera affiché que si l'édition actuelle correspond à l'édition sélectionnée et si l'option de mise en ligne est cochée et si le status de film est approuvé ou programmé.</em></p>
		<pre style="border: .15rem solid rgba(255, 255, 255, 0.75);line-height: 1.5;padding: 2rem 2rem;text-align: left;"><strong>Film status : </strong><?php echo $status; ?><br/><strong>Film selected edition : </strong><?php echo $selected_edition; ?><br/><strong>Film selected editions : </strong><?php echo implode(',', $selected_editions); ?><br/><strong>Current edition : </strong><?php echo $current_edition_id; ?><br/><strong>Option : films are online : </strong><?php var_dump($current_edition_films_are_online);?><strong>User loggued in : </strong><?php echo $user->roles[0]; ?></pre>
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
	get_template_part( 'partials/content', 'film' );

	// If comments are open or we have at least one comment, load up the comment template.
	/*if ( comments_open() || get_comments_number() ) {
		comments_template();
	}*/

endwhile;

get_footer();