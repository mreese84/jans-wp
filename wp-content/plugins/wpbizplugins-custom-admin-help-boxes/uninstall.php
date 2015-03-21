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
 
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();


// Delete all of our posts.

$args = array(
    
    'post_type' => 'wpbizplugins-cahb'
    
);

$menu_elements = new WP_Query( $args );
if( $menu_elements->have_posts() ) {

    while( $menu_elements->have_posts() ) {
        
        $menu_elements->the_post();
        wp_delete_post( get_the_ID(), true );
    }

}
