<?php
/**
 * Customizer setup
 *
 * @package WaffTwo
 */

namespace WaffTwo\Customizer;

use function Go\get_palette_color;
use function Go\hex_to_hsl;
use function WaffTwo\Core\rgbToHsl;
use function WaffTwo\Core\waff_HTMLToRGB;

use WP_Customize_Upload_Control;

// require_once 'lib/ColorThief/ColorThief.php';
// use ColorThief\ColorThief as ColorThief;

require_once 'vendor/autoload.php';
use ColorThief\ColorThief;



/**
 * Set up Customizer hooks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};
	
	add_action( 'customize_preview_init', $n( 'customize_preview_init' ) );
	add_action( 'customize_controls_enqueue_scripts', $n( 'customize_preview_init' ) );
	
	add_action( 'customize_register', $n( 'register_waff_site_controls' ), 100 );

	// Child inline CSS
	add_action( 'wp_head', $n( 'waff_inline_css' ) );

}

/**
 * Enqueues the preview js for the customizer.
 *
 * @return void
 */
function customize_preview_init() {

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	wp_enqueue_script(
		'go-customize-preview',
		get_theme_file_uri( "dist/js/admin/child-customize-preview{$suffix}.js" ), //{$suffix}
		array( 'jquery', 'wp-autop' ),
		GO_VERSION,
		true
	);

	wp_localize_script(
		'go-customize-preview',
		'GoPreviewData',
		array(
			'design_styles' => \Go\Core\get_available_design_styles(),
		)
	);
}

/**
 * Add control to Go's existing Site Settings section.
 *
 * @param \WP_Customize_Manager $wp_customize The customize manager object.
 *
 * @return void
 */
