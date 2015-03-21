<?php
/*  WPBizPlugins Custom Admin Help Boxes
    Copyright 2014  Gabriel Nordeborn  (email : gabriel@wpbizplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  021101301  USA
*/

/**
 *
 * This file contains custom functions.
 *
 */

/**
 * Simple function for checking whether or not we're on a certain post type.
 *
 * @return Returns the current post type.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_return_post_type() {

    global $post, $typenow, $current_screen;

    //we have a post so we can just get the post type from that
    if ( $post && $post->post_type )
        return $post->post_type;

    //check the global $typenow - set in admin.php
    elseif( $typenow )
        return $typenow;

    //check the global $current_screen object - set in sceen.php
    elseif( $current_screen && $current_screen->post_type )
        return $current_screen->post_type;

    //lastly check the post_type querystring
    elseif( isset( $_REQUEST['post_type'] ) )
        return sanitize_key( $_REQUEST['post_type'] );

    elseif (get_post_type($_REQUEST['post']))
            return get_post_type($_REQUEST['post']);
    //we do not know the post type!
    return null;

}

/**
 * Clean a string for use as HTML ID or similar element.
 *
 * @param string $string The string to be cleaned.
 * @return string Returns the cleaned string.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_clean_string_for_html_id( $string ) {

    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);

    return $string;

}


/**
 * Get an array of all metaboxes that should be added. Mostly to get around weird issues with pulling posts inside a post.
 *
 * @return array Returns an array of all the metaboxes + their settings.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_get_metaboxes_array() {

    $metaboxes_array = array();

    $args = array(
    
        'post_type'         => 'wpbizplugins-cahb',
        'post_status'       => 'publish',
        'posts_per_page'    => -1
        
    );

    $help_boxes = get_posts( $args );

    if( ! empty( $help_boxes ) ) {

        foreach( $help_boxes as $help_box ) {

            $help_box_id = $help_box->ID;

            // Get the various needed fields
            $title = get_the_title( $help_box_id );
            $html_id = wpbizplugins_cahb_clean_string_for_html_id( $title );

            // Get the data of where to display the boxes
            $where_to_display = get_post_meta( $help_box_id, 'where_to_display', true );
            // Get the additional text field, and get rid of all whitespace
            $where_to_display_additional = preg_replace('/\s+/', '', get_post_meta( $help_box_id, 'where_to_display_additional', true ) );
            $where_to_display_additional = sanitize_text_field( $where_to_display_additional );
            $where_to_display_additional_array = explode(',', $where_to_display_additional );

            if( ( is_array( $where_to_display_additional ) ) && ( is_array( $where_to_display ) ) ) $where_to_display = array_merge( $where_to_display, $where_to_display_additional_array );

            $content = get_post_meta( $help_box_id, 'content', true );
            $context = get_post_meta( $help_box_id, 'context', true );
            $priority = get_post_meta( $help_box_id, 'priority', true );
            $show_extras = get_post_meta( $help_box_id, 'show_extras', true );
            $use_popup = get_post_meta( $help_box_id, 'use_popup', true );
            $popup_button_text = get_post_meta( $help_box_id, 'popup_button_text', true );
            $popup_button_text_before = get_post_meta( $help_box_id, 'popup_button_text_before', true );
            $autop = get_post_meta( $help_box_id, 'autop', true );

            $metaboxes_array[] = array( 

                'html_id'                   => $html_id,
                'title'                     => $title,
                'content'                   => $content,
                'callback'                  => 'wpbizplugins_cahb_print_help_box_content',
                'where_to_display'          => $where_to_display,
                'context'                   => $context,
                'priority'                  => $priority,
                'show_extras'               => $show_extras,
                'use_popup'                 => $use_popup,
                'popup_button_text'         => $popup_button_text,
                'popup_button_text_before'  => $popup_button_text_before,
                'autop'                     => $autop

            );
        }

    }

    return $metaboxes_array;

    wp_reset_postdata();
    wp_reset_query();
}

/**
 *
 * Function to register regular metaboxes in the correct hooks.
 *
 * @param array All the metaboxes, in an array.
 * @return null Returns nothing.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_register_metaboxes( $metaboxes_array ) {

    foreach( $metaboxes_array as $metabox ) {

        if( is_array( $metabox[ 'where_to_display' ] ) ) {

            foreach( $metabox['where_to_display'] as $where_to_display ) {

                if( ( $where_to_display != 'dashboard' ) && ( $where_to_display != '' ) ) {

                    add_meta_box( 

                        $metabox['html_id'],
                        $metabox['title'],
                        $metabox['callback'],
                        $where_to_display,
                        $metabox['context'],
                        $metabox['priority'],
                        array(
                            'content'                   => $metabox[ 'content' ],
                            'show_extras'               => $metabox[ 'show_extras' ],
                            'use_popup'                 => $metabox[ 'use_popup' ],
                            'popup_button_text'         => $metabox[ 'popup_button_text' ],
                            'popup_button_text_before'  => $metabox[ 'popup_button_text_before' ],
                            'autop'                     => $metabox[ 'autop' ]

                        )

                    );

                }

            }

        }

    }

}

/**
 *
 * Function to register dashboard widgets in the correct hooks.
 *
 * @param array All the dashboard widgets, in an array.
 * @return null Returns nothing.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_register_dashboard_widgets( $metaboxes_array ) {

    foreach( $metaboxes_array as $metabox ) {

        if( is_array( $metabox[ 'where_to_display' ] ) ) {

            foreach( $metabox['where_to_display'] as $where_to_display ) {

                if( ( $where_to_display == 'dashboard' ) && ( $where_to_display != '' ) ) {

                    add_meta_box( 

                        $metabox['html_id'],
                        $metabox['title'],
                        $metabox['callback'],
                        $where_to_display,
                        $metabox['context'],
                        $metabox['priority'],
                        array(
                            'content'                   => $metabox['content'],
                            'show_extras'               => $metabox['show_extras'],
                            'use_popup'                 => $metabox['use_popup'],
                            'popup_button_text'         => $metabox['popup_button_text'],
                            'popup_button_text_before'  => $metabox['popup_button_text_before'],
                            'autop'                     => $metabox[ 'autop' ]
                        )

                    );

                }

            }

        }

    }

}

/**
 * Print the CSS style for the buttons.
 *
 */

