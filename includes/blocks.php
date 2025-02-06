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
		'render_callback' => 'WaffTwo\Blocks\wa_latest_posts_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_partners_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_edito_callback',
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
				'name' => esc_html__( 'Contact form id', 'waff' ),
                'desc' => esc_html__( 'Fill the Gravity form id.', 'waff' ),
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
		'render_callback' => 'WaffTwo\Blocks\wa_contact_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_playlist_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_awards_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_film_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_section_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_sections_callback',
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
			[
                'id'    => $prefix . 'm_blockmargin',
				'name'  => esc_html__( 'Block have margin ?', 'waff' ),
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
		'render_callback' => 'WaffTwo\Blocks\wa_mission_callback',
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
				'id'   => $prefix . 'c_image',
				'type' => 'image_advanced',
				'name' => esc_html__( 'Image', 'waff' ),
				'image_size'       => 'page-featured-image',
				'max_file_uploads' => 1,
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
		'render_callback' => 'WaffTwo\Blocks\wa_cols_callback',
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
		'render_callback' => 'WaffTwo\Blocks\wa_breaking_callback',
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
                    'Icon <i>'       	=> 'Fill here an css icon (optionnal)',
                    'Link'       	=> 'http://www.google.fr (optionnal)',
                    // 'Value'       	=> 'Value',
                ],
                'clone'             => true,
                'sort_clone'        => true,
                'max_clone'         => 100,
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
		'render_callback' => 'WaffTwo\Blocks\wa_insights_callback',
		'type'           => 'block',
		'context'        => 'side',
		//'Keyattrs'       => 'Value',
	];
 
    return $meta_boxes;
}

