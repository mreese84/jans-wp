<?php

function sb_instagram_menu() {
    add_menu_page(
        'Instagram Feed',
        'Instagram Feed',
        'manage_options',
        'sb-instagram-feed',
        'sb_instagram_settings_page'
    );
    add_submenu_page(
        'sb-instagram-feed',
        'Settings',
        'Settings',
        'manage_options',
        'sb-instagram-feed',
        'sb_instagram_settings_page'
    );
    add_submenu_page(
        'sb-instagram-feed',
        'License',
        'License',
        'manage_options',
        'sb-instagram-license',
        'sbi_license_page'
    );
}
add_action('admin_menu', 'sb_instagram_menu');


function sbi_register_option() {
    // creates our settings in the options table
    register_setting('sbi_license', 'sbi_license_key', 'sbi_sanitize_license' );
}
add_action('admin_init', 'sbi_register_option');

function sbi_sanitize_license( $new ) {
    $old = get_option( 'sbi_license_key' );
    if( $old && $old != $new ) {
        delete_option( 'sbi_license_status' ); // new license has been entered, so must reactivate
    }
    return $new;
}

function sbi_activate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['sbi_license_activate'] ) ) {

        // run a quick security check 
        if( ! check_admin_referer( 'sbi_nonce', 'sbi_nonce' ) )   
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $sbi_license = trim( get_option( 'sbi_license_key' ) );
            

        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'activate_license', 
            'license'   => $sbi_license, 
            'item_name' => urlencode( SBI_PLUGIN_NAME ), // the name of our product in EDD
            'url'       => home_url()
        );
        
        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, SBI_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $sbi_license_data = json_decode( wp_remote_retrieve_body( $response ) );

        //store the license data in an option
        update_option( 'sbi_license_data', $sbi_license_data );
        
        // $license_data->license will be either "valid" or "invalid"

        update_option( 'sbi_license_status', $sbi_license_data->license );

    }
}
add_action('admin_init', 'sbi_activate_license');

function sbi_deactivate_license() {

    // listen for our activate button to be clicked
    if( isset( $_POST['sbi_license_deactivate'] ) ) {

        // run a quick security check 
        if( ! check_admin_referer( 'sbi_nonce', 'sbi_nonce' ) )   
            return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $sbi_license= trim( get_option( 'sbi_license_key' ) );
            

        // data to send in our API request
        $api_params = array( 
            'edd_action'=> 'deactivate_license', 
            'license'   => $sbi_license, 
            'item_name' => urlencode( SBI_PLUGIN_NAME ), // the name of our product in EDD
            'url'       => home_url()
        );
        
        // Call the custom API.
        $response = wp_remote_get( add_query_arg( $api_params, SBI_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) )
            return false;

        // decode the license data
        $sbi_license_data = json_decode( wp_remote_retrieve_body( $response ) );
        
        // $license_data->license will be either "deactivated" or "failed"
        if( $sbi_license_data->license == 'deactivated' )
            delete_option( 'sbi_license_status' );

    }
}
add_action('admin_init', 'sbi_deactivate_license');

function sbi_check_license() {

    global $wp_version;

    $sbi_license= trim( get_option( 'sbi_license_key' ) );
        
    $api_params = array( 
        'edd_action' => 'check_license', 
        'license' => $sbi_license, 
        'item_name' => urlencode( SBI_PLUGIN_NAME ),
        'url'       => home_url()
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, SBI_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );


    if ( is_wp_error( $response ) )
        return false;

    $sbi_license_data = json_decode( wp_remote_retrieve_body( $response ) );

    if( $sbi_license_data->license == 'valid' ) {
        echo 'valid'; exit;
        // this license is still valid
    } else {
        echo 'invalid'; exit;
        // this license is no longer valid
    }
}



