<?php
// user login form
function jbfj_login_form() {
 
	if(!is_user_logged_in()) {
 
		$output = jbfj_login_form_fields();

	}

	return $output;
}
add_shortcode('login_form', 'jbfj_login_form');

// login form fields
function jbfj_login_form_fields() {
 
	ob_start(); ?> 
		<form id="login" class="ajax-auth" action="login" method="post">
			<p class="status"></p>
			<?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
			<fieldset>
				<div class="form-group">
					<label for="username">Username</label>
					<input id="username" type="text" class="required" name="username">
				</div>
				<div class="form-group">
					<label for="password">Password</label>
					<input id="password" type="password" class="required" name="password">
				</div>
				<div class="form-group">
					<input id="jbfj_login_submit" type="submit" name="submit" class="btn btn-primary btn-submit-lg btn-block" value="Login">
				</div>
			</fieldset>
		</form>
	<?php
	return ob_get_clean();
}