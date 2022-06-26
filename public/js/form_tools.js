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