<?php 
class WP_Widget_CallToAction extends WP_Widget {

	protected $registered = false;

    protected $default_instance = array(
        'title'   			=> '',
        'text_start'   		=> '<p class="card-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>',
        'text_end'   		=> '<p class="card-text">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.</p>',
		'classes' 			=> 'mt-10 mb-n8 contrast--dark z-2',
		'inside_classes' 	=> 'ms-1 me-1 ms-lg-10 me-lg-10',
		'card_position_start'=> 'justify-content-between',
		'card_position_end' => 'justify-content-center',
		'card_classes_start'=> 'text-white',
		'card_classes_end' 	=> 'text-white',
		'fullwitdh' 		=> 'no',
		'card_display_on_mobile_start' => 'yes',
		'card_display_on_mobile_end'   => 'yes',
    );  

	private function is_widget_preview() {
        if ( did_action( 'get_header' ) || did_action( 'get_footer' ) ) 
            return false;
		else return true;
    }

	function __construct() {	
		$widget_ops  = array(
			'classname'                   => 'widget_calltoaction',
			'description'                 => __( 'A two cols call-to-action widget.', 'waff' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'calltoaction', __( '(WA) Two cols CTA', 'waff' ), $widget_ops, $control_ops );

		// Add Widget scripts — needed for uploads 
		add_action( 'admin_footer', array( $this, 'media_fields' ) );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'media_fields' ) );
	}
	  
	// Creating widget front-end
	public function widget( $args, $instance ) {

		// print_r(array($args, $instance), true);
		// echo(var_dump(is_preview()));
		// echo(var_dump(is_admin()));
		// echo(var_dump($this->is_widget_preview()));

		$title 					= apply_filters( 'widget_title', $instance['title'] );

		$label_start             = ! empty( $instance['label_start'] ) ? $instance['label_start'] : '';
		$label_end               = ! empty( $instance['label_end'] ) ? $instance['label_end'] : '';

		$title_start             = ! empty( $instance['title_start'] ) ? $instance['title_start'] : '';
		$title_end               = ! empty( $instance['title_end'] ) ? $instance['title_end'] : '';
		
		$subtitle_start          = ! empty( $instance['subtitle_start'] ) ? $instance['subtitle_start'] : '';
		$subtitle_end            = ! empty( $instance['subtitle_end'] ) ? $instance['subtitle_end'] : '';
		
		$text_start              = ! empty( $instance['text_start'] ) ? $instance['text_start'] : '';
		$text_end                = ! empty( $instance['text_end'] ) ? $instance['text_end'] : '';

		$url_start             	= ! empty( $instance['url_start'] ) ? $instance['url_start'] : '';
		$url_end               	= ! empty( $instance['url_end'] ) ? $instance['url_end'] : '';

		$card_position_start          = ! empty( $instance['card_position_start'] ) ? $instance['card_position_start'] : '';
		$card_position_end          = ! empty( $instance['card_position_end'] ) ? $instance['card_position_end'] : '';

		$card_classes_start          = ! empty( $instance['card_classes_start'] ) ? $instance['card_classes_start'] : '';
		$card_classes_end          = ! empty( $instance['card_classes_end'] ) ? $instance['card_classes_end'] : '';

		$bg_image_start             = ! empty( $instance['bg_image_start'] ) ? $instance['bg_image_start'] : '';
		$bg_image_end               = ! empty( $instance['bg_image_end'] ) ? $instance['bg_image_end'] : '';

		$fullwidth          		= ! empty( $instance['fullwidth'] ) ? $instance['fullwidth'] : '';
		
		$card_display_on_mobile_start = ! empty( $instance['card_display_on_mobile_start'] ) ? $instance['card_display_on_mobile_start'] : 'yes';
		$card_display_on_mobile_end   = ! empty( $instance['card_display_on_mobile_end'] ) ? $instance['card_display_on_mobile_end'] : 'yes';

		// Inject the Text widget's container class name alongside this widget's class name for theme styling compatibility.
        //$args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_section ', $args['before_widget'] ); // Adds classes too
        $args['before_widget'] = preg_replace( '/(?<=\sclass=["\'])/', 'widget_calltoaction '.$instance['classes'].' ', $args['before_widget'] ); // Adds classes too

		// Add class to hide widget on mobile if display_on_mobile is 'no'
		$mobile_class = ($display_on_mobile === 'no') ? ' d-none d-md-block' : '';

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
		if ( ! empty( $instance['bg_image_start'] ) ) {  //is_singular() &&
			foreach ($selected_featured_sizes as $size) {
				$bg_image_start_url = wp_get_attachment_image_src($instance['bg_image_start'], $size ); // OK
				$bg_image_start_urls[$size] = ( !empty($bg_image_start_url[0]) )?$bg_image_start_url[0]:$instance['bg_image_start']; 
			}
		}
		if ( ! empty( $instance['bg_image_end'] ) ) {  //is_singular() &&
			foreach ($selected_featured_sizes as $size) {
				$bg_image_end_url = wp_get_attachment_image_src($instance['bg_image_end'], $size ); // OK
				$bg_image_end_urls[$size] = ( !empty($bg_image_end_url[0]) )?$bg_image_end_url[0]:$instance['bg_image_end']; 
			}
		}
		  
		// This is where you run the code and display the output
		// global $current_edition, $previous_editions, $current_edition_id, $current_edition_films_are_online;
		?>	
		
			<div class="container-fluid <?= $fullwidth === 'yes' ? 'px-0':'' ?>">
				<div class="row g-0 align-items-top <?= $instance['inside_classes']; ?>" <?php echo $this->is_widget_preview() ? 'style="margin:0!important;"' : ''; ?>>

					<!-- First col -->
					<div class="col-md-6 h-250-px bg-color-layout img-shifted <?= $fullwidth === 'yes' ? 'ps-1 ps-lg-10':'rounded-start-4 md-rounded-end-4' ?><?= $card_display_on_mobile_start === 'no' ? ' d-none d-md-block' : '' ?>" data-aos="fade-up" data-aos-delay="0" <?php echo $this->is_widget_preview() ? 'style="width: 50%;"' : ''; ?>>
						<div id="calltoaction_<?= $bg_image_start ?>" class="bg-image bg-cover bg-position-center-center"></div>
						<div class="card bg-transparent border-0 --text-white h-100 p-4 d-flex flex-column <?= $card_position_start; ?> <?= $card_classes_start; ?>">
							<?php if ( ! empty( $label_start ) ) : ?>
								<h6 class="display d-inline action-2"><?= $label_start ?></h6>
							<?php endif; ?>
							<hgroup>
								<?php if ( ! empty( $subtitle_start ) ) : ?>
									<p class="card-date muted --text-muted mt-1 mb-0 headflat"><?= $subtitle_start ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $title_start ) ) : ?>
									<h3 class="card-title w-60"><a class="stretched-link link-white" href="<?= $url_start ?>"><?= $title_start ?></a></h3>
								<?php endif; ?>
							</hgroup>
							<?= $text_start ?>
							<?= $url_start != '' ? '<div><a href="'.$url_start.'" class="btn btn-sm btn-inverse-action-2" alt="'.$title_start.'">En savoir plus...</a></div>':'' ?>
						</div>
					</div>

					<!-- Last col -->
					<div class="col-md-6 h-250-px bg-color-layout img-shifted  <?= $fullwidth === 'yes' ? 'ps-1 ps-lg-10':'rounded-end-4' ?><?= $card_display_on_mobile_end === 'no' ? ' d-none d-md-block' : '' ?>" data-aos="fade-up" data-aos-delay="100" <?php echo $this->is_widget_preview() ? 'style="width: 50%;"' : ''; ?>>
						<div id="calltoaction_<?= $bg_image_end ?>" class="bg-image bg-cover bg-position-center-center"></div>
						<div class="card bg-transparent border-0 --text-white h-100 p-4 d-flex flex-column <?= $card_position_end; ?> <?= $card_classes_end; ?>">
						<?php if ( ! empty( $label_end ) ) : ?>
								<h6 class="display d-inline action-2"><?= $label_end ?></h6>
							<?php endif; ?>
							<hgroup>
								<?php if ( ! empty( $subtitle_end ) ) : ?>
									<p class="card-date muted --text-muted mt-1 mb-0 headflat"><?= $subtitle_end ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $title_end ) ) : ?>
									<h3 class="card-title w-60"><a class="stretched-link link-white" href="<?= $url_end ?>"><?= $title_end ?></a></h3>
								<?php endif; ?>
							</hgroup>
							<?= $text_end ?>
							<?= $url_end != '' ? '<div><a href="'.$url_end.'" class="btn btn-sm btn-inverse-action-2" alt="'.$title_end.'">En savoir plus...</a></div>':'' ?>
						</div>
					</div>
					
				</div>
			</div>

			<!-- Images sources-->
			<style scoped type="text/css">
					/*S = 798x755 */
					#calltoaction_<?= $bg_image_start ?> { background-image: url('<?= $bg_image_start_urls['post-featured-image-s']; ?>') }
					#calltoaction_<?= $bg_image_end ?> { background-image: url('<?= $bg_image_end_urls['post-featured-image-s']; ?>') }
				@media (min-resolution: 192dpi) {
					/*Sx2 = 1596x1510 */
					/* #calltoaction_<?= $bg_image_start ?> { background-image: url('<?= $bg_image_start_urls['post-featured-image-s-x2']; ?>') } */
					/* #calltoaction_<?= $bg_image_end ?> { background-image: url('<?= $bg_image_end_urls['post-featured-image-s-x2']; ?>') } */
				}
				
				@media (min-width: 769px) {
					/*M = 1400x1325 */
					#calltoaction_<?= $bg_image_start ?> { background-image: url('<?= $bg_image_start_urls['post-featured-image-m']; ?>') }
					#calltoaction_<?= $bg_image_end ?> { background-image: url('<?= $bg_image_end_urls['post-featured-image-m']; ?>') }
				}
				@media (min-width: 769px) and (min-resolution: 192dpi) {
					/*Mx2 = 2800x2650 */
					/* #calltoaction_<?= $bg_image_start ?> { background-image: url('<?= $bg_image_start_urls['post-featured-image-m-x2']; ?>') } */
					/* #calltoaction_<?= $bg_image_end ?> { background-image: url('<?= $bg_image_end_urls['post-featured-image-m-x2']; ?>') } */
				}
				
				@media (min-width: 1400px) {
					/*XL = 1960x1855*/
					#calltoaction_<?= $bg_image_start ?> { background-image: url('<?= $bg_image_start_urls['post-featured-image']; ?>') }
					#calltoaction_<?= $bg_image_end ?> { background-image: url('<?= $bg_image_end_urls['post-featured-image']; ?>') }
				}
				@media (min-width: 1400px) and (min-resolution: 192dpi) {
					/*XLx2 = 3920x3710 */
					/* #calltoaction_<?= $bg_image_start ?> { background-image: url('<?= $bg_image_start_urls['post-featured-image-x2']; ?>') } */
					/* #calltoaction_<?= $bg_image_end ?> { background-image: url('<?= $bg_image_end_urls['post-featured-image-x2']; ?>') } */
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
		$text_start = apply_filters( 'the_editor_content', $instance['text_start'], $default_editor );
		$text_end = apply_filters( 'the_editor_content', $instance['text_end'], $default_editor );

		// Get media url 
		if ($instance['bg_image_start'])
			$bg_image_start_url = wp_get_attachment_url($instance['bg_image_start']);

		if ($instance['bg_image_end'])
			$bg_image_end_url = wp_get_attachment_url($instance['bg_image_end']);

		// Reset filter addition.
		if ( user_can_richedit() ) {
			remove_filter( 'the_editor_content', 'format_for_editor' );
		}

		// Prevent premature closing of textarea in case format_for_editor() didn't apply or the_editor_content filter did a wrong thing.
		$escaped_text_start = preg_replace( '#</textarea#i', '&lt;/textarea', $text_start );
		$escaped_text_end = preg_replace( '#</textarea#i', '&lt;/textarea', $text_end );

		// Widget admin form
		?>
		<div style="float:left; width:49%">
			<h6><?php _e( 'Left column:', 'waff' ); ?></h6>

			<p>
				<label for="<?php echo $this->get_field_id( 'label_start' ); ?>"><?php _e( 'Label:', 'waff' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'label_start' ); ?>" name="<?php echo $this->get_field_name( 'label_start' ); ?>" type="text" value="<?php echo esc_attr( $instance['label_start'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'title_start' ); ?>"><?php _e( 'Title:', 'waff' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title_start' ); ?>" name="<?php echo $this->get_field_name( 'title_start' ); ?>" type="text" value="<?php echo esc_attr( $instance['title_start'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'subtitle_start' ); ?>"><?php _e( 'Subtitle:', 'waff' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'subtitle_start' ); ?>" name="<?php echo $this->get_field_name( 'subtitle_start' ); ?>" type="text" value="<?php echo esc_attr( $instance['subtitle_start'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'text_start' ); ?>"><?php _e( 'Content:', 'waff' ); ?></label>
				<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'text_start' ); ?>" name="<?php echo $this->get_field_name( 'text_start' ); ?>"><?php echo esc_textarea( $text_start ); ?></textarea>
				<small class="description"><span class="label">INFO</span> <?php _e( 'Have to be wrapped in html. E.q. : <\p class=\"card-text\"\>', 'waff' ); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('url_start'); ?>"><?php esc_html_e('Link to page url:', 'waff'); ?></label>
				<input type="text" id="<?php echo $this->get_field_id( 'url_start' ); ?>" name="<?php echo $this->get_field_name( 'url_start' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['url_start'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'bg_image_start' ); ?>"><?php _e( 'Background image:', 'waff' ); ?></label>
				<input style="display:none;" class="widefat" id="<?php echo $this->get_field_id( 'bg_image_start' ); ?>" name="<?php echo $this->get_field_name( 'bg_image_start' ); ?>" type="media" value="<?php echo $instance['bg_image_start']; ?>" />
				<span id="preview<?php echo esc_attr( $this->get_field_id( 'bg_image_start' ) ) ?>" style="margin-right:10px;border:2px solid #eee;display:block;width: 100px;height:100px;background-image:url('<?php echo $bg_image_start_url; ?>');background-size:cover;background-repeat:no-repeat;"></span>
				<button id="<?php echo $this->get_field_id( 'bg_image_start' ); ?>" class="button select-media custommedia"><?php _e( 'Add media', 'waff' ); ?></button>
				<input style="width: 19%;" class="button remove-media" id="buttonremove" name="buttonremove" type="button" value="<?php _e( 'Clear', 'waff' ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('card_position_start'); ?>"><?php esc_html_e('Card content position:', 'waff'); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('card_position_start'); ?>"
					name="<?php echo $this->get_field_name('card_position_start'); ?>" type="text">
						<option value='justify-content-top' <?php echo ($instance['card_position_start']=='justify-content-top')?'selected':''; ?>>
							<?php _e( 'Top', 'waff' ); ?>
						</option>
						<option value='justify-content-center' <?php echo ($instance['card_position_start']=='justify-content-center')?'selected':''; ?>>
							<?php _e( 'Center', 'waff' ); ?>
						</option> 
						<option value='justify-content-bottom' <?php echo ($instance['card_position_start']=='justify-content-bottom')?'selected':''; ?>>
							<?php _e( 'Bottom', 'waff' ); ?>
						</option>
						<option value='justify-content-between' <?php echo ($instance['card_position_start']=='justify-content-between')?'selected':''; ?>>
							<?php _e( 'Between', 'waff' ); ?>
						</option> 
					</select>
				</label>
				<!-- <input type="text" id="<?php echo $this->get_field_id( 'card_position_start' ); ?>" name="<?php echo $this->get_field_name( 'card_position_start' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['card_position_start'] ); ?>" /> -->
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('card_classes_start'); ?>"><?php esc_html_e('Card classes:', 'waff'); ?></label>
				<input type="text" id="<?php echo $this->get_field_id( 'card_classes_start' ); ?>" name="<?php echo $this->get_field_name( 'card_classes_start' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['card_classes_start'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('card_display_on_mobile_start'); ?>"><?php esc_html_e('Display on mobile devices (left card):', 'waff'); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('card_display_on_mobile_start'); ?>"
					name="<?php echo $this->get_field_name('card_display_on_mobile_start'); ?>">
						<option value='yes' <?php echo ($instance['card_display_on_mobile_start']=='yes')?'selected':''; ?>>
							<?php _e( 'Yes', 'waff' ); ?>
						</option>
						<option value='no' <?php echo ($instance['card_display_on_mobile_start']=='no')?'selected':''; ?>>
							<?php _e( 'No', 'waff' ); ?>
						</option>
					</select>
				</label>
			</p>
		</div>
		<div style="float:right; width:49%">
			<h6><?php _e( 'Right column:', 'waff' ); ?></h6>

			<p>
				<label for="<?php echo $this->get_field_id( 'label_end' ); ?>"><?php _e( 'Label:', 'waff' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'label_end' ); ?>" name="<?php echo $this->get_field_name( 'label_end' ); ?>" type="text" value="<?php echo esc_attr( $instance['label_end'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'title_end' ); ?>"><?php _e( 'Title:', 'waff' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title_end' ); ?>" name="<?php echo $this->get_field_name( 'title_end' ); ?>" type="text" value="<?php echo esc_attr( $instance['title_end'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'subtitle_end' ); ?>"><?php _e( 'Subtitle:', 'waff' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'subtitle_end' ); ?>" name="<?php echo $this->get_field_name( 'subtitle_end' ); ?>" type="text" value="<?php echo esc_attr( $instance['subtitle_end'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'text_end' ); ?>"><?php _e( 'Content:', 'waff' ); ?></label>
				<textarea class="widefat" rows="8" cols="20" id="<?php echo $this->get_field_id( 'text_end' ); ?>" name="<?php echo $this->get_field_name( 'text_end' ); ?>"><?php echo esc_textarea( $text_end ); ?></textarea>
				<small class="description"><span class="label">INFO</span> <?php _e( 'Have to be wrapped in html. E.q. : <\p class=\"card-text\"\>', 'waff' ); ?></small>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('url_end'); ?>"><?php esc_html_e('Link to page url:', 'waff'); ?></label>
				<input type="text" id="<?php echo $this->get_field_id( 'url_end' ); ?>" name="<?php echo $this->get_field_name( 'url_end' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['url_end'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'bg_image_end' ); ?>"><?php _e( 'Background image:', 'waff' ); ?></label>
				<input style="display:none;" class="widefat" id="<?php echo $this->get_field_id( 'bg_image_end' ); ?>" name="<?php echo $this->get_field_name( 'bg_image_end' ); ?>" type="media" value="<?php echo $instance['bg_image_end']; ?>" />
				<span id="preview<?php echo esc_attr( $this->get_field_id( 'bg_image_end' ) ) ?>" style="margin-right:10px;border:2px solid #eee;display:block;width: 100px;height:100px;background-image:url('<?php echo $bg_image_end_url; ?>');background-size:cover;background-repeat:no-repeat;"></span>
				<button id="<?php echo $this->get_field_id( 'bg_image_end' ); ?>" class="button select-media custommedia"><?php _e( 'Add media', 'waff' ); ?></button>
				<input style="width: 19%;" class="button remove-media" id="buttonremove" name="buttonremove" type="button" value="<?php _e( 'Clear', 'waff' ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('card_position_end'); ?>"><?php esc_html_e('Card content position:', 'waff'); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('card_position_end'); ?>"
					name="<?php echo $this->get_field_name('card_position_end'); ?>" type="text">
						<option value='justify-content-top' <?php echo ($instance['card_position_end']=='justify-content-top')?'selected':''; ?>>
							<?php _e( 'Top', 'waff' ); ?>
						</option>
						<option value='justify-content-center' <?php echo ($instance['card_position_end']=='justify-content-center')?'selected':''; ?>>
							<?php _e( 'Center', 'waff' ); ?>
						</option> 
						<option value='justify-content-bottom' <?php echo ($instance['card_position_end']=='justify-content-bottom')?'selected':''; ?>>
							<?php _e( 'Bottom', 'waff' ); ?>
						</option>
						<option value='justify-content-between' <?php echo ($instance['card_position_end']=='justify-content-between')?'selected':''; ?>>
							<?php _e( 'Between', 'waff' ); ?>
						</option> 
					</select>
				</label>
				<!-- <input type="text" id="<?php echo $this->get_field_id( 'card_position_end' ); ?>" name="<?php echo $this->get_field_name( 'card_position_end' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['card_position_end'] ); ?>" /> -->
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('card_classes_end'); ?>"><?php esc_html_e('Card classes:', 'waff'); ?></label>
				<input type="text" id="<?php echo $this->get_field_id( 'card_classes_end' ); ?>" name="<?php echo $this->get_field_name( 'card_classes_end' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['card_classes_end'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('card_display_on_mobile_end'); ?>"><?php esc_html_e('Display on mobile devices (right card):', 'waff'); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('card_display_on_mobile_end'); ?>"
					name="<?php echo $this->get_field_name('card_display_on_mobile_end'); ?>">
						<option value='yes' <?php echo ($instance['card_display_on_mobile_end']=='yes')?'selected':''; ?>>
							<?php _e( 'Yes', 'waff' ); ?>
						</option>
						<option value='no' <?php echo ($instance['card_display_on_mobile_end']=='no')?'selected':''; ?>>
							<?php _e( 'No', 'waff' ); ?>
						</option>
					</select>
				</label>
			</p>
		</div>

		<div class="clearfix clear"></div>
		
		<p style="display: none;">
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'waff' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
	    <p>
		    <label for="<?php echo $this->get_field_id('classes'); ?>"><?php esc_html_e('Classes:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'classes' ); ?>" name="<?php echo $this->get_field_name( 'classes' ); ?>" class="widefat classes sync-input" value="<?php echo esc_attr( $instance['classes'] ); ?>"/>
		</p>
	    <p>
		    <label for="<?php echo $this->get_field_id('inside_classes'); ?>"><?php esc_html_e('Inside classes:', 'waff'); ?></label>
		    <input type="text" id="<?php echo $this->get_field_id( 'inside_classes' ); ?>" name="<?php echo $this->get_field_name( 'inside_classes' ); ?>" class="widefat inside_classes sync-input" value="<?php echo esc_attr( $instance['inside_classes'] ); ?>"/>
		</p>

		<p>
				<label for="<?php echo $this->get_field_id('fullwidth'); ?>"><?php esc_html_e('Container width :', 'waff'); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('fullwidth'); ?>"
					name="<?php echo $this->get_field_name('fullwidth'); ?>" type="text">
						<option value='no' <?php echo ($instance['fullwidth']=='no')?'selected':''; ?>>
							<?php _e( 'Normal', 'waff' ); ?>
						</option>
						<option value='yes' <?php echo ($instance['fullwidth']=='yes')?'selected':''; ?>>
							<?php _e( 'Full-width', 'waff' ); ?>
						</option> 
					</select>                
				</label>
				<!-- <input type="text" id="<?php echo $this->get_field_id( 'fullwidth' ); ?>" name="<?php echo $this->get_field_name( 'fullwidth' ); ?>" class="widefat url sync-input" value="<?php echo esc_attr( $instance['fullwidth'] ); ?>" /> -->
			</p>			
			<p>
				<label for="<?php echo $this->get_field_id('display_on_mobile'); ?>"><?php esc_html_e('Display on mobile devices:', 'waff'); ?>
					<select class='widefat' id="<?php echo $this->get_field_id('display_on_mobile'); ?>"
					name="<?php echo $this->get_field_name('display_on_mobile'); ?>">
						<option value='yes' <?php echo ($instance['display_on_mobile']=='yes')?'selected':''; ?>>
							<?php _e( 'Yes', 'waff' ); ?>
						</option>
						<option value='no' <?php echo ($instance['display_on_mobile']=='no')?'selected':''; ?>>
							<?php _e( 'No', 'waff' ); ?>
						</option>
					</select>
				</label>
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

		$instance['label_start'] = sanitize_text_field( $new_instance['label_start'] );
		$instance['label_end'] = sanitize_text_field( $new_instance['label_end'] );

		$instance['title_start'] = sanitize_text_field( $new_instance['title_start'] );
		$instance['title_end'] = sanitize_text_field( $new_instance['title_end'] );

		$instance['subtitle_start'] = sanitize_text_field( $new_instance['subtitle_start'] );
		$instance['subtitle_end'] = sanitize_text_field( $new_instance['subtitle_end'] );

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text_start'] = $new_instance['text_start'];
			$instance['text_end'] = $new_instance['text_end'];
		} else {
			$instance['text_start'] = wp_kses_post( $new_instance['text_start'] );
			$instance['text_end'] = wp_kses_post( $new_instance['text_end'] );
		}

        $instance['url_start'] = sanitize_text_field( $new_instance['url_start'] );
        $instance['url_end'] = sanitize_text_field( $new_instance['url_end'] );

		$instance['card_position_start'] = sanitize_text_field( $new_instance['card_position_start'] );
        $instance['card_position_end'] = sanitize_text_field( $new_instance['card_position_end'] );

		$instance['card_classes_start'] = sanitize_text_field( $new_instance['card_classes_start'] );
        $instance['card_classes_end'] = sanitize_text_field( $new_instance['card_classes_end'] );

		$instance['bg_image_start'] = ( ! empty( $new_instance['bg_image_start'] ) ) ? strip_tags( $new_instance['bg_image_start'] ) : '';
		$instance['bg_image_end'] = ( ! empty( $new_instance['bg_image_end'] ) ) ? strip_tags( $new_instance['bg_image_end'] ) : '';

		$instance['fullwidth'] = sanitize_text_field( $new_instance['fullwidth'] );
		$instance['card_display_on_mobile_start'] = sanitize_text_field( $new_instance['card_display_on_mobile_start'] );
		$instance['card_display_on_mobile_end'] = sanitize_text_field( $new_instance['card_display_on_mobile_end'] );

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
	 
// Class calltoaction ends here
} 
 
 
// Register and load the widget
function WP_Widget_CallToAction_init() {
    register_widget( 'WP_Widget_CallToAction' );
}
add_action( 'widgets_init', 'WP_Widget_CallToAction_init' );