function wa_latest_posts_callback( $attributes, $is_preview = false, $post_id = null ) {

	if ( $is_preview === true ) {
		?>
		<section style="text-align: center; padding-left: 15%; padding-right: 15%;">
		<?php
		switch(mb_get_block_field( 'waff_lp_style' )) {
			case 'normal':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-normal.svg" class="img-fluid" />	
				<?php
				break;
			case 'magazine':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-magazine.svg" class="img-fluid" />	
				<?php
				break;
			case 'bold':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-bold.svg" class="img-fluid" />	
				<?php
				break;
			case 'classic':
				?>
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/dist/images/admin/blocks/block-lastest-classic.svg" class="img-fluid" />	
				<?php
				break;
			default:
				?>
					ERROR / No style is selected
				<?php
				break;
		}
		?>
		</section>
		<?php
		return;
	}

	// print_r($attributes);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	//$themeClass = 'featured mt-10 mb-10 contrast--dark fix-vh-50';
	$themeClass = 'featured mt-md-5 mb-md-5 mt-2 mb-2 contrast--dark fix-vh-50'; // Responsive issue fix
	if ( mb_get_block_field( 'waff_lp_style' ) == 'normal') $themeClass = 'mt-2 mb-6 contrast--light';
	if ( mb_get_block_field( 'waff_lp_style' ) == 'classic') $themeClass = 'mt-2 mb-6 contrast--light overflow-visible';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	if ( mb_get_block_field( 'waff_lp_style' ) == null ) :
	?>
		<div class="alert alert-dark"><p><?php _e('Please define style to continue', 'waff'); ?></p></div>
	<?php
	endif;

	$sticky_posts 	= array();
	$categories 	= array();
	$categories_id	= array();
	// mb_get_block_field / mb_the_block_field
	$limit 			= esc_attr(mb_get_block_field( 'waff_lp_limit' ));
	// $morelink 		= esc_attr(mb_get_block_field( 'waff_lp_morelink' ));
	$posttype 		= esc_attr(mb_get_block_field( 'waff_lp_posttype' ));
	$meta 			= esc_attr(mb_get_block_field( 'waff_lp_meta' ));

	if ( $posttype === 'post' ) {
		//$categories 	= $attributes['data']['waff_lp_categories'];
		$categories 	= mb_get_block_field( 'waff_lp_categories' );
		$categories_id  = wp_list_pluck($categories, 'term_id');
	}

	$sticky_posts_option = get_option('sticky_posts');

	if (!empty($sticky_posts_option)) {
		$sticky_posts = get_posts(array(
			'post_type'			=> $posttype,
			'numberposts' 		=> $limit, 
			'post_status' 		=> 'publish', // Show only the published posts
			'orderby'			=> 'post_date',
			'order'				=> 'DESC',
			// Only the sticky ones !
			'post__in'  		=> $sticky_posts_option,
			'ignore_sticky_posts' => true,
			// Limit to selected cats 
			'category'			=> $categories_id,
		));
	} else {
		$sticky_posts = get_posts(array(
			'post_type'			=> $posttype,
			'numberposts' 		=> 1,
			'post_status' 		=> 'publish', // Show only the published posts
			'orderby'			=> 'post_date',
			'order'				=> 'DESC',
			// No limit to sticky if not, only the last one if featured 
			// Limit to selected cats 
			'category'			=> $categories_id,
		));
	}

	$args = array(
		'post_type'			=> $posttype,
		'numberposts' 		=> $limit, 
		'post_status' 		=> 'publish', // Show only the published posts
		'orderby'			=> 'post_date',
		'order'				=> 'DESC',
		// All but not sticky !
		'post__not_in'  	=> get_option( 'sticky_posts' ),
		// Limit to selected cats 
		'category'			=> $categories_id,
	);

	$all_posts = array(
		'post_type'			=> $posttype,
		'numberposts' 		=> $limit, 
		'post_status' 		=> 'publish', // Show only the published posts
		'orderby'			=> 'post_date',
		'order'				=> 'DESC',
		// Limit to selected cats 
		'category'			=> $categories_id,
	);

	if ( mb_get_block_field( 'waff_lp_style' ) === 'normal' ) :
	?>
	<!-- #Latest / Normal style -->
	<section id="<?= $id ?>" class="normal-style <?= $class ?> <?= $animation_class ?>" <?= $data ?>>
		<div class="container-fluid">

			<span class="bullet bullet-action-2 ml-0"></span>
			<h5><?= mb_get_block_field( 'waff_lp_title' ) ?></h5>

			<div class="row row-cols-1 row-cols-md-<?= ($limit != '')?$limit:3; ?> mt-4 mb-4">
			
			<?php 
				$index = 0;
				$recent_posts = get_posts($all_posts);
				//$recent_posts = array_merge($sticky_posts, $recent_posts);

				foreach( $recent_posts as $post_item ) : 
					// Set up global post data in loop 
					// setup_postdata($GLOBALS['post'] =& $post_item); //$GLOBALS['post'] =& $post_item

					$post_id 				= esc_attr($post_item->ID);
					$post_color 			= rwmb_meta( '_waff_bg_color_metafield', $args, $post_id );
					$post_color				= ($post_color!='')?$post_color:'#444444'; //00ff97
					$rgb_post_color			= waff_HTMLToRGB($post_color, 'array'); // , 'array' ICI Bug ??
					$the_categories 		= get_the_category($post_id);
					$excerpt = '';
					$the_excerpt = wp_strip_all_tags(get_the_excerpt($post_id));
					$the_content = wp_strip_all_tags(get_the_content('...', true, $post_id));
					$the_introduction = wp_strip_all_tags(get_post_meta($post_id, 'd_general_introduction', true));
					// echo $post_id;
					// echo $the_content; 
					$the_content = ( $the_introduction !== '' )?$the_introduction:$the_content;
					$excerpt = ( $the_excerpt !== '' )?$the_excerpt:$the_content;
					if ( strlen($excerpt) > 140 ) {
						$excerpt = substr($excerpt, 0, 140);
						$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
					}
					if ( $index > $limit ) { continue; }
					?>
				<div class="col">
					<div class="card mb-1 mb-md-2 border-0" id="<?= $post_id; ?>">
						<div class="row g-0">
							<div class="col-md-4">
								<img src="<?php echo get_the_post_thumbnail_url($post_id, 'thumbnail'); ?>" class="img-fluid rounded-4">
							</div>
							<div class="col-md-8">
								<div class="card-body py-0">
								
									<?php if ( ! empty( $the_categories ) ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
									
									<h5 class="card-title mt-2">
										<a href="<?= get_permalink( $post_id ) ?>" class="stretched-link">
											<?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?>
											<?= $post_item->post_title ?>
										</a>
									</h5>

									<p class="card-text"><?= $excerpt; ?></p>

									<?php if ( $meta == 1 ) : ?>
									<?= waff_entry_meta_header($post_item); ?>
									<?php else : ?>
									<p class="card-text mt-n2"><small class="text-body-secondary"><?= get_the_date('j F Y', $post_id); ?></small></p>
									<?php endif; ?>

								</div>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; 
					// After the loop, reset post data to ensure the global $post returns to its original state
					wp_reset_postdata();
				?>
				
			</div> <!-- END: .row -->

		</div>
	</section>
	<!-- END: #Latest / Normal style-->
	<?php
	endif;

	if ( mb_get_block_field( 'waff_lp_style' ) === 'classic' ) :
	?>
	<!-- #Latest / Classic style -->
	<section id="<?= $id ?>" class="classic-style <?= $class ?> <?= $animation_class ?>" <?= $data ?>>
		<div class="container-fluid">
			<div class="row row-cols-1 row-cols-md-2 mt-4 mb-4">

				<!-- Right col-->
				<div class="col mb-4 mb-md-0">

				<?php 
					foreach( $sticky_posts as $post_item ) : 
						$post_id 				= esc_attr($post_item->ID);
						$the_categories 		= get_the_category($post_id);
						?>
					<div class="card min-h-250-px h-100 overflow-hidden rounded-4 shadow-lg border-0 ---- bg-cover bg-position-center-center"
					id="<?= $post_id; ?>" 
					style="background-image: url('<?= get_the_post_thumbnail_url($post_id, 'large'); ?>');">
						<div class="card-img-overlay bg-gradient-action-2">
							<div class="d-flex flex-column justify-content-between h-100 p-4 pb-3 text-white text-shadow-1">
								<div>
									<h6 class="subline text-action-1"><?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?> En avant</h6>
									<?php if ( ! empty( $the_categories ) && $meta ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
								</div>
								<h2 class="text-white mt-auto mb-8">
									<a href="<?= get_permalink( $post_id ) ?>" class="stretched-link link-white">
										<?= $post_item->post_title ?>
									</a>
								</h5>
								<ul class="d-flex list-unstyled m-0">
									<li class="me-auto subline">Lire la suite <i class="bi bi-chevron-right"></i></li>
									<li class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> <small><?= get_the_date('j F Y', $post_id); ?></small></li>
								</ul>
							</div>
						</div>
					</div>
					<?php endforeach; 
					// After the loop, reset post data to ensure the global $post returns to its original state
					wp_reset_postdata();
				?>

				</div>

				<!-- Left col-->
				<div class="col">
					<style scoped>
						.card.r-card {
							height: 250px;
						}

						@media (min-width: 768px) {
							.card.r-card {
								height:16vw !important;
							}
						}
					</style>
					<!-- Grid rows -->
					<div class="row row-cols-1 row-cols-md-2 g-4">	
					<?php 
						$recent_posts = get_posts($args);
						foreach( $recent_posts as $post_item ) : 
							$post_id 				= esc_attr($post_item->ID);
							$the_categories 		= get_the_category($post_id);
							?>
						<div class="col">
							<div class="card r-card h-250-px overflow-hidden rounded-4 shadow-lg border-0 ---- bg-cover bg-position-center-center" 
							id="<?= $post_id; ?>" 
							style="background-image: url('<?= get_the_post_thumbnail_url($post_id, 'large'); ?>');">
								<div class="card-img-overlay bg-gradient-action-2">
									<div class="d-flex flex-column justify-content-between h-100 p-4 pb-3 text-white text-shadow-1">
										<div>
											<?php if ( ! empty( $the_categories ) && $meta ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
										</div>
										<h5 class="text-white">
											<a href="<?= get_permalink( $post_id ) ?>" class="stretched-link link-white">
												<?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?>
												<?= $post_item->post_title ?>
											</a>
										</h5>
										<ul class="d-flex list-unstyled m-0">
											<li class="me-auto subline">Lire la suite <i class="bi bi-chevron-right"></i></li>
											<li class="d-flex align-items-center"><i class="bi bi-calendar3 me-2"></i> <small><?= get_the_date('j F Y', $post_id); ?></small></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<?php endforeach; 
					// After the loop, reset post data to ensure the global $post returns to its original state
					wp_reset_postdata();
				?>
				
			</div> <!-- END: .row -->

		</div>
	</section>
	<!-- END: #Latest / Classic style-->
	<?php
	endif;

	if ( mb_get_block_field( 'waff_lp_style' ) === 'magazine' ) :
	?>
	<!-- #Featured / Magazine style -->
	<section id="<?= $id ?>" class="magazine-style <?= $class ?> <?= $animation_class ?>" <?= $data ?>>
		<div class="container-fluid px-0">
		<div class="row g-0 align-items-top">

			<?php 

			$index = 0;
			$recent_posts = get_posts($args);
			//$recent_posts = array_merge($sticky_posts, $recent_posts);

			if ( count($sticky_posts) > 0 ) :
				foreach( $sticky_posts as $post_item ) : 
					$post_color 			= rwmb_meta( '_waff_bg_color_metafield', $args, $post_item->ID );
					//$rgb_post_color			= waff_HTMLToRGB($post_color, 'array');
					$the_categories 		= get_the_category($post_item->ID);
					$excerpt 	= get_the_excerpt($post_item->ID);
					$excerpt 	= force_balance_tags( html_entity_decode( wp_trim_words( htmlentities($excerpt), 15, '...' ) ) );
					$sticky_post_date = wp_kses(
						sprintf(
							'<time datetime="%1$s">%2$s</time>',
							esc_attr( get_the_date( DATE_W3C, $post_item->ID ) ),
							( function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() == 'en' )?date_i18n( 'M jS Y', strtotime( get_the_date( DATE_W3C, $post_item->ID ) )  ) : get_the_date('j F Y', $post_item->ID)
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
					<!-- BEGIN: Sticky post -->
					<div class="col-md-6 p-4" data-aos="fade-right">
						<h6 class="headline d-inline"><?= mb_get_block_field( 'waff_lp_title' ) ?></h6>
					</div>
					<div class="col-md-6 p-0 vh-75 img-shifted shift-right overflow-visible" data-aos="fade-left" data-aos-delay="200">
						<!-- Images -->
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo get_the_post_thumbnail_url($post_item->ID, 'large'); ?>');"></div>
						
						<!-- Content -->
						<div class="card bg-transparent h-100 p-4 border-0 rounded-0 d-flex flex-column justify-content-center n-ms-50 w-100">
							<h6 class="display d-inline text-action-1 f-14 text-center">
							<?php if ( ! empty( $the_categories ) ) { echo '<a class="text-action-1" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . ' <span class="bullet bullet-action-1 w-25"></span></a>'; } ?>
							</h6>
							<div class="main-post my-5">
								<h1 class="card-title light display-2"><a href="<?php echo get_permalink($post_item->ID) ?>" class="stretched-link"><?php echo $post_item->post_title ?></a></h1>
								<p class="card-date mt-2 mb-0">
									<?= is_sticky( $post_item->ID ) ? '<i class="bi bi-pin"></i>' : '' ?>
									<?php echo $sticky_post_date; ?>
								</p>
								<p class="card-text d-none"><?php echo $excerpt; ?></p>
							</div>
							<p class="text-action-2 pb-0 mb-0"><i class="icon icon-plus"></i> <?php _e('Read more', 'waff'); ?></p>
						</div>
					</div>
					<!-- END: Sticky post -->
					<?php
				endforeach;
			endif;

			if ( count($recent_posts) > 0 ) :
				// $oldest_post_date = get_the_date('j F Y', $recent_posts[0]->ID);
				$oldest_post_date = wp_kses(
					sprintf(
						'<time datetime="%1$s">%2$s</time>',
						esc_attr( get_the_date( DATE_W3C, $recent_posts[0]->ID ) ),
						( function_exists('qtranxf_getLanguage') && qtranxf_getLanguage() == 'en' )?date_i18n( 'M jS Y', strtotime( get_the_date( DATE_W3C, $recent_posts[0]->ID ) )  ) : get_the_date('j F Y', $recent_posts[0]->ID)
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
				<!-- BEGIN: Posts -->
				<div class="col-md-3 p-4" data-aos="fade-right">
					<h6 class="headline d-inline"><?= mb_get_block_field( 'waff_lp_subtitle' ) ?></h6>
				</div>
				<div class="col-md-9 p-4 bold text-action-2" data-aos="fade-left">
					<?php echo $oldest_post_date; ?> <span class="bullet bullet-action-1"></span> <span class="color-action-1"><?php _e('Now', 'waff'); ?></span>
				</div>				
				<!-- END: Posts --> 
				<div class="col-md-12 p-0" data-aos="fade-down" data-aos-delay="400">
				<div class="list-group list-group-horizontal-lg list-group-flush rounded-0">
					<?php
					// Lightness threshold
					$lightness_threshold = 130;

					foreach( $recent_posts as $post_item ) : 
						$post_color 						= rwmb_meta('_waff_bg_color_metafield', $args, $post_item->ID );
						$rgb_post_color						= waff_HTMLToRGB($post_color); // TODO , 'array' ICI BUG ? // 'array'
						$post_color_class					= 'contrast--light';
						$post_title_color 					= 'color-dark';

						// print_r($rgb_post_color);

						// Check if the color is dark or light
						if ( $post_color && $post_color != '' ) { // Si $post_color n'est pas vide
							$hsl = waff_RGBToHSL($rgb_post_color); // Accepte un INTEGER
							if($hsl->lightness < $lightness_threshold) {
								$post_color_class 			= 'contrast--dark';
								$post_title_color 			= 'color-dark';	// Here, this is the same because we do need to handle the hover state l:547
							}
						}


						$the_categories 		= get_the_category($post_item->ID);

						/*$excerpt = '';
						$excerpt = wp_strip_all_tags(get_the_excerpt($post_item->ID));
						if ( strlen($excerpt) > 160 ) {
							$excerpt = substr($excerpt, 0, 160);
							$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
						}*/
						$excerpt 	= get_the_excerpt($post_item->ID);
						$excerpt 	= force_balance_tags( html_entity_decode( wp_trim_words( htmlentities($excerpt), 15, '...' ) ) );

						if ( $index > $limit ) { continue; }
						?>
							<div id="post-<?php echo esc_attr($post_item->ID); ?>" 
								class="<?= $post_color_class ?> <?= ( $post_color != '' && $post_color_class != 'contrast--light' )?'has-hover-background':''; ?> --vh-50 lg-vh-50 mh-380-px min-h-180-px p-4 p-sm-5 border border-start-0 border-transparent-color-silver list-group-item list-group-item-light list-group-item-action d-flex flex-column align-items-start justify-content-between">
								<div>
									<?php if ( ! empty( $the_categories ) ) { echo '<a class="badge rounded-pill bg-action-2 position-relative zi-2" href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
									
									<?php if ( $meta == 1 ) : ?>
									<?= waff_entry_meta_header($post_item); ?>
									<?php else : ?>
									<p class="<?= $post_title_color ?> text-muted list-date mt-1 mb-0 d-none"><?php echo get_the_date('j F Y', $post_item->ID); ?></p>
									<?php endif; ?>
									
									<h3 class="<?= $post_title_color ?> mb-1 mt-0"><?php echo $post_item->post_title ?></h3>
									<p class="text-action-2 list-text f-14"><?php echo $excerpt; ?></p>
								</div>
								<a href="<?php echo get_permalink($post_item->ID) ?>" class="stretched-link text-action-2 pb-0 mb-0"><i class="icon icon-plus"></i> <?php _e('Read more', 'waff'); ?></a>
							</div>
						<?php if ( $post_color != '' ) : ?>
							<style> .list-group #post-<?php echo esc_attr($post_item->ID); ?>.list-group-item:hover { background-color:<?= $post_color ?> !important; }</style>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
				</div>
				<?php endif; ?>
		</div>
		</div>
	</section>
	<!-- END: #Featured / Magazine style-->
	<?php
	endif;

	if ( mb_get_block_field( 'waff_lp_style' ) === 'bold' ) :
	?>
	<!-- #Featured / Bold style -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
		<div class="row g-0 align-items-top">
			<div class="col-md-2 p-4" data-aos="fade-right">
				<h6 class="headline d-inline"><?= mb_get_block_field( 'waff_lp_title' ) ?></h6>
			</div>
			<?php 
			$index = 0;
			$recent_posts = get_posts($args);
			$recent_posts = array_merge($sticky_posts, $recent_posts);

			foreach( $recent_posts as $post_item ) : 
				$post_id 				= esc_attr($post_item->ID);
				$post_color 			= rwmb_meta( '_waff_bg_color_metafield', $args, $post_id );
				$post_color				= ($post_color!='')?$post_color:'#444444'; // 444444 //00ff97 > Gray blending color if no post custom color 
				$rgb_post_color			= waff_HTMLToRGB($post_color, 'array'); // , 'array' ICI Bug ??
				$the_categories 		= get_the_category($post_id);
				$excerpt = '';
				$excerpt = wp_strip_all_tags(get_the_excerpt($post_id));
				if ( strlen($excerpt) > 160 ) {
					$excerpt = substr($excerpt, 0, 160);
					$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ')) . '...';
				}
				if ( ++$index === 1 ) { 
					?>
					<!-- First -->
					<div class="col-md-5 p-0 vh-50 bg-dark img-shifted shift-right nofilter-hover" data-aos="fade-down" data-aos-delay="200">
						<!-- Duotone filter : blended w/ custom post color  -->
						<!-- https://yoksel.github.io/svg-gradient-map/ -->
						<svg class="duotone-filters position-absolute" xmlns="http://www.w3.org/2000/svg">	
							<filter id="duotone_featured_<?= $post_id; ?>" x="-10%" y="-10%" width="120%" height="120%" filterUnits="objectBoundingBox" primitiveUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
								<feColorMatrix type="matrix" values="1 0 0 0 0
							1 0 0 0 0
							1 0 0 0 0
							0 0 0 1 0" in="SourceGraphic" result="colormatrix"></feColorMatrix>
								<feComponentTransfer in="colormatrix" result="componentTransfer">
									<feFuncR type="table" tableValues="0 <?= $rgb_post_color[0]/255; ?>"/>
									<feFuncG type="table" tableValues="0 <?= $rgb_post_color[1]/255; ?>"/>
									<feFuncB type="table" tableValues="0 <?= $rgb_post_color[2]/255; ?>"/>
									<feFuncA type="table" tableValues="0 1"/>
								</feComponentTransfer>
								<feBlend mode="normal" in="componentTransfer" in2="SourceGraphic" result="blend"></feBlend>
							</filter>
						</svg>
						<!-- Images -->
						<div class="bg-image bg-cover bg-position-top-center image--origin filter--<?= $post_id; ?>" style="background-image: url('<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>');"></div>
						<div class="bg-image bg-cover bg-position-top-center image--filtered filter--<?= $post_id; ?>" style="background-image: url('<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>');"></div>
						<!-- Content -->
						<div class="card bg-transparent text-light h-100 p-4 border-0 rounded-0 d-flex flex-column justify-content-between">
							<h6 class="display d-inline --action-2 f-14 link-light">
							<?php if ( ! empty( $the_categories ) ) { echo '<a href="' . esc_url( get_category_link( $the_categories[0]->term_id ) ) . '">' . esc_html( $the_categories[0]->name ) . '</a>'; } ?>
							</h6>
							<div class="main-post">
								<p class="card-date text-light mt-1 mb-0">
									<?= is_sticky( $post_id ) ? '<i class="bi bi-pin"></i>' : '' ?>
									<?php echo get_the_date('j F Y', $post_id); ?>
								</p>
								<h2 class="card-title w-60"><a href="<?php echo get_permalink($post_id) ?>" class="stretched-link link-light"><?php echo $post_item->post_title ?></a></h2>
							</div>
							<p class="card-text"><?php echo $excerpt; ?></p>
						</div>
					</div>
					<style scoped> 
						#<?= $id ?> .bg-image {
							transition: opacity .25s;
						}
						
						#<?= $id ?> img.filter--<?= $post_id; ?>, 
						#<?= $id ?> .bg-image.filter--<?= $post_id; ?>.image--filtered  {
							-webkit-filter: url(#duotone_featured_<?= $post_id; ?>);
							-moz-filter: url(#duotone_featured_<?= $post_id; ?>);
							-o-filter: url(#duotone_featured_<?= $post_id; ?>);
							-ms-filter: url(#duotone_featured_<?= $post_id; ?>);
							filter: url(#duotone_featured_<?= $post_id; ?>);
						}

						#<?= $id ?> .nofilter-hover:hover img { 
							-webkit-filter: none;
							-moz-filter: none;
							-o-filter: none;
							-ms-filter: none
							filter: none;
						}

						#<?= $id ?> .nofilter-hover:hover .bg-image.image--filtered {
							opacity:0;
						}
					</style>
					<!-- END : First --> 
					<?php
					continue;
				}
				if ( $index === 2 ) { 
					?>
				<div class="col-md-5 p-0 min-vh-50 min-h-50" data-aos="fade-down" data-aos-delay="400">
				<div class="list-group list-group-flush h-100 rounded-0">
					<!-- Second -->
					<a id="post-<?= $post_id; ?>" href="<?php echo get_permalink($post_id) ?>" class="list-group-item list-group-item-dark list-group-item-action d-flex flex-column align-items-start justify-content-start h-55 pr-0 overflow-hidden nofilter-hover">
						<p class="list-date text-muted mt-2 mb-0"><?php echo get_the_date('j F Y', $post_id); ?></p>
						<div class="d-flex w-100 justify-content-between ">
							<div class="second-post">
								<h6 class="normal mb-3 mt-0"><?php echo $post_item->post_title ?></h6>
								<small><?php echo $excerpt; ?></small>
							</div>
							<?php echo get_the_post_thumbnail($post_id, 'thumbnail', array( 'class' => 'img-fluid responsive float-right pl-2 max-w-50 filter--'.$post_id )); ?>
						</div>
					</a>
					<style scoped> 
						.list-group a#post-<?= $post_id; ?>.list-group-item:hover { background-color:<?= $post_color ?> !important; }
						/* #featured-<?= $id ?> .bg-image {
							transition: opacity .25s;
						}
						
						#featured-<?= $id ?> img.filter--<?= $post_id; ?>, 
						#featured-<?= $id ?> .bg-image.filter--<?= $post_id; ?>.image--filtered  {
							-webkit-filter: url(#duotone_featured_<?= $post_id; ?>);
							-moz-filter: url(#duotone_featured_<?= $post_id; ?>);
							-o-filter: url(#duotone_featured_<?= $post_id; ?>);
							-ms-filter: url(#duotone_featured_<?= $post_id; ?>);
							filter: url(#duotone_featured_<?= $post_id; ?>);
						}

						#featured-<?= $id ?> .nofilter-hover:hover img { 
							-webkit-filter: none;
							-moz-filter: none;
							-o-filter: none;
							-ms-filter: none
							filter: none;
						}

						#featured-<?= $id ?> .nofilter-hover:hover .bg-image.image--filtered {
							opacity:0;
						} */
					</style>
					<!-- END : Second --> 
					<?php
					continue;
				}
				if ( $index > $limit ) { continue; }
				?>
				<a id="post-<?= $post_id; ?>" href="<?php echo get_permalink($post_id) ?>" class="third-posts list-group-item list-group-item-dark list-group-item-action d-flex flex-column align-items-start justify-content-center h-15">
						<p class="list-date text-muted mt-1 mb-0"><?php echo get_the_date('j F Y', $post_id); ?></p>
						<h6 class="normal mb-1 mt-0"><?php echo $post_item->post_title ?></h6>
				</a>
				<style scoped> 
					.list-group a#post-<?= $post_id; ?>.list-group-item:hover { background-color:<?= $post_color ?> !important; } 
				</style>
			<?php endforeach; ?>
			
				</div>
			</div>
		</div>
	</div>
	</section>
	<!-- END: #Featured / Bold style -->
	<?php
	endif;
}

function wa_partners_callback( $attributes, $is_preview = false, $post_id = null ) {

	$partner_post_type 	= ( post_type_exists('partenaire') )?'partenaire':'partner'; // Depreciated WAFFTWO V1 
	$partner_category 	= ( post_type_exists('partenaire') )?'partenaire-category':'partner-category'; // Depreciated WAFFTWO V1 
	$partner_field 		= ( post_type_exists('partenaire') )?'p-link':'waff_partner_link'; // Depreciated WAFFTWO V1 
	$partner_field 		= ( defined('WAFF_THEME') && 'RSFP' === WAFF_THEME && post_type_exists('partner') )?'p_general_link':$partner_field; // Special RSFP 

	// print_r($attributes);
	// if ( $is_preview ) 
	// 	print_r($attributes);

	global $current_edition_id, $current_edition_films_are_online;

	// Fields data.
	if ( empty( $attributes['name'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'partners mt-1 mb-1 contrast--light';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	?>
	<!-- #Partners -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">

				<?php 
				$categories 	= array();
				$posttype 		= $partner_post_type;
				//$categories 	= $attributes['data']['waff_pn_categories']; 
				$categories 	= mb_get_block_field( 'waff_pn_categories' ); 
				// print_r($categories);

				foreach($categories as $category) {

					//$term = get_term_by( 'id', $category, $partner_category );
					if ( !empty($category) ) {
						?>

						<p class="subline mt-6 mb-2"><?= $category->name; ?></p>
						<hr/>
						<div class="row g-0 align-items-top">

						<?php
					}

					if ( $current_edition_id !== NULL && taxonomy_exists( 'edition') ) {
						$args = array( 
							'numberposts' 		=> -1, // No limit
							'post_status' 		=> 'publish', // Show only the published posts
							'orderby'			=> 'post_date',
							'order'				=> 'DESC',
							'post_type'			=> $posttype,
							// Limit to selected cats and edition
							'tax_query' => array(
								array(
									'taxonomy' => 'edition',
									'field' => 'term_id', 
									'terms' => $current_edition_id,
									'include_children' => false
								),
								array(
									'taxonomy' => $partner_category,
									'field' => 'term_id',
									'terms' => $category->term_id, // Fixed from DINARD SEPT23
									'operator' => 'IN'
								),
							)
						);
					} else {
						$args = array( 
							'numberposts' 		=> -1, // No limit
							'post_status' 		=> 'publish', // Show only the published posts
							'orderby'			=> 'post_date',
							'order'				=> 'DESC',
							'post_type'			=> $posttype,
							// Limit to selected cats and edition
							'tax_query' => array(
								array(
									'taxonomy' => $partner_category,
									'field' => 'term_id',
									'terms' => $category->term_id, // Fixed from DINARD SEPT23
									'operator' => 'IN'
								),
							)
						);
					}

					$partners = get_posts($args);
	
					foreach( $partners as $post ) : 

						$id 		= (( $post->ID )?$post->ID:get_the_ID());

						// DEPRECIATED WAFFTWO V.1 = FIFAM : p-link / DINARD : waff_partner_link
						if ( post_type_exists('partenaire') ) 
						$link 		= types_render_field( $partner_field, array('id' => $id) ); 
						else 
						$link 		= get_post_meta( $id, $partner_field, true );

						// Post Thumbnail
						$featured_img_urls = array();
						$partenaire_featured_sizes = array(
							//'full',
							'medium_large', // 768px
							'medium', // 300px
							'partenaire-featured-image', // 150px
							'partenaire-featured-image-x2', // 200px
						);
						$selected_featured_sizes = $partenaire_featured_sizes;
						if ( has_post_thumbnail($post) ) {  //is_singular() &&
							$featured_img_id     		= get_post_thumbnail_id($post);
							$featured_img_url_full 		= get_the_post_thumbnail_url($post);
							foreach ($selected_featured_sizes as $size) {
								$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
								$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full; 
							}
						}
						$featured_img_caption = $post->post_title;
						
					?>
						
						<div id="p-<?= $id ?>" class="col-3 col-sm-2 partner-slide-item d-inline-block p-2 p-sm-4">
							<a href="<?= esc_url($link) ?>" class="color-black link link-dark" title="<?php echo $post->post_title; ?>">
								<figure title="<?php echo esc_attr($featured_img_description); ?>">
									<picture class="lazy">
									<!-- Breakpoint : xl -->
									<data-src media="(min-width: 767px)"
											srcset="<?= $featured_img_urls['medium_large']; ?>" type="image/jpeg"></data-src>
									<!-- Breakpoint : lg -->
									<data-src media="(min-width: 299px)"
											srcset="<?= $featured_img_urls['medium']; ?>" type="image/jpeg"></data-src>
									<!-- Breakpoint : sm -->
									<data-src media="(min-width: 149px)"
											srcset="<?= $featured_img_urls['partenaire-featured-image']; ?>" type="image/jpeg"></data-src>
									<data-img src="<?= $featured_img_urls['partenaire-featured-image']; ?>" alt="<?= esc_html($featured_img_caption); ?>" class="img-fluid" style="object-fit: cover; width: 100%;"></data-img> <!-- style="height: 600px;" -->
									</picture>
									<!--
									Sizes :
									<?php print_r($featured_img_urls); ?>  
									-->
								</figure>
							</a>
						</div>

					<?php endforeach; 

					?>
					</div>
					<?php
				}
			?>
		</div>
	</section>
	<!-- END: #Partners -->
	<?php
}

function wa_edito_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// print_r($attributes);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	//$themeClass = 'edito mt-10 mb-10 contrast--light'; 
	//$themeClass = 'edito mt-10 mb-10 contrast--light fix-vh-100';
	$themeClass = 'edito mt-md-10 mb-md-10 mt-5 mb-5 contrast--light fix-vh-100'; // Responsive issue fix
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	$image = mb_get_block_field('waff_e_image');

	$hide_center_column				= (mb_get_block_field( 'waff_e_hide_center_column' ))?'1':'0'; 


	if ( mb_get_block_field( 'waff_e_framed' ) == 0 || mb_get_block_field( 'waff_e_framed' ) == null ) :
	?>
	<!-- #Edito / Normal version -->
	<section id="<?= $id ?>" class="fix-vh-100 <?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">
				<div class="<?= ( $hide_center_column != '1' )?'col-md-5':'col-md-6'; ?> d-flex flex-column justify-content-center min-h-100" data-aos="fade-right" <?= $is_preview?'style="float:left;"':''; ?>>
					<div class="p-4">
						<?php if ( mb_get_block_field( 'waff_e_editionbadge' ) == 1 ) echo waff_get_edition_badge(); ?>
					</div>
					<div class="p-4">
						<h2 class="mb-2"><?= mb_get_block_field( 'waff_e_title' ) ?></h2>
					</div>
					<div class="p-4">
						<article class="edito">
							<p class="lead mb-5"><span class="h6 headline d-inline"><?= mb_get_block_field( 'waff_e_subtitle' ) ?></span> <?= waff_do_markdown(mb_get_block_field( 'waff_e_leadcontent' )) ?></p>
							<?= waff_do_markdown(mb_get_block_field( 'waff_e_content' )) ?>
							<?php if ( mb_get_block_field( 'waff_e_morelink' ) == 1 ) : ?>
								<a class="btn btn-outline-dark mt-4" href="<?= mb_get_block_field( 'waff_e_moreurl' ) ?>"><?php _e('Discover...', 'waff'); ?></a>
							<?php endif; ?>
						</article>
					</div>
				</div>
				<?php if ( $hide_center_column != '1' ) : ?><div class="col-md-2 d-none d-md-block bg-secondary" <?= $is_preview?'style="display:none;"':''; ?>></div><?php endif; ?>
				<div class="<?= ( $hide_center_column != '1' )?'col-md-5':'col-md-6'; ?> overflow-hidden bg-bgcolor" data-aos="fade-left" <?= $is_preview?'style="float:right;"':''; ?>>
					<?php if ( count($image) > 0 ) : ?>
						<?php foreach ( $image as $im ) : ?>
							<figure class="img-shifted shift-right vh-100" <?= $is_preview?'style="margin:0;"':''; ?>>
								<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $im['full_url'] ?>');">
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
								<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
									<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
									<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
										<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
									</span>
								</figcaption>
								<?php endif; /* If captions */ ?>
								</div>	
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<!-- END: #Edito / Normal version -->
	<?php
	endif; 
	
	if ( mb_get_block_field( 'waff_e_framed' ) == 1 ) :
	?>
	<!-- #Edito / Framed version -->
	<section id="<?= $id ?>" class="<?= ( mb_get_block_field( 'waff_e_fit' ) == 1 )?'':'fix-vh-75' ?> px-4 px-sm-8 px-md-10 <?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid border border-transparent-color-silver px-0">
			<div class="row g-0 align-items-center" <?= $is_preview?'style="border:1px solid silver;display:flex;"':''; ?>>
				<div class="col-md-6 overflow-hidden bg-bgcolor" data-aos="fade-left" <?= $is_preview?'style="float:left;"':''; ?>> 
					<?php if ( count($image) > 0 ) : ?>
						<?php foreach ( $image as $im ) : ?>
							<figure class="<?= ( mb_get_block_field( 'waff_e_fit' ) == 1 )?'':'img-shifted shift-right vh-75' ?>" <?= $is_preview?'style="margin:0;"':''; ?>>
								<?php if ( mb_get_block_field( 'waff_e_fit' ) == 1 ) : ?>
									<img class="w-100" src="<?php echo $im['full_url'] ?>" />
									<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
										<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
									<?php endif; /* If captions */ ?>
								<?php else : ?>
									<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $im['full_url'] ?>');">
									<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
										<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
									<?php endif; /* If captions */ ?>
									</div>	
								<?php endif; ?>
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
				<div class="col-md-6 d-flex flex-column align-items-center justify-content-center text-center min-h-100 px-4" data-aos="fade-right" <?= $is_preview?'style="float:right;text-align: center;"':''; ?>>
					<div class="p-3">
						<?php if ( mb_get_block_field( 'waff_e_editionbadge' ) == 1 ) echo waff_get_edition_badge(); ?>
					</div>
					<div class="p-3">
						<h2 class="mb-2"><?= mb_get_block_field( 'waff_e_title' ) ?></h2>
					</div>
					<div class="p-3">
						<article class="edito">
							<p class="lead mb-5"><span class="h6 headline d-inline"><?= mb_get_block_field( 'waff_e_subtitle' ) ?></span> <?= waff_do_markdown(mb_get_block_field( 'waff_e_leadcontent' )) ?></p>
							<?= waff_do_markdown(mb_get_block_field( 'waff_e_content' )) ?>
							<?php if ( mb_get_block_field( 'waff_e_morelink' ) == 1 ) : ?>
								<a class="btn btn-outline-dark mt-4" href="<?= mb_get_block_field( 'waff_e_moreurl' ) ?>"><?php _e('Discover...', 'waff'); ?></a>
							<?php endif; ?>
						</article>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- END: #Edito / Framed version -->
	<?php
	endif; 

}

function wa_awards_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	//print_r($attributes);
	//global $current_edition_id, $current_edition_films_are_online;

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'awards mt-5 mt-sm-10 mb-5 mb-sm-10 contrast--light';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Get terms awards
	$master_awards = array();
	$master_awards_id = array();
	$awards = array();
	$awards_id = array();

	// Master awards
	$master_awards_args = array(
		'taxonomy' => 'award',
		'posts_per_page' => -1,
		'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'wpcf-a-master',
				'compare' => '=',
				'value' => '1',
			),
			array(
				'key' => 'wpcf-a-hide-in-block',
				'compare' => '!=',
				'value' => '1',
			),
		),
	);
	$master_awards = get_terms( $master_awards_args );
	if ( ! empty( $master_awards ) && ! is_wp_error( $master_awards ) ) {
        $master_awards_id = wp_list_pluck( $master_awards, 'term_id' );
	}
	//echo "blocks.php:: master awards IDs"; print_r($master_awards_id);

	// Awards
	$awards_args = array(
		'taxonomy' => 'award',
		'posts_per_page' => -1,
		'orderby'  => array( 'term_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'wpcf-a-master',
				'compare' => '=',
				'value' => '0',
			),
			array(
				'key' => 'wpcf-a-hide-in-block',
				'compare' => '!=',
				'value' => '1',
			),
		),
	);
	$awards = get_terms( $awards_args );
	if ( ! empty( $awards ) && ! is_wp_error( $awards ) ) {
		$awards_id = wp_list_pluck( $awards, 'term_id' );
	}
	//echo "blocks.php:: awards IDs"; print_r($awards_id);

	// Get posts
	$master_awards_films = array();
	$awards_films = array();
	$edition = (int)$attributes['data']['waff_a_edition'];
	/*if ( !isset($edition) ) 
		echo esc_html__( 'Please choose an edition', 'waff' );*/
	$edition = ( isset($edition) && $edition != null && $edition != 0 )?$edition:$current_edition_id;

	// Master awards Films
	foreach( $master_awards_id as $a_id ) {
		$master_awards_films_args = array(
			'post_type' => 'film',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			// In edition
			'tax_query' => array(
				'relation' => 'AND',
				array (
					'taxonomy' => 'award',
					'field' => 'term_id',
					'terms' => $a_id, //array_values($mai)
				),
				array (
					'taxonomy' => 'edition',
					'field' => 'term_id',
					'terms' => array($edition),
				),
			),
			// Order by 
			'orderby'  => array( 'menu_order' => 'DESC', 'date' => 'DESC' ),
		);
		// Filter to order by taxonomy 
		//add_filter('posts_orderby', __NAMESPACE__ . "\\edit_posts_orderby_award" );
		//add_filter('posts_clauses', __NAMESPACE__ . "\\edit_posts_orderby_award_clauses", 10, 2);
		$master_awards_films[] = get_posts( $master_awards_films_args ); 
	}
	//remove_filter('posts_orderby',  __NAMESPACE__ . "\\edit_posts_orderby_award" );
	//echo "blocks.php:: Films IDs"; echo '<pre>'.print_r($master_awards_films, true).'</pre>';

	// Awards Films
	foreach( $awards_id as $a_id ) {
		$awards_films_args = array(
			'post_type' => 'film',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			// In edition
			'tax_query' => array(
				'relation' => 'AND',
				array (
					'taxonomy' => 'award',
					'field' => 'term_id',
					'terms' => $a_id, //array_values($awards_id),
				),
				array (
					'taxonomy' => 'edition',
					'field' => 'term_id',
					'terms' => array($edition),
				),
			),
			// Order by 
			'orderby'  => array( 'term_order' => 'DESC', 'menu_order' => 'DESC', 'date' => 'DESC' ), //'term_taxonomy_id' => 'DESC',
		);
		// Filter to order by taxonomy 
		//add_filter('posts_orderby', __NAMESPACE__ . "\\edit_posts_orderby_award" );
		$awards_films[] = get_posts( $awards_films_args ); 
	}
	//remove_filter('posts_orderby',  __NAMESPACE__ . "\\edit_posts_orderby_award" );
	//echo "blocks.php:: Films IDs"; echo '<pre>'.print_r($awards_films, true).'</pre>';
	
	$empty_awards = '';
	if ( 
		( empty( $master_awards_films ) || is_wp_error( $master_awards_films ) )
		&&
		( empty( $awards_films ) || is_wp_error( $awards_films ) )
	) {
		$empty_awards = esc_html__( 'Be patient ! Awards has not been published yet', 'waff' );
	}

	// Get edition year
	$edition_year 			= get_term_meta( $edition, 'wpcf-e-year', true );
	$edition_name 			= get_term( $edition )->name;

	// Get section by edition year
	$all_section_args = array(
		'taxonomy' => 'section',
		'posts_per_page' => -1,
		'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'meta_query' => array(
			array(
				'key' => 'wpcf-select-edition',
				'compare' => '=',
				'value' => $edition,
			),
		),
	);
	$the_edition_section = get_terms( $all_section_args );
	$terms_list = array();
	if ( ! empty( $the_edition_section ) ) :
		foreach( $the_edition_section as $term ) {
			$termcolor 		= get_term_meta( $term->term_id, 'wpcf-s-color', true );
			$terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
				(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.';"':''),
				esc_url(get_term_link($term)),
				esc_html__($term->name),
				esc_html__($term->name)
			);
		}
	endif;

	$display = mb_get_block_field( 'waff_a_display' );
	$display_master_awards 	= ( $display == 2 )?false:true; 
	$display_awards 		= ( $display == 1 )?false:true; 

	?>
	<!-- #Awards -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
			<hgroup class="text-center">
				<h6 class="headline d-inline-block"><?= mb_get_block_field( 'waff_a_title' ) ?></h6>
				<h1 class="award-title mt-0 mb-0 display-1"><?= $edition_year; ?></h1>
				<?php
					if ( $terms_list ) {
						printf(
							/* translators: %s: list of categories. */
							'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
							esc_html__( 'Categorized as', 'waff' ),
							implode($terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
						);
					}
				?>
				<h6 class="visually-hidden">Le palmarès de l'édition <?= $edition_name; ?> du Festival Internationnal du Film d'Amiens</h6>
			</hgroup>

			<?php if ( mb_get_block_field( 'waff_a_leadcontent' ) ) : ?>
			<p class="lead mt-2 mt-sm-6 text-center"><?= waff_do_markdown(mb_get_block_field( 'waff_a_leadcontent' )) ?></p>
			<?php endif; ?>

			<?php if ( mb_get_block_field( 'waff_a_content' ) ) : ?>
			<div class="mt-1 mt-sm-3 text-center w-75 m-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_a_content' )) ?></div>
			<?php endif; ?>

			<!-- Empty -->
			<?php if ( $empty_awards != '' ) : ?>
			<div class="alert text-center">
				<p><?= $empty_awards ?></p>
			</div>
			<?php endif; ?>

			<!-- Master awards -->
			<?php if ( ! empty( $master_awards_films ) && ! is_wp_error( $master_awards_films )	&& $display_master_awards == true ) : ?>
			<section class="text-center">
				<hr class="vertical-separator h-80-px mt-2 mb-2 bg-transparent" size></hr>
				<p class="headline d-inline-block mx-auto"><?= esc_html__( 'Master awards', 'waff' ); ?></p>
			</section>

			<?php print(wa_awards_get_films($master_awards_films, true)); ?>

			<?php endif; // END:: if Master awards ?>

			<!-- Awards -->
			<?php if ( ! empty( $awards_films ) && ! is_wp_error( $awards_films ) && $display_awards == true ) : ?>
			<section class="text-center">
				<hr class="vertical-separator h-80-px mt-2 mb-2 bg-transparent" size></hr>
				<p class="headline d-inline-block mx-auto d-none"><?= esc_html__( 'Awards', 'waff' ); ?></p>
			</section>

			<?php print(wa_awards_get_films($awards_films, false)); ?>

			<?php endif; // END:: if Awards ?>

			<!-- More button-->
			<?php if ( mb_get_block_field( 'waff_a_morelink' ) == 1 ) : ?>
			<div class="--d-grid --gap-2 mt-2 mt-sm-6 mb-2 mb-sm-6 text-center">
				<a class="btn btn-outline-dark mt-4" href="<?= mb_get_block_field( 'waff_a_moreurl' ) ?>"><?= esc_html__( 'All the awards', 'waff' ); ?></a>
			</div>
			<?php endif; ?>

		</div>
	</section>
	<!-- END: #Awards -->
	<?php
}

function wa_awards_get_films( $films, $master = true ) {
	$html = '<div class="row award-list ' . (($master == true)?'master-':'') . 'awards">';
	$counter=0;
	$idx=0;
	//echo"<pre>".print_r($films, true)."</pre>";
	
	// Count array films 
	array_walk_recursive($films, function($value, $key) use (&$counter) {
		$counter++;
		//echo print_r($value,true) . " : " . $counter;  
	 }, $counter);
	 //echo "counter : " . $counter; 
	
	foreach($films as $_films) { // Foreach 1
		foreach($_films as $film) { // Foreach 2 Object/Array in array fix
			$idx++;
			$f_id						= $film->ID;
			$f_title 					= (( $film->post_title )?get_the_title($f_id):$film->post_title);
			$f_french_operating_title 	= get_post_meta( $f_id, 'wpcf-f-french-operating-title', true );
			$f_movie_length 			= get_post_meta( $f_id, 'wpcf-f-movie-length', true );
			$f_author 					= get_post_meta( $f_id, 'wpcf-f-author', true );
			$f_author 					= esc_html($f_author['firstname']) . ' <strong>' . esc_html($f_author['lastname']) . '</strong>';
			$f_director_contact 		= get_post_meta( $f_id, 'wpcf-c-e-director-contact', true );
			$f_color 					= get_post_meta( $f_id, 'film-color', true );

			$featured_img_caption = '';
			$featured_img_description = '';

			$f_image = '';
			// Featured image
			$featured_img_urls = array();
			if ( has_post_thumbnail($f_id) ) { 
				$post_featured_sizes = array(
					'thumbnail',
					'post-featured-image-s', 
					'post-featured-image-s-x2',
					'post-featured-image-m', 
					'post-featured-image-m-x2',
				);
				$featured_img_id     		= get_post_thumbnail_id($f_id);
				$featured_img_url_full 		= get_the_post_thumbnail_url($f_id);
				foreach ($post_featured_sizes as $size) {
					$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
					$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full; 
				}
				$alt = get_post_meta ( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
				$featured_img_caption = wp_get_attachment_caption($featured_img_id); // ADD WIL                    
				$thumb_img = get_post( $featured_img_id ); // Get post by ID
				$featured_img_description =  $thumb_img->post_content; // Display Description
				// Render image 
				$f_image = sprintf('<figure class="w-100 %s fit-image" %s>
						<picture class="lazy">
						<data-src media="(min-width: 399px)"
								srcset="%s 2x,
										%s" type="image/jpeg"></data-src>
						<data-src media="(min-width: 149px)"
								srcset="%s 2x,
										%s" type="image/jpeg"></data-src>
						<data-img src="%s" class="w-100 img-fluid %s fit-image" alt="%s"></data-img>
						</picture>
						%s
					</figure>',
					($master == true)?'h-340-px':'h-200-px',
					( $featured_img_description )?'title="'.esc_html($featured_img_description).'"':'title="'.esc_html($f_title).'"',
					$featured_img_urls['post-featured-image-s-x2'],
					$featured_img_urls['post-featured-image-s'],
					$featured_img_urls['post-featured-image-xs-x2'],
					$featured_img_urls['post-featured-image-xs'],
					$featured_img_urls['thumbnail'],
					($master == true)?'h-340-px':'h-200-px',
					( $featured_img_caption )?esc_html($featured_img_caption):esc_html($f_title),
					( $featured_img_caption || $featured_img_description )?'<figcaption><strong>© '. esc_html($featured_img_caption) .'</strong> '. esc_html($featured_img_description) .'</figcaption>':'',
				);
			}

			// Poster image
			$f_poster = '';
			$f_poster 								= get_post_meta( $f_id, 'wpcf-f-film-poster', true ); // Medium large
			if ( $master == true && $f_poster != '' ) { 
				$f_poster 							= get_post_meta( $f_id, 'wpcf-f-film-poster', true ); // Medium large
				$f_poster_ID 						= waff_get_image_id_by_url($f_poster);
				$featured_img_caption 				= wp_get_attachment_caption($f_poster_ID); // ADD WIL
				$thumb_img 							= get_post($f_poster_ID); // Get post by ID
				$featured_img_description 			= $thumb_img->post_content; // Display Description
				if ( function_exists( 'types_render_field' ) ) {
					$f_poster 						= types_render_field( 'f-film-poster', 
						array( 'item' => $f_id, 'size' => 'medium_large', 'alt' => esc_html($featured_img_caption), 'class' => 'w-100 img-fluid fit-image ' . (($master == true)?'h-340-px':'h-200-px' ) )
					); //'width' => '28', 'height' => '28', 'proportional' => 'false', 
				}
				// Render image 
				$f_image = sprintf('<figure class="w-100 %s fit-image" %s>
						<picture class="lazy">
							%s
						</picture>
						%s
					</figure>',
					($master == true)?'h-340-px':'h-200-px',
					( $featured_img_description )?'title="'.esc_html($featured_img_description).'"':'title="'.esc_html($f_title).'"',
					$f_poster,
					( $featured_img_caption || $featured_img_description )?'<figcaption><strong>© '. esc_html($featured_img_caption) .'</strong> '. esc_html($featured_img_description) .'</figcaption>':'',
				);
			}

			// Get terms
			$f_sections 				= get_the_terms( $f_id, 'section' );
			$html_f_section = '';
			if ( ! empty( $f_sections ) && ! is_wp_error( $f_sections ) ) {
				$html_f_section .= ($master == false)?'<div class="section-list d-inline cat-links">':'';
				foreach($f_sections as $f_section) {
					$f_section_color 	= get_term_meta( $f_section->term_id, 'wpcf-s-color', true );
					$f_section_edition 	= get_term_meta( $f_section->term_id, 'wpcf-select-edition', true );
					
					if ($master == true)
						$html_f_section .= sprintf('<a href="%s" %s class="dot-section" data-toggle="tooltip" data-container=".modal" data-title="%s" data-original-title="" title="">•</a>',
							esc_url(get_term_link($f_section->slug, 'section')),
							(( $f_section_color != '' )?'style="color: '.$f_section_color.';"':''),
							esc_html__($f_section->name)
						);
					else
						$html_f_section .= sprintf('<a href="%s" class="section-item" %s title="%s">%s</a>',
							esc_url(get_term_link($f_section, 'section')),
							(( $f_section_color!='' )?'style="background-color:'.$f_section_color.';border-color:'.$f_section_color.'"':''),
							esc_html__($f_section->name),
							esc_html__($f_section->name)
						);
				}
				$html_f_section .= ($master == false)?'</div>':'';
			}
			
			// Get award
			$f_awards 				= get_the_terms( $f_id, 'award' );
			$f_award_light_img = '';
			$f_award_dark_img = '';
			$f_award_name = '';
			$html_award_img = '';
			$html_award_title = '';
			$f_awards_count = count($f_awards);
			if ( ! empty( $f_awards ) && ! is_wp_error( $f_awards ) ) foreach ($f_awards as $f_award) {
				$f_award_name 		= $f_award->name; 
				$f_awards_is_master = get_term_meta( $f_award->term_id, 'wpcf-a-master', true );
				if ( $f_awards_is_master == (int)$master) {
					$f_award_light_img 	= get_term_meta( $f_award->term_id, 'wpcf-a-light-image', true );
					$f_award_dark_img 	= get_term_meta( $f_award->term_id, 'wpcf-a-dark-image', true );
					// print_r('MASTER:'.(int)$master);
					// print_r('IS_MASTER:'.$f_awards_is_master);
					$html_award_img 	.= '<img src="'.(($master == true)?$f_award_light_img:$f_award_dark_img).'" class="w-100 '.(($f_awards_count>2)?'mw-80-px':'mw-180-px').' fit-image mb-4 mb-sm-3" alt="'.(($f_award_name)?$f_award_name:__( 'Award', 'waff' )).'">';
					$html_award_title 	.= '<h6>'.(($f_award_name)?$f_award_name:__( 'Award', 'waff' )).'</h6>';
				}
				//if ( in_array( $f_award->term_id, $master_awards) )
					//print_r($f_award->term_id);
			}
			// print_r($html_award_img);
			// print_r($html_award_title);
			//var_dump($f_award_light_img);
			//print_r($counter);//count($films)

			$html .= '<div class="col-12 col-md-' . ( ($master == true)?ceil(12/$counter):3 ) . ' award-item ' . (($master == true)?'master-':'') . 'award">';

			// Print film / <img src="%s" class="w-100 %s fit-image" alt="%s">
			$html .= sprintf('<div class="card film-card flex-row flex-wrap bg-color-dark my-2 border-0 %s" %s data-film-id="%d" data-aos="flip-up" data-aos-delay="%d">
				<!-- Film -->
				<div class="card-featured overflow-hidden %s">
					<a href="%s" class="d-flex flex-column flex-wrap align-items-start justify-content-middle h-100 w-100 bg-bgcolor-lighten">
						%s
					</a>
				</div>
				<div class="card-body %s d-flex flex-column justify-content-center text-center %s">
					<div>
						%s
						%s
						<h5 class="card-title mb-0"><a href="%s" class="text-link">%s</a> %s</h5>
						%s
					</div>
					<div class="pt-3">
						<a %s class="card-text">%s</a>
					</div>
					<div>
						<a href="%s" class="card-link link-black stretched-link pt-2 d-none"><i class="icon icon-arrow"></i></a>
					</div>
				</div>
				<!-- Ribbon -->
				<div class="ribbon-wrapper d-none"><div class="ribbon">Special</div></div>
			</div>',
				($master == true)?'h-340-px shadow-lg card-light':'h-400-px shadow-sm card-dark',
				( $f_color != '' )?'style="background-color:#'.$f_color.';"':'style="background:'.(($master == true)?'black':'white').';"',
				esc_attr( $f_id ),
				$idx*100,
				//
				($master == true)?'w-45':'h-50',
				esc_url(get_permalink( $f_id )),
				$f_image,
				//
				(count($films) > 3)?'p-3':'p-4',
				($master == true)?'w-55':'h-50',
				( $f_award_light_img != '' && $f_award_dark_img != '' )?$html_award_img:'',
				( $f_award_light_img == '' && $f_award_dark_img == '' )?$html_award_title:'',
				esc_url(get_permalink( $f_id )),
				( $f_french_operating_title != '' )?$f_french_operating_title.' <span class="muted">('.$f_title.')</span>':$f_title,
				( $f_movie_length != '' )?'<span class="length">'.$f_movie_length.'\'</span>':'',
				$html_f_section,
				( $f_director_contact != null || $f_director_contact != '' )?'href="'.get_permalink( $f_director_contact ).'"':'',
				$f_author,
				esc_url(get_permalink( $f_id )),
			);

			$html .= '</div>';
		} // END: Foreach 2
	} // END: Foreach
	$html .= '</div>';

	return $html;
}

/*function edit_posts_orderby_award($orderby_statement) {
	error_log('##AWARDS QUERY :: orderby_statement');
	error_log('<pre>'.print_r($orderby_statement,true).'</pre>');
	$orderby_statement = " tt1.term_order DESC, " . $orderby_statement; //keeps the current orderby, but also adds the term_order in front
	error_log('<pre>'.print_r($orderby_statement,true).'</pre>');
	return $orderby_statement;
}

function edit_posts_orderby_award_clauses( $clauses, $wp_query ) {
	global $wpdb;
	error_log('##AWARDS QUERY :: orderby_statement');
	error_log('<pre>'.print_r($wp_query,true).'</pre>');
	error_log('<pre>'.print_r($clauses,true).'</pre>');
	$taxonomy = 'award';

    if ( isset( $wp_query->query['orderby'] ) && $taxonomy == $wp_query->query['orderby'] ) {
		$clauses['join'] .=<<<SQL
			LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
			LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
			LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
			SQL;
		$clauses['where'] .= " AND (taxonomy = '{$taxonomy}' OR taxonomy IS NULL)";
		// $clauses['groupby'] = "object_id";
		$clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.term_order ORDER BY term_order ASC) ";
		$clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
	}
		
    return $clauses;
}*/

function wa_playlist_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	//print_r($attributes);
	//global $current_edition_id, $current_edition_films_are_online;
	//https://codepen.io/tommydunn/pen/rNxQLNq?editors=1010

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'playlist mt-5 mt-sm-10 mb-5 mb-sm-10 contrast--light';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	?>
	<!-- #Playlist -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
		<div class="container-fluid px-0">
			<hgroup class="text-center">
				<h6 class="headline d-inline-block"><?= mb_get_block_field( 'waff_pl_title' ) ?></h6>
			</hgroup>

			<?php if ( mb_get_block_field( 'waff_pl_leadcontent' ) ) : ?>
			<p class="lead mt-1 mt-sm-3 mb-1 mb-sm-3 text-center w-75 mx-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_pl_leadcontent' )) ?></p>
			<?php endif; ?>

			<?php if ( mb_get_block_field( 'waff_pl_content' ) ) : ?>
			<div class="mt-0 mt-sm-2 mb-1 mb-sm-3 text-center w-75 mx-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_pl_content' )) ?></div>
			<?php endif; ?>

			<?php $classes = ''; if ( mb_get_block_field( 'waff_pl_videos' ) ) :
				$classes 	.= (mb_get_block_field( 'waff_pl_autoplay' ))?' autoplay':''; 
				$classes 	.= (mb_get_block_field( 'waff_pl_fullwidth' ))?' fullwidth':''; 
			?>
			<div id="<?= ( $attributes['id'] ?? '' ) ?>" class="slider-youtube slick-dark<?= $classes; ?>">

				<?php $idx = 0; foreach ( mb_get_block_field( 'waff_pl_videos' ) as $video_link) :
					$playlist = ( mb_get_block_field( 'waff_pl_playlist' ) != '' )?'&listType=playlist&list='.esc_attr(mb_get_block_field( 'waff_pl_playlist' )):'';
					$idx++;
				?>
					<div class="item youtube-sound">
						<iframe id="<?= $idx; ?>" class="embed-player slide-media" src="https://www.youtube.com/embed<?= esc_attr(parse_url($video_link, PHP_URL_PATH)); ?>?enablejsapi=1&controls=0&fs=0&iv_load_policy=3&rel=0&showinfo=0&loop=1&start=1&origin=https://www.fifam.fr<?= $playlist ?>" frameborder="0" allowfullscreen seamless sandbox="allow-scripts allow-same-origin" allow="autoplay"></iframe>
						<div class="slick-button">
							<a class="btn btn-sm" href="<?= $video_link; ?>"><?= __('See video', 'waff'); ?></a>
						</div>
					</div>
				<?php endforeach; ?>

			</div>
			<?php endif; ?>

		</div>
		<!-- Local styles -->
		<style>
			.slider-youtube .item {
				opacity: 1;
				filter: blur(0);
				background: #000;
				<?= ( mb_get_block_field( 'waff_pl_fullwidth' )?'height: 50vh;min-height:400px;':'max-height: 200px;'); ?>
				border-radius:5px; 
				margin:10px;
			}

			.slider-youtube .item:not(.slick-current) {
				opacity: 0.4;
				transition: opacity 1s;
				border-radius:5px; 
				filter: blur(1px);
			}

			.slider-youtube .item iframe {
				width: 100%;
				<?= ( mb_get_block_field( 'waff_pl_fullwidth' )?'height: 50vh;min-height:400px;':'height: 200px;'); ?>
				min-width: 300px;
				<?= ( mb_get_block_field( 'waff_pl_autoplay' )?'pointer-events: none;':''); ?> /* Can slide */
				border-radius:5px; 
			}

			.slick-button {
				position: relative;
				bottom: 60px;
				text-align: center;
				z-index: 9999;
			}

			.slick-button a {
				color: white;
				background-color: black;
			}
		</style>
		<!-- Local scripts -->
		<script>
			// Loads the IFrame Player API code asynchronously.
			var tag = document.createElement('script');

			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

			// POST commands to YouTube or Vimeo API
			function postMessageToPlayer(player, command){
				if (player == null || command == null) return;
				player.contentWindow.postMessage(JSON.stringify(command), "*");
			}

			// INIT
			var slideWrapper = jQuery(".slider-youtube"),
					iframes = slideWrapper.find('.embed-player');

			var autoplay = false;
			var autoplay = document.getElementById('<?= ( $attributes['id'] ?? '' ) ?>').classList.contains('autoplay');
			//console.log(autoplay);

			// When the slide is changing
			function playPauseVideo(slick, control){
				var currentSlide, slideType, startTime, player, video;

				currentSlide = slick.find(".slick-current");
				slideType = currentSlide.attr("class").split(" ")[1];
				player = currentSlide.find("iframe").get(0);
				startTime = currentSlide.data("video-start");

				console.log(slideType);

				if (slideType === "vimeo") {
					switch (control) {
					case "play":
						if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
						currentSlide.addClass('started');
						postMessageToPlayer(player, {
							"method": "setCurrentTime",
							"value" : startTime
						});
						}
						postMessageToPlayer(player, {
						"method": "play",
						"value" : 1
						});
						break;
					case "pause":
						postMessageToPlayer(player, {
						"method": "pause",
						"value": 1
						});
						break;
					}
				} else if (slideType === "youtube-sound") {
					switch (control) {
					case "play":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "playVideo"
						});
						break;
					case "pause":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "pauseVideo"
						});
						break;
					}
				}  else if (slideType === "youtube") {
					switch (control) {
					case "play":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "mute"
						});
						postMessageToPlayer(player, {
						"event": "command",
						"func": "playVideo"
						});
						break;
					case "pause":
						postMessageToPlayer(player, {
						"event": "command",
						"func": "pauseVideo"
						});
						break;
					}
				} else if (slideType === "video") {
					video = currentSlide.children("video").get(0);
					if (video != null) {
					if (control === "play"){
						video.play();
					} else {
						video.pause();
					}
					}
				}
			}

			// Resize player
			// function resizePlayer(iframes, ratio) {
			// 	if (!iframes[0]) return;
			// 	var win = jQuery(".slider-youtube"),
			// 		width = win.width(),
			// 		playerWidth,
			// 		height = win.height(),
			// 		playerHeight,
			// 		ratio = ratio || 16/9;

			// 	iframes.each(function(){
			// 		var current = jQuery(this);
			// 		if (width / ratio < height) {
			// 		playerWidth = Math.ceil(height * ratio);
			// 		current.width(playerWidth).height(height).css({
			// 			left: (width - playerWidth) / 2,
			// 			top: 0
			// 			});
			// 		} else {
			// 		playerHeight = Math.ceil(width / ratio);
			// 		current.width(width).height(playerHeight).css({
			// 			left: 0,
			// 			top: (height - playerHeight) / 2
			// 		});
			// 		}
			// 	});
			// }

			// DOM Ready
			jQuery(function($) {
				// Initialize
				slideWrapper.on("init", function(slick){
					console.log('init /  autoplay : ' + autoplay );
					slick = $(slick.currentTarget);
					setTimeout(function(){
						if ( autoplay == true ) { playPauseVideo(slick,"play"); }
					}, 1000);
					//resizePlayer(iframes, 16/9);
				});
				slideWrapper.on("beforeChange", function(event, slick) {
					console.log('beforeChange /  autoplay : ' + autoplay );
					slick = $(slick.$slider);
					playPauseVideo(slick,"pause");
				});
				slideWrapper.on("afterChange", function(event, slick) {
					console.log('afterChange /  autoplay : ' + autoplay );
					slick = $(slick.$slider);
					if ( autoplay == true ) { playPauseVideo(slick,"play"); }
				});
				/*slideWrapper.on("lazyLoaded", function(event, slick, image, imageSource) {
					lazyCounter++;
					if (lazyCounter === lazyImages.length){
					lazyImages.addClass('show');
					// slideWrapper.slick("slickPlay");
					}
				});*/

				//start the slider
				slideWrapper.slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					dots: true,
					infinite: true,
					<?= ( !mb_get_block_field( 'waff_pl_fullwidth' ))?'centerMode: true,':''  ?>
					// centerMode: true,
					<?= ( !mb_get_block_field( 'waff_pl_fullwidth' ))?'centerPadding: \'50px\',':'' ?>
					// centerPadding: '50px',
					<?= ( !mb_get_block_field( 'waff_pl_fullwidth' ))?'variableWidth: true,':'' ?>
					// variableWidth: true,
					//fade:true,
					//autoplaySpeed:4000,
					//autoplay: true,
					//speed:600,
					//lazyLoad:"progressive",
					cssEase:"cubic-bezier(0.87, 0.03, 0.41, 0.9)"
				});
			});

			// Resize event
			// jQuery(window).on("resize.slickVideoPlayer", function(){  
			// 	resizePlayer(iframes, 16/9);
			// });
		</script>
	</section>
	<!-- END: #Playlist -->
	<?php
}

