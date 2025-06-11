<?php
/**
 * Partial: single-farm.php
 * Display permalinks or full articles
 *
 * @package WAFF
 */

// Header
get_header();

// Start the Loop.
while ( have_posts() ) :
	the_post();
	get_template_part( 'partials/content', 'farm' );

	$post_id = get_the_ID();
	$datas = get_belongs('farm', $post_id);
	if ( $datas ) {
		foreach ( $datas as $directory_post ) {
			setup_postdata( $directory_post );
			get_template_part( 'partials/content', 'directory', array( 'post' => $directory_post ) );
		}
		wp_reset_postdata();
	}

	// // If comments are open or we have at least one comment, load up the comment template.
	// if ( comments_open() || get_comments_number() ) {
	// 	comments_template();
	// }

endwhile;

// Footer
get_footer();