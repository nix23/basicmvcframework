$(document).ready(function(){ 
    $.ajaxSetup({
        type: "POST"
    });
    
    // Form styling
    $('input[type="text"],input[type="password"],select,textarea').addClass("input-border");
    
    $('input[type="text"],input[type="password"],textarea').focus(function() {
        $(this).addClass("input-focus");
    });
    
    $('input[type="text"],input[type="password"],textarea').blur(function() { 
        $(this).removeClass("input-focus");
    });
    
    $('input[type="text"],input[type="password"],select,textarea').bind('mouseover', function(event) {
        $(this).addClass("input-over");
    });
    
    $('input[type="text"],input[type="password"],select,textarea').bind('mouseout', function(event) {
        $(this).removeClass("input-over");
    });
    
    // Show confirmation message if it was set
    if(php_vars.modal_show_confirmation)
    {
        modal_loading.show_confirmation_and_hide();
    }
    
    // Window resize logic
    $(window).resize(function()
    {
        // Update overlay height
        if(overlay.is_active)
        {
            overlay.update_position();
        }
        
        // Update gallery top
        if(gallery.is_active)
        {
            gallery.update_position();
        }
        
        // Update delete confirmation
        if(form_tools.delete_confirmation.is_active)
        {
            form_tools.delete_confirmation.update_position();
        }

        // Update confirmation prompt
        if(form_tools.confirmation_prompt.is_active)
        {
            form_tools.confirmation_prompt.update_position();
        }

        // Update settings top
        if(form_tools.settings.is_active)
        {
            form_tools.settings.update_position();
        }
    });
    
    // Window scroll logic
    $(window).scroll(function()
    {
        // Update gallery top
        if(gallery.is_active)
        {
            gallery.update_position();
        }
        
        // Update modal loading
        if(modal_loading.is_active)
        {
            modal_loading.update_position();
        }
        
        // Update delete confirmation
        if(form_tools.delete_confirmation.is_active)
        {
            form_tools.delete_confirmation.update_position();
        }

        // Update confirmation prompt
        if(form_tools.confirmation_prompt.is_active)
        {
            form_tools.confirmation_prompt.update_position();
        }

        // Update settings top
        if(form_tools.settings.is_active)
        {
            form_tools.settings.update_position();
        }
    });
    
    // Header trimmer logic
    html_tools.trimifier.run("trim-divs");

    // Dashboard events-to-show-list logic
    if($("#events-to-show-list").length > 0)
    {
        // Stopping list menu mouseout from bubling
        $("#events-to-show-list").mouseleave(function(event){
            html_tools.dashboard.hide_events_to_show_list();
        });

        // 'A' over
        $("#events-to-show-list").find("a").mouseover(function(){
            html_tools.dashboard.events_to_show_list_over(this);
        });

        // 'A' out
        $("#events-to-show-list").find("a").mouseout(function(){
            html_tools.dashboard.events_to_show_list_out(this);
        });
    }
});