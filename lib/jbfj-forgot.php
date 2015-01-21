<?php
// forgot password form
function jbfj_forgot() {
	if(!is_user_logged_in()) {
		return jbfj_forgot_form();
	}
}
add_shortcode('forgot_form', 'jbfj_forgot');

function jbfj_forgot_form(){ 
?>

	<div class="form-wrap">
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="wp-user-form">
		<div class="username">
		    <label for="user_login" class="hide"><?php _e('Username or Email'); ?>: </label>
		    <input type="text" name="user_login" value="" size="20" id="user_login" tabindex="1001" />
		</div>
		<div class="login_fields">
		    <?php do_action('login_form', 'resetpass'); ?>
		    <input type="submit" name="user-submit" value="<?php _e('Reset my password'); ?>" class="user-submit" tabindex="1002" />
		
		    <?php
		    if (isset($_POST['reset_pass']))
		    {
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
				    $message .= site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . "&redirect_to=" . $_SERVER['REQUEST_URI'] ."\r\n";
				    //send email meassage
				    if (FALSE == wp_mail($user_email, sprintf(__('[%s] Password Reset'), get_option('blogname')), $message))
				    $error[] = '<p>' . __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') . '</p>';
				}
				if (count($error) > 0 ){
				    foreach($error as $e){
				                echo $e . "<br/>";
				            }
				}else{
				    echo '<p>'.__('A message will be sent to your email address.').'</p>'; 
				}
		    }
		    ?> 
		    <input type="hidden" name="reset_pass" value="1" />
		    <input type="hidden" name="user-cookie" value="1" />
		</div>
		</form>
	</div>

<? }