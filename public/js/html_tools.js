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
            html += "   <div class='wrapper selected-like-bg'>";
            html += "       Liked                              ";
            html += "   </div>                                        ";
            html += "</div>                                           ";
            
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
            html += "   <div id='trim-text-div-clone'>";
            html += "   </div>";
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