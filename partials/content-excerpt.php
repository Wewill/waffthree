<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Waff
 */

if ( get_post_type(get_the_ID()) === 'post' ) {
	// Get post meta fields
	$excerpt_atts['post_color'] 			= rwmb_meta( '_waff_bg_color_metafield', $args, get_the_ID() );
	$excerpt_atts['post_color_class']		= ( $excerpt_atts['post_color'] )?'style="background-color:'.$excerpt_atts['post_color'].'!important;"':'';
	$excerpt_atts['article_class']			= ( $excerpt_atts['post_color'] )?'f-w-gutter p-gutter-sm-r p-gutter-sm-l has-color excerpt':'excerpt';	
}

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTEXCERPT</code>':'');
?>

<article <?php post_class($excerpt_atts['article_class']); ?> id="post-<?php the_ID(); ?>" <?= $excerpt_atts['post_color_class'] ?>>

	<?php if ( is_singular() && has_post_thumbnail() ) : ?>
		<div class="post__thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
				<?php the_post_thumbnail(); ?>
			</a>
		</div>
	<?php endif; ?>

	<header class="entry-header m-auto px">
		<?php
		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky-post">%s</span>', esc_html_x( 'Featured', 'post', 'go' ) );
		}

		if ( is_singular() ) :
			the_title( '<h1 class="post__title entry-title m-0">', '</h1>' );
		else :
			if ( get_post_type(get_the_ID()) === 'film' ) : 
				$film_french_title 	= get_post_meta( get_the_ID(), 'wpcf-f-french-operating-title', true ); 
				$film_length 		= get_post_meta( get_the_ID(), 'wpcf-f-movie-length', true ); 
					the_title( 
						sprintf( '<h2 class="post__title entry-title m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), 
						sprintf( '</a> <span class="length light">%s\'</span> <span class="subline-4 text-muted mb-1">%s</span></h2>', $film_length, $film_french_title )
					);
				else :
					the_title( sprintf( '<h2 class="post__title entry-title m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
				endif;
		endif;

		//DEBUG
		echo ((true === WAFF_DEBUG)?'<code> ##META'.is_singular().'</code>':'');

		WaffTwo\waff_post_meta( get_the_ID(), 'top' );
		?>
	</header>

	<div class="<?php Go\content_wrapper_class( 'content-area__wrapper' ); ?>">
		<div class="content-area entry-content">
			<?php 
				if ( get_post_type(get_the_ID()) === 'film' ) : 
					$film_punchline_french 	= get_post_meta( get_the_ID(), 'wpcf-f-punchline-french', true ); 
					$film_punchline_english = get_post_meta( get_the_ID(), 'wpcf-f-punchline-english', true ); 
					printf('<p class="--lead --light pt-0 pb-4 punchlines"><!-- French : f-punchline-french -->%s<!-- .sep -->	· <!-- English : f-punchline-english --><em class="italics">%s</em></p>',
					$film_punchline_french, $film_punchline_english);
				else :
					the_excerpt(); 
				endif;
			?>
		</div>
	</div>

</article>
