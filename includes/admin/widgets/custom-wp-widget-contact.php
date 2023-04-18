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
			'description'                 => __( 'A contact call-back widget.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'contact', __( '(WA) Contact', 'waff' ), $widget_ops, $control_ops );
	}
	  
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title 					= apply_filters( 'widget_title', $instance['title'] );
		$text                  	= ! empty( $instance['text'] ) ? $instance['text'] : '';
		$url                  	= ! empty( $instance['url'] ) ? $instance['url'] : '';
		$bg_url                 = ! empty( $instance['bg_url'] ) ? $instance['bg_url'] : '';
		  
        // Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        $args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_contact '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		  
		// This is where you run the code and display the output
		global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;
		?>	
		
			<div class="container-fluid px-0">
				<div class="row g-0">
					<div class="col-12 h-100 has-background bg-cover has-background-image bg-no-repeat bg-center-center hero-center-center-align has-padding has-huge-padding has-center-content is-fullscreen" data-aos="fade-down" data-aos-delay="200" style="background-image:url(<?= $bg_url; ?>)">
                            <div class="wp-block-button aligncenter is-style-circular"><a href="<?= $url ?>" class="wp-block-button__link --bg-white --link --color-action-1 rounded-pill"><?php esc_html_e('Contact us', 'waff'); ?></a></div>
                            <div class="d-flex align-items-center justify-content-center mt-2">
                                <?= $text ?>
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
		    <label for="<?php echo $this->get_field_id('url'); ?>"><?php esc_html_e('Contact page url:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['url'] ); ?>"/>
		</p>
        <p>
		    <label for="<?php echo $this->get_field_id('bg_url'); ?>"><?php esc_html_e('Background image url:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'bg_url' ); ?>" name="<?php echo $this->get_field_name( 'bg_url' ); ?>" class="widefat bg_url sync-input" value="<?php echo esc_attr( $instance['bg_url'] ); ?>"/>
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
        $instance['bg_url'] = sanitize_text_field( $new_instance['bg_url'] );

		return $instance;
	}
	 
// Class contact ends here
} 
 
 
// Register and load the widget
function WP_Widget_Contact_init() {
    register_widget( 'WP_Widget_Contact' );
}
add_action( 'widgets_init', 'WP_Widget_Contact_init' );