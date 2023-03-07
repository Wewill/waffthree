<?php

/**
* Add custom fields to menu item
*
* This will allow us to play nicely with any other plugin that is adding the same hook
*
* @param  int $item_id 
* @params obj $item - the menu item
* @params array $args
*/
function waff_custom_fields( $item_id, $item ) {

	wp_nonce_field( 'custom_menu_meta_nonce', '_custom_menu_meta_nonce_name' );
	$ismuted_menu_meta = get_post_meta( $item_id, '_ismuted_menu_meta', true );
	$isloggued_menu_meta = get_post_meta( $item_id, '_isloggued_menu_meta', true );
	?>

	<input type="hidden" name="waff-nonce" value="<?php echo wp_create_nonce( 'waff-name' ); ?>" />
	<input type="hidden" class="nav-menu-id" value="<?php echo $item_id ;?>" />

	<p class="field-custom_menu_meta description description-thin">
		<label for="waff-for-ismuted_menu_meta-<?php echo $item_id ;?>">
			<?php _e( 'Menu item is muted ?', 'waff' ); ?><br>
	        <input type="checkbox" name="ismuted_menu_meta[<?php echo $item_id ;?>]" id="waff-for-ismuted_menu_meta-<?php echo $item_id ;?>" <?php checked($ismuted_menu_meta, true); ?> />
		</label>
	</p>

	<p class="field-custom_menu_meta description description-thin">
		<label for="waff-for-isloggued_menu_meta-<?php echo $item_id ;?>">
			<?php _e( 'Display only if user is loggued', 'waff' ); ?><br>
	        <input type="checkbox" name="isloggued_menu_meta[<?php echo $item_id ;?>]" id="waff-for-isloggued_menu_meta-<?php echo $item_id ;?>" <?php checked($isloggued_menu_meta, true); ?> />
		</label>
	</p>

	<?php
}
add_action( 'wp_nav_menu_item_custom_fields', 'waff_custom_fields', 10, 2 );


/**
* Save the menu item meta
* 
* @param int $menu_id
* @param int $menu_item_db_id	
*/
function waff_nav_update( $menu_id, $menu_item_db_id ) {

	// Verify this came from our screen and with proper authorization.
	if ( ! isset( $_POST['_custom_menu_meta_nonce_name'] ) || ! wp_verify_nonce( $_POST['_custom_menu_meta_nonce_name'], 'custom_menu_meta_nonce' ) ) {
		return $menu_id;
	}

	$ismuted_menu_meta = ( isset($_POST['ismuted_menu_meta'][$menu_item_db_id]) && $_POST['ismuted_menu_meta'][$menu_item_db_id] == 'on') ? true : false;
	if ( $ismuted_menu_meta ) {
		update_post_meta( $menu_item_db_id, '_ismuted_menu_meta', $ismuted_menu_meta );
	} else {
		delete_post_meta( $menu_item_db_id, '_ismuted_menu_meta' );
	}

	$isloggued_menu_meta = ( isset($_POST['isloggued_menu_meta'][$menu_item_db_id]) && $_POST['isloggued_menu_meta'][$menu_item_db_id] == 'on') ? true : false;
	if ( $isloggued_menu_meta ) {
		update_post_meta( $menu_item_db_id, '_isloggued_menu_meta', $isloggued_menu_meta );
	} else {
		delete_post_meta( $menu_item_db_id, '_isloggued_menu_meta' );
	}

}
add_action( 'wp_update_nav_menu_item', 'waff_nav_update', 10, 2 );

/**
* Hide menu item on the front-end.
*
* @param string   $item_output The menu item's starting HTML output.
* @param WP_Post  $item        Menu item data object.
* @param int      $depth       Depth of menu item. Used for padding.
* @param stdClass $args        An object of wp_nav_menu() arguments.
* @return string      
*/
function waff_custom_menu_hide( $item_output, $item, $depth, $args ) {

	if( is_object( $item ) && isset( $item->ID ) ) {

		$isloggued_menu_meta = get_post_meta( $item->ID, '_isloggued_menu_meta', true );

		if ( ! empty( $isloggued_menu_meta ) && $isloggued_menu_meta == 1) {
			if ( !is_user_logged_in() )
				return null;
		}
	}	
	return $item_output;

}
add_filter( 'walker_nav_menu_start_el', 'waff_custom_menu_hide', 10, 4 );

