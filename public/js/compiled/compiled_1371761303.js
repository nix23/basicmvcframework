debug = {
	rows:        new Array(),
	current_row: null,
	initialized: false,
	
	show: function()
	{
		this.rows.push(this.current_row);
		
		for(i = 0; i < this.rows.length; i++)
		{
			var row_html = "";
			var row      = this.rows[i];
			
			row_html += "<div class='row'>";
			row_html += "	<div class='content'>";
			
			for(key in this.rows[i])
			{ 
				row_html += "<div>";
				row_html += key + ": " + row[key];
				row_html += "</div>";
			}
			
			row_html += "	</div>";
			row_html += "</div>";
			
			$("#debugger-content").html($("#debugger-content").html() + row_html); 
		}
		
		$("#debugger").css("display", "block");
	},
	
	hide: function()
	{
		$("#debugger-content").html("");
		$("#debugger").css("display", "none");
	},
	
	new_row: function()
	{
		if(this.initialized)
		{
			this.rows.push(this.current_row);
		}
		
		this.current_row = new Object(); 
		this.initialized = true;
	},
	
	add: function(key, value)
	{
		this.current_row[key] = value;
	}
}

effects = {
	// Spinner Effect
	spinner: {
		animation_speed:    20,
		animation_slices:   10,
		spinner:            null,
		wrapper:            null,
		current_width:      null,
		current_height:     null,
		target_width:       null,
		target_height:      null,
		width_delta:        null,
		height_delta:       null,
		spinner_start_left: null,
		spinner_start_top:  null,
		wrapper_start_left: null,
		wrapper_start_top:  null,
		// Used in shrink method to calculate
		// left and top offset
		spinner_start_width:  null,
		spinner_start_height: null,
		call_callback:        false,
		callback_type:        "",
		
		callback: function()
		{
			switch(this.callback_type)
			{
				case "hide_login_wrapper":
					form_tools.login.hide_wrapper();
				break;
				
				case "hide_registration_wrapper":
					form_tools.registration.hide_wrapper();
				break;
				
				case "hide_newcomment_wrapper":
					form_tools.newcomment.hide_wrapper();
				break;
				
				case "hide_followed_users_wrapper":
					form_tools.followed_users.hide_wrapper();
				break;
				
				case "hide_description_wrapper":
					form_tools.description.hide_wrapper();
				break;

				case "hide_modal_helper_wrapper":
					form_tools.modal_helper.hide_wrapper();
				break;
			}
			
			this.call_callback = false;
			this.callback_type = "";
		},
		
		grow: function()
		{
			var last_animation_frame = false;
			
			this.current_width  += this.width_delta;
			this.current_height += this.height_delta;
			
			// Checks,if this is last animation step
			if((this.current_width >= this.target_width) || (this.current_height >= this.target_height))
			{
				last_animation_frame = true;
				
				this.current_width  = this.target_width;
				this.current_height = this.target_height;
			}
			
			// Calculating spinner offsets from original left and top values
			var spinner_current_left_offset = Math.round(this.current_width / 2);
			var spinner_current_top_offset  = Math.round(this.current_height / 2);
			
			// Moving left and top by half of width and height values
			$(this.spinner).css("left", this.spinner_start_left - spinner_current_left_offset + "px");
			$(this.spinner).css("top",  this.spinner_start_top  - spinner_current_top_offset + "px");
			
			$(this.spinner).css("width",  Math.round(this.current_width) + "px");
			$(this.spinner).css("height", Math.round(this.current_height) + "px");
			
			// Moving spinner inside wrapper in opposite direction.
			// It will keeps them on place.(Otherwise it will move with parent div)
			$(this.wrapper).css("left", this.wrapper_start_left + spinner_current_left_offset + "px");
			$(this.wrapper).css("top",  this.wrapper_start_top  + spinner_current_top_offset + "px");
			
			// Running next animation iteration
			if(!last_animation_frame)
			{
				var self = this;
				window.setTimeout(function() { self.grow(); }, this.animation_speed);
			}
			// Or calling callbacks if neccesary
			else
			{
				// Animation finished.
				// Callbacks can be called here
				if(this.call_callback)
				{
					this.callback();
				}
			}
		},
		
		shrink: function()
		{
			var last_animation_frame = false;
			
			this.current_width  -= this.width_delta;
			this.current_height -= this.height_delta;
			
			// Checks,if this is last animation step
			if((this.current_width <= this.target_width) || (this.current_height <= this.target_height))
			{
				last_animation_frame = true;
				
				this.current_width  = this.target_width;
				this.current_height = this.target_height;
			}
			
			// Calculating spinner offsets from original left and top values
			var spinner_current_left_offset = Math.round((this.spinner_start_width - this.current_width) / 2);
			var spinner_current_top_offset  = Math.round((this.spinner_start_height - this.current_height) / 2);
			
			// Moving left and top by half of distance of spinner start and current top and left values
			$(this.spinner).css("left", this.spinner_start_left + spinner_current_left_offset + "px");
			$(this.spinner).css("top",  this.spinner_start_top  + spinner_current_top_offset + "px");
			
			$(this.spinner).css("width",  Math.round(this.current_width) + "px");
			$(this.spinner).css("height", Math.round(this.current_height) + "px");
			
			// Moving spinner inside wrapper in opposite direction.
			// It will keeps them on place.(Otherwise it will move with parent div)
			$(this.wrapper).css("left", this.wrapper_start_left - spinner_current_left_offset + "px");
			$(this.wrapper).css("top",  this.wrapper_start_top  - spinner_current_top_offset + "px");
			
			// Running next animation iteration
			if(!last_animation_frame)
			{
				var self = this;
				window.setTimeout(function() { self.shrink(); }, this.animation_speed);
			}
			// Or calling callbacks if neccesary
			else
			{
				// Animation finished.
				$(this.spinner).css("visibility", "hidden");
				// Callbacks can be called here
				if(this.call_callback)
				{
					this.callback();
				}
			}
		},
		
		init_spinner_and_wrapper: function()
		{
			this.spinner_start_left = parseInt($(this.spinner).css("left")); 
			this.spinner_start_top  = parseInt($(this.spinner).css("top"));
			
			this.wrapper_start_left = parseInt($(this.wrapper).css("left"));
			this.wrapper_start_top  = parseInt($(this.wrapper).css("top")); 
		},
		
		update_spinner_top: function(update_spinner_top_div_id,
											  spinner_wrapper_div)
		{
			var spinner_top_offset  = $("#" + update_spinner_top_div_id).position().top;
			spinner_top_offset     += Math.round(parseInt($(spinner_wrapper_div).css("height")) / 2);
			
			$(this.spinner).css("top", spinner_top_offset + "px");
		},
		
		toggle: function( spinner_id, 
								update_spinner_top,
								update_spinner_top_div_id, 
								spinner_wrapper_div)
		{
			this.spinner = $("#" + spinner_id);
			this.wrapper = $(this.spinner).children(".spinner-wrapper")
			
			// Initialize grow
			if($(this.spinner).css("visibility") == "hidden")
			{
				if(update_spinner_top)
				{
					this.update_spinner_top(update_spinner_top_div_id,
													spinner_wrapper_div);
				}
				
				this.init_spinner_and_wrapper();
				
				this.current_width  = 0;
				this.current_height = 0;
				
				this.target_width  = parseInt($(this.wrapper).css("width")); 
				this.target_height = parseInt($(this.wrapper).css("height"));
				
				// How many pixels we will add on each animation step
				this.width_delta  = this.target_width / this.animation_slices;
				this.height_delta = this.target_height / this.animation_slices;
				
				$(this.spinner).css("visibility", "visible");
				
				this.grow();
			}
			// Initialize shrink
			else
			{
				this.init_spinner_and_wrapper();
				
				this.spinner_start_width  = parseInt($(this.spinner).css("width"));
				this.spinner_start_height = parseInt($(this.spinner).css("height"));
				
				this.current_width  = parseInt($(this.wrapper).css("width"));
				this.current_height = parseInt($(this.wrapper).css("height"));
				
				this.target_width  = 0;
				this.target_height = 0;
				
				// How many pixels we will substract on each animation step
				this.width_delta  = this.current_width / this.animation_slices;
				this.height_delta = this.current_height / this.animation_slices;
				
				this.shrink();
			}
		}
	}
	// Spinner Effect END
}

