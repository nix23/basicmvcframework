<?php
    /** 
    * Application router. Gets URl,and calls necessary controller.
    * Routing settings are located in file 'config/routing.php'
    **/
    class Router 
    {
        private $controller;
        private $action;
        private $controller_parametrs = array();
        private $url;
        private $url_segments = array();
        private $is_backend = false;
        private $admin_panel_url;
        private $routes;
        private $input;
        private $config;
        private $database;
        
        public function __construct() 
        {
            $this->input    = Registry::get('input');
            $this->config   = Registry::get('config');
            $this->database = Registry::get('database');
            
            // If url parametr is missing,function
            // will return value 'load_default_route'
            $this->url             = $this->input->get('url', 'load_default_route');
            $this->admin_panel_url = $this->config->admin_panel_url;
            
            // If no parametrs are passed,or passed empty url,
            // then we need to load frontend
            if($this->url == "load_default_route" || empty($this->url))
            {
                if($this->is_site_in_maintenance_mode())
                    $this->load_maintenance_mode();

                $this->controller = $this->config->default_frontend_controller;
                $this->action     = $this->config->default_frontend_action;
            }
            else
            {
                $this->make_url_segments();

                if(!$this->is_backend())
                {
                    if($this->is_site_in_maintenance_mode())
                        $this->load_maintenance_mode();
                }
                
                $this->load_controller();
                $this->load_action();
                $this->load_parametrs();
            }
            
            $this->run_url();
        }
        
        private function make_url_segments() 
        { 
            // Split url to segments
            $this->url_segments = explode("/", $this->url); 
            
            // Checking,if url is query to backend
            if(preg_match("~^{$this->admin_panel_url}$~u", $this->url_segments[0])) 
            {
                // Sets backend flag
                $this->is_backend = true;
                
                // Deletes backend segment
                array_shift($this->url_segments);
            }
            
            // Building url from segments
            $this->url = implode("/", $this->url_segments);
            
            // And run it through all routes
            $this->route_url();
            
            // After routing again splitting url to segments
            $this->url_segments = explode("/", $this->url);
        }

        // If site is disabled,rendering special 'maintenance_mode' template.
        // Model logic is used here,because site core should be
        // independent of /application folder. This is required,
        // because when we are updating our site,this template
        // will be unavailable only while and if we are updating /core.
        // So,while we are updating our application,users always will see
        // site maintenance page from application frontend.
        private function is_site_in_maintenance_mode()
        {

            $sql          = "SELECT mode FROM settings WHERE name='main_settings'";
            $settings_row = $this->database->query($sql);
            $settings     = $this->database->fetch_array($settings_row);

            return ($settings["mode"] == "disabled") ? true : false;
        }

        private function load_maintenance_mode()
        {
//          $sql                   = "SELECT offline_image_binary FROM settings WHERE name='main_settings'";
//          $settings_row          = $this->database->query($sql);
//          $settings              = $this->database->fetch_array($settings_row);
//          $data["offline_image"] = base64_encode($settings["offline_image_binary"]);
            $data["offline_image"] = base64_encode(file_get_contents(CORE . "maintenance" . DS . "maintenance_logo.jpg"));

            View::set_base_path(CORE);
            View::render(View::capture("maintenance_mode", $data));
            exit();
        }
        
        private function run_url()
        {
            if($this->is_backend())
            {
                $controller_name = "Admin_" . ucfirst($this->controller) . "_Controller";
            }
            else
            {
                // Checking for name collisions with abstract classes Public and Admin Controller
                if(strtolower($this->controller) == 'admin')  $this->controller = "Not_Found";
                if(strtolower($this->controller) == 'public') $this->controller = "Not_Found";
                
                $controller_name = ucfirst($this->controller) . "_Controller";
            }
            
            $controller = new $controller_name;
            
            if(method_exists($controller_name, $this->action))
            {
                $controller->setRouterData($this->controller, $this->action);
                $controller->before();
                call_user_func_array(array($controller, $this->action), $this->controller_parametrs);
                $controller->after();
            }
            else
            {
                $error = new Error_Controller;
                $error->show_404();
            }
        }
        
        private function load_controller()
        {
            if(!isset($this->url_segments[0]) || empty($this->url_segments[0]))
            {
                if($this->is_backend())
                {
                    $this->controller = $this->config->default_backend_controller; 
                }
                else
                {
                    $this->controller = $this->config->default_frontend_controller;
                }
            }
            else
            {
                $this->controller = $this->url_segments[0];
                array_shift($this->url_segments); 
            }
        }
        
        private function load_action()
        {
            if(!isset($this->url_segments[0]) || empty($this->url_segments[0]))
            {
                if($this->is_backend())
                {
                    $this->action = $this->config->default_backend_action;
                }
                else
                {
                    $this->action = $this->config->default_frontend_action;
                }
            }
            else
            {
                $this->action = $this->url_segments[0];
                array_shift($this->url_segments);

                // Processing special methods(Restricted as URL action name)
                if($this->action == "before" or $this->action == "after")
                {
                    if($this->is_backend())
                        $this->action = $this->config->default_backend_action;
                    else
                        $this->action = $this->config->default_frontend_action;
                }
            }
        }
        
        private function load_parametrs()
        {
            if(isset($this->url_segments[0]) && !empty($this->url_segments[0]))
            {
                $this->controller_parametrs = $this->url_segments; 
            }
        }
        
        private function route_url() 
        {
            // Connecting routes file
            $routes_directory = ROOT . DS . 'config' . DS;
            
            ($this->is_backend()) ? $file = 'admin_routes' : $file = 'routes';
            
            $this->routes = include($routes_directory . $file . ".php");
            
            $url_routed = false;
            
            foreach($this->routes as $routes_batch) 
            { 
                foreach($routes_batch as $pattern => $route) 
                {
                    if(preg_match("~^{$pattern}$~u", $this->url)) 
                    { 
                        $this->url = preg_replace("~^{$pattern}$~u", $route, $this->url);
                        $url_routed = true;
                        
                        break;
                    } 
                }
                
                if($url_routed) break;
            }
        }
        
        public function is_backend()
        {
            return ($this->is_backend) ? true : false;
        }
    }
?>