<?php
/*
Plugin Name: JBFJ Custom Registration
Description: Front end registration and login
Version: 1.1
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

if(!function_exists('jbfj_errors')) {
	// used for tracking error messages
	function jbfj_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}

if(!function_exists('jbfj_show_error_messages')) {
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
}

// custom new user email
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        $user = new WP_User($user_id);

        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
		
		// topic cookie
		$topic_cookie = isset( $_COOKIE['topic-cookie'] ) ? $_COOKIE['topic-cookie'] : 'not set';	
		
		$fname = stripslashes($user->first_name);
		$lname = stripslashes($user->last_name);
		$name = $fname .' '. $lname;
		$phone = $user->phone;		
		
        $message  = sprintf(__('You have a New Lead from %s.'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Topic of Interest: %s'), $topic_cookie) . "\r\n";
        $message .= sprintf(__('Name: %s'), $name) . "\r\n";
        $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
        $message .= sprintf(__('Phone Number: %s'), $phone	) . "\r\n\r\n";
        $message .= sprintf(__('You can also find this lead information inside your KonnexMe dashboard')) . "\r\n";

        @wp_mail(get_theme_mod('admin_email'), sprintf(__('New Lead from %s'), get_option('blogname')), $message);

        if ( empty($plaintext_pass) ) {
            return;
        }
        
        // New User Email
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
		
		$extra = 'Here is your requested file.' ."\r\n".
			'<a href="'. $url . '">'. $name . '</a>' ."\n";
		
        $message2  = sprintf(__('Hi %s,'), $fname) . "\r\n\r\n";
        $message2 .= sprintf(__("Welcome to %s!"), get_option('blogname')) . "\r\n\r\n";
        $message2 .= sprintf(__("Below is your login credentials.")) . "\r\n\r\n";
        $message2 .= sprintf( home_url() ) . "\r\n";
        $message2 .= sprintf(__('Username: %s'), $user_login) . "\r\n";
        $message2 .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n\r\n";
        $message2 .= $extra . "\r\n\r\n";
        //signature
        $message2 .= '----------' . "\r\n";
        $message2 .= sprintf(__('%s'), get_theme_mod('advisor_name')) . "\r\n";
        $message2 .= sprintf(__('%s'), get_theme_mod('insurance_name')) . "\r\n";
        $message2 .= sprintf(__('%s'), get_theme_mod('advisor_email')) . "\r\n";
        $message2 .= sprintf(__('%s'), get_theme_mod('advisor_phone')) . "\r\n";
        $message2 .= sprintf(__('%s'), get_theme_mod('advisor_website')) . "\r\n";

        wp_mail($user_email, sprintf(__('Welcome to %s'), get_option('blogname')), $message2);

    }
}