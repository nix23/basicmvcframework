library = {
    // TO DO: Extend to copy time and runs count,
    // TO DO: Extend to save results in file(ajax)
    profiler: {
        time_count: null,
        
        start: function()
        {
            this.time_count = new Date().getTime();
        },
        
        stop: function()
        {
            alert(new Date().getTime() - this.time_count + " msec");
        }
    },
    
    is_object: function(object)
    {
         return (typeof(object) != 'object') ? false : true;
    },
    
    iterate_object: function(object)
    {
        var message = "";
        
        for(property in object)
        {
            if(this.is_object(object[property]))
            {
                message += this.iterate_object(object[property]);
            }
            else
            {
                message += property + ": " + object[property] + "\n";
            }
        }
        
        return message;
    },
    
    print_object: function(object)
    {
        var message = "";
        
        message += this.iterate_object(object);
        
        alert(message);
    },
    
    not_empty: function(string_to_check)
    {
        if(string_to_check.length() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    },
    
    is_empty: function(string_to_check)
    {
        if(string_to_check.length() == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    },
    
    ucfirst: function(string)
    {
         return string.charAt(0).toUpperCase() + string.substr(1);
    }
}