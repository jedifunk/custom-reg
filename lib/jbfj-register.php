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
	
	$defaults = array(
		// form
		'form_class' => 'ajax-auth clear register',
		
		//form fields
		'field_before' => '<div class="form-group">',
		'field_after' => '</div>',
		
		// button
		'button_class' => 'btn btn-primary btn-lg btn-block',
		'button_text' => __( 'Download Now' ),
		'button_icon' => '<i class="fa fa-download fa-fw"></i> ',
		
	);
	
	$args = apply_filters( 'jbfj_reg_form_filter', '' );
	
	extract( wp_parse_args( $args, $defaults ) );
 
	ob_start(); ?>	
		<form id="vaForm" class="<?php echo $form_class; ?>" action="register" name="vaForm" method="post">
			<div class="status"></div>
			<?php wp_nonce_field( 'ajax-register-nonce', 'submitsecurity'); ?>
			<fieldset>
				<div class="form-group">
					<label for="username"><?php _e('Email*'); ?></label>
					<input name="Email" id="username" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="user_first"><?php _e('First Name*'); ?></label>
					<input name="FirstName" id="user_first" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="user_last"><?php _e('Last Name*'); ?></label>
					<input name="LastName" id="user_last" class="textbox required" type="text"/>
				</div>
				<div class="form-group">
					<label for="phone"><?php _e('Phone'); ?></label>
					<input name="PhoneNumber" id="phone" class="textbox" type="text"/>
				</div>
				<div class="form-group">
					<button name="submit" class="<?php echo $button_class; ?>" type="submit"><?php echo $button_icon; ?><?php echo $button_text; ?></button>
				</div>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}