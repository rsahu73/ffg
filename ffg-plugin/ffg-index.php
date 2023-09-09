<?php
/*
Plugin Name:  FFG Custom Profile Plugin
Description:  FFG Custom Plugin for assigining Persona to User. 
Version:      1.0
Author:       FFG Team 
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

//Include CSS file
add_action( 'wp_enqueue_scripts', 'load_ffg_css' );
add_action( 'admin_enqueue_scripts', 'load_ffg_css' );


// Include functions file
require_once plugin_dir_path(__FILE__) . 'includes/ffg-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/ffg-admin.php';

function load_ffg_css(){
    wp_enqueue_style( 'ffg-css', plugin_dir_url(__FILE__) . 'includes/ffg-admin.css' );
}

