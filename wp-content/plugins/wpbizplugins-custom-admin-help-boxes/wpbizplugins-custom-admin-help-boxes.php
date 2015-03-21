<?php
/*
Plugin Name: WPBizPlugins Custom Admin Help Boxes
Plugin URI: http://www.wpbizplugins.com?utm_source=cahb&utm_medium=plugin&utm_campaign=pluginuri
Description: Add your own custom help boxes to the admin section of WordPress.
Version: 1.3.2
Author: Gabriel Nordeborn
Author URI: http://www.wpbizplugins.com?utm_source=cahb&utm_medium=plugin&utm_campaign=authoruri
Text Domain: wpbizplugins-cahb
*/

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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 *
 * Register activation hook for the plugin, that adds the default menu settings if necessary.
 *
 */

register_activation_hook( __FILE__, 'wpbizplugins_cahb_activation_function' );

/**
 *
 * START BY INCLUDING THE VARIOUS EMBEDDED PLUGINS. CURRENTLY:
 *  - ACF for custom fields
 *  - ReduxFramework for options
 *
 */

if( is_admin() ) {

    // If ACF isn't active, load ACF.
    if ( ! class_exists( 'Acf' ) && file_exists( dirname( __FILE__ ) . '/assets/acf/acf.php' ) ) {

        define( 'ACF_LITE' , true );
        require_once( dirname( __FILE__ ) . '/assets/acf/acf.php' );
        
    }
    

    // Include Redux if plugin isn't available
    if ( ! class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/assets/redux/ReduxCore/framework.php' ) ) {

        require_once( dirname( __FILE__ ) . '/assets/redux/ReduxCore/framework.php' );

    }

    require_once( dirname( __FILE__ ) . '/inc/redux-config.php' );

}

require_once( dirname( __FILE__ ) . '/inc/install.php' );                  // Import the installation functions
require_once( dirname( __FILE__ ) . '/inc/custom-functions.php' );         // Import our custom functions first
require_once( dirname( __FILE__ ) . '/inc/custom-posttypes.php' );         // Import our custom post types

// Load localization
function wpbizplugins_cahb_init_plugin() {

    load_plugin_textdomain( 'wpbizplugins-cahb', false, dirname( __FILE__ ) . '/lang' );

}

add_action( 'init', 'wpbizplugins_cahb_init_plugin' );

/**
 * LOAD APPROPRIATE STYLES
 * Load fitvids.js
 *
 */

function wpbizplugins_cahb_enqueue_scripts() {

    wp_register_script( 'fitvids', plugins_url( '/assets/fitvids.js', __FILE__ ), null, null, true );
    wp_enqueue_script( 'fitvids' );

}

add_action( 'admin_enqueue_scripts', 'wpbizplugins_cahb_enqueue_scripts' );


/**
 *
 * THE FUNCTION FOR OUTPUTTING THE METABOXES
 * The actual function that takes care of outputting all of the metaboxes on the appropriate pages.
 *
 */

function wpbizplugins_cahb_get_metaboxes_array_correct_hook() {

    global $metaboxes_array;

    $metaboxes_array = wpbizplugins_cahb_get_metaboxes_array();

}

if( is_admin() == true ) add_action( 'wp_loaded', 'wpbizplugins_cahb_get_metaboxes_array_correct_hook' );

function wpbizplugins_cahb_check_and_output_metaboxes() {

    global $metaboxes_array;

    wpbizplugins_cahb_register_metaboxes( $metaboxes_array );

    unset( $metaboxes_array );

}

// Only add the action if it's on an admin page
if( is_admin() == true ) add_action( 'add_meta_boxes', 'wpbizplugins_cahb_check_and_output_metaboxes' );

function wpbizplugins_cahb_check_and_output_dashboard_widgets() {

    global $metaboxes_array;

    wpbizplugins_cahb_register_dashboard_widgets( $metaboxes_array );

    unset( $metaboxes_array );

}

// Only add the action if it's on an admin page
if( is_admin() == true ) add_action( 'wp_dashboard_setup', 'wpbizplugins_cahb_check_and_output_dashboard_widgets' );

/**
 * Print the contents of the help box.
 *
 * @param string $id The ID of the post containing the help box contents.
 * @param string $content The content to be printed.
 * @return null Returns nothing.
 * @since 1.0
 *
 */