function register_waff_site_controls( \WP_Customize_Manager $wp_customize ) {

	// Remove go social media to menu social
	$wp_customize->remove_section('go_social_media');

	// Add settings logotype 
	$wp_customize->add_setting(
		'svglogo_dark_url', 
		array(
			'transport'         => 'postMessage'
		)
	);

	$wp_customize->add_control(
		'svglogo_dark_url',
		array(
			'label'    => esc_html__( 'Logo dark url ( *.svg )', 'waff' ),
			'description' => esc_html__( 'E.g : /dist/images/*.svg', 'waff' ),
			'priority' => 80,
			'section'  => 'title_tagline',
			'settings' => 'svglogo_dark_url',
			'type'     => 'url',
		)
	);

	$wp_customize->add_setting(
		'svglogo_light_url', 
		array(
			'transport'         => 'postMessage'
		)
	);

	$wp_customize->add_control(
		'svglogo_light_url',
		array(
			'label'    => esc_html__( 'Logo light url ( *.svg )', 'waff' ),
			'description' => esc_html__( 'E.g : /dist/images/*.svg', 'waff' ),
			'priority' => 80,
			'section'  => 'title_tagline',
			'settings' => 'svglogo_light_url',
			'type'     => 'url',
		)
	);

	$wp_customize->add_setting(
		'svgsign_dark_url', 
		array(
			'transport'         => 'postMessage'
		)
	);

	$wp_customize->add_control(
		'svgsign_dark_url',
		array(
			'label'    => esc_html__( 'Sign dark url ( *.svg )', 'waff' ),
			'description' => esc_html__( 'E.g : /dist/images/*.svg', 'waff' ),
			'priority' => 80,
			'section'  => 'title_tagline',
			'settings' => 'svgsign_dark_url',
			'type'     => 'url',
		)
	);

	$wp_customize->add_setting(
		'svgsign_light_url', 
		array(
			'transport'         => 'postMessage'
		)
	);

	$wp_customize->add_control(
		'svgsign_light_url',
		array(
			'label'    => esc_html__( 'Sign light url ( *.svg )', 'waff' ),
			'description' => esc_html__( 'E.g : /dist/images/*.svg', 'waff' ),
			'priority' => 80,
			'section'  => 'title_tagline',
			'settings' => 'svgsign_light_url',
			'type'     => 'url',
		)
	);


	// $wp_customize->add_control( 
	// 	new WP_Customize_Upload_Control( 
	// 	$wp_customize, 
	// 	'logotype_dark', 
	// 	array(
	// 		'label'      => esc_html__( 'Main dark version logotype', 'waff' ),
	// 		'section'    => 'title_tagline',
	// 		'settings'   => 'logotype_dark',
	// 	) ) 
	// );

	// Add settings 
	$wp_customize->add_setting(
		'night_mode', array(
			'default'           => true,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'night_mode_control',
		array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Night Mode', 'waff' ),
			'description' => esc_html__( 'Enable for readers to view content easily while in the dark.', 'waff' ),
			'section'     => 'go_site_settings',
			'settings' 	  => 'night_mode'
		)
	);

	// Add settings 
	if ( defined('WAFF_THEME') && WAFF_THEME == 'FIFAM' ) :
		$wp_customize->add_setting(
			'mailchimp_popup', array(
				'default'           => false,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'absint',
			)
		);

		$wp_customize->add_control(
			'mailchimp_popup_control',
			array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Mailchimp popup', 'waff' ),
				'description' => esc_html__( 'Enable the Mailchimp script to add a newsletter popup.', 'waff' ),
				'section'     => 'go_site_settings',
				'settings' 	  => 'mailchimp_popup'
			)
		);
	endif;

	// Add settings 
	$wp_customize->add_setting(
		'author_meta', array(
			'default'           => true,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'author_meta_control',
		array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Show author name in single posts ?', 'waff' ),
			'description' => esc_html__( 'Show or hide author name in post metas.', 'waff' ),
			'section'     => 'go_site_settings',
			'settings' 	  => 'author_meta'
		)
	);

	// Add settings 
	$wp_customize->add_setting(
		'advanced_blocks', array(
			'default'           => false,
			'transport'         => 'postMessage',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		'advanced_blocks_control',
		array(
			'type'        => 'checkbox',
			'label'       => esc_html__( 'Advanced blocks', 'waff' ),
			'description' => esc_html__( 'Enable all the advanced blocks for gutenberg or a usefull selection.', 'waff' ),
			'section'     => 'go_site_settings',
			'settings' 	  => 'advanced_blocks'
		)
	);
	
	// Phone 
	$wp_customize->add_setting(
		'telephone',
		array(
			'default'           => '+33 (0)1 22 33 44 55',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'telephone_control',
		array(
			'label'    => esc_html__( 'Phone number', 'waff' ),
			'priority' => 80,
			'section'  => 'go_site_settings',
			'settings' => 'telephone',
			'type'     => 'text',
		)
	);

	// Email 
	$wp_customize->add_setting(
		'email',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'email_control',
		array(
			'label'    => esc_html__( 'Contact email', 'waff' ),
			'priority' => 80,
			'section'  => 'go_site_settings',
			'settings' => 'email',
			'type'     => 'text',
		)
	);

	// Site message 
	$wp_customize->add_setting(
		'site_message',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
		)
	);
	
	$wp_customize->add_control(
		'site_message_control',
		array(
			'description' => esc_html__( 'This will appear in the header at the top of the site.', 'waff' ),
			'label'    => esc_html__( 'Site message', 'waff' ),
			'priority' => 90,
			'section'  => 'go_site_settings',
			'settings' => 'site_message',
			'type'     => 'textarea',
		)
	);

	// Cie address
	$wp_customize->add_setting(
		'company_address',
		array(
			'default'           => '',
			'transport'         => 'postMessage',
		)
	);

	$wp_customize->add_control(
		'company_address_control',
		array(
			'description' => esc_html__( 'This will appear in the footer at the bottom of the site.', 'waff' ),
			'label'    => esc_html__( 'Company address', 'waff' ),
			'priority' => 100,
			'section'  => 'go_site_settings',
			'settings' => 'company_address',
			'type'     => 'textarea',
		)
	);

	// Privacy statement 
	$wp_customize->add_setting(
		'privacy_statement',
		array(
			'default'           => '',
			'transport'         => 'postMessage', //refresh
		)
	);

	$wp_customize->add_control(
		'personal_privacy_control',
		array(
			'description' => esc_html__( 'This will appear in the footer at the bottom of the site.', 'waff' ),
			'label'    => esc_html__( 'Privacy Statement', 'waff' ),
			'priority' => 110,
			'section'  => 'go_site_settings',
			'settings' => 'privacy_statement',
			'type'     => 'textarea',
		)
	);

	// Planning 
	if ( defined('WAFF_THEME') && (WAFF_THEME == 'FIFAM' || WAFF_THEME == 'DINARD') ) :
		$wp_customize->add_setting(
			'planning_url',
			array(
				'default'           => '',
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'planning_url_control',
			array(
				'description' => esc_html__( 'This will appear in the programmation modal.', 'waff' ),
				'label'    => esc_html__( 'Planning file URL (*.pdf)', 'waff' ),
				'priority' => 80,
				'section'  => 'go_site_settings',
				'settings' => 'planning_url',
				'type'     => 'url',
			)
		);

		// Booklet 
		$wp_customize->add_setting(
			'booklet_url',
			array(
				'default'           => '',
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'booklet_url_control',
			array(
				'description' => esc_html__( 'This will appear in the programmation modal.', 'waff' ),
				'label'    => esc_html__( 'Booklet file URL (*.pdf)', 'waff' ),
				'priority' => 80,
				'section'  => 'go_site_settings',
				'settings' => 'booklet_url',
				'type'     => 'url',
			)
		);

		// Catalog 
		$wp_customize->add_setting(
			'catalog_url',
			array(
				'default'           => '',
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			'catalog_url_control',
			array(
				'description' => esc_html__( 'This will appear in the programmation modal.', 'waff' ),
				'label'    => esc_html__( 'Catalog file URL (*.pdf)', 'waff' ),
				'priority' => 80,
				'section'  => 'go_site_settings',
				'settings' => 'catalog_url',
				'type'     => 'url',
			)
		);
	endif;
}

/**
 * Returns dominant color of an image via ColorThief class.
 *
 * @return void
 */
function waff_get_dominant_color($sourceImage) {

	echo ((true === WAFF_DEBUG)?'<code> #waff_get_dominant_color</code>':'');	

	if ( $sourceImage != '' ) {
		$dominantColor = ColorThief::getColor($sourceImage, 5); // $quality 1 to 10
		return $dominantColor;
	}

}

/**
 * Generates the inline CSS from the Customizer settings
 *
 * @return void
 */
function waff_inline_css() {
	global $post; 
	// Rgb regex
	// $re = '/rgb\(\s*((?:[0-2]?[0-9])?[0-9])\s*,\s*((?:[0-2]?[0-9])?[0-9])\s*,\s*((?:[0-2]?[0-9])?[0-9])\s*\)$/m';

	// Color palette.
	$primary_color    	= get_palette_color( 'primary', 'HSL' );
	// $primary_color_rgb_	= get_palette_color( 'primary', 'RGB' );
	// $primary_color_rgb_ = preg_match_all($re, $primary_color_rgb_, $primary_color_rgb, PREG_SET_ORDER, 0);
	// $primary_color_rgb 	= array_slice($primary_color_rgb[0], 1); // Not needed for the moment 

	$secondary_color  	= get_palette_color( 'secondary', 'HSL' );
	$tertiary_color   	= get_palette_color( 'tertiary', 'HSL' );

	// Additionnal color palette.
	$quarternary_color  = get_palette_color( 'quarternary', 'HSL' );
	$quinary_color  	= get_palette_color( 'quinary', 'HSL' );

	// WAFF Special.
	// Layout
	$font_size  						= (defined('WAFF_FONTSIZE'))?WAFF_FONTSIZE:'0.9rem'; 

	// Spacing
	$max_width  						= (defined('WAFF_MAX_WIDTH'))?WAFF_MAX_WIDTH:'50rem'; 
	$max_width_alignwide  				= (defined('WAFF_MAX_WIDTH_ALIGNWIDE'))?WAFF_MAX_WIDTH_ALIGNWIDE:'100rem'; 
	$layout_left_alignement  			= (defined('WAFF_LAYOUT_LEFT_ALIGNEMENT'))?WAFF_LAYOUT_LEFT_ALIGNEMENT:'0'; 
	$editor_title_block_alignement  	= (defined('WAFF_EDITOR_TITLE_BLOCK_ALIGNEMENT'))?WAFF_EDITOR_TITLE_BLOCK_ALIGNEMENT:'left'; 

	// Button fonts
	$button_font_size  					= (defined('WAFF_BUTTON_FONT_SIZE'))?WAFF_BUTTON_FONT_SIZE:'0.70rem'; 
	$button_font_weight  				= (defined('WAFF_BUTTON_FONT_WEIGHT'))?WAFF_BUTTON_FONT_WEIGHT:'600';
	$button_border_radius  				= (defined('WAFF_BUTTON_BORDER_RADIUS'))?WAFF_BUTTON_BORDER_RADIUS:'0';
	
	?>
		<style>
			:root {
				--waff: #0F0 !important;
				--waff--editor: #0F0 !important;

				/* Customizer > change waff action color */
				<?php if ( $primary_color ) : ?>
					--waff-action-1-h: <?php echo esc_attr( $primary_color[0] ) ?> !important;
					--waff-action-1-s: <?php echo esc_attr( $primary_color[1] ) ?>% !important;
					--waff-action-1-l: <?php echo esc_attr( $primary_color[2] ) ?>% !important;
				<?php endif; ?>
				<?php if ( $secondary_color ) : ?>
					--waff-action-2-h: <?php echo esc_attr( $secondary_color[0] ) ?> !important;
					--waff-action-2-s: <?php echo esc_attr( $secondary_color[1] ) ?>% !important;
					--waff-action-2-l: <?php echo esc_attr( $secondary_color[2] ) ?>% !important;
				<?php endif; ?>
				<?php if ( $tertiary_color ) : ?>
					--waff-action-3-h: <?php echo esc_attr( $tertiary_color[0] ) ?> !important;
					--waff-action-3-s: <?php echo esc_attr( $tertiary_color[1] ) ?>% !important;
					--waff-action-3-l: <?php echo esc_attr( $tertiary_color[2] ) ?>% !important;
				<?php endif; ?>

				/* If film as a color > if we have a film_id & a post_type */
				<?php if ( $post->post_type == 'film' && get_queried_object_id() != '' ) :
					$film_color = rwmb_meta( 'waff_film_color', array(), get_queried_object_id() );
					if ( $film_color != '' && $film_color != '#ffffff' && $film_color != '#000000' ) :
						$film_color_rgb = waff_HTMLToRGB($film_color, 'array');
						$film_color 	= hex_to_hsl($film_color);
				?> 
					/* Film have a color */
					--waff-action-1-h: <?php echo esc_attr( $film_color[0] ) ?> !important;
					--waff-action-1-s: <?php echo esc_attr( $film_color[1] ) ?>% !important;
					--waff-action-1-l: <?php echo esc_attr( $film_color[2] ) ?>% !important;
					--waff-action-1-r: <?php echo esc_attr( $film_color_rgb[0] ) ?> !important;
					--waff-action-1-g: <?php echo esc_attr( $film_color_rgb[1] ) ?> !important;
					--waff-action-1-b: <?php echo esc_attr( $film_color_rgb[2] ) ?> !important;
					<?php else:
						if ( defined('WAFF_USE_DOMINANT_FILM_COLOR') && true == WAFF_USE_DOMINANT_FILM_COLOR ) :
							if ( is_singular() && has_post_thumbnail() ) { 
								$featured_img_id     			= get_post_thumbnail_id( get_queried_object_id() );
								$featured_img_url 				= wp_get_attachment_image_src( $featured_img_id, "large" ); // OK
							}
							if ( !empty($featured_img_url) && $featured_img_url[0] != '' ) :
								// echo ( print_r($featured_img_url, true)); 
								$dominant_color = waff_get_dominant_color($featured_img_url[0]);
								// echo ( print_r($dominant_color, true)); 
								if ( !empty($dominant_color) ) :
									$film_color = rgbToHsl($dominant_color[0], $dominant_color[1], $dominant_color[2]);
									// echo ( print_r($film_color, true)); 
									?>
									/* Film haven't a color, we found dominant colors */
									--waff-action-1-h: <?php echo esc_attr( $film_color['h'] ) ?> !important;
									--waff-action-1-s: <?php echo esc_attr( $film_color['s'] ) ?>% !important;
									--waff-action-1-l: <?php echo esc_attr( $film_color['l'] ) ?>% !important;
									--waff-action-1-r: <?php echo esc_attr( $dominant_color[0] ) ?> !important;
									--waff-action-1-g: <?php echo esc_attr( $dominant_color[1] ) ?> !important;
									--waff-action-1-b: <?php echo esc_attr( $dominant_color[2] ) ?> !important;
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>
				<?php endif; ?>

				--go--color--white: hsl(0, 0%, 100%);
				--go--color--black: hsl(0, 0%, 0%);
				<?php if ( $quarternary_color ) : ?>
					--go--color--quarternary: hsl(<?php echo esc_attr( $quarternary_color[0] ) . ', ' . esc_attr( $quarternary_color[1] ) . '%, ' . esc_attr( $quarternary_color[2] ) . '%'; ?>);
				<?php endif; ?>
				<?php if ( $quinary_color ) : ?>
					--go--color--quinary: hsl(<?php echo esc_attr( $quinary_color[0] ) . ', ' . esc_attr( $quinary_color[1] ) . '%, ' . esc_attr( $quinary_color[2] ) . '%'; ?>);
				<?php endif; ?>
				<?php if ( $font_size ) : ?>
					--go--font-size: <?php echo esc_attr( $font_size ); ?> !important;
				<?php endif; ?>
				<?php if ( $max_width ) : ?>
					--go--max-width: <?php echo esc_attr( $max_width ); ?> !important;
					--stored--max-width: <?php echo esc_attr( $max_width ); ?> !important;
					--go-entryheader--max-width: <?php echo esc_attr( $max_width ); ?> !important;
					--stored-entryheader--max-width: <?php echo esc_attr( $max_width ); ?> !important;
				<?php endif; ?>
				<?php if ( $max_width_alignwide ) : ?>
					--go--max-width--alignwide: <?php echo esc_attr( $max_width_alignwide ); ?> !important;
					--stored--max-width--alignwide: <?php echo esc_attr( $max_width_alignwide ); ?> !important;
				<?php endif; ?>
				<?php if ( $layout_left_alignement ) : ?>
					--waff--layout--left-alignement: <?php echo esc_attr( $layout_left_alignement ); ?> !important;
				<?php endif; ?>
				<?php if ( $editor_title_block_alignement ) : ?>
					--editor-title-block--alignment: <?php echo esc_attr( $editor_title_block_alignement ); ?> !important;
				<?php endif; ?>
				<?php if ( $button_font_size ) : ?>
					--go-button--font-size: <?php echo esc_attr( $button_font_size ); ?> !important;
				<?php endif; ?>
				<?php if ( $button_font_weight ) : ?>
					--go-button--font-weight: <?php echo esc_attr( $button_font_weight ); ?> !important;
				<?php endif; ?>				
				<?php if ( $button_border_radius ) : ?>
					--go-button--border-radius: <?php echo esc_attr( $button_border_radius ); ?> !important;
				<?php endif; ?>				
				<?php if ( WAFF_FONTS ) : ?>
				<?php foreach ( WAFF_FONTS as $name => $font ) : ?>
					--<?php echo esc_attr( $name ); ?>: <?php echo $font; ?>;
				<?php endforeach; ?>
				<?php endif; ?>

			}
		</style>
	<?php
}