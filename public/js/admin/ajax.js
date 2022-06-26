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
			var url_segments = php_vars.admin_panel_url + "/" + this.data.url_segments;
			var url          = php_vars.base_url + "index.php?url=" + url_segments;
			
			window.location.href = url;
		},
		
		update_status: function()
		{
			var new_status = library.ucfirst(this.data.status);
			$(this.html_object).html(new_status);
		},
		
		update_caption: function()
		{
			var new_caption = library.ucfirst(this.data.new_caption);
			$(this.html_object).html(new_caption);
		},
		
		insert_uploaded_photo: function()
		{
			form_tools.photo.insert(this.data.target_div_id,
											this.data.master_photo_name,
											this.data.photo_extension,
											this.data.spinner_id);
		},
		
		// Updates items table and pagination after item delete
		update_items_and_pagination_html: function()
		{
			$(".ajax-pagination").html(this.data.pagination_html);
			$(".ajax-module-items").html(this.data.items_html);
			html_tools.trimifier.run("trim-divs");
		},
		
		// Dashboard callbacks
		update_dashboard_items_and_pagination_html: function()
		{
			$(".ajax-pagination").html(this.data.pagination_html);
			$(".ajax-dashboard-events").html(this.data.items_html);
		},
		
		update_dashboard_module_item_status: function()
		{
			var html = "";
			
			if(this.data.status == "enabled")
				html = "<span class='highlight'>d</span>isable";
			else
				html = "<span class='highlight'>e</span>nable";
			
			$(this.html_object).html(html);
		},
		
		update_dashboard_module_item_moderation: function()
		{
			var html = "";
			
			if(this.data.moderated == "yes")
				html = "<span class='highlight'>u</span>nmoderate";
			else
				html = "<span class='highlight'>m</span>oderate";
			
			$(this.html_object).html(html);
		},

		// Settings callbacks
		update_settings_last_rating_update: function()
		{
			var item_div = $(this.html_object).parent(".item");
			$(item_div).find(".subname").html("Just now");
		},

		update_settings_site_mode: function()
		{
			var item_div = $(this.html_object).parent(".item");
			$(item_div).find(".subname").html(this.data.new_status);
		},

		update_settings_clear_ajax_files_count: function()
		{
			var settings_row_div = $(this.html_object).closest(".settings-row");
			$(settings_row_div).find(".ajaxdir-files-count").html("0");
		},

		update_settings_clear_cache_files_count: function()
		{
			var settings_row_div = $(this.html_object).closest(".settings-row");
			$(settings_row_div).find(".ajaxdir-files-count").html("0");
		},

		update_settings_unactivated_accounts_count: function()
		{
			var settings_row_div = $(this.html_object).closest(".settings-row");
			$(settings_row_div).find(".ajaxdir-files-count").html("0");
		},

		update_resources_compiled_message: function()
		{
			var html = "<span style='color: rgb(81,81,181); font-size: 18px; font-weight: bold;'>Compiled</span>";
			$(this.html_object).html(html);
		},
		
		// Categories module callbacks
		set_children_html_and_toggle: function()
		{ 
			category.set_children_html_and_toggle(this.data.html);
		},
		
		delete_categories: function()
		{
			category.delete_with_children(this.html_object);
		},

		// Users callbacks
		update_user_items_and_pagination_html: function()
		{
			$(".ajax-pagination").html(this.data.pagination_html);
			$(".ajax-users-list-items").html(this.data.items_html);
		},

		update_user_blocked_status: function()
		{
			$(this.html_object).html(this.data.new_status);
		},
		
		// Module forms callback
		parse_select_subcategories: function()
		{
			form_tools.category_select.parse_loaded_subcategories(this.data.subcategories);
		}
	},
	
	parse_server_answer: function(server_answer)
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
			library.show_errors(errors); 
		}
	},
	
	enable_loading: function(loading_type)
	{
		switch(loading_type)
		{
			case "modal":
			case "modal_no_confirmation":
				modal_loading.show_loading();
			break;
		
			case "form":
				overlay.show("transparent_overlay_bg");
				$("#form-loading").css('display', 'block');
				document.getElementById("form-submit").disabled = true;
			break;

			case "settings_form":
				var action_div = $(this.callbacks.html_object).closest(".action");
				$(action_div).find(".loading").css("display", "block");
				$(action_div).find(".submit").attr("disabled", "disabled");
			break;
		
			case "logout":
				modal_loading.show_loading();
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
			
			case "modal_no_confirmation":
				modal_loading.hide_fast();
			break;
			
			case "form":
				overlay.hide();
				$("#form-loading").css('display', 'none'); 
				document.getElementById("form-submit").disabled = false;
			break;

			case "settings_form":
				var action_div = $(this.callbacks.html_object).closest(".action");
				$(action_div).find(".loading").css("display", "none");
				$(action_div).find(".submit").removeAttr("disabled");
			break;
		
			case "logout":
				; // Don't disable
			break;
		}
	},
	
	process: function(controller,
							action,
							parametrs,
							html_object,
							loading_type)
	{
		var loading_type = loading_type || "modal";
		
		this.enable_loading(loading_type);
		
		if(library.is_object(html_object))
		{
			this.callbacks.html_object = html_object;
		}
		
		var router_parametrs = controller + "/" + action + "/" + parametrs;
		var url_segments     = php_vars.admin_panel_url + "/" + router_parametrs;
		
		$.ajax({
			url: php_vars.base_url + "index.php?url=" + url_segments,
			
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
					this.parse_server_answer(server_answer);
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
								  loading_type)
	{
		if(library.is_object(html_object))
		{
			this.callbacks.html_object = html_object;
		}

		var loading_type = loading_type || "form";
		this.enable_loading(loading_type);

		var action           = action || "save";
		var form_data        = $("form[name=" + form_name + "]").serialize();
		var router_parametrs = controller + "/" + action + "/" + parametrs;
		var url_segments     = php_vars.admin_panel_url + "/" + router_parametrs;
		
		$.ajax({
			url: php_vars.base_url + "index.php?url=" + url_segments,
			
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
					this.parse_server_answer(server_answer);
				}
				
				this.disable_loading(loading_type);
			}
		});
	}
}