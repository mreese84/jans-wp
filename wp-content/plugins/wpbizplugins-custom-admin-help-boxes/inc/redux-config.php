<?php

/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://docs.reduxframework.com
 * */

if (!class_exists('WPBizPlugins_CustomAdminHelpBoxes_Config')) {

    class WPBizPlugins_CustomAdminHelpBoxes_Config {

        public $args        = array();
        public $sections    = array();
        public $theme;
        public $ReduxFramework;

        public function __construct() {

            if (!class_exists('ReduxFramework')) {
                return;
            }

            // This is needed. Bah WordPress bugs. ;)
            if ( true == Redux_Helpers::isTheme(__FILE__) ) {
                $this->initSettings();
            } else {
                add_action('plugins_loaded', array($this, 'initSettings'), 10);
            }
        }

        public function initSettings() {

            // Just for demo purposes. Not needed per say.
            $this->theme = wp_get_theme();

            // Set the default arguments
            $this->setArguments();

            // Set a few help tabs so you can see how it's done
            $this->setHelpTabs();

            // Create the sections and fields
            $this->setSections();

            if (!isset($this->args['opt_name'])) { // No errors please
                return;
            }

            // If Redux is running as a plugin, this will remove the demo notice and links
            add_action( 'redux/loaded', array( $this, 'remove_demo' ) );
            
            // Function to test the compiler hook and demo CSS output.
            // Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
            add_filter('redux/options/'.$this->args['opt_name'].'/compiler', array( $this, 'compiler_action' ), 10, 2);
            
            // Change the arguments after they've been declared, but before the panel is created
            //add_filter('redux/options/'.$this->args['opt_name'].'/args', array( $this, 'change_arguments' ) );
            
            // Change the default value of a field after it's been set, but before it's been useds
            //add_filter('redux/options/'.$this->args['opt_name'].'/defaults', array( $this,'change_defaults' ) );
            
            // Dynamically add a section. Can be also used to modify sections/fields
            // add_filter('redux/options/' . $this->args['opt_name'] . '/sections', array($this, 'dynamic_section'));

            $this->ReduxFramework = new ReduxFramework($this->sections, $this->args);
        }

        /**

          This is a test function that will let you see when the compiler hook occurs.
          It only runs if a field	set with compiler=>true is changed.

         * */
        function compiler_action($options, $css) {

            //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )

            /*
              // Demo of how to use the dynamic CSS and write your own static CSS file
              $filename = dirname(__FILE__) . '/style' . '.css';
              global $wp_filesystem;
              if( empty( $wp_filesystem ) ) {
                require_once( ABSPATH .'/wp-admin/includes/file.php' );
              WP_Filesystem();
              }

              if( $wp_filesystem ) {
                $wp_filesystem->put_contents(
                    $filename,
                    $css,
                    FS_CHMOD_FILE // predefined mode settings for WP files
                );
              }
             */
        }

        /**

          Custom function for filtering the sections array. Good for child themes to override or add to the sections.
          Simply include this function in the child themes functions.php file.

          NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
          so you must use get_template_directory_uri() if you want to use any of the built in icons

         * */
        function dynamic_section($sections) {
            
            //$sections = array();
            $sections[] = array(
                'title' => __('Section via hook', 'wpbizplugins-cahb'),
                'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'wpbizplugins-cahb'),
                'icon' => 'el-icon-paper-clip',
                // Leave this as a blank section, no options just some intro text set above.
                'fields' => array()
            );

            return $sections;
        }

        /**

          Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.

         * */
        function change_arguments($args) {
            //$args['dev_mode'] = true;

            return $args;
        }

        /**

          Filter hook for filtering the default value of any given field. Very useful in development mode.

         * */
        function change_defaults($defaults) {
            $defaults['str_replace'] = 'Testing filter hook!';

            return $defaults;
        }

        // Remove the demo link and the notice of integrated demo from the redux-framework plugin
        function remove_demo() {

            // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
            if (class_exists('ReduxFrameworkPlugin')) {
                remove_filter('plugin_row_meta', array(ReduxFrameworkPlugin::instance(), 'plugin_metalinks'), null, 2);

                // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
                remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
            }
        }

        public function setSections() {

            // ACTUAL DECLARATION OF SECTIONS

            $this->sections[] = array(
                'title' => __('Main configuration', 'wpbizplugins-cahb'),
                'desc' => __('Configure the custom admin help boxes. Please contact <a href="mailto:support@wpbizplugins.com">support@wpbizplugins.com</a> if you need further assistance.', 'wpbizplugins-cahb'),
                'icon' => 'el-icon-info-sign',
                // 'submenu' => false, // Setting submenu to false on a given section will hide it from the WordPress sidebar menu!
                'fields' => array(
                
                    /**
                     * Custom Admin Help Boxes configuration
                     *
                     */

                    array(
                        'id'       => 'menu_capability',
                        'type'     => 'select',
                        'title'    => __('Capability required', 'wpbizplugins-cahb'),
                        'subtitle' => __('The capability required to edit the menu', 'wpbizplugins-cahb'),
                        'desc'     => __('Set the capability required for editing the menu contents here. Use to restrict access for your clients.', 'wpbizplugins-cahb'),
                        // Must provide key => value pairs for select options
                        'options'  => wpbizplugins_cahb_return_capabilities_array(),
                        'default'  => 'delete_plugins',
                    ),

                    array(
                        'id'       => 'company_logo',
                        'type'     => 'media',
                        'url'      => true,
                        'title'    => __('Your company logo', 'wpbizplugins-cahb'),
                        'desc'     => __('Upload your company logo to here if you want it to be displayed in every help box. Helps with branding and looks professional.', 'wpbizplugins-cahb'),
                        'subtitle' => __('Will be displayed in every help box if set.', 'wpbizplugins-cahb')
                    ),

                    array(
                        'id'    => 'info_extra_contact',
                        'type'  => 'info',
                        'title' => __('Extra contact options', 'wpbizplugins-cahb'),
                        'style' => 'warning',
                        'desc'  => __('The options below will let you add extra contact details for your support. The contact details will be included at the bottom of each help box.', 'wpbizplugins-cahb')
                    ),

                    array(
                        'id'       => 'support_text',
                        'type'     => 'text',
                        'title'    => __('Text for support section', 'wpbizplugins-cahb'),
                        'subtitle' => __('This will be displayed right above the contact buttons set below.', 'wpbizplugins-cahb'),
                        'desc'     => __('Default:', 'wpbizplugins-cahb') . ' <em>Get in touch with us directly via:</em>',
                        'default'  => 'Get in touch with us directly via:'
                    ),

                    array(
                        'id'       => 'support_email',
                        'type'     => 'text',
                        'title'    => __('Support e-mail', 'wpbizplugins-cahb'),
                        'subtitle' => __('Where do you want people to e-mail if they have issues?', 'wpbizplugins-cahb'),
                        'desc'     => __('This will be put together with other contact details you specify below in each help box.', 'wpbizplugins-cahb'),
                        'validate' => 'email',
                        'msg'      => __('Invalid e-mail address, try again.', 'wpbizplugins-cahb')
                    ),

                    array(
                        'id'       => 'support_phone',
                        'type'     => 'text',
                        'title'    => __('Support phone number', 'wpbizplugins-cahb'),
                        'subtitle' => __('Where do you want people to call if they have issues?', 'wpbizplugins-cahb'),
                        'desc'     => __('This will be put together with other contact details you specify in each help box.', 'wpbizplugins-cahb'),
                        'validate' => 'no_html',
                        'msg'      => __('Number contains invalid characters, please try again.', 'wpbizplugins-cahb')
                    ),

                    array(
                        'id'       => 'support_url',
                        'type'     => 'text',
                        'title'    => __('Support URL', 'wpbizplugins-cahb'),
                        'subtitle' => __('Do you have an external support page?', 'wpbizplugins-cahb'),
                        'desc'     => __('If you have an external support page you want linked to in each help box, put the URL to that page here. This will be put together with other contact details you specify in each help box.', 'wpbizplugins-cahb'),
                        'validate' => 'url',
                        'msg'      => __('Invalid URL, try again.', 'wpbizplugins-cahb')
                    ),

                    array(
                        'id' => 'info_custom_css',
                        'type' => 'info',
                        'style' => 'warning',
                        'icon' => 'el-icon-info-sign',
                        'title' => __('Warning', 'wpbizplugins-cahb'),
                        'desc' => __('Only edit the custom CSS if you know what you are doing.', 'wpbizplugins-cahb')
                    ), 

                    array(
                        'id'       => 'custom_css',
                        'type'     => 'ace_editor',
                        'title'    => __('Custom CSS Code', 'wpbizplugins-cahb'),
                        'subtitle' => __('Put your custom CSS code here.', 'wpbizplugins-cahb'),
                        'mode'     => 'css',
                        'theme'    => 'monokai',
                        'desc'     => '
                            <strong>' . __('Available CSS selectors', 'wpbizplugins-cahb') . '</strong>' .
                            '<p><code>.wpbizplugins-cahb-metabox</code> - ' . __('The inner container for the metabox itself.', 'wpbizplugins-cahb') . '</p>' .
                            '<p><code>.wpbizplugins-cahb-company-logo</code> - ' . __('The company logo <img>.', 'wpbizplugins-cahb') . '</p>' .
                            '<p><code>.wpbizplugins-cahb-content</code> - ' . __('The text/content in the metabox that is editable.', 'wpbizplugins-cahb') . '</p>' .
                            '<p><code>.wpbizplugins-cahb-support-section</code> - ' . __('The bottom support section with buttons for e-mail, phone etc.', 'wpbizplugins-cahb') . '</p>' .
                            '<p><code>.wpbizplugins-cahb-extra-buttons</code> - ' . __('The container holding the buttons in the support section.', 'wpbizplugins-cahb') . '</p>' .
                            ''

                        ,
                        'default'  => ''
                    )
                )
            );

            $this->sections[] = array(
                'title'     => __('Import / Export', 'wpbizplugins-cahb'),
                'desc'      => __('Import and Export the menu settings from file, text or URL.', 'wpbizplugins-cahb'),
                'icon'      => 'el-icon-refresh',
                'fields'    => array(
                    array(
                        'id'            => 'opt-import-export',
                        'type'          => 'import_export',
                        'title'         => 'Import Export',
                        'subtitle'      => 'Save and restore your menu options',
                        'full_width'    => false,
                    ),
                ),
            );                     
                    
           

            if (file_exists(trailingslashit(dirname(__FILE__)) . 'README.html')) {
                $tabs['docs'] = array(
                    'icon'      => 'el-icon-book',
                    'title'     => __('Documentation', 'wpbizplugins-cahb'),
                    'content'   => nl2br(file_get_contents(trailingslashit(dirname(__FILE__)) . 'README.html'))
                );
            }
        }

        public function setHelpTabs() {

            // Custom page help tabs, displayed using the help API. Tabs are shown in order of definition.
            /*$this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-1',
                'title'     => __('Theme Information 1', 'wpbizplugins-cahb'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'wpbizplugins-cahb')
            );

            $this->args['help_tabs'][] = array(
                'id'        => 'redux-help-tab-2',
                'title'     => __('Theme Information 2', 'wpbizplugins-cahb'),
                'content'   => __('<p>This is the tab content, HTML is allowed.</p>', 'wpbizplugins-cahb')
            );

            // Set the help sidebar
            $this->args['help_sidebar'] = __('<p>This is the sidebar content, HTML is allowed.</p>', 'wpbizplugins-cahb');
        */
        }

        /**

          All the possible arguments for Redux.
          For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments

         * */
        public function setArguments() {

            //$theme = wp_get_theme(); // For use with some settings. Not necessary.

            $this->args = array(
                // TYPICAL -> Change these values as you need/desire
                'opt_name'          => 'wpbizplugins_cahb_options',            // This is where your data is stored in the database and also becomes your global variable name.
                'display_name'      => 'Configuration',            // Name that appears at the top of your panel
                'display_version'   => '1.0',  // Version that appears at the top of your panel
                'menu_type'         => 'submenu',                  //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
                'allow_sub_menu'    => true,                    // Show the sections below the admin menu item or not
                'menu_title' => __('Configuration', 'wpbizplugins-cahb'),
                'page_title' => __('Custom Admin Help Boxes Options', 'wpbizplugins-cahb'),
                
                // You will need to generate a Google API key to use this feature.
                // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
                'google_api_key' => '', // Must be defined to add google fonts to the typography module
                
                'async_typography'  => false,                    // Use a asynchronous font on the front end or font string
                'admin_bar'         => false,                    // Show the panel pages on the admin bar
                'global_variable'   => '',                      // Set a different name for your global variable other than the opt_name
                'dev_mode'          => false,                    // Show the time the page took to load, etc
                'customizer'        => false,                    // Enable basic customizer support
                
                // OPTIONAL -> Give you extra features
                'page_priority'     => null,                    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
                'page_parent'       => 'edit.php?post_type=wpbizplugins-cahb',            // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
                'page_permissions'  => 'manage_options',        // Permissions needed to access the options panel.
                'menu_icon'         => '',                       // Specify a custom URL to an icon
                'last_tab'          => '',                      // Force your panel to always open to a specific tab (by id)
                'page_icon'         => 'icon-themes',           // Icon displayed in the admin panel next to your menu_title
                'page_slug'         => 'wpbizplugins_cahb_options',              // Page slug used to denote the panel
                'save_defaults'     => true,                    // On load save the defaults to DB before user clicks save or not
                'default_show'      => false,                   // If true, shows the default value next to each field that is not the default value.
                'default_mark'      => '',                      // What to print by the field's title if the value shown is default. Suggested: *
                'show_import_export' => false,                   // Shows the Import/Export panel when not used as a field.
                
                // CAREFUL -> These options are for advanced use only
                'transient_time'    => 60 * MINUTE_IN_SECONDS,
                'output'            => true,                    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
                'output_tag'        => true,                    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
                // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
                
                // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
                'database'              => '', // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
                'system_info'           => false, // REMOVE

                // HINTS
                'hints' => array(
                    'icon'          => 'icon-question-sign',
                    'icon_position' => 'right',
                    'icon_color'    => 'lightgray',
                    'icon_size'     => 'normal',
                    'tip_style'     => array(
                        'color'         => 'light',
                        'shadow'        => true,
                        'rounded'       => false,
                        'style'         => '',
                    ),
                    'tip_position'  => array(
                        'my' => 'top left',
                        'at' => 'bottom right',
                    ),
                    'tip_effect'    => array(
                        'show'          => array(
                            'effect'        => 'slide',
                            'duration'      => '500',
                            'event'         => 'mouseover',
                        ),
                        'hide'      => array(
                            'effect'    => 'slide',
                            'duration'  => '500',
                            'event'     => 'click mouseleave',
                        ),
                    ),
                )
            );


            // SOCIAL ICONS -> Setup custom links in the footer for quick links in your panel footer icons.
            $this->args['share_icons'][] = array(
                'url'   => 'https://www.facebook.com/wpbizplugins',
                'title' => 'Like us on Facebook',
                'icon'  => 'el-icon-facebook'
            );
            $this->args['share_icons'][] = array(
                'url'   => 'http://twitter.com/wpbizplugins',
                'title' => 'Follow us on Twitter',
                'icon'  => 'el-icon-twitter'
            );

            // Panel Intro text -> before the form
            $this->args['intro_text'] = '<img src="' . plugins_url( '../assets/img/wpbizplugins-cahb-logo.png', __FILE__ ) . '"><p>' . __('Welcome to the Custom Admin Help Boxes configuration. Configure everything needed for display of the boxes here.', 'wpbizplugins-cahb') . '</p>';

            // Add content after the form.
            $this->args['footer_text'] = '<a href="http://www.wpbizplugins.com?utm_source=cahb&utm_medium=plugin&utm_campaign=cahb" target="_blank"><img style="margin-top: 20px; margin-bottom: 20px;" src="' . plugins_url( '../assets/img/wpbizplugins-footer-img.png', __FILE__ ) . '"></a>';
        }

    }
    
    global $wpbizplugins_cahb_options_config;
    $wpbizplugins_cahb_options_config = new WPBizPlugins_CustomAdminHelpBoxes_Config();
}

/**
  Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')):
    function redux_my_custom_field($field, $value) {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
endif;

/**
  Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')):
    function redux_validate_callback_function($field, $value, $existing_value) {
        $error = false;
        $value = 'just testing';

        /*
          do your validation

          if(something) {
            $value = $value;
          } elseif(something else) {
            $error = true;
            $value = $existing_value;
            $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }
        return $return;
    }
endif;
