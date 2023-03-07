<?php

/**
 * Adds custom admin columns
 */

add_filter('manage_post_posts_columns', 			'waff_add_img_column');
add_filter('manage_post_posts_custom_column', 		'waff_manage_img_column', 10, 2);
add_filter('manage_homeslide_posts_columns', 		'waff_add_img_column');
add_filter('manage_homeslide_posts_custom_column', 	'waff_manage_img_column', 10, 2);

function waff_add_img_column($columns) {
  	$columns = array_slice($columns, 0, 1, true) + array('thumbnail' => esc_html__( 'Featured', 'waff' )) + array_slice($columns, 1, count($columns) - 1, true);
    return $columns;
}

function waff_manage_img_column($column_name, $post_id) {
    if( $column_name == 'thumbnail' ) {
        //echo get_the_post_thumbnail($post_id, 'thumbnail');
        the_post_thumbnail( [40, 40] );
    }
    return $column_name;
}

/**
 * Adds Flash post type 
 */

if( true === WAFF_HAS_FLASHS_POSTTYPE ) {
	if ( !function_exists('waff_flash_register_post_type') ) {
		add_action( 'init', 'waff_flash_register_post_type' );
		function waff_flash_register_post_type() {
			$args = array(
				'label'  => esc_html__( 'Flashes', 'waff' ),
				'labels' => array(
					'menu_name'          => esc_html__( 'Flashes', 'waff' ),
					'name_admin_bar'     => esc_html__( 'Flash', 'waff' ),
					'add_new'            => esc_html__( 'Add Flash', 'waff' ),
					'add_new_item'       => esc_html__( 'Add new Flash', 'waff' ),
					'new_item'           => esc_html__( 'New Flash', 'waff' ),
					'edit_item'          => esc_html__( 'Edit Flash', 'waff' ),
					'view_item'          => esc_html__( 'View Flash', 'waff' ),
					'update_item'        => esc_html__( 'View Flash', 'waff' ),
					'all_items'          => esc_html__( 'All Flashes', 'waff' ),
					'search_items'       => esc_html__( 'Search Flashes', 'waff' ),
					'parent_item_colon'  => esc_html__( 'Parent Flash', 'waff' ),
					'not_found'          => esc_html__( 'No Flashes found', 'waff' ),
					'not_found_in_trash' => esc_html__( 'No Flashes found in Trash', 'waff' ),
					'name'               => esc_html__( 'Flashes', 'waff' ),
					'singular_name'      => esc_html__( 'Flash', 'waff' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_nav_menus' => false,
				'menu_position'       => 22,
				'menu_icon'         => 'dashicons-tide',
				'supports'          => array( 'title' ), //'excerpt', 'custom-fields', 'revisions', 'thumbnail' 
				'rewrite'           => false,
				'has_archive'       => false,
				//'taxonomies'        => array( 'flash-category', 'post_tag' )
			);
		
			register_post_type( 'flash', $args );
		}
	}
}

/**
 * Adds Homeslide post type 
 */

if( true === WAFF_HAS_HOMESLIDES_POSTTYPE ) {
	if ( !function_exists('waff_homeslide_register_post_type') ) {
		add_action( 'init', 'waff_homeslide_register_post_type' );
		function waff_homeslide_register_post_type() {
			$args = array(
				'label'  => esc_html__( 'Homeslides', 'waff' ),
				'labels' => array(
					'menu_name'          => esc_html__( 'Homeslides', 'waff' ),
					'name_admin_bar'     => esc_html__( 'Homeslide', 'waff' ),
					'add_new'            => esc_html__( 'Add Homeslide', 'waff' ),
					'add_new_item'       => esc_html__( 'Add new Homeslide', 'waff' ),
					'new_item'           => esc_html__( 'New Homeslide', 'waff' ),
					'edit_item'          => esc_html__( 'Edit Homeslide', 'waff' ),
					'view_item'          => esc_html__( 'View Homeslide', 'waff' ),
					'update_item'        => esc_html__( 'View Homeslide', 'waff' ),
					'all_items'          => esc_html__( 'All Homeslides', 'waff' ),
					'search_items'       => esc_html__( 'Search Homeslides', 'waff' ),
					'parent_item_colon'  => esc_html__( 'Parent Homeslide', 'waff' ),
					'not_found'          => esc_html__( 'No Homeslides found', 'waff' ),
					'not_found_in_trash' => esc_html__( 'No Homeslides found in Trash', 'waff' ),
					'name'               => esc_html__( 'Homeslides', 'waff' ),
					'singular_name'      => esc_html__( 'Homeslide', 'waff' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_nav_menus' => false,
				'menu_position'       => 20,
				'menu_icon'         => 'dashicons-images-alt2',
				'supports'          => array( 'title', 'editor', 'thumbnail'), //'excerpt', 'custom-fields', 'revisions', 'thumbnail' 
				'rewrite'           => false,
				'has_archive'       => false,
				//'taxonomies'        => array( 'homeslide-category', 'post_tag' )
			);
		
			register_post_type( 'homeslide', $args );
		}
	}
}

/**
 * Adds Partners post type 
 */

if( true === WAFF_HAS_PARTNERS_POSTTYPE ) {
	if ( !function_exists('waff_partner_register_post_type') ) {
		add_action( 'init', 'waff_partner_register_post_type', 999); // Hook at the end, to get toolset categories
		function waff_partner_register_post_type() {
			$args = array(
				'label'  => esc_html__( 'Partners', 'waff' ),
				'labels' => array(
					'menu_name'          => esc_html__( 'Partners', 'waff' ),
					'name_admin_bar'     => esc_html__( 'Partner', 'waff' ),
					'add_new'            => esc_html__( 'Add Partner', 'waff' ),
					'add_new_item'       => esc_html__( 'Add new Partner', 'waff' ),
					'new_item'           => esc_html__( 'New Partner', 'waff' ),
					'edit_item'          => esc_html__( 'Edit Partner', 'waff' ),
					'view_item'          => esc_html__( 'View Partner', 'waff' ),
					'update_item'        => esc_html__( 'View Partner', 'waff' ),
					'all_items'          => esc_html__( 'All Partner', 'waff' ),
					'search_items'       => esc_html__( 'Search Partner', 'waff' ),
					'parent_item_colon'  => esc_html__( 'Parent Partner', 'waff' ),
					'not_found'          => esc_html__( 'No Partners found', 'waff' ),
					'not_found_in_trash' => esc_html__( 'No Partners found in Trash', 'waff' ),
					'name'               => esc_html__( 'Partners', 'waff' ),
					'singular_name'      => esc_html__( 'Partner', 'waff' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_in_nav_menus' => false,
				'menu_position'       => 20,
				'menu_icon'         => 'dashicons-groups',
				'supports'          => array( 'title', 'thumbnail'), //'editor', 'excerpt', 'custom-fields', 'revisions', 'thumbnail' 
				'rewrite'           => false,
				'has_archive'       => false,
				'taxonomies'        => array( 'partner-category', 'edition' )
			);
		
			register_post_type( 'partner', $args );

			$taxonomy_args = array(
				"label"             => __( 'Partner categories', 'waff' ),
				"singular_label"    => __( 'Partner category', 'waff' ),
				'public'            => true,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_in_nav_menus' => false,
				'args'              => array( 'orderby' => 'term_order' ),
				'rewrite'           => false,
				'query_var'         => true
			);

			register_taxonomy( 'partner-category', 'partner', $taxonomy_args );
		
		}
	}
}