function wpbizplugins_cahb_print_plugin_styles() {

    global $wpbizplugins_cahb_options;

    echo '<style type="text/css">';

    echo wpbizplugins_cahb_minify_css( '

    .wpbizplugins-cahb-content {

        margin-top: 20px;
        margin-bottom: 20px;

    }

    .wpbizplugins-cahb-metabox {
        padding: 10px 10px 10px 10px;
        margin: 10px 10px 10px 10px;
    }

    .wpbizplugins-cahb-support-section {

        background-color: #f2f2f2;
        padding-left: 10px;
        padding-right: 10px;
        padding-top: 5px;
        padding-bottom: 10px;
        -webkit-border-top-left-radius: 5px;
        -moz-border-radius-topleft:5px;
        border-top-left-radius:5px;
        -webkit-border-top-right-radius:5px;
        -moz-border-radius-topright:5px;
        border-top-right-radius:5px;
        -webkit-border-bottom-right-radius:5px;
        -moz-border-radius-bottomright:5px;
        border-bottom-right-radius:5px;
        -webkit-border-bottom-left-radius:5px;
        -moz-border-radius-bottomleft:5px;
        border-bottom-left-radius:5px;
        text-indent:0px;
        border:2px solid #ececec;


    }


    .wpbizplugins-cahb-button {

        padding: 5px 5px 5px 5px;
        margin-top: 5px;
        
        background-color:#4ea5fc;
        -webkit-border-top-left-radius: 5px;
        -moz-border-radius-topleft:5px;
        border-top-left-radius:5px;
        -webkit-border-top-right-radius:5px;
        -moz-border-radius-topright:5px;
        border-top-right-radius:5px;
        -webkit-border-bottom-right-radius:5px;
        -moz-border-radius-bottomright:5px;
        border-bottom-right-radius:5px;
        -webkit-border-bottom-left-radius:5px;
        -moz-border-radius-bottomleft:5px;
        border-bottom-left-radius:5px;
        text-indent:0px;
        border:1px solid #469df5;
        display:inline-block;
        color:#FFF;        
        
        max-width: 100%;
        width:96%;
        text-decoration:none;
        //text-align:center;
        text-shadow:1px 1px 1px #528ecc;
    }

    .wpbizplugins-cahb-button:hover { 
        background-color:#6aaaeb; 
        color: #F6F6F6;
    }

    .wpbizplugins-cahb-button:active { 
        color: #F6F6F6;
    }

    .wpbizplugins-cahb-button:focus { 
        color: #F6F6F6;
    }

    .btn-blue { background-color:#2EA0CC; border:1px solid #408099; text-shadow:0px 0px 3px #0F6485 ; }
    .btn-blue:hover { background-color:#408099; }.btn-green { background-color:#26B637; border:1px solid #42A84F; text-shadow:0px 0px 3px #0E921E; }
    .btn-green:hover { background-color:#42A84F; }.btn-red { background-color:#FF4531; border:1px solid #BF564B; text-shadow:0px 0px 3px #A61E10; }
    .btn-red:hover { background-color:#BF564B; }.btn-orange { background-color:#FF9C31; border:1px solid #BF884B; text-shadow:0px 0px 3px #A65E10; }
    .btn-orange:hover { background-color:#BF884B; }

    ' . $wpbizplugins_cahb_options['custom_css'] );

    echo '</style>';

    unset( $wpbizplugins_cahb_options );

}

add_action( 'admin_head', 'wpbizplugins_cahb_print_plugin_styles' );

/**
 * Minifies CSS somewhat.
 *
 * @param string $css The CSS.
 * @return string The minified CSS.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_minify_css( $css ) {

    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
     
    // Remove space after colons
    $css = str_replace(': ', ':', $css);
     
    // Remove whitespace
    $css = str_replace(array("\r\n", "\r", "\n", "\t", '', '', ''), '', $css);

    return $css;

}


/**
 * Returns a number cleaned from everything but digits.
 *
 * @param string $number The number to clean.
 * @return string The cleaned number.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_clean_number( $number ) {

    $number_clean = preg_replace("/[^0-9]/","", $number);
    
    return $number_clean;

}

/**
 * Return array of capabilities for use with restricting access to editing the plugin contents.
 *
 * @return array Returns an array of all available capabilities.
 * @since 1.1
 *
 */

function wpbizplugins_cahb_return_capabilities_array() {

    global $wp_roles;

    if ( ! isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }                

    $capabilities_array = array();

    foreach( $wp_roles->roles[ 'administrator' ][ 'capabilities' ] as $capability => $status ) {
        $capabilities_array[ $capability ] = $capability;
    }

    return $capabilities_array;
}
