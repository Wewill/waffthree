<?php 
class WP_Widget_Contact extends WP_Widget {

	protected $registered = false;

    protected $default_instance = array(
        'title'   			=> '',
        'text'   			=> '<p class="text-white text-center lead">Contact us via email@email.fr or<br/><span class="text-color-gray">01 00 11 22 33 44</span></p>',
		'classes' 			=> 'mt-md-10 mt-5 mb-0 contrast--light bg-layoutcolor',
		'inside_classes' 	=> 'px-4',
    );  
	function __construct() {	
		$widget_ops  = array(
			'classname'                   => 'widget_contact',
			'description'                 => __( 'A contact call-to-action widget.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'contact', __( '(WA) Contact CTA', 'waff' ), $widget_ops, $control_ops );

		// Add Widget scripts — needed for uploads 
		add_action( 'admin_footer', array( $this, 'media_fields' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'media_fields' ) );
	}
	  
	// Creating widget front-end
	public function widget( $args, $instance ) {

print_r(array($args, $instance), true);

		$title 					= apply_filters( 'widget_title', $instance['title'] );
		$text                  	= ! empty( $instance['text'] ) ? $instance['text'] : '';
		$url                  	= ! empty( $instance['url'] ) ? $instance['url'] : '';
		// $bg_url                 = ! empty( $instance['bg_url'] ) ? $instance['bg_url'] : ''; // Old image
		$bg_image               = ! empty( $instance['bg_image'] ) ? $instance['bg_image'] : '';
		  
        // Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        $args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_contact '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		// BG image Thumbnail
		$featured_img_urls = array();
		$post_image_sizes = array( 
			'thumbnail', 
			'post-featured-image', 
			'post-featured-image-x2',
			'post-featured-image-m', 
			'post-featured-image-m-x2',
			'post-featured-image-s', 
			'post-featured-image-s-x2',
		); 		
		$selected_featured_sizes = $post_image_sizes;
		if ( ! empty( $instance['bg_image'] ) ) {  //is_singular() &&
			foreach ($selected_featured_sizes as $size) {
				$featured_img_url = wp_get_attachment_image_src($instance['bg_image'], $size ); // OK
				$featured_img_urls[$size] = ( !empty($featured_img_url[0]) )?$featured_img_url[0]:$featured_img_url_full; 
			}
		}
		  
		// This is where you run the code and display the output
		global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;
		?>	
		
			<div class="container-fluid px-0">
				<div class="row g-0">
					<div id="contact_<?= $bg_image ?>" class="col-12 h-100 has-background bg-cover has-background-image bg-no-repeat bg-center-center hero-center-center-align has-padding has-huge-padding has-center-content is-fullscreen" data-aos="fade-down" data-aos-delay="200">
                            <div class="wp-block-button aligncenter is-style-circular"><a href="<?= $url ?>" class="wp-block-button__link --bg-white --link --color-action-1 rounded-pill"><?php esc_html_e('Contact us', 'waff'); ?></a></div>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <?= $text ?>
                            </div>
					</div>
				</div>
			</div>

			<!-- Images sources-->
			<style scoped type="text/css">
					/*S = 798x755 */
					#contact_<?= $bg_image ?> { background-image: url('<?= $featured_img_urls['post-featured-image-s']; ?>') }
				@media (min-resolution: 192dpi) {
					/*Sx2 = 1596x1510 */
					/* #contact_<?= $bg_image ?> { background-image: url('<?= $featured_img_urls['post-featured-image-s-x2']; ?>') } */
				}
				
				@media (min-width: 769px) {
					/*M = 1400x1325 */
					#contact_<?= $bg_image ?> { background-image: url('<?= $featured_img_urls['post-featured-image-m']; ?>') }
				}
				@media (min-width: 769px) and (min-resolution: 192dpi) {
					/*Mx2 = 2800x2650 */
					/* #contact_<?= $bg_image ?> { background-image: url('<?= $featured_img_urls['post-featured-image-m-x2']; ?>') } */
				}
				
				@media (min-width: 1400px) {
					/*XL = 1960x1855*/
					#contact_<?= $bg_image ?> { background-image: url('<?= $featured_img_urls['post-featured-image']; ?>') }
				}
				@media (min-width: 1400px) and (min-resolution: 192dpi) {
					/*XLx2 = 3920x3710 */
					/* #contact_<?= $bg_image ?> { background-image: url('<?= $featured_img_urls['post-featured-image-x2']; ?>') } */
				}
			</style>	
	
			
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

		// Get media url 
		if ($instance['bg_image'])
			$bg_image_url = wp_get_attachment_url($instance['bg_image']);

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
		    <label for="<?php echo $this->get_field_id('url'); ?>"><?php esc_html_e('Contact page url:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['url'] ); ?>" />
		</p>
        <!-- <p>
		    <label for="<?php echo $this->get_field_id('bg_url'); ?>"><?php esc_html_e('Background image url:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'bg_url' ); ?>" name="<?php echo $this->get_field_name( 'bg_url' ); ?>" class="widefat bg_url sync-input" value="<?php echo esc_attr( $instance['bg_url'] ); ?>"/>
		</p> Old version -->
		<p>
			<label for="<?php echo $this->get_field_id( 'bg_image' ); ?>"><?php _e( 'Background image:', 'waff' ); ?></label>
			<input style="display:none;" class="widefat" id="<?php echo $this->get_field_id( 'bg_image' ); ?>" name="<?php echo $this->get_field_name( 'bg_image' ); ?>" type="media" value="<?php echo $instance['bg_image']; ?>" />
			<span id="preview<?php echo esc_attr( $this->get_field_id( 'bg_image' ) ) ?>" style="margin-right:10px;border:2px solid #eee;display:block;width: 100px;height:100px;background-image:url('<?php echo $bg_image_url; ?>');background-size:cover;background-repeat:no-repeat;"></span>
			<button id="<?php echo $this->get_field_id( 'bg_image' ); ?>" class="button select-media custommedia"><?php _e( 'Add media', 'waff' ); ?></button>
			<input style="width: 19%;" class="button remove-media" id="buttonremove" name="buttonremove" type="button" value="<?php _e( 'Clear', 'waff' ); ?>" />
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

        $instance['url'] = sanitize_text_field( $new_instance['url'] );
        // $instance['bg_url'] = sanitize_text_field( $new_instance['bg_url'] );
		$instance['bg_image'] = ( ! empty( $new_instance['bg_image'] ) ) ? strip_tags( $new_instance['bg_image'] ) : '';

		return $instance;
	}

	// Handle media upload
	public function media_fields() {
		?><script>
			jQuery(document).ready(function($){
				if ( typeof wp.media !== 'undefined' ) {
					var _custom_media = true,
					_orig_send_attachment = wp.media.editor.send.attachment;
					$(document).on('click','.custommedia',function(e) {
						var send_attachment_bkp = wp.media.editor.send.attachment;
						var button = $(this);
						var id = button.attr('id');
						_custom_media = true;
							wp.media.editor.send.attachment = function(props, attachment){
							if ( _custom_media ) {
								$('input#'+id).val(attachment.id);
								$('span#preview'+id).css('background-image', 'url('+attachment.url+')');
								$('input#'+id).trigger('change');
							} else {
								return _orig_send_attachment.apply( this, [props, attachment] );
							};
						}
						wp.media.editor.open(button);
						return false;
					});
					$('.add_media').on('click', function(){
						_custom_media = false;
					});
					$(document).on('click', '.remove-media', function() {
						var parent = $(this).parents('p');
						parent.find('input[type="media"]').val('').trigger('change');
						parent.find('span').css('background-image', 'url()');
					});
				}
			});
		</script><?php
	}
	 
// Class contact ends here
} 
 
 
// Register and load the widget
function WP_Widget_Contact_init() {
    register_widget( 'WP_Widget_Contact' );
}
add_action( 'widgets_init', 'WP_Widget_Contact_init' );