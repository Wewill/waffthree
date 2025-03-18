<?php
/**
 * Adds Flash metaboxes for post type 
 */

if( true === WAFF_HAS_FLASHS_POSTTYPE) {
    add_filter( 'rwmb_meta_boxes', 'waff_flash_register_meta_boxes' );
    function waff_flash_register_meta_boxes( $meta_boxes ) {
        $prefix = 'waff_flash_';
        $themeColors = '';
        $editorColorPalette = get_theme_support( 'editor-color-palette' );
        foreach($editorColorPalette[0] as $color) {
            $themeColors .= ' / <em>' . $color['name'] . '</em> : <code>'. $color['color'] .'</code>';
        }
        $meta_boxes[] = [
            'title'      => esc_html__( '⚡︎ Flash › General', 'waff' ),
            'id'         => 'flash_content',
            'post_types' => ['flash'],
            'context'    => 'normal',
            'priority'   => 'high',
            'fields'     => [
                [
                    'type' => 'textarea',
                    'name' => esc_html__( '⚡︎ Flash Content', 'waff' ),
                    'desc' => __( 'Fill the flash content', 'waff' ) . '. ' . __( '<p class="description"><span class="important">Informations pour le catalogue :</span><br><span class="label">UK</span> Markdown available : *italic* **bold** ***label*** #small# ##huge##<br><span class="label">FR</span> <em>Markdown disponible : *italique* **gras** ***label*** #petit# ##grand##</em></p>', 'waff' ),
                    'id'   => $prefix . 'content',
                    'admin_columns' => 'after title',
                ],
                [
                    'type' => 'url',
                    'name' => esc_html__( '⚡︎ Flash URL', 'waff' ),
                    'desc' => __( 'Fill an url if you want to link to a page', 'waff' ),
                    'placeholder'       => __( 'http://', 'waff' ),
                    'size'              => 90,
                    'id'   => $prefix . 'url',
                ],
                [
                    'type' => 'color',
                    'name' => esc_html__( '⚡︎ Flash Color Picker', 'waff' ),
                    'desc' => __( 'Choose a special color if needed. <br/>Theme colors : <em>Light gray</em> : <code>#EAEFF0</code> / <em>Gray</em> : <code>#BFBFBF</code>' . $themeColors, 'waff' ),
                    'id'   => $prefix . 'color',
                    // Alpha
                    'alpha_channel' => true,
                    // Color picker options. See here: https://automattic.github.io/Iris/.
                    'js_options'    => array(
                        'palettes' => WaffTwo\Core\get_palette()
                    ),
                    'admin_columns' => 'before date',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

/**
 * Adds Homeslide metaboxespost type 
 */

if( true === WAFF_HAS_HOMESLIDES_POSTTYPE ) {
    add_filter( 'rwmb_meta_boxes', 'waff_homeslide_register_meta_boxes' );
    function waff_homeslide_register_meta_boxes( $meta_boxes ) {
        $prefix = 'waff_homeslide_';
        $themeColors = '';
        $editorColorPalette = get_theme_support( 'editor-color-palette' );
        foreach($editorColorPalette[0] as $color) {
            $themeColors .= ' / <em>' . $color['name'] . '</em> <code>'. $color['color'] .'</code>';
        }
        $meta_boxes[] = [
            'title'      => esc_html__( 'Homeslide › General', 'waff' ),
            'id'         => 'slide_content',
            'post_types' => ['homeslide'],
            'context'    => 'normal',
            'priority'   => 'high',
            'fields'     => [
                [
                    'type' => 'text',
                    'size' => 90,
                    'name' => esc_html__( 'Slider label', 'waff' ),
                    'desc' => __( 'Fill a label in upper position (optionnal).', 'waff' ),
                    'id'   => $prefix . 'label',
                    'admin_columns' => 'after title',
                    'before'        => '<span class="required clearfix" style="margin-bottom:2rem;">'.__( 'How to choose the image ? Favor a sober image, with a Shot / reverse shot, a character, a landscape, a depth of field, a nice bokey. An overloaded image will not be ideal given its large size. Better choose a great color ( in shade ) consistent with the color of the slide.', 'waff' ).'</span><br/>'.'<span class="important clearfix" style="margin-bottom:2rem;">'.__( 'For the content (below), it\'s better to not exceed <u>160 signs.</u>', 'waff' ).'</span><br/><br/>',
                ],
                /* FIFAM */
                [
                    'type'    => 'select',
                    'name'    => esc_html__( 'Slider lightness mode', 'waff' ),
                    'desc'    => __( 'Choose if the slide lightness mode is light or dark', 'waff' ),
                    'std'     => 'light',
                    'id'      => $prefix . 'slide_mode',
                    'options' => [
                        'light' => esc_html__( 'Light', 'waff' ),
                        'dark'  => esc_html__( 'Dark', 'waff' ),
                    ],
                ],
                /* ADD DINARD */
                [
                    'type'    => 'select',
                    'name'    => esc_html__( 'Slider text color mode', 'waff' ),
                    'desc'    => __( 'Choose if the slide text mode is light or dark. Overrides slider color mode.', 'waff' ),
                    'std'     => 'default',
                    'id'      => $prefix . 'text_slide_mode',
                    'options' => [
                        'default' => esc_html__( 'Default', 'waff' ),
                        'light' => esc_html__( 'Light', 'waff' ),
                        'dark'  => esc_html__( 'Dark', 'waff' ),
                    ],
                ],
                /*[
                    'type' => 'image',
                    'name' => esc_html__( 'Slide image', 'waff' ),
                    'desc' => esc_html__( 'Choose a slide image', 'waff' ),
                    'id'   => $prefix . 'slide_image',
                ],*/
                [
                    'type' => 'url',
                    'placeholder'       => __( 'http://', 'waff' ),
                    'size'              => 90,
                    'name' => esc_html__( 'Slide URL', 'waff' ),
                    'desc' => __( 'Fill an url if you want to link to a page', 'waff' ),
                    'id'   => $prefix . 'slide_url',	
                    'admin_columns' => 'after ' . $prefix . 'label',
                ],
                [
                    'type' => 'color',
                    'name' => esc_html__( 'Slide color', 'waff' ),
                    'desc' => __( 'Choose a special color if needed. <br/>Theme colors : <em>Light gray</em> : <code>#EAEFF0</code> / <em>Gray</em> : <code>#BFBFBF</code>' . $themeColors, 'waff' ),
                    'id'   => $prefix . 'slide_color',
                    'admin_columns' => 'after ' . $prefix . 'slide_url',
                    //'admin_columns' => true,
                    // Alpha
                    'alpha_channel' => false,
                    // Color picker options. See here: https://automattic.github.io/Iris/.
                    'js_options'    => array(
                        'palettes' => WaffTwo\Core\get_palette()
                    ),
                    'admin_columns' => 'before date',
                ],
                [
                    'name'              => esc_html__( 'Video', 'waff' ),
                    'desc'              => __( 'Choose a video. It will autoplay and loop it in slide. Squared format recommanded. 20 Mo maximum recommanded.', 'waff' ),
                    'id'               => $prefix . 'video',
                    'type'             => 'video',
                    'max_file_uploads' => 1,
                ],
                // [
                //     'name'              => esc_html__( 'Show content', 'waff' ),
                //     'id'                => $prefix . 'show_content',
                //     'type'              => 'switch',
                //     'label_description' => __( 'Display text content or display image/video only', 'waff' ),
                //     'style'             => 'rounded',
                //     'on_label'          => esc_html__( 'Show', 'waff' ),
                //     'off_label'         => esc_html__( 'Hide', 'waff' ),
                //     'std'               => true,
                // ]
            ],
        ];

        return $meta_boxes;
    }
}

/**
 * Adds Partner metaboxes post type 
 */

if( true === WAFF_HAS_PARTNERS_POSTTYPE ) {
    add_filter( 'rwmb_meta_boxes', 'waff_partner_register_meta_boxes' );
    function waff_partner_register_meta_boxes( $meta_boxes ) {
        $prefix = 'waff_partner_';
        $meta_boxes[] = [
            'title'      => esc_html__( 'Partner › General', 'waff' ),
            'id'         => 'slide_content',
            'post_types' => ['partner'],
            'context'    => 'normal',
            'priority'   => 'high',
            'fields'     => [
                [
                    'type' => 'url',
                    'name' => esc_html__( 'Partner link', 'waff' ),
                    //'label_description' => __( 'A ', 'waff' ),
                    'placeholder'       => __( 'http://www.google.fr', 'waff' ),
                    'size'              => 90,
                    'desc' => __( '<span class="label">UK</span> Enter a website link for the partner (if you want the image to be clickable)<br/><span class="label">FR</span> <em>Saisir un lien de site internet pour le partenaire (si vous souhaitez qu\'il soit cliquable)</em>', 'waff' ),
                    'id'   => $prefix . 'link',
                    'admin_columns' => 'after title',
                    'before'        => '<span class="important clearfix" style="margin-bottom:2rem;">'.__( 'Les logotypes doivent faire minimum 800 x 800px, recommandé 1200 x 1200px. Nous vous recommandons d\'utiliser des images transparentes et détourées en *.PNG. Pour réussir, le traitement de couleur, les blancs doivent être rendus en défonce ( c\'est à dire transparents ).', 'waff' ).'</span><br/><br/>',
                ],
            ],
        ];

        return $meta_boxes;
    }
}

/*
    Film / waff
*/

add_filter( 'rwmb_meta_boxes', 'waff_film_register_meta_boxes' );

function waff_film_register_meta_boxes( $meta_boxes ) {
    $prefix = 'waff_film_';

    $meta_boxes[] = [
        'title'      => esc_html__( 'Film color', 'waff' ),
        'id'         => 'film-color',
        'post_types' => ['film'],
        'context'    => 'side',
        'priority'   => 'default', // Instead high by default
        'fields'     => [
            [
                'name'              => __( 'Color', 'waff' ),
                'id'                => $prefix . 'color',
                'type'              => 'color',
                'label_description' => __( 'Choose a color for the film page', 'waff' ),
            ],
        ],
    ];

    return $meta_boxes;
}

/*
 	Page / gutenberg
*/

add_filter( 'rwmb_meta_boxes', 'waff_page_register_meta_boxes' );
function waff_page_register_meta_boxes( $meta_boxes ) {
    $prefix = 'waff_page_';
    
    $meta_boxes[] = [
        'title'      => esc_html__( 'Page › Options', 'waff' ),
        'id'         => 'page-options',
        'post_types' => ['page'],
        'context'    => 'side',
        'priority'   => 'high',
        'fields'     => [
            /*[
                'id'      => $prefix . 'layout',
                'name'    => esc_html__( 'Page layout', 'waff' ),
                'type'      => 'switch',
                'desc'    => esc_html__( 'Choose if the page is layouted', 'waff' ),
                'style'     => 'square',
                'on_label'  => 'Layouted',
                'off_label' => 'Raw',
            ],*/
            /*[
                'id'      => $prefix . 'mode',
                'name'    => esc_html__( 'Page mode', 'waff' ),
                'type'    => 'select',
                'desc'    => esc_html__( 'Choose if the page mode is light or dark', 'waff' ),
                'std'     => 'light',
                'options' => [
                    'light' => esc_html__( 'Light', 'waff' ),
                    'dark'  => esc_html__( 'Dark', 'waff' ),
                ],
            ],*/
            [
                'id'   => $prefix . 'title',
                'type' => 'text',
                'name' => esc_html__( 'Page title', 'waff' ),
                'desc' => __( 'Overrides default page title if filled', 'waff' ),
            ],
            [
                'id'   => $prefix . 'subtitle',
                'type' => 'text',
                'name' => esc_html__( 'Page subtitle', 'waff' ),
                'desc' => __( 'Show a subtitle if filled', 'waff' ),
            ],
            [
                'id'      => $prefix . 'content',
				'type' => 'wysiwyg', //textarea
                'name'    => esc_html__( 'Page head content', 'waff' ) . ' *',
                'desc'    => __( 'Fill the Page head content showing after title and before regular content (optionnal)', 'waff' ),
                'visible' => [
                    'when'     => [['page_template', '=', 'template-navlist.php']],
                    'relation' => 'and',
                ],
            ],
            [
                'id'         => $prefix . 'anchors',
                'type'       => 'text',
                'name'       => esc_html__( 'Page anchors', 'waff' ),
                'desc'       => __( 'Fill anchors by name serialized for id ( works only with Modern header style)', 'waff' ),
                'clone'      => 1,
                'sort_clone' => 1,
                'max_clone'  => 20,
            ],
            [
                'id'      => $prefix . 'header_style',
                'name'    => esc_html__( 'Header style', 'waff' ),
                'type'    => 'select',
                'desc'    => __( 'Choose the header style (default: normal)', 'waff' ),
                'std'     => 'normal',
                'options' => [
                    'normal' => esc_html__( 'Normal', 'waff' ), // Logo sans intitulé
                    'full' 	 => esc_html__( 'Full', 'waff' ), // Logo entier
                    'fancy'  => esc_html__( 'Fancy', 'waff' ), // Logo en défonce sur image
                    //'naked'  => esc_html__( 'Naked', 'waff' ),
                    'modern' => esc_html__( 'Modern', 'waff' ), // Image décalé et ancres
                    'split'  => esc_html__( 'Split', 'waff' ), // Moitié image moitié texte
                ],
            ],
            /*[
                'id'   => $prefix . 'header_image',
                'type' => 'single_image',
                'name' => esc_html__( 'Header image', 'waff' ),
                'desc' => esc_html__( 'Choose a header image', 'waff' ),
            ],*/
            [
                'id'   => $prefix . 'header_color',
                'name' => esc_html__( 'Header color', 'waff' ),
                'type' => 'color',
                'desc' => __( 'Choose the header background color', 'waff' ),
                'size' => 7,
                // Alpha
			    'alpha_channel' => false,
			    // Color picker options. See here: https://automattic.github.io/Iris/.
			    'js_options'    => array(
			        'palettes' => WaffTwo\Core\get_palette()
			    ),
            ],
            [
                'id'        => $prefix . 'header_image_style',
                'type'      => 'switch',
                'name'      => esc_html__( 'Header image filter', 'waff' ),
                'desc'      => __( 'Adds a filter to the featured image ( if color header background color is filled )', 'waff' ),
                'style'     => 'square',
                'on_label'  => 'Colorized',
                'off_label' => 'Normal',
            ],
            [
                'id'   => $prefix . 'advanced_class',
                'type' => 'text',
                'name' => esc_html__( 'Advanced page class', 'waff' ),
                'desc' => __( 'Fill a custom class for the main page wrapper. Advanced feature.', 'waff' ),
            ],
        ],
        'style'      => 'normal', //seamless
    ];

    return $meta_boxes;
}

/*
 	Post / gutenberg > géré depuis un plugin 
*/

/*
add_filter( 'rwmb_meta_boxes', 'waff_post_register_meta_boxes' );
function waff_post_register_meta_boxes( $meta_boxes ) {
    $prefix = 'waff_post_';

    $meta_boxes[] = [
        'title'      => esc_html__( 'Post options', 'waff' ),
        'id'         => 'post-options',
        'post_types' => ['post'],
        'context'    => 'side',
        'priority'   => 'high',
        'fields'     => [
            [
                'id'   => $prefix . 'color',
                'name' => esc_html__( 'Post color', 'waff' ),
                'type' => 'color',
                'desc' => esc_html__( 'Choose the post color', 'waff' ),
                'size' => 7,
            ],
            
        ],
        'style'      => 'seamless',
    ];

    return $meta_boxes;
}
*/
