<?php
/*  WPBizPlugins Easy Admin Quick Menu
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

function wpbizplugins_cahb_activation_function() {

    $args = array(
    
        'post_type' => 'wpbizplugins-cahb'
        
    );

    $menu_elements = new WP_Query( $args );
    if( !$menu_elements->have_posts() ) {

        $post = array(
          'post_title'     => 'Example help box',
          'post_type'      => 'wpbizplugins-cahb',
          'post_status'    => 'publish'
        );  

        $default_button = wp_insert_post( $post );

        $content = '
                <p>This is an example of how a custom admin help box can look. Here follows instructions on how to add your own custom help box.</p>
                <ol>
                <li>Fill in the fields and select your options.</li>
                <li>Press publish.</li>
                <li>Go check out your brand new help box in action!</li>
                </ol>
                <p>You can add contact details and more on the <a href="' . get_admin_url() . 'edit.php?post_type=wpbizplugins-cahb">configuration page</a>. <strong>Remember that you can also easily create dashboard widgets, that you can use to for example welcome your users.</strong>
                </p>
                <p>Much joy!</p>
        ';

        // Add all of the meta values
        add_post_meta( $default_button, 'content', $content );
        add_post_meta( $default_button, 'url', get_admin_url() . 'post-new.php' );
        add_post_meta( $default_button, 'where_to_display', array('wpbizplugins-cahb') );
        add_post_meta( $default_button, 'context', 'side' );
        add_post_meta( $default_button, 'priority', 'high' );
        add_post_meta( $default_button, 'show_extras', 1 );
        add_post_meta( $default_button, 'use_popup', 0 );
        add_post_meta( $default_button, 'autop', 1 );

    }

    wp_reset_query();
    wp_reset_postdata();

}