function wa_contact_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// if ( $is_preview ) 
	// 	print_r($attributes);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'contact mb-10 container-fluid contrast--light';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	$gallery = mb_get_block_field( 'waff_c_gallery' );
	$images = array();
	$index = 0;

	foreach($gallery as $im) {
		if ( $im['full_url' ] != '')
			$images[] = $im;
		else
			$images[] = $images[$index-1];
		$index++; 
	}

	?>
	<!-- #Contact -->
	<section id="<?= $id ?>" class="<?= $class ?> mt-0 mb-0 bg-bgcolor" style="height: 100%;">
		<div class="row f-w g-0">
				<div class="col-8 bg-primary" style="height: 540px;">

					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[0]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[0]['alt'] || $images[0]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[0]['alt']); ?></strong> <?= esc_html($images[0]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>	
					</figure>
			
				</div>
				<div class="col-4"></div>
				
				<div class="col-4"></div>
				<div class="col-4"></div>
				<div class="col-4 bg-action-1" style="height: 780px;margin-top: -250px;">

					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[1]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[1]['alt'] || $images[1]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[1]['alt']); ?></strong> <?= esc_html($images[1]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>	
					</figure>
					<!-- <figure class="img-shifted shift-right h-100">
						<img src="<?php echo $images[1]['sizes']['post-featured-image-m'] ?>"
							 srcset="<?php echo $images[1]['srcset'] ?>" 
							 alt="<?= esc_html($images[1]['alt']); ?>"$
							 class="img-fluid h-100" style="object-fit: cover; height: 100%;"
						>
						<?php $im['alt'] = 'DR'; if ( $images[1]['alt'] || $images[0]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[1]['alt']); ?></strong> <?= esc_html($images[1]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>	
					</figure> -->

				</div>

				<div class="col-4 bg-action-3" style="height: 540px;margin-top: -160px;">
					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[2]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[2]['alt'] || $images[2]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[2]['alt']); ?></strong> <?= esc_html($images[2]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>	
					</figure>
				</div>
				<div class="col-4"></div>
				<div class="col-4"></div>

				
				<div class="col-4"></div>
				<div class="col-8 bg-secondary" style="height: 540px;">
					<figure class="img-shifted shift-right h-100">
						<div class="bg-image bg-cover bg-position-top-center" style="background-image: url('<?php echo $images[3]['full_url'] ?>');">
						<?php $im['alt'] = 'DR'; if ( $images[3]['alt'] || $images[3]['description'] ) : ?>
						<figcaption><strong>© <?= esc_html($images[3]['alt']); ?></strong> <?= esc_html($images[3]['description']); ?></figcaption>
						<?php endif; /* If captions */ ?>
						</div>	
					</figure>
				</div>
		</div>
	</section>
	
	<!-- Begin: Contact content -->
	<section id="contact-section-<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="margin-top: -1820px; position: relative;">
		<div class="row f-w">
				<div class="col-10 col-md-8 offset-1 offset-md-2 p-4 p-md-5 <?= mb_get_block_field( 'waff_c_first_color_class' ) && mb_get_block_field( 'waff_c_first_color_class' ) !== '' ? mb_get_block_field( 'waff_c_first_color_class' ) : 'bg-action-2' ?> <?= mb_get_block_field( 'waff_c_rounded' ) ? 'rounded-top-4' :'' ?>" style="height: 370px;">
					<div class="row">
						<div class="col-12 col-md-6 ">
							<h2 class="heading-4 mb-5 mb-md-0"><?= waff_do_markdown(mb_get_block_field( 'waff_c_first_title' )) ?></h2>
						</div>
						<div class="col-12 col-md-6">
							<div class="row">
								<?= mb_get_block_field( 'waff_c_first_content' ); ?>
							</div>
						</div>
					</div>
					
				</div>
				<div class="col-10 col-md-8 offset-1 offset-md-2 p-4 p-md-5 <?= mb_get_block_field( 'waff_c_second_color_class' ) && mb_get_block_field( 'waff_c_second_color_class' ) !== '' ? mb_get_block_field( 'waff_c_second_color_class' ) : 'bg-secondary' ?>" style="height: 370px;">
					
					<div class="row">
						<div class="col-12 col-md-6 ">
							<h2 class="heading-4 mb-5 mb-md-0"><?= waff_do_markdown(mb_get_block_field( 'waff_c_second_title' )) ?></h2>
						</div>
						<div class="col-12 col-md-6">
							<div class="row">
								<?= mb_get_block_field( 'waff_c_second_content' ); ?>
							</div>
						</div>
					</div>

					
				</div>
				<div class="col-10 col-md-8 offset-1 offset-md-2 bg-light p-4 p-md-5 <?= mb_get_block_field( 'waff_c_rounded' ) ? 'rounded-bottom-4' :'' ?>" style="height: 740px;">
					<?php
					echo do_shortcode('[gravityform id="'.mb_get_block_field( 'waff_c_form' ).'" title="false" description="false" ajax="true" field_values=""]');
					?>
				</div>
		</div>
	</section>
	<div class="clear clearfix"></div>
	<!-- End: Contact content -->

	<!-- END: #Contact -->
	<?php

}

function wa_film_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// if ( $is_preview ) 
	// 	print_r($attributes);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'single-film --alignwide contrast--light';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	$film_ID = mb_get_block_field( 'waff_sf_film' );
	if ( $film_ID != "" ) :
		$promotted 	= (mb_get_block_field( 'waff_sf_promotted' ))?'1':'0'; 

		$promote 	= get_post_meta($film_ID, 'wpcf-f-promote', true);
		$film_color = rwmb_meta( 'waff_film_color', array(), $film_ID );
		$film_color_class = 'contrast--light card-dark';
		if ( ($promotted=='1' || $promote=='1') && isset($film_color) && $film_color != '' ) {
			$rgb = waff_HTMLToRGB($film_color);
			$hsl = waff_RGBToHSL($rgb);
			if($hsl->lightness < $lightness_threshold)
				$film_color_class = 'contrast--dark card-light';
		}
		global $attributes;
		$attributes = array(
			'wrapper' 		=> 'div', // div / li
			'title_wrapper' => (($promotted=='1' || $promote=='1')?'h2':'h5'), // h5 / h6
			// section + projection : div
			// Related-sections : li
			'parent' 		=> 'film', // film / projection
			// section : film
			// Projection in fiche film : projection
			// Related-sections : film
			'class' 		=> 'card film-card flex-row flex-wrap '.(($promotted=='1' || $promote=='1')?'col-md-12 h-520-px':'col-md-6 h-280-px').' bg-light my-2 border-0 shadow-sm '.$film_color_class,
			// section : card film-card flex-row flex-wrap col-md-6 bg-light my-2 border-0 h-280-px shadow-sm card-dark
			// Projection in fiche film : card film-card flex-row flex-wrap col-4 --bg-custom mx-2 my-0 border-0 h-300-px shadow-sm --card-white --p-0
			// Related-sections : card film-card --flex-row flex-wrap bg-light border-0 h-200-px shadow-sm card-dark
			'image_class' => '--w-100 '.(($promotted=='1' || $promote=='1')?'h-520-px':'h-280-px').' fit-image',
			// section : w-100 h-280-px fit-image
			// Projection in fiche film : w-100 h-600-px fit-image
			// Related-sections : w-100 --h-100 h-200-px fit-image
			'image_width' => 'w-60',
			// section : w-60
			// Projection in fiche film : w-50 float-left
			// Related-sections : w-150-px
			'body_width' => 'w-40',
			// section : w-40
			// Projection in fiche film : w-50 h-100
			// Related-sections : w-250-px
			'show_sections' => 'false', // string = false / true
			'show_cats' 	=> 'true', // string = false / true
			'show_excerpt' 	=> 'true', // string = false / true
			'excerpt_length' => '100',
			// section = room : 100
			// Projection in fiche film : 80
			// Related-sections : 60
			'show_rooms' 	=> 'false', // string = false / true
			'items' 		=> '', // string = @film_projection.parent / empty
			// Parent items 
			// Color
			'film_color'	=> ((($promotted=='1' || $promote=='1') && $film_color != '')?$film_color:''),
			// Animation
			'has_animation' => (($animation_class != '')?'false':'true'),
		);
		$subdomain = substr($_SERVER['SERVER_NAME'],0,4);
		$view_id = ( $subdomain == 'dev2.' || $subdomain == 'www.' )?54057:44405;
		if ( defined('WAFF_THEME') && WAFF_THEME == 'DINARD' )
			$view_id = 670;
		?>
		<!-- #Single Film -->
		<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?>>
			<!-- <div class="container-fluid px-0">
				<div class="row g-0 align-items-center py-2 offset-md-2">-->
					<?php echo render_view_template( $view_id, $film_ID ); // ID de la vue Film card / film-card ?>
				<!-- </div>
			</div>-->
		</section>
		<!-- END: #Single Film -->
		<?php
	endif;
}

