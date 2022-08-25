<?php

/**
 * Methods for working with site url
 * and site links
 **/
class Url
{
    // Check how on hosting url maps to docroot
    private static $base_url = "";
    private static $is_backend = null;

    // Sets site base url
    private static function set_base_url()
    {
        $hostname = $_SERVER['HTTP_HOST'];

        // TO DO
        // This function should return http://localhost/app_path/ on localhost,
        // or http://www.mysitename.com/ in production
        if ($hostname == "localhost" || $hostname == "127.0.0.1") {
            $hostname .= "/fordrive";
        }

        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
            $protocol = "https";
        } else {
            $protocol = "http";
        }

        self::$base_url = $protocol . "://" . $hostname . "/";
    }

    // Get site base url
    public static function get_base_url()
    {
        if (empty(self::$base_url)) {
            self::set_base_url();
        }

        return self::$base_url;
    }

    // Detects,if url is query to backend
    private static function detect_frontend_or_backend()
    {
        $input = Registry::get('input');
        $config = Registry::get('config');

        $url = $input->get('url', 'not_defined');
        $admin_panel_url = $config->admin_panel_url;

        $url_segments = explode("/", $url);
        $url = $url_segments[0];

        if (preg_match("~^{$admin_panel_url}$~u", $url)) {
            self::$is_backend = true;
        } else {
            self::$is_backend = false;
        }
    }

    // Checks,if url is query to backend
    public static function is_backend()
    {
        if (is_null(self::$is_backend)) {
            self::detect_frontend_or_backend();
        }

        return (self::$is_backend) ? true : false;
    }

    // Creates and returns absolute link
    public static function link($url_segments = "")
    {
        if (self::is_backend()) {
            $index_path = "index.php?url=";
        } else {
            // In this application mod_rewrite is always installed
            $index_path = "";
            // if($registry->get->settings("use short path(mod rewrite installed)))
            //     $index_path = "";
            // else
            //     $index_path = "index.php?url="
        }

        $base_url = self::get_base_url();

        $browser_path = $base_url . $index_path . $url_segments;

        return $browser_path;
    }

    // Redirecting to specified url
    public static function redirect($url_segments = "")
    {
        // Send headers?
        $location = self::link($url_segments);

        header("Location: {$location}");
        exit;
    }

    // Filters variable to display correctly
    // in URL(SEO FRIENDLY)
    public static function create_slug($string)
    {
        $slug = preg_replace("~[^a-zA-Z0-9-_ ]~u", "", $string);
        $slug = str_replace(array(" ", "_", "--"), "-", $slug);
        $slug = str_replace("quot", "", $slug);
        $slug = mb_strtolower($slug, "UTF-8");

        return $slug;
    }

    // Returns absolute path to css-file
    public static function css($url = "")
    {
        $browser_path = "";

        $browser_path .= self::get_base_url();
        $browser_path .= "public/";
        $browser_path .= "css/";

        if (self::is_backend()) {
            $browser_path .= "admin/";
        }

        $browser_path .= $url . ".css";

        return $browser_path;
    }

    // Returns absolute path to js file
    public static function js($url = "")
    {
        $browser_path = "";

        $browser_path .= self::get_base_url();
        $browser_path .= "public/";
        $browser_path .= "js/";

        if (self::is_backend()) {
            $browser_path .= "admin/";
        }

        $browser_path .= $url . ".js";

        return $browser_path;
    }

    /**
     * Returns url segments for main page of module catalog
     * in format 'controller/action/cat_name-subcat_name-id/page-num/sort-order'.
     **/
    public static function make_module_segments($controller = "",
                                                $action = "",
                                                $category = false,
                                                $subcategory = false,
                                                $page = 1,
                                                $sort = "moderated-asc")
    {
        $url_segments = "{$controller}/{$action}";

        if ($category) {
            $url_segments .= "/" . self::create_slug($category->name);
            $url_segments .= "-";

            if ($subcategory) {
                $url_segments .= self::create_slug($subcategory->name);
                $url_segments .= "-";
                $url_segments .= $subcategory->id;
            } else {
                $url_segments .= $category->id;
            }
        }

        $url_segments .= "/page-{$page}/sort-{$sort}";
        return $url_segments;
    }

    /**
     * Returns url segments for opened item in some module
     * in format 'controller/seo_parts-category_id-item_id'.
     **/
    public static function make_item_segments($controller = "",
                                              $seo_parts = array(),
                                              $category_id = false,
                                              $item_id = false)
    {
        $url_segments = "$controller/";

        foreach ($seo_parts as $seo_part) {
            $url_segments .= self::create_slug($seo_part) . "-";
        }

        $url_segments .= "$category_id-$item_id";
        return $url_segments;
    }

    /**
     * Returns url segments for drive module
     * in format 'drive/list/module/page-num/cat_name-subcat_name-id'.
     **/
    public static function make_drive_segments($module = "photos",
                                               $page = 1,
                                               $category = false,
                                               $subcategory = false)
    {
        $url_segments = "drive/list/$module/page-$page";

        if ($category) {
            $url_segments .= "/" . self::create_slug($category->name);
            $url_segments .= "-";

            if ($subcategory) {
                $url_segments .= self::create_slug($subcategory->name);
                $url_segments .= "-";
                $url_segments .= $subcategory->id;
            } else {
                $url_segments .= $category->id;
            }
        }

        return $url_segments;
    }

    /**
     * Returns url segments for view profile
     * in format 'profile/view/user-id/module/page-num/cat_name-subcat_name-id'.
     **/
    public static function make_profile_segments($user_id = false,
                                                 $module = "photos",
                                                 $page = 1,
                                                 $category = false,
                                                 $subcategory = false)
    {
        $url_segments = "profile/view/user-$user_id/$module/page-$page";

        if ($category) {
            $url_segments .= "/" . self::create_slug($category->name);
            $url_segments .= "-";

            if ($subcategory) {
                $url_segments .= self::create_slug($subcategory->name);
                $url_segments .= "-";
                $url_segments .= $subcategory->id;
            } else {
                $url_segments .= $category->id;
            }
        }

        return $url_segments;
    }
}

?>