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