function wa_section_callback( $attributes, $is_preview = false, $post_id = null ) {
	global $current_edition_slug; 
	
	// if ( $is_preview ) 
	// 	print_r($attributes);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'section-slideshow caroussel mt-10 mb-10 bg-dark contrast--dark color-light';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	$section 				= mb_get_block_field( 'waff_ss_section' );
	$section_id 			= $attributes['data']['waff_ss_section'];
	$section_slug 			= $section[0]->slug;
	$use_section_color 		= (mb_get_block_field( 'waff_ss_section_color' ))?'1':'0'; 

	if ( $section_id != "" ) :

		// Get term section
		$html_section = '';
		if ( ! empty( $section ) && ! is_wp_error( $section ) ) {
			$html_section .= '<div class="section-list d-inline cat-links">';
			foreach($section as $s) {
				$s_color 	= get_term_meta( $s->term_id, 'wpcf-s-color', true );
				$s_edition 	= get_term_meta( $s->term_id, 'wpcf-select-edition', true );
				$html_section .= sprintf('<a href="%s" class="section-item" %s title="%s">%s</a>',
					esc_url(get_term_link($s, 'section')),
					(( $s_color!='' )?'style="background-color:'.$s_color.';border-color:'.$s_color.'"':''),
					esc_html__($s->name),
					esc_html__($s->name)
				);
			}
			$html_section .= '</div>';
		}

		$section_color 						= get_term_meta( $section_id, 'wpcf-s-color', true ); 
		$section_color_class				= 'contrast--light card-dark color-dark';
		if ( $use_section_color == '1' && $section_color != '' ) {
			$rgb = waff_HTMLToRGB($section_color);
			$hsl = waff_RGBToHSL($rgb);
			if($hsl->lightness < 200) {
				$section_color_class 		= 'contrast--dark card-light color-light';
			}
		}

		// print_r($section);
		// print_r($section[0]);
		// print_r($section_id);
		// print_r($section_slug);
		// echo $section_color;
		// echo $section_color_class;
		
		global $attributes;

		// <div class="card film-card flex-row flex-wrap col-10 bg-custom my-0 border-0 h-600-px shadow-sm card-white p-0" style="background-color: #d54100 !important;">
		// 	<!-- Film -->
		// 	<div class="card-featured overflow-hidden w-50 float-start">
		// 		<a class="d-flex flex-column flex-wrap align-items-start justify-content-middle h-100 w-100 bg-secondary">
		// 			<img data-srcset="img/carousel/1-1200x1200.jpg 2x, img/carousel/1-600x600.jpg" data-lazy="img/carousel/1-600x600.jpg" data-sizes="" class="w-100 h-600-px fit-image" alt="">
		// 		</a>
		// 	</div>
		// 	<div class="card-body p-4 d-flex flex-column justify-content-between w-50 h-100">
		// 		<div>
		// 			<h5 class="card-title mb-0"><a href="#" class="text-link">Arguments</a> <span class="length">108'</span></h5>
		// 			<div class="section-list"><a class="section-item" style="color: #d54100 !important;">Coups de coeur</a></div>
		// 		</div>
		// 		<div>
		// 			<div class="category-list"><a class="category-item">Long-métrage</a><a class="category-item">Documentaire</a></div>
		// 			<p class="card-text d-none d-sm-block"><small>Ce documentaire est consacré aux derniers témoignages des résistants déportés NN, rescapés du camp de concentration de Natzweiler-Struthof. C'est aussi l'histoire de ce camp, le seul installé en France, l'un des plus meurtriers du système nazi. C'est le récit des arrestations de ces jeunes résistants et de leurs souffrances vécues pendant leur déportation.</small></p>
		// 		</div>
		// 		<div>
		// 			<div class="room-list">
		// 				<a class="room-item">Petit théâtre</a>
		// 				<a class="parentroom-item">Maison de la culture d'Amiens</a>
		// 			</div>
		// 			<a href="#" class="card-link link-black stretched-link pt-2 d-block"><i class="icon icon-arrow"></i></a>
		// 		</div>
		// 	</div>
		// </div>

		$attributes = array(
			'wrapper' 		=> 'div', // div / li
			'title_wrapper' => 'h4', // h5 / h6
			// section + projection : div
			// Related-sections : li
			'parent' 		=> 'film', // film / projection
			// section : film
			// Projection in fiche film : projection
			// Related-sections : film
			'class' 		=> '###CAROUSEL### card film-card flex-row flex-wrap col-md-10 h-600-px bg-light my-0 p-0 border-0 shadow-sm '.$section_color_class,
			// section : card film-card flex-row flex-wrap col-md-6 bg-light my-2 border-0 h-280-px shadow-sm card-dark
			// Projection in fiche film : card film-card flex-row flex-wrap col-4 --bg-custom mx-2 my-0 border-0 h-300-px shadow-sm --card-white --p-0
			// Related-sections : card film-card --flex-row flex-wrap bg-light border-0 h-200-px shadow-sm card-dark
			// Carousel : card film-card flex-row flex-wrap col-10 bg-custom my-0 border-0 h-600-px shadow-sm card-white p-0
			'image_class' => 'w-100 h-600-px fit-image',
			// section : w-100 h-280-px fit-image
			// Projection in fiche film : w-100 h-600-px fit-image
			// Related-sections : w-100 --h-100 h-200-px fit-image
			// Carousel : w-100 h-600-px  fit-image
			'image_width' => 'w-50 float-start',
			// section : w-60
			// Projection in fiche film : w-50 float-left
			// Related-sections : w-150-px
			// Carousel : w-50 float-start
			'body_width' => 'w-50 h-100',
			// section : w-40
			// Projection in fiche film : w-50 h-100
			// Related-sections : w-250-px
			// Projection in fiche film : w-50 h-100
			'show_sections' => 'false', // string = false / true
			'show_cats' 	=> 'true', // string = false / true
			'show_excerpt' 	=> 'true', // string = false / true
			'excerpt_length' => '100',
			// section = room : 100
			// Projection in fiche film : 80
			// Related-sections : 60
			'show_rooms' 	=> 'true', // string = false / true
			'items' 		=> '', // string = @film_projection.parent / empty
			// Parent items 
			// Color
			'film_color'	=> (($use_section_color=='1' && $section_color != '')?$section_color:''),
			// Animation
			'has_animation' => (($animation_class != '')?'false':'true'),
		);
		?>
		<!-- #Carrousel section -->
		<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?>>
			<div class="carousel-header container px-0 text-light">
				<div class="row">
					<div class="col-11 p-4">
						<hgroup id="carousel-title" class="py-4">
							<?= $html_section ?>
							<h2 class="color-light"><?= mb_get_block_field( 'waff_ss_title' ) ?></h2>
						</hgroup>
					</div>
					<div class="col-1 p-4 position-relative">
						<div class="slick-carousel-arrows"><button class="slick-prev slick-arrow" aria-label="Previous" type="button" style="">Previous</button><button class="slick-next slick-arrow" aria-label="Next" type="button" style="">Next</button></div>
					</div>
				</div>
			</div>
			<div class="carousel-items text-dark">
				<!-- Slick slide -->
				<div class="slick-carousel card-deck w-100 m-0">

					<!-- FILM-CARD CALLED BY A VIEW -->
					<?php
					$subdomain = substr($_SERVER['SERVER_NAME'],0,4);
					$view_id = ( $subdomain == 'dev2.' || $subdomain == 'www.' )?66660:0;
					if ( defined('WAFF_THEME') && WAFF_THEME == 'DINARD' )
						$view_id = 0;
					// Then, print if we found results
					// 66660 = films-section
					$args = array(
						'id' => $view_id,
						'wpvsection' => $section_slug,
						'wpvedition' => $current_edition_slug,
					);
					echo render_view( $args );
					?>
					<!-- FIN FILM CARD -->

				</div>
				<!-- End : Slick slide -->
			</div>
		</section>
		<!-- END: #Carrousel section -->
		<?php
	endif;
}

