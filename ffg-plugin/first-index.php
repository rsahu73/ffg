<?php
/*
Plugin Name:  FFG Custom Plugin Test
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
//require_once plugin_dir_path(__FILE__) . 'includes/ffg-custom-settings.php';

function load_ffg_css(){
    wp_enqueue_style( 'ffg-css', plugin_dir_url(__FILE__) . 'includes/ffg-admin.css' );
}


function wpb_follow_us($content) {
 
    // Only do this when a single post is displayed
    if ( is_single() ) { 
     
    // Message you want to display after the post
    // Add URLs to your own Twitter and Facebook profiles
     
    $content .= '<p class="follow-us">From the custom plugin<br>If you liked this article, then please follow  us on <a href="http://twitter.com/wpbeginner" title="WPBeginner on Twitter" target="_blank" rel="nofollow">Twitter</a> and <a href="https://www.facebook.com/wpbeginner" title="WPBeginner on Facebook" target="_blank" rel="nofollow">Facebook</a>.</p>';
     
    } 
    // Return the content
    return $content; 
     
    }
    // Hook our function to WordPress the_content filter
    add_filter('the_content', 'wpb_follow_us'); 
