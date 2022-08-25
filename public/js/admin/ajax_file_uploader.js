// Extends functionality of base ajax class
// to provide ajax file uploads.
ajax_file_uploader = {
    root_div:          "",
    target_div_id:     "",
    
    // Binds callback to iframe(only on first upload)
    attach_callback_event: function()
    {
        var context = this;
        
        $('#ajax-iframe').load(function() {
            context.after_upload();
        });
    },
    
    // Fires after file upload is complete
    after_upload: function()
    { 
        var server_answer = $('#ajax-iframe').contents().find('body').html();
        
        try
        {
            var server_answer = jQuery.parseJSON(server_answer);
        }
        catch(e)
        {
            alert(server_answer);
        }
        
        if(library.is_object(server_answer))
        {
            // Adding frame to server answer with target div
            server_answer.data.target_div_id = this.target_div_id;
            
            ajax.parse_server_answer(server_answer);
        }
        
        this.delete_iframe();
        this.disable_loading();
    },
    
    // Creates iframe,which will catch server answer
    create_iframe: function()
    {
        var html  = "<iframe";
        html     += " id='ajax-iframe'";
        html     += " name='ajax-iframe'";
        html     += " class='ajax-iframe'";
        html     += "></iframe>";
        
        $("body").append(html);
    },
    
    // Removes iframe from body
    delete_iframe: function()
    {
        $("#ajax-iframe").remove();
    },
    
    enable_loading: function()
    {
        $(this.root_div).find(".icon").css("display", "block");
        $(this.root_div).find(".button").attr("disabled", "disabled");
    },
    
    disable_loading: function()
    {
        $(this.root_div).find(".icon").css("display", "none");
        $(this.root_div).find(".button").removeAttr("disabled");
    },
    
    // Calls upload script via iframe submit
    upload: function(root_div_id, target_div_id, controller, action)
    {
        this.root_div = $("#" + root_div_id);
        
        this.enable_loading();
        this.create_iframe();
        
        // Div,which will contain file frames
        this.target_div_id = target_div_id;
        
        // Binding load event to iframe
        this.attach_callback_event();
        
        // Generating file-upload script url
        var url_segments = php_vars.admin_panel_url + "/" + controller + "/" + action + "/ajax";
        var url          = php_vars.base_url + "index.php?url=" + url_segments;
        
        // Submitting form via iframe
        $(this.root_div).find("form").attr("action", url);
        $(this.root_div).find("form").submit();
    }
}