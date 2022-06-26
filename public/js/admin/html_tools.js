html_tools = {
	// *** Dashboard
	dashboard: {
		show_events_to_show_list: function()
		{
			$("#events-to-show-list").css("display", "block");
		},

		hide_events_to_show_list: function()
		{
			$("#events-to-show-list").css("display", "none");
		},

		events_to_show_list_over: function(container)
		{
			$(container).find(".heading").css("color", "rgb(81,81,181)");
		},

		events_to_show_list_out: function(container)
		{
			$(container).find(".heading").css("color", "black");
		}
	},

	// *** Settings
	settings: {
		item_over: function(html_object)
		{
			var item_big_div = $(html_object).parent(".item");
			$(item_big_div).addClass("item-over");
			$(item_big_div).find(".name").addClass("name-over");
		},

		item_out: function(html_object)
		{
			var item_big_div = $(html_object).parent(".item");
			$(item_big_div).removeClass("item-over");
			$(item_big_div).find(".name").removeClass("name-over");
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