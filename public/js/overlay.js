overlay = {
    overlay_id:       "overlay",
    overlay_bg_class: "",
    active:           false,
    hide_on_click:    false,
    before_hide:      "",
    
    attach_hide_event: function()
    {
        $("#" + this.overlay_id).on("click", this.force_hide);
    },
    
    detach_hide_event: function()
    {
        $("#" + this.overlay_id).off("click"); 
    },
    
    force_hide: function()
    {
        switch(overlay.before_hide)
        {
            case "close_gallery":
                gallery.unload();
            break;
            
            case "close_login":
                form_tools.login.hide();
            break;
            
            case "close_registration":
                form_tools.registration.hide();
            break;
            
            case "close_default_errors":
                form_tools.default_errors.hide();
            break;
            
            case "close_newcomment":
                form_tools.newcomment.hide();
            break;
            
            case "close_followed_users":
                form_tools.followed_users.hide();
            break;

            case "close_confirmation_prompt":
                form_tools.confirmation_prompt.hide();
            break;
            
            case "close_description":
                form_tools.description.hide();
            break;

            case "close_modal_helper":
                form_tools.modal_helper.hide();
            break;
        }
        
        overlay.hide();
    },
    
    is_active: function()
    {
        return this.active;
    },
    
    set_background: function()
    {
        $("#" + this.overlay_id).addClass(this.overlay_bg_class);
    },
    
    update_position: function()
    {
        $("#" + this.overlay_id).css("height", $(document).height() + "px");
    },
    
    show: function(bg_type)
    {
        // Hidding all flash videos
        $(".video").find(".player").css("visibility", "hidden");
        $(".video").find(".pause").css("display", "block");
        
        // Removing background classes
        if($("#" + this.overlay_id).hasClass("overlay-transparent-background"))
            $("#" + this.overlay_id).removeClass("overlay-transparent-background");
        
        if($("#" + this.overlay_id).hasClass("overlay-light-background"))
            $("#" + this.overlay_id).removeClass("overlay-light-background");
        
        // Setting background class
        if(bg_type == "overlay-transparent-background")
            this.overlay_bg_class = "overlay-transparent-background";
        else
            this.overlay_bg_class = "overlay-light-background";
        
        if(this.hide_on_click)
        {
            this.attach_hide_event();
        }
        
        this.set_background();
        this.update_position();
        
        $("#" + this.overlay_id).css("display", "block");
        this.active = true;
    },
    
    hide: function()
    {
        if(this.hide_on_click)
        {
            this.detach_hide_event();
            
            this.hide_on_click = false;
            this.before_hide   = "";
        }
        
        $("#" + this.overlay_id).css("display", "none");
        $("#" + this.overlay_id).removeClass(this.overlay_bg_class);
        
        // Restoring all flash videos
        $(".video").find(".player").css("visibility", "visible");
        $(".video").find(".pause").css("display", "none");
        
        this.active = false; 
    }
}