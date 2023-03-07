<?php
/**
 * Template part for displaying films
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

//print_r(types_render_field( 'f-french-operating-title', array() ));
//print_r(get_post_meta( $post->ID, 'wpcf-f-french-operating-title', false ));

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTFILM</code>':'');

?>
<!-- Begin: SINGLE FILM-->
<article <?php post_class(); ?> id="film-<?php the_ID(); ?>">

	<?php
	if ( is_search() || ( get_theme_mod( 'blog_excerpt', false ) && is_home() ) ) {
		echo "SEARCH / EXTRAIT ( remettre le title ? )";
		the_excerpt();
	} else {
		the_content();
	}
	wp_link_pages(
		array(
			'before' => '<nav class="post-nav-links" aria-label="' . esc_attr__( 'Page', 'go' ) . '"><span class="label">' . __( 'Pages:', 'go' ) . '</span>',
			'after'  => '</nav>',
		)
	);
	?>

	<?php
	if ( is_singular() ) {
		Go\post_meta( get_the_ID(), 'single-bottom' );
	}
	?>

</article>
<!-- End: SINGLE FILM-->