<?php

add_action( 'wp_enqueue_scripts', 'ajax_ew_main_enqueue_scripts' );
function ajax_ew_main_enqueue_scripts() {
   wp_enqueue_style( 'ew-grid', EW_PLUGIN_URL.'assets/css/ew-grid.css', 10, 1.0 );
   wp_enqueue_style( 'ew-main-style', EW_PLUGIN_URL. 'assets/css/ew-main.css', 999, 1.0 );
   wp_enqueue_script( 'ew-main', EW_PLUGIN_URL.'assets/js/ew-main.js', array('jquery'), '1.0', true );

}

function load_ew_wp_admin_style() {
   wp_enqueue_style( 'ew-admin-style', EW_PLUGIN_URL.'assets/css/ew-admin.css', 11, 1.0 );
}

add_action( 'admin_enqueue_scripts', 'load_ew_wp_admin_style' );