overlay = {
    overlay_id:       "overlay",
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
            
            case "close_delete_confirmation":
                form_tools.delete_confirmation.cancel_delete();
            break;

            case "close_confirmation_prompt":
                form_tools.confirmation_prompt.cancel();
            break;

            case "close_settings":
                form_tools.settings.hide();
            break;
        }
        
        overlay.hide();
    },
    
    is_active: function()
    {
        return this.active;
    },
    
    set_background: function(bg_image)
    {
        $("#" + this.overlay_id).css("background", "url(img/admin/" + bg_image + ".png)");
    },
    
    update_position: function()
    {
        $("#" + this.overlay_id).css("height", $(document).height() + "px");
    },
    
    show: function(bg_image)
    {
        var bg_image = bg_image || "overlay_bg";
        
        if(this.hide_on_click)
        {
            this.attach_hide_event();
        }
        
        this.set_background(bg_image);
        this.update_position();
        
        $("#" + this.overlay_id).css("display", "block");
        
        this.active = true;
    },
    
    hide: function()
    {
        if(this.hide_on_click)
        {
            this.detach_hide_event();
            
            this.hide_on_click    = false;
            this.callback_on_hide = "";
        }
        
        $("#" + this.overlay_id).css("display", "none");
        
        this.active = false;
    }
}