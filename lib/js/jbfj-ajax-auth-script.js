jQuery(document).ready(function ($) {

	// Perform AJAX login/register on form submit
	$('form#login, form#register').on('submit', function (e) {
        if (!$(this).valid()) return false;
        $('p.status', this).show().text(ajax_auth_object.loadingmessage);
		username = 	$('form#login #username').val();
		password = $('form#login #password').val();
		email = '';
		security = $('form#login #security').val();
		
		if ($(this).attr('id') == 'register') {
			action = 'ajaxregister';
			username = $('form#register #username').val();
        	email = $('form#register #user_email').val();
        	firstName = $('form#register #user_first').val();
        	lastName = $('form#register #user_last').val();
        	phone = $('form#register #phone').val();
        	security = $('form#register #submitsecurity').val();	
		}  
		ctrl = $(this);
		
		if ($(this).attr('id') == 'login') {
			
			$.ajax({
	            type: 'POST',
	            dataType: 'json',
	            url: ajax_auth_object.ajaxurl,
	            data: {
	                'action': 'ajaxlogin',
	                'username': username,
	                'password': password,
	                'security': security
	            },
	            success: function (data) {
					$('p.status', ctrl).text(data.message);
					if (data.loggedin == true) {
	                    document.location.href = ajax_auth_object.redirecturl;
	                }
	            }
	        });
	        
        } else if ($(this).attr('id') == 'register') {
	        
			$.ajax({
	            type: 'POST',
	            dataType: 'json',
	            url: ajax_auth_object.ajaxurl,
	            data: {
	                'action': 'ajaxregister',
	                'username': username,
	                'email' : email,
	                'user_first' : firstName,
	                'user_last' : lastName,
	                'phone' : phone,
	                'security' : security
	            },
	            success: function (data) {
					$('p.status', ctrl).text(data.message);
					console.log(data);
					if (data.loggedin == true) {
	                    document.location.href = ajax_auth_object.redirecturl;
	                }
	            }
	        });
        }
        e.preventDefault();
    });
	
	// Client side form validation
    if (jQuery("#register").length) 
		jQuery("#register").validate();
    else if (jQuery("#login").length) 
		jQuery("#login").validate();
});