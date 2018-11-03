<?php
/*
* Plugin Name: Gibby's Quick Update
* Plugin URI: http://paramountwebtechnology.com/
* Description: Gibby's Quick Update Plugin
* Version: 1.0
* Text Domain: wp-gibbys-quick-update
* Author: Paramount Web Technology
* Author URI: http://paramountwebtechnology.com/
* License: GPLv2
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// add_action( 'admin_menu', 'gibbys_add_menu_page' );
// function gibbys_add_menu_page(){
//     add_menu_page('Quick Update' , 'Quick Update' , 'edit_pages', 'gibbys_quick_update', 'gibbys_quick_updater');
// }

add_action( 'admin_menu', 'forte_add_menu_page' );
function forte_add_menu_page(){
    add_menu_page('Quick Update' , 'Quick Update' , 'edit_pages', 'gibbys_quick_update', 'gibbys_quick_updater');
    add_submenu_page('gibbys_quick_update', 'Search Query', 'Search Query', 'manage_options', 'gibbys_quick_update/search_query', 'search_query_page');

}

 function search_query_page(){
   global $plugin_url;
 	 global $options;
   require('search_query_page.php');
 }

function gibbys_quick_updater(){
	if( !current_user_can( 'edit_pages' ) ){
		wp_die( 'You do not have sufficient permissions to access this page.' );
	}
	global $plugin_url;
	global $options;
    require('options-page-wrapper.php');
}

function gibbys_quick_update_admin_bar() {
    global $wp_admin_bar;

    $wp_admin_bar->add_menu(array('id' => $menu_id, 'title' => 'Quick Update', 'href' => admin_url( 'admin.php?page=gibbys_quick_update' )));
}
add_action('admin_bar_menu', 'gibbys_quick_update_admin_bar', 2000);

add_action( 'admin_init', 'gibbys_quick_update_init' );
function gibbys_quick_update_init() {
    wp_enqueue_style( 'gibbys-quick-update-styles', plugins_url( 'style.css', __FILE__ ) );
    wp_enqueue_script('gibbys-quick-update-js', plugins_url( 'js/gibbys-quick-update.js', __FILE__ ), array('jquery'), '', true);
    wp_enqueue_script('cat_brand-js', plugins_url( 'js/cat_brand.js', __FILE__ ), array('jquery'), '', true);
    wp_enqueue_script('search-query-js', plugins_url( 'js/search-query.js', __FILE__ ), array('jquery'), '', true);
}
?>
