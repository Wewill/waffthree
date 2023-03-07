<?php 
class WP_Widget_Partners extends WP_Widget {

	protected $registered = false;

    protected $default_instance = array(
        'title'   			=> '',
        'text'   			=> '<p class="subline px-4">Nos sponsors & partenaires</p>',
		'classes' 			=> 'pt-md-10 pb-md-10 pt-5 pb-5 contrast--light bg-layoutcolor',
		'inside_classes' 	=> 'px-4',
    );  
	function __construct() {	
		$widget_ops  = array(
			'classname'                   => 'widget_partners',
			'description'                 => __( 'Print partners form.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'partners', __( '(WA) Partners', 'waff' ), $widget_ops, $control_ops );
	}
	  
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title 					= apply_filters( 'widget_title', $instance['title'] );
		$text                  	= ! empty( $instance['text'] ) ? $instance['text'] : '';
		  
        // Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        $args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_partners '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		  
		// This is where you run the code and display the output
		global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;
		?>	
		
			<div class="container-fluid px-0">
				<div class="row g-0">
					<div class="col-12 h-100" data-aos="fade-down" data-aos-delay="200">
							<?= $text ?>
							<!-- Partners carousel -->
							<div id="slick-partners" class="partners-slide mt-1 <?= $instance['inside_classes']; ?>">
														
							<?php 
							$post_type 			= ( post_type_exists('partenaire') )?'partenaire':'partner'; // Depreciated WAFFTWO V1 
							$partner_category 	= ( post_type_exists('partenaire') )?'partenaire-category':'partner-category'; // Depreciated WAFFTWO V1 
							$partner_field 		= ( post_type_exists('partenaire') )?'p-link':'waff_partner_link'; // Depreciated WAFFTWO V1 

							$partners = new WP_Query( array( 'post_type' => $post_type, 'posts_per_page' => 200 ) ); 
							
							while ( !is_admin() && $partners->have_posts() ) : $partners->the_post(); 
							
									$id 		= (( !empty($post) && $post->ID )?$post->ID:get_the_ID());
									// $post 		= $id; // Backend issue

									// DEPRECIATED WAFFTWO V.1 = FIFAM : p-link / DINARD : waff_partner_link
									if ( post_type_exists('partenaire') ) 
									$link 		= types_render_field( $partner_field, array('id' => $id) ); 
									else 
									$link 		= get_post_meta( $id, $partner_field, true );

									// Issue corrected in WAFFTWO 2.0 
									if( strpos($link, 'http') !== 0 ) {
										$link = 'https://' . $link;
									}

							    	// Get selected edition term
									$terms = get_the_terms( $id, 'edition' );
									$selected_edition = $terms[0]->term_id; 
									$selected_editions = array();
									foreach ($terms as $term) 
										$selected_editions[] = $term->term_id;
									if ( in_array($current_edition_id, $selected_editions) ) :
								    	// Post Thumbnail
										$featured_img_urls = array();
										$partenaire_featured_sizes = array(
											'full',
											'partenaire-featured-image', 
											'partenaire-featured-image-x2',
										);
										$selected_featured_sizes = $partenaire_featured_sizes;
										if ( !empty($id) && has_post_thumbnail($id) ) {  //is_singular() &&
											
											// Featured Image
											$featured_img_id     		= get_post_thumbnail_id($id);

											// Colorized image 
											$_colorized_desktop_URL = get_post_meta( $id, '_colorized_desktop_URL', true );
											$_colorized_retina_URL = get_post_meta( $id, '_colorized_retina_URL', true );
											$featured_img_urls['partenaire-featured-image-colorized'] = $_colorized_desktop_URL;
											$featured_img_urls['partenaire-featured-image-colorized-x2'] = $_colorized_retina_URL;
																	  
											// Get all sizes
											$featured_img_url_full 		= get_the_post_thumbnail_url($id);
											foreach ($selected_featured_sizes as $size) {
												$featured_img_url = wp_get_attachment_image_src( $featured_img_id, $size ); // OK
												$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full; 
											}
										}
							    ?>
								
								<div id="<?= $id ?>" class="partner-slide-item d-inline-block p-2">
									<a href="<?= esc_url($link) ?>" class="color-black link link-dark h-90-px d-flex align-items-center" title="<?php the_title(); ?>">
										<?php if ( $featured_img_urls['partenaire-featured-image-colorized'] != '' ) : ?>
										<img data-srcset="<?= $featured_img_urls['partenaire-featured-image-colorized-x2']; ?> 2x, <?= $featured_img_urls['partenaire-featured-image-colorized']; ?>" 
											 data-lazy="<?= $featured_img_urls['partenaire-featured-image-colorized']; ?>" 
											 data-sizes="" class="img-fluid" alt="Logotype partenaire : <?php the_title(); ?>" width="90" height="90">
										<?php else : ?>
										<img data-srcset="<?= $featured_img_urls['partenaire-featured-image-x2']; ?> 2x, <?= $featured_img_urls['partenaire-featured-image']; ?>" 
											 data-lazy="<?= $featured_img_urls['partenaire-featured-image']; ?>" 
											 data-sizes="" class="img-fluid" alt="Logotype partenaire : <?php the_title(); ?>" width="90" height="90">
										<?php endif; ?>
										<!--
										Sizes :
										<?php print_r($featured_img_urls); ?>  
										-->
									</a>
								</div>
										
							<?php endif; endwhile; wp_reset_postdata(); ?>														
														
							</div>
					</div>
				</div>
			</div>			
			
		<?php
		echo $args['after_widget'];
	}
	          
	// Widget Backend 
	public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, $this->default_instance );
	    if ( !isset($instance['classes']) )
	        $instance['classes'] = null;

		if ( !isset($instance['inside_classes']) )
	        $instance['inside_classes'] = null;

			/** This filter is documented in wp-includes/class-wp-editor.php */
		$text = apply_filters( 'the_editor_content', $instance['text'], $default_editor );

		// Reset filter addition.
		if ( user_can_richedit() ) {
			remove_filter( 'the_editor_content', 'format_for_editor' );
		}

		// Prevent premature closing of textarea in case format_for_editor() didn't apply or the_editor_content filter did a wrong thing.
		$escaped_text = preg_replace( '#</textarea#i', '&lt;/textarea', $text );

		// Widget admin form
		?>
		<p style="display: none;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'waff' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:', 'waff' ); ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
		</p>
	    <p>
		    <label for="<?php echo $this->get_field_id('classes'); ?>"><?php esc_html_e('Classes:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" class="widefat classes sync-input" value="<?php echo esc_attr( $instance['classes'] ); ?>"/>
		</p>
	    <p>
		    <label for="<?php echo $this->get_field_id('inside_classes'); ?>"><?php esc_html_e('Inside classes:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'inside_classes' ); ?>" name="<?php echo $this->get_field_name( 'inside_classes' ); ?>" class="widefat inside_classes sync-input" value="<?php echo esc_attr( $instance['inside_classes'] ); ?>"/>
		</p>
		<?php 
	}
	      
	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$new_instance = wp_parse_args(
			(array) $new_instance,
			$this->default_instance
		);

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['classes'] = sanitize_text_field( $new_instance['classes'] );
		$instance['inside_classes'] = sanitize_text_field( $new_instance['inside_classes'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		return $instance;
	}
	 
// Class partners ends here
} 
 
 
// Register and load the widget
function WP_Widget_Partners_init() {
    register_widget( 'WP_Widget_Partners' );
}
add_action( 'widgets_init', 'WP_Widget_Partners_init' );