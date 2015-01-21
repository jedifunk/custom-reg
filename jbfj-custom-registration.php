<?php
/*
Plugin Name: JBFJ Custom Registration
Description: Front end registration and login
Version: 0.1
Author: Bryce Flory
*/

// set constants
define( 'JBFJ_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'JBFJ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once( JBFJ_PLUGIN_PATH . '/lib/jbfj-login.php' );
require_once( JBFJ_PLUGIN_PATH . '/lib/jbfj-register.php' );
require_once( JBFJ_PLUGIN_PATH . '/lib/jbfj-pwd-change.php' );
require_once( JBFJ_PLUGIN_PATH . '/lib/jbfj-profile.php' );
require_once( JBFJ_PLUGIN_PATH . '/lib/jbfj-forgot.php' );
require_once( JBFJ_PLUGIN_PATH . '/lib/ajax-auth.php' );

// used for tracking error messages
function jbfj_errors(){
    static $wp_error; // Will hold global variable safely
    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
}

// displays error messages from form submissions
function jbfj_show_error_messages() {
	if($codes = jbfj_errors()->get_error_codes()) {
		echo '<div class="jbfj_errors">';
		    // Loop error codes and display errors
		   foreach($codes as $code){
		        $message = jbfj_errors()->get_error_message($code);
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}	
}

// custom new user email
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
		
		// topic cookie
		$topic_cookie = isset( $_COOKIE['topic-cookie'] ) ? $_COOKIE['topic-cookie'] : 'not set';
		
		//Download
		$topic_slug = $topic_cookie;
		$topic_slug = str_replace('-', '_', basename( get_permalink() ) ); 
		$id = get_option( $topic_slug.'_dl' );
		$img = get_post($id[dl]);
		
		$url_args = get_posts( array(
			'post_parent' => $img->ID,
			'post_type' => 'attachment',
			'post_mime_type' => 'application/pdf',
			'exclude' => get_post_thumbnail_id()
		));
		$url = wp_get_attachment_url($url_args[0]->ID);
		$name = $url_args[0]->post_title;
		
		$extra = 'Here is your requested file.' ."\n\n".
			'<a href="'. $url . '">'. $name . '</a>' ."\n";
			
		
        $message  = sprintf(__('You have a New Lead from %s:'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Topic of Interest: %s'), $topic_cookie) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

        if ( empty($plaintext_pass) )
            return;

        $message  = __('Hi there,') . "\r\n\r\n";
        $message .= sprintf(__("Welcome to %s! Here's how to log in:"), get_option('blogname')) . "\r\n\r\n";
        $message .= wp_login_url() . "\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n\r\n";
        $message .= $extra . "\r\n\r\n";
        
        $message .= sprintf(__('If you have any problems, please contact me at %s.'), get_option('admin_email')) . "\r\n\r\n";
        

        wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message);

    }
}