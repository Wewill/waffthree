<?php 
defined( 'ABSPATH' ) or die( 'Invalid request.' );

if ( ! class_exists( 'Custom_Media_Sizes' ) ):
	
	class Custom_Media_Sizes {
		
		private $allowed_post_type = array('post', 'page', 'homeslide', 'film');
	   	private $_generated_sizes;
	   	private $_aborded_sizes;
	   	private $_attachement_id;
	
	
		public function __construct(){
	        $this->_generated_sizes = array();
	        $this->_aborded_sizes = array();
	        $this->_attachment_id = 0;

			add_action( 'after_setup_theme',  array( $this, 'waff_add_image_sizes') , 110);
			add_action( 'admin_init',  array( $this, 'init') );
		}

		public function init(){
	    	add_filter( 'intermediate_image_sizes', array( $this, 'waff_limit_image_sizes_by_post_type'), 999 );
			add_filter( 'media_sizes_by_post_types',  array( $this, 'get_media_sizes_by_post_types'), 10, 2 );
			add_action( 'save_post', array( $this, 'waff_check_image_sizes_by_post_type_save_post'), 10, 3 );
	
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_filter( 'removable_query_args', array($this, 'remove_notice_query_var'));

			add_filter( 'attachment_fields_to_edit',  array( $this, 'attachment_fields_to_edit' ), 10, 2 );
	
			add_filter( 'manage_media_columns',       array( $this, 'manage_media_columns' ) );
			add_action( 'manage_media_custom_column', array( $this, 'manage_media_custom_column' ), 10, 2 );
		}
		
		/*
			Functions
		*/
		// Unset multiple keys by value from an array 
		public function array_except($array, $keys){
			foreach($keys as $key => $value){
				$k = array_search($value, $array);
			    unset($array[$k]);
			}
			return $array;
		}	
		// Retrieves the attachment ID from the file URL > OK 
		public function get_attachment_id($image_url) {
		    global $wpdb;
		    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
		        return $attachment[0]; 
		}
	
		// Retrieves where the attachement post is used 
		public function get_post_types_by_attachment_id( $attachment_id ) {
			$used_in_post_types = array();
	
			if ( wp_attachment_is_image( $attachment_id ) ) {
				$thumbnail_query = new WP_Query( array(
					'meta_key'       => '_thumbnail_id',
					'meta_value'     => $attachment_id,
					'post_type'      => 'any',	
					//'fields'         => 'ids',
					'no_found_rows'  => true,
					'posts_per_page' => -1,
				) );
	
				//$used_as_thumbnail = $thumbnail_query->posts;
				foreach ($thumbnail_query->posts as $post) {
					$used_in_post_types[] = $post->post_type;
				}
				
			}
	
			$posts = array(
				'post_types' => $used_in_post_types,
			);
	
			return $posts;
		}
		
		// Retrieves where the attachement post is used in content 
		public function get_posts_in_content_by_attachment_id( $attachment_id ) {
			$attachment_urls = array( wp_get_attachment_url( $attachment_id ) );
	
			if ( wp_attachment_is_image( $attachment_id ) ) {
				foreach ( get_intermediate_image_sizes() as $size ) {
					$intermediate = image_get_intermediate_size( $attachment_id, $size );
					if ( $intermediate ) {
						$attachment_urls[] = $intermediate['url'];
					}
				}
			}
	
			$used_in_content = array();
	
			foreach ( $attachment_urls as $attachment_url ) {
				$content_query = new WP_Query( array(
					's'              => $attachment_url,
					'post_type'      => 'any',	
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'posts_per_page' => -1,
				) );
	
				$used_in_content = array_merge( $used_in_content, $content_query->posts );
			}
	
			$used_in_content = array_unique( $used_in_content );
	
			$posts = array(
				'content'   => $used_in_content,
			);
	
			return $posts;
		}
		
		// From Plugin Name: Find Posts Using Attachment / Plugin URI: http://wptavern.com/the-problem-with-image-attachments-in-wordpress
		public function get_posts_by_attachment_id( $attachment_id ) {
			$used_as_thumbnail = array();
	
			if ( wp_attachment_is_image( $attachment_id ) ) {
				$thumbnail_query = new WP_Query( array(
					'meta_key'       => '_thumbnail_id',
					'meta_value'     => $attachment_id,
					'post_type'      => 'any',	
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'posts_per_page' => -1,
				) );
	
				$used_as_thumbnail = $thumbnail_query->posts;
			}
	
			$attachment_urls = array( wp_get_attachment_url( $attachment_id ) );
	
			if ( wp_attachment_is_image( $attachment_id ) ) {
				foreach ( get_intermediate_image_sizes() as $size ) {
					$intermediate = image_get_intermediate_size( $attachment_id, $size );
					if ( $intermediate ) {
						$attachment_urls[] = $intermediate['url'];
					}
				}
			}
	
			$used_in_content = array();
	
			foreach ( $attachment_urls as $attachment_url ) {
				$content_query = new WP_Query( array(
					's'              => $attachment_url,
					'post_type'      => 'any',	
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'posts_per_page' => -1,
				) );
	
				$used_in_content = array_merge( $used_in_content, $content_query->posts );
			}
	
			$used_in_content = array_unique( $used_in_content );
	
			$posts = array(
				'thumbnail' => $used_as_thumbnail,
				'content'   => $used_in_content,
			);
	
			return $posts;
		}
	
		public function get_posts_using_attachment( $attachment_id, $context ) {
			$post_ids = $this->get_posts_by_attachment_id( $attachment_id );
	
			$posts = array_merge( $post_ids['thumbnail'], $post_ids['content'] );
			$posts = array_unique( $posts );
	
			switch ( $context ) {
				case 'column':
					$item_format   = '<strong>%1$s</strong>, %2$s %3$s<br />';
					$output_format = '%s';
					break;
				case 'details':
				default:
					$item_format   = '%1$s %3$s<br />';
					$output_format = '<div style="padding-top: 8px">%s</div>';
					break;
			}
	
			$output = '';
	
			foreach ( $posts as $post_id ) {
				$post = get_post( $post_id );
				if ( ! $post ) {
					continue;
				}
	
				$post_title = _draft_or_post_title( $post );
				$post_type  = get_post_type_object( $post->post_type );
	
				if ( $post_type && $post_type->show_ui && current_user_can( 'edit_post', $post_id ) ) {
					$link = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post_id ), $post_title );
				} else {
					$link = $post_title;
				}
	
				if ( in_array( $post_id, $post_ids['thumbnail'] ) && in_array( $post_id, $post_ids['content'] ) ) {
					$usage_context = __( '(as Featured Image and in content)', 'waff' );
				} elseif ( in_array( $post_id, $post_ids['thumbnail'] ) ) {
					$usage_context = __( '(as Featured Image)', 'waff' );
				} else {
					$usage_context = __( '(in content)', 'waff' );
				}
	
				$output .= sprintf( $item_format, $link, get_the_time( __( 'Y/m/d', 'waff' ) ), $usage_context );
			}
	
			if ( ! $output ) {
				$output = __( '(Unused)', 'waff' );
			}
	
			$output = sprintf( $output_format, $output );
	
			return $output;
		}
	
		public function attachment_fields_to_edit( $form_fields, $attachment ) {
			$form_fields['used_in'] = array(
				'label' => __( 'Used In', 'waff' ),
				'input' => 'html',
				'html'  => $this->get_posts_using_attachment( $attachment->ID, 'details' ),
			);
	
			return $form_fields;
		}
	
		/*
			Medias columns 
		*/

		public function manage_media_columns( $columns ) {
			$filtered_columns = array();
	
			foreach ( $columns as $key => $column ) {
				$filtered_columns[ $key ] = $column;
	
				if ( 'parent' === $key ) {
					$filtered_columns['used_in'] = __( 'Used In', 'waff' );
				}
			}
	
			return $filtered_columns;
		}
	
		public function manage_media_custom_column( $column_name, $attachment_id ) {
			switch ( $column_name ) {
				case 'used_in':
					echo $this->get_posts_using_attachment( $attachment_id, 'column' );
					break;
			}
		}
	
		/*
			Set sizes 
		*/
		
		public function waff_add_image_sizes( $image_sizes ){	
			// Articles & films
			// XL
			add_image_size( 'post-featured-image', 1900, 600, true );
			add_image_size( 'post-featured-image-x2', 3800, 1200, true );
			// M
			add_image_size( 'post-featured-image-m', 1000, 600, true );
			add_image_size( 'post-featured-image-m-x2', 2000, 1200, true );
			// S
			add_image_size( 'post-featured-image-s', 600, 600, true );
			add_image_size( 'post-featured-image-s-x2', 1200, 1200, true );
			// XS
			add_image_size( 'post-featured-image-xs', 400, 400, true );
			add_image_size( 'post-featured-image-xs-x2', 800, 800, true );
			

			// Films
			add_image_size( 'film-gallery-image', 1600, 1600, true );
			add_image_size( 'film-poster', 150 ); //150 w unlimited h no crop #43

	
			// Pages 
			// XL
			add_image_size( 'page-featured-image', 1900, 9999, false ); //1900x600 (normal full)
			add_image_size( 'page-featured-image-x2', 3800, 9999, false ); //1900x600 @x2
			add_image_size( 'page-featured-image-fancy', 1900, 1200, true ); //1900x1200 (fancy)
			add_image_size( 'page-featured-image-fancy-x2', 3800, 2400, true ); //1900x1200 @x2
			add_image_size( 'page-featured-image-modern', 1200, 900, true ); //1600x1100 (modern)
			add_image_size( 'page-featured-image-modern-x2', 2400, 1800, true ); //1600x1100 @x2
			// M
			add_image_size( 'page-featured-image-m', 1000, 600, true ); //1000x600 (normal full fancy) 
			add_image_size( 'page-featured-image-m-x2', 2000, 1200, true );//1000x600 @x2
			add_image_size( 'page-featured-image-modern-m', 800, 600, true );//800x550 (modern)
			add_image_size( 'page-featured-image-modern-m-x2', 1600, 1200, true );//800x550 @x2 
			// S
			add_image_size( 'page-featured-image-s', 600, 600, true ); //600x600 + 550x550 (normal full fancy) + (modern)
			add_image_size( 'page-featured-image-s-x2', 1200, 1200, true ); //600x600 + 550x550 @x2
	
			
			// Home slide XL
			// XL
			add_image_size( 'homeslide-featured-image', 1960, 9999, false ); // @x1 : 1960x1855 
			add_image_size( 'homeslide-featured-image-x2', 3920, 9999, false ); // @x2 : 3920x3710
			// M
			add_image_size( 'homeslide-featured-image-m', 1400, 9999, false ); // @x1 : 1400x1325 
			add_image_size( 'homeslide-featured-image-m-x2', 2800, 9999, false ); // @x2 : 2800x2650
			// S
			add_image_size( 'homeslide-featured-image-s', 798, 9999, false ); // @x1 : 798x755 
			add_image_size( 'homeslide-featured-image-s-x2', 1596, 9999, false ); // @x2 : 1596x1510

			// Partenaire
			add_image_size( 'partenaire-featured-image', 150, 150, true ); // @x1 : 85x85
			//add_image_size( 'partenaire-featured-image-x2', 200, 200, true ); // @x2 : 170x170
		}
		
		/*
			Filter sizes 
		*/
	
		public function get_media_sizes_by_post_types( $sizes = array(), $post_types = array() ) {
		
			//error_log('##CALL FILTER : media_sizes_by_post_types' );
			$new_image_sizes = $sizes;
						
			// Common images sizes 
			$common_image_sizes = array( 
				'thumbnail',
			    'medium',
			    'medium_large',
			    'large',
			);
			$new_image_sizes = array_unique(array_merge($new_image_sizes,$common_image_sizes), SORT_REGULAR);
			
			// Remove common images sizes 
			$remove_common_image_sizes = array( 
			    '1536x1536',
			    '2048x2048',
			);
			
			
			// De base on clean
			$new_image_sizes = array_diff($new_image_sizes, $remove_common_image_sizes);		
			//wp_die('##FILTER :<pre>'.print_r($new_image_sizes, true).print_r($remove_common_image_sizes, true).'</pre>');
	
			// Size for partenaire
			$partenaire_image_sizes = array( 
				'partenaire-featured-image', 
				//'partenaire-featured-image-x2',
			); 
			$new_image_sizes = array_diff($new_image_sizes, $partenaire_image_sizes);
			
			// Size for homeslide
			$homeslide_image_sizes = array( 
				'homeslide-featured-image', 
				'homeslide-featured-image-x2',
				'homeslide-featured-image-m', 
				'homeslide-featured-image-m-x2',
				'homeslide-featured-image-s', 
				'homeslide-featured-image-s-x2',
			);
			$new_image_sizes = array_diff($new_image_sizes, $homeslide_image_sizes);
			
			// Size for film
			$film_image_sizes = array( 
				//'post-featured-image', 
				'post-featured-image', 
				'post-featured-image-x2',
				'post-featured-image-m', 
				'post-featured-image-m-x2',
				'post-featured-image-s', 
				'post-featured-image-s-x2',
				'post-featured-image-xs',
				'post-featured-image-xs-x2',
				'film-gallery-image',
				'film-poster',
			); 
			$new_image_sizes = array_diff($new_image_sizes, $film_image_sizes);
			
			// Size for post
			$post_image_sizes = array( 
				//'post-featured-image', 
				'post-featured-image', 
				'post-featured-image-x2',
				'post-featured-image-m', 
				'post-featured-image-m-x2',
				'post-featured-image-s', 
				'post-featured-image-s-x2',
				'post-featured-image-xs',
				'post-featured-image-xs-x2',
			); 
			$new_image_sizes = array_diff($new_image_sizes, $post_image_sizes);
			
			// Size for page
			$page_image_sizes = array( 
				//'page-featured-image', 
				'page-featured-image', 
				'page-featured-image-x2',
				'page-featured-image-fancy', 
				'page-featured-image-fancy-x2',
				'page-featured-image-modern', 
				'page-featured-image-modern-x2',
				'page-featured-image-m', 
				'page-featured-image-m-x2',
				'page-featured-image-m-modern', 
				'page-featured-image-m-modern-x2',
				'page-featured-image-s', 
				'page-featured-image-s-x2',
			); 
			$new_image_sizes = array_diff($new_image_sizes, $page_image_sizes);
			
					    
			foreach ($post_types as $pt) {
			    switch ($pt) {
					case ( 'film' === $pt ) :
					    	$new_image_sizes = array_merge($new_image_sizes, $film_image_sizes);
					break;
					case ( 'post' === $pt ) :
					    	$new_image_sizes = array_merge($new_image_sizes, $post_image_sizes);
					break;
					case ( 'page' === $pt ) :
					    	$new_image_sizes = array_merge($new_image_sizes, $page_image_sizes);
					break;
					case ( 'homeslide' === $pt ) :
					    	$new_image_sizes = array_merge($new_image_sizes, $homeslide_image_sizes);
					break;
					case ( 'partenaire' === $pt ) :
					    	$new_image_sizes = array_merge($new_image_sizes, $partenaire_image_sizes);
					break;
			    }
			}	
	
			//error_log('####RETURN FILTER = NEW IMAGES SIZES BY POSTTYPE :' );
			//error_log(print_r($new_image_sizes,true));
		
		    return $new_image_sizes;
		}
	
		/*
			Limit sizes 
		*/
		
		public function waff_limit_image_sizes_by_post_type( $image_sizes ){
			//error_log('##intermediate_image_sizes : CALL');
		
			$post = (isset($_REQUEST['post_id']))?get_post( $_REQUEST['post_id'] ):get_post();
			$post_type = (isset($_REQUEST['post_id']))?(array)get_post_type( $_REQUEST['post_id'] ):(array)get_post_type();
			$post_types = array();
			
			$attachment_id = get_post_thumbnail_id($post->ID);
			// On cherche où le media est utilisé
			if ( $attachment_id != 0 || $attachment_id != false ) { 
				$attachment_used_in = $this->get_post_types_by_attachment_id($attachment_id);
				if ( !empty((array)$attachment_used_in['post_types']) && $post->post_status != 'auto-draft') {
					$post_type = (array)$attachment_used_in['post_types'];
				}
			}
			
			//error_log('##intermediate_image_sizes : MEDIA IS USED IN: ');
			//error_log(print_r($post_type,true));
	
			$image_sizes = apply_filters( 'media_sizes_by_post_types', $image_sizes, $post_type );
		    return $image_sizes;
		}
	
		/*
			Save post 
		*/
		
		public function waff_check_image_sizes_by_post_type_save_post( $post_id, $post, $update ) {
			$allowed_post_type = $this->allowed_post_type;
	
			// Sinon la fonction se lance dès le clic sur "ajouter"
			if( ! $update ) { return; }
		
			// On ne veut pas executer le code lorsque c'est une révision
			if( wp_is_post_revision( $post_id ) ) { return; }
		
			// On évite les sauvegardes automatiques
			if( defined( 'DOING_AUTOSAVE' ) and DOING_AUTOSAVE ) { return; }
		
			// Seulement si il est dans les post_type allowed
			if( ! in_array($post->post_type, $allowed_post_type) ) { return; }
			
			// Get upload dir & path
			$uploads = wp_upload_dir();
			
			// Debug
			//error_log('##CALL SAVEPOST: '.$post->ID);
			
			// On recupere le média
			$attachment_id = get_post_thumbnail_id($post->ID);
			// Seulement si le post contient une image 
			if( $attachment_id == 0 || $attachment_id == false ) { return; }
	
			// On cherche où le media est utilisé
				// SI UTILE  >https://www.wpbeginner.com/wp-themes/how-to-get-all-post-attachments-in-wordpress-except-for-featured-image/
			$attachment_used_in = $this->get_post_types_by_attachment_id($attachment_id);
			//error_log('##MEDIA IS USED IN: '.print_r($attachment_used_in, true));
	
			// On récupere le path "full"
			$path = get_attached_file( $attachment_id );
	
			// On recupere le post pour obtenir le post->guid
			$attachment = get_post($attachment_id);
	
			// On transforme l'url en path
			$pathguid = str_replace($uploads['baseurl'],$uploads['basedir'],$attachment->guid);
	
			// On recupere les metadonnées du média
			$attachment_metadata = wp_get_attachment_metadata($attachment_id);
			//error_log(print_r($attachment_metadata, true));
	
			// On travaille depuis l'image source si elle a était redimensionnée -scaled
			if ( $path != $pathguid && strstr($path,"-scaled") ) {
				$path = $pathguid;
			}
					
			// On recupere les sizes de ce média
			$sizes = array();
	        foreach ( $attachment_metadata['sizes'] as $name=>$info) { $sizes[$name] = $info; }
			//error_log('##BEFORE wp_update_attachment_metadata: '.$post->ID);
			//error_log(print_r($sizes, true));
	
	
			// On agit si les deux arrays sont différents
			$current_sizes = $sizes;
			error_log('------ ------ ------ DANS SAVEPOST > GET SIZES ');
			$needed_sizes = get_intermediate_image_sizes();
			$missing_sizes = array_diff( $needed_sizes, array_keys($current_sizes) );
			
			if ( !empty($missing_sizes) ) {
				error_log('##WE DONT HAVE THIS SIZE : ');
				error_log(print_r($missing_sizes, true));
	
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $path ); // Appel > intermediate_image_sizes
				$not_generated_sizes = array_diff( $needed_sizes, array_keys($attachment_data['sizes']) );
				$generated_sizes = $this->array_except( $needed_sizes, $not_generated_sizes );
				wp_update_attachment_metadata( $attachment_id, $attachment_data );
			
				error_log('##------ ------ ------ ------ ------ ------ FINAL AFTER wp_update_attachment_metadata '.$post->ID);
	
				// Add your query var if the coordinates are not retreive correctly.
		        $this->_aborded_sizes = $not_generated_sizes;
		        $this->_generated_sizes = $generated_sizes;		        
		        $this->_attachment_id = $attachment_id;
				add_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
	
	
			} else {
				error_log('##WE HAVE ALL SIZES !');
			}		
			
		}
		
		/*
			Admin notices 
		*/
		
		public function add_notice_query_var( $location ) {
			remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
			return add_query_arg( array( 
				'new_sizes' => true, 
				'generated_sizes' => implode(', ', $this->_generated_sizes), 
				'aborded_sizes' => implode(', ', $this->_aborded_sizes), 
				'attachment_id' => sanitize_key($this->_attachment_id),
				), $location );
		}
		
		public function remove_notice_query_var($args) {
			array_push($args, 'new_sizes');
			array_push($args, 'generated_sizes');
			array_push($args, 'aborded_sizes');
			array_push($args, 'attachment_id');
			return $args;
		}
		
		public function admin_notices() {
			if ( !isset( $_GET['new_sizes'] ) ) {
				return;
			}
			if ( $_GET['attachment_id'] == 0 ) {
				return;
			}
			if ( !empty($_GET['generated_sizes']) ) {
			?>
			<div class="notice notice-info is-dismissible">
				<p><b><?php esc_html_e( 'Updated image sizes for :', 'waff' ); ?></b> <?php esc_html($_GET['generated_sizes']); ?> • <a href="?attachment_id=<?= sanitize_key($_GET['attachment_id']); ?>"><?php esc_html_e( 'Click here to crop new image sizes', 'waff' ); ?></a></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'waff' ); ?></span></button>
			</div>
			<?php
			}
			if ( !empty($_GET['aborded_sizes']) ) {
			?>
			<div class="notice notice-error is-dismissible">
				<p><b><?php esc_html_e( 'Unable to create image sizes ( probably too small ) for :', 'waff' ); ?></b> <?php esc_html($_GET['aborded_sizes']); ?> • <a href="#"><?php esc_html_e( 'Click here to change original image', 'waff' ); ?></a></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'waff' ); ?></span></button>
			</div>			
			<?php
			}
		}
	}

	$Custom_Media_Sizes = new Custom_Media_Sizes();
	
endif;