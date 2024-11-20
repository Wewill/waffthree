<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTPOST (updated merge 2022 > not sure )</code>':'');
$post_color 		= rwmb_meta( '_waff_bg_color_metafield', array(), $post->ID );
$post_color_class	= ( $post_color )?'style="background-color:'.$post_color.'!important;"':'';
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

<?php if ( !is_singular() ) : ?>

	<?php echo ((true === WAFF_DEBUG)?'<code> ##!is_singular</code>':''); ?>

	<header class="row content-header g-0">
		<div class="col-12 col-md-6 <?= (($wp_query->current_post % 2 == 0)?'order-1':'order-md-2 order-1') ?> h-250-px-md">

			<!-- Thumbnail -->
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="post__thumbnail" data-aos="fade-down">
					<?php the_post_thumbnail('post-thumbnail', ['class' => 'img-responsive fit-image h-100 h-250-px-md']); ?>
				</div>
			<?php endif; ?>

		</div>
		<div class="contrast--light col-12 col-md-6 p-6 <?= (($wp_query->current_post % 2 == 0)?'order-2':'order-md-1 order-2') ?>" <?= $post_color_class;?>>
			
			<!-- Title -->
			<?php
				// Archives 
				the_title( sprintf( '<h2 class="subline-3 post__title entry-title m-0"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
			?>

			<!-- Metas -->
			<?php //Go\post_meta( get_the_ID(), 'top' ); ?>
			<div class="post-meta">
				<?php
					// Archives > totalité 
					print(WaffTwo\waff_entry_meta_header());
				?>
			</div>

			<!-- Contents -->
			<div class="content <?php Go\content_wrapper_class( 'content-area__wrapper' ); ?>">

				<div class="pt-4 pb-0 content-area entry-content">
					<!-- Content -->
					<?php
						/* List */
						echo ((true === WAFF_DEBUG)?'<code> ##the_excerpt</code>':'');
						the_excerpt();

						wp_link_pages(
							array(
								'before' => '<nav class="post-nav-links" aria-label="' . esc_attr__( 'Page', 'go' ) . '"><span class="label">' . __( 'Pages:', 'go' ) . '</span>',
								'after'  => '</nav>',
							)
						);
					?>
				</div>

		</div>
	</header>

	<?php else : ?>

		<?php echo ((true === WAFF_DEBUG)?'<code> ##is_singular</code>':''); ?>

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
					/* List */
					echo ((true === WAFF_DEBUG)?'<code> ##the_excerpt</code>':'');
					the_excerpt();
				} else {
					/* List */
					echo ((true === WAFF_DEBUG)?'<code> ##the_content</code>':'');
					the_content();
				}
				
				echo ((true === WAFF_DEBUG)?'<code> ##wp_link_pages</code>':'');
				wp_link_pages(
					array(
						'before' => '<nav class="post-nav-links" aria-label="' . esc_attr__( 'Page', 'go' ) . '"><span class="label">' . __( 'Pages:', 'go' ) . '</span>',
						'after'  => '</nav>',
					)
				);
				?>
			</div>

			<?php
			if ( is_singular() && !is_singular(array('partenaire', 'projection'))) {
				// Ajouter une sidebar
				echo ((true === WAFF_DEBUG)?'<code> ##WAFF_SHOW_ASIDE?</code>':'');
				if ( true === WAFF_SHOW_ASIDE ) {
			?>
				<aside class="col-12 col-sm-2 offset-sm-1 pt-2 pt-md-8 pb-8">

					<!-- Aside -->
					<?php get_sidebar('sidebar-1'); ?>

					<!-- Single bottom metas side / mostly tags -->
					<?php 
						echo ((true === WAFF_DEBUG)?'<code> ##post_meta</code>':'');
						// Go\post_meta( get_the_ID(), 'single-bottom' );
					?>
		
				</aside>
			<?php
				}
			}
			?>

		</div>
		
	<?php endif; ?>

</article>
