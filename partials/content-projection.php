<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Go
 */

$local_page_class = '';
$local_page_class .= ( $post->post_content )?'mb-6 ':'';

//DEBUG
echo ((true === WAFF_DEBUG)?'<code> ##CONTENTPROJECTION</code>':'');
?>

<?php /*if ( has_post_thumbnail() ) : ?>
	<figure class="post__thumbnail">
		<?php the_post_thumbnail(); ?>
	</figure>
<?php endif;*/ ?>

<article <?php post_class($local_page_class); ?> id="post-<?php the_ID(); ?>">

	<div class="<?php Go\content_wrapper_class( 'content-area__wrapper' ); ?>">
		<div class="content-area entry-content">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div>
	</div>

</article>
