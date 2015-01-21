<?php
	// profile shortcode
function jbfj_profile() {
	if ( is_user_logged_in() ) {
		return jbfj_profile_data();
	}
}
add_shortcode('jbfj_profile', 'jbfj_profile');

// get current user data
function jbfj_profile_data() {
	
	$current = wp_get_current_user();
	
	$date = date( "F j, Y", strtotime($current->user_registered) );
	
	
	$items = array();
	$items['Username'] = $current->user_login;
	$items['Email'] = $current->user_email;
	$items['First Name'] = $current->first_name;
	$items['Last Name'] = $current->last_name;
	$items['Phone'] = $current->phone;
	$items['Member Since'] = $date;
	
?>
	
	<div class="box">
		<header><h2><i class="fa fa-user fa-fw"></i> My Info</h2></header>
		<div class="inner">
			<ul>
				<?php foreach( $items as $key => $value ) { ?>
				<li><?php echo $key; ?>: <?php echo $value; ?></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	
<?php }