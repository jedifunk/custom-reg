jQuery(document).ready(function ($) {

	// Perform AJAX login/register on form submit
	$('form#login, form#vaForm').on('submit', function (e) {
        if (!$(this).valid()) return false;
        $('div.status', this).show().text(ajax_auth_object.loadingmessage);
		username = 	$('form#login #username').val();
		password = $('form#login #password').val();
		email = '';
		security = $('form#login #security').val();
		
		if ($(this).attr('id') == 'vaForm') {
			action = 'ajaxregister';
			username = $('form#vaForm #username').val();
        	email = $('form#vaForm #username').val();
        	firstName = $('form#vaForm #user_first').val();
        	lastName = $('form#vaForm #user_last').val();
        	phone = $('form#vaForm #phone').val();
        	security = $('form#vaForm #submitsecurity').val();	
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
					$('div.status', ctrl).text(data.message);
					if (data.loggedin == true) {
	                    document.location.href = ajax_auth_object.redirecturl;
	                }
	            }
	        });
	        
        } else if ($(this).attr('id') == 'vaForm') {
	        
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
					$('div.status', ctrl).text(data.message);
					if (data.loggedin == true) {
	                    document.location.href = ajax_auth_object.redirecturl;
	                }
	                $('#vaForm').leadify({ vaId: ajax_auth_object.vaID });
	            }
	        });
        }
        e.preventDefault();
    });
	
	// Client side form validation
    if (jQuery("#vaForm").length) 
		jQuery("#vaForm").validate();
    else if (jQuery("#login").length) 
		jQuery("#login").validate();
});