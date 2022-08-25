<?php
    class Services_Controller extends Public_Controller
    {
        private $modules = array("photos", "spots", "speed");

        public function show_sitemap()
        {
                $this->view->content = file_get_contents( VIEWS . "base" . DS . "sitemap.xml" );
                echo $this->view->content;
               exit();
        }

        public function viewphoto($module,
                                          $photo_id,
                                          $photo_width,
                                          $photo_height)
        {
            $this->render_layout = false;

            if(!in_array($module, $this->modules))
            {
                $error_controller = new Error_Controller;
                $error_controller->show_404();
            }

            switch($module)
            {
                case "photos":
                    $module_photo_model = new Photo_Photo_Model;
                    $module_item_model  = new Photo_Model;
                break;

                case "spots":
                    $module_photo_model = new Spot_Photo_Model;
                    $module_item_model  = new Spot_Model;
                break;

                case "speed":
                    $module_photo_model = new Speed_Photo_Model;
                    $module_item_model  = new Speed_Model;
                break;

                case "videos":
                    $module_photo_model = new Video_Photo_Model;
                    $module_item_model  = new Video_Model;
                break;
            }

            $module_photo = $module_photo_model->find_by_id($photo_id);
            if(!$module_photo)
            {
                $error_controller = new Error_Controller;
                $error_controller->show_404();
            }

            switch($module)
            {
                case "photos":
                    $module_item = $module_item_model->find_by_id($module_photo->photo_id);
                    $module_item->find_category_and_subcategory();
                break;

                case "spots":
                    $module_item = $module_item_model->find_by_id($module_photo->spot_id);
                    $module_item->find_category_and_subcategory();
                break;

                case "speed":
                    $module_item = $module_item_model->find_by_id($module_photo->speed_id);
                break;

                case "videos":
                    $module_item = $module_item_model->find_by_id($module_photo->video_id);
                break;
            }

            $module_photo->find_lazy_clones_that_exists();
            if(!$module_photo->does_lazy_clone_exists($photo_width,
                                                                    $photo_height))
            {
                $error_controller = new Error_Controller;
                $error_controller->show_404();
            }

            $photo_filename  = Url::create_slug($module_item->get_full_heading());
            $photo_filename .= "-" . $module_photo->master_name;
            $photo_filename .= "-" . $photo_width . "-" . $photo_height;
            $photo_filename  = str_replace("--", "-", $photo_filename);

            if($module_photo->does_photo_with_specified_sizes_exists_in_fs($photo_filename))
            {
                $url_segments  = "uploads/cache/";
                $url_segments .= $photo_filename . ".jpg";

                URL::redirect($url_segments);
            }
            else
            {
                $data["module"]       = $module;
                $data["photo_id"]     = $photo_id;
                $data["photo_width"]  = $photo_width;
                $data["photo_height"] = $photo_height;

                $this->session->set("generate_photo_filename", $photo_filename);

                $this->view->content = View::capture("services" . DS . "generate_photo", $data);
                View::render($this->view->content);
            }
        }

        public function generate_photo($request_type = "",
                                                 $module       = "",
                                                 $photo_id     = false,
                                                 $photo_width  = false,
                                                 $photo_height = false)
        {
            $this->is_ajax($request_type);

            if(!in_array($module, $this->modules))
            {
                exit("Generate photo error: wrong module name.");
            }

            switch($module)
            {
                case "photos":
                    $module_photo_model = new Photo_Photo_Model;
                break;

                case "spots":
                    $module_photo_model = new Spot_Photo_Model;
                break;

                case "speed":
                    $module_photo_model = new Speed_Photo_Model;
                break;

                case "videos":
                    $module_photo_model = new Video_Photo_Model;
                break;
            }

            $module_photo = $module_photo_model->find_by_id($photo_id);
            if(!$module_photo)
            {
                exit("Generate photo error: wrong photo id.");
            }

            $module_photo->find_lazy_clones_that_exists();
            if(!$module_photo->does_lazy_clone_exists($photo_width,
                                                                    $photo_height))
            {
                exit("Generate photo error: wrong photo sizes.");
            }

            if($this->session->is_set("generate_photo_filename"))
            {
                $photo_filename = $this->session->get("generate_photo_filename");
                $this->session->delete("generate_photo_filename");
            }
            else
            {
                exit("Generate photo error: photo_filename not exists.");
            }

            $module_photo->create_lazy_clone($photo_width,
                                                        $photo_height,
                                                        $photo_filename);
            /*
            if($module_photo->are_all_lazy_clones_created())
            {
                $module_photo->delete_original_photo();
            }
            */

            $url_segments  = "uploads/cache/";
            $url_segments .= $photo_filename . ".jpg";

            $this->ajax->data->url_segments = $url_segments;
            $this->ajax->callback           = "redirect";
            $this->ajax->result             = "ok";
            $this->ajax->render();
        }
        
        /*
        private function load_models_to_map()
        {
            $category_fs_path = "Volvo" . DS;
            $models_to_map    = array();
            $models_to_map[]  = array("subcategory_fs_path" => "s80" . DS . "i" . DS, 
                                              "category_id"         => 8021,
                                              "models"              => array()); 
            $models_to_map[]  = array("subcategory_fs_path" => "s80" . DS . "ii" . DS, 
                                              "category_id"         => 8021,
                                              "models"              => array()); 
            $models_to_map[]  = array("subcategory_fs_path" => "v40" . DS . "i" . DS, 
                                              "category_id"         => 8023,
                                              "models"              => array()); 
            $models_to_map[]  = array("subcategory_fs_path" => "v40" . DS . "ii" . DS, 
                                              "category_id"         => 8023,
                                              "models"              => array());
            $models_to_map[]  = array("subcategory_fs_path" => "v50" . DS, 
                                              "category_id"         => 8024,
                                              "models"              => array());
            
            foreach($models_to_map as &$model_to_map)
                $model_to_map["subcategory_fs_path"] = UPLOADS_DATA . $category_fs_path . $model_to_map["subcategory_fs_path"];
            
            return $models_to_map;
        }
        
        private function extract_all_models_from_each_subcategory($models_to_map)
        {
            foreach($models_to_map as &$model_to_map)
            {
                if($directory_handle = opendir($model_to_map["subcategory_fs_path"]))
                {
                    while(false !== ($filename = readdir($directory_handle)))
                    {
                        if($filename != "." and $filename != "..")
                        {
                            $model_name    = $filename;
                            $model_fs_path = $model_to_map["subcategory_fs_path"] . $model_name . DS;
                            
                            $model_to_map["models"][] = array("model_name"       => $model_name,
                                                                        "model_fs_path"    => $model_fs_path,
                                                                        "images_fs_pathes" => array());
                        }
                    }

                    closedir($directory_handle);
                }
                else
                {
                    exit("Error: Can't open directory '" . $model_to_map["subcategory_fs_path"] . "'");
                }
            }
            
            return $models_to_map;
        }
        
        private function extract_all_image_pathes_from_each_model($models_to_map)
        {
            foreach($models_to_map as &$model_to_map)
            {
                foreach($model_to_map["models"] as &$model)
                {
                    if($directory_handle = opendir($model["model_fs_path"]))
                    {
                        while(false !== ($filename = readdir($directory_handle)))
                        {
                            if($filename != "." and $filename != "..")
                            {
                                $model["images_fs_pathes"][] = $model["model_fs_path"] . $filename;
                            }
                        }

                        closedir($directory_handle);
                    }
                    else
                    {
                        exit("Error: Can't open directory '" . $model_to_map["model_fs_path"] . "'");
                    }
                }
            }
            
            return $models_to_map;
        }
        
        private function is_greater_than_min_size($image_to_map)
        {
            if($image_to_map->width < 800
                    ||
                $image_to_map->height < 600)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        
        private function generate_master_photo_tmp_name_and_path()
        {
            do {
                $unique_photo_id   = uniqid() . mt_rand(0, 99999);
                $master_photo_name = $unique_photo_id;
                $master_photo_path = UPLOADS_TMP . $master_photo_name . ".jpg";
            }
            while(file_exists($master_photo_path)
                        or 
                    file_exists(UPLOADS_IMAGES . $this->settings->current_upload_directory . DS . $master_photo_name));
            
            return array($master_photo_name,
                             $master_photo_path);
        }
        
        private function make_clones($image_to_map,
                                              $master_photo_tmp_name)
        {
            $clones = array(
                array("width" => 800, "height" => 520), // Gallery large photo
                array("width" => 380, "height" => 245), // Module item main photo
                array("width" => 330, "height" => 210), // Most active posts big photo
                array("width" => 270, "height" => 180), // Module item large photo
                array("width" => 135, "height" => 100), // Most active posts small photo
                array("width" => 130, "height" => 90),  // View item small photo
                array("width" => 145, "height" => 95),  // Follow/activity photo
                array("width" => 100, "height" => 75),  // Module item small photo
                array("width" => 80,  "height" => 60),  // Backend list photo
                array("width" => 40,  "height" => 30)   // Main fordrivers small item photo
            );
            
            foreach($clones as $clone)
            {
                // Format: dirname-mastername-width-height.jpg
                $clone_name  = "";
                $clone_name .= $master_photo_tmp_name;
                $clone_name .= "-" . $clone["width"];
                $clone_name .= "-" . $clone["height"];
                
                $image_to_map->resize_image($clone["width"], $clone["height"], "crop");
                $image_to_map->save_as_jpg(UPLOADS_TMP . $clone_name);
            }
        }
        
        private function find_lazy_clones_greatest_sizes($image_to_map)
        {
            $lazy_clones = array(
                array("width" => 1600, "height" => 1200, "exists" => false),
                array("width" => 1280, "height" => 960,  "exists" => false),
                array("width" => 1024, "height" => 768,  "exists" => false),
                array("width" => 800,  "height" => 600,  "exists" => false)
            );
            
            $master_photo_width  = $image_to_map->width;
            $master_photo_height = $image_to_map->height;
            
            foreach($lazy_clones as $lazy_clone)
            {
                if($master_photo_width >= $lazy_clone["width"]
                        and
                    $master_photo_height >= $lazy_clone["height"])
                {
                    $lazy_clone_greatest_width  = $lazy_clone["width"];
                    $lazy_clone_greatest_height = $lazy_clone["height"];
                    break;
                }
            }
            
            return array($lazy_clone_greatest_width,
                             $lazy_clone_greatest_height);
        }
        
        
            // $models_to_map = array(
            //  array(
            //      "subcategory_fs_path" => "path",
            //      "category_id"         => id,
            //      "models"              => array(
            //          array(
            //              "model_name"       => "name",
            //              "model_fs_path"    => "path"
            //              "images_fs_pathes" => array("path1", ..., "patnN")
            //          )
            //      )
            //  )
            // )
        
        public function map_photosets()
        {
            $models_to_map = $this->load_models_to_map();
            $models_to_map = $this->extract_all_models_from_each_subcategory($models_to_map);
            $models_to_map = $this->extract_all_image_pathes_from_each_model($models_to_map);
            
            $map_photoset_models = array();
            foreach($models_to_map as $model_to_map)
            {
                foreach($model_to_map["models"] as $model)
                { 
                    $map_photoset_model = new Map_Photo_Model();
                    
                    $map_photoset_model->user_id     = 112;
                    $map_photoset_model->name        = $model["model_name"];
                    $map_photoset_model->year        = 2013;
                    $map_photoset_model->category_id = $model_to_map["category_id"];
                    $map_photoset_model->posted_on   = strftime("%Y-%m-%d %H:%M:%S", time());
                    $map_photoset_model->status      = "enabled";
                    $map_photoset_model->moderated   = "yes";
                    $main_model_photo                = true;
                    
                    foreach($model["images_fs_pathes"] as $image_fs_path)
                    {
                        $image_to_map = new Image_Resizer();
                        $image_to_map->load($image_fs_path); echo $image_fs_path . "<br>";
                        
                        if(!$this->is_greater_than_min_size($image_to_map))
                            continue;
                        
                        list($master_photo_tmp_name,
                              $master_photo_tmp_path) = $this->generate_master_photo_tmp_name_and_path();
                        $image_to_map->save_original($master_photo_tmp_path);
                        $image_to_map->load($master_photo_tmp_path);
                        list($lazy_clone_greatest_width,
                              $lazy_clone_greatest_height) = $this->find_lazy_clones_greatest_sizes($image_to_map);
                        $this->make_clones($image_to_map, $master_photo_tmp_name);
                        
                        $map_photo_photo_model = new Map_Photo_Photo_Model;
                        
                        $map_photo_photo_model->master_name = $master_photo_tmp_name;
                        
                        if($main_model_photo)
                        {
                            $map_photo_photo_model->main = "yes";
                            $main_model_photo            = false;
                        }
                        else
                        {
                            $map_photo_photo_model->main = "no";
                        }
                        
                        $map_photo_photo_model->lazy_clone_greatest_width  = $lazy_clone_greatest_width;
                        $map_photo_photo_model->lazy_clone_greatest_height = $lazy_clone_greatest_height;
                        
                        $map_photoset_model->map_photo_photos[] = $map_photo_photo_model;
                    }
                    
                    $map_photoset_models[] = $map_photoset_model;
                }
                
                foreach($map_photoset_models as &$map_photoset_model)
                {
                    $map_photoset_model->save();
                    $map_photoset_model->save_photos();
                }
            }
            
            echo "All data have been succesfully mapped.";
            exit();
        }
        
        public function move_photosets()
        {
            $map_photo_model   = new Map_Photo_Model();
            $photosets_to_move = $map_photo_model->find_all();
            
            foreach($photosets_to_move as $photoset)
            {
                $photoset->find_all_photos();
                $photoset->move_to_original();
                $photoset->delete();
            }
            
            echo "All mapped data have been succesfully moved to site.";
            exit();
        }
    }
    */
    }
?>