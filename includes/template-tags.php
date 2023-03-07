<?php
/**
 * Custom template tags for this child theme.
 *
 * This file is for custom template tags only and it should not contain
 * functions that will be used for filtering or adding an action.
 *
 * @package WaffTwo
 */

namespace WaffTwo;

use function Go\load_inline_svg as load_inline_svg;

/**
 * Go func override 
 * Return the Post Meta.
 *
 * @param int    $post_id The ID of the post for which the post meta should be output.
 * @param string $location Which post meta location to output.
 */
function waff_post_meta( $post_id = null, $location = 'top' ) {

	echo waff_get_post_meta( $post_id, $location ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in get_post_meta().

}

/**
 * Go func override 
 * Get the post meta.
 *
 * @param int    $post_id The iD of the post.
 * @param string $location The location where the meta is shown.
 */
function waff_get_post_meta( $post_id = null, $location = 'top' ) {

	// Require post ID.
	if ( ! $post_id ) {
		return;
	}

	$page_template = get_page_template_slug( $post_id );

	// Check whether the post type is allowed to output post meta.
	$disallowed_post_types = apply_filters( 'go_disallowed_post_types_for_meta_output', array( 'accreditation', 'contact', 'ticket', 'projection', 'partenaire' ) );

	if ( in_array( get_post_type( $post_id ), $disallowed_post_types, true ) ) {
		return ((true === WAFF_DEBUG)?'<code> ##ABORD</code>':'');
	}
	// WAFF override potential diswaloowed post_types with particular post meta output. 
	$overallowed_post_types = apply_filters( 'waff_overallowed_post_types_for_meta_output', array( 'page', 'post', 'film', 'jury') );

	if ( in_array( get_post_type( $post_id ), $overallowed_post_types, true ) ) {
		printf('<span class="badge rounded-pill bg-dark mr-1 d-none">%s</span>', get_post_type( $post_id ));
		return waff_entry_meta_header();
	}
	


	/* ON SUPPRIME TOUT CE QUI EST EN DESSOUS */
/*

	$post_meta                 = false;
	$post_meta_wrapper_classes = '';
	$post_meta_classes         = '';

	// Get the post meta settings for the location specified.
	if ( 'top' === $location ) {

		$post_meta                 = apply_filters(
			'go_post_meta_location_single_top',
			array(
				'author',
				'post-date',
				'comments',
				'sticky',
			)
		);
		$post_meta_wrapper_classes = ' post__meta--single post__meta--top';

	} elseif ( 'single-bottom' === $location ) {

		$post_meta                 = apply_filters(
			'go_post_meta_location_single_bottom',
			array(
				'tags',
			)
		);
		$post_meta_wrapper_classes = ' post__meta--single post__meta--single-bottom';

	}

	// If the post meta setting has the value 'empty', it's explicitly empty and the default post meta shouldn't be output.
	if ( ! $post_meta || in_array( 'empty', $post_meta, true ) ) {

		return;

	}

	// Make sure we don't output an empty container.
	$has_meta = false;

	global $post;
	$the_post = get_post( $post_id );
	setup_postdata( $the_post );

	ob_start();

	?>

	<div class="post__meta--wrapper<?php echo esc_attr( $post_meta_wrapper_classes ); ?>">

		<ul class="post__meta list-reset<?php echo esc_attr( $post_meta_classes ); ?>">

			<?php

			// Allow output of additional meta items to be added by child themes and plugins.
			do_action( 'go_start_of_post_meta_list', $post_meta, $post_id );

			// Author.
			if ( in_array( 'author', $post_meta, true ) ) {

				$has_meta = true;
				?>
				<li class="post-author meta-wrapper">
					<span class="meta-icon">
						<span class="screen-reader-text"><?php esc_html_e( 'Post author', 'go' ); ?></span>
						<?php load_inline_svg( 'author.svg' ); ?>
					</span>
					<span class="meta-text">
						<?php
						// Translators: %s = the author name.
						printf( esc_html_x( 'By %s', '%s = author name', 'go' ), '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author_meta( 'display_name' ) ) . '</a>' );
						?>
					</span>
				</li>
				<?php

			}

			// Post date.
			if ( in_array( 'post-date', $post_meta, true ) ) {
				$has_meta = true;

				?>
				<li class="post-date">
					<a class="meta-wrapper" href="<?php the_permalink(); ?>">
						<span class="meta-icon">
							<span class="screen-reader-text"><?php esc_html_e( 'Post date', 'go' ); ?></span>
							<?php load_inline_svg( 'calendar.svg' ); ?>
						</span>
						<span class="meta-text">
							<?php
							echo wp_kses(
								sprintf(
									'<time datetime="%1$s">%2$s</time>',
									esc_attr( get_the_date( DATE_W3C ) ),
									esc_html( get_the_date() )
								),
								array_merge(
									wp_kses_allowed_html( 'post' ),
									array(
										'time' => array(
											'datetime' => true,
										),
									)
								)
							);
							?>
						</span>
					</a>
				</li>
				<?php

			}

			// Categories.
			if ( in_array( 'categories', $post_meta, true ) && has_category() ) {

				$has_meta = true;
				?>
				<li class="post-categories meta-wrapper">
					<span class="meta-icon">
						<span class="screen-reader-text"><?php esc_html_e( 'Categories', 'go' ); ?></span>
						<?php load_inline_svg( 'categories.svg' ); ?>
					</span>
					<span class="meta-text">
						<?php esc_html_e( 'In', 'go' ); ?> <?php the_category( ', ' ); ?>
					</span>
				</li>
				<?php

			}

			// Tags.
			if ( in_array( 'tags', $post_meta, true ) && has_tag() ) {

				$has_meta = true;
				?>
				<li class="post-tags meta-wrapper">
					<span class="meta-icon">
						<span class="screen-reader-text"><?php esc_html_e( 'Tags', 'go' ); ?></span>
						<?php load_inline_svg( 'tags.svg' ); ?>
					</span>
					<span class="meta-text">
						<?php the_tags( '', ', ', '' ); ?>
					</span>
				</li>
				<?php

			}

			// Comments link.
			if ( in_array( 'comments', $post_meta, true ) && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {

				$has_meta = true;
				?>
				<li class="post-comment-link meta-wrapper">
					<span class="meta-icon">
						<?php load_inline_svg( 'comments.svg' ); ?>
					</span>
					<span class="meta-text">
						<?php comments_popup_link(); ?>
					</span>
				</li>
				<?php

			}

			// Sticky.
			if ( in_array( 'sticky', $post_meta, true ) && is_sticky() ) {

				$has_meta = true;
				?>
				<li class="post-sticky meta-wrapper">
					<span class="meta-icon">
						<?php load_inline_svg( 'bookmark.svg' ); ?>
					</span>
					<span class="meta-text">
						<?php esc_html_e( 'Featured', 'go' ); ?>
					</span>
				</li>
				<?php

			}

			// Allow output of additional post meta types to be added by child themes and plugins.
			do_action( 'go_end_of_post_meta_list', $post_meta, $post_id );
			?>

		</ul>

	</div>

	<?php

	wp_reset_postdata();

	$meta_output = ob_get_clean();

	if ( ! $has_meta || empty( $meta_output ) ) {

		return;

	}

	return '#DEBUGGETPOSTMETA'.$meta_output;
	*/

}

/**
 * Options from customizer 
 */

/**
 * Displays the browser notice compability after the body tag 
 * @return void
*/
function browser_notice() {
	?>
	<!--[if lt IE 8]>
		<p class="browserupgrade d-block px-3 py-2 text-center text-bold text-dark bg-warning m-0">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" class="text-danger link">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
	<?php
}

/**
 * Displays the site phone number (set in Customizer).
 *
 * @param array $args {
 *   Optional. An array of arguments.
 *
 *   @type string $class The div class. Default is .site-phone
 * }
 *
 * @return void
 */
function display_phone( $args = array() ) {

	$args = wp_parse_args(
		$args,
		array(
			'class' => 'site-phone',
		)
	);

	$phone = get_theme_mod( 'telephone' );

	$html = array(
		'br'  => array(
			'class' => array(),
		),
		'span' => array(
			'class' => array(),
		),
		'a'    => array(
			'href'  => array(),
			'class' => array(),
		),
	);
	?>

	<?php if ( $phone != "" || is_customize_preview() ) : ?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<span id="telephone" class="phone">
				<?php echo wp_kses( $phone, $html ); ?>
			</span>
		</div>
	<?php endif; ?>

	<?php
}

/**
 * Displays the site contact email (set in Customizer).
 *
 * @param array $args {
 *   Optional. An array of arguments.
 *
 *   @type string $class The div class. Has default.
 * }
 *
 * @return void
 */
function display_email( $args = array() ) {

	$args = wp_parse_args(
		$args,
		array(
			'class' => 'site-contact-email',
		)
	);

	$email = get_theme_mod( 'email' );

	$html = array(
		'br'  => array(
			'class' => array(),
		),
		'span' => array(
			'class' => array(),
		),
		'a'    => array(
			'href'  => array(),
			'class' => array(),
		),
	);
	?>

	<?php if ( $email || is_customize_preview() ) : ?>
		<span class="<?php echo esc_attr( $args['class'] ); ?>">
			<?php echo wp_kses( $email, $html ); ?>
		</span>
	<?php endif; ?>

	<?php
}

/**
 * Displays the site message (set in Customizer).
 *
 * @param array $args {
 *   Optional. An array of arguments.
 *
 *   @type string $class The div class. Has default.
 * }
 *
 * @return void
 */
function display_site_message( $args = array() ) {

	$args = wp_parse_args(
		$args,
		array(
			'class' => 'site-message',
		)
	);

	$site_message = get_theme_mod( 'site_message' );

	$html = array(
		'div'  => array(
			'class' => array(),
		),
		'span' => array(
			'class' => array(),
		),
		'a'    => array(
			'href'  => array(),
			'class' => array(),
		),
	);
	?>

	<?php if ( $site_message != "" || is_customize_preview() ) : ?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<span id="site_message" class="message">
				<?php echo wp_kses( $site_message, $html ); ?>
			</span>
		</div>
	<?php endif; ?>

	<?php
}

/**
 * Displays the company address (set in Customizer).
 *
 * @param array $args {
 *   Optional. An array of arguments.
 *
 *   @type string $class The div class. Has default.
 * }
 *
 * @return void
 */
function display_company_address( $args = array() ) {

	$args = wp_parse_args(
		$args,
		array(
			'class' => 'company-address',
		)
	);

	$company_address = get_theme_mod( 'company_address' );

	$html = array(
		'br'  => array(),
	);
	?>

	<?php if ( $company_address != "" || is_customize_preview() ) : ?>
		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<address id="company_address"><?php echo wp_kses( $company_address, $html ); ?></address>
		</div>
	<?php endif; ?>

	<?php
}

/**
 * Displays the personal privacy statement (set in Customizer).
 *
 * @return void
 */
function display_privacy_statement() {
	$privacy_statement = get_theme_mod( 'privacy_statement' );

	if ( $privacy_statement || is_customize_preview() ) {
		echo '<span id="privacy_statement" class="privacy-statement text-sm">' . esc_html( $privacy_statement ) . '</span>';
	}
}

/**
 * Displays the catalog url (set in Customizer).
 *
 * @return void
 */
function display_planning_url() {
	$planning_url = get_theme_mod( 'planning_url' );

	if ( $planning_url || is_customize_preview() ) {
		echo esc_url( $planning_url );
	}
}

/**
 * Displays the catalog url (set in Customizer).
 *
 * @return void
 */
function display_booklet_url() {
	$booklet_url = get_theme_mod( 'booklet_url' );

	if ( $booklet_url || is_customize_preview() ) {
		echo esc_url( $booklet_url );
	}
}

/**
 * Displays the catalog url (set in Customizer).
 *
 * @return void
 */
function display_catalog_url() {
	$catalog_url = get_theme_mod( 'catalog_url' );

	if ( $catalog_url || is_customize_preview() ) {
		echo esc_url( $catalog_url );
	}
}

 /**
  * Get either Logged in or Logged out link based
  * on current user's state.
  *
  * @return void
  */
function get_loginout_link() {
	$link_text = '';

	if ( is_user_logged_in() ) {
		$link_text = 'My Account';
	}

	elseif ( ! is_user_logged_in() ) {
		$link_text = 'Sign in';
	}

	echo '<a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '" class="my-account">';
		load_inline_svg( 'account.svg' );
		echo esc_html__( $link_text, 'waff' );
	echo '</a>';
}

/**
 * Coblocks func 
 */

if ( ! function_exists( 'waff_night_toggle' ) ) {
	/**
	 * Toggle for the night mode option.
	 *
	 * Create your own coblocks_night_toggle() to override in a child theme.
	 */
	function waff_night_toggle() {

		$night      = get_theme_mod( 'night_mode', waff_defaults( 'night_mode' ) );
		$visibility = ( false === $night ) ? ' hidden' : null;
		
		if ( $night || is_customize_preview() ) {
			?>
			<button id="night-mode-toggle" class="site-header__button header__button--night-mode button--chromeless<?php echo esc_attr( $visibility ); ?>" role="switch" aria-checked="false" aria-label="<?php esc_attr_e( 'Toggle Night Mode', 'waff' ); ?>">
				<div class="night-mode-toggle-icon">
					<?php load_inline_svg( 'night-mode.svg' ); ?>
				</div>
				<span class="screen-reader-text"><?php echo esc_html_x( 'Settings', 'settings button', 'waff' ); ?></span>
			</button>
			<?php
		}
	}
}

/**
 * Go func override
 */

if ( ! function_exists( 'waff_posted_on' ) ) {
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function waff_posted_on() {
		
		$author_meta = get_theme_mod( 'author_meta', waff_defaults( 'author_meta' ) );

		//$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		// $time_string = sprintf(
		// 	$time_string,
		// 	esc_attr( get_the_date( DATE_W3C ) ),
		// 	esc_html( get_the_date( ) )
		// );
		$time_string = wp_kses(
			sprintf(
				'<time datetime="%1$s">%2$s</time>',
				esc_attr( get_the_date( DATE_W3C ) ),
				( function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() == 'en' )?date_i18n( 'l M jS, Y', strtotime( get_the_date( DATE_W3C ) )  ) : get_the_date()
			),
			array_merge(
				wp_kses_allowed_html( 'post' ),
				array(
					'time' => array(
						'datetime' => true,
					),
				)
			)
		);
		$color = ( defined('WAFF_SECONDARY_COLOR') )?WAFF_SECONDARY_COLOR:'action-3';
		echo '<span class="subline posted-on '.$color.'">';
		echo ((int)$author_meta === 1)?'— ':'';
		printf(
			/* translators: %s: publish date. */
			esc_html__( 'Published %s', 'waff' ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput
		);
		echo '</span>';
	}
}

if ( ! function_exists( 'waff_posted_by' ) ) {
	/**
	 * Prints HTML with meta information about theme author.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function waff_posted_by() {

		$author_meta = get_theme_mod( 'author_meta', waff_defaults( 'author_meta' ) );

		if ( ! get_the_author_meta( 'description' ) && post_type_supports( get_post_type(), 'author' ) && (int)$author_meta === 1) {
			//echo '<span class="subline byline">';
			printf(
				/* translators: %s author name. */
				'<span class="subline">%s</span> %s ',
				esc_html__( 'By', 'waff' ),
				'<span class="impact"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" rel="author">' . esc_html( get_the_author() ) . '</a></span>'
			);
			//echo '</span>';
		}
	}
}