/**
* Displays field on the front-end.
*
* @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
* @param WP_Post  $item    The current menu item.
* @param stdClass $args    An object of wp_nav_menu() arguments.
* @param int      $depth   Depth of menu item. Used for padding.
* @return string      
*/
function waff_custom_menu_class( $classes, $item, $args) {

	if( is_object( $item ) && isset( $item->ID ) ) {

		$ismuted_menu_meta = get_post_meta( $item->ID, '_ismuted_menu_meta', true );

		if ( !empty( $ismuted_menu_meta ) && $ismuted_menu_meta == 1) {
			$classes[] = 'link-muted';
		}
	}
	return $classes;

}
add_filter( 'nav_menu_css_class', 'waff_custom_menu_class', 10, 3 );

/* ADDS CUSTOM DATA TO WALKERS
================================================== */
//apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
function waff_add_additional_data_on_a($atts, $item, $args, $depth) {

	// print_r($item); //> classes > [4] => menu-item-has-children
	// print_r($args); //> menu_class
	// print_r($depth); //> 0

	// echo "Depth::" . $depth;
	// if ( $depth == 0 ) print_r((array)$item->classes);
	// if ( $depth == 0 ) print_r($args->menu_class);

	$atts['data-depth'] 		= $depth;
	$atts['data-classes'] 		= (array)$item->classes;
	$atts['data-menu_class'] 	= $args->menu_class;


    //if( in_array('main-nav', explode(' ', $args->menu_class) ) && in_array('menu-item-has-children', (array)$item->classes ) && $depth == 0) {
	if( in_array('main-nav', explode(' ', $args->menu_class) ) && in_array('menu-item', (array)$item->classes ) && $depth == 0) { // WP 6.1 FIX Wainting 6.1.1
		//<a class="" data-toggle="collapse" data-target="#collapse1" aria-expanded="false" aria-controls="collapse1" aria-label="Ouvrir le sous-menu : L'édition">
		$atts['data-toggle'] 	= 'collapse';
		$atts['data-target'] 	= '#'.sanitize_title($item->title);
		$atts['aria-expanded'] 	= 'false';
		$atts['aria-controls'] 	= sanitize_title($item->title);
		$atts['aria-label'] 	= 'Open : '.$item->title;
	}

	if( in_array('main-nav', explode(' ', $args->menu_class) ) && $atts['href'] == '#') {
		$atts['class'] 			= 'no-links';
	}

	//print_r($atts);
	return $atts;

}
add_filter('nav_menu_link_attributes', 'waff_add_additional_data_on_a', 10, 4);

/* ADDS CUSTOM ID TO WALKERS
================================================== */
//$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
//$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
function waff_add_additional_id_on_li($id, $item, $args, $depth) {

    if( in_array('sub-nav', explode(' ', $args->menu_class) ) && $depth == 0) {
    	$id = sanitize_title($item->title);
    }
	return $id;
}
add_filter('nav_menu_item_id', 'waff_add_additional_id_on_li', 10, 4);

/* ADDS CUSTOM CLASS TO WALKERS
================================================== */
function waff_add_additional_class_on_li($classes, $item, $args, $depth) {

    if( in_array('sub-nav', explode(' ', $args->menu_class) ) && $depth == 0) {
        $classes[] = 'collapse collapse-menu';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'waff_add_additional_class_on_li', 10, 4);


/* ADDS CUSTOM CLASS TO WALKERS
================================================== */
function waff_add_li_class($classes, $item, $args, $depth) {
    if(isset($args->add_li_class) && $depth == 0) {
        $classes[] = $args->add_li_class;
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'waff_add_li_class', 1, 4);
