<?php
/**
 * File: archive.php (for archives and blog landing).
 *
 * @package Waff
 */

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##SEARCH.php</code>':'');

get_header();

Go\page_title();

$wrapper_class = "row row-cols-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-3 align-items-stretch gx-4 gy-2";


if ( have_posts() ) {

	// Start the Loop.
	echo '<div class="'.$wrapper_class.'">';
	while ( have_posts() ) :
		the_post();
		get_template_part( 'partials/content', 'excerpt' );
	endwhile;
	echo '</div>';

	// Previous/next page navigation.
	get_template_part( 'partials/pagination' );

} else {

	// If no content, include the "No posts found" template.
	get_template_part( 'partials/content', 'none' );
}

get_footer();