if ( ! function_exists( 'waff_is_sticky' ) ) {
	/**
	 * Prints HTML with meta information about theme author.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function waff_is_sticky() {
			// Reprise de Go + reprise de twenty twenty > il faudra uniqformiser avec tout venant de Go.. 
			$post_meta                 = apply_filters(
				'go_post_meta_location_single_top',
				array(
					'author',
					'post-date',
					'comments',
					'sticky',
				)
			);
		
			if ( in_array( 'sticky', $post_meta, true ) && is_sticky() ) {

				$has_meta = true;
				
				printf(
					/* translators: %s author name. */
					'<mark class="post-sticky meta-wrapper subline align-text-bottom">
						<span class="meta-icon">%s</span>
						%s
					</mark>',
					esc_html__(load_inline_svg( 'bookmark.svg' )),
					esc_html__( 'Featured', 'waff' )
				); 

			}
	}
}

if ( ! function_exists( 'waff_continue_reading_text' ) ) {
	/**
	 * Creates continue reading text
	 */
	function waff_continue_reading_text() {
		$continue_reading = sprintf(
			/* translators: %s: Name of current post. */
			esc_html__( 'Continue reading %s', 'waff' ),
			the_title( '<span class="screen-reader-text">', '</span>', false )
		);
	
		return $continue_reading;
	}
}

