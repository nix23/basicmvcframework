form_tools = {
	// *** Settings form
	settings: {
		is_active: false,

		show: function()
		{
			this.is_active = true;
			this.update_position();

			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_settings";
			overlay.show();

			$("#settings-form").css("display", "block");
			effects.spinner.toggle("settings-form-spinner", false, false, "no");
		},

		hide: function()
		{
			overlay.hide();
			effects.spinner.call_callback = true;
			effects.spinner.callback_type = "hide_settings_wrapper";
			effects.spinner.toggle("settings-form-spinner", false,  false, "no");
		},

		// Used as effects.spinner.shrink callback
		hide_wrapper: function()
		{
			$("#settings-form").css("display", "none");
			this.is_active = false;
		},

		update_position: function()
		{
			var form_height     = parseInt($("#settings-form").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();

			$("#settings-form").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},

	// *** Category form
	parent_category_select: function(select_object)
	{
		var selected_value = $(select_object).val();
		
		if(selected_value.toString() == '0')
		{
			$("#show-in-modules-list").slideDown(300);
		}
		else
		{
			$("#show-in-modules-list").slideUp(300);
		}
	},
	
	// *** Delete confirmation form
	delete_confirmation: {
		is_active:    false,
		form_name:    "",
		controller:   "",
		action:       "",
		parametrs:    "",
		html_object:  null,
		loading_type: "",
		
		show: function(form_name,
							controller,
							action,
							parametrs,
							html_object,
							loading_type)
		{
			this.is_active = true;
			this.update_position();
			
			// Saving ajax-call parametrs
			this.form_name    = form_name;
			this.controller   = controller;
			this.action       = action;
			this.parametrs    = parametrs;
			this.html_object  = html_object;
			this.loading_type = loading_type;
			
			// Setting overlay
			overlay.hide_on_click = true;
			overlay.before_hide   = "close_delete_confirmation";
			overlay.show();
			
			$("#delete-confirmation").css("display", "block");
		},
		
		hide: function()
		{
			overlay.hide();
			this.is_active = false;
			$("#delete-confirmation").css("display", "none");
		},
		
		process_delete: function()
		{
			this.hide();
			ajax.process_form(this.form_name,
									this.controller,
									this.action,
									this.parametrs,
									this.html_object,
									this.loading_type);
		},
		
		cancel_delete: function()
		{
			this.hide();
		},
		
		update_position: function()
		{
			var form_height     = parseInt($("#delete-confirmation").css("height"));
			var viewport_offset = ($(window).height() - form_height) / 2;
			var scroll_offset   = $(window).scrollTop();
			
			$("#delete-confirmation").css("top", (viewport_offset + scroll_offset) + "px");
		}
	},

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
				case "change_site_mode":
					prompt_message      = "Are you sure that you want change site mode?";
					process_button_text = "Yes";
					cancel_button_text  = "No";
				break;

				case "recalculate_rating":
					prompt_message      = "Are you sure that you want recalculate all user ratings(this can take some time)?";
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
			});
			
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
	}
};