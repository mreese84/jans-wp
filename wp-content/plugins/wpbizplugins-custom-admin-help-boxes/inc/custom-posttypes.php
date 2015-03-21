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
 * This file contains custom post types.
 *
 */

/**
 * Define the custom post type that holds the menu entries.
 *
 * @since 1.0
 *
 */

function wpbizpluins_cahb_custom_post_menu() { 
    
    // Abort if we're not in an admin page
    if( !is_admin() ) return false;

    global $wpbizplugins_cahb_options;

    if( current_user_can( $wpbizplugins_cahb_options['menu_capability'] ) ) $show_in_menu = true; else $show_in_menu = false;

    $labels = array(
        'name'               => _x( 'Custom help boxes', 'post type general name', 'wpbizplugins-cahb' ),
        'singular_name'      => _x( 'Custom help box', 'post type singular name', 'wpbizplugins-cahb' ),
        'add_new'            => __( 'Add new custom help box', 'book', 'wpbizplugins-cahb' ),
        'add_new_item'       => __( 'Add new custom help box', 'wpbizplugins-cahb'),
        'edit_item'          => __( 'Edit custom help box', 'wpbizplugins-cahb' ),
        'new_item'           => __( 'New custom help box', 'wpbizplugins-cahb' ),
        'all_items'          => __( 'All custom help boxes', 'wpbizplugins-cahb' ),
        'view_item'          => __( 'See custom help boxes', 'wpbizplugins-cahb' ),
        'search_items'       => __( 'Search custom help boxes', 'wpbizplugins-cahb' ),
        'not_found'          => __( 'No custom help box found', 'wpbizplugins-cahb' ),
        'not_found_in_trash' => __( 'No custom help boxes found in the trash', 'wpbizplugins-cahb' ), 
        'parent_item_colon'  => '',
        'menu_name'          => __('Custom Help Boxes', 'wpbizplugins-cahb')
    );
    
    $args = array(
        'labels'                => $labels,
        'description'           => __('Custom help boxes for the admin section.', 'wpbizplugins-cahb' ),
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => $show_in_menu,
        'show_in_admin_bar'     => false,
        'publicly_queryable'    => false,
        'menu_icon'             => plugins_url( '../assets/img/wpbizplugins-cahb-menuicon.png', __FILE__ ),
        'menu_position'         => null,
        'supports'              => array( 'title' ),
        'has_archive'           => false
    );
    register_post_type( 'wpbizplugins-cahb', $args );   
}

add_action( 'init', 'wpbizpluins_cahb_custom_post_menu' );

/**
 * Update the messages for the custom post type.
 *
 * @since 1.0
 *
 */

function wpbizplugins_cahb_updated_messages( $messages ) {

    global $post, $post_ID;

    $messages['wpbizplugins-cahb'] = array(
        0 => '', // Unused. Messages start at index 1.
        1 => __('Custom help box updated.', 'wpbizplugins-cahb'),
        2 => __('Custom help box updated.', 'wpbizplugins-cahb'),
        3 => __('Custom help box updated.', 'wpbizplugins-cahb'),
        4 => __('Custom help box updated.', 'wpbizplugins-cahb'),
        /* translators: %s: date and time of the revision */
        5 => '',
        6 => __('Custom help box added.', 'wpbizplugins-cahb'),
        7 => __('Custom help box saved.', 'wpbizplugins-cahb'),
        8 => __('Custom help box added.', 'wpbizplugins-cahb'),
        9 => '',
          // translators: Publish box date format, see http://php.net/date
          //date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => __('Draft of custom help box saved.', 'wpbizplugins-cahb'),
        );

    return $messages;
}
add_filter( 'post_updated_messages', 'wpbizplugins_cahb_updated_messages' );


/**
 * Adds the custom fields for ACF.
 *
 * @since 1.0
 *
 */