function wa_sections_callback( $attributes, $is_preview = false, $post_id = null ) {
	global $current_edition_id, $current_edition_slug; 
	
	// if ( $is_preview ) 
	 	//print_r($attributes);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'sections-list mt-10 mb-10 contrast--dark';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	$subclass = ( $attributes['name'] ?? '' ) . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
		$subclass .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Params
	$show_introduction 		= (mb_get_block_field( 'waff_sl_show_introduction' ))?'1':'0'; 
	$show_parent_section 	= (mb_get_block_field( 'waff_sl_show_parent_section' ))?'1':'0'; 
	$show_tiny_list 		= (mb_get_block_field( 'waff_sl_show_tiny_list' ))?'1':'0'; 

	// Get edition metas
	$edition 			= mb_get_block_field( 'waff_sl_edition' ); // WP_Term Object
	$edition_id 		= (int)$attributes['data']['waff_sl_edition']; // ID
	$edition_id 		= ( isset($edition_id) && $edition_id != null && $edition_id != 0 )?$edition_id:$current_edition_id;
	$edition_name		= ( !empty($edition) && !is_wp_error($edition) )?$edition->name:get_term($edition_id)->name;
	$edition_year 		= ( !empty($edition) && !is_wp_error($edition) )?get_term_meta( $edition_id, 'wpcf-e-year', true ):'';
	if ( empty($edition_id) ) //|| is_wp_error($edition) 
		echo esc_html__( 'Please choose an edition', 'waff' );

	// Get parent section by edition year
	$parent_section_args = array(
		'taxonomy' => 'section',
		'posts_per_page' => -1,
		'orderby' => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => 0,
		'number' => 1,
		'meta_query' => array(
			array(
				'key' => 'wpcf-select-edition',
				'compare' => '=',
				'value' => $edition_id,
			),
		),
	);
	$the_edition_section = get_terms( $parent_section_args );
	$the_edition_terms_list = array();
	if ( !empty( $the_edition_section ) && !is_wp_error( $the_edition_section ) ) :
		foreach( $the_edition_section as $term ) {
			$termcolor 		= get_term_meta( $term->term_id, 'wpcf-s-color', true );
			$the_edition_terms_list[] = sprintf('<a class="section-item" %s href="%s" title="%s">%s</a>',
				(($termcolor!='')?'style="background-color:'.$termcolor.';border-color:'.$termcolor.';"':''),
				esc_url(get_term_link($term)),
				esc_html__($term->name),
				esc_html__($term->name)
			);
		}
	endif;

	// Get all sections by edition year
	$all_section_args = array(
		'taxonomy' => 'section',
		'posts_per_page' => -1,
		'orderby'  => array( 'menu_order' => 'DESC', 'title' => 'ASC' ), //'meta_value_num' => 'DESC', 
		'hide_empty' => false,
		//'hierarchical' => false,
		'parent' => $the_edition_section[0]->term_id,
		//'exclude' => $the_edition_section[0]->term_id,
		'meta_query' => array(
			array(
				'key' => 'wpcf-select-edition',
				'compare' => '=',
				'value' => $edition_id,
			),
		),
	);
	$sections = get_terms( $all_section_args );

	?>
		<!-- #Sections list -->
		<?php if ( isset( $show_introduction ) && $show_introduction == '1' ) : ?>
		<!-- BEGIN:Introduction -->
		<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>">
			<div class="container-fluid px-0">
				<hgroup class="text-center">
					<h6 class="headline d-inline-block"><?= esc_html(mb_get_block_field( 'waff_sl_title' )) ?></h6>
					<?php if ($edition_year != '') : ?><h1 class="sections-title mt-0 mb-0 display-1"><?= $edition_year; ?></h1><?php endif; ?>
					<?php
						if ( !empty($the_edition_terms_list) ) {
							printf(
								/* translators: %s: list of categories. */
								'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
								esc_html__( 'Categorized as', 'waff' ),
								implode($the_edition_terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
							);
						}
					?>
					<h6 class="visually-hidden">Les sections de l'édition <?= $edition_name; ?> du Festival Internationnal du Film d'Amiens</h6>
				</hgroup>

				<?php if ( mb_get_block_field( 'waff_sl_leadcontent' ) ) : ?>
				<p class="lead mt-2 mt-sm-6 text-center"><?= waff_do_markdown(mb_get_block_field( 'waff_sl_leadcontent' )) ?></p>
				<?php endif; ?>

				<?php if ( mb_get_block_field( 'waff_sl_content' ) ) : ?>
				<div class="mt-1 mt-sm-3 text-center w-75 m-auto"><?= waff_do_markdown(mb_get_block_field( 'waff_sl_content' )) ?></div>
				<?php endif; ?>
			</div>
		</section>
		<!-- END:Introduction -->
		<?php endif; ?>

		<!-- #BEGIN: Sections tiny list -->
		<?php if ( isset( $show_tiny_list ) && $show_tiny_list == '1' ) : ?>
		<section class="<?= $subclass ?> mt-0 mb-0 <?= $section_color_class ?> <?= $animation_class ?> tiny-list" <?= $data ?>>
			<div class="d-sm-flex row g-0">
		<?php endif; ?>

		<?php
		// Lightness threshold
		$lightness_threshold = 200; // Section = 200
		if ( isset( $show_parent_section ) && $show_parent_section == '1' )
			$sections = array_merge($the_edition_section, $sections);
		if ( !empty( $sections ) && !is_wp_error( $sections ) ) :
			foreach( $sections as $section ) :
				$section_id 				= $section->term_id;
				$section_color 				= get_term_meta( $section_id, 'wpcf-s-color', true );
				// Ajust lightness of section
				$section_color_class		= 'contrast--light bg-light color-dark';
				$section_title_color 		= 'color-dark link-dark';
				if ( $section_color != '' ) {
					$rgb = waff_HTMLToRGB($section_color); //, 'array'
					$hsl = waff_RGBToHSL($rgb);
					if($hsl->lightness < $lightness_threshold) {
						$section_color_class = 'contrast--dark bg-dark color-light';
						$section_title_color = 'color-light link-light';
					}
				}
				// Counts
				if ( function_exists('get_counts') )
					$counts = get_counts('section', $section_id, null);
				// Content 
				$section_description 				= term_description($section_id);
				$section_content 					= get_term_meta( $section_id, 'wpcf-s-content', true ); 
				// Image
				$section_image 						= get_term_meta( $section_id, 'wpcf-s-image', true );
				$section_credits_image 				= get_term_meta( $section_id, 'wpcf-s-credits-image', true ); 
				$section_image_ID 					= waff_get_image_id_by_url($section_image);
				$featured_img_caption 				= wp_get_attachment_caption($section_image_ID); // ADD WIL                    
				$thumb_img 							= get_post( $section_image_ID ); // Get post by ID
				//$featured_img_description 		= $thumb_img->post_content; // Display Description >> ISSUE with description > it display the page post_content instead of image. 
				if ( function_exists( 'types_render_termmeta' ) ) {
					$section_image = types_render_termmeta( 's-image', array(
						'term_id' => $section_id, 
						'size' => ( isset( $show_tiny_list ) && $show_tiny_list == '1' )?'post-featured-image-xs':'post-featured-image', //post-featured-image-x2
						'alt' => esc_html($featured_img_caption),
						'style' => 'object-fit: cover; width: 100%;',
						'class' => ( isset( $show_tiny_list ) && $show_tiny_list == '1' )?'img-fluid h-100-px':'img-fluid h-600-px')
					);
				}
				$section_credits_image 				= get_term_meta( $section_id, 'wpcf-s-credits-image', true ); 
		?>
		<!-- BEGIN:Sections list-->
		<?php if ( isset( $show_tiny_list ) && $show_tiny_list == '0' ) : ?>
		<section class="<?= $subclass ?> mt-0 mb-0 <?= $section_color_class ?> <?= $animation_class ?>" <?= $data ?>>
			<div class="card border-0 rounded-0">
				<?php if ( $section_image != '' ) : ?> 
				<figure title="<?php echo esc_attr(sanitize_text_field($section->name)); ?>" class="h-600-px">
					<div class="overlay bg-dark" <?= (($section_color!='')?'style="background-color:'.$section_color.' !important;"':'')?>></div>
					<picture class="lazy">
					<!-- 3800x1200 > 1900x600 -->
					<?= $section_image ?>
					</picture>
					<?php if ( $featured_img_caption ) : ?>
					<figcaption class="bg-transparent text-light"><strong>© <?= waff_do_markdown(strip_tags(esc_html($featured_img_caption))); ?></strong></figcaption>
					<?php elseif ( $section_credits_image ) : ?>
					<figcaption class="bg-transparent text-light"><strong>© <?= waff_do_markdown(strip_tags(esc_html($section_credits_image))); ?></strong></figcaption>
					<?php endif; /* If captions */ ?>
				</figure>
				<?php endif; ?>
				<div class="<?= (($section_image!='')?'card-img-overlay':'p-3 h-600-px'); ?> d-flex flex-column flex-sm-row justify-content-center justify-content-sm-between align-items-start align-items-sm-center" <?= (($section_image=='' && $section_color!='')?'style="background-color:'.$section_color.' !important;"':'')?>>
					<div class="w-sm-50">
						<h2 class="pt-4 heading-4 heading-sm card-title <?= $section_title_color ?>"><?= sanitize_text_field($section->name) ?></h2>
						<!-- Edition-->
						<?php
						if ( !empty($the_edition_terms_list) ) {
							printf(
								/* translators: %s: list of categories. */
								'<div class="section-list d-inline cat-links"><span class="screen-reader-text">%s </span>%s</div>',
								esc_html__( 'Categorized as', 'waff' ),
								implode($the_edition_terms_list, __( '&#8203;', 'waff' )) // phpcs:ignore WordPress.Security.EscapeOutput
							);
						}
						?>
						<!-- <div class="category-list d-inline"><a class="category-item">En avant</a></div> -->
						<!-- Description -->
						<?php if ( strlen(strip_tags($section_description)) > 0 ) : ?> 
							<p class="card-text <?= $section_title_color ?> pt-4 mb-2"><?= waff_do_markdown(strip_tags($section_description)) ?></p>
						<?php else : ?>
							<?php echo apply_filters('the_content', waff_do_markdown(waff_trim(waff_clean_alltags($section_content), 300))); ?>
						<?php endif; ?>
					</div>
					<div class="mt-2 mt-sm-0">
						<a href="<?= get_term_link($section_id); ?>" class="card-link <?= $section_title_color ?> stretched-link pr-1"><?= $counts['films']; ?> films
						<i class="icon icon-right pl-2"></i></a>
					</div>
				</div>
			</div>
		</section>
		<?php else: ?>
		<!-- BEGIN:Sections list tiny-->
		<div class="card border-0 rounded-0 flex-sm-equal col-4 col-sm-12" <?= (($section_color!='')?'style="background-color:'.$section_color.' !important;"':'')?> >
			<?php if ( $section_image != '' ) : ?> 
			<figure title="<?php echo esc_attr(sanitize_text_field($section->name)); ?>" class="">
				<picture class="lazy">
				<!-- 3800x1200 > 1900x600 -->
				<?= $section_image ?>
				</picture>
			</figure>
			<?php endif; ?>
			<div class="p-2 d-flex flex-column justify-content-between h-100" <?= (($section_image=='' && $section_color!='')?'style="background-color:'.$section_color.' !important;"':'')?>>
				<h6 class="pt-2 <?= $section_title_color ?>"><?= sanitize_text_field($section->name) ?></h6>
				<div class="mt-2 mt-sm-0 pb-4">
					<a href="<?= get_term_link($section_id); ?>" class="card-link <?= $section_title_color ?> stretched-link pr-1"><?= $counts['films']; ?> films
					<i class="icon icon-right pl-2"></i></a>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
			endforeach;

		if ( isset( $show_tiny_list ) && $show_tiny_list == '1' ) : ?>
			</div>
		</section>
		<!-- #END: Sections tiny list -->
		<?php endif;

		endif;
		?>
		<!-- END:Sections -->
		<!-- END: #Sections list -->
		<?php
}

function wa_mission_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// print_r($is_preview);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	if ( mb_get_block_field( 'waff_m_blockmargin' ) == 1 ) {
		$blockmargin = 'mt-lg-10 mb-lg-10 mt-5 mb-5';
	} else {
		$blockmargin = 'mt-n10 mb-0';
	}

	$themeClass = 'mission '.$blockmargin.' pt-10 pb-10 contrast--light bg-image bg-cover bg-position-center-center position-relative';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Image 
	$image 					= mb_get_block_field('waff_m_image');

	// print_r(mb_get_block_field('waff_m_alignment'));
	// print_r(mb_get_block_field('waff_m_position'));
	// Alignment 
	switch(mb_get_block_field('waff_m_alignment')) {
		case 'aligned': 
			$r_alignment 	= '';
			$b_alignment 	= 'col-12 col-lg-4 bg-action-1 h-850-px h-lg-850-px'; // col-4
			//$f_alignment 	= 'align-items-end';
			$i_alignment 	= 'h-700-px h-lg-700-px'; // h-700-px
			break;
		case 'shifted': 
			$r_alignment 	= 'vh-100';
			$b_alignment 	= 'col-lg-5 col-xl-5 bg-action-2 vh-75';
			$f_alignment 	= 'lg-vh-75 h-100'; // h-100 pose soucis. 
			$i_alignment 	= 'vh-75 --h-100';
			break;
	}

	// Position 
	switch(mb_get_block_field('waff_m_position')) {
		case 'top': 
			$b_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'align-items-lg-end align-items-start' : 'align-items-start';
			$f_position 	= 'top-0';
			$aos_position 	= 'fade-up';
			if (mb_get_block_field('waff_m_alignment') === 'aligned') { $f_alignment 	= 'align-items-start'; }
			break;
		case 'center': 
			$b_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'align-items-lg-center align-items-start' : 'align-items-end';
			$f_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'top-50 end-0 translate-middle-y lg-transform-0' : 'bottom-0';
			$aos_position 	= 'fade-up';
			if (mb_get_block_field('waff_m_alignment') === 'aligned') { $f_alignment 	= 'align-items-center'; }
			break;
		case 'bottom': 
			$b_position 	= mb_get_block_field('waff_m_alignment') === 'shifted' ? 'align-items-start' : 'align-items-end';
			$f_position 	= 'bottom-0';
			$aos_position 	= 'fade-down';
			if (mb_get_block_field('waff_m_alignment') === 'aligned') { $f_alignment 	= 'align-items-end'; }
			break;
	}

	// Responsive 
	$b_position 	.= ' ---- position-absolute position-lg-relative top-0 left-0 w-100'; 

	// Background image 
	$bg_images 		= waff_get_blocks_background();
	$bg_image 		= ( !empty($bg_images) ) ? reset( $bg_images ) : false;

	?>
	<!-- #Mission -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>; background-image: url('<?= $bg_image['url']; ?>');">
		<div class="container-fluid px-0 position-relative">
			<div class="row g-0 <?= $b_position; ?> <?= $r_alignment; ?> <?= $is_preview ? 'd-none' : '' ?>">
				<div class="<?= $b_alignment; ?> <?= $attributes['align'] === 'full' ? 'rounded-end-4':'rounded-4'; ?>" --data-aos="fade-left" --data-aos-delay="100"></div>
			</div>
			<div class="row <?= $f_alignment; ?> w-100 ---- position-lg-absolute <?= $f_position; ?> left-0">
				<!-- Col 1 -->
				<div class="col-2 d-none d-lg-block" ---data-aos="fade-left"></div>

				<!-- Col 2 -->
				<!-- Figure -->
				<?php if ( count($image) > 0 ) : ?>
					<?php foreach ( $image as $im ) : ?>
						<figure class="col-10 col-lg-4 p-0 rounded-4 contrast--light <?= $i_alignment; ?> overflow-hidden position-relative mb-10 --mb-md-10 mb-lg-0" data-aos="<?= $aos_position; ?>" data-aos-delay="200" style="<?= $is_preview ? 'float:left; width:49%;' : '' ?>">
							<picture class="">
								<img src="<?= $im['full_url'] ?>" alt="<?= esc_html($im['alt']); ?>" class="img-fluid rounded-4 <?= $i_alignment; ?> fit-image w-100 img-transition-scale">
							</picture>
							<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
								<!-- <figcaption> -->
								<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
									<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
									<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
										<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
									</span>
								</figcaption>
							<?php endif; /* If captions */ ?>
						</figure>
					<?php endforeach; ?>
				<?php endif; ?>

				<!-- Col 3 -->
				<!-- Begin: Content -->
				<div class="col-12 col-lg-6 col-xl-5 ps-5 d-flex flex-column justify-content-between --align-items-end" data-aos="fade-left" data-aos-delay="400" style="<?= $is_preview ? 'float:right; width:49%;' : '' ?>">
					<div>
						<h6 class="subline text-action-1"><?= mb_get_block_field( 'waff_m_subtitle' ) ?></h6>
						<h2><?= mb_get_block_field( 'waff_m_title' ) ?></h2>
						<p class="lead mb-3"><?= waff_do_markdown(mb_get_block_field( 'waff_m_leadcontent' )) ?></p>
						<?= waff_do_markdown(mb_get_block_field( 'waff_m_content' )) ?>
					</div>
					
					<div>
						<div class="row row-cols-2 row-cols-sm-3 row-cols-lg-2 g-4 py-5">
							<?php 
							foreach( mb_get_block_field( 'waff_m_lists' ) as $list ) : 
								echo sprintf('<div class="col d-flex align-items-center">
									<i class="%s flex-shrink-0 me-3 h4"></i>
									<div>
										<h6 class="fw-bold">%s</h6>
										<p>%s</p>
									</div>
								</div>', 
								$list[2],
								$list[0],
								$list[1]
								);
							endforeach;
							?>
						</div>

						<?php if ( mb_get_block_field( 'waff_m_morelink' ) == 1 ) : ?>
						<a class="btn btn-action-2 btn-lg btn-transition-scale" href="<?= mb_get_block_field( 'waff_m_moreurl' ) ?>"><?php _e('More...', 'waff'); ?></a>
						<?php endif; ?>

					</div>
				</div>
				<!-- End: Content -->

				<?php if ($is_preview) { echo '<div class="clear clearfix"></div>'; } ?>
			</div>
		</div>
	</section>
	<!-- END: #Mission -->
	<?php
	
}

