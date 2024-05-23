<?php
/**
 * File: archive.php (for archives and blog landing).
 *
 * @package Waff
 */

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##ARCHIVE.php</code>':'');

// Relocate
$relocate = true;

// Get post_type
if( true === WAFF_DEBUG ){
	print_r( get_queried_object()); 
}

$archive_post_type = is_archive() ? get_queried_object()->name : false;
$allowed_post_type = array('film', 'client', 'accreditation', 'contact', 'projection', 'jury', 'directory');	

$archive_taxonomy = ( is_archive() && is_tax() ) ? get_queried_object()->taxonomy : false;
$allowed_taxonomy = array('function', 'movie-type', 'partenaire-category', 'category', 'thematic', 'operation'); // 'edition' => taxonomy-edition.php 'section' => taxonomy-section.php 'room' => taxonomy-room.php 


// Redirect if archive_post_type don't fit 
if( in_array($archive_post_type, $allowed_post_type) ){
	$relocate = false;
} 

if( isset($archive_taxonomy) && in_array($archive_taxonomy, $allowed_taxonomy) ){
	$relocate = false;
}
 
if( true === WAFF_DEBUG ){

	/*echo '###DEBUG###';

	print_r(var_dump($archive_taxonomy));
	print_r(var_dump($allowed_taxonomy));

	echo '<br/>In allowed post type=';
	echo in_array($archive_post_type, $allowed_post_type);
	echo '<br/>Is post type ?';
	echo (!empty($archive_post_type))?'YES':'NO';
	echo $archive_post_type;

	echo '<br/>In allowed taxonomy =';
	echo in_array($archive_taxonomy, $allowed_taxonomy);
	echo in_array($allowed_taxonomy, $archive_taxonomy);
	echo '<br/>Is taxonomy ?';
	echo (!empty($archive_taxonomy))?'YES':'NO';
	echo $archive_taxonomy;

	echo '<br/>##RESULT:';
	echo '<br/>relocate=';
	echo ($relocate == true)?'YES':'NO';*/

}

if( $relocate == true ){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".get_bloginfo('url'));
	exit();
	// echo "##Je redirige";
}

get_header();

Go\page_title();

if ( have_posts() ) {

	// Start the Loop.
	while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/content', 'excerpt' );
	endwhile;

	// Previous/next page navigation.
	get_template_part( 'partials/pagination' );

} else {

	// If no content, include the "No posts found" template.
	get_template_part( 'partials/content', 'none' );
}

get_footer();
