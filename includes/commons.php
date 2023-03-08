<?php
/**
 * Commons functions
 *
 * @package     WaffTwo
 * @author      Wilhem Arnoldy
 * @license     GPL-3.0
 */

/** 
 * Commons
 * Setup a gravity forms lists
 */

add_action( 'init', function() {
    if ( class_exists( 'RWMB_Field' ) ) {
        class RWMB_GForm_Select_Field extends RWMB_Field {
            public static function html( $meta, $field ) {
				// print_r($meta);
				// print_r($field);

                $forms = GFAPI::get_forms();

                // Get saved form ID
				$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

				// print_r($_GET);
				// global $post;

				// print_r(var_dump($post->ID));
                $saved_form = rwmb_get_value( 'waff_c_form', '', $post_id );

                $html = '';
                $html .= '<label for="' . $field['id'] . '">Choose a form :</label>
                        <select id="' . $field['id'] . '" name="' . $field['field_name'] . '">';
                foreach ( $forms as $id => $form ) {
                    if( $saved_form == $id ) {
                        $html .= '<option value="' . $id . '" selected >' . $form['title'] . '</option>';
                    } else {
                        $html .= '<option value="' . $id . '">' . $form['title'] . '</option>';
                    }                    
                }
                $html .= '</select>';
                return $html;
            }
        }
    }

} );