/**
 * Determine the top-most parent of a term
 */ 
function get_term_top_most_parent( $term, $taxonomy ) {
    // Start from the current term
	$parent  = get_term( $term, $taxonomy );
	if ( ! empty( $parent ) ) :
		if ( ! is_wp_error( $parent ) ) :
			// Climb up the hierarchy until we reach a term with parent = '0'
			while ( $parent->parent != '0' && !empty( $parent ) ) {
				$term_id = $parent->parent;
				$parent  = get_term( $term_id, $taxonomy);
			}
		endif;
	endif;
	return $parent;
}

/**
 * Get wp_types select options to translate it 
 */ 
function get_translated_movie_category($id, $default_value = '') {
	// Define translations
	$translations = array(
		'f-movie-category' => array(
			'wpcf-fields-select-option-37cb1c83ef750c20da1a494bd6cebaab-1' => __('Fiction', 'waff'),//Fiction
			'wpcf-fields-select-option-3b3ed3d99f863222f0dffffd9698f544-1' => __('Documentary', 'waff'),//Documentaire
			'wpcf-fields-select-option-a8d18d1214b549c1821acae52f16374b-1' => __('Fiction documentary', 'waff'),//Documentaire fiction
			'wpcf-fields-select-option-ec0063cbe81a512097bbde6fc504d2b8-1' => __('Experimental', 'waff'),//Expérimental
			'wpcf-fields-select-option-bd134dbdae1dc4f4f27e099fa9fb9212-1' => __('Animation', 'waff'),//Animation
			'wpcf-fields-select-option-772e109c87963c7e5ff5b7b42c5f8b51-1' => __('Animation documentary', 'waff'),//Documentaire animation
			'wpcf-fields-select-option-3d0b5b94690691518ac6ec8a06615db8-1' => __('Drama', 'waff'),//Drame
			'wpcf-fields-select-option-ded5a984627d372fefa90a07af159855-1' => __('Comedy', 'waff'),//Comédie
			'wpcf-fields-select-option-bebdc51431b7588d2b98eaa9e78589f6-1' => __('Drama comedy', 'waff'),//Comédie dramatique
			'wpcf-fields-select-option-4092461ddd8889361f266929d3ad0fd7-1' => __('Music', 'waff'),//Musique
			'wpcf-fields-select-option-47a5efd65c4053fe56954ab8561b7f7f-1' => __('Other', 'waff'),//Autre
		),
	); 

	// Use translations w/ options fields control only  
	$fields = get_option('wpcf-fields');

	// Use translations w/ options fields control only  
	$translated = $default_value;
	if(isset($fields['f-movie-category']['data']['options']))
		foreach($fields['f-movie-category']['data']['options'] as $k => $v)
			if ( isset($v['value']) && $v['value'] == $id ) $translated = $translations['f-movie-category'][$k];

	return $translated; 
}

