<?php 
class WP_Widget_Newsletter extends WP_Widget {

	protected $registered = false;

    protected $default_instance = array(
        'title'   => '',
        'text'   => '<h3 class="w-70 mb-2">Inscrivez-vous à la newsletter pour ne rien rater du festival et des infos en exclusivité !</h3><p class="subline">Nous envoyons un email par mois</p>',
		'classes' => '--pt-11 pt-md-10 pb-md-10 pt-5 pb-5 contrast--light --border-bottom',
		'reduced' => '0',
    );  
	function __construct() {
		$widget_ops  = array(
			'classname'                   => 'widget_newsletter',
			'description'                 => __( 'Print newsletter form.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'newsletter', __( '(WA) Newsletter', 'waff' ), $widget_ops, $control_ops );
	}
	  
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title 					= apply_filters( 'widget_title', $instance['title'] );
		// $text                  	= ! empty( $instance['text'] ) ? $instance['text'] : '';
		$reduced 				= ! empty( $instance['reduced'] ) ? '1' : '0';
		$button 				= ! empty( $instance['button'] ) ? '1' : '0';
		$url                  	= ! empty( $instance['url'] ) ? $instance['url'] : '';
		  
		$text = apply_filters( 'the_editor_content', $instance['text'], $default_editor );

        // Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        $args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_newsletter '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {echo $args['before_title'] . $title . $args['after_title'];}
		  
		// This is where you run the code and display the output
		if ( $button === '1') {
			// Button version ( text + link )
			?>

				<div class="container-fluid px-3 full-newsletter">
					<div class="row g-0">
						<div class="col-8" data-aos="fade-down" data-aos-delay="0">								
							<!-- Content -->
							<?php echo $text; ?>			
						</div>
						<div class="col-4 d-flex flex-center" data-aos="fade-down" data-aos-delay="200">								
							<!-- Url  -->
							<a class="btn btn-outline-color-main btn-lg" href="<?= $url; ?>" title="Go to newsletter">S'inscrire<span class="d-none d-sm-inline"> à la newsletter<span></a>
						</div>
					</div>
				</div>

			<?php

		} else {
			// Reduced version ( half / half )
			if ( $reduced === '1' ) :
			?>
			
				<div class="container-fluid px-0 reduced-newsletter">
					<div class="row g-0 align-items-center">

						<div class="col-12 col-sm-6 ps-3 ps-md-5 pe-3 pe-md-0" data-aos="fade-down" data-aos-delay="0">
							<!-- Content -->
							<?php echo $text; ?>
						</div>

						<div class="col-12 col-sm-6 d-table h-100 ps-3 ps-md-0 pe-3 pe-md-5" data-aos="fade-down" data-aos-delay="200">
							<!-- Chimpy SC -->
							<?php if ( defined('WAFF_NEWSLETTER_USE_CHIMPY') && WAFF_NEWSLETTER_USE_CHIMPY == 1 ) { echo do_shortcode('[chimpy_form forms="1"]'); } ?>
							<!-- Gravity SC -->
							<?php if ( defined('WAFF_NEWSLETTER_USE_GRAVITY') && WAFF_NEWSLETTER_USE_GRAVITY == 1 ) { echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true" tabindex="20" field_values=""]'); } ?>
						</div>

					</div>
				</div>
				
			<?php
			// Or normal version ( full / full )
			else :
			?>

				<div class="container-fluid px-0 full-newsletter">
					<div class="row g-0">
						<div class="col-12 d-table h-100" data-aos="fade-down" data-aos-delay="0">
							<div class="card card-body d-table-cell align-middle border-0 rounded-0">
								
								<!-- Content -->
								<?php echo $text; ?>
								
								<!-- Sep -->
								<div class="mt-5"></div>
								
								<!-- Chimpy SC -->
								<?php if ( defined('WAFF_NEWSLETTER_USE_CHIMPY') && WAFF_NEWSLETTER_USE_CHIMPY == 1 ) { print do_shortcode('[chimpy_form forms="1"]'); } ?>
								<!-- Gravity SC -->
								<?php if ( defined('WAFF_NEWSLETTER_USE_GRAVITY') && WAFF_NEWSLETTER_USE_GRAVITY == 1 ) { echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true" tabindex="20" field_values=""]'); } ?>

							</div>
			
						</div>
					</div>
				</div>

			<?php
			endif;
		}

		// Render
		echo $args['after_widget'];
	}
	          
	// Widget Backend
	public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, $this->default_instance );
	    if ( !isset($instance['classes']) ) { $instance['classes'] = null; }
	    if ( !isset($instance['url']) ) { $instance['url'] = null; }

		/** This filter is documented in wp-includes/class-wp-editor.php */
		// $text = apply_filters( 'the_editor_content', $instance['text'], $default_editor );

		// Process reduced
		$reduced = isset( $instance['reduced'] ) ? (bool) $instance['reduced'] : false;
		$button = isset( $instance['button'] ) ? (bool) $instance['button'] : false;

		// Reset filter addition.
		if ( user_can_richedit() ) {
			remove_filter( 'the_editor_content', 'format_for_editor' );
		}

		// Prevent premature closing of textarea in case format_for_editor() didn't apply or the_editor_content filter did a wrong thing.
		// $escaped_text = preg_replace( '#</textarea#i', '&lt;/textarea', $text );

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
		    <label for="<?php echo $this->get_field_id('classes'); ?>"><?php esc_html_e('Classes:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" class="widefat classes sync-input" value="<?php echo esc_attr( $instance['classes'] ); ?>"/>
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('reduced'); ?>" name="<?php echo $this->get_field_name('reduced'); ?>"<?php checked( $reduced ); ?> />
			<label for="<?php echo $this->get_field_id('reduced'); ?>"><?php _e( 'Display reduced in two columns', 'waff' ); ?></label><br />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('button'); ?>" name="<?php echo $this->get_field_name('button'); ?>"<?php checked( $button ); ?> />
			<label for="<?php echo $this->get_field_id('button'); ?>"><?php _e( 'Display url button only', 'waff' ); ?></label><br />
		</p>
		<p>
		    <label for="<?php echo $this->get_field_id('url'); ?>"><?php esc_html_e('Url:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['url'] ); ?>"/>
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

		$instance['title'] 		= sanitize_text_field( $new_instance['title'] );
		$instance['classes'] 	= sanitize_text_field( $new_instance['classes'] );
		$instance['url'] 	= sanitize_text_field( $new_instance['url'] );
		$instance['reduced'] 	= !empty( $new_instance['reduced'] ) ? 1 : 0;
		$instance['button'] 	= !empty( $new_instance['button'] ) ? 1 : 0;

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		return $instance;
	}
	 
// Class newsletter ends here
}

// Register and load the widget
function WP_Widget_Newsletter_init() {
    register_widget( 'WP_Widget_Newsletter' );
}
add_action( 'widgets_init', 'WP_Widget_Newsletter_init' );