function wpbizplugins_cahb_print_help_box_content( $post, $extra_data ) {

    global $wpbizplugins_cahb_options;

    $data = $extra_data['args'];

    echo '<div class="wpbizplugins-cahb-metabox">';

    // Displays the popup and the popup content if that is set to be enabled
    if( $data['use_popup'] == 1 ) {

        $unique_id = uniqid();

        if( ( isset( $wpbizplugins_cahb_options['company_logo']['url'] ) ) && ( $wpbizplugins_cahb_options['company_logo']['url'] != '' ) ) {

            // Print the company logo in the center
            echo '<img style="max-width:90%;" src="' . $wpbizplugins_cahb_options['company_logo']['url'] . '" class="aligncenter wpbizplugins-cahb-company-logo">';
            echo '<hr />';

        }

        add_thickbox();
        echo '<p>' . do_shortcode( $data['popup_button_text_before'] ) . '</p>';
        echo '<a href="#TB_inline?width=640&height=600&inlineId=wpbizplugins-thickbox-' . $unique_id . '" class="thickbox wpbizplugins-cahb-button btn-green"><span class="dashicons dashicons-visibility"></span> ' . $data['popup_button_text'] . '</a>';
        echo '<div id="wpbizplugins-thickbox-' . $unique_id . '" style="display:none; max-width:100%;">';
        echo '<div class="wpbizplugins-thickbox-content">';

    }


    if( ( isset( $wpbizplugins_cahb_options['company_logo']['url'] ) ) && ( $wpbizplugins_cahb_options['company_logo']['url'] != '' ) ) {

        // Print the company logo in the center
        echo '<img style="max-width:90%;" src="' . $wpbizplugins_cahb_options['company_logo']['url'] . '" class="aligncenter wpbizplugins-cahb-company-logo">';
        echo '<hr />';

    }

    echo '<div class="wpbizplugins-cahb-content">';

    if( ( $data[ 'autop' ] == "" ) || ( $data[ 'autop' ] == 1 ) ) echo apply_filters( 'the_content', $data['content'] ); else echo $data['content'];
    echo '</div>';

    $is_any_support_option_set = false;
    
    // Check if any of the support options are set or not
    if( ( isset( $wpbizplugins_cahb_options['support_email'] ) ) && ( $wpbizplugins_cahb_options['support_email'] != '' ) ) $is_any_support_option_set = true;
    if( ( isset( $wpbizplugins_cahb_options['support_phone'] ) ) && ( $wpbizplugins_cahb_options['support_phone'] != '' ) ) $is_any_support_option_set = true;
    if( ( isset( $wpbizplugins_cahb_options['support_url'] ) ) && ( $wpbizplugins_cahb_options['support_url'] != '' ) ) $is_any_support_option_set = true;

    // Check if we are to show the extra information here.
    if( ( $data['show_extras'] == 1 ) && ( $is_any_support_option_set == true ) ) {

        echo '<div class="wpbizplugins-cahb-support-section">';
        echo '<p><em>' . $wpbizplugins_cahb_options['support_text'] . '</em></p>';
        echo '<div class="wpbizplugins-cahb-extra-buttons">';
        if( ( isset( $wpbizplugins_cahb_options['support_email'] ) ) && ( $wpbizplugins_cahb_options['support_email'] != '' ) ) echo '<a href="mailto:' . $wpbizplugins_cahb_options['support_email'] . '" target="_blank" class="wpbizplugins-cahb-button btn-blue"><span class="dashicons dashicons-email-alt"></span> E-mail <em>(' . $wpbizplugins_cahb_options['support_email'] . ')</em></a>';
        if( ( isset( $wpbizplugins_cahb_options['support_phone'] ) ) && ( $wpbizplugins_cahb_options['support_phone'] != '' ) ) echo '<a href="tel:' . wpbizplugins_cahb_clean_number( $wpbizplugins_cahb_options['support_phone'] ) . '" target="_blank" class="wpbizplugins-cahb-button btn-orange"><span class="dashicons dashicons-businessman"></span> Phone <em>(' . $wpbizplugins_cahb_options['support_phone'] . ')</em></a>';
        if( ( isset( $wpbizplugins_cahb_options['support_url'] ) ) && ( $wpbizplugins_cahb_options['support_url'] != '' ) ) echo '<a href="' . $wpbizplugins_cahb_options['support_url'] . '" target="_blank" class="wpbizplugins-cahb-button btn-green"><span class="dashicons dashicons-admin-links"></span> Support page</a>';
        echo '</div>';
        echo '</div>';

    }

    echo '</div>';
    if( $data['use_popup'] == 1 ) echo '</div></div>';

    unset( $wpbizplugins_cahb_options );

}

/**
 * Print the fitVids fix
 *
 */

function wpbizplugins_cahb_enable_fitvids_on_content() {

    ?>
    
    <script type="text/javascript">

    jQuery(document).ready(function(){
        // Target your .container, .wrapper, .post, etc.
        jQuery(".wpbizplugins-cahb-content").fitVids();
    });

    </script>

    <?php

}

add_action( 'admin_footer', 'wpbizplugins_cahb_enable_fitvids_on_content' );
