JBFJ Custom Registration
=======================

**Custom Wordpress front-end registration, login, and password reset plugin.**

Currently specialized to a specific project. Long term goal to make plugin generic and useable in any project.

Uses Ajax for authentication. Uses `wp_mail` and `wp_new_user_notification` for email.
Uses jQuery validate for validation. http://jqueryvalidation.org/

Roadmap
----------
 - Add custom filters
 - Build Admin interface for creating form input fields

Usage
----------
Shortcodes available
+ Login `[login_form]`
+ Register `[register_form]`
+ Password Reset `[pwd_reset_form]`
+ Forgot `[forgot_form]`
+ User Profile Output `[jbfj_profile]`

**Available Filters**
Registration has three filters currently that are called via `jbfj_reg_form_filter`
+ `<form>` class using `form_class`
+ Input field wrapper using `field_before` and `field_after`
+ Button classes, text, and optional icon using `button_class`, `button_text` and `button_icon` respectively.

Example usage
```
function reg_btn( $args ){
		$args = array(
			'button_text' => 'Join Today!',
			'button_icon' => ''
		);
	return $args;
}
add_filter( 'jbfj_reg_form_filter', 'reg_btn' );
```

Change Log:
----------

**1.1: February 13, 2015**
+ PW set to 5 with special characters
 
**1.0: February 5, 2015**
+ Registration, Login, PW Reset, Forgot password all working

**0.1: January 21, 2015**
+ Initial Commit
