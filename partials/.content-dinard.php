<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Waff
 */

 //DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTPOST</code>':'');
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php if ( !is_singular() && has_post_thumbnail() ) : ?>
		<div class="post__thumbnail mb-6" data-aos="fade-down">
			<?php the_post_thumbnail(); ?>
		</div>
	<?php endif; ?>
	
	<!-- Metas -->
	<header class="row content-header">
		<div class="<?php echo (( is_singular() )?'col-12 col-sm-9':'col-12' ) ?>">

			<?php
			if ( is_singular() ) :
				// Pas de titre car il est dans le header
				//the_title( '<h1 class="post__title entry-title m-0">', '</h1>' );
			else :
				// Archives > un titre 
				the_title( sprintf( '<h2 class="post__title entry-title m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			endif;
			?>
			
			<?php //Go\post_meta( get_the_ID(), 'top' ); ?>
			<div class="post-meta">
				<?php
				if ( is_singular() ) :
					// Partiel car deja dans le header
					print(WaffTwo\waff_entry_meta_footer());
				else :
					// Archives > totalité 
					print(WaffTwo\waff_entry_meta_header());
				endif;
				?>
			</div>
		
		</div>
	</header>

	<!-- Contents -->
	<div class="row content contrast--light <?php Go\content_wrapper_class( 'content-area__wrapper' ); ?>">

		<div class="<?php echo (( is_singular() )?'col-12 col-sm-9 pt-8 pb-8 --pb-md-8':'col-12 is-style-wide pt-4 pb-0' ) ?> content-area entry-content">
			<!-- Content -->
			<?php
			if ( is_search() || ( get_theme_mod( 'blog_excerpt', false ) && is_home() ) ) {
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
		</div>

		<?php
		if ( is_singular() ) {
			Go\post_meta( get_the_ID(), 'single-bottom' );
			// Ajouter une sidebar
			if ( true === WAFF_SHOW_ASIDE ) {
		?>
			<aside class="col-12 col-sm-2 offset-sm-1 pt-2 pt-md-8 pb-8">

				<!-- Aside -->
				<?php get_sidebar('sidebar-1'); ?>
	
			</aside>
		<?php
			}
		}
		?>

	</div>

</article>