function wa_cols_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// print_r($is_preview);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'cols mt-10 mb-0 contrast--dark text-white';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Image 
	$image 					= mb_get_block_field('waff_c_image');

	// Background image 
	$bg_images 		= waff_get_blocks_transition();
	$bg_image 		= ( !empty($bg_images) ) ? reset( $bg_images ) : false;

	?>
	<!-- #cols -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>;">

		<figure class="m-0 p-0 overflow-hidden mb-n1 z-2">
			<picture class="">
				<img src="<?= $bg_image['url']; ?>" alt="Image de fond" class="img-fluid fit-image w-100">
			</picture>
		</figure>

		<div class="container-fluid p-4 p-md-8 bg-v-plain-gradient-action-2 z-2 position-relative" style="<?= !$is_preview ?: 'color:white; background-color:var(--go--color--secondary, --wp--preset--color--secondary);' ?>">
			<div class="row mt-10 mb-10">
				<div class="col-4"></div>
				<div class="col-4 text-center">
					<h6 class="subline text-action-3" style="<?=!$is_preview ?: 'color:white;' ?>"><?= mb_get_block_field( 'waff_c_subtitle' ) ?></h6>
					<h2 class="text-white" style="<?= !$is_preview ?: 'color:white;' ?>"><?= mb_get_block_field( 'waff_c_title' ) ?></h2>
				</div>
				<div class="col-4 d-flex align-items-start justify-content-end">
					<?php if ( mb_get_block_field( 'waff_c_morelink' ) == 1 ) : ?>
					<a class="btn btn-action-3 btn-lg btn-transition-scale" href="<?= mb_get_block_field( 'waff_c_moreurl' ) ?>"><?php _e('More...', 'waff'); ?></a>
					<?php endif; ?>
				</div>
			</div>
			<div class="row mb-15">
				<?php if (mb_get_block_field( 'waff_c_leadcontent' ) != "") {
					echo '<div class="col-12"><p class="lead mb-4 text-center fw-bold">'.waff_do_markdown(mb_get_block_field( 'waff_c_leadcontent' )).'</p></div>';
				} ?>

				<?php 
				foreach( mb_get_block_field( 'waff_c_contents' ) as $content ) : 
					echo '<div class="col" style="' .( $is_preview ? 'display: inline-block; width: calc(24% - 10px); margin-right: 10px;' : '' ). '"><div class="lead">'.waff_do_markdown($content).'</div></div>';
				endforeach;
				?>
			</div>	
		</div>

		<!-- Background image-->
		<?php if ( count($image) > 0 && !$is_preview ) : ?>
			<?php foreach ( $image as $im ) : ?>
				<figure class="overflow-hidden h-100 w-100 position-absolute top-0 start-0 z-0" style="height: calc(100% - 112px);  margin-top: 112px;">
					<picture class="">
							<img src="<?= $im['full_url'] ?>" alt="Image de fond" class="img-fluid fit-image h-100 w-100">
					</picture>
				</figure>
			<?php endforeach; ?>
		<?php endif; ?>

	</section>
	<!-- END: #cols -->
	<?php
	
}