library = {
	// TO DO: Extend to copy time and runs count,
	// TO DO: Extend to save results in file(ajax)
	profiler: {
		time_count: null,
		
		start: function()
		{
			this.time_count = new Date().getTime();
		},
		
		stop: function()
		{
			alert(new Date().getTime() - this.time_count + " msec");
		}
	},
	
	is_object: function(object)
	{
		 return (typeof(object) != 'object') ? false : true;
	},
	
	iterate_object: function(object)
	{
		var message = "";
		
		for(property in object)
		{
			if(this.is_object(object[property]))
			{
				message += this.iterate_object(object[property]);
			}
			else
			{
				message += property + ": " + object[property] + "\n";
			}
		}
		
		return message;
	},
	
	print_object: function(object)
	{
		var message = "";
		
		message += this.iterate_object(object);
		
		alert(message);
	},
	
	not_empty: function(string_to_check)
	{
		if(string_to_check.length() > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	},
	
	is_empty: function(string_to_check)
	{
		if(string_to_check.length() == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	},
	
	ucfirst: function(string)
	{
		 return string.charAt(0).toUpperCase() + string.substr(1);
	}
}

ajax = {
	// Contains all responses,which should be
	// called after Ajax calls. (php -> Ajax class -> callback).
	// You can write response logic right inside this methods, or
	// call some class method,if function logic belongs to it.
	callbacks: {
		data: {},
		html_object: {},
		
		// Shared callbacks
		refresh: function()
		{
			location.reload(true);
		},
		
		redirect: function()
		{
			var url = php_vars.base_url + this.data.url_segments;
			window.location.href = url;
		},
		
		update_caption: function()
		{
			var new_caption = library.ucfirst(this.data.new_caption);
			$(this.html_object).html(new_caption);
		},
		
		// Inserts uploaded photo into batch photo block
		insert_uploaded_photo: function()
		{
			form_tools.photo.insert(this.data.target_div_id,
											this.data.master_photo_name,
											this.data.photo_extension,
											this.data.spinner_id);
		},
		
		// Inserts uploaded photo into single photo block
		insert_uploaded_single_photo: function()
		{
			form_tools.single_photo.insert(this.data.target_div_id,
													 this.data.master_photo_name,
													 this.data.photo_extension,
													 this.data.spinner_id);
		},
		
		// Registration form callbacks
		show_activation_sent: function()
		{
			form_tools.registration.show_activation_sent(this.data.email);
		},
		
		// View item callbacks
		add_like: function()
		{
			html_tools.module_item.add_like(this.html_object);
		},
		
		change_panel_list_item: function()
		{
			html_tools.module_item.change_panel_list_item(this.html_object,
																	  	 this.data.new_caption,
																	  	 this.data.count_action);
		},
		
		// Comments callbacks
		refresh_comments: function()
		{
			form_tools.comments.refresh(this.data.comment_to_scroll_id,
												 this.data.comments_count,
												 this.data.current_page,
												 this.data.comments_items_html,
												 this.data.comments_pagination_html,
												 this.data.callback);
		},

		refresh_comments_after_delete: function()
		{
			form_tools.comments.refresh_after_delete(this.data.comments_count,
																  this.data.current_page,
																  this.data.comments_items_html,
																  this.data.comments_pagination_html);
		},

		delete_answer: function()
		{
			form_tools.comments.delete_answer(this.data.comments_count,
														 this.html_object);
		},
		
		// Follow callbacks
		update_followed_users_form: function()
		{
			form_tools.followed_users.update_data(this.data.followed_users_html,
															  this.data.followed_users_count);
			html_tools.trimifier.run("trim-divs");
			form_tools.followed_users.show();
		},
		
		update_followed_users_form_status: function()
		{
			form_tools.followed_users.update_status(this.html_object,
																 this.data.new_follow_status);
		},

		// Favorites callbacks
		update_html_after_item_unfavorite: function()
		{
			html_tools.favorites_list.update_html_after_item_unfavorite(this.data.items_html,
																							this.data.pagination_html);
		},

		// MyDrive callbacks
		update_mydrive_html_after_item_delete: function()
		{
			var current_count = parseInt($("#ajax-update-items-count").html());
			var new_count     = --current_count;

			$("#ajax-update-items-count").html(new_count);
			$(".ajax-update-pagination").html(this.data.pagination_html);
			$("#ajax-update-items").html(this.data.items_html);
			$("#ajax-update-rating-stats").html(this.data.rating_stats_html);

			html_tools.trimifier.run("trim-divs");
		},

		update_mydrive_status: function()
		{
			var html = "";

			if(this.data.new_status == "disabled")
				html = "<div class='status-disabled-icon'></div>";
			else
				html = "<div class='status-enabled-icon'></div>";

			$(this.html_object).html(html);
		},

		// Profile callbacks
		update_profile_follow_status: function()
		{
			$(this.html_object).find(".name").html(this.data.new_caption);

			var count_action  = this.data.count_action;
			var count_div     = $("#ajax-update-followers-count");
			var current_count = parseInt($(count_div).html());

			if(count_action == "increase")
				var new_count = ++current_count;
			else
				var new_count = --current_count;

			$(count_div).html(new_count);
		},
		
		// Module forms callback
		parse_select_subcategories: function()
		{
			form_tools.category_select.parse_loaded_subcategories(this.data.subcategories);
		},

		init_registration: function()
		{
			var html = "<input type='hidden' id='auth_key' name='auth_key[key]' value='" + this.data.auth_key + "'>";
			$("form[name=register-form]").append(html);
		},

		init_login: function()
		{
			var html = "<input type='hidden' id='auth_key' name='auth_key[key]' value='" + this.data.auth_key + "'>";
			$("form[name=login-form]").append(html);
		},

		init_comment: function()
		{
			var html = "<input type='hidden' id='auth_key' name='auth_key[key]' value='" + this.data.auth_key + "'>";
			$("form[name=newcomment-form]").append(html);
		}
	},
	
	parse_server_answer: function(server_answer,
											error_stream)
	{
		var result   = server_answer.result;
		var errors   = server_answer.errors;
		var callback = server_answer.callback;
		var data     = server_answer.data; 
		
		if(result == "ok")
		{
			if(callback != "none")
			{
				this.callbacks.data = data; 
				this.callbacks[callback]();
			} 
		}
		else
		{
			form_tools.errors.render(errors,
											 error_stream); 
		}
	},
	
	enable_loading: function(loading_type)
	{
		switch(loading_type)
		{
			case "modal":
			case "modal_no_confirmation":
			case "modal_update_overlay":
				modal_loading.show_loading();
			break;
			
			case "modal_form":
				$("#modal-form-loading").css("display", "block");
				document.getElementById("modal-form-submit").disabled = true;
			break;
			
			case "second_modal_form":
				$("#second-modal-form-loading").css("display", "block");
				document.getElementById("second-modal-form-submit").disabled = true;
			break;
			
			case "third_modal_form":
				$("#third-modal-form-loading").css("display", "block");
				document.getElementById("third-modal-form-submit").disabled = true;
			break;
			
			case "form":
				overlay.show("overlay-transparent-background");
				$("#form-loading").css('display', 'block');
				document.getElementById("form-submit").disabled = true;
			break;
			
			case "load_new_comments":
				$('#refresh-comments-active-icon').css('display', 'block');
			break;
			
			case "logout":
				modal_loading.show_loading();
			break;
			
			case "followed_users":
				$("#followed-users-form-loading").css("display", "block");
			break;
		}
	},
	
	disable_loading: function(loading_type)
	{
		switch(loading_type)
		{
			case "modal":
				modal_loading.show_confirmation_and_hide();
			break;
			
			case "modal_form":
				$("#modal-form-loading").css("display", "none");
				document.getElementById("modal-form-submit").disabled = false;
			break;
			
			case "second_modal_form":
				$("#second-modal-form-loading").css("display", "none");
				document.getElementById("second-modal-form-submit").disabled = false;
			break;
			
			case "third_modal_form":
				$("#third-modal-form-loading").css("display", "none");
				document.getElementById("third-modal-form-submit").disabled = false;
			break;
			
			case "modal_no_confirmation":
				modal_loading.hide_fast();
			break;
			
			case "modal_update_overlay":
				modal_loading.hide_instantly();
			break;
			
			case "form":
				// Hidding overlay,if it is transparent
				// (Else we want keep it to render default errors)
				if(overlay.overlay_bg_class != "overlay-light-background")
					overlay.hide();
				
				$("#form-loading").css('display', 'none'); 
				document.getElementById("form-submit").disabled = false;
			break;
		
			case "load_new_comments":
				$('#refresh-comments-active-icon').css('display', 'none');
			break;
		
			case "logout":
				; // Don't disable
			break;
			
			case "followed_users":
				$("#followed-users-form-loading").css("display", "none");
			break;
		}
	},
	
	process: function(controller,
							action,
							parametrs,
							html_object,
							loading_type,
							error_stream)
	{
		var loading_type = loading_type || "modal";
		this.enable_loading(loading_type);
		
		var error_stream = error_stream || "default";
		
		if(library.is_object(html_object))
		{
			this.callbacks.html_object = html_object;
		}
		
		var url_segments = controller + "/" + action + "/" + parametrs;
		
		$.ajax({
			url: php_vars.base_url + url_segments,
			
			datatype: "json",
			
			context: this,
			
			success: function (data, textStatus)
			{
				try
				{
					var server_answer = jQuery.parseJSON(data);
				}
				catch(e)
				{
					alert(data);
				}
				
				if(library.is_object(server_answer))
				{
					this.parse_server_answer(server_answer,
													 error_stream);
				}
				
				this.disable_loading(loading_type);
			}
		}); 
	},
	
	process_form: function(form_name,
								  controller,
								  action,
								  parametrs,
								  html_object,
								  loading_type,
								  error_stream)
	{
		var loading_type = loading_type || "form";
		this.enable_loading(loading_type);
		
		var error_stream = error_stream || "default";
		
		if(library.is_object(html_object))
		{
			this.callbacks.html_object = html_object;
		}
		
		var action       = action || "save";
		var form_data    = $("form[name=" + form_name + "]").serialize();
		var url_segments = controller + "/" + action + "/" + parametrs;
		
		$.ajax({
			url: php_vars.base_url + url_segments,
			
			data: form_data,
			
			datatype: "json",
			
			context: this,
			
			success: function (data, textStatus)
			{
				try
				{
					var server_answer = jQuery.parseJSON(data);
				}
				catch(e)
				{
					alert(data);
				}
				
				if(library.is_object(server_answer))
				{
					this.parse_server_answer(server_answer,
													 error_stream);
				}
				
				this.disable_loading(loading_type);
			}
		});
	}
}

// Extends functionality of base ajax class
// to provide ajax file uploads.
ajax_file_uploader = {
	root_div:          "",
	target_div_id:     "",
	
	// Binds callback to iframe(only on first upload)
	attach_callback_event: function()
	{
		var context = this;
		
		$('#ajax-iframe').load(function() {
			context.after_upload();
		});
	},
	
	// Fires after file upload is complete
	after_upload: function()
	{ 
		var server_answer = $('#ajax-iframe').contents().find('body').html();
		
		try
		{
			var server_answer = jQuery.parseJSON(server_answer);
		}
		catch(e)
		{
			alert(server_answer);
		}
		
		if(library.is_object(server_answer))
		{
			// Adding frame to server answer with target div
			server_answer.data.target_div_id = this.target_div_id;
			ajax.parse_server_answer(server_answer,
											 "default");
		}
		
		this.delete_iframe();
		this.disable_loading();
	},
	
	// Creates iframe,which will catch server answer
	create_iframe: function()
	{
		var html  = "<iframe";
		html     += " id='ajax-iframe'";
		html     += " name='ajax-iframe'";
		html     += " class='ajax-iframe'";
		html     += "></iframe>";
		
		$("body").append(html);
	},
	
	// Removes iframe from body
	delete_iframe: function()
	{
		$("#ajax-iframe").remove();
	},
	
	enable_loading: function()
	{
		$(this.root_div).find(".icon").css("display", "block");
		$(this.root_div).find(".button").attr("disabled", "disabled");
	},
	
	disable_loading: function()
	{
		$(this.root_div).find(".icon").css("display", "none");
		$(this.root_div).find(".button").removeAttr("disabled");
	},
	
	// Calls upload script via iframe submit
	upload: function(root_div_id, target_div_id, controller, action)
	{
		this.root_div = $("#" + root_div_id);
		
		this.enable_loading();
		this.create_iframe();
		
		// Div,which will contain file frames
		this.target_div_id = target_div_id;
		
		// Binding load event to iframe
		this.attach_callback_event();
		
		// Generating file-upload script url
		var url = php_vars.base_url + controller + "/" + action + "/ajax";
		
		// Submitting form via iframe
		$(this.root_div).find("form").attr("action", url);
		$(this.root_div).find("form").submit();
	}
}

overlay = {
	overlay_id:       "overlay",
	overlay_bg_class: "",
	active:           false,
	hide_on_click:    false,
	before_hide:      "",
	
	attach_hide_event: function()
	{
		$("#" + this.overlay_id).on("click", this.force_hide);
	},
	
	detach_hide_event: function()
	{
		$("#" + this.overlay_id).off("click"); 
	},
	
	force_hide: function()
	{
		switch(overlay.before_hide)
		{
			case "close_gallery":
				gallery.unload();
			break;
			
			case "close_login":
				form_tools.login.hide();
			break;
			
			case "close_registration":
				form_tools.registration.hide();
			break;
			
			case "close_default_errors":
				form_tools.default_errors.hide();
			break;
			
			case "close_newcomment":
				form_tools.newcomment.hide();
			break;
			
			case "close_followed_users":
				form_tools.followed_users.hide();
			break;

			case "close_confirmation_prompt":
				form_tools.confirmation_prompt.hide();
			break;
			
			case "close_description":
				form_tools.description.hide();
			break;

			case "close_modal_helper":
				form_tools.modal_helper.hide();
			break;
		}
		
		overlay.hide();
	},
	
	is_active: function()
	{
		return this.active;
	},
	
	set_background: function()
	{
		$("#" + this.overlay_id).addClass(this.overlay_bg_class);
	},
	
	update_position: function()
	{
		$("#" + this.overlay_id).css("height", $(document).height() + "px");
	},
	
	show: function(bg_type)
	{
		// Hidding all flash videos
		$(".video").find(".player").css("visibility", "hidden");
		$(".video").find(".pause").css("display", "block");
		
		// Removing background classes
		if($("#" + this.overlay_id).hasClass("overlay-transparent-background"))
			$("#" + this.overlay_id).removeClass("overlay-transparent-background");
		
		if($("#" + this.overlay_id).hasClass("overlay-light-background"))
			$("#" + this.overlay_id).removeClass("overlay-light-background");
		
		// Setting background class
		if(bg_type == "overlay-transparent-background")
			this.overlay_bg_class = "overlay-transparent-background";
		else
			this.overlay_bg_class = "overlay-light-background";
		
		if(this.hide_on_click)
		{
			this.attach_hide_event();
		}
		
		this.set_background();
		this.update_position();
		
		$("#" + this.overlay_id).css("display", "block");
		this.active = true;
	},
	
	hide: function()
	{
		if(this.hide_on_click)
		{
			this.detach_hide_event();
			
			this.hide_on_click = false;
			this.before_hide   = "";
		}
		
		$("#" + this.overlay_id).css("display", "none");
		$("#" + this.overlay_id).removeClass(this.overlay_bg_class);
		
		// Restoring all flash videos
		$(".video").find(".player").css("visibility", "visible");
		$(".video").find(".pause").css("display", "none");
		
		this.active = false; 
	}
}

modal_loading = {
	loading_id: "overlay-loading",
	is_active:  false,
	
	set_message: function(message)
	{
		$("#" + this.loading_id + " .message").html(message);
	},
	
	set_icon: function(icon_type)
	{
		switch(icon_type)
		{
			case "loading":
				$("#" + this.loading_id).find(".icon").addClass("loading-bg");
				$("#" + this.loading_id).find(".icon").removeClass("confirmation-bg");
			break;
			
			case "confirmation":
				$("#" + this.loading_id).find(".icon").removeClass("loading-bg");
				$("#" + this.loading_id).find(".icon").addClass("confirmation-bg");
			break;
		}
	},
	
	is_timeout_set: function()
	{
		if(typeof this.timeout_id == "number")
		{
			return true;
		}
		else
		{
			return false;
		}
	},
	
	set_timeout: function()
	{
		var self = this; 
		this.timeout_id = window.setTimeout(function() { self.hide(); }, 1400); 
	},
	
	clear_timeout: function()
	{
		window.clearTimeout(this.timeout_id);
		delete this.timeout_id;
	},
	
	update_position: function()
	{
		if($(window).scrollTop() <= 120) 
		{
			var scroll_offset = 120;
		}
		else
		{
			var scroll_offset = $(window).scrollTop();
		}
		var content_offset_top = 10;
		
		var top = scroll_offset + content_offset_top;
		$("#" + this.loading_id).css("top", top + "px");
	},
	
	show_loading: function()
	{
		this.is_active = true;
		
		$("#" + this.loading_id).stop();
		$("#" + this.loading_id).css({ opacity: 1.0 });
		
		if(this.is_timeout_set())
		{
			this.clear_timeout();
		}
		
		this.update_position();
		
		overlay.show("overlay-transparent-background");
		
		html  = "";
		html += "<div class='heading'>";
		html += "	Loading..."
		html += "</div>";
		html += "<div class='subheading'>";
		html += "	Please wait a moment.";
		html += "</div>";
		
		this.set_message(html);
		this.set_icon("loading");
		
		$("#" + this.loading_id).css("display", "block");
	},
	
	show_confirmation_and_hide: function()
	{
		this.is_active = true;
		this.update_position();
		
		html  = "";
		html += "<div class='heading'>";
		html += "	Saved!"
		html += "</div>";
		html += "<div class='subheading'>";
		html += "	Your changes have been";
		html += "</div>";
		html += "<div class='subheading'>";
		html += "	succesfully saved.";
		html += "</div>";
		
		this.set_message(html);
		this.set_icon("confirmation");
		
		$("#" + this.loading_id).css("display", "block");
		
		if(overlay.is_active)
		{
			overlay.hide();
		}
		
		if(this.is_timeout_set())
		{
			this.clear_timeout();
		}
		this.set_timeout();
		
		$("#" + this.loading_id).stop();
		$("#" + this.loading_id).css({ opacity: 1.0 });
		$("#" + this.loading_id).css("display", "block");
	},
	
	hide: function()
	{
		this.is_active = false;
		this.clear_timeout();
		
		$("#" + this.loading_id).fadeOut(500);
	},
	
	hide_fast: function()
	{
		this.is_active = false;
		
		if(overlay.is_active)
		{
			overlay.hide();
		}
		
		$("#" + this.loading_id).fadeOut(500);
	},
	
	// Used when we are showing light-background in ajax requests.
	// (Don't hide overlay,but reload it)
	hide_instantly: function()
	{
		this.is_active = false;
		$("#" + this.loading_id).css("display", "none");
	}
};

gallery = {
	gallery:                      null,
	module:                       "",
	photo_ids:                    new Array(),
	master_photo_names:           new Array(),
	upload_directories:           new Array(),
	packed_resolutions:           new Array(),
	current_number:               null,
	total_number:                 null,
	active:                       false,
	clicked_gallery_photo_number: null,
	id_to_collect:                null,
	class_to_collect:             null,
	collect_resolutions:          false,
	
	is_active: function()
	{
		return this.active;
	},
	
	update_position: function()
	{
		var viewport_offset = ($(window).height() - parseInt($("#gallery").css("height"))) / 2;
		var scroll_offset   = $(window).scrollTop();
		
		$("#gallery").css("top", (viewport_offset + scroll_offset) + "px");
	},
	
	attach_events: function()
	{
		$(window).on("keydown", this.process_keypress);
		
		$(this.gallery).find(".previous").on("click",     this.previous);
		$(this.gallery).find(".previous").on("mouseover", this.show_previous_button);
		$(this.gallery).find(".previous").on("mouseout",  this.hide_previous_button);
		
		$(this.gallery).find(".next").on("click",     this.next);
		$(this.gallery).find(".next").on("mouseover", this.show_next_button);
		$(this.gallery).find(".next").on("mouseout",  this.hide_next_button);
		
		$(this.gallery).find(".close").on("click", this.close);
	},
	
	detach_events: function()
	{
		$(window).off("keydown");
		
		$(this.gallery).find(".previous").off("click");
		$(this.gallery).find(".previous").off("mouseover");
		$(this.gallery).find(".previous").off("mouseout");
		
		$(this.gallery).find(".next").off("click");
		$(this.gallery).find(".next").off("mouseover");
		$(this.gallery).find(".next").off("mouseout");
		
		$(this.gallery).find(".close").off("click");
	},
	
	process_keypress: function()
	{
		if(event.keyCode == 37)
		{
			gallery.previous();
		}
		else if(event.keyCode == 39)
		{
			gallery.next();
		}
	},
	
	show_next_button: function()
	{
		if(gallery.has_next())
		{
			var next_button = $(gallery.gallery).find(".next");
			$(next_button).addClass("show-next");
		}
	},
	
	hide_next_button: function()
	{
		if(gallery.has_next())
		{
			var next_button = $(gallery.gallery).find(".next");
			$(next_button).removeClass("show-next");
		}
	},
	
	show_previous_button: function()
	{
		if(gallery.has_previous())
		{
			var previous_button = $(gallery.gallery).find(".previous");
			$(previous_button).addClass("show-previous");
		}
	},
	
	hide_previous_button: function()
	{
		if(gallery.has_previous())
		{
			var previous_button = $(gallery.gallery).find(".previous");
			$(previous_button).removeClass("show-previous");
		}
	},
	
	has_next: function()
	{
		return (this.current_number == this.total_number) ? false : true;
	},
	
	has_previous: function()
	{
		return (this.current_number == 1) ? false : true;
	},
	
	next: function()
	{
		if(gallery.has_next())
		{
			gallery.current_number++;
			gallery.update_panel();
			gallery.load_image();
			
			if(!gallery.has_next())
			{
				var next_button = $(gallery.gallery).find(".next");
				$(next_button).removeClass("show-next");
			}
		}
	},
	
	previous: function()
	{
		if(gallery.has_previous())
		{
			gallery.current_number--;
			gallery.update_panel();
			gallery.load_image();
			
			if(!gallery.has_previous())
			{
				var previous_button = $(gallery.gallery).find(".previous");
				$(previous_button).removeClass("show-previous");
			}
		}
	},
	
	update_panel: function()
	{
		// Updating current and total photo number
		$(this.gallery).find(".count").html(this.current_number);
		$(this.gallery).find(".total").html(this.total_number);
		
		// Updating photo resolutions(if necessary)
		if(this.collect_resolutions)
		{
			var packed_resolutions = this.packed_resolutions[this.current_number - 1]; 
			var resolutions        = packed_resolutions.split("|");
			var context            = this;
			
			$(this.gallery).find(".resolutions").html("");
			
			$(resolutions).each(function(index, value)
			{
				var sizes  = value.split("-");
				var width  = sizes[0];
				var height = sizes[1];
				var html   = "";

				var path  = php_vars.base_url;
				path     += "services/viewphoto/";
				path     += context.module + "/";
				path     += context.photo_ids[context.current_number - 1] + "/";
				path     += width + "/" + height;

				html += "<a href='" + path + "' target='_blank'							   ";
				html += "	onmouseover='html_tools.module_item.wallpaper_over(this)'";
				html += "	onmouseout='html_tools.module_item.wallpaper_out(this)'  ";
				html += "   rel='nofollow'>                                          ";
				html += "		<div class='sizes'>												";
				html += "			<div class='width'>											";
				html += 					width;
				html += "			</div>															";
				html += "																				";
				html += "			<div class='height'>											";
				html += 					height;
				html += "			</div>															";
				html += "		</div>																";
				html += "</a>																			";
				
				$(context.gallery).find(".resolutions").append(html);
			});
		} 
	},
	
	// Loads current image
	load_image: function()
	{
		var path = "";
		
		path += php_vars.base_url;
		path += this.upload_directories[this.current_number - 1];
		path += this.master_photo_names[this.current_number - 1];
		path += "-800-520.jpg";
		
		$(this.gallery).find("#gallery-image").attr("src", path);
	},
	
	// Collects photos with associated data
	collect: function()
	{
		var context      = this;
		var photo_number = 0;
		
		$("#" + this.id_to_collect).find("." + this.class_to_collect).each(function()
		{
			photo_number++;

			// Pushing photo id
			var current_photo_id = $(this).attr("data-photo-id");
			context.photo_ids.push(current_photo_id);

			// Pushing master photo name
			var current_master_photo_name = $(this).attr("data-master-photo-name");
			context.master_photo_names.push(current_master_photo_name);

			// Checking if photo is clicked gallery photo
			var current_gallery_photo_number = $(this).attr("data-gallery-photo-number");

			if(current_gallery_photo_number == context.clicked_gallery_photo_number)
				context.current_number = current_gallery_photo_number;
			
			// Pushing upload directory name
			var current_upload_directory = $(this).attr("data-upload-directory");
			context.upload_directories.push("uploads/" + current_upload_directory + "/");
			
			// Pushing high resolution photos sizes
			if(context.collect_resolutions) 
				context.packed_resolutions.push($(this).attr("data-packed-resolutions")); 
		});
		
		this.total_number = photo_number;
	},
	
	// Loads gallery and displays clicked photo
	load: function(html_object, 
						id_to_collect, 
						class_to_collect)
	{
		this.active = true;
		
		// Detecting wallpaper collection mode
		if($("#" + id_to_collect).attr("data-collect-resolutions") == "yes") 
			this.collect_resolutions = true; 
		else
			this.collect_resolutions = false;
		
		var clicked_photo_div             = $(html_object).closest("." + class_to_collect);

		this.gallery                      = $("#gallery");
		this.clicked_gallery_photo_number = $(clicked_photo_div).attr("data-gallery-photo-number");
		this.id_to_collect                = id_to_collect;
		this.class_to_collect             = class_to_collect;
		
		// Setting item heading and subheading
		var heading    = $("#" + id_to_collect).attr("data-heading");
		var subheading = $("#" + id_to_collect).attr("data-subheading");
		$(this.gallery).find(".heading").html(heading);
		$(this.gallery).find(".subheading").html(subheading);

		this.module = $("#" + id_to_collect).attr("data-module");
		
		this.attach_events();
		this.collect(); 
		this.update_position();
		
		// Create image
		var html = "<img id='gallery-image' src=''>";
		$(this.gallery).find(".image").html(html);
		
		// Setting overlay 
		overlay.hide_on_click = true;
		overlay.before_hide   = "close_gallery";
		overlay.show();
		
		$(this.gallery).css("display", "block");
		
		this.update_panel();
		this.load_image();
	},
	
	// Unloads gallery
	unload: function()
	{
		this.detach_events();
		
		$(this.gallery).find(".image").html("");
		$(this.gallery).css("display", "none");
		
		this.master_photo_names.length = 0;
		this.upload_directories.length = 0;
		this.packed_resolutions.length = 0;
		this.photo_ids.length          = 0;
		
		this.active = false;
	},
	
	close: function()
	{
		overlay.hide();
		gallery.unload();
	}
}

form_tools = {
	// *** Confirmation prompt form
	confirmation_prompt: {
		is_active:    false,
		form_name:    "",
		controller:   "",
		action:       "",
		parametrs:    "",
		html_object:  null,
		loading_type: "",

		parse_mode: function(mode)
		{
			var prompt_message      = "";
			var process_button_text = "";
			var cancel_button_text  = "";

			switch(mode)
			{
				case "change_upload_status":
					prompt_message      = "Are you sure that you want change this item status?";
					process_button_text = "Yes";
					cancel_button_text  = "No";
				break;

				case "delete_upload":
					prompt_message      = "Are you sure that you want delete this item?";
					process_button_text = "Yes";
					cancel_button_text  = "No";
				break;

				case "unfavorite_item":
					prompt_message      = "Are you sure that you want unfavorite this item?";
					process_button_text = "Yes";
					cancel_button_text  = "No";
				break;

				case "delete_comment":
					prompt_message      = "Are you sure that you want delete this comment?";
					process_button_text = "Yes";
					cancel_button_text  = "No";
				break;
			}

			$("#confirmation-prompt").find(".wrapper").html(prompt_message);
			$("#confirmation-prompt").find(".bottom").find(".process").html(process_button_text);
			$("#confirmation-prompt").find(".bottom").find(".cancel").html(cancel_button_text);
		},

		show: function(mode,
							form_name,
							controller,
							action,
							parametrs,
							html_object,
							loading_type)
		{
			this.is_active = true;
			this.update_position();
			this.parse_mode(mode);

			// Saving ajax-call parametrs
			this.form_name    = form_name;
			this.controller   = controller;
			this.action       = action;
			this.parametrs    = parametrs;
			this.html_object  = html_object;
			this.loading_type = loading_type;

			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_confirmation_prompt";
			overlay.show();

			$("#confirmation-prompt").css("display", "block");
		},

		hide: function()
		{
			overlay.hide();
			this.is_active = false;
			$("#confirmation-prompt").css("display", "none");
		},

		process: function()
		{
			this.hide();
			ajax.process_form(this.form_name,
									this.controller,
									this.action,
									this.parametrs,
									this.html_object,
									this.loading_type);
		},

		cancel: function()
		{
			this.hide();
		},

		update_position: function()
		{
			var form_height     = parseInt($("#confirmation-prompt").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();

			$("#confirmation-prompt").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},

	// *** Header
	header: {
		show_add_list: function()
		{
			$("#header-add-list").css("display", "block");
		},
		
		hide_add_list: function()
		{
			$("#header-add-list").css("display", "none");
		},
		
		add_list_over: function(container)
		{
			$(container).find(".heading").css("color", "rgb(81,81,181)");
		},
		
		add_list_out: function(container)
		{
			$(container).find(".heading").css("color", "black");
		}
	},
	
	// *** New comment form
	newcomment: {
		is_active: false,

		init_auth: function()
		{
			ajax.process_form("newcomment-form",
			                  "account",
			                  "init_auth",
			                  "ajax/init_comment",
									null,
									"modal_update_overlay");
		},

		show: function(heading,
							subheading,
							answer_id)
		{
			this.init_auth();
			this.is_active = true;
			this.update_position();
			
			// Setting heading and subheading
			$("#newcomment-form").find(".label").html(heading);
			$("#newcomment-form").find(".sublabel").html(subheading);
			
			// Updating form values
			$("#newcomment-answer-id").val(answer_id);
			$("#newcomment-comment").val("");
			
			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_newcomment";
			overlay.show();
			
			$("#newcomment-form").css("display", "block");
			effects.spinner.toggle("newcomment-form-spinner", false);
		},
		
		hide: function()
		{
			overlay.hide();
			form_tools.modal_errors.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_newcomment_wrapper";
			effects.spinner.toggle("newcomment-form-spinner", false);
			$("#auth_key").remove();
		},
		
		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#newcomment-form").css("display", "none");
			this.is_active = false;
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#newcomment-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#newcomment-form").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},
	
	// *** Comments
	comments: {
		refresh: function(comment_to_scroll_id,
								comments_count,
								current_page,
								comments_items_html,
								comments_pagination_html,
								callback)
		{
			$(".ajax-comments-count").html(comments_count);
			$(".ajax-comments-pagination").html(comments_pagination_html);
			$(".ajax-comments-items").html(comments_items_html);
			$("#newcomment-current-page").val(current_page);
			
			var comment_to_scroll_class = ".comment-id-" + comment_to_scroll_id;
			$("html,body").animate({scrollTop: $(comment_to_scroll_class).offset().top - 200});
			
			if(callback == "hide_newcomment_form")
				form_tools.newcomment.hide();
		},

		refresh_after_delete: function(comments_count,
												 current_page,
												 comments_items_html,
												 comments_pagination_html)
		{
			$(".ajax-comments-count").html(comments_count);
			$(".ajax-comments-pagination").html(comments_pagination_html);
			$(".ajax-comments-items").html(comments_items_html);
			$("#newcomment-current-page").val(current_page);
		},

		delete_answer: function(comments_count,
										delete_answer_span)
		{
			$(".ajax-comments-count").html(comments_count);
			$(delete_answer_span).closest(".answer").remove();
		},
		
		load_new: function(item_id,
								 controller)
		{
			var parametrs = "ajax/" + item_id + "/1";
			ajax.process(controller,
							 'load_comments',
							 parametrs,
							 false,
							 'load_new_comments');
		}
	},
	
	// *** Login form
	login: {
		is_active: false,

		init_auth: function()
		{
			ajax.process_form("register-form",
			                  "account",
			                  "init_auth",
			                  "ajax/init_login",
									null,
									"modal_update_overlay");
		},

		show: function()
		{
			this.init_auth();
			this.is_active = true;
			this.update_position();
			
			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_login";
			overlay.show();
			
			$("#login-form").css("display", "block");
			effects.spinner.toggle("login-form-spinner", false);
		},
		
		hide: function()
		{
			overlay.hide();
			form_tools.modal_errors.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_login_wrapper";
			effects.spinner.toggle("login-form-spinner", false);
			$("#auth_key").remove();
		},
		
		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#login-form").css("display", "none");
			this.is_active = false;
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#login-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#login-form").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},

	// *** Modal helper form
	modal_helper: {
		is_active: false,

		show: function()
		{
			this.is_active = true;
			this.update_position();

			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_modal_helper";
			overlay.show();

			$("#modal-helper-form").css("display", "block");
			effects.spinner.toggle("modal-helper-form-spinner", false);
		},

		hide: function()
		{
			overlay.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_modal_helper_wrapper";
			effects.spinner.toggle("modal-helper-form-spinner", false);
		},

		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#modal-helper-form").css("display", "none");
			this.is_active = false;
		},

		update_position: function()
		{
			var form_height     = parseInt($("#modal-helper-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();

			$("#modal-helper-form").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},

	// *** Followed users form
	followed_users: {
		is_active: false,
		
		show: function()
		{
			this.is_active = true;
			this.update_position();
			
			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_followed_users";
			overlay.show();
			
			$("#followed-users-form").css("display", "block");
			effects.spinner.toggle("followed-users-form-spinner", false);
		},
		
		hide: function()
		{
			overlay.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_followed_users_wrapper";
			effects.spinner.toggle("followed-users-form-spinner", false);
		},
		
		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#followed-users-form").css("display", "none");
			this.is_active = false;
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#followed-users-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#followed-users-form").css("top", (viewport_offset + scroll_offset) + "px");
		},
		
		update_data: function(followed_users_html,
									 followed_users_count)
		{
			if(followed_users_count == 1)
				var caption = "user";
			else
				var caption = "users";
			
			$("#followed-users-form").find(".content").html(followed_users_html);
			$("#followed-users-form").find(".form-heading").find(".count").html(followed_users_count);
			$("#followed-users-form").find(".form-heading").find(".caption").html(caption);
		},
		
		update_status: function(change_status_div,
										new_follow_status)
		{
			if(new_follow_status == "follow")
			{
				$(change_status_div).html("Follow");
			}
			else
			{
				$(change_status_div).html("Unfollow");
			}
			
			var heading_div   = $(change_status_div).closest(".content").siblings(".form-heading");
			var current_count = $(heading_div).find(".count").html();
			var new_count     = (new_follow_status == "follow") ? --current_count : ++current_count;
			var new_caption   = (new_count == 1) ? "user" : "users";
			
			$(heading_div).find(".count").html(new_count);
			$(heading_div).find(".caption").html(new_caption);
		}
	},
	
	// *** Description form
	description: {
		is_active: false,
		
		show: function()
		{
			this.is_active = true;
			this.update_position();
			
			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_description";
			overlay.show();
			
			$("#description-form").css("display", "block");
			effects.spinner.toggle("description-form-spinner", false);
		},
		
		hide: function()
		{
			overlay.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_description_wrapper";
			effects.spinner.toggle("description-form-spinner", false);
		},
		
		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#description-form").css("display", "none");
			this.is_active = false;
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#description-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#description-form").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},
	
	// *** Registration form
	registration: {
		is_active: false,

		init_auth: function()
		{
			ajax.process_form("register-form",
			                  "account",
			                  "init_auth",
			                  "ajax/init_registration",
									null,
									"modal_update_overlay");
		},

		show: function()
		{
			this.init_auth();
			this.is_active = true;
			this.update_position();
			
			// Setting overlay 
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_registration";
			overlay.show();
			
			$("#registration-form").css("display", "block");
			effects.spinner.toggle("registration-form-spinner", false);
		},
		
		hide: function()
		{
			overlay.hide();
			form_tools.modal_errors.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_registration_wrapper";
			effects.spinner.toggle("registration-form-spinner", false);
			$("#auth_key").remove();
		},
		
		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#registration-form").css("display", "none");
			this.is_active = false;
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#registration-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#registration-form").css("top", (viewport_offset + scroll_offset) + "px");
		},
		
		show_activation_sent: function(email)
		{
			form_tools.modal_errors.hide();
			var activation_message  = "Thank you for registering! ";
			activation_message     += "Activation email has been sent to " + email + ".";
			activation_message     += " Please click on the activation link inside activation mail";
			activation_message     += " to activate your account.";
			var html = "";
			
			html += "<div class='activation-sent'							";
			html += "	  onclick='form_tools.registration.hide()'>	";
			html += "	<div class='wrapper'>								";
			html += "		<div class='confirmation-heading'>			";
			html += "			<span>											";
			html += "				Registration successful					";
			html += "			</span>											";
			html += "		</div>												";
			html += "																";
			html += "		<div class='message'>							";
			html += 				activation_message;
			html += "		</div>												";
			html += "	</div>													";
			html += "</div>														";
			
			$("#registration-form").find(".spinner-wrapper").html(html);
			$("#registration-form").find("#registration-form-spinner").removeClass("registration-form");
		}
	},
	
	errors: {
		render: function(errors,
							  error_stream)
		{
			switch(error_stream)
			{
				case "compact":
					form_tools.modal_errors.show(errors);
				break;
				
				case "default":
					form_tools.default_errors.show(errors);
				break;
			}
		}
	},
	
	// *** Default errors
	default_errors: {
		is_active: false,
		
		show: function(errors_array)
		{
			this.is_active = true;
			this.update_position();
			
			// Setting overlay 
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_default_errors";
			overlay.show();
			
			// Building errors HTML
			var html         = "";
			var errors_count = 0;
			
			for(error in errors_array)
			{
				html += errors_array[error] + "<br>";
				errors_count++;
			}
			
			if(errors_count == 1)
				var errors_label = "Error";
			else
				var errors_label = "Errors";
			
			$("#default-errors").find(".message").find(".wrapper").html(html);
			$("#default-errors").find(".count").html(errors_count);
			$("#default-errors").find(".label").html(errors_label);
			$("#default-errors").css("display", "block");
		},
		
		hide: function()
		{
			overlay.hide();
			$("#default-errors").css("display", "none");
			this.is_active = false;
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#default-errors").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#default-errors").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},
	
	// *** Modal errors
	modal_errors: {
		is_active: false,
		
		show: function(errors_array)
		{
			this.is_active = true;
			this.clear();
			this.update_position();
			
			var errors_count = 0;
			var list_class   = ".first-list";
			
			for(error in errors_array)
			{
				if(errors_count == 4)
					list_class = ".second-list";
				
				$("#modal-errors").find(list_class).append(errors_array[error] + "<br>");
				errors_count++;
			}
			
			var label = (errors_count == 1) ? "Error" : "Errors";
			
			$("#modal-errors").find(".count").html(errors_count);
			$("#modal-errors").find(".label").html(label);
			$("#modal-errors").css("display", "block");
		},
		
		clear: function()
		{
			$("#modal-errors").find(".count").html("");
			$("#modal-errors").find(".label").html("");
			$("#modal-errors").find(".first-list").html("");
			$("#modal-errors").find(".second-list").html("");
		},
		
		hide: function()
		{
			$("#modal-errors").css("display", "none");
			this.clear();
			this.is_active = false;
		},
		
		update_position: function()
		{
			var div_height      = parseInt($("#modal-errors").css("height"));
			var viewport_offset = $(window).height();
			var scroll_offset   = $(window).scrollTop();
			var div_top         = (viewport_offset + scroll_offset) - div_height;
			
			$("#modal-errors").css("top", div_top + "px");
		}
	},
	
	// *** Module form with category select
	category_select: {
		category_select:      null,
		subcategory_select:   null,
		category_input:       null,
		subcategory_item_div: null,
		
		is_selected_empty_element: function(select_object)
		{
			var selected_value = $(select_object).val();
			return (selected_value.length == 0) ? true : false;
		},
		
		set_real_category_id: function(category_id)
		{
			$(this.category_input).val(category_id);
		},
		
		unset_real_category_id: function()
		{
			$(this.category_input).val("");
		},
		
		disable_subcategory_select: function()
		{
			$(this.subcategory_select).attr("disabled", "disabled");
		},
		
		enable_subcategory_select: function()
		{
			$(this.subcategory_select).removeAttr("disabled");
		},
		
		is_subcategory_list_visible: function()
		{
			return ($(this.subcategory_item_div).css("display") == "block") ? true : false;
		},
		
		hide_subcategory_list: function()
		{
			$(this.subcategory_item_div).slideUp();
		},
		
		show_subcategory_list: function()
		{
			$(this.subcategory_item_div).slideDown();
		},
		
		load_subcategories: function()
		{
			var category_id = $(this.category_select).val();
			ajax.process(
				'photos',
				'load_subcategories',
				'ajax/' + category_id,
				'',
				'modal_no_confirmation'
			);
		},
		
		make_subcategory_select_options: function(subcategories)
		{
			var html  = "<option value=''>";
			html     += "</option>";
			
			for(i = 0; i <= subcategories.length - 1; i++)
			{
				html += "<option value='" + subcategories[i][0] + "'>";
				html += 		subcategories[i][1];
				html += "</option>";
			}
			
			$("#fake-select-subcategory").html(html);
		},
		
		parse_loaded_subcategories: function(subcategories)
		{
			if(subcategories.length > 0)
			{
				this.make_subcategory_select_options(subcategories);
				this.enable_subcategory_select();
				
				if(!this.is_subcategory_list_visible())
				{
					this.show_subcategory_list();
				}
			}
			else
			{
				this.set_real_category_id($(this.category_select).val());
				this.enable_subcategory_select();
				
				if(this.is_subcategory_list_visible())
				{
					this.hide_subcategory_list();
				}
			}
		},
		
		init: function()
		{
			this.category_select      = $("#fake-select-category");
			this.subcategory_select   = $("#fake-select-subcategory");
			this.category_input       = $("#real-category-input");
			this.subcategory_item_div = $(this.subcategory_select).closest(".item");
		},
		
		change_category: function()
		{
			this.init();
			this.unset_real_category_id();
			
			if(this.is_selected_empty_element(this.category_select))
			{
				if(this.is_subcategory_list_visible())
				{
					this.hide_subcategory_list();
				}
			}
			else
			{
				this.disable_subcategory_select();
				this.load_subcategories();
			}
		},
		
		change_subcategory: function()
		{
			this.init();
			this.unset_real_category_id();
			
			if(!this.is_selected_empty_element(this.subcategory_select))
			{
				this.set_real_category_id($(this.subcategory_select).val());
			}
		}
	},
	
	// *** Photos uploading
	photo: {
		target_div_id:     "",
		master_name:       "",
		extension:         "",
		spinner_id:        "",
		preview_to_delete: "",
		
		update_photo_numbers: function()
		{
			var photos_div         = $("#" + this.target_div_id);
			var previews_div       = $(photos_div).find(".previews");
			var previews_backwards = $(previews_div).find(".preview");
			var previews           = new Array();
			var photo_number       = 1;
			
			if(!($(previews_backwards).length > 0)) return;
			
			$(previews_backwards).each(function()
			{
				previews.unshift(this);
				$(this).attr("data-gallery-photo-number", photo_number);
				photo_number++;
			});

			photo_number = 1;
			$(previews).each(function()
			{
				$(this).find(".number").html(photo_number);
				photo_number++;
			});
		},
		
		// Adds new hidden with frame "ajax-master-photo-name"
		add_master_frame: function()
		{
			var photos_div = $("#" + this.target_div_id);
			var frames_div = $(photos_div).find(".frames");
			
			var last_input = $(frames_div).find(":input").last();
			
			if($(last_input).length > 0)
			{
				var last_photo_number = $(last_input).attr("name").match(/[\d]+/);
				
				var photo_number = parseInt(last_photo_number);
				photo_number++;
			}
			else
			{
				var photo_number = 0;
			}
			
			$("<input>").attr({
				type:  "hidden",
				name:  this.target_div_id + "[" + photo_number + "][frame]",
				id:    this.master_name,
				value: "ajax-" + this.master_name
			}).appendTo(frames_div);
		},
		
		// Adds photo HTML
		add_markup: function()
		{
			var base_path    = php_vars.base_url + "uploads/ajax/" + this.master_name;
			var preview_path = base_path + "-100-75." + this.extension;
			
			var html = "";
			
			html += "<div class='preview'";
			html += "     data-gallery-photo-number=''";
			html += "	  data-master-photo-name='" + this.master_name + "'";
			html += "	  data-upload-directory='ajax'>";
			html += "												";
			html += "	<img src='" + preview_path + "' width='100' height='75'";
			html += "        onclick='gallery.load(this, \"" + this.target_div_id + "\", \"preview\")'>";
			html += "								";
			html += "	<div class='number'> ";
			html += "	</div>					";
			html += "								";
			html += "	<div class='actions'>";
			html += "		<div class='main'";
			html += "			  onclick='form_tools.photo.set_as_main(this)'>";
			html += "		</div>";
			html += "								";
			html += "		<div class='delete'";
			html += "			  onclick='form_tools.photo.remove(this)'>";
			html += "		</div>";
			html += "	</div>";
			html += "								";
			html += "</div>";
			
			$("#" + this.target_div_id).find(".previews").prepend(html);
		},
		
		// Updates main photo name and main photo markup
		update_main: function()
		{
			var previews_div          = $("#" + this.target_div_id).find(".previews");
			var main_photo_name_input = $("#" + this.target_div_id).find(".main-photo-master-name");
			
			// Check main photo exists(multi-photo uploader)
			if($(main_photo_name_input).length == 0) return;
			
			// Check main photo is not selected
			if($(main_photo_name_input).val().length > 0) return;
				
			// Setting first uploaded(last) photo as main
			var last_preview = $(previews_div).find(".preview").last();
			
			if($(last_preview).length > 0)
			{
				var master_photo_name = $(last_preview).attr("data-master-photo-name");
				
				$(main_photo_name_input).val(master_photo_name);
				
				$(last_preview).find(".main").addClass("main-selected");
				$(last_preview).find(".main").removeClass("main");
			}
		},
		
		// Inserts uploaded photo into form
		insert: function(target_div_id,
							  master_photo_name,
							  photo_extension,
							  spinner_id)
		{
			this.target_div_id = target_div_id;
			this.master_name   = master_photo_name;
			this.extension     = photo_extension;
			this.spinner_id    = spinner_id;
			
			this.add_master_frame();
			this.add_markup();
			this.update_main();
			this.update_photo_numbers();
			this.close_uploader(); 
		},
		
		// Mark master frame as delete
		delete_master_frame: function()
		{
			var photos_div  = $("#" + this.target_div_id);
			var frames_div  = $(photos_div).find(".frames");
			var frame_input = $(frames_div).find("#" + this.master_name);
			
			if($(frame_input).val().match(/^ajax/))
			{
				// Frame: "deleteajax-mastername"
				$(frame_input).val("delete" + $(frame_input).val());
			}
			else
			{
				// Frame: "delete-mastername"
				$(frame_input).val("delete-" + $(frame_input).val());
			}
		},
		
		// Check if deleted photo is main
		is_main: function()
		{
			var photos_div            = $("#" + this.target_div_id);
			var main_photo_name_input = $(photos_div).find(".main-photo-master-name");
			
			return (this.master_name == $(main_photo_name_input).val()) ? true : false;
		},
		
		// Clears main photo name input
		delete_current_main: function()
		{
			var photos_div            = $("#" + this.target_div_id);
			var main_photo_name_input = $(photos_div).find(".main-photo-master-name");
			
			$(main_photo_name_input).val("");
		},
		
		// Deletes preview markup
		delete_markup: function()
		{
			$(this.preview_to_delete).remove();
		},
		
		// Deletes photo
		remove: function(html_object)
		{
			this.target_div_id     = $(html_object).closest(".photos").attr("id");
			this.preview_to_delete = $(html_object).closest(".preview");
			this.master_name       = $(this.preview_to_delete).attr("data-master-photo-name");
			
			this.delete_master_frame();
			
			if(this.is_main())
			{
				this.delete_current_main();
			}
			
			this.delete_markup();
			this.update_main();
			this.update_photo_numbers();
		},
		
		// Set's new main photo
		set_as_main: function(html_object)
		{
			var photos_div            = $(html_object).closest(".photos");
			var main_photo_name_input = $(photos_div).find(".main-photo-master-name");
			var new_main              = $(html_object);
			
			// If not main with "main-selected" class clicked
			if($(new_main).attr("class") == "main")
			{
				var previews     = $(new_main).closest(".previews");
				var preview      = $(new_main).closest(".preview");
				var current_main = $(previews).find(".main-selected");
				
				// Removing previous main
				$(current_main).removeClass("main-selected");
				$(current_main).addClass("main");
				
				// Setting new main
				$(new_main).addClass("main-selected");
				$(new_main).removeClass("main");
				
				$(main_photo_name_input).val($(preview).attr("data-master-photo-name"));
			}
		},
		
		// Clear's and closes upload form
		close_uploader: function()
		{
			var upload_form     = $("#" + this.spinner_id).find("form");
			var file_input_html = $(upload_form).html();
			
			$(upload_form).html(file_input_html);
			
			effects.spinner.toggle(this.spinner_id); 
		}
	},
	
	// Unique photo uploading
	single_photo: {
		// Photo wrapper div
		target_div_id:     "",
		master_name:       "",
		extension:         "",
		spinner_id:        "",
		
		// Adds new hidden with frame "ajax-master-photo-name"
		// as last element of frames batch
		add_master_frame: function()
		{
			var photos_div = $("#" + this.target_div_id);
			var frames_div = $(photos_div).find(".frames");
			var last_input = $(frames_div).find(":input").last();
			
			if($(last_input).length > 0)
			{
				var last_photo_number = $(last_input).attr("name").match(/[\d]+/);
				var photo_number      = parseInt(last_photo_number);
				photo_number++;
			}
			else
			{
				var photo_number = 0;
			}
			
			$("<input>").attr({
				type:  "hidden",
				name:  this.target_div_id + "[" + photo_number + "][frame]",
				id:    this.master_name,
				value: "ajax-" + this.master_name
			}).appendTo(frames_div);
		},
		
		// Checking if last photo isset,and is it marked
		// as deleted
		is_uploaded_photo_replacing_previous: function()
		{
			var photos_div = $("#" + this.target_div_id);
			var frames_div = $(photos_div).find(".frames");
			var last_input = $(frames_div).find(":input").last();
			
			if($(last_input).length > 0)
			{
				// Checking if last photo already marked as deleted
				if($(last_input).val().match(/^delete/))
					return false;
				else
					return true;
			}
			else
			{
				return false;
			}
		},
		
		// Adds uploaded photo HTML
		add_markup: function()
		{
			var base_path    = php_vars.base_url + "uploads/ajax/" + this.master_name;
			var preview_path = base_path + "-75-75." + this.extension;
			
			var html = "";
			
			html += "<img src='" + preview_path + "' width='75' height='75'>";
			html += "<div class='delete' onclick='form_tools.single_photo.remove(this)'></div>";
			
			$("#" + this.target_div_id).find(".photo-wrapper").html(html);
		},
		
		// Marks,that last photo is deleted
		mark_last_photo_frame_as_deleted: function()
		{
			var photos_div = $("#" + this.target_div_id);
			var frames_div = $(photos_div).find(".frames");
			var last_input = $(frames_div).find(":input").last();
			
			if($(last_input).val().match(/^ajax/))
			{
				// Frame: "deleteajax-mastername"
				$(last_input).val("delete" + $(last_input).val());
			}
			else
			{
				// Frame: "delete-mastername"
				$(last_input).val("delete-" + $(last_input).val());
			}
		},
		
		// Inserts uploaded photo
		insert: function(target_div_id,
							  master_photo_name,
							  photo_extension,
							  spinner_id)
		{
			this.target_div_id = target_div_id;
			this.master_name   = master_photo_name;
			this.extension     = photo_extension;
			this.spinner_id    = spinner_id;
			
			if(this.is_uploaded_photo_replacing_previous())
			{
				this.mark_last_photo_frame_as_deleted();
			}
			
			this.add_master_frame();
			this.add_markup();
			this.close_uploader();
		},
		
		// Marks photo as deleted and removes HTML
		remove: function(html_object)
		{
			this.target_div_id = $(html_object).closest(".single-photo").attr("id");
			this.mark_last_photo_frame_as_deleted();
			
			var html = "<div class='no-avatar'></div>";
			$("#" + this.target_div_id).find(".photo-wrapper").html(html);
		},
		
		// Clears file input value and closes
		// upload spinner
		close_uploader: function()
		{
			var upload_form     = $("#" + this.spinner_id).find("form");
			var file_input_html = $(upload_form).html();
			
			$(upload_form).html(file_input_html);
			effects.spinner.toggle(this.spinner_id); 
		}
	}
};

html_tools = {
	ie7_image_inside_link_click_fix: function(html_object) {
		if ($.browser.msie  && $.browser.version == 7) {
			 $(window.location).attr('href', $(html_object).closest('a').attr('href'));
		}
	},

	main_menu: {
		over: function(html_object)
		{
			var item_wrapper_div = $(html_object);
			var heading_div      = $(item_wrapper_div).find(".heading");
			var subheading_div   = $(item_wrapper_div).find(".subheading");
			
			$(heading_div).addClass("highlight");
			$(subheading_div).addClass("highlight");
		},
		
		out: function(html_object)
		{
			var item_wrapper_div = $(html_object);
			var heading_div      = $(item_wrapper_div).find(".heading");
			var subheading_div   = $(item_wrapper_div).find(".subheading");
			
			$(heading_div).removeClass("highlight");
			$(subheading_div).removeClass("highlight");
		}
	},
	
	module_list: {
		item_over: function(html_object)
		{
			$(html_object).find(".info").addClass("info-selected");
		},
		
		item_out: function(html_object)
		{
			$(html_object).find(".info").removeClass("info-selected");
		}
	},

	main: {
		most_active_post_over: function(html_object)
		{
			$(html_object).find(".bottom-panel").addClass("bottom-panel-selected");
		},

		most_active_post_out: function(html_object)
		{
			$(html_object).find(".bottom-panel").removeClass("bottom-panel-selected");
		},

		most_small_active_post_over: function(html_object, trim_div_class)
		{
			html_tools.trimifier.run(trim_div_class);
			$(html_object).find(".comments").addClass("comments-selected");
			$(html_object).find(".heading").css("display", "block");
			$(html_object).find(".module").css("display", "none");
		},

		most_small_active_post_out: function(html_object)
		{
			$(html_object).find(".comments").removeClass("comments-selected");
			$(html_object).find(".heading").css("display", "none");
			$(html_object).find(".module").css("display", "block");
		},

		module_images_small_image_over: function(html_object, trim_div_class)
		{
			html_tools.trimifier.run(trim_div_class);
			$(html_object).find(".header").css("display", "block");
		},

		module_images_small_image_out: function(html_object)
		{
			$(html_object).find(".header").css("display", "none");
		}
	},

	drive_list: {
		item_over: function(html_object)
		{
			$(html_object).find(".bottom-panel").addClass("bottom-panel-selected");
		},
		
		item_out: function(html_object)
		{
			$(html_object).find(".bottom-panel").removeClass("bottom-panel-selected");
		}
	},

	profile_list: {
		item_over: function(html_object)
		{
			$(html_object).find(".bottom-panel").addClass("bottom-panel-selected");
		},

		item_out: function(html_object)
		{
			$(html_object).find(".bottom-panel").removeClass("bottom-panel-selected");
		}
	},

	favorites_list: {
		item_over: function(html_object)
		{
			$(html_object).find(".bottom-panel").addClass("bottom-panel-selected");
		},

		item_out: function(html_object)
		{
			$(html_object).find(".bottom-panel").removeClass("bottom-panel-selected");
		},

		update_html_after_item_unfavorite: function(items_html,
																  pagination_html)
		{
			var current_count = parseInt($("#ajax-update-count").html());
			var new_count     = --current_count;

			$("#ajax-update-items").html(items_html);
			$(".ajax-update-pagination").html(pagination_html);
			$("#ajax-update-count").html(new_count);

			html_tools.trimifier.run("trim-divs");
		}
	},
	
	module_item: {
		wallpaper_over: function(html_object)
		{
			var wallpaper_link = html_object;
			$(wallpaper_link).find(".width").css("color", "rgb(81,81,181)");
		},
		
		wallpaper_out: function(html_object)
		{
			var wallpaper_link = html_object;
			$(wallpaper_link).find(".width").css("color", "white");
		},
		
		panel_button_over: function(html_object)
		{
			var button = $(html_object).closest(".button");
			$(button).addClass("over");
		},
		
		panel_button_out: function(html_object)
		{
			var button = $(html_object).closest(".button");
			$(button).removeClass("over");
		},
		
		add_like: function(button_wrapper_div)
		{
			// *** Setting button as selected
			var html = "";
			
			html += "<div class='button selected'>            ";
			html += "	<div class='wrapper selected-like-bg'>";
			html += "		Liked                              ";
			html += "	</div>										  ";
			html += "</div>											  ";
			
			var action_div = $(button_wrapper_div).closest(".action");
			$(action_div).html(html);
			
			// *** Updating panel
			var panel_div = $(action_div).siblings(".panel");
			
			// Setting new count
			var count_div     = $(panel_div).find(".count");
			var current_count = parseInt($(count_div).html());
			var new_count     = ++current_count;
			$(count_div).html(new_count);
			
			// Setting new caption
			var caption_div = $(panel_div).find(".caption");
			$(caption_div).removeClass("singular-caption plural-caption");
			
			if(new_count == 1)
				$(caption_div).addClass("singular-caption");
			else
				$(caption_div).addClass("plural-caption");
		},
		
		change_panel_list_item: function(button_wrapper_div,
												 	new_caption,
												 	count_action)
		{
			// *** Setting button caption
			$(button_wrapper_div).html(new_caption);
			
			// *** Updating panel
			var action_div = $(button_wrapper_div).closest(".action");
			var panel_div  = $(action_div).siblings(".panel");
			
			// Setting new count
			var count_div     = $(panel_div).find(".count");
			var current_count = parseInt($(count_div).html());
			
			if(count_action == "increase")
				var new_count = ++current_count;
			else
				var new_count = --current_count;
			
			$(count_div).html(new_count);
			
			// Setting new caption
			var caption_div = $(panel_div).find(".caption");
			$(caption_div).removeClass("singular-caption plural-caption");
			
			if(new_count == 1)
				$(caption_div).addClass("singular-caption");
			else
				$(caption_div).addClass("plural-caption");
		}
	},
	
	trimifier: {
		is_batch_initialized: false,
		trim_acceleration:    8,
		target_div:           null,
		text_div:             null,
		target_div_clone:     null,
		text_div_clone:       null,
		target_div_height:    null,
		div_text:             '',
		last_text_trim:       '',

		run: function(headers_to_trim_root_class)
		{
			$("." + headers_to_trim_root_class).each(function()
			{
				$(this).find(".trim-to-parent").each(function()
				{
					if(!html_tools.trimifier.is_batch_initialized)
					{
						html_tools.trimifier.init_batch(this);
					}

					html_tools.trimifier.trim_to_parent(this);
				});

				html_tools.trimifier.destroy_batch();
			});
		},

		init_batch: function(text_div)
		{
			this.text_div          = text_div;
			this.target_div        = $(this.text_div).closest("div");
			this.target_div_height = $(this.target_div).height();
			this.last_text_trim    = '';
			
			this.make_clones();
			this.is_batch_initialized = true;
		},
		
		destroy_batch: function()
		{
			this.delete_clones();
			this.is_batch_initialized = false;
		},
		
		trim_to_parent: function(text_div)
		{
			this.text_div = text_div; 
			this.div_text = $.trim($(this.text_div).text()); 
			this.update_clone_text();
			
			if(this.is_text_overflowing_div())
			{
				while(this.is_text_overflowing_div())
				{ 
					this.trim_last_batch_of_char();
					this.update_clone_text();
				}
				
				this.restore_max_div_text();
				this.insert_ellipsis();
				this.update_original_div();
			}
		},
		
		update_clone_text: function()
		{
			$(this.text_div_clone).html(this.div_text); 
		},
		
		trim_last_batch_of_char: function()
		{
			var new_length      = this.div_text.length - this.trim_acceleration;
			this.last_text_trim = this.div_text.substr(new_length,
																	 this.div_text.length);
			this.div_text       = this.div_text.substr(0, new_length);
		},
		
		restore_max_div_text: function()
		{
			var restore_char_index = 0;
			
			while(!this.is_text_overflowing_div())
			{ 
				this.div_text += this.last_text_trim.charAt(restore_char_index);
				this.update_clone_text();
				restore_char_index++;
			} 
			
			this.div_text = this.div_text.substr(0,
															 this.div_text.length - 1);
		},
		
		is_text_overflowing_div: function()
		{ 
			if($(this.text_div_clone).outerHeight()
					>
				this.target_div_height)
			{ 
				return true;
			}
			else
			{ 
				return false;
			}
		},
		
		insert_ellipsis: function()
		{
			var new_length = this.div_text.length - 3;
			this.div_text  = this.div_text.substr(0, new_length);
			
			this.div_text  = $.trim(this.div_text);
			this.div_text += "..."; 
		},
		
		update_original_div: function()
		{
			$(this.text_div).text(this.div_text);
		},
		
		make_clones: function()
		{
			var html = "";
			html += "<div id='trim-target-div-clone'>";
			html += "	<div id='trim-text-div-clone'>";
			html += "	</div>";
			html += "</div>";
			
			$("body").append(html);
			
			this.target_div_clone = $("#trim-target-div-clone");
			this.text_div_clone   = $("#trim-text-div-clone");
			
			$(this.target_div_clone).css({
				"width"       : $(this.target_div).css("width"),
				"height"      : $(this.target_div).css("height"),
				"line-height" : $(this.target_div).css("line-height"),
				"text-align"  : $(this.target_div).css("text-align"),
				"position"    : "absolute",
				"left"        : "-30000px",
				"top"         : "10px",
				"background"  : "red",
				"visibility"  : "none"
			});
			
			$(this.text_div_clone).css({
				"font-size"      : $(this.text_div).css("font-size"),
				"font-weight"    : $(this.text_div).css("font-weight"),
				"letter-spacing" : $(this.text_div).css("letter-spacing"),
				"padding-left"   : $(this.text_div).css("padding-left"),
				"padding-right"  : $(this.text_div).css("padding-right"),
				"padding-top"    : $(this.text_div).css("padding-top"),
				"padding-bottom" : $(this.text_div).css("padding-bottom"),
				"line-height"    : $(this.text_div).css("line-height")
			});
		},
		
		delete_clones: function()
		{
			$(this.text_div_clone).remove();
			$(this.target_div_clone).remove();
		}
	}
};

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
});

