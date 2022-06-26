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