function wa_breaking_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// print_r($is_preview);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'breaking mt-0 mb-10 contrast--dark';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Image 
	$image_1 					= mb_get_block_field('waff_b_image_1');
	$image_2 					= mb_get_block_field('waff_b_image_2');

	?>
	<!-- #Breaking -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>;">
		<div class="container-fluid px-0">
			<div class="row g-0 align-items-center">

				<div class="col-md-6 h-500-px bg-color-layout img-shifted --shift-right rounded-bottom-4 rounded-bottom-right-0 md-rounded-0" data-aos="fade-up" data-aos-delay="0" style="<?=!$is_preview ?: 'display:inline-block; width:49%' ?>">
					
					<!-- Figure -->
					<figure class="bg-image h-100 m-0 position-absolute">
					<?php if ( count($image_1) > 0 && !$is_preview ) : ?>
						<?php foreach ( $image_1 as $im ) : ?>
								<div class="bg-image bg-cover bg-position-center-center z-0" style="background-image: url('<?= $im['full_url'] ?>');" data-aos="fade" data-aos-delay="200"></div>
								<div class="bg-image bg-v-inverse-gradient-action-2 z-1"></div>
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<!-- <figcaption> -->
									<figcaption class="top-0 bottom-auto d-flex align-items-center bg-transparent pt-2 ps-2 zi-5">
										<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapseA-<?= $id  ?>" role="button" aria-expanded="false" aria-controls="collapseA-<?= $id  ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapseA-<?= $id  ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
								<?php endif; /* If captions */ ?>
						<?php endforeach; ?>
					<?php endif; ?>
					</figure>

					<div class="card bg-transparent border-0 text-white --h-100 px-4 py-4 px-md-8 py-md-6 d-flex flex-column justify-content-between align-items-start z-2 <?= $is_preview ? '' : 'h-100' ?>">
						<h6 class="subline d-inline text-light"><?= mb_get_block_field( 'waff_b_label_1' ) ?></h6>
						<div>
							<div class="w-100 w-lg-50">
								<p class="card-date fw-bold text-transparent-color-layout mt-1 mb-0"><?= mb_get_block_field( 'waff_b_subtitle_1' ) ?></p>
								<h2 class="card-title"><a href="#" class="stretched-link link-white"><?= mb_get_block_field( 'waff_b_title_1' ) ?></a></h2>
							</div>
							<div class="card-text fw-bold"><?= waff_do_markdown(mb_get_block_field( 'waff_b_content_1' )) ?></div>
							<?php if ( mb_get_block_field( 'waff_b_morelink_1' ) == 1 ) : ?>
							<a class="btn btn-action-3 btn-lg mt-4 btn-transition-scale" href="<?= mb_get_block_field( 'waff_b_moreurl_1' ) ?>"><?php _e('More...', 'waff'); ?></a>
							<?php endif; ?>
						</div>
					</div>


				</div>
				<div class="col-md-6 h-500-px bg-color-layout img-shifted --shift-right rounded-bottom-4 rounded-bottom-left-0 md-rounded-bottom-4" data-aos="fade-up" data-aos-delay="100" style="<?=!$is_preview ?: 'display:inline-block; width:49%' ?>">
					
					<!-- Figure -->
					<figure class="bg-image h-100 m-0 position-absolute">
					<?php if ( count($image_2) > 0 && !$is_preview ) : ?>
						<?php foreach ( $image_2 as $im ) : ?>
								<div class="bg-image bg-cover bg-position-center-center z-0" style="background-image: url('<?= $im['full_url'] ?>');" data-aos="fade" data-aos-delay="200"></div>
								<div class="bg-image bg-v-inverse-gradient-action-2 z-1"></div>
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<!-- <figcaption> -->
									<figcaption class="top-0 bottom-auto d-flex align-items-center bg-transparent pt-2 ps-2 zi-5">
										<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapseB-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapseB-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapseB-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
								<?php endif; /* If captions */ ?>
						<?php endforeach; ?>
					<?php endif; ?>
					</figure>
					
					<div class="card bg-transparent border-0 text-white --h-100 px-4 py-4 px-md-8 py-md-6 d-flex flex-column justify-content-between align-items-start z-2 <?= $is_preview ? '' : 'h-100' ?>">
						<h6 class="subline d-inline action-1"><?= mb_get_block_field( 'waff_b_label_2' ) ?></h6>
						<div>
							<div class="w-100 w-lg-50">
								<p class="card-date fw-bold text-transparent-color-layout mt-1 mb-0"><?= mb_get_block_field( 'waff_b_subtitle_2' ) ?></p>
								<h3 class="card-title"><a href="#" class="stretched-link link-white"><?= mb_get_block_field( 'waff_b_title_2' ) ?></a></h3>
							</div>
							<div class="card-text fw-bold"><?= waff_do_markdown(mb_get_block_field( 'waff_b_content_2' )) ?></div>
							<?php if ( mb_get_block_field( 'waff_b_morelink_2' ) == 1 ) : ?>
							<a class="btn btn-action-3 btn-lg mt-4 btn-transition-scale" href="<?= mb_get_block_field( 'waff_b_moreurl_2' ) ?>"><?php _e('More...', 'waff'); ?></a>
							<?php endif; ?>
						</div>
					</div>


				</div>

			</div>
		</div>
	</section>
	<!-- END: #Breaking -->
	<?php
	
}