/**
 * Get wp_types select options to translate it 
 */ 
function get_translated_movie_type($id, $default_value = '') {
	// Define translations
	$translations = array(
		'f-movie-type' => array(
			'wpcf-fields-select-option-d66cdf85bea5b880cd081f950edcc73e-1' => __('Short film', 'waff'), //Court-métrage
			'wpcf-fields-select-option-f2562f92792fbc93c47dfd2f87bb223c-1' => __('Documentary film', 'waff'),//Long métrage documentaire
			'wpcf-fields-select-option-22f299bb745a3190b0a9733f815ab909-1' => __('Feature film', 'waff'),//Long métrage
			'wpcf-fields-select-option-a462234757e5407d52794aa3ea61e161-1' => __('Medium-length film', 'waff'),//Moyen métrage
			'wpcf-fields-select-option-9becbc9fcd7d590c2e5a91786723cc18-1' => __('Serie', 'waff'),//Série
			'wpcf-fields-select-option-d9bc21e838eb27933d01ec2239624777-1' => __('Program', 'waff'),//Programme
		),
	); 

	// Use translations w/ options fields control only  
	$fields = get_option('wpcf-fields');

	// Use translations w/ options fields control only  
	$translated = $default_value;
	if(isset($fields['f-movie-type']['data']['options']))
		foreach($fields['f-movie-type']['data']['options'] as $k => $v)
			if ( isset($v['value']) && $v['value'] == $id ) $translated = $translations['f-movie-type'][$k];

	return $translated; 
}

