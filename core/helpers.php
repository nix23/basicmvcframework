<?php
    /** 
    * This file contains all functions,which can be
    * called in application templates. Many functions
    * are used as wrappers to call core static methods,
    * this is used to separate logic from presentation,
    * and is making code in templates more compact and
    * readable.
    **/
    
    /*********************************************************************************
     *********************************************************************************
     **                             Shared helpers                                  **
     *********************************************************************************
     ********************************************************************************/
    
    // Includes css-file
    function css($url = "")
    {
        echo "<link rel='StyleSheet' href='" . Url::css($url) . "' type='text/css' media='screen'>";
    }
    
    // Includes css-file(safe from browser caching)
    function uncached_css($url = "")
    {
        echo "<link rel='StyleSheet' href='" . Url::css($url) . "?" . time() . "' type='text/css' media='screen'>";
    }
    
    // Include ie6 css-file
    function css_ie_less_than_version($url = "", $ie_number)
    {
        echo "<!--[if lt IE $ie_number]>";
        uncached_css($url);
        echo "<![endif]-->";
    }
    
    // Includes javascript file
    function js($url = "")
    {
        echo "<script src='" . Url::js($url) . "'></script>";
    }
    
    // Includes js-file(safe from browser caching)
    function uncached_js($url = "")
    {
        echo "<script src='" . Url::js($url) . "?" . time() . "'></script>";
    }
    
    // Generates token,unique for this form
    function token($form)
    {
        echo Form::generate_token($form);
    }
    
    // Generates full web-server path to photo
    function load_photo($master_name, $width, $height)
    {
        $master_name_parts = explode("-", $master_name);
        $directory         = $master_name_parts[0];

        // $photoFsPath  = UPLOADS_IMAGES . $directory . DS . $master_name;
        // $photoFsPath .= "-" . $width . "-" . $height . ".jpg";
        // if(file_exists($photoFsPath)) {
        //  $base_url = Url::get_base_url();
        //  echo "{$base_url}uploads/images/{$directory}/{$master_name}-{$width}-{$height}.jpg";
        //  }
        // else {
        //  $base_url = "http://83.99.185.92/fordrive/public/";
        //  echo "{$base_url}uploads/images/{$directory}/{$master_name}-{$width}-{$height}.jpg";
        //  }

        $base_url          = Url::get_base_url();
        
        echo "{$base_url}uploads/images/{$directory}/{$master_name}-{$width}-{$height}.jpg";
    }
    
    /**
    * Filters variable for correct displaying
    * in URL(SEO FRIENDLY)
    **/
    function create_slug($string)
    {
        return Url::create_slug($string);
    }

    // Returns application base url
    function get_base_url()
    {
        return Url::get_base_url();
    }

    function trim_text($text, $length)
    {
        if(mb_strlen($text, "UTF-8") < $length)
            return $text;
        else
            return mb_substr($text, 0, $length, "UTF-8") . "...";
    }

    /** 
    * Prints true array values as string,
    * glued with spaces.
    **/
    function stringify($pieces = array()) 
    {
        if($pieces)
        { 
            $joined_string = "";
            $array_length  = count($pieces);
            
            if($array_length == 1)
            {
                if($pieces[0])
                {
                    $joined_string .= $pieces[0];
                }
            }
            else
            {
                for($i = 0; $i < ($array_length - 1); $i++)
                {
                    if($pieces[$i])
                    {
                        $joined_string .= $pieces[$i] . " ";
                    }
                    
                    $last_index = $i;
                    $last_index++;
                }
                
                $joined_string .= $pieces[$last_index];
            }
            
            echo $joined_string;
        }
    }
    
    /*********************************************************************************
     *********************************************************************************
     **                              Admin helpers                                  **
     *********************************************************************************
     ********************************************************************************/
    
    // Displays absolute link in backend
    function admin_link($url_segments = "")
    {
        echo Url::link(Registry::get('config')->admin_panel_url . "/" . $url_segments);
    }
    
    // Generates admin module catalog link
    function admin_module_link($controller  = "",
                                        $action      = "",
                                        $category    = false,
                                        $subcategory = false,
                                        $page        = 1,
                                        $sort        = "moderated-asc")
    {
        admin_link(Url::make_module_segments($controller,
                                                         $action,
                                                         $category,
                                                         $subcategory,
                                                         $page,
                                                         $sort));
    }
    
    /** 
    * Prints shared variables between php and javascript.
    * (Example: "var javascript_var = '<?php echo $php_var; ?>';)
    **/
    function admin_php_to_js()
    {
        $session = Registry::get('session');
        
        if($session->is_modal_show_confirmation_set())
        {
            $modal_show_confirmation = "true";
            $session->unset_modal_show_confirmation();
        }
        else
        {
            $modal_show_confirmation = "false";
        }
        
        echo "<script>
                    php_vars = {
                        base_url: '"               . Url::get_base_url() . "',
                        admin_panel_url: '"        . Registry::get('config')->admin_panel_url . "',
                        modal_show_confirmation: " . $modal_show_confirmation . "
                    }
              </script>";
    }
    
    /**********************************************
    **             Dashboard helpers             **
    **********************************************/
    function parse_dashboard_event_module($module)
    {
        switch($module)
        {
            case "photos":
                return "photoset";
            break;

            case "spots":
                return "spot";
            break;

            case "speed":
                return "speed";
            break;

            case "videos":
                return "video";
            break;
        }
    }

    function parse_dashboard_event_header($dashboard_event)
    {
        $header   = "";
        $module   = $dashboard_event->module;
        $type     = $dashboard_event->type;

        if($dashboard_event->user)
        {
            $username  = $dashboard_event->user->username;
            $header   .= $username . " ";
        }

        switch($type)
        {
            case "upload":
                $header .= "saved ";
                $header .= parse_dashboard_event_module($module);
            break;

            case "comment":
                $header .= "commented ";
                $header .= parse_dashboard_event_module($module);
            break;

            case "like":
                $header .= "liked ";
                $header .= parse_dashboard_event_module($module);
            break;

            case "answer":
                $header .= "answered comment";
            break;

            case "favorite":
                $header .= "added ";
                $header .= parse_dashboard_event_module($module);
                $header .= " to favorites";
            break;

            case "follow":
                $header  = $dashboard_event->follower_user->username;
                $header .= " followed ";
                $header .= $dashboard_event->followed_user->username;
            break;

            case "activated_user":
                $header .= "activated account";
            break;

            case "registred_user":
                $header .= "just registred on fordrive";
            break;
        }

        return $header;
    }

    function render_dashboard_event_full_name($post)
    {
        switch($post->module)
        {
            case "photos":
                $module_object                   = new stdClass;
                $module_object->year             = $post->heading;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->name             = $post->secondary_heading;
                photoset_full_name($module_object);
            break;

            case "spots":
                $module_object                   = new stdClass;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->album_name       = $post->heading;
                spot_full_name($module_object);
            break;

            case "speed":
                echo $post->heading;
            break;

            case "videos":
                echo $post->heading;
            break;
        }
    }

    function render_dashboard_event_module_item_link($post)
    {
        switch($post->module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $post->id;
                $module_object->year = $post->heading;
                $module_object->name = $post->secondary_heading;
                photoset_item_link($module_object,
                                         $post->category,
                                         $post->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $post->id;
                $module_object->album_name = $post->heading;
                spot_item_link($module_object,
                                    $post->category,
                                    $post->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $post->id;
                $module_object->heading = $post->heading;
                speed_item_link($module_object,
                                     $post->category,
                                     $post->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $post->id;
                $module_object->heading = $post->heading;
                video_item_link($module_object,
                                     $post->category,
                                     $post->subcategory);
            break;
        }
    }

    function render_dashboard_event_time_ago($dashboard_event)
    {
        $time_ago = time_ago($dashboard_event->posted_on);

        if($time_ago == "Just now")
        {
            list($first_part, $second_part) = explode(" ", $time_ago);
            echo "<span class='highlight'>$first_part</span> $second_part";
        }
        else
        {
            echo $time_ago;
        }
    }
    
    /*********************************************************************************
     *********************************************************************************
     **                             Public helpers                                  **
     *********************************************************************************
     ********************************************************************************/
    
     /**********************************************
     **                  Common                   **
     **********************************************/
    
    function time_ago($source_datetime)
    {
        $time_ago = Datetime_Converter::get_time_ago($source_datetime);
        $time_ago = preg_replace("~(\d+)~u", "<span class='highlight'>$1</span>", $time_ago);
        
        return $time_ago;
    }
    
    function time_on_site($source_datetime)
    {
        $time_ago = time_ago($source_datetime);
        
        if($time_ago == "Just now")
        {
            echo "Just registred on fordrive";
        }
        else
        {
            $time_ago = substr($time_ago, 0, mb_strlen($time_ago, 'UTF-8') - 4);
            echo "$time_ago on fordrive";
        }
    }
    
    function time_ago_splitted($source_datetime)
    {
        $full_time_ago  = Datetime_Converter::get_time_ago($source_datetime);
        $time_ago_parts = explode(" ", $full_time_ago);
        
        $time_ago_label    = $time_ago_parts[0];
        array_shift($time_ago_parts);
        $time_ago_sublabel = implode(" ", $time_ago_parts);
        
        return array($time_ago_label, ucfirst($time_ago_sublabel));
    }
    
    function views_to_compact_form($views_count)
    {
        if($views_count < 1000)
            return $views_count;
        else
            return floor($views_count / 1000) . "k";
    }
    
    function replace_new_lines($text)
    {
        $php_eol_windows = "\r\n";
        $php_eol_linux   = "\n";
        $text = "<p class='first'>" . preg_replace("~(" . $php_eol_windows . "|" . $php_eol_linux . "){2,}~u",
                                                                  "</p><p class='newparagraph'>", 
                                                                  $text)
                    . "</p>";
        
        return $text;
    }
    
    function short_month($month_index)
    {
        $months = array(
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        );
        
        echo $months[$month_index];
    }

    function full_month($month_index)
    {
        $months = array(
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        );

        echo $months[$month_index];
    }
    
    /** 
    * Prints shared variables between php and javascript.
    * (Example: "var javascript_var = '<?php echo $php_var; ?>';)
    **/
    function php_to_js($currentController, $currentAction)
    {
        $session = Registry::get('session');
        
        if($session->is_modal_show_confirmation_set())
        {
            $modal_show_confirmation = "true";
            $session->unset_modal_show_confirmation();
        }
        else
        {
            $modal_show_confirmation = "false";
        }
        
        echo "<script>
                    php_vars = {
                        base_url: '"               . Url::get_base_url() . "',
                        modal_show_confirmation: " . $modal_show_confirmation . ",
                        current_controller: '" . $currentController . "',
                        current_action: '" . $currentAction . "',
                        isLoggedIn: " . (($session->is_logged_in()) ? "true" : "false") . "
                    }
              </script>";
    }
    
    // Displays absolute link in frontend
    // TODO rename public_link
    function public_link($url_segments = "",
                      $render       = true)
    {
        if($render)
            echo Url::link($url_segments);
        else
            return Url::link($url_segments);
    }
    
    // Generates module catalog link
    function module_link($controller  = "",
                                $action      = "",
                                $category    = false,
                                $subcategory = false,
                                $page        = 1,
                                $sort        = "year-desc")
    {
        public_link(Url::make_module_segments($controller,
                                                          $action,
                                                          $category,
                                                          $subcategory,
                                                          $page,
                                                          $sort));
    }
    
    // Generates drive link
    function drive_link($module      = "photos",
                              $page        = 1,
                              $category    = false,
                              $subcategory = false)
    {
        public_link(Url::make_drive_segments($module,
                                                         $page,
                                                         $category,
                                                         $subcategory));
    }

    // Generates profile link
    function profile_link($user_id     = false,
                                 $module      = "photos",
                                $page        = 1,
                                $category    = false,
                                $subcategory = false)
    {
        public_link(Url::make_profile_segments($user_id,
                                                           $module,
                                                           $page,
                                                           $category,
                                                           $subcategory));
    }
    
     /**********************************************
     **            Photoset  helpers              **
     **********************************************/
    
    function photoset_item_link($photoset    = false,
                                         $category    = false,
                                         $subcategory = false)
    {
        $category_id = false;
        $seo_parts   = array();
        $seo_parts[] = $photoset->year;
        
        if($category)
        {
            $seo_parts[] = $category->name;
            $category_id = $category->id;
            
            if($subcategory)
            {
                $seo_parts[] = $subcategory->name;
                $category_id = $subcategory->id;
            }
        }
        
        if($photoset->name)
        {
            $seo_parts[] = $photoset->name;
        }
        
        public_link(Url::make_item_segments( "photos",
                                                         $seo_parts,
                                                         $category_id,
                                                         $photoset->id));
    }
    
    function photoset_full_name($photoset)
    {
        stringify(array(
            $photoset->year,
            $photoset->category_name,
            $photoset->subcategory_name,
            $photoset->name
        ));
    }
    
     /**********************************************
     **               Spot helpers                **
     **********************************************/
    
    function spot_item_link($spot        = false,
                                    $category    = false,
                                    $subcategory = false)
    {
        $category_id = false;
        $seo_parts   = array();
        
        if($category)
        {
            $seo_parts[] = $category->name;
            $category_id = $category->id;
            
            if($subcategory)
            {
                $seo_parts[] = $subcategory->name;
                $category_id = $subcategory->id;
            }
        }
        
        if($spot->album_name)
        {
            $seo_parts[] = $spot->album_name;
        }
        
        public_link(Url::make_item_segments( "spots",
                                                         $seo_parts,
                                                         $category_id,
                                                         $spot->id));
    }
    
    function spot_full_name($spot)
    {
        stringify(array(
            $spot->category_name,
            $spot->subcategory_name,
            $spot->album_name
        ));
    }
    
     /**********************************************
     **               Speed helpers               **
     **********************************************/
     
    function speed_item_link($speed       = false,
                                     $category    = false,
                                     $subcategory = false)
    {
        $category_id = false;
        $seo_parts   = array();
        
        if($category)
        {
            $seo_parts[] = $category->name;
            $category_id = $category->id;
            
            if($subcategory)
            {
                $seo_parts[] = $subcategory->name;
                $category_id = $subcategory->id;
            }
        }
        
        $seo_parts[] = $speed->heading;
        public_link(Url::make_item_segments( "speed",
                                                         $seo_parts,
                                                         $category_id,
                                                         $speed->id));
    }
    
     /**********************************************
     **               Video helpers               **
     **********************************************/
    function video_item_link($video       = false,
                                     $category    = false,
                                     $subcategory = false)
    {
        $category_id = false;
        $seo_parts   = array();
        
        if($category)
        {
            $seo_parts[] = $category->name;
            $category_id = $category->id;
            
            if($subcategory)
            {
                $seo_parts[] = $subcategory->name;
                $category_id = $subcategory->id;
            }
        }
        
        $seo_parts[] = $video->heading;
        public_link(Url::make_item_segments( "videos",
                                                         $seo_parts,
                                                         $category_id,
                                                         $video->id));
    }

     /**********************************************
     **                 Main helpers              **
     **********************************************/
    function render_most_active_module_item_link($module_item,
                                                                $selected_module)
    {
        switch($selected_module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $module_item->id;
                $module_object->year = $module_item->year;
                $module_object->name = $module_item->name;
                photoset_item_link($module_object,
                                         $module_item->category,
                                         $module_item->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $module_item->id;
                $module_object->album_name = $module_item->album_name;
                spot_item_link($module_object,
                                    $module_item->category,
                                    $module_item->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                speed_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                video_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;
        }
    }

    function render_main_module_item_link($module_item,
                                                      $selected_module)
    {
        switch($selected_module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $module_item->id;
                $module_object->year = $module_item->year;
                $module_object->name = $module_item->name;
                photoset_item_link($module_object,
                                         $module_item->category,
                                         $module_item->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $module_item->id;
                $module_object->album_name = $module_item->album_name;
                spot_item_link($module_object,
                                    $module_item->category,
                                    $module_item->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                speed_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                video_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;
        }
    }

    function parse_main_activity_event_module($module)
    {
        switch($module)
        {
            case "photos":
                return "photoset";
            break;

            case "spots":
                return "spot";
            break;

            case "speed":
                return "speed";
            break;

            case "videos":
                return "video";
            break;
        }
    }

    function parse_main_activity_event_header($activity)
    {
        $header = "";
        $module = $activity->module;
        $type   = $activity->type;

        if($activity->user)
        {
            $username  = $activity->user->username;
            $header   .= $username . " ";
        }

        switch($type)
        {
            case "comment":
                $header .= "commented ";
                $header .= parse_main_activity_event_module($module);
            break;

            case "answer":
                $header .= "answered comment";
            break;
        }

        return $header;
    }

    function render_main_activity_full_name($post)
    {
        switch($post->module)
        {
            case "photos":
                $module_object                   = new stdClass;
                $module_object->year             = $post->heading;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->name             = $post->secondary_heading;
                photoset_full_name($module_object);
            break;

            case "spots":
                $module_object                   = new stdClass;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->album_name       = $post->heading;
                spot_full_name($module_object);
            break;

            case "speed":
                echo $post->heading;
            break;

            case "videos":
                echo $post->heading;
            break;
        }
    }

    function render_main_activity_module_item_link($post)
    {
        switch($post->module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $post->id;
                $module_object->year = $post->heading;
                $module_object->name = $post->secondary_heading;
                photoset_item_link($module_object,
                                         $post->category,
                                         $post->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $post->id;
                $module_object->album_name = $post->heading;
                spot_item_link($module_object,
                                    $post->category,
                                    $post->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $post->id;
                $module_object->heading = $post->heading;
                speed_item_link($module_object,
                                     $post->category,
                                     $post->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $post->id;
                $module_object->heading = $post->heading;
                video_item_link($module_object,
                                     $post->category,
                                     $post->subcategory);
            break;
        }
    }

     /**********************************************
     **               Follow helpers              **
     **********************************************/
     function parse_follow_post_type($type)
     {
        switch($type)
        {
            case "newpost":
                return "added new";
            break;
            
            case "comment":
                return "commented";
            break;
            
            case "like":
                return "liked";
            break;
        }
     }
     
     function parse_follow_post_module($module)
     {
        switch($module)
        {
            case "photos":
                return "photoset";
            break;
            
            case "spots":
                return "spot";
            break;
            
            case "speed":
                return "speed";
            break;
            
            case "videos":
                return "video";
            break;
        }
     }
     
     function render_follow_post_full_name($post)
     {
        switch($post->module)
        {
            case "photos":
                $module_object                   = new stdClass;
                $module_object->year             = $post->heading;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->name             = $post->secondary_heading;
                photoset_full_name($module_object);
            break;
            
            case "spots":
                $module_object                   = new stdClass;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->album_name       = $post->heading;
                spot_full_name($module_object);
            break;
            
            case "speed":
                echo $post->heading;
            break;
            
            case "videos":
                echo $post->heading;
            break;
        }
     }
     
     function render_follow_post_module_item_link($post)
     {
            switch($post->module)
            {
                case "photos":
                    $module_object       = new stdClass;
                    $module_object->id   = $post->id;
                    $module_object->year = $post->heading;
                    $module_object->name = $post->secondary_heading;
                    photoset_item_link($module_object,
                                             $post->category,
                                             $post->subcategory);
                break;
                
                case "spots":
                    $module_object             = new stdClass;
                    $module_object->id         = $post->id;
                    $module_object->album_name = $post->heading;
                    spot_item_link($module_object,
                                        $post->category,
                                        $post->subcategory);
                break;
                
                case "speed":
                    $module_object          = new stdClass;
                    $module_object->id      = $post->id;
                    $module_object->heading = $post->heading;
                    speed_item_link($module_object,
                                         $post->category,
                                         $post->subcategory);
                break;
                
                case "videos":
                    $module_object          = new stdClass;
                    $module_object->id      = $post->id;
                    $module_object->heading = $post->heading;
                    video_item_link($module_object,
                                         $post->category,
                                         $post->subcategory);
                break;
            }
     }

    /**********************************************
    **             Activity helpers              **
    **********************************************/
    function render_activity_module_photo_label($module,
                                                              $type)
    {
        if($type == "answer")
            return ucfirst($module);
        else
            return "Your " . ucfirst(parse_activity_post_module($module));
    }

    function parse_activity_post_module($module)
    {
        switch($module)
        {
            case "photos":
                return "photoset";
            break;

            case "spots":
                return "spot";
            break;

            case "speed":
                return "speed";
            break;

            case "videos":
                return "video";
            break;
        }
    }

    function parse_activity_post_header($module,
                                                    $type)
    {
        $header = "";

        switch($type)
        {
            case "comment":
                $header .= "commented your ";
                $header .= parse_activity_post_module($module);
            break;

            case "like":
                $header .= "liked your ";
                $header .= parse_activity_post_module($module);
            break;

            case "answer":
                $header .= "answered your comment";
            break;
        }

        return $header;
    }

    function render_activity_post_full_name($post)
    {
        switch($post->module)
        {
            case "photos":
                $module_object                   = new stdClass;
                $module_object->year             = $post->heading;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->name             = $post->secondary_heading;
                photoset_full_name($module_object);
            break;

            case "spots":
                $module_object                   = new stdClass;
                $module_object->category_name    = $post->category_name;
                $module_object->subcategory_name = $post->subcategory_name;
                $module_object->album_name       = $post->heading;
                spot_full_name($module_object);
            break;

            case "speed":
                echo $post->heading;
            break;

            case "videos":
                echo $post->heading;
            break;
        }
    }

    function render_activity_post_module_item_link($post)
    {
        switch($post->module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $post->id;
                $module_object->year = $post->heading;
                $module_object->name = $post->secondary_heading;
                photoset_item_link($module_object,
                                         $post->category,
                                         $post->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $post->id;
                $module_object->album_name = $post->heading;
                spot_item_link($module_object,
                                    $post->category,
                                    $post->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $post->id;
                $module_object->heading = $post->heading;
                speed_item_link($module_object,
                                     $post->category,
                                     $post->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $post->id;
                $module_object->heading = $post->heading;
                video_item_link($module_object,
                                     $post->category,
                                     $post->subcategory);
            break;
        }
    }

    /**********************************************
     **             Favorites helpers            **
     **********************************************/
    function render_favorites_module_item_link($favorite)
    {
        $module_item = $favorite->module_item;

        switch($favorite->module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $module_item->id;
                $module_object->year = $module_item->year;
                $module_object->name = $module_item->name;
                photoset_item_link($module_object,
                                         $module_item->category,
                                         $module_item->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $module_item->id;
                $module_object->album_name = $module_item->album_name;
                spot_item_link($module_object,
                                    $module_item->category,
                                    $module_item->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                speed_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                video_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;
        }
    }

    /**********************************************
     **             MyDrive helpers              **
     **********************************************/
    function render_mydrive_module_item_link($module_item,
                                                          $selected_module)
    {
        switch($selected_module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $module_item->id;
                $module_object->year = $module_item->year;
                $module_object->name = $module_item->name;
                photoset_item_link($module_object,
                                         $module_item->category,
                                         $module_item->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $module_item->id;
                $module_object->album_name = $module_item->album_name;
                spot_item_link($module_object,
                                    $module_item->category,
                                    $module_item->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                speed_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                video_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;
        }
    }

    /**********************************************
     **             Profile helpers              **
     **********************************************/
    function render_profile_module_item_link($module_item,
                                                          $selected_module)
    {
        switch($selected_module)
        {
            case "photos":
                $module_object       = new stdClass;
                $module_object->id   = $module_item->id;
                $module_object->year = $module_item->year;
                $module_object->name = $module_item->name;
                photoset_item_link($module_object,
                                         $module_item->category,
                                         $module_item->subcategory);
            break;

            case "spots":
                $module_object             = new stdClass;
                $module_object->id         = $module_item->id;
                $module_object->album_name = $module_item->album_name;
                spot_item_link($module_object,
                                    $module_item->category,
                                    $module_item->subcategory);
            break;

            case "speed":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                speed_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;

            case "videos":
                $module_object          = new stdClass;
                $module_object->id      = $module_item->id;
                $module_object->heading = $module_item->heading;
                video_item_link($module_object,
                                     $module_item->category,
                                     $module_item->subcategory);
            break;
        }
    }

     /**********************************************
     **           Common module helpers           **
     **********************************************/
    
    function full_category_name($module_item_object)
    {
        stringify(array(
            $module_item_object->category_name,
            $module_item_object->subcategory_name
        ));
    }
    
    function pack_resolutions_for_gallery($lazy_clones_array,
                                                      $render = true)
    {
        $resolutions = array();
        
        foreach($lazy_clones_array as $lazy_clone_array)
        {
            $lazy_clone = (object) $lazy_clone_array;
            
            if($lazy_clone->exists)
            {
                $resolution  = $lazy_clone->width;
                $resolution .= "-";
                $resolution .= $lazy_clone->height;
                
                $resolutions[] = $resolution;
            }
        }
        
        if($render)
            echo implode("|", $resolutions);
        else
            return implode("|", $resolutions);
    }
?>