function wa_insights_callback( $attributes, $is_preview = false, $post_id = null ) {
	
	// print_r($is_preview);

	// Fields data.
	if ( empty( $attributes['data'] ) ) {
		return;
	}
	
	// Unique HTML ID if available.
	$id = ( $attributes['name'] ?? '' ) . '-' . ( $attributes['id'] ?? '' );
	if ( ! empty( $attributes['anchor'] ) ) {
		$id = $attributes['anchor'];
	}

	// Custom CSS class name.
	$themeClass = 'insights mt-10 mb-10 contrast--light bg-image bg-cover bg-position-center-center position-relative';
	$class = ( $attributes['name'] ?? '' ) . ' ' . $themeClass . ' ' . ( $attributes['className'] ?? '' );
	if ( ! empty( $attributes['align'] ) ) {
		$class .= " align{$attributes['align']}";
	}
	$data = '';
	$animation_class = '';
	if ( ! empty( $attributes['animation'] ) ) {
		$animation_class .= " coblocks-animate";
		$data .= " data-coblocks-animation='{$attributes['animation']}'";
	}

	// Image 
	$image 					= mb_get_block_field('waff_i_image');

	// // Background image 
	// $bg_images = waff_get_blocks_background();
	// $bg_image = reset( $bg_images );

	// Pattern image
	$pat_images = waff_get_blocks_pattern();
	$pat_image 		= ( !empty($pat_images) ) ? reset( $pat_images ) : false;
	
	?>
	<!-- #Insights -->
	<section id="<?= $id ?>" class="<?= $class ?> <?= $animation_class ?>" <?= $data ?> style="background-color: <?= mb_get_block_field( 'background_color' ) ?>;">
		<div class="container-fluid px-0 position-relative">
			<div class="row">
				<div class="col-12 col-lg-8 ps-4 pe-4 ps-lg-10 pe-lg-10" ---data-aos="fade-left" style="<?= !$is_preview ?: 'display:inline-block; width:49%;' ?>">

					<h6 class="subline text-action-1"><?= mb_get_block_field( 'waff_i_subtitle' ) ?></h6>
					<hgroup class="pt-8 pb-4 d-flex justify-content-between align-items-center">
						<h2><?= mb_get_block_field( 'waff_i_title' ) ?></h2>
						<?php if ( mb_get_block_field( 'waff_i_morelink' ) == 1 ) : ?>
						<a class="btn btn-action-2 btn-lg btn-transition-scale" href="<?= mb_get_block_field( 'waff_i_moreurl' ) ?>"><?php _e('More...', 'waff'); ?></a>
						<?php endif; ?>
					</hgroup>

					<p class="lead mb-3"><?= waff_do_markdown(mb_get_block_field( 'waff_i_leadcontent' )) ?></p>

					<div class="row row-cols-1 row-cols-md-3 mb-3 text-center">
						<?php 
						foreach( mb_get_block_field( 'waff_i_lists' ) as $list ) : 
							echo sprintf('<div class="col">
								<div class="card mb-4 rounded-3 --shadow-sm border-0 text-start %s text-color-main">
									%s
									<div class="card-body">
										<h1 class="card-title %s">%s<small class="text-body-secondary fw-light">%s</small></h1>
										<p class="mt-3 mb-4">%s</p>
										%s
									</div>
								</div>
							</div>',
							$list[4]?$list[4]:'bg-color-layout',
							$list[0]?'<div class="card-header py-3"><h4 class="my-0 fw-normal">'.$list[0].'</h4></div>':'',
							$list[4]?'heading-2':'fw-medium',
							$list[1],
							$list[2],
							$list[3],
							$list[5]?'<a href="'.$list[5].'" class="w-100 btn btn-lg '.($list[4]=='bg-action-1'?'btn-outline-light':'btn-primary').'">En savoir plus...</a>':''
							);
						endforeach;
						?>
					</div>
					
				</div>

				<div class="col-12 col-lg-4 bg-color-layout <?= $attributes['align'] === 'full' ? 'rounded-start-4':'rounded-4'; ?> d-flex align-items-end justify-content-end ---- bg-position-center-center bg-repeat" ---data-aos="fade-left" style="<?= !$is_preview ?: 'display:inline-block; width:49%;' ?> background-image: url('<?= $pat_image['url']; ?>');">

					<!-- Figure -->
					<?php if ( count($image) > 0 ) : ?>
						<?php foreach ( $image as $im ) : ?>
							<figure class="p-0 <?= $attributes['align'] === 'full' ? 'rounded-start-4':'rounded-4'; ?> contrast--light h-80 w-80 overflow-hidden" data-aos="fade-left" data-aos-delay="200">
								<picture class="">
									<img src="<?= $im['full_url'] ?>" alt="<?= esc_html($im['alt']); ?>" class="img-fluid rounded-4 w-100 h-100 fit-image img-transition-scale">
								</picture>
								<?php $im['alt'] = 'DR'; if ( $im['alt'] || $im['description'] || wp_get_attachment_caption($im['ID']) ) : ?>
									<!-- <figcaption> -->
									<figcaption class="d-flex align-items-center bg-transparent pb-2 ps-2">
										<span class="collapse-hover bg-white text-action-2 p-1 lh-1 rounded-pill z-2" href="#collapse-<?= $id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $id ?>">©</span>
										<span class="collapse collapse-horizontal p-1 lh-1 bg-color-layout rounded-end-pill ms-n2" id="collapse-<?= $id ?>">
											<span class="text-nowrap p-1 lh-1 m-0 ps-2 fw-semibold"><strong><?= wp_get_attachment_caption($im['ID']) ? wp_get_attachment_caption($im['ID']) : esc_html($im['alt'] ? $im['alt'] : 'DR'); ?></strong> <?= esc_html($im['description']); ?></span>
										</span>
									</figcaption>
								<?php endif; /* If captions */ ?>
							</figure>
						<?php endforeach; ?>
					<?php endif; ?>

				</div>

			</div>
		</div>
	</section>
	<!-- END: #Insights -->
	<?php
	
}


/**
 * Disallow some blocks 
 * 
 */

/*
	core/embed
	core-embed/twitter
	core-embed/youtube
	core-embed/facebook
	core-embed/instagram
	core-embed/wordpress
	core-embed/soundcloud
	core-embed/spotify
	core-embed/flickr
	core-embed/vimeo
	core-embed/animoto
	core-embed/cloudup
	core-embed/collegehumor
	core-embed/dailymotion
	core-embed/funnyordie
	core-embed/hulu
	core-embed/imgur
	core-embed/issuu
	core-embed/kickstarter
	core-embed/meetup-com
	core-embed/mixcloud
	core-embed/photobucket
	core-embed/polldaddy
	core-embed/reddit
	core-embed/reverbnation
	core-embed/screencast
	core-embed/scribd
	core-embed/slideshare
	core-embed/smugmug
	core-embed/speaker
	core-embed/ted
	core-embed/tumblr
	core-embed/videopress
	core-embed/wordpress-tv
*/

/*
    [0] => complianz/document
    [1] => toolset-views/view-editor
    [2] => toolset-views/wpa-editor
    [3] => toolset-views/sorting
    [4] => toolset-views/view-pagination-block
    [5] => core/archives
    [6] => core/block
    [7] => core/calendar
    [8] => core/categories
    [9] => core/latest-comments
    [10] => core/latest-posts
    [11] => core/rss
    [12] => core/search
    [13] => core/shortcode
    [14] => core/social-link
    [15] => core/tag-cloud
    [16] => gravityforms/form
    [17] => coblocks/form
    [18] => coblocks/field-name
    [19] => coblocks/field-email
    [20] => coblocks/field-textarea
    [21] => coblocks/field-text
    [22] => coblocks/field-date
    [23] => coblocks/field-phone
    [24] => coblocks/field-radio
    [25] => coblocks/field-select
    [26] => coblocks/field-submit-button
    [27] => coblocks/field-checkbox
    [28] => coblocks/field-website
    [29] => coblocks/field-hidden
    [30] => coblocks/events
    [31] => coblocks/post-carousel
    [32] => coblocks/posts
    [33] => coblocks/social
    [34] => coblocks/social-profiles
    [35] => wp-bootstrap-blocks/container
    [36] => wp-bootstrap-blocks/row
    [37] => wp-bootstrap-blocks/column
    [38] => wp-bootstrap-blocks/button
    [39] => bcn/breadcrumb-trail
    [40] => meta-box/wa-latest-posts
    [41] => meta-box/wa-partners
    [42] => meta-box/wa-edito
    [43] => toolset/map
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
// 		// General
// 		'core/paragraph',
// 		'core/image',
// 		'core/heading',
// 		'core/gallery',
// 		'core/list',
// 		'core/quote',
// 		'core/audio',
// 		'core/cover',
// 		'core/file',
// 		'core/video',
// 		'core/html', // Added #43

// 		// Embed 
// 		'core/embed',
// 		'core-embed/twitter',
// 		'core-embed/youtube',
// 		'core-embed/facebook',
// 		'core-embed/instagram',
// 		// core-embed/wordpress
// 		// core-embed/soundcloud
// 		// core-embed/spotify
// 		'core-embed/flickr',
// 		'core-embed/vimeo',
// 		// core-embed/animoto
// 		// core-embed/cloudup
// 		// core-embed/collegehumor
// 		// core-embed/dailymotion
// 		// core-embed/funnyordie
// 		// core-embed/hulu
// 		// core-embed/imgur
// 		'core-embed/issuu',
// 		// core-embed/kickstarter
// 		// core-embed/meetup-com
// 		// core-embed/mixcloud
// 		// core-embed/photobucket
// 		// core-embed/polldaddy
// 		// core-embed/reddit
// 		// core-embed/reverbnation
// 		// core-embed/screencast
// 		// core-embed/scribd
// 		// core-embed/slideshare
// 		// core-embed/smugmug
// 		// core-embed/speaker
// 		// core-embed/ted
// 		// core-embed/tumblr
// 		// core-embed/videopress
// 		// core-embed/wordpress-tv

// 		//
// 		// complianz/document
// 		// toolset-views/view-editor
// 		// toolset-views/wpa-editor
// 		// toolset-views/sorting
// 		// toolset-views/view-pagination-block
// 		// core/archives
// 		// core/block
// 		// core/calendar
// 		// core/categories
// 		// core/latest-comments
// 		// core/latest-posts
// 		// core/rss
// 		// core/search
// 		// core/shortcode
// 		// core/social-link
// 		// core/tag-cloud
// 		// gravityforms/form
// 		// coblocks/form
// 		// coblocks/field-name
// 		// coblocks/field-email
// 		// coblocks/field-textarea
// 		// coblocks/field-text
// 		// coblocks/field-date
// 		// coblocks/field-phone
// 		// coblocks/field-radio
// 		// coblocks/field-select
// 		// coblocks/field-submit-button
// 		// coblocks/field-checkbox
// 		// coblocks/field-website
// 		// coblocks/field-hidden
// 		// coblocks/events
// 		// coblocks/post-carousel
// 		// coblocks/posts
// 		// coblocks/social
// 		// coblocks/social-profiles
// 		// wp-bootstrap-blocks/container
// 		// wp-bootstrap-blocks/row
// 		// wp-bootstrap-blocks/column
// 		// wp-bootstrap-blocks/button
// 		// bcn/breadcrumb-trail
// 		// meta-box/wa-latest-posts
// 		// meta-box/wa-partners
// 		// meta-box/wa-edito
// 		// toolset/map
// 	);
	
// 	return array_merge($core,get_dynamic_block_names());

// }

/**
 * Disallow some blocks 
 * JS way 
 * https://github.com/WordPress/gutenberg/issues/25676
 */

function waff_reset_blocks_enqueue_block_editor_assets() {
	// Get theme option
	$advanced_blocks = (bool) get_theme_mod( 'advanced_blocks', waff_defaults( 'advanced_blocks' ) );

	// If the option is not checked, return.
	if ( $advanced_blocks !== true )
		wp_enqueue_script( 'wp-bootstrap-block-reset', get_stylesheet_directory_uri() . '/dist/js/admin/custom-wp-bootstrap-reset.js', array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ), '1.0.0', true ); // Script solution > only remove blocks but not in list
}

/**
 * Disallow some blocks 
 * PHP way 
 */

function waff_allowed_block_types( $allowed_blocks, $editor_context ) {

	// Get theme option
	$advanced_blocks = (bool) get_theme_mod( 'advanced_blocks', waff_defaults( 'advanced_blocks' ) );

	// error_log($advanced_blocks);
	// error_log(print_r($allowed_blocks, true));

	//if ( $advanced_blocks !== true && isset( $editor_context->post ) && $editor_context->post->post_type === 'page' ) { // Only page or a custom post_type 
	if ( $advanced_blocks !== true && isset( $editor_context->post ) ) { // All post_type blocks 
			return array(
			// 'core/image', 
			// 'core/heading', 
			// 'core/paragraph', 
			// 'core/list', 
			// 'core/quote', 
			// 'core/pullquote', 
			// 'core/block', 
			// 'core/button', 
			// 'core/buttons', 
			// 'core/column', 
			// 'core/columns', 
			// 'core/table', 
			// 'core/text-columns', 
			// //
			// 'coblocks/accordion',
			// 'coblocks/accordion-item',
			// 'coblocks/alert',
			// 'coblocks/counter',
			// 'coblocks/column',
			// 'coblocks/row',
			// 'coblocks/dynamic-separator',
			// 'coblocks/logos',
			// 'coblocks/icon',
			// 'coblocks/buttons',	
			
			
			//"toolset/ct",
			//"bcn/breadcrumb-trail",
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
			"coblocks/accordion",
			"coblocks/accordion-item",
			"coblocks/alert",
			"coblocks/author",
			"coblocks/gallery-carousel",
			// "coblocks/shape-divider",
			"coblocks/social",
			"coblocks/social-profiles",
			"coblocks/gallery-stacked",
			"coblocks/posts",
			"coblocks/post-carousel",
			"coblocks/map",
			"coblocks/counter",
			"coblocks/column",
			"coblocks/dynamic-separator",
			// "coblocks/events",
			// "coblocks/event-item",
			"coblocks/faq",
			"coblocks/faq-item",
			"coblocks/feature",
			"coblocks/features",
			// "coblocks/form",
			// "coblocks/field-date",
			// "coblocks/field-email",
			// "coblocks/field-name",
			// "coblocks/field-radio",
			// "coblocks/field-phone",
			// "coblocks/field-textarea",
			// "coblocks/field-text",
			// "coblocks/field-select",
			// "coblocks/field-submit-button",
			// "coblocks/field-checkbox",
			// "coblocks/field-website",
			// "coblocks/field-hidden",
			// "coblocks/click-to-tweet",
			"coblocks/gallery-collage",
			// "coblocks/food-and-drinks",
			// "coblocks/food-item",
			"coblocks/logos",
			"coblocks/gallery-masonry",
			// "coblocks/pricing-table",
			// "coblocks/pricing-table-item",
			"coblocks/row",
			"coblocks/service",
			"coblocks/services",
			"coblocks/gallery-offset",
			// "coblocks/opentable",
			"coblocks/icon",
			"coblocks/gif",
			"coblocks/gist",
			"coblocks/hero",
			"coblocks/highlight",
			// "complianz/document",
			// "complianz/consent-area",
			"wp-bootstrap-blocks/container",
			"wp-bootstrap-blocks/column",
			"wp-bootstrap-blocks/row",
			"wp-bootstrap-blocks/button",
			"gravityforms/form",
			"core/paragraph",
			"core/image",
			"core/heading",
			"core/gallery",
			"core/list",
			"core/list-item",
			"core/quote",
			// "core/archives",
			"core/audio",
			"core/button",
			"core/buttons",
			// "core/calendar",
			// "core/categories",
			// "core/code",
			"core/column",
			"core/columns",
			"core/cover",
			"core/details",
			// "core/embed",
			"core/file",
			"core/group",
			"core/html",
			// "core/latest-comments",
			// "core/latest-posts",
			"core/media-text",
			"core/missing",
			// "core/more",
			// "core/nextpage",
			// "core/page-list",
			// "core/page-list-item",
			"core/pattern",
			// "core/preformatted",
			"core/pullquote",
			"core/block",
			// "core/rss",
			// "core/search",
			"core/separator",
			"core/shortcode",
			"core/social-link",
			// "core/social-links",
			"core/spacer",
			"core/table",
			// "core/tag-cloud",
			"core/text-columns",
			// "core/verse",
			"core/video",
			"core/footnotes",
			// "core/navigation",
			// "core/navigation-link",
			// "core/navigation-submenu",
			// "core/site-logo",
			// "core/site-title",
			// "core/site-tagline",
			// "core/query",
			"core/template-part",
			// "core/avatar",
			// "core/post-title",
			// "core/post-excerpt",
			// "core/post-featured-image",
			// "core/post-content",
			// "core/post-author",
			// "core/post-author-name",
			// "core/post-date",
			// "core/post-terms",
			// "core/post-navigation-link",
			// "core/post-template",
			// "core/query-pagination",
			// "core/query-pagination-next",
			// "core/query-pagination-numbers",
			// "core/query-pagination-previous",
			// "core/query-no-results",
			// "core/read-more",
			// "core/comments",
			// "core/comment-author-name",
			// "core/comment-content",
			// "core/comment-date",
			// "core/comment-edit-link",
			// "core/comment-reply-link",
			// "core/comment-template",
			// "core/comments-title",
			// "core/comments-pagination",
			// "core/comments-pagination-next",
			// "core/comments-pagination-numbers",
			// "core/comments-pagination-previous",
			// "core/post-comments-form",
			"core/home-link",
			// "core/loginout",
			// "core/term-description",
			// "core/query-title",
			// "core/post-author-biography",
			// "core/freeform",
			"core/legacy-widget",
			"core/widget-group",
			"coblocks/buttons",
			"coblocks/media-card",

			// Remplacez ceci par l'identifiant du bloc que vous souhaitez autoriser
			// Ajoutez d'autres identifiants de blocs au besoin
			'directory/wa-rsfp-directory-block',
		);
	}
	return $allowed_blocks;
}

/**
 * Define new colors for wp-bootstrap-blocks 
 */

function waff_wp_boostrap_enqueue_block_editor_assets() {
	wp_enqueue_script( 'wp-bootstrap-block-filter', get_stylesheet_directory_uri() . '/dist/js/admin/custom-wp-bootstrap-blocks.js', array( 'wp-hooks' ), '1.0.0', true );
    if ( defined( 'WAFF_THEME_COLORS' ) ) wp_localize_script( 'wp-bootstrap-block-filter', 'wpBootstrapBlockFilterOptions', WAFF_THEME_COLORS );
}