//License page
function sbi_license_page() {
    $sbi_license    = trim( get_option( 'sbi_license_key' ) );
    $sbi_status     = get_option( 'sbi_license_status' );
    ?>
    
    <div id="sbi_admin" class="wrap">

        <div id="header">
            <h2><?php _e('Instagram Feed Pro'); ?></h2>
        </div>

        <?php sbi_expiration_notice(); ?>
    
        <form name="form1" method="post" action="options.php">

            <h2 class="nav-tab-wrapper">
                <a href="?page=sb-instagram-feed&amp;tab=configure" class="nav-tab"><?php _e('1. Configure'); ?></a>
                <a href="?page=sb-instagram-feed&amp;tab=customize" class="nav-tab"><?php _e('2. Customize'); ?></a>
                <a href="?page=sb-instagram-feed&amp;tab=display" class="nav-tab"><?php _e('3. Display Your Feed'); ?></a>
                <a href="?page=sb-instagram-feed&amp;tab=support" class="nav-tab"><?php _e('Support'); ?></a>
                <a href="?page=sb-instagram-license" class="nav-tab nav-tab-active"><?php _e('License'); ?></a>
            </h2>
        
            <?php settings_fields('sbi_license'); ?>

            <?php
            // data to send in our API request
            $sbi_api_params = array( 
                'edd_action'=> 'check_license', 
                'license'   => $sbi_license, 
                'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
            );
            
            // Call the custom API.
            $sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

            // decode the license data
            $sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

            //Store license data in db unless the data comes back empty as wasn't able to connect to our website to get it
            if( !empty($sbi_license_data) ) update_option( 'sbi_license_data', $sbi_license_data );

            ?>
            
            <table class="form-table">
                <tbody>
                    <h3><?php _e('License'); ?></h3>

                    <tr valign="top">   
                        <th scope="row" valign="top">
                            <?php _e('Enter your license key'); ?>
                        </th>
                        <td>
                            <input id="sbi_license_key" name="sbi_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $sbi_license ); ?>" />

                            <?php if( false !== $sbi_license ) { ?>
  
                                <?php if( $sbi_status !== false && $sbi_status == 'valid' ) { ?>
                                    <?php wp_nonce_field( 'sbi_nonce', 'sbi_nonce' ); ?>
                                    <input type="submit" class="button-secondary" name="sbi_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>

                                    <?php if($sbi_license_data['license'] == 'expired'){ ?>
                                        <span class="sbi_license_status" style="color:red;"><?php _e('Expired'); ?></span>
                                    <?php } else { ?>
                                        <span class="sbi_license_status" style="color:green;"><?php _e('Active'); ?></span>
                                    <?php } ?>

                                <?php } else {
                                    wp_nonce_field( 'sbi_nonce', 'sbi_nonce' ); ?>
                                    <input type="submit" class="button-secondary" name="sbi_license_activate" value="<?php _e('Activate License'); ?>"/>

                                    <?php if($sbi_license_data['license'] == 'expired'){ ?>
                                        <span class="sbi_license_status" style="color:red;"><?php _e('Expired'); ?></span>
                                    <?php } else { ?>
                                        <span class="sbi_license_status" style="color:red;"><?php _e('Inactive'); ?></span>
                                    <?php } ?>

                                <?php } ?>
                            <?php } ?>

                            <br />
                            <i style="color: #666; font-size: 11px;"><?php _e('The license key you received when purchasing the plugin.'); ?></i>
                            <?php global $sbi_download_id; ?>
                            <p style="font-size: 13px;">
                                <a href='https://smashballoon.com/checkout/?edd_license_key=<?php echo trim($sbi_license) ?>&amp;download_id=<?php echo $sbi_download_id ?>' target='_blank'><?php _e("Renew your license"); ?></a>
                                &nbsp;&nbsp;&nbsp;&middot;
                                <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("Upgrade your license"); ?></a>

                                <span class="sbi_tooltip">
                                    <?php _e("You can upgrade your license in two ways:<br />
                                    &bull;&nbsp; Log into <a href='https://smashballoon.com/account' target='_blank'>your Account</a> and click on the 'Upgrade my License' tab<br />
                                    &bull;&nbsp; <a href='https://smashballoon.com/contact/' target='_blank'>Contact us directly</a>"); ?>
                                </span>

                            </p>
                            
                            
                        </td>
                    </tr>
                    
                </tbody>
            </table>    
            <?php submit_button(); ?>
        
        </form>

    </div>

    <?php
} //End License page



function sb_instagram_settings_page() {

    //Hidden fields
    $sb_instagram_settings_hidden_field = 'sb_instagram_settings_hidden_field';
    $sb_instagram_configure_hidden_field = 'sb_instagram_configure_hidden_field';
    $sb_instagram_customize_hidden_field = 'sb_instagram_customize_hidden_field';

    //Declare defaults
    $sb_instagram_settings_defaults = array(
        'sb_instagram_at'                   => '',
        'sb_instagram_type'                 => 'user',
        'sb_instagram_user_id'              => '',
        'sb_instagram_hashtag'              => '',
        'sb_instagram_preserve_settings'    => '',
        'sb_instagram_ajax_theme'           => false,
        'sb_instagram_width'                => '100',
        'sb_instagram_width_unit'           => '%',
        'sb_instagram_height'               => '',
        'sb_instagram_num'                  => '20',
        'sb_instagram_height_unit'          => '',
        'sb_instagram_cols'                 => '4',
        'sb_instagram_disable_mobile'       => false,
        'sb_instagram_image_padding'        => '5',
        'sb_instagram_image_padding_unit'   => 'px',
        'sb_hover_background'               => '',
        'sb_hover_text'                     => '',
        'sb_instagram_sort'                 => 'none',
        'sb_instagram_disable_lightbox'     => false,
        'sb_instagram_background'           => '',
        'sb_instagram_show_btn'             => true,
        'sb_instagram_btn_background'       => '',
        'sb_instagram_btn_text_color'       => '',
        'sb_instagram_btn_text'             => 'Load More...',
        'sb_instagram_image_res'            => 'auto',
        'sb_instagram_hide_photos'          => '',
        //Text
        'sb_instagram_show_caption'         => true,
        'sb_instagram_caption_length'       => '50',
        'sb_instagram_caption_color'        => '',
        'sb_instagram_caption_size'         => '13',
        //Meta
        'sb_instagram_show_meta'            => true,
        'sb_instagram_meta_color'           => '',
        'sb_instagram_meta_size'            => '13',
        //Header
        'sb_instagram_show_header'          => true,
        'sb_instagram_header_color'         => '',
        //Follow button
        'sb_instagram_show_follow_btn'      => true,
        'sb_instagram_folow_btn_background' => '',
        'sb_instagram_follow_btn_text_color' => '',
        'sb_instagram_follow_btn_text'      => 'Follow on Instagram',

        //Misc
        'sb_instagram_custom_css'           => '',
        'sb_instagram_custom_js'            => ''
    );
    //Save defaults in an array
    $options = wp_parse_args(get_option('sb_instagram_settings'), $sb_instagram_settings_defaults);
    update_option( 'sb_instagram_settings', $options );

    //Set the page variables
    $sb_instagram_at = $options[ 'sb_instagram_at' ];
    $sb_instagram_type = $options[ 'sb_instagram_type' ];
    $sb_instagram_user_id = $options[ 'sb_instagram_user_id' ];
    $sb_instagram_hashtag = $options[ 'sb_instagram_hashtag' ];
    $sb_instagram_preserve_settings = $options[ 'sb_instagram_preserve_settings' ];
    $sb_instagram_ajax_theme = $options[ 'sb_instagram_ajax_theme' ];
    $sb_instagram_width = $options[ 'sb_instagram_width' ];
    $sb_instagram_width_unit = $options[ 'sb_instagram_width_unit' ];
    $sb_instagram_height = $options[ 'sb_instagram_height' ];
    $sb_instagram_height_unit = $options[ 'sb_instagram_height_unit' ];
    $sb_instagram_num = $options[ 'sb_instagram_num' ];
    $sb_instagram_cols = $options[ 'sb_instagram_cols' ];
    $sb_instagram_disable_mobile = $options[ 'sb_instagram_disable_mobile' ];
    $sb_instagram_image_padding = $options[ 'sb_instagram_image_padding' ];
    $sb_instagram_image_padding_unit = $options[ 'sb_instagram_image_padding_unit' ];
    $sb_hover_background = $options[ 'sb_hover_background' ];
    $sb_hover_text = $options[ 'sb_hover_text' ];
    $sb_instagram_sort = $options[ 'sb_instagram_sort' ];
    $sb_instagram_disable_lightbox = $options[ 'sb_instagram_disable_lightbox' ];
    $sb_instagram_background = $options[ 'sb_instagram_background' ];
    $sb_instagram_show_btn = $options[ 'sb_instagram_show_btn' ];
    $sb_instagram_btn_background = $options[ 'sb_instagram_btn_background' ];
    $sb_instagram_btn_text_color = $options[ 'sb_instagram_btn_text_color' ];
    $sb_instagram_btn_text = $options[ 'sb_instagram_btn_text' ];
    $sb_instagram_image_res = $options[ 'sb_instagram_image_res' ];
    $sb_instagram_hide_photos = $options[ 'sb_instagram_hide_photos' ];
    //Text
    $sb_instagram_show_caption = $options[ 'sb_instagram_show_caption' ];
    $sb_instagram_caption_length = $options[ 'sb_instagram_caption_length' ];
    $sb_instagram_caption_color = $options[ 'sb_instagram_caption_color' ];
    $sb_instagram_caption_size = $options[ 'sb_instagram_caption_size' ];
    //Meta
    $sb_instagram_show_meta = $options[ 'sb_instagram_show_meta' ];
    $sb_instagram_meta_color = $options[ 'sb_instagram_meta_color' ];
    $sb_instagram_meta_size = $options[ 'sb_instagram_meta_size' ];
    //Header
    $sb_instagram_show_header = $options[ 'sb_instagram_show_header' ];
    $sb_instagram_header_color = $options[ 'sb_instagram_header_color' ];
    //Follow button
    $sb_instagram_show_follow_btn = $options[ 'sb_instagram_show_follow_btn' ];
    $sb_instagram_folow_btn_background = $options[ 'sb_instagram_folow_btn_background' ];
    $sb_instagram_follow_btn_text_color = $options[ 'sb_instagram_follow_btn_text_color' ];
    $sb_instagram_follow_btn_text = $options[ 'sb_instagram_follow_btn_text' ];

    //Misc
    $sb_instagram_custom_css = $options[ 'sb_instagram_custom_css' ];
    $sb_instagram_custom_js = $options[ 'sb_instagram_custom_js' ];

    // See if the user has posted us some information. If they did, this hidden field will be set to 'Y'.
    if( isset($_POST[ $sb_instagram_settings_hidden_field ]) && $_POST[ $sb_instagram_settings_hidden_field ] == 'Y' ) {

        if( isset($_POST[ $sb_instagram_configure_hidden_field ]) && $_POST[ $sb_instagram_configure_hidden_field ] == 'Y' ) {
            $sb_instagram_at = $_POST[ 'sb_instagram_at' ];
            $sb_instagram_type = $_POST[ 'sb_instagram_type' ];
            $sb_instagram_user_id = $_POST[ 'sb_instagram_user_id' ];
            $sb_instagram_hashtag = $_POST[ 'sb_instagram_hashtag' ];
            isset($_POST[ 'sb_instagram_preserve_settings' ]) ? $sb_instagram_preserve_settings = $_POST[ 'sb_instagram_preserve_settings' ] : $sb_instagram_preserve_settings = '';
            isset($_POST[ 'sb_instagram_ajax_theme' ]) ? $sb_instagram_ajax_theme = $_POST[ 'sb_instagram_ajax_theme' ] : $sb_instagram_ajax_theme = '';

            $options[ 'sb_instagram_at' ] = $sb_instagram_at;
            $options[ 'sb_instagram_type' ] = $sb_instagram_type;
            $options[ 'sb_instagram_user_id' ] = $sb_instagram_user_id;
            $options[ 'sb_instagram_hashtag' ] = $sb_instagram_hashtag;
            $options[ 'sb_instagram_preserve_settings' ] = $sb_instagram_preserve_settings;
            $options[ 'sb_instagram_ajax_theme' ] = $sb_instagram_ajax_theme;
        } //End config tab post

        if( isset($_POST[ $sb_instagram_customize_hidden_field ]) && $_POST[ $sb_instagram_customize_hidden_field ] == 'Y' ) {
            $sb_instagram_width = $_POST[ 'sb_instagram_width' ];
            $sb_instagram_width_unit = $_POST[ 'sb_instagram_width_unit' ];
            $sb_instagram_height = $_POST[ 'sb_instagram_height' ];
            $sb_instagram_height_unit = $_POST[ 'sb_instagram_height_unit' ];
            $sb_instagram_num = $_POST[ 'sb_instagram_num' ];
            $sb_instagram_cols = $_POST[ 'sb_instagram_cols' ];
            $sb_instagram_disable_mobile = $_POST[ 'sb_instagram_disable_mobile' ];
            $sb_instagram_image_padding = $_POST[ 'sb_instagram_image_padding' ];
            $sb_instagram_image_padding_unit = $_POST[ 'sb_instagram_image_padding_unit' ];
            $sb_hover_background = $_POST[ 'sb_hover_background' ];
            $sb_hover_text = $_POST[ 'sb_hover_text' ];
            $sb_instagram_sort = $_POST[ 'sb_instagram_sort' ];
            $sb_instagram_disable_lightbox = $_POST[ 'sb_instagram_disable_lightbox' ];
            $sb_instagram_background = $_POST[ 'sb_instagram_background' ];
            isset($_POST[ 'sb_instagram_show_btn' ]) ? $sb_instagram_show_btn = $_POST[ 'sb_instagram_show_btn' ] : $sb_instagram_show_btn = '';
            $sb_instagram_btn_background = $_POST[ 'sb_instagram_btn_background' ];
            $sb_instagram_btn_text_color = $_POST[ 'sb_instagram_btn_text_color' ];
            $sb_instagram_btn_text = $_POST[ 'sb_instagram_btn_text' ];
            $sb_instagram_image_res = $_POST[ 'sb_instagram_image_res' ];
            $sb_instagram_hide_photos = $_POST[ 'sb_instagram_hide_photos' ];
            //Text
            isset($_POST[ 'sb_instagram_show_caption' ]) ? $sb_instagram_show_caption = $_POST[ 'sb_instagram_show_caption' ] : $sb_instagram_show_caption = '';
            $sb_instagram_caption_length = $_POST[ 'sb_instagram_caption_length' ];
            $sb_instagram_caption_color = $_POST[ 'sb_instagram_caption_color' ];
            $sb_instagram_caption_size = $_POST[ 'sb_instagram_caption_size' ];
            //Meta
            isset($_POST[ 'sb_instagram_show_meta' ]) ? $sb_instagram_show_meta = $_POST[ 'sb_instagram_show_meta' ] : $sb_instagram_show_meta = '';
            $sb_instagram_meta_color = $_POST[ 'sb_instagram_meta_color' ];
            $sb_instagram_meta_size = $_POST[ 'sb_instagram_meta_size' ];
            //Header
            isset($_POST[ 'sb_instagram_show_header' ]) ? $sb_instagram_show_header = $_POST[ 'sb_instagram_show_header' ] : $sb_instagram_show_header = '';
            $sb_instagram_header_color = $_POST[ 'sb_instagram_header_color' ];
            //Follow button
            isset($_POST[ 'sb_instagram_show_follow_btn' ]) ? $sb_instagram_show_follow_btn = $_POST[ 'sb_instagram_show_follow_btn' ] : $sb_instagram_show_follow_btn = '';
            $sb_instagram_folow_btn_background = $_POST[ 'sb_instagram_folow_btn_background' ];
            $sb_instagram_follow_btn_text_color = $_POST[ 'sb_instagram_follow_btn_text_color' ];
            $sb_instagram_follow_btn_text = $_POST[ 'sb_instagram_follow_btn_text' ];

            //Misc
            $sb_instagram_custom_css = $_POST[ 'sb_instagram_custom_css' ];
            $sb_instagram_custom_js = $_POST[ 'sb_instagram_custom_js' ];

            $options[ 'sb_instagram_width' ] = $sb_instagram_width;
            $options[ 'sb_instagram_width_unit' ] = $sb_instagram_width_unit;
            $options[ 'sb_instagram_height' ] = $sb_instagram_height;
            $options[ 'sb_instagram_height_unit' ] = $sb_instagram_height_unit;
            $options[ 'sb_instagram_num' ] = $sb_instagram_num;
            $options[ 'sb_instagram_cols' ] = $sb_instagram_cols;
            $options[ 'sb_instagram_disable_mobile' ] = $sb_instagram_disable_mobile;
            $options[ 'sb_instagram_image_padding' ] = $sb_instagram_image_padding;
            $options[ 'sb_instagram_image_padding_unit' ] = $sb_instagram_image_padding_unit;
            $options[ 'sb_hover_background' ] = $sb_hover_background;
            $options[ 'sb_hover_text' ] = $sb_hover_text;
            $options[ 'sb_instagram_sort' ] = $sb_instagram_sort;
            $options[ 'sb_instagram_disable_lightbox' ] = $sb_instagram_disable_lightbox;
            $options[ 'sb_instagram_background' ] = $sb_instagram_background;
            $options[ 'sb_instagram_show_btn' ] = $sb_instagram_show_btn;
            $options[ 'sb_instagram_btn_background' ] = $sb_instagram_btn_background;
            $options[ 'sb_instagram_btn_text_color' ] = $sb_instagram_btn_text_color;
            $options[ 'sb_instagram_btn_text' ] = $sb_instagram_btn_text;
            $options[ 'sb_instagram_image_res' ] = $sb_instagram_image_res;
            $options[ 'sb_instagram_hide_photos' ] = $sb_instagram_hide_photos;
            //Text
            $options[ 'sb_instagram_show_caption' ] = $sb_instagram_show_caption;
            $options[ 'sb_instagram_caption_length' ] = $sb_instagram_caption_length;
            $options[ 'sb_instagram_caption_color' ] = $sb_instagram_caption_color;
            $options[ 'sb_instagram_caption_size' ] = $sb_instagram_caption_size;
            //Meta
            $options[ 'sb_instagram_show_meta' ] = $sb_instagram_show_meta;
            $options[ 'sb_instagram_meta_color' ] = $sb_instagram_meta_color;
            $options[ 'sb_instagram_meta_size' ] = $sb_instagram_meta_size;
            //Header
            $options[ 'sb_instagram_show_header' ] = $sb_instagram_show_header;
            $options[ 'sb_instagram_header_color' ] = $sb_instagram_header_color;
            //Follow button
            $options[ 'sb_instagram_show_follow_btn' ] = $sb_instagram_show_follow_btn;
            $options[ 'sb_instagram_folow_btn_background' ] = $sb_instagram_folow_btn_background;
            $options[ 'sb_instagram_follow_btn_text_color' ] = $sb_instagram_follow_btn_text_color;
            $options[ 'sb_instagram_follow_btn_text' ] = $sb_instagram_follow_btn_text;

            //Misc
            $options[ 'sb_instagram_custom_css' ] = $sb_instagram_custom_css;
            $options[ 'sb_instagram_custom_js' ] = $sb_instagram_custom_js;
            
        } //End customize tab post
        
        //Save the settings to the settings array
        update_option( 'sb_instagram_settings', $options );

    ?>
    <div class="updated"><p><strong><?php _e('Settings saved.', 'custom-facebook-feed' ); ?></strong></p></div>
    <?php } ?>


    <div id="sbi_admin" class="wrap">

        <div id="header">
            <h2><?php _e('Instagram Feed Pro'); ?></h2>
        </div>

        <?php sbi_expiration_notice(); ?>
    
        <form name="form1" method="post" action="">
            <input type="hidden" name="<?php echo $sb_instagram_settings_hidden_field; ?>" value="Y">

            <?php $sbi_active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'configure'; ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=sb-instagram-feed&amp;tab=configure" class="nav-tab <?php echo $sbi_active_tab == 'configure' ? 'nav-tab-active' : ''; ?>"><?php _e('1. Configure'); ?></a>
                <a href="?page=sb-instagram-feed&amp;tab=customize" class="nav-tab <?php echo $sbi_active_tab == 'customize' ? 'nav-tab-active' : ''; ?>"><?php _e('2. Customize'); ?></a>
                <a href="?page=sb-instagram-feed&amp;tab=display" class="nav-tab <?php echo $sbi_active_tab == 'display' ? 'nav-tab-active' : ''; ?>"><?php _e('3. Display Your Feed'); ?></a>
                <a href="?page=sb-instagram-feed&amp;tab=support" class="nav-tab <?php echo $sbi_active_tab == 'support' ? 'nav-tab-active' : ''; ?>"><?php _e('Support'); ?></a>
                <a href="?page=sb-instagram-license" class="nav-tab"><?php _e('License'); ?></a>
            </h2>

            <?php if( $sbi_active_tab == 'configure' ) { //Start Configure tab ?>
            <input type="hidden" name="<?php echo $sb_instagram_configure_hidden_field; ?>" value="Y">

            <table class="form-table">
                <tbody>
                    <h3><?php _e('Configure'); ?></h3>

                    <div id="sbi_config">
                        <a href="https://instagram.com/oauth/authorize/?client_id=97584dabe06548f99b54d318f8db509d&redirect_uri=https://smashballoon.com/instagram-feed/instagram-token-plugin/?return_uri=<?php echo admin_url('admin.php?page=sb-instagram-feed'); ?>&response_type=token" class="sbi_admin_btn"><?php _e('Log in and get my Access Token and User ID'); ?></a>
                    </div>
                    
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Access Token'); ?></label></th>
                        <td>
                            <input name="sb_instagram_at" id="sb_instagram_at" type="text" value="<?php esc_attr_e( $sb_instagram_at ); ?>" size="60" placeholder="Click button above to get your Access Token" />
                            &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("In order to display your photos you need an Access Token from Instagram. To get yours, simply click the button above and log into Instagram. You can also use the button on <a href='https://smashballoon.com/instagram-feed/token/' target='_blank'>this page</a>."); ?></p>
                        </td>
                    </tr>

                    <tr valign="top">
                        <th scope="row"><label><?php _e('Show Photos From:'); ?></label></th>
                        <td>
                            <span>
                                <input type="radio" name="sb_instagram_type" id="sb_instagram_type_user" value="user" <?php if($sb_instagram_type == "user") echo "checked"; ?> />
                                <label class="sbi_radio_label" for="sb_instagram_type_user">User ID:</label>
                                <input name="sb_instagram_user_id" id="sb_instagram_user_id" type="text" value="<?php esc_attr_e( $sb_instagram_user_id ); ?>" size="35" placeholder="Eg: 13460080" />
                                &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?"); ?></a>
                                <p class="sbi_tooltip"><?php _e("This is the <b>numeric ID</b> of the Instagram account you want to display photos from. To get your ID simply click on the button above and log into Instagram.<br /><br />You can also display photos from other peoples Instagram accounts. To find their User ID you can use <a href='http://jelled.com/instagram/lookup-user-id' target='_blank'>this tool</a>."); ?></p><br />
                            </span>
                            
                            <div class="sbi_notice sbi_user_id_error">
                                <?php _e("<p>Please be sure to enter your numeric <b>User ID</b> and not your Username. You can find your User ID by clicking the blue Instagram Login button above, or by entering your username into <a href='http://www.otzberg.net/iguserid/' target='_blank'>this tool</a>.</p>"); ?>
                            </div>
                            
                            <span>
                                <input type="radio" name="sb_instagram_type" id="sb_instagram_type_hashtag" value="hashtag" <?php if($sb_instagram_type == "hashtag") echo "checked"; ?> />
                                <label class="sbi_radio_label" for="sb_instagram_type_hashtag">Hashtag:</label>
                                <input name="sb_instagram_hashtag" id="sb_instagram_hashtag" type="text" value="<?php esc_attr_e( $sb_instagram_hashtag ); ?>" size="35" placeholder="Eg: balloon" />
                                &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?"); ?></a>
                                <p class="sbi_tooltip"><?php _e("Display posts from a specific hashtag instead of from a user"); ?></p>
                            </span>
                            <br />
                            <span class="sbi_note" style="margin: 10px 0 0 0; display: block;"><?php _e('Separate multiple IDs or hashtags using commas'); ?></span>

                        </td>
                    </tr>

                    <!-- <tr valign="top">
                        <th scope="row"><label><?php _e('User ID'); ?></label></th>
                        <td>
                            <input name="sb_instagram_user_id" id="sb_instagram_user_id" type="text" value="<?php esc_attr_e( $sb_instagram_user_id ); ?>" size="23" />
                            &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("This is the ID of the Instagram account you want to display photos from. To get your ID simply click on the button above and log into Instagram.<br /><br />You can also display photos from other peoples Instagram accounts. To find their User ID you can use <a href='http://jelled.com/instagram/lookup-user-id' target='_blank'>this tool</a>."); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><label><?php _e('Show Posts by Hashtag'); ?></label></th>
                        <td>
                            <input name="sb_instagram_hashtag" id="sb_instagram_hashtag" type="text" value="<?php esc_attr_e( $sb_instagram_hashtag ); ?>" size="30" />
                            &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("Display posts from a specific hashtag instead of from a user"); ?></p>
                        </td>
                    </tr> -->

                    <tr>
                        <th class="bump-left"><label for="sb_instagram_preserve_settings" class="bump-left"><?php _e("Preserve settings when plugin is removed"); ?></label></th>
                        <td>
                            <input name="sb_instagram_preserve_settings" type="checkbox" id="sb_instagram_preserve_settings" <?php if($sb_instagram_preserve_settings == true) echo "checked"; ?> />
                            <label for="sb_instagram_preserve_settings"><?php _e('Yes'); ?></label>
                            <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?'); ?></a>
                            <p class="sbi_tooltip"><?php _e('When removing the plugin your settings are automatically erased. Checking this box will prevent any settings from being deleted. This means that you can uninstall and reinstall the plugin without losing your settings.'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th class="bump-left"><label for="sb_instagram_ajax_theme" class="bump-left"><?php _e("Are you using an Ajax powered theme?"); ?></label></th>
                        <td>
                            <input name="sb_instagram_ajax_theme" type="checkbox" id="sb_instagram_ajax_theme" <?php if($sb_instagram_ajax_theme == true) echo "checked"; ?> />
                            <label for="sb_instagram_ajax_theme"><?php _e('Yes'); ?></label>
                            <a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e('What does this mean?'); ?></a>
                            <p class="sbi_tooltip"><?php _e("When navigating your site, if your theme uses Ajax to load content into your pages (meaning your page doesn't refresh) then check this setting. If you're not sure then please check with the theme author."); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <?php submit_button(); ?>
        </form>

        <p><?php _e('Next Step: <a href="?page=sb-instagram-feed&tab=customize">Customize your Feed</a>'); ?></p>
        <p><?php _e('Need help setting up the plugin? Check out our <a href="https://smashballoon.com/instagram-feed/docs/" target="_blank">setup directions</a>'); ?></p>


    <?php } // End Configure tab ?>



    <?php if( $sbi_active_tab == 'customize' ) { //Start Configure tab ?>
    <input type="hidden" name="<?php echo $sb_instagram_customize_hidden_field; ?>" value="Y">

        <h3><?php _e('Customize'); ?></h3>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Width of Feed'); ?></label></th>
                    <td>
                        <input name="sb_instagram_width" type="text" value="<?php esc_attr_e( $sb_instagram_width ); ?>" size="4" />
                        <select name="sb_instagram_width_unit">
                            <option value="px" <?php if($sb_instagram_width_unit == "px") echo 'selected="selected"' ?> ><?php _e('px'); ?></option>
                            <option value="%" <?php if($sb_instagram_width_unit == "%") echo 'selected="selected"' ?> ><?php _e('%'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Height of Feed'); ?></label></th>
                    <td>
                        <input name="sb_instagram_height" type="text" value="<?php esc_attr_e( $sb_instagram_height ); ?>" size="4" />
                        <select name="sb_instagram_height_unit">
                            <option value="px" <?php if($sb_instagram_height_unit == "px") echo 'selected="selected"' ?> ><?php _e('px'); ?></option>
                            <option value="%" <?php if($sb_instagram_height_unit == "%") echo 'selected="selected"' ?> ><?php _e('%'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Background Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_background" type="text" value="<?php esc_attr_e( $sb_instagram_background ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h3><?php _e('Photos'); ?></h3>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Sort Photos By'); ?></label></th>
                    <td>
                        <select name="sb_instagram_sort">
                            <option value="none" <?php if($sb_instagram_sort == "none") echo 'selected="selected"' ?> ><?php _e('Newest to oldest'); ?></option>
                            <!-- <option value="most-recent" <?php if($sb_instagram_sort == "most-recent") echo 'selected="selected"' ?> ><?php _e('Newest to Oldest'); ?></option>
                            <option value="least-recent" <?php if($sb_instagram_sort == "least-recent") echo 'selected="selected"' ?> ><?php _e('Oldest to newest'); ?></option>
                            <option value="most-liked" <?php if($sb_instagram_sort == "most-liked") echo 'selected="selected"' ?> ><?php _e('Most liked first'); ?></option>
                            <option value="least-liked" <?php if($sb_instagram_sort == "least-liked") echo 'selected="selected"' ?> ><?php _e('Least liked first'); ?></option>
                            <option value="most-commented" <?php if($sb_instagram_sort == "most-commented") echo 'selected="selected"' ?> ><?php _e('Most commented first'); ?></option>
                            <option value="least-commented" <?php if($sb_instagram_sort == "least-commented") echo 'selected="selected"' ?> ><?php _e('Least commented first'); ?></option> -->
                            <option value="random" <?php if($sb_instagram_sort == "random") echo 'selected="selected"' ?> ><?php _e('Random'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Number of Photos'); ?></label></th>
                    <td>
                        <input name="sb_instagram_num" type="text" value="<?php esc_attr_e( $sb_instagram_num ); ?>" size="4" />
                        <span class="sbi_note"><?php _e('Number of photos to show initially. Maximum of 33.'); ?></span>
                        &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("Using multiple IDs or hashtags?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("If you're displaying photos from multiple User IDs or hashtags then this is the number of photos which will be displayed from each."); ?></p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Number of Columns'); ?></label></th>
                    <td>

                        <select name="sb_instagram_cols">
                            <option value="1" <?php if($sb_instagram_cols == "1") echo 'selected="selected"' ?> ><?php _e('1'); ?></option>
                            <option value="2" <?php if($sb_instagram_cols == "2") echo 'selected="selected"' ?> ><?php _e('2'); ?></option>
                            <option value="3" <?php if($sb_instagram_cols == "3") echo 'selected="selected"' ?> ><?php _e('3'); ?></option>
                            <option value="4" <?php if($sb_instagram_cols == "4") echo 'selected="selected"' ?> ><?php _e('4'); ?></option>
                            <option value="5" <?php if($sb_instagram_cols == "5") echo 'selected="selected"' ?> ><?php _e('5'); ?></option>
                            <option value="6" <?php if($sb_instagram_cols == "6") echo 'selected="selected"' ?> ><?php _e('6'); ?></option>
                            <option value="7" <?php if($sb_instagram_cols == "7") echo 'selected="selected"' ?> ><?php _e('7'); ?></option>
                            <option value="8" <?php if($sb_instagram_cols == "8") echo 'selected="selected"' ?> ><?php _e('8'); ?></option>
                            <option value="9" <?php if($sb_instagram_cols == "9") echo 'selected="selected"' ?> ><?php _e('9'); ?></option>
                            <option value="10" <?php if($sb_instagram_cols == "10") echo 'selected="selected"' ?> ><?php _e('10'); ?></option>
                        </select>

                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><label><?php _e('Image Resolution'); ?></label></th>
                    <td>

                        <select name="sb_instagram_image_res">
                            <option value="auto" <?php if($sb_instagram_image_res == "auto") echo 'selected="selected"' ?> ><?php _e('Auto-detect (recommended)'); ?></option>
                            <option value="thumb" <?php if($sb_instagram_image_res == "thumb") echo 'selected="selected"' ?> ><?php _e('Thumbnail (150x150)'); ?></option>
                            <option value="medium" <?php if($sb_instagram_image_res == "medium") echo 'selected="selected"' ?> ><?php _e('Medium (306x306)'); ?></option>
                            <option value="full" <?php if($sb_instagram_image_res == "full") echo 'selected="selected"' ?> ><?php _e('Full size (640x640)'); ?></option>
                        </select>

                        &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What does Auto-detect mean?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("Auto-detect means that the plugin automatically sets the image resolution based on the size of your feed."); ?></p>

                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('Padding around Images'); ?></label></th>
                    <td>
                        <input name="sb_instagram_image_padding" type="text" value="<?php esc_attr_e( $sb_instagram_image_padding ); ?>" size="4" />
                        <select name="sb_instagram_image_padding_unit">
                            <option value="px" <?php if($sb_instagram_image_padding_unit == "px") echo 'selected="selected"' ?> ><?php _e('px'); ?></option>
                            <option value="%" <?php if($sb_instagram_image_padding_unit == "%") echo 'selected="selected"' ?> ><?php _e('%'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Hover Background Color'); ?></label></th>
                    <td>
                        <input name="sb_hover_background" type="text" value="<?php esc_attr_e( $sb_hover_background ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Hover Text Color'); ?></label></th>
                    <td>
                        <input name="sb_hover_text" type="text" value="<?php esc_attr_e( $sb_hover_text ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e("Disable Pop-up Lightbox"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_disable_lightbox" id="sb_instagram_disable_lightbox" <?php if($sb_instagram_disable_lightbox == true) echo 'checked="checked"' ?> />
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e("Disable mobile layout"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_disable_mobile" id="sb_instagram_disable_mobile" <?php if($sb_instagram_disable_mobile == true) echo 'checked="checked"' ?> />
                        &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What does this mean?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("By default on mobile devices the layout automatically changes to use fewer columns. Checking this setting disables the mobile layout."); ?></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label><?php _e('Hide Photos'); ?></label></th>
                    <td>
                        <textarea name="sb_instagram_hide_photos" id="sb_instagram_hide_photos" style="width: 70%;" rows="3"><?php esc_attr_e( stripslashes($sb_instagram_hide_photos) ); ?></textarea>
                        <br />
                        <span class="sbi_note" style="margin-left: 0;"><?php _e('Put each ID on a new line'); ?></span>


                        &nbsp;<a class="sbi_tooltip_link" href="JavaScript:void(0);"><?php _e("What is this?"); ?></a>
                            <p class="sbi_tooltip"><?php _e("You can use this setting to hide specific photos in your feed. Just click the 'Hide Photo' link in the photo pop-up in your feed to get the ID of the photo, then copy and paste it into this text box."); ?></p>

                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>

        <hr />
        <h3><?php _e("Caption"); ?></h3>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Show Caption"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_show_caption" id="sb_instagram_show_caption" <?php if($sb_instagram_show_caption == true) echo 'checked="checked"' ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Maximum Text Length"); ?></label></th>
                    <td>
                        <input name="sb_instagram_caption_length" id="sb_instagram_caption_length" type="text" value="<?php esc_attr_e( $sb_instagram_caption_length ); ?>" size="4" />
                        <span class="sbi_note"><?php _e("Number of characters of text to display. An elipsis link will be added to allow you to reveal more text."); ?></span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Text Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_caption_color" type="text" value="<?php esc_attr_e( $sb_instagram_caption_color ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Text Size'); ?></label></th>
                    <td>
                        <select name="sb_instagram_caption_size">
                            <option value="inherit" <?php if($sb_instagram_caption_size == "inherit") echo 'selected="selected"' ?> ><?php _e('Inherit'); ?></option>
                            <option value="10" <?php if($sb_instagram_caption_size == "10") echo 'selected="selected"' ?> ><?php _e('10px'); ?></option>
                            <option value="11" <?php if($sb_instagram_caption_size == "11") echo 'selected="selected"' ?> ><?php _e('11px'); ?></option>
                            <option value="12" <?php if($sb_instagram_caption_size == "12") echo 'selected="selected"' ?> ><?php _e('12px'); ?></option>
                            <option value="13" <?php if($sb_instagram_caption_size == "13") echo 'selected="selected"' ?> ><?php _e('13px'); ?></option>
                            <option value="14" <?php if($sb_instagram_caption_size == "14") echo 'selected="selected"' ?> ><?php _e('14px'); ?></option>
                            <option value="16" <?php if($sb_instagram_caption_size == "16") echo 'selected="selected"' ?> ><?php _e('16px'); ?></option>
                            <option value="18" <?php if($sb_instagram_caption_size == "18") echo 'selected="selected"' ?> ><?php _e('18px'); ?></option>
                            <option value="20" <?php if($sb_instagram_caption_size == "20") echo 'selected="selected"' ?> ><?php _e('20px'); ?></option>
                            <option value="24" <?php if($sb_instagram_caption_size == "24") echo 'selected="selected"' ?> ><?php _e('24px'); ?></option>
                            <option value="28" <?php if($sb_instagram_caption_size == "28") echo 'selected="selected"' ?> ><?php _e('28px'); ?></option>
                            <option value="32" <?php if($sb_instagram_caption_size == "32") echo 'selected="selected"' ?> ><?php _e('32px'); ?></option>
                            <option value="36" <?php if($sb_instagram_caption_size == "36") echo 'selected="selected"' ?> ><?php _e('36px'); ?></option>
                            <option value="40" <?php if($sb_instagram_caption_size == "40") echo 'selected="selected"' ?> ><?php _e('40px'); ?></option>
                        </select>
                        <span class="sbi_note"><?php _e("'Inherit' means it will inherit the text size from your website"); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h3><?php _e("Likes &amp; Comments Icons"); ?></h3>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Show Icons"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_show_meta" id="sb_instagram_show_meta" <?php if($sb_instagram_show_meta == true) echo 'checked="checked"' ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Icon Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_meta_color" type="text" value="<?php esc_attr_e( $sb_instagram_meta_color ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Icon Size'); ?></label></th>
                    <td>
                        <select name="sb_instagram_meta_size">
                            <option value="inherit" <?php if($sb_instagram_meta_size == "inherit") echo 'selected="selected"' ?> ><?php _e('Inherit'); ?></option>
                            <option value="10" <?php if($sb_instagram_meta_size == "10") echo 'selected="selected"' ?> ><?php _e('10px'); ?></option>
                            <option value="11" <?php if($sb_instagram_meta_size == "11") echo 'selected="selected"' ?> ><?php _e('11px'); ?></option>
                            <option value="12" <?php if($sb_instagram_meta_size == "12") echo 'selected="selected"' ?> ><?php _e('12px'); ?></option>
                            <option value="13" <?php if($sb_instagram_meta_size == "13") echo 'selected="selected"' ?> ><?php _e('13px'); ?></option>
                            <option value="14" <?php if($sb_instagram_meta_size == "14") echo 'selected="selected"' ?> ><?php _e('14px'); ?></option>
                            <option value="16" <?php if($sb_instagram_meta_size == "16") echo 'selected="selected"' ?> ><?php _e('16px'); ?></option>
                            <option value="18" <?php if($sb_instagram_meta_size == "18") echo 'selected="selected"' ?> ><?php _e('18px'); ?></option>
                            <option value="20" <?php if($sb_instagram_meta_size == "20") echo 'selected="selected"' ?> ><?php _e('20px'); ?></option>
                            <option value="24" <?php if($sb_instagram_meta_size == "24") echo 'selected="selected"' ?> ><?php _e('24px'); ?></option>
                            <option value="28" <?php if($sb_instagram_meta_size == "28") echo 'selected="selected"' ?> ><?php _e('28px'); ?></option>
                            <option value="32" <?php if($sb_instagram_meta_size == "32") echo 'selected="selected"' ?> ><?php _e('32px'); ?></option>
                            <option value="36" <?php if($sb_instagram_meta_size == "36") echo 'selected="selected"' ?> ><?php _e('36px'); ?></option>
                            <option value="40" <?php if($sb_instagram_meta_size == "40") echo 'selected="selected"' ?> ><?php _e('40px'); ?></option>
                        </select>
                        <span class="sbi_note"><?php _e("'Inherit' means it will inherit the text size from your website"); ?></span>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>

        <hr />
        <h3><?php _e("Header"); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Show the Header"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_show_header" id="sb_instagram_show_header" <?php if($sb_instagram_show_header == true) echo 'checked="checked"' ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Header Text Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_header_color" type="text" value="<?php esc_attr_e( $sb_instagram_header_color ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h3><?php _e("'Load More' Button"); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Show the 'Load More' button"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_show_btn" id="sb_instagram_show_btn" <?php if($sb_instagram_show_btn == true) echo 'checked="checked"' ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Button Background Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_btn_background" type="text" value="<?php esc_attr_e( $sb_instagram_btn_background ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Button Text Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_btn_text_color" type="text" value="<?php esc_attr_e( $sb_instagram_btn_text_color ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Button Text'); ?></label></th>
                    <td>
                        <input name="sb_instagram_btn_text" type="text" value="<?php esc_attr_e( $sb_instagram_btn_text ); ?>" size="20" />
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h3><?php _e("'Follow on Instagram' Button"); ?></h3>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><label><?php _e("Show the Follow button"); ?></label></th>
                    <td>
                        <input type="checkbox" name="sb_instagram_show_follow_btn" id="sb_instagram_show_follow_btn" <?php if($sb_instagram_show_follow_btn == true) echo 'checked="checked"' ?> />
                    </td>
                </tr>

                <!-- <tr valign="top">
                    <th scope="row"><label><?php _e("Button Position"); ?></label></th>
                    <td>
                        <select name="sb_instagram_follow_btn_position">
                            <option value="top" <?php if($sb_instagram_follow_btn_position == "top") echo 'selected="selected"' ?> ><?php _e('Top'); ?></option>
                            <option value="bottom" <?php if($sb_instagram_follow_btn_position == "bottom") echo 'selected="selected"' ?> ><?php _e('Bottom'); ?></option>
                        </select>
                    </td>
                </tr> -->

                <tr valign="top">
                    <th scope="row"><label><?php _e('Button Background Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_folow_btn_background" type="text" value="<?php esc_attr_e( $sb_instagram_folow_btn_background ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Button Text Color'); ?></label></th>
                    <td>
                        <input name="sb_instagram_follow_btn_text_color" type="text" value="<?php esc_attr_e( $sb_instagram_follow_btn_text_color ); ?>" class="sbi_colorpick" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label><?php _e('Button Text'); ?></label></th>
                    <td>
                        <input name="sb_instagram_follow_btn_text" type="text" value="<?php esc_attr_e( $sb_instagram_follow_btn_text ); ?>" size="30" />
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h3><?php _e('Misc'); ?></h3>

        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <td style="padding-bottom: 0;">
                    <?php _e('<strong style="font-size: 15px;">Custom CSS</strong><br />Enter your own custom CSS in the box below'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td>
                        <textarea name="sb_instagram_custom_css" id="sb_instagram_custom_css" style="width: 70%;" rows="7"><?php esc_attr_e( stripslashes($sb_instagram_custom_css) ); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <td style="padding-bottom: 0;">
                    <?php _e('<strong style="font-size: 15px;">Custom JavaScript</strong><br />Enter your own custom JavaScript/jQuery in the box below'); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <td>
                        <textarea name="sb_instagram_custom_js" id="sb_instagram_custom_js" style="width: 70%;" rows="7"><?php esc_attr_e( stripslashes($sb_instagram_custom_js) ); ?></textarea>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>

    </form>

    <p><?php _e('Next Step: <a href="?page=sb-instagram-feed&tab=display">Display your Feed</a>'); ?></p>

    <p><?php _e('Need help setting up the plugin? Check out our <a href="https://smashballoon.com/instagram-feed/docs/" target="_blank">setup directions</a>'); ?></p>


    <?php } //End Customize tab ?>



    <?php if( $sbi_active_tab == 'display' ) { //Start Configure tab ?>

        <h3><?php _e('Display your Feed'); ?></h3>
        <p><?php _e("Copy and paste the following shortcode directly into the page, post or widget where you'd like the feed to show up:"); ?></p>
        <input type="text" value="[instagram-feed]" size="16" readonly="readonly" style="text-align: center;" onclick="this.focus();this.select()" title="<?php _e('To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac).'); ?>" />

        <p><?php _e("If you'd like to display multiple feeds then you can set different settings directly in the shortcode like so:"); ?>
        <code>[instagram-feed num=9 cols=3]</code></p>
        <p>You can display as many different feeds as you like, on either the same page or on different pages, by just using the shortcode options below. For example:<br />
        <code>[instagram-feed]</code><br />
        <code>[instagram-feed id="12986477"]</code><br />
        <code>[instagram-feed type=hashtag hashtag="#sun,#beach" num=4 cols=4 showcaption=false]</code>
        </p>
        <p><?php _e("See the table below for a full list of available shortcode options:"); ?></p>

        <table class="sbi_shortcode_table">
            <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e('Shortcode option'); ?></th>
                    <th scope="row"><?php _e('Description'); ?></th>
                    <th scope="row"><?php _e('Example'); ?></th>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("Configure Options"); ?></td></tr>
                <tr>
                    <td>type</td>
                    <td><?php _e("Display photos from a User ID (user) or Hashtag (hashtag)"); ?></td>
                    <td><code>[instagram-feed type=hashtag]</code></td>
                </tr>
                <tr>
                    <td>id</td>
                    <td><?php _e('An Instagram User ID. Separate multiple IDs by commas.'); ?></td>
                    <td><code>[instagram-feed id="ANY_USER_ID"]</code></td>
                </tr>
                <tr>
                    <td>hashtag</td>
                    <td><?php _e('Any hashtag. Separate multiple IDs by commas.'); ?></td>
                    <td><code>[instagram-feed hashtag="#awesome"]</code></td>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("Customize Options"); ?></td></tr>
                <tr>
                    <td>width</td>
                    <td><?php _e("The width of your feed. Any number."); ?></td>
                    <td><code>[instagram-feed width=50]</code></td>
                </tr>
                <tr>
                    <td>widthunit</td>
                    <td><?php _e("The unit of the width. 'px' or '%'"); ?></td>
                    <td><code>[instagram-feed widthunit=%]</code></td>
                </tr>
                <tr>
                    <td>height</td>
                    <td><?php _e("The height of your feed. Any number."); ?></td>
                    <td><code>[instagram-feed height=250]</code></td>
                </tr>
                <tr>
                    <td>heightunit</td>
                    <td><?php _e("The unit of the height. 'px' or '%'"); ?></td>
                    <td><code>[instagram-feed heightunit=px]</code></td>
                </tr>
                <tr>
                    <td>background</td>
                    <td><?php _e("The background color of the feed. Any hex color code."); ?></td>
                    <td><code>[instagram-feed background=#ffff00]</code></td>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("Photos Options"); ?></td></tr>
                <tr>
                    <td>sortby</td>
                    <td><?php _e("Sort the posts by Newest to Oldest (none) or Random (random)"); ?></td>
                    <td><code>[instagram-feed sortby=random]</code></td>
                </tr>
                <tr>
                    <td>disablelightbox</td>
                    <td><?php _e("Whether to disable the photo Lightbox. It is enabled by default."); ?></td>
                    <td><code>[instagram-feed disablelightbox=true]</code></td>
                </tr>
                <tr>
                    <td>num</td>
                    <td><?php _e("The number of photos to display initially. Maximum is 33."); ?></td>
                    <td><code>[instagram-feed num=10]</code></td>
                </tr>
                <tr>
                    <td>cols</td>
                    <td><?php _e("The number of columns in your feed. 1 - 10."); ?></td>
                    <td><code>[instagram-feed cols=5]</code></td>
                </tr>
                <tr>
                    <td>imageres</td>
                    <td><?php _e("The resolution/size of the photos. 'auto', full', 'medium' or 'thumb'."); ?></td>
                    <td><code>[instagram-feed imageres=full]</code></td>
                </tr>
                <tr>
                    <td>imagepadding</td>
                    <td><?php _e("The spacing around your photos"); ?></td>
                    <td><code>[instagram-feed imagepadding=10]</code></td>
                </tr>
                <tr>
                    <td>imagepaddingunit</td>
                    <td><?php _e("The unit of the padding. 'px' or '%'"); ?></td>
                    <td><code>[instagram-feed imagepaddingunit=px]</code></td>
                </tr>
                <tr>
                    <td>hovercolor</td>
                    <td><?php _e("The background color when hovering over a photo. Any hex color code."); ?></td>
                    <td><code>[instagram-feed hovercolor=#ff0000]</code></td>
                </tr>
                <tr>
                    <td>hovertextcolor</td>
                    <td><?php _e("The text/icon color when hovering over a photo. Any hex color code."); ?></td>
                    <td><code>[instagram-feed hovertextcolor=#fff]</code></td>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("Caption Options"); ?></td></tr>
                <tr>
                    <td>showcaption</td>
                    <td><?php _e("Whether to show the photo caption. 'true' or 'false'."); ?></td>
                    <td><code>[instagram-feed showcaption=false]</code></td>
                </tr>
                <tr>
                    <td>captionlength</td>
                    <td><?php _e("The number of characters of the caption to display"); ?></td>
                    <td><code>[instagram-feed captionlength=50]</code></td>
                </tr>
                <tr>
                    <td>captioncolor</td>
                    <td><?php _e("The text color of the caption. Any hex color code."); ?></td>
                    <td><code>[instagram-feed captioncolor=#000]</code></td>
                </tr>
                <tr>
                    <td>captionsize</td>
                    <td><?php _e("The size of the caption text. Any number."); ?></td>
                    <td><code>[instagram-feed captionsize=24]</code></td>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("Likes &amp; Comments Options"); ?></td></tr>
                <tr>
                    <td>showlikes</td>
                    <td><?php _e("Whether to show the Likes &amp; Comments. 'true' or 'false'."); ?></td>
                    <td><code>[instagram-feed showlikes=false]</code></td>
                </tr>
                <tr>
                    <td>likescolor</td>
                    <td><?php _e("The color of the Likes &amp; Comments. Any hex color code."); ?></td>
                    <td><code>[instagram-feed likescolor=#FF0000]</code></td>
                </tr>
                <tr>
                    <td>likessize</td>
                    <td><?php _e("The size of the Likes &amp; Comments. Any number."); ?></td>
                    <td><code>[instagram-feed likessize=14]</code></td>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("Header Options"); ?></td></tr>
                <tr>
                    <td>showheader</td>
                    <td><?php _e("Whether to show the feed Header. 'true' or 'false'."); ?></td>
                    <td><code>[instagram-feed showheader=false]</code></td>
                </tr>
                <tr>
                    <td>headercolor</td>
                    <td><?php _e("The color of the Header text. Any hex color code."); ?></td>
                    <td><code>[instagram-feed headercolor=#333]</code></td>
                </tr>
                
                <tr class="sbi_table_header"><td colspan=3><?php _e("'Load More' Button Options"); ?></td></tr>
                <tr>
                    <td>showbutton</td>
                    <td><?php _e("Whether to show the 'Load More' button. 'true' or 'false'."); ?></td>
                    <td><code>[instagram-feed showbutton=false]</code></td>
                </tr>
                <tr>
                    <td>buttoncolor</td>
                    <td><?php _e("The background color of the button. Any hex color code."); ?></td>
                    <td><code>[instagram-feed buttoncolor=#000]</code></td>
                </tr>
                <tr>
                    <td>buttontextcolor</td>
                    <td><?php _e("The text color of the button. Any hex color code."); ?></td>
                    <td><code>[instagram-feed buttontextcolor=#fff]</code></td>
                </tr>
                <tr>
                    <td>buttontext</td>
                    <td><?php _e("The text used for the button."); ?></td>
                    <td><code>[instagram-feed buttontext="Load More Photos"]</code></td>
                </tr>

                <tr class="sbi_table_header"><td colspan=3><?php _e("'Follow on Instagram' Button Options"); ?></td></tr>
                <tr>
                    <td>showfollow</td>
                    <td><?php _e("Whether to show the 'Follow on Instagram' button. 'true' or 'false'."); ?></td>
                    <td><code>[instagram-feed showfollow=true]</code></td>
                </tr>
                <tr>
                    <td>followcolor</td>
                    <td><?php _e("The background color of the button. Any hex color code."); ?></td>
                    <td><code>[instagram-feed followcolor=#ff0000]</code></td>
                </tr>
                <tr>
                    <td>followtextcolor</td>
                    <td><?php _e("The text color of the button. Any hex color code."); ?></td>
                    <td><code>[instagram-feed followtextcolor=#fff]</code></td>
                </tr>
                <tr>
                    <td>followtext</td>
                    <td><?php _e("The text used for the button."); ?></td>
                    <td><code>[instagram-feed followtext="Follow me"]</code></td>
                </tr>
                

            </tbody>
        </table>

        <p><?php _e('Need help setting up the plugin? Check out our <a href="https://smashballoon.com/instagram-feed/docs/" target="_blank">setup directions</a>'); ?></p>

    <?php } //End Display tab ?>


    <?php if( $sbi_active_tab == 'support' ) { //Start Support tab ?>

        <h3><?php _e('Setting up and Customizing the plugin'); ?></h3>
        <p><?php _e('<a href="https://smashballoon.com/instagram-feed/docs/" target="_blank">Click here for step-by-step setup directions</a>'); ?></p>
        <p>See below for a short video demonstrating how to set up, customize and use the plugin.</p>
        <iframe class="youtube-video" src="//www.youtube.com/embed/3tc-UvcTcgk?theme=light&amp;showinfo=0&amp;controls=2" width="960" height="540" frameborder="0" allowfullscreen="allowfullscreen" style="border: 1px solid #ddd;"></iframe>

        <br />
        <br />
        <p><?php _e('Still need help? <a href="http://smashballoon.com/instagram-feed/support/" target="_blank">Request support</a>. Please include your <b>System Info</b> below with all support requests.'); ?></p>

        <h3><?php _e('System Info &nbsp; <i style="color: #666; font-size: 11px; font-weight: normal;">Click the text below to select all</i>'); ?></h3>


        <?php $sbi_options = get_option('sb_instagram_settings'); ?>
        <textarea readonly="readonly" onclick="this.focus();this.select()" title="To copy, click the field then press Ctrl + C (PC) or Cmd + C (Mac)." style="width: 100%; max-width: 960px; height: 500px; white-space: pre; font-family: Menlo,Monaco,monospace;">
## SITE/SERVER INFO: ##
Site URL:                 <?php echo site_url() . "\n"; ?>
Home URL:                 <?php echo home_url() . "\n"; ?>
WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
PHP Version:              <?php echo PHP_VERSION . "\n"; ?>
Web Server Info:          <?php echo $_SERVER['SERVER_SOFTWARE'] . "\n"; ?>

## ACTIVE PLUGINS: ##
<?php
$plugins = get_plugins();
$active_plugins = get_option( 'active_plugins', array() );

foreach ( $plugins as $plugin_path => $plugin ) {
    // If the plugin isn't active, don't show it.
    if ( ! in_array( $plugin_path, $active_plugins ) )
        continue;

    echo $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
}
?>

## PLUGIN SETTINGS: ##
sb_instagram_license => <?php echo get_option( 'sbi_license_key' ) . "\n"; ?>
sb_instagram_license_type => <?php echo SBI_PLUGIN_NAME . "\n"; ?>
<?php 
while (list($key, $val) = each($sbi_options)) {
    echo "$key => $val\n";
}
?>
        </textarea>

        
<?php 
} //End Support tab 
?>

    <hr />

    <p>Want to display your Facebook posts? Check out our <a href="https://wordpress.org/plugins/custom-facebook-feed/" target="_blank">Custom Facebook Feed plugin</a></p>

</div> <!-- end #sbi_admin -->

<?php } //End Settings page

function sb_instagram_admin_style() {
        wp_register_style( 'sb_instagram_admin_css', plugin_dir_url( __FILE__ ) . 'css/sb-instagram-admin.css?1', false, '1.0.0' );
        wp_enqueue_style( 'sb_instagram_admin_css' );
        wp_enqueue_style( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'sb_instagram_admin_style' );

function sb_instagram_admin_scripts() {
    wp_enqueue_script( 'sb_instagram_admin_js', plugin_dir_url( __FILE__ ) . 'js/sb-instagram-admin.js?1' );
    if( !wp_script_is('jquery-ui-draggable') ) { 
        wp_enqueue_script(
            array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-draggable'
            )
        );
    }
    wp_enqueue_script(
        array(
        'hoverIntent',
        'wp-color-picker'
        )
    );
}
add_action( 'admin_enqueue_scripts', 'sb_instagram_admin_scripts' );

// Add a Settings link to the plugin on the Plugins page
$sbi_plugin_file = 'instagram-feed-pro/instagram-feed.php';
add_filter( "plugin_action_links_{$sbi_plugin_file}", 'sbi_add_settings_link', 10, 2 );
 
//modify the link by unshifting the array
function sbi_add_settings_link( $links, $file ) {
    $sbi_settings_link = '<a href="' . admin_url( 'admin.php?page=sb-instagram-feed' ) . '">' . __( 'Settings', 'sb-instagram-feed' ) . '</a>';
    array_unshift( $links, $sbi_settings_link );
 
    return $links;
}



function sbi_expiration_notice(){

// delete_option( 'sbi_license_data' );
    $sbi_license = trim( get_option( 'sbi_license_key' ) );

    // delete_option( 'sbi_license_key' );
    // delete_option( 'sbi_license_status' );

    //If there's no license key then don't do anything
    if( empty($sbi_license) || !isset($sbi_license) ) return;

    //Is there already license data in the db?
    if( get_option( 'sbi_license_data' ) ){
        //Yes
        //Get license data from the db and convert the object to an array
        $sbi_license_data = (array) get_option( 'sbi_license_data' );
    } else {
        //No
        // data to send in our API request
        $sbi_api_params = array( 
            'edd_action'=> 'check_license', 
            'license'   => $sbi_license, 
            'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
        );
        
        // Call the custom API.
        $sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

        // decode the license data
        $sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

        //Store license data in db
        update_option( 'sbi_license_data', $sbi_license_data );
    }

    //Number of days until license expires
    $sbi_date1 = $sbi_license_data['expires'];
    $sbi_date2 = date('Y-m-d');
    // $sbi_date2 = '2015-01-20';
    $sbi_interval = round(abs(strtotime($sbi_date2)-strtotime($sbi_date1))/86400);

    //Is license expired?
    ( $sbi_interval == 0 || strtotime($sbi_date1) < strtotime($sbi_date2) ) ? $sbi_license_expired = true : $sbi_license_expired = false;

    //If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
    if( $sbi_date1[0] == '1' ) $sbi_license_expired = false;

    //If there's no expired date then don't show the expired notification
    if( empty($sbi_date1) || !isset($sbi_date1) ) $sbi_license_expired = false;

    //Is license missing - ie. on very first check
    if( isset($sbi_license_data['error']) ){
        if( $sbi_license_data['error'] == 'missing' ) $sbi_license_expired = false;
    }

    //If license expires in less than 30 days and it isn't currently expired then show the expire countdown instead of the expiration notice
    if($sbi_interval < 30 && !$sbi_license_expired){
        $sbi_expire_countdown = true;
    } else {
        $sbi_expire_countdown = false;
    }

    global $sbi_download_id;

    //Is the license expired?
    if($sbi_license_expired || $sbi_expire_countdown) {
        
        //If expire countdown then add the countdown class to the notice box
        if($sbi_expire_countdown){
            $sbi_expired_box_classes = "sbi-license-expired sbi-license-countdown";
            $sbi_expired_box_msg = "expires in " . $sbi_interval . " days";
        } else {
            $sbi_expired_box_classes = "sbi-license-expired";
            $sbi_expired_box_msg = "has expired";
        }

        _e("
        <div class='".$sbi_expired_box_classes."'>
            <p>Hey ".$sbi_license_data["customer_name"].", your Instagram Feed Pro license key ".$sbi_expired_box_msg.". Click <a href='https://smashballoon.com/checkout/?edd_license_key=".$sbi_license."&download_id=".$sbi_download_id."' target='_blank'>here</a> to renew your license. <a href='javascript:void(0);' id='sbi-why-renew-show' onclick='sbiShowReasons()'>Why renew?</a><a href='javascript:void(0);' id='sbi-why-renew-hide' onclick='sbiHideReasons()' style='display: none;'>Hide text</a></p>
            <div id='sbi-why-renew' style='display: none;'>
                <h4>Customer Support</h4>
                <p>Without a valid license key you will no longer be able to receive updates or support for the Instagram Feed plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>

                <h4>Maintenance Upates</h4>
                <p>With both WordPress and the Instagram API being updated on a regular basis we stay on top of the latest changes and provide frequent updates to keep pace.</p>

                <h4>New Feature Updates</h4>
                <p>We're continually adding new features to the plugin, based on both customer suggestions and our own ideas for ways to make it better, more useful, more customizable, more robust and just more awesome! Renew your license to prevent from missing out on any of the new features added in the future.</p>
            </div>
        </div>
        <script type='text/javascript'>
        function sbiShowReasons() {
            document.getElementById('sbi-why-renew').style.display = 'block';
            document.getElementById('sbi-why-renew-show').style.display = 'none';
            document.getElementById('sbi-why-renew-hide').style.display = 'inline';
        }
        function sbiHideReasons() {
            document.getElementById('sbi-why-renew').style.display = 'none';
            document.getElementById('sbi-why-renew-show').style.display = 'inline';
            document.getElementById('sbi-why-renew-hide').style.display = 'none';
        }
        </script>
        ");
    }

}


/* Display a license expired notice that can be dismissed */
add_action('admin_notices', 'sbi_renew_license_notice');
function sbi_renew_license_notice() {

    $sbi_license = trim( get_option( 'sbi_license_key' ) );

    if( empty($sbi_license) || !isset($sbi_license) ) return;

    //Is there already license data in the db?
    if( get_option( 'sbi_license_data' ) ){
        //Yes
        //Get license data from the db and convert the object to an array
        $sbi_license_data = (array) get_option( 'sbi_license_data' );
    } else {
        //No
        // data to send in our API request
        $sbi_api_params = array( 
            'edd_action'=> 'check_license', 
            'license'   => $sbi_license, 
            'item_name' => urlencode( SBI_PLUGIN_NAME ) // the name of our product in EDD
        );
        
        // Call the custom API.
        $sbi_response = wp_remote_get( add_query_arg( $sbi_api_params, SBI_STORE_URL ), array( 'timeout' => 60, 'sslverify' => false ) );

        // decode the license data
        $sbi_license_data = (array) json_decode( wp_remote_retrieve_body( $sbi_response ) );

        //Store license data in db
        update_option( 'sbi_license_data', $sbi_license_data );
    }

    //Number of days until license expires
    $sbi_date1 = $sbi_license_data['expires'];
    $sbi_date2 = date('Y-m-d');
    $sbi_interval = round(abs(strtotime($sbi_date2)-strtotime($sbi_date1))/86400);

    //Is license expired?
    ( $sbi_interval == 0 || strtotime($sbi_date1) < strtotime($sbi_date2) ) ? $sbi_license_expired = true : $sbi_license_expired = false;

    //If expired date is returned as 1970 (or any other 20th century year) then it means that the correct expired date was not returned and so don't show the renewal notice
    if( $sbi_date1[0] == '1' ) $sbi_license_expired = false;

    //If there's no expired date then don't show the expired notification
    if( empty($sbi_date1) || !isset($sbi_date1) ) $sbi_license_expired = false;

    //Is license missing - ie. on very first check
    if( isset($sbi_license_data['error']) ){
        if( $sbi_license_data['error'] == 'missing' ) $sbi_license_expired = false;
    }

    //If license expires in less than 30 days and it isn't currently expired then show the expire countdown instead of the expiration notice
    if($sbi_interval < 30 && !$sbi_license_expired){
        $sbi_expire_countdown = true;
    } else {
        $sbi_expire_countdown = false;
    }


    //Is the license expired?
    if($sbi_license_expired || $sbi_expire_countdown) {

        //Show this notice on every page apart from the Custom Facebook Feed settings pages
        isset($_GET['page'])? $sbi_check_page = $_GET['page'] : $sbi_check_page = '';
        if ( $sbi_check_page !== 'sb-instagram-feed' && $sbi_check_page !== 'sb-instagram-license' ) {

            global $current_user;
                $user_id = $current_user->ID;

            global $sbi_download_id;

            // Use this to show notice again
            // delete_user_meta($user_id, 'sbi_ignore_notice');

            /* Check that the user hasn't already clicked to ignore the message */
            if ( ! get_user_meta($user_id, 'sbi_ignore_notice') ) {

            //If expire countdown then add the countdown class to the notice box
            if($sbi_expire_countdown){
                $sbi_expired_box_classes = "sbi-license-expired sbi-license-countdown";
                $sbi_expired_box_msg = "expires in " . $sbi_interval . " days";
            } else {
                $sbi_expired_box_classes = "sbi-license-expired";
                $sbi_expired_box_msg = "has expired";
            }

                _e("
                <div class='".$sbi_expired_box_classes."'>
                    <a style='float:right; color: #dd3d36; text-decoration: none;' href='?sbi_nag_ignore=0'>Dismiss</a>
                    <p>Hey ".$sbi_license_data["customer_name"].", your Instagram Feed Pro license key ".$sbi_expired_box_msg.". Click <a href='https://smashballoon.com/checkout/?edd_license_key=".$sbi_license."&download_id=".$sbi_download_id."' target='_blank'>here</a> to renew your license. <a href='javascript:void(0);' id='sbi-why-renew-show' onclick='sbiShowReasons()'>Why renew?</a><a href='javascript:void(0);' id='sbi-why-renew-hide' onclick='sbiHideReasons()' style='display: none;'>Hide text</a></p>
                    <div id='sbi-why-renew' style='display: none;'>
                        <h4>Customer Support</h4>
                        <p>Without a valid license key you will no longer be able to receive updates or support for the Instagram Feed plugin. A renewed license key grants you access to our top-notch, quick and effective support for another full year.</p>

                        <h4>Maintenance Upates</h4>
                        <p>With both WordPress and the Instagram API being updated on a regular basis we stay on top of the latest changes and provide frequent updates to keep pace.</p>

                        <h4>New Feature Updates</h4>
                        <p>We're continually adding new features to the plugin, based on both customer suggestions and our own ideas for ways to make it better, more useful, more customizable, more robust and just more awesome! Renew your license to prevent from missing out on any of the new features added in the future.</p>
                    </div>
                </div>
                <script type='text/javascript'>
                function sbiShowReasons() {
                    document.getElementById('sbi-why-renew').style.display = 'block';
                    document.getElementById('sbi-why-renew-show').style.display = 'none';
                    document.getElementById('sbi-why-renew-hide').style.display = 'inline';
                }
                function sbiHideReasons() {
                    document.getElementById('sbi-why-renew').style.display = 'none';
                    document.getElementById('sbi-why-renew-show').style.display = 'inline';
                    document.getElementById('sbi-why-renew-hide').style.display = 'none';
                }
                </script>
                ");

            }

        }

    }
}
add_action('admin_init', 'sbi_nag_ignore');
function sbi_nag_ignore() {
    global $current_user;
        $user_id = $current_user->ID;
        if ( isset($_GET['sbi_nag_ignore']) && '0' == $_GET['sbi_nag_ignore'] ) {
             add_user_meta($user_id, 'sbi_ignore_notice', 'true', true);
    }
}

?>