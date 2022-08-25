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
        },

        feedAfterMostActivePostsDataLoaded: function()
        {   
            feedBuilder.isMostActivePostsDataLoaded = true;
            feedBuilder.mostActivePostsData = this.data.mostActivePostsData;
            feedBuilder.renderFeedIfAllDataIsLoaded();
        },

        feedAfterLastPostsDataLoaded: function()
        {   
            feedBuilder.isLastPostsDataLoaded = true;
            feedBuilder.lastPostsData = this.data.lastPostsData;
            feedBuilder.renderFeedIfAllDataIsLoaded();
        },

        feedAfterLinkedPostsDataLoaded: function()
        {
            feedBuilder.isLinkedPostsDataLoaded = true;
            feedBuilder.linkedPostsData = this.data.linkedPostsData;
            feedBuilder.renderFeedIfAllDataIsLoaded();
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
            case "modal_wait_for_redirect":
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
            
            case "modal_wait_for_redirect":
                ;
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
                ; // Dont disable
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