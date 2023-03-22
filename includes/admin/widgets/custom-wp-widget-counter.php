<?php 
class WP_Widget_Counter extends WP_Widget {

	protected $registered = false;

	static $counter_vars = array();

    protected $default_instance = array(
        'title'   => '',
        'text'   => '<p class="subline text-center">Dans...</p>',
        'classes' => 'mt-0 --mt-10 mb-0 --mb-5 contrast--light position-relative',
        'col1_classes' => 'bg-action-2 --text-dark',
        'col2_classes' => 'bg-action-2 --text-dark',
        'col3_classes' => 'bg-secondary --text-dark',
    );  
	function __construct() {
		$widget_ops  = array(
			'classname'                   => 'widget_counter',
			'description'                 => __( 'Print counter before / after edition.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'counter', __( '(WA) Counter', 'waff' ), $widget_ops, $control_ops );
	}
	  
	// Creating widget front-end
	public function widget( $args, $instance ) {
		$title 					= apply_filters( 'widget_title', $instance['title'] );
		$text                  	= !empty( $instance['text'] ) ? $instance['text'] : '';

		$during_edition = (bool)do_shortcode( '[getcurrenteditionsc display="duringafter"]' ); //#41
		$counts = array();

		if ( $during_edition == false ) {
			//Then make sure our options will be added in the footer
			add_action('wp_footer', array(__CLASS__, 'add_options_to_script'));

			//Enqueue the script (registered on init)
			wp_enqueue_script( 'countdown-js', get_stylesheet_directory_uri() . '/dist/js/theme/countdown.js', array(),'1.0.0',true); // Passer dans le block

			//Add the data to the (static) class variable `$variables`, using the widget ID.
			$id = $args['widget_id'];
			self::$counter_vars['id'] = $id;
			self::$counter_vars['date'] = do_shortcode( '[getcurrenteditionsc display="formatted"]' );
		} else {
			$counts = get_counts(''); // No sections
			//print_r($counts);
		}

		//Add the data to the (static) class variable `$variables`, using the widget ID.
		$id = $args['widget_id'];
		self::$counter_vars['id'] = $id;
		self::$counter_vars['date'] = do_shortcode( '[getcurrenteditionsc display="formatted"]' );

        // Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        $args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_counter '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		  
		// This is where you run the code and display the output
		if ( $during_edition == false ) :
		?>	
			<!-- Before / After edition -->
			<div class="container-fluid px-0">
				<div class="row g-0 vh-50">
					<div class="col-4 text-center d-table h-100 <?= esc_attr( $instance['col1_classes'] ); ?>" data-aos="fade-down" data-aos-delay="0">
						<div class="card card-body d-table-cell align-middle border-0 rounded-0 <?= esc_attr( $instance['col1_classes'] ); ?>"><p class="subline text-center opacity-75"><?php _e('Edition', 'waff'); ?> <?php echo do_shortcode('[getcurrenteditionsc][/getcurrenteditionsc]'); ?></p><span class="display-impact-number"><strong class="days">00</strong></span><p class="subline"><?php _e('Days', 'waff'); ?></p></div>
					</div>
					<div class="col-4 text-center d-table h-100 <?= esc_attr( $instance['col2_classes'] ); ?>" data-aos="fade-down" data-aos-delay="200">
						<!-- Du [getcurrenteditionsc display="dates"][/getcurrenteditionsc] [getcurrenteditionsc display="month"][/getcurrenteditionsc] [getcurrenteditionsc display="year"][/getcurrenteditionsc] -->
						<div class="card card-body d-table-cell align-middle border-0 rounded-0 <?= esc_attr( $instance['col2_classes'] ); ?>"><p class="subline text-center opacity-75"><?php echo do_shortcode('[getcurrenteditionsc display="full"][/getcurrenteditionsc]'); ?></p><span class="display-impact-number"><strong class="hours">00</strong></span><p class="subline"><?php _e('Hours', 'waff'); ?></p></div>
					</div>
					<div class="col-4 text-center d-table h-100 <?= esc_attr( $instance['col3_classes'] ); ?>" data-aos="fade-down" data-aos-delay="400">
						<div class="card card-body d-table-cell align-middle border-0 rounded-0 <?= esc_attr( $instance['col3_classes'] ); ?>"><?php echo $text; ?><span class="display-impact-number"><span class="minutes">00</span></span><p class="subline"><?php _e('Minutes', 'waff'); ?></p></div>
					</div>
				</div>
			</div>		
			
		<?php
		else:
		?>	

			<!-- During edition -->
			<div class="container-fluid px-0">
				<div class="row g-0 vh-50">
					<div class="col-4 text-center d-table h-100 <?= esc_attr( $instance['col1_classes'] ); ?>" data-aos="fade-down" data-aos-delay="0">
						<div class="card card-body d-table-cell align-middle border-0 rounded-0 <?= esc_attr( $instance['col1_classes'] ); ?>"><p class="subline text-center opacity-75"><?php _e('Edition', 'waff'); ?> <?php echo do_shortcode('[getcurrenteditionsc][/getcurrenteditionsc]'); ?></p><span class="display-impact-number"><strong class="days"><?= $counts['films'] ?></strong></span><p class="subline"><?php _e('Films', 'waff'); ?></p></div>
					</div>
					<div class="col-4 text-center d-table h-100 <?= esc_attr( $instance['col2_classes'] ); ?>" data-aos="fade-down" data-aos-delay="200">
						<div class="card card-body d-table-cell align-middle border-0 rounded-0 <?= esc_attr( $instance['col2_classes'] ); ?>"><p class="subline text-center opacity-75"><?php echo do_shortcode('[getcurrenteditionsc display="full"][/getcurrenteditionsc]'); ?></p><span class="display-impact-number"><strong class="hours"><?= $counts['wpcf-p-is-guest'] ?></strong></span><p class="subline"><i class="icon icon-guest mr-1 f-12"></i> <?php _e('Guest', 'waff'); ?></p></div>
					</div>
					<div class="col-4 text-center d-table h-100 <?= esc_attr( $instance['col3_classes'] ); ?>" data-aos="fade-down" data-aos-delay="400">
						<div class="card card-body d-table-cell align-middle border-0 rounded-0 <?= esc_attr( $instance['col3_classes'] ); ?>"><p class="subline text-center opacity-75"><?php _e('It started !', 'waff'); ?></p><span class="display-impact-number"><span class="minutes"><?= $counts['wpcf-p-highlights'] ?></span></span><p class="subline"><i class="icon icon-sun mr-1 f-12"></i> <?php _e('Highlights', 'waff'); ?></p></div>
					</div>
				</div>
			</div>
			
		<?php
		endif;
		// Or get counts
		echo $args['after_widget'];
	}

	// Footer localize script
	public function add_options_to_script(){
		//If there is data to add, add it
		if(!empty(self::$counter_vars))
			 wp_localize_script( 'countdown-js', 'countdownVars', self::$counter_vars);   
	}

	// Widget Backend 
	public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, $this->default_instance );
	    if ( !isset($instance['classes']) )
	        $instance['classes'] = null;

		if ( !isset($instance['col1_classes']) )
	        $instance['col1_classes'] = null;

		if ( !isset($instance['col2_classes']) )
	        $instance['col2_classes'] = null;

		if ( !isset($instance['col3_classes']) )
	        $instance['col3_classes'] = null;

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
		    <label for="<?php echo $this->get_field_id('classes'); ?>"><?php esc_html_e('Classes:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" class="widefat classes sync-input" value="<?php echo esc_attr( $instance['classes'] ); ?>"/>
		</p>
		<!-- Classes -->
	    <p>
		    <label for="<?php echo $this->get_field_id('col1_classes'); ?>"><?php esc_html_e('First column classes:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'col1_classes' ); ?>" name="<?php echo $this->get_field_name( 'col1_classes' ); ?>" class="widefat col1_classes sync-input" value="<?php echo esc_attr( $instance['col1_classes'] ); ?>"/>
		</p>
		<p>
		    <label for="<?php echo $this->get_field_id('col2_classes'); ?>"><?php esc_html_e('Second column classes:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'col2_classes' ); ?>" name="<?php echo $this->get_field_name( 'col2_classes' ); ?>" class="widefat col2_classes sync-input" value="<?php echo esc_attr( $instance['col2_classes'] ); ?>"/>
		</p>
		<p>
		    <label for="<?php echo $this->get_field_id('col3_classes'); ?>"><?php esc_html_e('Third column classes:', 'waff' ); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'col3_classes' ); ?>" name="<?php echo $this->get_field_name( 'col3_classes' ); ?>" class="widefat col3_classes sync-input" value="<?php echo esc_attr( $instance['col3_classes'] ); ?>"/>
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
		$instance['col1_classes'] = sanitize_text_field( $new_instance['col1_classes'] );
		$instance['col2_classes'] = sanitize_text_field( $new_instance['col2_classes'] );
		$instance['col3_classes'] = sanitize_text_field( $new_instance['col3_classes'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		} else {
			$instance['text'] = wp_kses_post( $new_instance['text'] );
		}

		return $instance;
	}
	 
// Class counter ends here
} 
 
 
// Register and load the widget
function WP_Widget_Counter_init() {
    register_widget( 'WP_Widget_Counter' );
}
add_action( 'widgets_init', 'WP_Widget_Counter_init' );