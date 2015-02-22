<?php
// forgot password form
function jbfj_forgot() {
	if(!is_user_logged_in()) {
		
		if( $_GET['action'] == 'rp' ) {
			return jbfj_reset_form();
		} else {
			return jbfj_forgot_form();
		}
	}
}
add_shortcode('forgot_form', 'jbfj_forgot');

function jbfj_forgot_form(){ 
?>

	<div class="form-wrap">
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="wp-user-form">
		<div class="username form-group">
		    <label for="user_login" class="hide"><?php _e('Email'); ?>: </label>
		    <input type="text" name="user_login" value="" id="user_login" />
		</div>
		<div class="login_fields form-group">
		    <?php do_action('login_form', 'resetpass'); ?>
		    <input type="submit" name="user-submit" value="<?php _e('Reset my password'); ?>" class="user-submit btn btn-block btn-primary" tabindex="1002" />

		    <?php
		    if (isset($_POST['reset_pass'])) {
		        global $wpdb;
				$username = trim($_POST['user_login']);
				$user_exists = false;
				// First check by username
				if ( username_exists( $username ) ){
				    $user_exists = true;
				    $user = get_user_by('login', $username);
				}
				// Then, by e-mail address
				elseif( email_exists($username) ){
				        $user_exists = true;
				        $user = get_user_by_email($username);
				}else{
				    $error[] = '<p>'.__('Username or Email was not found, try again!').'</p>';
				}
				if ($user_exists){
				    $user_login = $user->user_login;
				    $user_email = $user->user_email;
				
				    $key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
				    if ( empty($key) ) {
				        // Generate something random for a key...
				        $key = wp_generate_password(20, false);
				        do_action('retrieve_password_key', $user_login, $key);
				        // Now insert the new md5 key into the db
				        $wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
				    }
				
				    //create email message
				    $message = __('Someone has asked to reset the password for the following site and username.') . "\r\n\r\n";
				    $message .= get_option('siteurl') . "\r\n\r\n";
				    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
				    $message .= __('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\r\n\r\n";
				    $message .= site_url("my-profile?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "\r\n";
				    //send email meassage
				    if (FALSE == wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_option('blogname')), $message))
				    $error[] = '<p>' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>';
				}
				if (count($error) > 0 ){
				    foreach($error as $e){
				                echo $e . "<br/>";
				            }
				}else{
				    echo '<div class="bg-success">'.__('A message will be sent to your email address.').'</div>'; 
				}
		    }
		    ?> 
		    <input type="hidden" name="reset_pass" value="1" />
		    <input type="hidden" name="user-cookie" value="1" />
		</div><!-- .login-fields -->
		</form>
	</div><!-- .form-wrap -->

<?php }

// Reset form
function jbfj_reset_form() { ?>
	
	<div class="form-wrap">
		<?php ob_start();
 
		// show any error messages after form submission
		jbfj_show_error_messages(); ?>
		<?php if(isset($_GET['np']) && $_GET['np'] == '1') { ?>
			<div class="bg-success">
				<?php _e('Password changed successfully, please login', 'rcp'); ?>
			</div>
			<?php echo do_shortcode('[login_form]'); ?>
		<?php } else { ?>
		<form id="jbfj_password_form" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<fieldset>
				<div class="form-group">
					<label for="jbfj_user_pass"><?php _e('New Password', 'rcp'); ?></label>
					<input name="jbfj_user_pass" id="jbfj_user_pass" class="required" type="password"/>
				</div>
				<div class="form-group">
					<label for="jbfj_user_pass_confirm"><?php _e('Password Confirm', 'rcp'); ?></label>
					<input name="jbfj_user_pass_confirm" id="jbfj_user_pass_confirm" class="required" type="password"/>
				</div>
				<div class="form-group">
					<input type="hidden" name="jbfj_action" value="new-password"/>
					<input type="hidden" name="jbfj_redirect" value="<?php echo home_url(); ?>"/>
					<input type="hidden" name="jbfj_password_nonce" value="<?php echo wp_create_nonce('rcp-password-nonce'); ?>"/>
					<input id="jbfj_password_submit" type="submit" class="btn btn-primary btn-block" value="<?php _e('Change Password', 'jbfj'); ?>"/>
				</div>
			</fieldset>
		</form>
		<?php } ?>
		
		<?php return ob_get_clean(); ?>
	</div><!-- .form-wrap -->
<?php }
 
function jbfj_new_password() {
	// reset a users password
	if(isset($_POST['jbfj_action']) && $_POST['jbfj_action'] == 'new-password') {
 
		$get_user = htmlspecialchars($_GET['login']);
		$user = get_user_by('login', $get_user);
		$u_id = $user->ID;
 
		if(wp_verify_nonce($_POST['jbfj_password_nonce'], 'rcp-password-nonce')) {
 
			if($_POST['jbfj_user_pass'] == '' || $_POST['jbfj_user_pass_confirm'] == '') {
				// password(s) field empty
				jbfj_errors()->add('password_empty', __('Please enter a password, and confirm it', 'jbfj'));
			}
			if($_POST['jbfj_user_pass'] != $_POST['jbfj_user_pass_confirm']) {
				// passwords do not match
				jbfj_errors()->add('password_mismatch', __('Passwords do not match', 'jbfj'));
			}
 
			// retrieve all error messages, if any
			$errors = jbfj_errors()->get_error_messages();
 
			if(empty($errors)) {
				// change the password here
				$user_data = array(
					'ID' => $u_id,
					'user_pass' => $_POST['jbfj_user_pass']
				);
				wp_update_user($user_data);
				
				wp_redirect( add_query_arg( 'np', '1' ) );

				exit;
			}
		}
	}	
}
add_action('init', 'jbfj_new_password');

if(!function_exists('jbfj_show_error_messages')) {
	// displays error messages from form submissions
	function jbfj_show_error_messages() {
		if($codes = jbfj_errors()->get_error_codes()) {
			echo '<div class="jbfj_message error">';
			    // Loop error codes and display errors
			   foreach($codes as $code){
			        $message = jbfj_errors()->get_error_message($code);
			        echo '<span class="jbfj_error"><strong>' . __('Error', 'rcp') . '</strong>: ' . $message . '</span><br/>';
			    }
			echo '</div>';
		}	
	}
}
 
if(!function_exists('jbfj_errors')) { 
	// used for tracking error messages
	function jbfj_errors(){
	    static $wp_error; // Will hold global variable safely
	    return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}
}