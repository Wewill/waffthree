<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

$local_page_class = '';
$local_page_class .= ( true === is_home() || true === is_front_page() )?'f-w pt-0 ':'';

$parent_post = get_post($post->post_parent); 

// add classes to wp_list_pages
function wp_list_pages_filter( $output, $parsed_args, $pages ) {
	if ( is_page_template( 'template-navlist.php' ) ){
  		$output = str_replace('page_item', 'page_item col-sm-3', $output);
		// $output = preg_replace( '/(<li )/', '<span data-aos="fade-down" data-aos-delay="200" ', $output);

		preg_match_all( '/(<li([^>]+)>)/', $output, $matches);
		foreach($matches[0] as $idx => $match) {
			$replace = sprintf("<span%s %s>", $matches[2][$idx], 'data-aos="fade-down" data-aos-delay="'.(200 * ($idx + 1)).'"');
			$output = str_replace($match, $replace, $output);
		}

		$output = str_replace('</li>', '</span>', $output);
	}
  	return $output;
}
add_filter('wp_list_pages', 'wp_list_pages_filter', 90, 3);

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTNAVLIST</code>':'');
?>

<article <?php post_class($local_page_class); ?> id="post-<?php the_ID(); ?>">

	<?php if ( empty( get_the_content() ) ) Go\page_title(); ?>

	<div class="<?php Go\content_wrapper_class( 'content-area__wrapper' ); ?>">
		<div class="content-area --entry-content row">

		<?php if ( empty( get_the_content() ) ) : ?>
			<div class="col-sm-12">
				<?php
					// Get sub pages list 
					$navlist = wp_list_pages( array(
						'title_li'    => '', // No titles
						'child_of'    => $post->ID,
						'echo'		  => 0
					) );
				?>
				
				<?php if ( !empty( $navlist ) ) : ?>
				<!-- Nav list -->
				<div class="nav-list without-content list-unstyled row">
					<?= $navlist; ?>
				</div>
				<?php else : ?>
					<div class="alert alert-warning fade show" role="alert">
						<strong>Allmost done!</strong> Please add sub pages or start writing this page content</a>.
					</div>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<div class="col-sm-3">
				<!-- Back to nav list-->
				<a class="btn btn-outline-secondary btn-lg p-3 w-100" href="<?= get_permalink($post->post_parent); ?>"> < <?= esc_html($parent_post->post_title) ?></a>

				<!-- Nav list -->
				<div class="nav-list with-content list-unstyled">
					<?php
					// Get sub pages list 
					wp_list_pages( array(
						'title_li'    => '', // No titles
						'child_of'    => $post->ID,
					) );

					if ( $post->post_parent ) {
						wp_list_pages( array(
							'title_li' => '',
							'child_of' => $post->post_parent,
						) );
					} else {
						wp_list_pages( array(
							'title_li' => '',
							'child_of' => $post->ID,
						) );
					}
					?>
				</div>
			</div>
			<div class="col-sm-9 --entry-content">
				<div class="card border-secondary mb-3 w-100">
					<div class="card-header"><h5 class="my-2"><?php the_title(); ?></h5></div>
					<div class="card-body"><?php the_content(); ?></div>
				</div>
				<?php wp_link_pages(); ?>
			</div>
		<?php endif; ?>
		</div>
	</div>

</article>