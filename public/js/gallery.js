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

	_bindImageReloadFromExternalUrlOnError: function() {
		var me = this;

		me._lastReloadedMasterPhotoName = null;
		var $galleryImage = $(this.gallery).find("#gallery-image");

		$galleryImage.bind("error", function(event) {
			var path = "http://83.99.185.92/fordrive/public/";
			path += me.upload_directories[me.current_number - 1];
			path += me.master_photo_names[me.current_number - 1];
			path += "-800-520.jpg";
			

			if(me._lastReloadedMasterPhotoName != null) {
				if(me._lastReloadedMasterPhotoName ==
					me.master_photo_names[me.current_number - 1]) {
					$galleryImage.unbind("error");
					return;
				}
			}

			me._lastReloadedMasterPhotoName = me.master_photo_names[me.current_number - 1];
			$galleryImage.attr("src", path);
		});
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