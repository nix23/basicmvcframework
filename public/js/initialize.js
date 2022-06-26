$(document).ready(function(){ 
	$.ajaxSetup({
		type: "POST"
	});
	
	// Form styling
	$('input[type="text"],input[type="password"],select,textarea').addClass("input-border");
	
	$('input[type="text"],input[type="password"],textarea').focus(function() {
		$(this).addClass("input-focus");
	});
	
	$('input[type="text"],input[type="password"],textarea').blur(function() { 
		$(this).removeClass("input-focus");
	});
	
	$('input[type="text"],input[type="password"],select,textarea').bind('mouseover', function(event) {
		$(this).addClass("input-over");
	});
	
	$('input[type="text"],input[type="password"],select,textarea').bind('mouseout', function(event) {
		$(this).removeClass("input-over");
	});
	
	// Show confirmation message if it was set
	if(php_vars.modal_show_confirmation)
	{
		modal_loading.show_confirmation_and_hide();
	}
	
	// Window resize logic
	$(window).resize(function()
	{
		// Update overlay height
		if(overlay.is_active)
		{
			overlay.update_position();
		}
		
		// Update gallery top
		if(gallery.is_active)
		{
			gallery.update_position();
		}
		
		// Updating login form top
		if(form_tools.login.is_active)
		{
			form_tools.login.update_position();
		}
		
		// Updating registration form top
		if(form_tools.registration.is_active)
		{
			form_tools.registration.update_position();
		}
		
		// Updating modal errors top
		if(form_tools.modal_errors.is_active)
		{
			form_tools.modal_errors.update_position();
		}
		
		// Updating default errors top
		if(form_tools.default_errors.is_active)
		{
			form_tools.default_errors.update_position();
		}
		
		// Updating newcomment top
		if(form_tools.newcomment.is_active)
		{
			form_tools.newcomment.update_position();
		}
		
		// Updating followed users top
		if(form_tools.followed_users.is_active)
		{
			form_tools.followed_users.update_position();
		}

		// Updating delete form top
		if(form_tools.confirmation_prompt.is_active)
		{
			form_tools.confirmation_prompt.update_position();
		}
		
		// Updating description form top
		if(form_tools.description.is_active)
		{
			form_tools.description.update_position();
		}

		if(form_tools.modal_helper.is_active)
		{
			form_tools.modal_helper.update_position();
		}
	});
	
	// Window scroll logic
	$(window).scroll(function()
	{
		// Update gallery top
		if(gallery.is_active)
		{
			gallery.update_position();
		}
		
		// Update modal loading
		if(modal_loading.is_active)
		{
			modal_loading.update_position();
		}
		
		// Updating login form top
		if(form_tools.login.is_active)
		{
			form_tools.login.update_position();
		}
		
		// Updating registration form top
		if(form_tools.registration.is_active)
		{
			form_tools.registration.update_position();
		}
		
		// Updating modal errors top
		if(form_tools.modal_errors.is_active)
		{
			form_tools.modal_errors.update_position();
		}
		
		// Updating default errors top
		if(form_tools.default_errors.is_active)
		{
			form_tools.default_errors.update_position();
		}
		
		// Updating newcomment top
		if(form_tools.newcomment.is_active)
		{
			form_tools.newcomment.update_position();
		}
		
		// Updating followed users top
		if(form_tools.followed_users.is_active)
		{
			form_tools.followed_users.update_position();
		}

		// Updating delete form top
		if(form_tools.confirmation_prompt.is_active)
		{
			form_tools.confirmation_prompt.update_position();
		}
		
		// Updating description form top
		if(form_tools.description.is_active)
		{
			form_tools.description.update_position();
		}

		if(form_tools.modal_helper.is_active)
		{
			form_tools.modal_helper.update_position();
		}
	});
	
	// Header trimmer logic
	html_tools.trimifier.run("trim-divs");
	
	// Header add-list logic
	if($("#header-add-list").length > 0)
	{
		// Stopping add list menu mouseout from bubling
		$("#header-add-list").mouseleave(function(event){
			form_tools.header.hide_add_list();
		});
		
		// 'A' over
		$("#header-add-list").find("a").mouseover(function(){
			form_tools.header.add_list_over(this);
		});
		
		// 'A' out
		$("#header-add-list").find("a").mouseout(function(){
			form_tools.header.add_list_out(this);
		});
	}

	feedBuilder.initialize();
	social.facebook.init();
});