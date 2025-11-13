<?php
/**
 * Blocks setup and functions.
 * v2.0
 * @package WaffTwo\Blocks
 */

namespace WaffTwo\Blocks;

use function WaffTwo\Core\waff_do_markdown as waff_do_markdown;
use function WaffTwo\Core\waff_clean_alltags as waff_clean_alltags;
use function WaffTwo\Core\waff_trim as waff_trim;
use function WaffTwo\Theme\waff_get_edition_badge as waff_get_edition_badge;
use function WaffTwo\Core\waff_HTMLToRGB as waff_HTMLToRGB; 
use function WaffTwo\Core\waff_RGBToHSL as waff_RGBToHSL; 
use function WaffTwo\Core\waff_get_image_id_by_url as waff_get_image_id_by_url;
use function WaffTwo\waff_entry_meta_header as waff_entry_meta_header;

use function WaffTwo\Theme\waff_get_theme_homeslide_background as waff_get_theme_homeslide_background;
use function WaffTwo\Theme\waff_get_theme_homeslide_content as waff_get_theme_homeslide_content;

require_once get_theme_file_path( 'includes/blocks/block-programmation.php' );
require_once get_theme_file_path( 'includes/blocks/block-latest-posts.php' );
require_once get_theme_file_path( 'includes/blocks/block-partners.php' );
require_once get_theme_file_path( 'includes/blocks/block-edito.php' );
require_once get_theme_file_path( 'includes/blocks/block-awards.php' );
require_once get_theme_file_path( 'includes/blocks/block-playlist.php' );
require_once get_theme_file_path( 'includes/blocks/block-contact.php' );
require_once get_theme_file_path( 'includes/blocks/block-film.php' );
require_once get_theme_file_path( 'includes/blocks/block-section.php' );
require_once get_theme_file_path( 'includes/blocks/block-sections.php' );
require_once get_theme_file_path( 'includes/blocks/block-mission.php' );
require_once get_theme_file_path( 'includes/blocks/block-cols.php' );
require_once get_theme_file_path( 'includes/blocks/block-breaking.php' );
require_once get_theme_file_path( 'includes/blocks/block-insights.php' );
require_once get_theme_file_path( 'includes/blocks/block-keymessages.php' );

//use function Go\hex_to_rgb as hex_to_rgb; 

/**
 * Set up theme defaults and register supported WordPress features.
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	// Register Theme Blocks >> need rwmb_meta_boxes
	add_filter( 'rwmb_meta_boxes', $n( 'waff_blocks_register_meta_boxes' ));

	// Allow / Disallow some blocks 
	add_filter( 'allowed_block_types_all', $n( 'waff_allowed_block_types' ), 10, 2 ); // Php way
	// add_action( 'enqueue_block_editor_assets', $n( 'waff_reset_blocks_enqueue_block_editor_assets' )); // JS way 

	// Adds custom theme options to wp bootstrap blocks plugin
	add_action( 'enqueue_block_editor_assets', $n( 'waff_wp_boostrap_enqueue_block_editor_assets' ));

	// Adds a page option for some blocks in settings
	add_filter( 'mb_settings_pages', $n( 'waff_add_blocks_setting_page' ) );
	add_filter( 'rwmb_meta_boxes',  $n( 'waff_add_blocks_custom_fields_to_setting_page' ) );
	
}

/**
 * Setup options 
 */

 function waff_add_blocks_setting_page( $settings_pages ) {
	$settings_pages[] = [
		'menu_title' => __( 'Blocks', 'waff' ),
		'id'         => 'theme-blocks',
		'parent'     => 'options-general.php',
		'class'      => 'custom_css',
		'style'      => 'no-boxes',
		// 'message'    => __( 'Custom message', 'waff' ), // Saved custom message
		'customizer' => true,
		'icon_url'   => 'dashicons-admin-generic',
	];

	return $settings_pages;
}

function waff_add_blocks_custom_fields_to_setting_page( $meta_boxes ) {
	$prefix = 'waff_';

	$meta_boxes[] = [
		'id'             => 'theme-blocks-fields',
		'settings_pages' => ['theme-blocks'],
		'fields'         => [
			[
				'name'            => __( 'Blocks background', 'waff' ),
				'id'              => $prefix . 'blocks_background',
				'type'            => 'image_advanced',
			],
			[
				'name'            => __( 'Blocks pattern', 'waff' ),
				'id'              => $prefix . 'blocks_pattern',
				'type'            => 'image_advanced',
			],
			[
				'name'            => __( 'Blocks transition', 'waff' ),
				'id'              => $prefix . 'blocks_transition',
				'type'            => 'image_advanced',
			],
		],
	];

	return $meta_boxes;
}

function waff_get_blocks_background() {
	$prefix = 'waff_';
	return rwmb_meta( $prefix . 'blocks_background', [ 'size' => 'full', 'limit' => 1, 'object_type' => 'setting' ], 'theme-blocks' );
}

function waff_get_blocks_pattern() {
	$prefix = 'waff_';
	return rwmb_meta( $prefix . 'blocks_pattern', [ 'size' => 'full', 'limit' => 1, 'object_type' => 'setting' ], 'theme-blocks' );
}

function waff_get_blocks_transition() {
	$prefix = 'waff_';
	return rwmb_meta( $prefix . 'blocks_transition', [ 'size' => 'full', 'limit' => 1, 'object_type' => 'setting' ], 'theme-blocks' );
}
/**
 * Setup Blocks ( helped from MB Blocks php code )
 */

