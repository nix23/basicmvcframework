modal_loading = {
	loading_id: "overlay-loading",
	is_active:  false,
	
	set_message: function(message)
	{
		$("#" + this.loading_id + " .message").html(message);
	},
	
	set_icon: function(image_name)
	{
		$("#" + this.loading_id + " .icon").css("background",  "url(img/admin/" + image_name  + ")");
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
		var scroll_offset      = 0;
		var content_offset_top = 10;
		
		if($(window).scrollTop() > 120)
		{
			scroll_offset = $(window).scrollTop() - 120;
		}
		
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
		
		overlay.show("transparent_overlay_bg");
		
		html  = "";
		html += "<div class='heading'>";
		html += "	Loading..."
		html += "</div>";
		html += "<div class='subheading'>";
		html += "	Please wait a moment.";
		html += "</div>";
		
		this.set_message(html);
		this.set_icon("loading_middle.gif");
		
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
		this.set_icon("ok.png");
		
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
	}
};