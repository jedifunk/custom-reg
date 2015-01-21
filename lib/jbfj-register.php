<?php
// user registration login form
function jbfj_registration_form() {
 
	// only show the registration form to non-logged-in members
	if(!is_user_logged_in()) {
		
		// check to make sure user registration is enabled
		$registration_enabled = get_option('users_can_register');
 
		// only show the registration form if allowed
		if($registration_enabled) {
			$output = jbfj_registration_form_fields();
		} else {
			$output = __('User registration is not enabled');
		}
		return $output;
	}
}
add_shortcode('register_form', 'jbfj_registration_form');

// registration form fields
function jbfj_registration_form_fields() {
 
	ob_start(); ?>	
		<form id="register" class="ajax-auth" action="register" name="vaForm" method="post">
			<p class="status"></p>
			<?php wp_nonce_field( 'ajax-register-nonce', 'submitsecurity'); ?>
			<fieldset>
				<div class="form-group">
					<label for="username"><?php _e('Choose a Username'); ?></label>
					<input name="username" id="username" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="user_email"><?php _e('Email'); ?></label>
					<input name="Email" id="user_email" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="user_first"><?php _e('First Name'); ?></label>
					<input name="FirstName" id="user_first" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="user_last"><?php _e('Last Name'); ?></label>
					<input name="LastName" id="user_last" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="phone"><?php _e('Phone'); ?></label>
					<input name="PhoneNumber" id="phone" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<input name="submit" class="btn btn-primary btn-lg btn-block" type="submit" value="<?php _e('Access Now!'); ?>"/>
				</div>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}