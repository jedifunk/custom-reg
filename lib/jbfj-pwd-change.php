<?php
function jbfj_change_password_form() {
	global $post;	
 
	if (is_singular()) :
		$current_url = get_permalink($post->ID);
	else :
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") $pageURL .= "s";
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$current_url = $pageURL;
	endif;		
	$redirect = $current_url; ?>

	<div class="form-wrap">
		<?php ob_start();
 
		// show any error messages after form submission
		jbfj_show_error_messages(); ?>
		<?php if(isset($_GET['password-reset']) && $_GET['password-reset'] == 'true') { ?>
			<div class="jbfj_message success">
				<span><?php _e('Password changed successfully', 'rcp'); ?></span>
			</div>
		<?php } ?>
		<form id="jbfj_password_form" method="POST" action="<?php echo $current_url; ?>">
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
					<input type="hidden" name="jbfj_action" value="reset-password"/>
					<input type="hidden" name="jbfj_redirect" value="<?php echo $redirect; ?>"/>
					<input type="hidden" name="jbfj_password_nonce" value="<?php echo wp_create_nonce('rcp-password-nonce'); ?>"/>
					<input id="jbfj_password_submit" type="submit" class="btn btn-primary btn-block" value="<?php _e('Change Password', 'jbfj'); ?>"/>
				</div>
			</fieldset>
		</form>
	<?php return ob_get_clean(); ?>
	</div><!-- .form-wrap -->
<?php }
 
// password reset form
function jbfj_reset_password_form() {
	if(is_user_logged_in()) {
		return jbfj_change_password_form();
	}
}
add_shortcode('pwd_reset_form', 'jbfj_reset_password_form');
 
 
function jbfj_reset_password() {
	// reset a users password
	if(isset($_POST['jbfj_action']) && $_POST['jbfj_action'] == 'reset-password') {
 
		global $user_ID;
 
		if(!is_user_logged_in())
			return;
 
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
					'ID' => $user_ID,
					'user_pass' => $_POST['jbfj_user_pass']
				);
				wp_update_user($user_data);
				// send password change email here (if WP doesn't)
				wp_redirect(add_query_arg('password-reset', 'true', $_POST['jbfj_redirect']));
				exit;
			}
		}
	}	
}
add_action('init', 'jbfj_reset_password');
 
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