if ( ! function_exists( 'waff_entry_meta_header' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 * Footer entry meta is displayed differently in archives and single posts.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function waff_entry_meta_header() {
		global $current_edition_id;
		global $post;

		// Early exit if not a post, film.
		if ( !in_array( get_post_type(), array('post', 'film'), true ) ) {
			echo ((true === WAFF_DEBUG)?'<code> #NOTPOSTorFILM</code>':'');
			//return;
		}
				
		// Hide meta information on pages.
		if ( !is_single() ) {

			// DEBUG
			echo ((true === WAFF_DEBUG)?'<code> #NOTSINGLE</code>':'');

			// Render top parent terms for current term
			$taxonomy = 'section';
			$queried_object = get_queried_object();
			$taxonomy = $queried_object->taxonomy; 
			
			$top_parent_terms = array();
			if ( is_tax() ) {
				$term = get_term( get_queried_object_id(), $taxonomy );
				if ( ! empty( $term ) && $term->parent != '0') :
					//get top level parent
					//DEBUG
					echo ((true === WAFF_DEBUG)?'<code> #GETTOPLEVELPARENT</code>':'');
					echo ((true === WAFF_DEBUG)?'<pre class="p-3">'.print_r($term,1).'</pre>':'');

					$top_parent = get_term_top_most_parent( $term, $taxonomy );
					//check if you have it in your array to only add it once
					if ( !in_array( $top_parent, $top_parent_terms ) ) {
						$top_parent_terms[] = $top_parent;
					}
				endif;
			} else {
				$terms = wp_get_object_terms( get_the_ID(), $taxonomy );
				if ( ! empty( $terms ) ) :
					if ( ! is_wp_error( $terms ) ) :
						foreach ( $terms as $term ) {
							//get top level parent
							$top_parent = get_term_top_most_parent( $term, $taxonomy );
							//check if you have it in your array to only add it once
							if ( !in_array( $top_parent, $top_parent_terms ) ) {
								$top_parent_terms[] = $top_parent;
							}
						}
					endif;
				endif;
			}			
			if ( ! empty( $top_parent_terms ) ) :
				$terms_list = array();
				foreach( $top_parent_terms as $term ) {
					$termcolor 		= get_term_meta( $term->term_id, 'wpcf-s-color', true );
					$selectedition 	= get_term_meta( $term->term_id, 'wpcf-select-edition', true ); // #41
					if ( $selectedition == $current_edition_id )
					$terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
						(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.'"':''),
						esc_url(get_term_link($term)),
						esc_html__($term->name),
						esc_html__($term->name)
					);
				}
			endif;


			//DEBUG
			echo ((true === WAFF_DEBUG)?'<code> #ISTAX '.is_tax().'</code>':'');
			echo ((true === WAFF_DEBUG)?'<code> #ISTAXSECTION '.is_tax('section').'</code>':'');
			echo ((true === WAFF_DEBUG)?'<code> #HASTERMSECTION '.has_term('', 'section').'</code>':'');
			echo ((true === WAFF_DEBUG)?'<pre class="p-3">'.print_r($terms,1).'</pre>':'');
			
			// SECTION or ROOM
			//if ( is_tax('section') ) { //&& has_term('', 'section')
			if ( is_tax() ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #ISaSECTION</code>':'');		
				
				if ( $terms_list ) {
					printf(
						/* translators: %s: list of categories. */
						'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
						esc_html__( 'Categorized as', 'waff' ),
						implode($terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}
			
			}

			// SECTION + FILM SECTION PARENT 
			if ( !is_tax('section') && has_term('', 'section') ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #HAVEaSECTIONinaFILM</code>':'');		
				
				if ( $terms_list ) {
					printf(
						/* translators: %s: list of categories. */
						'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
						esc_html__( 'Categorized as', 'waff' ),
						implode($terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}

			} 

			// FILM
			if ( 'film' == get_post_type() && !is_tax() ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #ISaFILM</code>':'');		

				// Render sections
				if ( has_term('', 'section') ) {

					//DEBUG
					echo ((true === WAFF_DEBUG)?'<code> #HAVEaFILMSECTION</code>':'');		

					/* translators: used between list items, there is a space after the comma. */
					/* $terms_list = preg_replace('/<a 
					/', '<a class="section-item" style="background-color: #0000FF;"', get_the_term_list(
					get_the_ID(), 'section', 'BEFORE ', __( '&#8203;', 'waff' ) ) ); */
					//$terms = get_terms( array( 'taxonomy' => 'section' ) ); // Returns the complete list
					$terms = wp_get_object_terms( get_the_ID(),  'section' );
					if ( ! empty( $terms ) ) :
						if ( ! is_wp_error( $terms ) ) :
							$terms_list = array();
							foreach( $terms as $term ) {
								$termcolor = get_term_meta( $term->term_id, 'wpcf-s-color', true );
								$selectedition 	= get_term_meta( $term->term_id, 'wpcf-select-edition', true ); // #41
								if ( $selectedition == $current_edition_id )
								$terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
									(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.'"':''),
									esc_url(get_term_link($term)),
									esc_html__($term->name),
									esc_html__($term->name)
								);
							}
						endif;
					endif;
					
					if ( $terms_list ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							implode($terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				}

				// Render film movie types and categories
				if (function_exists('types_render_field')) :
					$film_type 				= types_render_field( 'f-movie-type', array() );
					$film_type 				= get_translated_movie_type(types_render_field( 'f-movie-type', array('output' =>'raw') ), $film_type); // Return an non zero-based index 
					$film_category 			= types_render_field( 'f-movie-category', array() );
					$film_category 			= get_translated_movie_category(types_render_field( 'f-movie-category', array('output' =>'raw') ), $film_category); // Return an non zero-based index 
					//https://toolset.com/forums/topic/can-types-works-with-qtranslate-x-httpswordpress-orgpluginsqtranslate-x/
				else: 
					$film_type 			= get_post_meta( $post->ID, 'wpcf-f-movie-type', true ); 
					$film_category 		= get_post_meta( $post->ID, 'wpcf-f-movie-category', true ); 
				endif;

				// Finally print
				if ( $film_type != '' ) printf('<div class="type-list d-inline"><a class="type-item link-disabled">%s</a></div>', sanitize_text_field($film_type) );
				if ( $film_category != '' ) printf('<div class="category-list d-inline"><a class="category-item link-disabled">%s</a></div>', sanitize_text_field($film_category) );
				
				// POST ???
				if ( has_category() || has_tag() ) {

					//DEBUG
					echo ((true === WAFF_DEBUG)?'<code> #HAVECAT&TAGinaFILM?</code>':'');		

					/* translators: used between list items, there is a space after the comma. */
					$category_color = ( defined('WAFF_SECONDARY_COLOR') )?WAFF_SECONDARY_COLOR:'action-3';
					$categories_list = preg_replace('/<a /', '<a class="section-item" style="background-color:var(--waff-'.$category_color.');border-color:var(--waff-'.$category_color.');"', get_the_category_list( __( '&#8203;', 'waff' ) ) );
					//$categories_list = get_the_category_list( __( ', ', 'waff' ) );
					if ( $categories_list ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							$categories_list // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
					
					/* translators: used between list items, there is a space after the comma. */
					$tags_list = preg_replace('/<a /', '<a class="category-item"', get_the_tag_list( '', __( '&#8203;', 'waff' ) ) );
					if ( $tags_list ) {
						printf(
							/* translators: %s: list of tags. */
							'<div class="category-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Taggued', 'waff' ),
							$tags_list // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				}
			}

			// POST
			if ( 'post' == get_post_type() ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #ISPOST</code>':'');		

				// Post Archive ? 
				echo '<span class="posted-by">';
				// Posted by.
				waff_posted_by();
				// Posted on.
				waff_posted_on();		
				// Sticky
				echo '<span class="float-right">';
				echo waff_is_sticky();
				echo '</span>';

	
				if ( has_category() || has_tag() ) {

					echo '<span class="sep"> </span>';

					/* translators: used between list items, there is a space after the comma. */
					$category_color = ( defined('WAFF_SECONDARY_COLOR') )?WAFF_SECONDARY_COLOR:'action-3';
					$categories_list = preg_replace('/<a /', '<a class="section-item" style="background-color:var(--waff-'.$category_color.');border-color:var(--waff-'.$category_color.');"', get_the_category_list( __( '&#8203;', 'waff' ) ) );
					//$categories_list = get_the_category_list( __( ', ', 'waff' ) );
					if ( $categories_list ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							$categories_list // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
					
					/* translators: used between list items, there is a space after the comma. */
					$tags_list = preg_replace('/<a /', '<a class="category-item"', get_the_tag_list( '', __( '&#8203;', 'waff' ) ) );
					if ( $tags_list ) {
						printf(
							/* translators: %s: list of tags. */
							'<div class="category-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Taggued', 'waff' ),
							$tags_list // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
					
				}
	


			}

		} else {

			//DEBUG
			echo ((true === WAFF_DEBUG)?'<code> #SINGLE</code>':'');		

			// FILM / IN SECTION ? 
			if ( 'film' == get_post_type() && has_term('','section') ) {
				/* translators: used between list items, there is a space after the comma. */
				/* $terms_list = preg_replace('/<a /', '<a class="section-item" style="background-color: #0000FF;"', get_the_term_list(
				get_the_ID(), 'section', 'BEFORE ', __( '&#8203;', 'waff' ) ) ); */
				//$terms = get_terms( array( 'taxonomy' => 'section' ) ); // Returns the complete list
				$terms = wp_get_object_terms( get_the_ID(),  'section' );
				if ( ! empty( $terms ) ) :
					if ( ! is_wp_error( $terms ) ) :
						$terms_list = array();
						foreach( $terms as $term ) {
						    $termcolor = get_term_meta( $term->term_id, 'wpcf-s-color', true );
							$selectedition 	= get_term_meta( $term->term_id, 'wpcf-select-edition', true ); // #41
							if ( $selectedition == $current_edition_id )
						    $terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
								(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.'"':''),
								esc_url(get_term_link($term)),
						        esc_html__($term->name),
						        esc_html__($term->name)
						    );
						}
					endif;
				endif;
				
				if ( $terms_list ) {
					printf(
						/* translators: %s: list of categories. */
						'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
						esc_html__( 'Categorized as', 'waff' ),
						implode($terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}
			}

			// FILM / SINGLE 
			if ( 'film' == get_post_type() ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #SINGLE FILM</code>':'');		
				
				// Render film movie types and categories
				if (function_exists('types_render_field')) :
					$film_type 				= types_render_field( 'f-movie-type', array() );
					$film_type 				= get_translated_movie_type(types_render_field( 'f-movie-type', array('output' =>'raw') ), $film_type); // Return an non zero-based index 
					$film_category 			= types_render_field( 'f-movie-category', array() );
					$film_category 			= get_translated_movie_category(types_render_field( 'f-movie-category', array('output' =>'raw') ), $film_category); // Return an non zero-based index 
					//https://toolset.com/forums/topic/can-types-works-with-qtranslate-x-httpswordpress-orgpluginsqtranslate-x/
				else: 
					$film_type 			= get_post_meta( $post->ID, 'wpcf-f-movie-type', true ); 
					$film_category 		= get_post_meta( $post->ID, 'wpcf-f-movie-category', true ); 
				endif;

				// Finally print
				if ( $film_type != '' ) printf('<div class="type-list d-inline"><a class="type-item link-disabled">%s</a></div>', sanitize_text_field($film_type) );
				if ( $film_category != '' ) printf('<div class="category-list d-inline"><a class="category-item link-disabled">%s</a></div>', sanitize_text_field($film_category) );
			
			}

			// JURY / SINGLE 
			if ( in_array(get_post_type(), array('jury','partenaire','projection')) ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #SINGLE JURY PARTNAIRE PROJECTION</code>':'');	


				if ( get_post_type() !== 'partenaire' ):
					$editions = wp_get_object_terms( get_the_ID(),  'edition' );
					$sections_args = array(
						'taxonomy'   => 'section',
						'parent'        => 0,
						'number'        => 1,				
						'hide_empty' => false,
						'meta_query' => array(
							array(
								'key'       => 'wpcf-select-edition',
								'value'     => $editions[0]->term_id,
								'compare'   => '='
							)
						)
					);
					$top_parent_terms = get_terms($sections_args);

					$terms_list_section_edition = array();
					foreach( $top_parent_terms as $term ) {
						$termcolor 		= get_term_meta( $term->term_id, 'wpcf-s-color', true );
						$selectedition 	= get_term_meta( $term->term_id, 'wpcf-select-edition', true ); // #41
						$terms_list_section_edition[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
							(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.'"':''),
							(($selectedition==$current_edition_id)?esc_url(get_term_link($term)):''),
							esc_html__($term->name),
							esc_html__($term->name),
						);
					}

					if ( $terms_list_section_edition ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							implode($terms_list_section_edition, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				endif;
		
				if ( get_post_type() === 'partenaire' ):
					/* translators: used between list items, there is a space after the comma. */
					/* edition */
					$terms_list_edition = array();
					$terms = wp_get_object_terms( get_the_ID(),  'edition' );
					if ( ! empty( $terms ) ) :
						if ( ! is_wp_error( $terms ) ) :
							$termcolor = 'var(--waff-color-dark)';
							foreach( $terms as $term ) {
								$terms_list_edition[] = sprintf('<a class="section-item" %s title="%s">%s</a>',
									(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.'"':''),
									esc_html__($term->name),
									esc_html__($term->name)
								);
							}
						endif;
					endif;
					if ( $terms_list_edition ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							implode($terms_list_edition, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				endif;
				
				if ( get_post_type() === 'jury' ):
					/* translators: used between list items, there is a space after the comma. */
					/* movie-type */
					$terms_list_movie_type = array();
					$terms = wp_get_object_terms( get_the_ID(),  'movie-type' );
					if ( ! empty( $terms ) ) :
						if ( ! is_wp_error( $terms ) ) :
							$termcolor = 'var(--waff-color-silver)';
							foreach( $terms as $term ) {
								$terms_list_movie_type[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
									(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.'"':''),
									esc_url(get_term_link($term)),
									esc_html__($term->name),
									esc_html__($term->name)
								);
							}
						endif;
					endif;
					if ( $terms_list_movie_type ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							implode($terms_list_movie_type, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				endif;
			
			}

			// POST
			if ( has_category() || has_tag() ) {

				//DEBUG
				echo ((true === WAFF_DEBUG)?'<code> #HAVECAT&TAGinaPOST?</code>':'');		
				
				/* translators: used between list items, there is a space after the comma. */
				$category_color = ( defined('WAFF_SECONDARY_COLOR') )?WAFF_SECONDARY_COLOR:'action-3';
				$categories_list = preg_replace('/<a /', '<a class="section-item" style="background-color:var(--waff-'.$category_color.');border-color:var(--waff-'.$category_color.');"', get_the_category_list( __( '&#8203;', 'waff' ) ) );
				//$categories_list = get_the_category_list( __( ', ', 'waff' ) );
				if ( $categories_list ) {
					printf(
						/* translators: %s: list of categories. */
						'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
						esc_html__( 'Categorized as', 'waff' ),
						$categories_list // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}
				
				/* translators: used between list items, there is a space after the comma. */
				$tags_list = preg_replace('/<a /', '<a class="category-item"', get_the_tag_list( '', __( '&#8203;', 'waff' ) ) );
				if ( $tags_list ) {
					printf(
						/* translators: %s: list of tags. */
						'<div class="category-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
						esc_html__( 'Taggued', 'waff' ),
						$tags_list // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}
			}
		}
	}
endif;

if ( ! function_exists( 'waff_entry_meta_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 * Footer entry meta is displayed differently in archives and single posts.
	 *
	 * @since Twenty Twenty-One 1.0
	 *
	 * @return void
	 */
	function waff_entry_meta_footer() {

		// Early exit if not a post.
		if ( 'post' !== get_post_type() ) {
			return;
		}

		// Hide meta information on pages.
		if ( ! is_single() ) {

			if ( is_sticky() ) {
				echo '<p>' . esc_html_x( 'Featured post', 'Label for sticky posts', 'waff' ) . '</p>';
			}

			$post_format = get_post_format();
			if ( 'aside' === $post_format || 'status' === $post_format ) {
				echo '<p><a href="' . esc_url( get_permalink() ) . '">' . waff_continue_reading_text() . '</a></p>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}

			// Posted on.
			waff_posted_on();

			// Edit post link.
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					esc_html__( 'Edit %s', 'waff' ),
					'<span class="screen-reader-text">' . get_the_title() . '</span>'
				),
				'<span class="edit-link">',
				'</span>'
			);

			if ( has_category() || has_tag() ) {

				echo '<div class="post-taxonomies">';

				/* translators: used between list items, there is a space after the comma. */
				$categories_list = get_the_category_list( __( ', ', 'waff' ) );
				if ( $categories_list ) {
					printf(
						/* translators: %s: list of categories. */
						'<span class="cat-links">' . esc_html__( 'Categorized as %s', 'waff' ) . ' </span>',
						$categories_list // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}

				/* translators: used between list items, there is a space after the comma. */
				$tags_list = get_the_tag_list( '', __( ', ', 'waff' ) );
				if ( $tags_list ) {
					printf(
						/* translators: %s: list of tags. */
						'<span class="tags-links">' . esc_html__( 'Tagged %s', 'waff' ) . '</span>',
						$tags_list // phpcs:ignore WordPress.Security.EscapeOutput
					);
				}
				echo '</div>';
			}
		} else {
		

			echo '<span class="posted-by">';
			// Posted by.
			waff_posted_by();
			// Posted on.
			waff_posted_on();
			// Edit post link.
			edit_post_link(
				sprintf(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					esc_html__( 'Edit %s', 'waff' ),
					'<span class="screen-reader-text">' . get_the_title() . '</span>'
				),
				'&nbsp;<span class="edit-link badge bg-action-3 link-white ms-2 bold">',
				'</span>'
			);
			echo '</span>';
			
			// Sticky
			echo '<span class="float-right">';
			echo waff_is_sticky();
			echo '</span>';

		}
	}
endif;
