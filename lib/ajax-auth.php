<?php
function ajax_auth_init(){
	if ( ! is_user_logged_in() ) {
		wp_enqueue_script('validate-script', JBFJ_PLUGIN_URL . 'lib/js/jquery.validate.min.js', array('jquery'), null, true ); 

	    wp_enqueue_script('ajax-auth-script', JBFJ_PLUGIN_URL . 'lib/js/jbfj-ajax-auth-script.js', array('jquery'), null, true ); 
	
	    // Enable the user with no privileges to run ajax_login() in AJAX
	    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
	    
		// Enable the user with no privileges to run ajax_register() in AJAX
		add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
		
	}

}
add_action( 'init', 'ajax_auth_init' );

function ajax_localize() {
	
	if ( ! is_user_logged_in() ) {
		
		if ( is_page( 'register' ) ) {
			
			wp_localize_script( 'ajax-auth-script', 'ajax_auth_object', array( 
	        	'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'redirecturl' => home_url(),
				'vaID' => get_theme_mod('vaID'),
				'loadingmessage' => __('Sending user info, please wait...')
			));
			
		} else {
			
			wp_localize_script( 'ajax-auth-script', 'ajax_auth_object', array( 
		        'ajaxurl' => admin_url( 'admin-ajax.php' ),
		        'redirecturl' => get_permalink(),
		        'vaID' => get_theme_mod('vaID'),
		        'loadingmessage' => __('Sending user info, please wait...')
		    ));
		    
		}

	}
}
add_action( 'template_redirect', 'ajax_localize' );
  
function ajax_login(){

    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
  	// Call auth_user_login
	auth_user_login($_POST['username'], $_POST['password'], 'Login'); 
	
    die();
}

function auth_user_login($user_login, $password, $login){
	$fields = array();
    $fields['user_login'] = $user_login;
    $fields['user_password'] = $password;
    $fields['remember'] = true;
	
	$user_signon = wp_signon( $fields, false );
    if ( is_wp_error($user_signon) ){
		echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
    } else {
		wp_set_current_user($user_signon->ID); 
        echo json_encode(array('loggedin'=>true, 'message'=>__($login.' successful, redirecting...')));
    }
	
	die();
}

function ajax_register(){
 
	// First check the nonce, if it fails the function will break
	check_ajax_referer( 'ajax-register-nonce', 'security' );
	
	// Nonce is checked, get the POST data and sign user on
	$fields = array();
	$fields['user_login'] = sanitize_user($_POST['username']);
	$fields['user_email'] = sanitize_email( $_POST['username']);
	$fields['user_pass'] = wp_generate_password(5, true, false);
	$fields['first_name'] = sanitize_text_field($_POST['user_first']);
	$fields['last_name'] = sanitize_text_field($_POST['user_last']);
	$fields['phone'] = sanitize_text_field($_POST['phone']);
	
	// Register the user
	$user_register = wp_insert_user( $fields );
	update_user_meta($user_register, 'phone', $_POST['phone']);
	
	if ( is_wp_error($user_register) ){ 
		$error  = $user_register->get_error_codes() ;
	
		if(in_array('empty_user_login', $error)) {
	
			echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'))));
			
		} elseif(in_array('existing_user_login',$error)) {
			
			echo json_encode(array('loggedin'=>false, 'message'=>__('This username is already registered.')));
			
		} elseif(in_array('existing_user_email',$error)) {
			
			echo json_encode(array('loggedin'=>false, 'message'=>__('This email address is already registered.')));
			
		}
		
	} else {
		
		wp_new_user_notification( $user_register, $fields['user_pass'] );
		
		$user = get_user_by('login', $fields['user_login']);
		$uid = $user->ID;
		
		$user_signon = wp_signon(
			array(
				'user_login' => $user->user_login,
				'user_password' => $fields['user_pass'],
				'remember' => true
			)
		);
	    
		if ( is_wp_error($user_signon) ){
			
			echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
			
	    } else { 
		    
		    wp_set_current_user( $uid );
		    wp_set_auth_cookie( $uid, true );

	        echo json_encode(array('loggedin'=>true, 'message'=>__('Registration successful, redirecting...')));
	    }
	
	}
	
	die();
}