function wpbizplugins_cahb_load_custom_fields() {

    global $wpbizplugins_cahb_options;

    // Add Dashboard separately
    $wpbizplugins_cahb_post_types = array();
    $wpbizplugins_cahb_post_types['dashboard'] = 'dashboard'; 

    $args = array(
       //'public'   => false,
       //'_builtin' => false
    );

    $custom_post_types = get_post_types( $args, 'names' );

    if( is_array( $custom_post_types ) ) $wpbizplugins_cahb_post_types = array_merge( $wpbizplugins_cahb_post_types, $custom_post_types );

    $wpbizplugins_cahb_post_types_array = array();

    $excluded_post_types = array( 'revision', 'nav_menu_item' );

    // Create a usable array for the select from the post types, and exclude certain post types
    foreach( $wpbizplugins_cahb_post_types as $post_type ) {

        if( ! in_array( $post_type, $excluded_post_types ) ) $wpbizplugins_cahb_post_types_array[ $post_type ] = ucfirst( $post_type );

    }

    if(function_exists("register_field_group"))
    {
        register_field_group(array (
            'id' => 'acf_custom-admin-help-boxes',
            'title' => __('Custom Admin Help Boxes', 'wpbizplugins-cahb'),
            'fields' => array (
                array (
                    'key' => 'field_5374b111b5762',
                    'label' => __('Help box content', 'wpbizplugins-cahb'),
                    'name' => 'content',
                    'type' => 'wysiwyg',
                    'instructions' => __('Add instructions and other help text and elements in this box. You can add images and whatever else you want and need in here.', 'wpbizplugins-cahb'),
                    'default_value' => '',
                    'toolbar' => 'full',
                    'media_upload' => 'yes',
                ),
                
                array (
                    'key' => 'field_popup_or_not',
                    'label' => __('Popup', 'wpbizplugins-cahq'),
                    'name' => 'use_popup',
                    'type' => 'true_false',
                    'instructions' => __('Select this if you want to display your content in a popup. If so, a button will be displayed in your help box, urging your users to click the button to get help. Very good for longer and more comprehensive help content.', 'wpbizplugins-cahq') . '<br /><strong>' . __('This does not look very good when used on the Dashboard.', 'wpbizplugins-cahb') . '</strong>',
                    'message' => '',
                    'default_value' => 0,
            
                ),
                array (
                    'key' => 'field_popup_button_name',
                    'label' => __('Button text', 'wpbizplugins-cahq'),
                    'name' => 'popup_button_text',
                    'type' => 'text',
                    'default_value' => 'Click to get help',
                    'instructions' => __('What do you want the popup button to say?', 'wpbizplugins-cahq'),
                    'conditional_logic' => array (
                        'status' => 1,
                        'rules' => array (
                            
                            array (
                                'field' => 'field_popup_or_not',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                        'allorany' => 'all',
                        )
                    ),
                array (
                    'key' => 'field_popup_button_pre_text',
                    'label' => __('Text before button', 'wpbizplugins-cahq'),
                    'name' => 'popup_button_text_before',
                    'type' => 'textarea',
                    'default_value' => 'Click the button below to reveal additional help instructions and information.',
                    'instructions' => __('The text displayed before the button. Make this brief.', 'wpbizplugins-cahq'),
                    'conditional_logic' => array (
                        'status' => 1,
                        'rules' => array (
                            
                            array (
                                'field' => 'field_popup_or_not',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                        'allorany' => 'all',
                        )
                    ),
                array (
                    'key' => 'field_5374b197b5763',
                    'label' => __('Where to display this help box', 'wpbizplugins-cahb'),
                    'name' => 'where_to_display',
                    'type' => 'checkbox',
                    'instructions' => __('Select where you want this help box to show. Choose from the various post types in your WordPress install, as well as the Dashboard.', 'wpbizplugins-cahb'),
                    'required' => 1,
                    'choices' => $wpbizplugins_cahb_post_types_array,
                    'default_value' => 'post',
                    'allow_null' => 1,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'field_additional_custom_post_types',
                    'label' => __('Additional custom post types', 'wpbizplugins-cahq'),
                    'name' => 'where_to_display_additional',
                    'type' => 'text',
                    'default_value' => '',
                    'instructions' => __('If the custom post type you want to add the help box to do not appear in the list above, enter the name for that custom post type in this text field. You can add several custom post types by separating them with commas.', 'wpbizplugins-cahq')
                    ),
                array (
                    'key' => 'field_5374b210b5764',
                    'label' => __('Display placement', 'wpbizplugins-cahb'),
                    'name' => 'context',
                    'type' => 'select',
                    'instructions' => __('Choose in what place you want this help box to be displayed. Choosing "In the middle" will place the help box close to the middle of the screen. Choosing "On the side" will place it on the right hand side.', 'wpbizplugins-cahb'),
                    'choices' => array (
                        'normal' => 'In the middle',
                        'side' => 'On the side',
                    ),
                    'default_value' => 'normal',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'field_5374b270b5765',
                    'label' => __('Display priority', 'wpbizplugins-cahb'),
                    'name' => 'priority',
                    'type' => 'select',
                    'instructions' => 'Select the priority you want for the display of your help box. Set to "High up" by default, to be as visible as possible.',
                    'choices' => array (
                        'high' => 'High up',
                        'default' => 'Normal',
                        'low' => 'Low',
                    ),
                    'default_value' => 'high',
                    'allow_null' => 0,
                    'multiple' => 0,
                ),
                array (
                    'key' => 'field_add_extra_stuff',
                    'label' => __('Show extra information in help box', 'wpbizplugins-cahq'),
                    'name' => 'show_extras',
                    'type' => 'true_false',
                    'instructions' => __('De-select this if you do not want to display extras like your e-mail, phone number and more in this help box.', 'wpbizplugins-cahq'),
                    'message' => '',
                    'default_value' => 0,
            
                ),

                array (
                    'key' => 'autop',
                    'label' => __('Automatically add tags', 'wpbizplugins-cahq'),
                    'name' => 'autop',
                    'type' => 'true_false',
                    'instructions' => __( 'Untick this if you want the contents of this help box to be printed without any processing at all from WordPress. <strong>Normal users want this checked</strong>.', 'wpbizplugins-cahb' ),
                    'message' => '',
                    'default_value' => 1,
            
                ),

                array (
                    'key' => 'field_bottom_message',
                    'label' => __('Bottom message', 'wpbizplugins-cahb'),
                    'name' => '',
                    'type' => 'message',
                    'message' => __('All done!', 'wpbizplugins-cahb') . '<div><a href="http://www.wpbizplugins.com?utm_source=cahb&utm_medium=plugin&utm_campaign=cahb" target="_blank"><img style="margin-top: 20px; margin-bottom: 20px;" src="' . plugins_url( '../assets/img/wpbizplugins-footer-img.png', __FILE__ ) . '"></a></div>',
                )
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'wpbizplugins-cahb',
                        'order_no' => 0,
                        'group_no' => 0,
                    ),
                ),
            ),
            'options' => array (
                'position' => 'acf_after_title',
                'layout' => 'no_box',
                'hide_on_screen' => array (
                    0 => 'permalink',
                    1 => 'the_content',
                    2 => 'excerpt',
                    3 => 'custom_fields',
                    4 => 'discussion',
                    5 => 'comments',
                    6 => 'revisions',
                    7 => 'slug',
                    8 => 'author',
                    9 => 'format',
                    10 => 'featured_image',
                    11 => 'categories',
                    12 => 'tags',
                    13 => 'send-trackbacks',
                ),
            ),
            'menu_order' => 0,
        ));
    }

    unset( $wpbizplugins_cahb_options );

}

// Add the fields if we're in admin, and also make sure it loads last, to collect _all_ post types.
if( is_admin() ) add_action( 'admin_init', 'wpbizplugins_cahb_load_custom_fields', 9999 );

/**
 * Adds text before the title input in the edit form for our custom post type.
 *
 * @since 1.0
 *
 */


add_action( 'edit_form_top', 'wpbizplugins_cahb_before_title_edit' );
function wpbizplugins_cahb_before_title_edit() {
    if( wpbizplugins_cahb_return_post_type() == 'wpbizplugins-cahb' ) {
        echo '<hr />';
        echo '<div style="margin-bottom:20px;"><img src="' . plugins_url( '../assets/img/wpbizplugins-cahb-logo.png', __FILE__ ) . '"></div>';
        echo __('Fill in the fields and select your options on this page. Then simply press publish and you have a new, fresh custom admin help box!', 'wpbizplugins-cahb');
        echo '<hr />';
        echo '<h3>' . __('Title', 'wpbizplugins-cahb') . '</h3><p class="label">' . __('Enter the title of your custom help box.', 'wpbizplugins-cahb') . '</p>';
    }
}