function waff_blocks_register_meta_boxes( $meta_boxes ) {
	$prefix = 'waff_';
	// global $current_edition_id; // Not working
	global $ccp_editions_filter; // Working 
	
	// WA Latest posts
    $meta_boxes[] = [
        'title'           => esc_html__( '(WA) Latest posts', 'waff' ),
        'id'              => 'wa-latest-posts',
        'fields'          => [
            [
                'id'   => $prefix . 'lp_title',
                'type' => 'text',
                'name' => esc_html__( 'Featured title', 'waff' ),
                // 'std'  => esc_html__( 'Breaking news', 'waff' ),
                'placeholder' => esc_html__( 'Breaking news', 'waff' ),
            ],
            [
                'id'   => $prefix . 'lp_subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Posts title', 'waff' ),
                // 'std'  => esc_html__( 'News', 'waff' ),
                'placeholder' => esc_html__( 'News', 'waff' ),
			],
			[
                'id'      => $prefix . 'lp_limit',
                'name'    => esc_html__( 'Limit posts to ?', 'waff' ),
                'type'    => 'radio',
                'desc'    => esc_html__( 'Choose the number of latest posts to show', 'waff' ),
                'std'     => 5,
                'options' => [
                    3 => esc_html__( '3', 'waff' ),
                    4 => esc_html__( '4', 'waff' ),
                    5 => esc_html__( '5', 'waff' ),
                    6 => esc_html__( '6', 'waff' ),
                ],
                'inline'  => 1,
			],
			// [
            //     'id'    => $prefix . 'lp_morelink',
            //     'type'  => 'switch',
            //     'name'  => esc_html__( 'Display more link ?', 'waff' ),
            //     'style' => 'rounded',
			// ],
			[
                'id'    => $prefix . 'lp_meta',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display meta infos ( posted by, posted at, catégories ... ) ?', 'waff' ),
                'style' => 'rounded',
			],
			[
                'id'		=> $prefix . 'lp_posttype',
                'name'		=> esc_html__( 'Select post type', 'waff' ),
                'type'		=> 'select',
                'desc'		=> esc_html__( 'Choose which post is the latest post. Default : posts', 'waff' ),
                'std'		=> 'post',
                //'placeholder'       => esc_html__( 'Placeholder', 'waff' ),
                'options'           => array_merge([
						'post' => esc_html__( 'post', 'waff' ),
						'page' => esc_html__( 'page', 'waff' )
					],
					( true === WAFF_ISFILM_VERSION ) ? [
						'film' => esc_html__( 'film', 'waff' ),
						'jury' => esc_html__( 'jury', 'waff' )
					] : [],
					( 'RSFP' === WAFF_THEME ) ? [
						'directory' 	=> esc_html__( 'directory', 'waff' ), //@TODO BLOCK Considering film or RSFP theme 
						'farm' 			=> esc_html__( 'farm', 'waff' ),
						'structure' 	=> esc_html__( 'structure', 'waff' ),
						'operation' 	=> esc_html__( 'operation', 'waff' ),
						'partner' 		=> esc_html__( 'partner', 'waff' )
					] : [],
				),
                'required'          => 1,
                'label_description' => esc_html__( 'Label', 'waff' ),
                //'before'            => 'html before',
                //'after'             => 'html after',
                //'class'             => 'Customcss',
                'key'               => 'value',
			],
			( 'RSFP' === WAFF_THEME ) ? [
                'id'    => $prefix . 'lp_containsvideo',
                'type'  => 'switch',
                'name'  => esc_html__( 'Filter only posts w/ a video', 'waff' ),
                'style' => 'rounded',
			] : [],
			[
                'id'         => $prefix . 'lp_categories',
                'type'       => 'taxonomy_advanced',
                'name'       => esc_html__( 'Select categories', 'waff' ),
                'taxonomy'   => 'category',
                'desc'       => esc_html__( 'Filter to those categories. If empty, all the categories will be showed. Only works with posts.', 'waff' ),
                'field_type' => 'checkbox_list',
				'multiple' => true,
				'select_all_none' => true,
				'query_args'  => array(
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				),
				'hidden' => array( $prefix . 'lp_posttype', '!=', 'post' ),
            ],	
            [
                'id'      => $prefix . 'lp_style',
                'name'    => esc_html__( 'Select style', 'waff' ),
                'type'    => 'select',
                'desc'    => esc_html__( 'Choose the style of the latest posts block', 'waff' ),
                'std'     => 'normal',
                'options' => [
                    'normal' 		=> esc_html__( 'Normal', 'waff' ),
					'classic' 		=> esc_html__( 'Classic', 'waff' ),
                    'magazine' 		=> esc_html__( 'Magazine', 'waff' ),
                    'bold' 			=> esc_html__( 'Bold', 'waff' ),
                    // 'directory' 	=> esc_html__( 'Directory list', 'waff' ), // TODO BLOCK Considering film or RSFP theme 
                ],
			],		
		],
        'category'        => 'layout',
		// 'icon'			  => 'format-standard',
        'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'format-standard',
		],
        'description'     => esc_html__( 'Display latest posts', 'waff' ),
        'keywords'        => ['latest', 'posts', 'blog', 'articles'],
        'supports'        => [
            'anchor'          => true,
            'customClassName' => true,
			'align'           => ['wide', 'full'],
        ],
        //'enqueue_style'  => 'customCSS',
        //'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_latest_posts_callback',
        'type'            => 'block',
		'context'         => 'side',
		//Special attrs
        //'key'       	  => 'value',
	];
	
	// WA Partners
	$partner_category 	= ( post_type_exists('partenaire') )?'partenaire-category':'partner-category'; // Depreciated WAFFTWO V1 
	//wp_die(var_dump(post_type_exists('partner'))); // Returns false 
	if( post_type_exists('partenaire') || post_type_exists('partner') || true === WAFF_HAS_PARTNERS_POSTTYPE )
	$meta_boxes[] = [
		'title'           => esc_html__( '(WA) Partners', 'waff' ),
		'id'              => 'wa-partners',
		'fields'          => [
			[
				'id'         => $prefix . 'pn_categories',
				'type'       => 'taxonomy_advanced',
				'name'       => esc_html__( 'Select categories', 'waff' ),
				'taxonomy'   => array($partner_category),
				'desc'       => esc_html__( 'Filter to those categories. If empty, none of the categories will be displayed.', 'waff' ),
				'field_type' => 'checkbox_list',
				'multiple' => true,
				'select_all_none' => true,
				'query_args'  => array(
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				),
				//'hidden' => array( $prefix . 'lp_posttype', '!=', 'post' ),
			],	
		
		],
		'category'        => 'layout',
		// 'icon'            => 'networking',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'networking',
		],
		'description'     => esc_html__( 'Display the current edition partners', 'waff' ),
		'keywords'        => ['partners', 'posts', 'partner', 'logotype'],
		'supports'        => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide'], // left, center, right, 
		],
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_partners_callback',
		'type'            => 'block',
		'context'         => 'side',
		//Special attrs
		//'key'       	  => 'value',
	];
	
	// WA Edito
	$__meta_box_edition = [
        'title'          => esc_html__( '(WA) Edito', 'waff' ),
        'id'             => 'wa-edito',
        'fields'         => [
            [
                'id'   => $prefix . 'e_title',
                'type' => 'text',
                'name' => esc_html__( 'Title', 'waff' ),
                // 'std'  => esc_html__( 'An awesome edition', 'waff' ),
                'placeholder' => esc_html__( 'An awesome edition', 'waff' ),
            ],
            [
                'id'   => $prefix . 'e_subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Subtitle', 'waff' ),
                'std'  => esc_html__( 'Edito', 'waff' ),
                //'placeholder' => esc_html__( 'An awesome edition', 'waff' ),
			],
			[
                'id'   => $prefix . 'e_leadcontent',
                'type' => 'textarea',
                'name' => esc_html__( 'Lead content', 'waff' ),
                'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
            ],
            [
                'id'   => $prefix . 'e_content',
                'type' => 'wysiwyg', //textarea
                'name' => esc_html__( 'Content', 'waff' ),
                'desc' => esc_html__( 'Markdown is available.', 'waff' ),
            ],
            [	
                'id'   => $prefix . 'e_image',
                'type' => 'image_advanced',
				'name' => esc_html__( 'Image', 'waff' ),
                'image_size'       => 'page-featured-image',
                'max_file_uploads' => 1,
            ],
            [
                'id'    => $prefix . 'e_fit',
                'type'  => 'switch',
                'name'  => esc_html__( 'Fit image size or limit block ?', 'waff' ),
                'style' => 'rounded',
			],
			// [
            //     'id'    => $prefix . 'e_editionbadge',
            //     'type'  => 'switch',
            //     'name'  => esc_html__( 'Display edition badge ?', 'waff' ),
            //     'style' => 'rounded',
            // ],
            [
                'id'    => $prefix . 'e_morelink',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display more link ?', 'waff' ),
                'style' => 'rounded',
            ],
            [
                'id'   => $prefix . 'e_moreurl',
                'type' => 'url',
                'name'  => esc_html__( 'More URL', 'waff' ),
                'desc'  => esc_html__( 'Fill an absolute link. Can be internal or external, e.g. : http://www.google.com', 'waff' ),
            ],
            [
                'id'    => $prefix . 'e_framed',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display framed version ?', 'waff' ),
                'desc'  => esc_html__( 'Adds a frame around the content.', 'waff' ),
                'style' => 'rounded',
			],
			[
                'id'    => $prefix . 'e_hide_center_column',
                'type'  => 'switch',
                'name'  => esc_html__( 'Hide center column ?', 'waff' ),
                'desc'  => esc_html__( 'Hide the empty column between the content and the image.', 'waff' ),
                'style' => 'rounded',
			],
			// Remove top / bottom margin class
			[
                'type' => 'heading',
                'name' => __( 'Block margins', 'waff' ),
			],
			[
				'id'    => $prefix . 'e_remove_topmargin',
				'type'  => 'switch',
				'name'  => esc_html__( 'Remove top margin ?', 'waff' ),
				'desc'  => esc_html__( 'Removes the top margin of the block.', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'    => $prefix . 'e_remove_bottommargin',
				'type'  => 'switch',
				'name'  => esc_html__( 'Remove bottom margin ?', 'waff' ),
				'desc'  => esc_html__( 'Removes the bottom margin of the block.', 'waff' ),
				'style' => 'rounded',
			],
		],
		'category'       => 'layout',
        // 'icon'           => 'format-quote',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'format-quote',
		],
        'description'     => esc_html__( 'Display edito bloc', 'waff' ),
        'keywords'       => ['edito', 'post', 'text'],
        'supports'       => [
            'anchor'          => true,
            'customClassName' => true,
            'align'           => ['wide', 'full'],
        ],
        //'render_code'    => '{{Twix}}',
        //'enqueue_style'  => 'customCSS',
        //'enqueue_script' => 'CustomJS',
        //'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_edito_callback',
        'type'           => 'block',
        'context'        => 'side',
        //'Keyattrs'       => 'Value',
	];
	// WA Edition conditionnal for FILMS version
	if( true === WAFF_ISFILM_VERSION )
		$__meta_box_edition['fields'][] = [
			'id'    => $prefix . 'e_editionbadge',
			'type'  => 'switch',
			'name'  => esc_html__( 'Display edition badge ?', 'waff' ),
			'style' => 'rounded',
		];
	$meta_boxes[] = $__meta_box_edition;

	// WA Contact page
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Contact', 'waff' ),
		'id'             => 'wa-contact',
		'fields'         => [
			// Gallery
            [
                'name'              => __( 'Contact block gallery', 'waff' ),
                'id'                => $prefix . 'c_gallery',
                'type'              => 'image_advanced',
                'label_description' => __( 'Choose the images', 'waff' ),
                'max_file_uploads'  => 4,
                'required'          => true,
                'image_size'        => 'post-featured-image',
			],
			// First line
			[
                'type' => 'heading',
                'name' => __( 'First block', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_first_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				'placeholder' => esc_html__( 'Get in touch !', 'waff' ),
			],
			[
                'id'   => $prefix . 'c_first_content',
                'type' => 'wysiwyg',
                'name' => esc_html__( 'Rich content', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_first_color_class',
				'type' => 'text',
				'name' => esc_html__( 'Class', 'waff' ),
				'std' => 'bg-action-2',
                'desc' => esc_html__( 'Fill the background color class ( or more ).', 'waff' ),
			],
			// Second line
			[
                'type' => 'heading',
                'name' => __( 'Second block', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_second_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				'placeholder' => esc_html__( 'Get in touch !', 'waff' ),
			],
			[
                'id'   => $prefix . 'c_second_content',
                'type' => 'wysiwyg',
                'name' => esc_html__( 'Rich content', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_second_color_class',
				'type' => 'text',
				'name' => esc_html__( 'Class', 'waff' ),
				'std' => 'bg-secondary',
                'desc' => esc_html__( 'Fill the background color class ( or more ).', 'waff' ),
			],
			// Form
			[
                'type' => 'heading',
                'name' => __( 'Contact form', 'waff' ),
			],
			// [
            //     'id' => $prefix . 'c_form',
            //     'type' => 'gform_select', >> TO DEBUG 
            //     'name' => esc_html__( 'Select contact form', 'waff' ),
			// ],
			[
				'id'   => $prefix . 'c_form',
				'type' => 'number',
				'name' => esc_html__( 'Contact GravityForm id', 'waff' ),
                'desc' => esc_html__( 'Fill the GravityForm form id.', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_ws_form',
				'type' => 'number',
				'name' => esc_html__( 'Contact WSform id', 'waff' ),
                'desc' => esc_html__( 'Fill the WSform form id.', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_form_color_class',
				'type' => 'text',
				'name' => esc_html__( 'Form class', 'waff' ),
				'std' => 'bg-secondary',
                'desc' => esc_html__( 'Fill the background color class ( or more ).', 'waff' ),
			],
			[
                'type' => 'heading',
                'name' => __( 'Style', 'waff' ),
			],
			[
                'id'    => $prefix . 'c_rounded',
                'type'  => 'switch',
                'name'  => esc_html__( 'Rounded elements ?', 'waff' ),
                'style' => 'rounded',
			],
		],
		'category'       => 'layout',
		// 'icon'           => 'text-page',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'text-page',
		],
		'description'     => esc_html__( 'Display edito bloc', 'waff' ),
		'keywords'       => ['contact', 'post', 'text'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_contact_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Playlist
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Playlist', 'waff' ),
		'id'             => 'wa-playlist',
		'fields'         => [
			[
				'id'   => $prefix . 'pl_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				'std'  => esc_html__( 'Title', 'waff' ),
				'placeholder' => esc_html__( 'Title', 'waff' ),
			],
			[
				'id'   => $prefix . 'pl_leadcontent',
				'type' => 'textarea',
				'name' => esc_html__( 'Lead content', 'waff' ),
				'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
			],
			[
				'id'   => $prefix . 'pl_content',
				'type' => 'textarea',
				'name' => esc_html__( 'Content', 'waff' ),
				'desc' => esc_html__( 'Markdown is available.', 'waff' ),
			],
			[
				'id'         => $prefix . 'pl_videos',
				'type'       => 'oembed',
				'name'       => __( 'Video(s)', 'waff' ),
				'std'        => 'https://youtu.be/...',
				'required'   => true,
				'clone'      => true,
				'sort_clone' => true,
				'max_clone'  => 99,
				'add_button' => __( 'Add more video to the playlist', 'waff' ),
			],
			[
				'id'    => $prefix . 'pl_playlist',
				'type' => 'text',
				'name'  => esc_html__( 'Fill youtube playlist ID here if you want to show it', 'waff' ),
			],
			[
				'id'    => $prefix . 'pl_autoplay',
				'type'  => 'switch',
				'name'  => esc_html__( 'Autoplay videos ?', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'    => $prefix . 'pl_fullwidth',
				'type'  => 'switch',
				'name'  => esc_html__( 'Display fullwidth videos ?', 'waff' ),
				'desc' => esc_html__( 'Diplayed in thumbnails by default.', 'waff' ),
				'style' => 'rounded',
			],
		],
		'category'       => 'layout',
		// 'icon'           => 'video-alt3',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'video-alt3',
		],
		'description'     => esc_html__( 'Display a video playlist block ( better with youtube )', 'waff' ),
		'keywords'       => ['video', 'youtube', 'playlist', 'film'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_playlist_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Awards
	if( true === WAFF_ISFILM_VERSION )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Awards', 'waff' ),
		'id'             => 'wa-awards',
		'fields'         => [
			[
				'id'   => $prefix . 'a_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				'std'  => esc_html__( 'Awards', 'waff' ),
				'placeholder' => esc_html__( 'Title', 'waff' ),
			],
			[
				'id'   => $prefix . 'a_leadcontent',
				'type' => 'textarea',
				'name' => esc_html__( 'Lead content', 'waff' ),
				'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
			],
			[
				'id'   => $prefix . 'a_content',
				'type' => 'textarea',
				'name' => esc_html__( 'Content', 'waff' ),
				'desc' => esc_html__( 'Markdown is available.', 'waff' ),
			],
			[
				'id'         => $prefix . 'a_edition',
				'type'       => 'taxonomy_advanced',
				'name'       => esc_html__( 'Select edition', 'waff' ),
				'taxonomy'   => 'edition',
				'desc'       => esc_html__( 'Filter awards by edition. If empty, none of the editions will be displayed.', 'waff' ),
				'field_type' => 'radio_list',
				'multiple' => false,
				'select_all_none' => false,
				'query_args'  => array(
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				),
				//'hidden' => array( 'waff_lp_posttype', '!=', 'post' ),
			],
			[
				'id'      => $prefix . 'a_display',
				'name'    => esc_html__( 'Choose display', 'waff' ),
				'type'    => 'select',
				'desc'    => esc_html__( 'Choose to display all the awards or selected awards', 'waff' ),
				'std'     => 5,
				'options' => [
					0 => esc_html__( 'All', 'waff' ),
					1 => esc_html__( 'Master awards only', 'waff' ),
					2 => esc_html__( 'Standard awards only', 'waff' ),
				],
				'inline'  => 1,
			],
			[
				'id'    => $prefix . 'a_morelink',
				'type'  => 'switch',
				'name'  => esc_html__( 'Display more link ?', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'   => $prefix . 'a_moreurl',
				'type' => 'url',
				'name'  => esc_html__( 'More URL', 'waff' ),
				'hidden' => array( $prefix . 'a_morelink', '!=', 1 ),
			],
		],
		'category'       => 'layout',
		// 'icon'           => 'admin-site',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'admin-site',
		],
		'description'     => esc_html__( 'Display film awards block', 'waff' ),
		'keywords'       => ['wa', 'awards', 'film', 'post', 'text', 'winning'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_awards_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Film
	if( true === WAFF_ISFILM_VERSION )
	$meta_boxes[] = [
		'title'           => esc_html__( '(WA) Film', 'waff' ),
		'id'              => 'wa-film',
		'fields'          => [
            [
                'name'       => esc_html__( 'Select a film', 'waff' ),
				'desc'       => __( 'Choose a film to display a single film card. If empty, none of the categories will be displayed.', 'waff' ),
                'id'         => $prefix . 'sf_film',
                'type'       => 'post',
                'post_type'  => ['film'],
                'field_type' => 'select_advanced',
            ],
			[
				'id'    => $prefix . 'sf_promotted',
				'type'  => 'switch',
				'name'  => esc_html__( 'Show as promotted film ?', 'waff' ),
				'style' => 'rounded',
			],
		],
		'category'        => 'layout',
		// 'icon'            => 'video-alt',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'video-alt',
		],
		'description'     => esc_html__( 'Display a single film', 'waff' ),
		'keywords'        => ['film', 'posts', 'single'],
		'supports'        => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide'], // left, center, right, 
		],
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_film_callback',
		'type'            => 'block',
		'context'         => 'side',
		//Special attrs
		//'key'       	  => 'value',
	];

	// WA Section
	if( true === WAFF_ISFILM_VERSION )
	$meta_boxes[] = [
		'title'           => esc_html__( '(WA) Section', 'waff' ),
		'id'              => 'wa-section',
		'fields'          => [
            [
                'id'   => $prefix . 'ss_title',
                'type' => 'text',
                'name' => esc_html__( 'Featured section title', 'waff' ),
                // 'std'  => esc_html__( 'Breaking news', 'waff' ),
                'placeholder' => esc_html__( 'Break into the competition', 'waff' ),
            ],
			[
				'id'         => $prefix . 'ss_section',
				'type'       => 'taxonomy_advanced',
				'name'       => esc_html__( 'Select a section', 'waff' ),
				'desc'       => __( 'Choose a section to display a slideshow. If empty, none of the categories will be displayed. <em>Current edition : '.(( $ccp_editions_filter != null )?$ccp_editions_filter->get_current_edition():'None').'</em>', 'waff' ),
				'taxonomy'   => 'section',
				'field_type' => 'checkbox_list',
				'multiple' => false,
				'select_all_none' => true,
				'query_args'  => array(
					'meta_query' => array(
						'relation' => 'and',
						array(
							'key' => 'wpcf-select-edition',
							'value' => (( $ccp_editions_filter != null )?$ccp_editions_filter->get_current_edition_id():0),
							'compare' => '=',
							'type' => 'numeric',
						),
					),
				),
				//'hidden' => array( $prefix . 'lp_posttype', '!=', 'post' ),
			],
			[
				'id'    => $prefix . 'ss_section_color',
				'type'  => 'switch',
				'name'  => esc_html__( 'Use section color ?', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'    => $prefix . 'ss_showonly_when_edition_is_online',
				'type'  => 'switch',
				'name'  => esc_html__( 'Show only when edition is online ?', 'waff' ),
				'style' => 'rounded',
			],
		],
		'category'        => 'layout',
		// 'icon'            => 'images-alt',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'images-alt',
		],
		'description'     => esc_html__( 'Display a single section slideshow', 'waff' ),
		'keywords'        => ['film', 'section', 'category'],
		'supports'        => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_section_callback',
		'type'            => 'block',
		'context'         => 'side',
		//Special attrs
		//'key'       	  => 'value',
	];

	// WA Sections list
	if( true === WAFF_ISFILM_VERSION )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Sections list', 'waff' ),
		'id'             => 'wa-sections',
		'fields'         => [
			[
				'id'   => $prefix . 'sl_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				'std'  => esc_html__( 'Sections', 'waff' ),
				'placeholder' => esc_html__( 'Title', 'waff' ),
			],
			[
				'id'   => $prefix . 'sl_leadcontent',
				'type' => 'textarea',
				'name' => esc_html__( 'Lead content', 'waff' ),
				'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
			],
			[
				'id'   => $prefix . 'sl_content',
				'type' => 'textarea',
				'name' => esc_html__( 'Content', 'waff' ),
				'desc' => esc_html__( 'Markdown is available.', 'waff' ),
			],
			[
				'id'         => $prefix . 'sl_edition',
				'type'       => 'taxonomy_advanced',
				'name'       => esc_html__( 'Select edition', 'waff' ),
				'taxonomy'   => 'edition',
				'desc'       => esc_html__( 'Filter sections by edition. If empty, none of the sections will be displayed.', 'waff' ),
				'field_type' => 'radio_list',
				'multiple' => false,
				'select_all_none' => false,
				'query_args'  => array(
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				),
				//'hidden' => array( 'waff_lp_posttype', '!=', 'post' ),
			],
			[
                'id'    => $prefix . 'sl_show_introduction',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display introduction content ?', 'waff' ),
                'style' => 'rounded',
			],
			[
                'id'    => $prefix . 'sl_show_parent_section',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display parent edition section of sections ?', 'waff' ),
                'style' => 'rounded',
			],
			[
                'id'    => $prefix . 'sl_show_tiny_list',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display as a tiny list?', 'waff' ),
                'style' => 'rounded',
			],
		],
		'category'       => 'layout',
		// 'icon'           => 'list-view',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'list-view',
		],
		'description'     => esc_html__( 'Display a list of sections filtered by edition', 'waff' ),
		'keywords'       => ['wa', 'sections', 'film', 'winning'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_sections_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Programmation
	if( true === WAFF_ISFILM_VERSION )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Programmation', 'waff' ),
		'id'             => 'wa-programmation',
		'fields'         => [
			[
				'id'   => $prefix . 'p_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				'std'  => esc_html__( 'Programmation', 'waff' ),
				'placeholder' => esc_html__( 'Title', 'waff' ),
			],
			[
				'id'   => $prefix . 'p_leadcontent',
				'type' => 'textarea',
				'name' => esc_html__( 'Lead content', 'waff' ),
				'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
			],
			[
				'id'   => $prefix . 'p_content',
				'type' => 'textarea',
				'name' => esc_html__( 'Content', 'waff' ),
				'desc' => esc_html__( 'Markdown is available.', 'waff' ),
			],
			[
				'id'         => $prefix . 'p_edition',
				'type'       => 'taxonomy_advanced',
				'name'       => esc_html__( 'Select edition', 'waff' ),
				'taxonomy'   => 'edition',
				'desc'       => esc_html__( 'Filter sections by edition. If empty, none of the sections will be displayed.', 'waff' ),
				'field_type' => 'radio_list',
				'multiple' => false,
				'select_all_none' => false,
				'query_args'  => array(
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
				),
				//'hidden' => array( 'waff_lp_posttype', '!=', 'post' ),
			],
			[
                'id'    => $prefix . 'p_show_gazette',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display Gazette mode ?', 'waff' ),
                'style' => 'rounded',
			],
			[
				'id'    => $prefix . 'p_showonly_when_edition_is_online',
				'type'  => 'switch',
				'name'  => esc_html__( 'Show only when edition is online ?', 'waff' ),
				'style' => 'rounded',
			],
			// [
            //     'id'    => $prefix . 'p_show_introduction',
            //     'type'  => 'switch',
            //     'name'  => esc_html__( 'Display introduction content ?', 'waff' ),
            //     'style' => 'rounded',
			// ],
			// [
            //     'id'    => $prefix . 'p_show_parent_section',
            //     'type'  => 'switch',
            //     'name'  => esc_html__( 'Display parent edition section of sections ?', 'waff' ),
            //     'style' => 'rounded',
			// ],
			// [
            //     'id'    => $prefix . 'p_show_tiny_list',
            //     'type'  => 'switch',
            //     'name'  => esc_html__( 'Display as a tiny list?', 'waff' ),
            //     'style' => 'rounded',
			// ],
		],
		'category'       => 'layout',
		// 'icon'           => 'list-view',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'calendar-alt',
		],
		'description'     => esc_html__( 'Display the programmation filtered by edition', 'waff' ),
		'keywords'       => ['wa', 'planning', 'programmation', 'film'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_programmation_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Mission ( #RSFP )
	$meta_boxes[] = [
        'title'          => esc_html__( '(WA) Mission', 'waff' ),
        'id'             => 'wa-misson',
        'fields'         => [
            [
                'id'   => $prefix . 'm_title',
                'type' => 'text',
                'name' => esc_html__( 'Title', 'waff' ),
                // 'std'  => esc_html__( 'An awesome edition', 'waff' ),
                'placeholder' => esc_html__( 'An awesome title', 'waff' ),
            ],
            [
                'id'   => $prefix . 'm_subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Subtitle', 'waff' ),
                // 'std'  => esc_html__( 'Edito', 'waff' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'waff' ),
			],
			[
				'id'   => $prefix . 'm_subtitle_class',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle class', 'waff' ),
				'std' => 'text-action-1',
                'desc' => esc_html__( 'Fill the subtitle class.', 'waff' ),
			],
			[
                'id'   => $prefix . 'm_leadcontent',
                'type' => 'textarea',
                'name' => esc_html__( 'Lead content', 'waff' ),
                'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
            ],
            [
                'id'   => $prefix . 'm_content',
                'type' => 'wysiwyg', //textarea
                'name' => esc_html__( 'Content', 'waff' ),
                'desc' => esc_html__( 'Markdown is available.', 'waff' ),
            ],
			[
                'id'                => $prefix . 'm_lists',
                'type'              => 'text_list',
                'name'              => __( 'List.s', 'waff' ),
                'label_description' => __( '<span class="label">INFO</span> Fill to create a list of items.', 'wa-rsfp' ),
                'options'           => [
                    'Label'       	=> 'Label',
                    'Description' 	=> 'Description',
                    'Icon'       	=> 'Fill here an css icon',
                    // 'Value'       	=> 'Value',
                ],
                'clone'             => true,
                'sort_clone'        => true,
                'max_clone'         => 100,
                'desc'				=> __( '<br/><span class="label">TIPS</span> You can use Boostrap icons, ex : bi bi-bookmark ( https://icons.getbootstrap.com ).', 'waff' ),
            ],
            [	
                'id'   				=> $prefix . 'm_image',
                'type' 				=> 'image_advanced',
				'name' 				=> esc_html__( 'Image', 'waff' ),
                'image_size'       	=> 'page-featured-image',
                'max_file_uploads' 	=> 1,
                'required'         	=> 1,
            ],
            [
                'id'    			=> $prefix . 'm_alignment',
				'name'				=> esc_html__( 'Select an alignment', 'waff' ),
                'type'				=> 'select',
                'desc'				=> esc_html__( 'Choose the aligment beetween background and image.', 'waff' ),
                'std'				=> 'post',
                'options'           => [
                    'aligned' => esc_html__( 'Aligned', 'waff' ),
                    'shifted' => esc_html__( 'Shifted', 'waff' ),
                ],
                // 'required'       => 1,
                'key'               => 'value',
			],	
			[
                'id'		=> $prefix . 'm_position',
                'name'		=> esc_html__( 'Select a position', 'waff' ),
                'type'		=> 'select',
                'desc'		=> esc_html__( 'Choose image position.', 'waff' ),
                'std'		=> 'post',
                'options'           => [
                    'top' 		=> esc_html__( 'Top', 'waff' ),
                    'center' 	=> esc_html__( 'Centered', 'waff' ),
                    'bottom' 	=> esc_html__( 'Bottom', 'waff' ),
                ],
                // 'required'          => 1,
                'key'               => 'value',
			],
			[
				'id'   => $prefix . 'm_bg_color',
				'type' => 'text',
				'name' => esc_html__( 'Override background color', 'waff' ),
				'std' => '',
                'desc' => esc_html__( 'Fill a color class.', 'waff' ),
			],
            [
                'id'    => $prefix . 'm_morelink',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display more link ?', 'waff' ),
                'style' => 'rounded',
            ],
            [
                'id'   => $prefix . 'm_moreurl',
                'type' => 'url',
                'name'  => esc_html__( 'More URL', 'waff' ),
                'desc'  => esc_html__( 'Fill an absolute link. Can be internal or external, e.g. : http://www.google.com', 'waff' ),
            ],
			// Block margin
			// Remove top / bottom margin class
			[
                'type' => 'heading',
                'name' => __( 'Block margins', 'waff' ),
			],
			[
                'id'    => $prefix . 'm_blockmargin',
				'name'  => esc_html__( 'Block have margin ?', 'waff' ),
                'desc'  => esc_html__( 'Removes both top & bottom block margin.', 'waff' ),
                'type'  => 'switch',
                'style' => 'rounded',
                'std'   => true,
            ],
		],
		'category'       => 'layout',
        // 'icon'           => 'format-quote',
		'icon'            => [
            'foreground' 	=> '#9500ff',
			'src' 			=> 'align-pull-left',
		],
        'description'     => esc_html__( 'Display mission bloc', 'waff' ),
        'keywords'       => ['hero', 'content', 'text', 'mission', 'bloc'],
        'supports'       => [
            'anchor'          => true,
            'customClassName' => true,
            'align'           => ['wide', 'full'],
        ],
        //'render_code'    => '{{Twix}}',
        //'enqueue_style'  => 'customCSS',
        //'enqueue_script' => 'CustomJS',
        //'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_mission_callback',
        'type'           => 'block',
        'context'        => 'side',
        //'Keyattrs'       => 'Value',
	];

	// WA Cols ( #RSFP )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Cols', 'waff' ),
		'id'             => 'wa-cols',
		'fields'         => [
			[
				'id'   => $prefix . 'c_title',
				'type' => 'text',
				'name' => esc_html__( 'Title', 'waff' ),
				// 'std'  => esc_html__( 'An awesome edition', 'waff' ),
				'placeholder' => esc_html__( 'An awesome title', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_subtitle',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle', 'waff' ),
				// 'std'  => esc_html__( 'Edito', 'waff' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_subtitle_class',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle class', 'waff' ),
				'std' => 'text-action-3',
                'desc' => esc_html__( 'Fill the subtitle class. Ex: text-action-1, text-action-2, text-action-3, text-dark, text-light', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_leadcontent',
				'type' => 'textarea',
				'name' => esc_html__( 'Lead content', 'waff' ),
				'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
			],
			[
				'id'   => $prefix . 'c_contents',
				'type' => 'wysiwyg', //textarea
				'name' => esc_html__( 'Content', 'waff' ),
				'desc' => esc_html__( 'Content will be displayed as cols. Markdown is available.', 'waff' ),
				'clone'             => true,
				'sort_clone'        => true,
				'max_clone'         => 4,
			],
			[
				'type' => 'divider',
			],
			[	
				'id'   => $prefix . 'c_image',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Image', 'waff' ),
				'image_size'       => 'page-featured-image',
				'max_file_uploads' => 1,
			],
			[
				'id'   => $prefix . 'c_gradient_color_class',
				'type' => 'text',
				'name' => esc_html__( 'Gradient color (class)', 'waff' ),
				'std' => 'action-2',
                'desc' => esc_html__( 'Fill the gradient color class. Ex: action-1, action-2, action-3, dark, light', 'waff' ),
			],
			[
                'id'		=> $prefix . 'c_image_style',
                'name'		=> esc_html__( 'Select an image overlay style', 'waff' ),
                'type'		=> 'select',
                'desc'		=> esc_html__( 'Choose image position, overlay and style.', 'waff' ),
                'std'		=> 'full',
                'options'           => [
                    'full' 		=> esc_html__( 'Full w/ gradient', 'waff' ),
                    'masked' 	=> esc_html__( 'Masked w/ blocks pattern', 'waff' ),
                    'no' 		=> esc_html__( 'No overlay', 'waff' ),
                ],
                // 'required'          => 1,
                'key'               => 'value',
			],
			[
				'type' => 'divider',
			],
			[
				'id'    => $prefix . 'c_morelink',
				'type'  => 'switch',
				'name'  => esc_html__( 'Display more link ?', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'   => $prefix . 'c_moreurl',
				'type' => 'url',
				'name'  => esc_html__( 'More URL', 'waff' ),
				'desc'  => esc_html__( 'Fill an absolute link. Can be internal or external, e.g. : http://www.google.com', 'waff' ),
			],
			// Block margin
			// Remove top / bottom margin class
			[
                'type' => 'heading',
                'name' => __( 'Block margins', 'waff' ),
			],
			[
                'id'    => $prefix . 'c_blockmargin',
				'name'  => esc_html__( 'Block have margin ?', 'waff' ),
                'desc'  => esc_html__( 'Removes both top & bottom block margin.', 'waff' ),
                'type'  => 'switch',
                'style' => 'rounded',
                'std'   => true,
            ],

		],
		'category'       => 'layout',
		// 'icon'           => 'format-quote',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'table-row-before',
		],
		'description'     => esc_html__( 'Display text bloc with cols', 'waff' ),
		'keywords'       => ['hero', 'content', 'text', 'columns', 'bloc'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_cols_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Breaking ( #RSFP )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Breaking', 'waff' ),
		'id'             => 'wa-breaking',
		'fields'         => [
			[
				'id'   => $prefix . 'b_label_1',
				'type' => 'text',
				'name' => esc_html__( 'Label (first)', 'waff' ),
				// 'std'  => esc_html__( 'An awesome edition', 'waff' ),
				'placeholder' => esc_html__( 'A top label', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_label_1_class',
				'type' => 'text',
				'name' => esc_html__( 'Label class (first)', 'waff' ),
				'std' => 'text-light',
                'desc' => esc_html__( 'Fill the subtitle class. Ex: text-action-1, text-action-2, text-action-3, text-dark, text-light', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_title_1',
				'type' => 'text',
				'name' => esc_html__( 'Title (first)', 'waff' ),
				// 'std'  => esc_html__( 'An awesome edition', 'waff' ),
				'placeholder' => esc_html__( 'An awesome title', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_subtitle_1',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle (first)', 'waff' ),
				// 'std'  => esc_html__( 'Edito', 'waff' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_subtitle_1_class',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle class (first)', 'waff' ),
				'std' => 'text-light',
                'desc' => esc_html__( 'Fill the subtitle class. Ex: text-action-1, text-action-2, text-action-3, text-dark, text-light', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_content_1',
				'type' => 'wysiwyg', //textarea
				'name' => esc_html__( 'Content (first)', 'waff' ),
				'desc' => esc_html__( 'Content will be displayed as cols. Markdown is available.', 'waff' ),
			],
			[	
				'id'   => $prefix . 'b_image_1',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Image (first)', 'waff' ),
				'image_size'       => 'page-featured-image',
				'max_file_uploads' => 1,
			],
			[
				'id'    => $prefix . 'b_morelink_1',
				'type'  => 'switch',
				'name'  => esc_html__( 'Display more link ? (first)', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'   => $prefix . 'b_moreurl_1',
				'type' => 'url',
				'name'  => esc_html__( 'More URL (first)', 'waff' ),
				'desc'  => esc_html__( 'Fill an absolute link. Can be internal or external, e.g. : http://www.google.com', 'waff' ),
			],
			[
				'type' => 'divider',
			],
			[
				'id'   => $prefix . 'b_label_2',
				'type' => 'text',
				'name' => esc_html__( 'Label (last)', 'waff' ),
				// 'std'  => esc_html__( 'An awesome edition', 'waff' ),
				'placeholder' => esc_html__( 'A top label', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_label_2_class',
				'type' => 'text',
				'name' => esc_html__( 'Label class (last)', 'waff' ),
				'std' => 'text-action-1',
                'desc' => esc_html__( 'Fill the subtitle class. Ex: text-action-1, text-action-2, text-action-3, text-dark, text-light', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_title_2',
				'type' => 'text',
				'name' => esc_html__( 'Title (last)', 'waff' ),
				// 'std'  => esc_html__( 'An awesome edition', 'waff' ),
				'placeholder' => esc_html__( 'An awesome title', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_subtitle_2',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle (last)', 'waff' ),
				// 'std'  => esc_html__( 'Edito', 'waff' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_subtitle_2_class',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle class (last)', 'waff' ),
				'std' => 'text-action-1',
                'desc' => esc_html__( 'Fill the subtitle class. Ex: text-action-1, text-action-2, text-action-3, text-dark, text-light', 'waff' ),
			],
			[
				'id'   => $prefix . 'b_content_2',
				'type' => 'wysiwyg', //textarea
				'name' => esc_html__( 'Content (last)', 'waff' ),
				'desc' => esc_html__( 'Content will be displayed as cols. Markdown is available.', 'waff' ),
			],
			[	
				'id'   => $prefix . 'b_image_2',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Image (last)', 'waff' ),
				'image_size'       => 'page-featured-image',
				'max_file_uploads' => 1,
			],
			[
				'id'    => $prefix . 'b_morelink_2',
				'type'  => 'switch',
				'name'  => esc_html__( 'Display more link ? (last)', 'waff' ),
				'style' => 'rounded',
			],
			[
				'id'   => $prefix . 'b_moreurl_2',
				'type' => 'url',
				'name'  => esc_html__( 'More URL (last)', 'waff' ),
				'desc'  => esc_html__( 'Fill an absolute link. Can be internal or external, e.g. : http://www.google.com', 'waff' ),
			],
			[
				'type' => 'divider',
			],
			[
				'id'   => $prefix . 'b_gradient_color_class',
				'type' => 'text',
				'name' => esc_html__( 'Gradient color (class)', 'waff' ),
				'std' => 'action-2',
                'desc' => esc_html__( 'Fill the gradient color class. Ex: action-1, action-2, action-3, dark, light', 'waff' ),
			],
			[
                'id'		=> $prefix . 'b_gradient_style',
                'name'		=> esc_html__( 'Gradient style', 'waff' ),
                'type'		=> 'select',
                'desc'		=> esc_html__( 'Choose a gradient overlay style over images.', 'waff' ),
                'std'		=> 'inverse',
                'options'           => [
					'vertical' 		=> esc_html__( 'Vertical gradient', 'waff' ),
                    'inverse' 		=> esc_html__( 'Inverse gradient', 'waff' ),
                    'plain' 		=> esc_html__( 'Plain gradient', 'waff' ),
                    'smooth' 		=> esc_html__( 'Smooth gradient', 'waff' ),
                    'horizontal' 	=> esc_html__( 'Horizontal gradient', 'waff' ),
                    'transparent' 	=> esc_html__( 'Transparent overlay', 'waff' ),
                    'no' 			=> esc_html__( 'No overlay', 'waff' ),
                ],
                // 'required'          => 1,
                'key'               => 'value',
			],
			// Block margin
			// Remove top / bottom margin class
			[
                'type' => 'heading',
                'name' => __( 'Block margins', 'waff' ),
			],
			[
                'id'    => $prefix . 'b_blockmargin',
				'name'  => esc_html__( 'Block have margin ?', 'waff' ),
                'desc'  => esc_html__( 'Removes both top & bottom block margin.', 'waff' ),
                'type'  => 'switch',
                'style' => 'rounded',
                'std'   => true,
            ],			
		],
		'category'       => 'layout',
		// 'icon'           => 'format-quote',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'format-status',
		],
		'description'     => esc_html__( 'Display breaking bloc with two cols', 'waff' ),
		'keywords'       => ['hero', 'content', 'text', 'breaking', 'news', 'bloc'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_breaking_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

	// WA Insights ( #RSFP )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Insights', 'waff' ),
		'id'             => 'wa-insights',
		'fields'         => [
			[
                'id'   => $prefix . 'i_title',
                'type' => 'text',
                'name' => esc_html__( 'Title', 'waff' ),
                // 'std'  => esc_html__( 'An awesome edition', 'waff' ),
                'placeholder' => esc_html__( 'An awesome title', 'waff' ),
            ],
            [
                'id'   => $prefix . 'i_subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Subtitle', 'waff' ),
                // 'std'  => esc_html__( 'Edito', 'waff' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'waff' ),
			],
			[
				'id'   => $prefix . 'i_subtitle_class',
				'type' => 'text',
				'name' => esc_html__( 'Subtitle class', 'waff' ),
				'std' => 'text-action-1',
                'desc' => esc_html__( 'Fill the subtitle class.', 'waff' ),
			],
			[
                'id'   => $prefix . 'i_leadcontent',
                'type' => 'textarea',
                'name' => esc_html__( 'Lead content', 'waff' ),
                'desc' => esc_html__( 'Displayed in a bigger size. Markdown is available.', 'waff' ),
            ],
            // [
            //     'id'   => $prefix . 'i_content',
            //     'type' => 'wysiwyg', //textarea
            //     'name' => esc_html__( 'Content', 'waff' ),
            //     'desc' => esc_html__( 'Markdown is available.', 'waff' ),
            // ],
			[
				'id'                => $prefix . 'i_lists',
				'type'              => 'text_list',
				'name'              => __( 'List.s', 'waff' ),
				'label_description' => __( '<span class="label">INFO</span> Fill to create a list of items.', 'wa-rsfp' ),
				'options'           => [
					'Title'       	=> 'Title (optionnal)',
					'Label'       	=> 'Label',
					'Suffix'       	=> 'Suffix (optionnal)',
					'Description' 	=> 'Description',
					'Class'       	=> 'Fill here an css class (optionnal)',
					'Link'       	=> 'http://www.google.fr (optionnal)',
					'TextColor'    => 'Override here text color (optionnal)',
					'FontWeight'   => 'Override here font weight (optionnal)',
					// 'Value'       	=> 'Value',
				],
				'clone'             => true,
				'sort_clone'        => true,
				'max_clone'         => 100,
				'class'             => 'row-list',
			],
            [	
                'id'   => $prefix . 'i_image',
                'type' => 'image_advanced',
				'name' => esc_html__( 'Image', 'waff' ),
                'image_size'       => 'page-featured-image',
                'max_file_uploads' => 1,
            ],
            // [
            //     'id'    => $prefix . 'i_alignment',
			// 	'name'		=> esc_html__( 'Select an alignment', 'waff' ),
            //     'type'		=> 'select',
            //     'desc'		=> esc_html__( 'Choose the aligment beetween background and image.', 'waff' ),
            //     'std'		=> 'post',
            //     'options'           => [
            //         'aligned' => esc_html__( 'Aligned', 'waff' ),
            //         'shifted' => esc_html__( 'Shifted', 'waff' ),
            //     ],
            //     // 'required'          => 1,
            //     'key'               => 'value',
			// ],	
			// [
            //     'id'		=> $prefix . 'i_position',
            //     'name'		=> esc_html__( 'Select a position', 'waff' ),
            //     'type'		=> 'select',
            //     'desc'		=> esc_html__( 'Choose image position.', 'waff' ),
            //     'std'		=> 'post',
            //     'options'           => [
            //         'top' => esc_html__( 'Top', 'waff' ),
            //         'center' => esc_html__( 'Centered', 'waff' ),
            //         'bottom' => esc_html__( 'Bottom', 'waff' ),
            //     ],
            //     // 'required'          => 1,
            //     'key'               => 'value',
			// ],
			[
				'id'   => $prefix . 'i_bg_color',
				'type' => 'text',
				'name' => esc_html__( 'Override background color', 'waff' ),
				'std' => '',
                'desc' => esc_html__( 'Fill a color class.', 'waff' ),
			],
            [
                'id'    => $prefix . 'i_morelink',
                'type'  => 'switch',
                'name'  => esc_html__( 'Display more link ?', 'waff' ),
                'style' => 'rounded',
            ],
            [
                'id'   => $prefix . 'i_moreurl',
                'type' => 'url',
                'name'  => esc_html__( 'More URL', 'waff' ),
                'desc'  => esc_html__( 'Fill an absolute link. Can be internal or external, e.g. : http://www.google.com', 'waff' ),
            ],
			// Block margin
			// Remove top / bottom margin class
			[
                'type' => 'heading',
                'name' => __( 'Block margins', 'waff' ),
			],
			[
                'id'    => $prefix . 'i_blockmargin',
				'name'  => esc_html__( 'Block have margin ?', 'waff' ),
                'desc'  => esc_html__( 'Removes both top & bottom block margin.', 'waff' ),
                'type'  => 'switch',
                'style' => 'rounded',
                'std'   => true,
            ],
		],
		'category'       => 'layout',
		// 'icon'           => 'format-quote',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'chart-area',
		],
		'description'     => esc_html__( 'Display breaking bloc with two cols', 'waff' ),
		'keywords'       => ['hero', 'content', 'text', 'insight', 'data', 'bloc'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_insights_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];
 
	// WA Keywords ( #RSFP )
	$meta_boxes[] = [
		'title'          => esc_html__( '(WA) Key messages', 'waff' ),
		'id'             => 'wa-keymessages',
		'fields'         => [
			[
                'id'   => $prefix . 'k_title',
                'type' => 'text',
                'name' => esc_html__( 'Title', 'waff' ),
                // 'std'  => esc_html__( 'An awesome edition', 'waff' ),
                'placeholder' => esc_html__( 'An awesome title', 'waff' ),
            ],
            [
                'id'   => $prefix . 'k_subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Subtitle', 'waff' ),
                // 'std'  => esc_html__( 'Edito', 'waff' ),
				'placeholder' => esc_html__( 'An awesome subtitle', 'waff' ),
			],
		],
		'category'       => 'layout',
		// 'icon'           => 'format-quote',
		'icon'            => [
			'foreground' 	=> '#9500ff',
			'src' 			=> 'editor-paragraph',
		],
		'description'     => esc_html__( 'Display homeslide key messages / contextual / engagement in content with a block', 'waff' ),
		'keywords'       => ['homeslide', 'content', 'text', 'insight', 'data', 'bloc'],
		'supports'       => [
			'anchor'          => true,
			'customClassName' => true,
			'align'           => ['wide', 'full'],
		],
		//'render_code'    => '{{Twix}}',
		//'enqueue_style'  => 'customCSS',
		//'enqueue_script' => 'CustomJS',
		//'enqueue_assets' => 'CustomCallback',
		'render_callback' => 'WaffTwo\Blocks\Block\wa_keymessages_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];

    return $meta_boxes;
}

/**
 * Disallow some blocks 
 * 
 */

// function waff_allowed_block_types( $allowed_block_types, $post ) {
// 	/* OLD WAY > use JS now */

// 	print_r($post->post_type);
// 	print_r($allowed_block_types);
// 	print_r(get_dynamic_block_names());

// 	// Fetch block names.
// 	//$block_names = get_dynamic_block_names();

// 	if ( $post->post_type !== 'page' ) {
//         return $allowed_block_types;
//     }
 
// 	//https://wordpress.org/support/article/blocks/
// 	//https://rudrastyh.com/gutenberg/remove-default-blocks.html
// 	//https://github.com/WordPress/gutenberg/issues/27913
// 	//https://github.com/WordPress/gutenberg/issues/27708
// 	$core = array( 
// 		'core/paragraph',
// ....
// 	);
	
// 	return array_merge($core,get_dynamic_block_names());

// }

/**
 * Disallow some blocks 
 * JS way 
 * https://github.com/WordPress/gutenberg/issues/25676
 */

// function waff_reset_blocks_enqueue_block_editor_assets() {
// 	// Get theme option
// 	$advanced_blocks = (bool) get_theme_mod( 'advanced_blocks', waff_defaults( 'advanced_blocks' ) );

// 	// If the option is not checked, return.
// 	if ( $advanced_blocks !== true )
// 		wp_enqueue_script( 'wp-bootstrap-block-reset', get_stylesheet_directory_uri() . '/dist/js/admin/custom-wp-bootstrap-reset.js', array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ), '1.0.0', true ); // Script solution > only remove blocks but not in list
// }

/**
 * Disallow some blocks 
 * PHP way 
 */

function waff_allowed_block_types( $allowed_blocks, $editor_context ) {

	// error_log( "waff_allowed_block_types :: allowed_blocks :: " . print_r($allowed_blocks, true)  );
	// error_log( "waff_allowed_block_types :: editor_context :: " . print_r($editor_context, true)  );

	// Get theme option
	$advanced_blocks = (bool) get_theme_mod( 'advanced_blocks', waff_defaults( 'advanced_blocks' ) );

	// error_log('BEFORE::');
	// error_log($advanced_blocks);
	// error_log(print_r($allowed_blocks, true));

	if ( $advanced_blocks !== true && isset( $editor_context->post ) && !empty( $editor_context->post ) ) { // All post_type blocks

		// The blocks I want to enable for sure
		$blocks_to_enable = array(
			"meta-box/wa-latest-posts",
			"meta-box/wa-partners",
			"meta-box/wa-edito",
			"meta-box/wa-contact",
			"meta-box/wa-playlist",
			"meta-box/wa-awards",
			"meta-box/wa-film",
			"meta-box/wa-section",
			"meta-box/wa-sections",
			"meta-box/wa-misson",
			"meta-box/wa-cols",
			"meta-box/wa-breaking",
			"meta-box/wa-insights",
			"meta-box/wa-programmation",
		);
	
		// The blocks I want to disable
		$blocks_to_disable = array(

			// core/
			"core/loginout",
			"core/term-description",
			"core/query-title",
			"core/post-author-biography",
			"core/freeform",
			"core/avatar",
			"core/post-title",
			"core/post-excerpt",
			"core/post-featured-image",
			"core/post-content",
			"core/post-author",
			"core/post-author-name",
			"core/post-date",
			"core/post-terms",
			"core/post-navigation-link",
			"core/post-template",
			"core/query-pagination",
			"core/query-pagination-next",
			"core/query-pagination-numbers",
			"core/query-pagination-previous",
			"core/query-no-results",
			"core/read-more",
			"core/comments",
			"core/comment-author-name",
			"core/comment-content",
			"core/comment-date",
			"core/comment-edit-link",
			"core/comment-reply-link",
			"core/comment-template",
			"core/comments-title",
			"core/comments-pagination",
			"core/comments-pagination-next",
			"core/comments-pagination-numbers",
			"core/comments-pagination-previous",
			"core/post-comments-form",
			"core/navigation",
			"core/navigation-link",
			"core/navigation-submenu",
			"core/site-logo",
			"core/site-title",
			"core/site-tagline",
			"core/query",
			"core/archives",
			"core/calendar",
			"core/categories",
			"core/code",
			"core/embed",
			"core/latest-comments",
			"core/latest-posts",
			"core/more",
			"core/nextpage",
			"core/page-list",
			"core/page-list-item",
			"core/preformatted",
			"core/rss",
			"core/search",
			"core/social-links",
			"core/tag-cloud",
			"core/verse",
			// "core/details",

			// coblocks/
			'coblocks/accordion',
			'coblocks/accordion-item',
			//'coblocks/alert', > Used for fifam #45
			'coblocks/counter',
			'coblocks/column',
			'coblocks/row',
			'coblocks/dynamic-separator',
			'coblocks/logos',
			'coblocks/icon',
			'coblocks/buttons',	
			"coblocks/shape-divider",
			"coblocks/events",
			"coblocks/event-item",
			"coblocks/form",
			"coblocks/field-date",
			"coblocks/field-email",
			"coblocks/field-name",
			"coblocks/field-radio",
			"coblocks/field-phone",
			"coblocks/field-textarea",
			"coblocks/field-text",
			"coblocks/field-select",
			"coblocks/field-submit-button",
			"coblocks/field-checkbox",
			"coblocks/field-website",
			"coblocks/field-hidden",
			"coblocks/click-to-tweet",
			"coblocks/food-and-drinks",
			"coblocks/food-item",
			"coblocks/pricing-table",
			"coblocks/pricing-table-item",
			"coblocks/opentable",
			"coblocks/gist",

			// toolset/
			"toolset/ct",

			// bcn/
			"bcn/breadcrumb-trail",

			// complianz/
			"complianz/document",
			"complianz/consent-area",

		);
		
		// The list of active blocks in WordPress
		$active_blocks = array_keys(
			\WP_Block_Type_Registry::get_instance()->get_all_registered()
		);
		// error_log('active_blocks ::' . print_r($active_blocks, true));

		// The new list without the unwanted blocks
		return array_values(array_merge(array_diff($active_blocks, $blocks_to_disable), $blocks_to_enable));
	}

	// Or nothing, return the default allowed blocks.
	return $allowed_blocks;
}

/**
 * Define new colors for wp-bootstrap-blocks 
 */

function waff_wp_boostrap_enqueue_block_editor_assets() {
	wp_enqueue_script( 'wp-bootstrap-block-filter', get_stylesheet_directory_uri() . '/dist/js/admin/custom-wp-bootstrap-blocks.js', array( 'wp-hooks' ), '1.1.0', true );
    if ( defined( 'WAFF_THEME_COLORS' ) ) wp_localize_script( 'wp-bootstrap-block-filter', 'wpBootstrapBlockFilterOptions', WAFF_THEME_COLORS );
}
