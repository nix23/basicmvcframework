<?php
    class Admin_Speed_Controller extends Admin_Controller
    {
        public function index($page        = 1, 
                                     $sort        = "moderated-asc", 
                                     $category_id = false)
        {
            $category_model = new Category_Model;
            
            if($category_id)
            {
                if(!$category_model->find_selected_categories($category_id,
                                                                             true,
                                                                             "Speed_Model"))
                {
                    $error = new Error_Controller;
                    $error->show_404();
                }
            }
            
            list($order_by, $direction) = explode("-", $sort); 
            
            $sort_items = array(
                (object) array("name" => "moderated", "type" => "moderated", "direction" => "asc",  "selected" => false),
                (object) array("name" => "reads",     "type" => "views",     "direction" => "desc", "selected" => false),
                (object) array("name" => "activity",  "type" => "activity",  "direction" => "desc", "selected" => false)
            );
            
            foreach($sort_items as $sort_item)
            {
                if($sort_item->type == $order_by)
                {
                    $sort_item->direction = ($direction == "asc") ? "desc" : "asc";
                    $sort_item->selected  = true;
                }
                
                $sort_item->sort = $sort_item->type . "-" . $sort_item->direction;
            }
            
            $speed_model = new Speed_Model;
            
            // Replacing URL sorting names with db names
            $order_by  = str_replace("views",  "views_count",     $order_by);
            $direction = mb_strtoupper($direction, "utf-8");
            
            $speeds = $speed_model->find_all_speeds($category_model->in_categories,
                                                                 $page,
                                                                 $order_by,
                                                                 $direction);
            
            foreach($speeds as $speed)
            {
                $speed->find_category_and_subcategory();
                $speed->find_main_photo();
                $speed->find_views_count();
            }
            
            $data['speeds']               = $speeds;
            $data['pages']                = $speed_model->pagination->make_pages("compact");
            $data['current_page']         = $page;
            $data['categories']           = $category_model->get_not_empty_root_categories_by_module("speeds", "Speed_Model");
            $data['selected_category']    = $category_model->selected_category;
            $data['selected_subcategory'] = $category_model->selected_subcategory;
            $data['sort_items']           = $sort_items;
            $data['selected_sort']        = $sort;
            $data['settings']             = $this->view->settings;
            
            $this->view->content = View::capture("speeds" . DS . "speeds_list", $data, true, array("settings"));
        }
        
        public function delete( $request_type  = "",
                                        $id            = 0,
                                        $page          = 1,
                                        $sort          = false,
                                        $category_id   = false)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $speed_model = new Speed_Model;
            $speed       = $speed_model->find_by_id($id);
            
            if($speed)
            {
                $speed->delete();
                
                $category_model = new Category_Model;
                if($category_id)
                {
                    $category_model->find_selected_categories($category_id,
                                                                            true,
                                                                            "Speed_Model");
                }
                
                list($order_by, $direction) = explode("-", $sort);
                
                // Replacing URL sorting names with db names
                $order_by  = str_replace("name",  "heading", $order_by);
                $direction = mb_strtoupper($direction, "utf-8");
                
                $speeds = $speed_model->find_all_speeds($category_model->in_categories,
                                                                     $page,
                                                                     $order_by,
                                                                     $direction,
                                                                     false);
                
                // If now this page has 0 items and it isn't first page,
                // we are redirecting to previous page
                if(!$speeds and $page != 1)
                {
                    $page--;
                    $url_segments = Url::make_module_segments("speed",
                                                                            "index",
                                                                            $category_model->selected_category,
                                                                            $category_model->selected_subcategory,
                                                                            $page,
                                                                            $sort);
                    $this->ajax->callback           = "redirect";
                    $this->ajax->data->url_segments = $url_segments;
                }
                // Else we should update pagination and items
                else
                {
                    foreach($speeds as $speed)
                    {
                        $speed->find_category_and_subcategory();
                        $speed->find_main_photo();
                        $speed->find_views_count();
                    }
                    
                    $data['speeds']               = $speeds;
                    $data['pages']                = $speed_model->pagination->make_pages("compact");
                    $data['current_page']         = $page;
                    $data['selected_category']    = $category_model->selected_category;
                    $data['selected_subcategory'] = $category_model->selected_subcategory;
                    $data['selected_sort']        = $sort;
                    
                    $this->ajax->callback              = "update_items_and_pagination_html";
                    $this->ajax->data->items_html      = View::capture("speeds" . DS . "speeds_list_items",      $data);
                    $this->ajax->data->pagination_html = View::capture("speeds" . DS . "speeds_list_pagination", $data);
                }
                
                $this->ajax->result = "ok";
                $this->ajax->render();
            }
            else
            {
                $message  = "Wrong item id passed to delete action, ";
                $message .= "or user is just deleted this record.   ";
                exit($message);
            }
        }
        
        public function change_status($request_type = "", 
                                                $id = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $speed_model = new Speed_Model;
            $speed       = $speed_model->find_by_id($id);
            
            if($speed)
            {
                if($speed->change_status())
                {
                    $this->ajax->result       = 'ok';
                    $this->ajax->callback     = 'update_status';
                    $this->ajax->data->status = $speed->status;
                    
                    $this->ajax->render();
                }
            }
            else
            {
                $message  = "Wrong item id passed to change_status action, ";
                $message .= "or user is just deleted this record.          ";
                exit($message);
            }
        }
        
        public function change_moderation($request_type = "", 
                                                     $id = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $speed_model = new Speed_Model;
            $speed       = $speed_model->find_by_id($id);
            
            if($speed)
            {
                if($speed->change_moderation())
                {
                    $this->ajax->result            = 'ok';
                    $this->ajax->callback          = 'update_caption';
                    $this->ajax->data->new_caption = $speed->moderated;
                    
                    $this->ajax->render();
                }
            }
            else
            {
                $message  = "Wrong item id passed to change_moderation action, ";
                $message .= "or user is just deleted this record.              ";
                exit($message);
            }
        }
        
        public function form($id          = false,
                                    $category_id = false)
        {
            // Capturing all users
            $user          = new User_Model;
            $data['users'] = $user->find_all();
            
            // Capturing module root categories
            $category_model     = new Category_Model;
            $data['categories'] = $category_model->get_categories_by_module(0, "speed");
            
            // Remember opened category
            if($category_id)
                $data['selected_category_id'] = $category_id;
            else
                $data['selected_category_id'] = false;
            
            $speed_model = new Speed_Model;
            
            if($id and $id != "add")
            {
                $speed = $speed_model->find_by_id($id);
                
                if($speed)
                {
                    $data['speed']       = $speed;
                    $data['action']     = "Edit";
                    $data['is_editing'] = true;
                    
                    $speed->find_attached_photos();
                    $speed->find_main_photo();
                    
                    if($speed->photos)
                    {
                        $speed->photos = array_reverse($speed->photos);
                        
                        foreach($speed->photos as $photo)
                        {
                            $photo->unpack_directory();
                        }
                    }
                    
                    $speed->find_category_and_subcategory();
                    
                    if($speed->subcategory)
                    {
                        $speed->category->find_subcategories();
                    }
                }
                else
                {
                    $error = new Error_Controller;
                    $error->show_404();
                }
            }
            else
            {
                // If some categories were opened,we need
                // open them on form at start.
                if($category_id)
                {
                    $speed_model->category_id = $category_id;
                    $speed_model->find_category_and_subcategory();
                    
                    if($speed_model->category->has_subcategories())
                    {
                        $speed_model->category->find_subcategories();
                        
                        if(!$speed_model->subcategory)
                        {
                            $speed_model->subcategory = new Category_Model;
                            $speed_model->category_id = "";
                        }
                    }
                }
                
                $data['speed']       = $speed_model;
                $data['action']     = "Add new";
                $data['is_editing'] = false;
            }

            $data['settings']    = $this->view->settings;
            $this->view->content = View::capture("speeds" . DS . "speed_form", $data, true, array("settings"));
        }
        
        public function load_subcategories( $request_type = "", 
                                                        $id = false)
        { 
            $this->is_ajax($request_type);
            
            if($id)
            {
                $category_model = new Category_Model; 
                $category       = $category_model->find_by_id($id); 
                
                if($category)
                { 
                    $category->find_subcategories(); 
                    $subcategories = array();
                    
                    if($category->subcategories)
                    {
                        foreach($category->subcategories as $subcategory)
                        {
                            $subcategories[] = array($subcategory->id, $subcategory->name);
                        }
                    } 
                    
                    $this->ajax->result              = "ok";
                    $this->ajax->callback            = "parse_select_subcategories";
                    $this->ajax->data->subcategories = $subcategories;
                    
                    $this->ajax->render(); 
                }
            }
        }
        
        public function upload_photo($request_type = "")
        {
            $this->is_ajax($request_type);
            $image_uploader = new Fordrive_Uploader(800, 600);
            
            if(!$image_uploader->is_file_uploaded('upload-file')) 
            { 
                $this->ajax->errors->file_upload_error = $image_uploader->error; 
            }
            else
            {
                if($image_uploader->attach_file($_FILES['upload-file']))
                {
                    $speed_photo_model = new Speed_Photo_Model;
                    $clones            = $speed_photo_model->clones;
                    
                    if($image_uploader->upload_photo($clones))
                    {
                        $this->ajax->result                   = "ok";
                        $this->ajax->callback                 = "insert_uploaded_photo";
                        $this->ajax->data->master_photo_name  = $image_uploader->master_photo_name;
                        $this->ajax->data->photo_extension    = "jpg";
                        $this->ajax->data->spinner_id         = "photo-upload-spinner";
                    }
                    else
                    {
                        $this->ajax->errors->file_upload_error = $image_uploader->error;
                    }
                }
                else
                {
                    $this->ajax->errors->file_upload_error = $image_uploader->error;
                }
            }
            
            $this->ajax->render();
        }
        
        public function save($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $speed = new Speed_Model;
            
            $speed->bind($this->input->post("speed"));
            $speed->validate();
            
            $photos = $this->input->post("speed-photos", "no-photos");
            $uploaded_photos_count = 0;
            
            if($photos == "no-photos")
            {
                $this->model_errors->set("photo_required", "Please add at least one photo.");
            }
            // Check,that at least one photo exists.
            // (Frames 'delete' and 'deleteajax' will delete that photos)
            else
            {
                $no_photos = true;
                foreach($photos as $photo)
                {
                    if(!preg_match("~^delete|deleteajax~u", $photo["frame"]))
                    {
                        $no_photos = false;
                        $uploaded_photos_count++;
                    }
                }
                
                if($no_photos)
                {
                    $this->model_errors->set("photo_required", "Please add at least one photo.");
                }
            }
            
            $speed->validate_article_tags($uploaded_photos_count);
            $this->model_errors->ajaxify_if_has_errors();

            if($speed->moderated == "yes")
            {
                $speed->moderation_fail_text = "";
            }
            
            $update_exceptions = array();
            $special_form      = $this->input->post("special");
            if($special_form["update_postdate"] == "no")
                $update_exceptions[] = "posted_on";

            if($speed->save(false, $update_exceptions))
            {
                // Processing photos
                foreach($photos as $photo)
                {
                    $speed_photo_model = new Speed_Photo_Model;
                    
                    $speed_photo_model->unpack_frame($photo["frame"]);
                    $speed_photo_model->bind_id($speed, "speed_id"); 
                    
                    switch($speed_photo_model->frame_action)
                    {
                        case "ajax":
                            $speed_photo_model->save_with_clones();
                        break;
                        
                        case "deleteajax":
                            $speed_photo_model->delete_ajax();
                        break;
                        
                        case "delete":
                            $speed_photo_model->delete_with_clones();
                        break;
                        
                        case "none":
                            // No photo processing required
                        break;
                    }
                }
                
                // Updating main photo
                $speed_photo_model = new Speed_Photo_Model;
                
                $speed_photo_model->bind($this->input->post("main_photo"));
                $speed_photo_model->bind_id($speed, "speed_id");
                
                $speed_photo_model->update_main();
                
                // Save succesfull
                $this->session->set_modal_show_confirmation(); 
                
                // If some categories were opened,redirecting to them
                $category             = $this->input->post("category");
                $selected_category_id = $category["selected_category_id"];
                
                if($selected_category_id)
                {
                    $category_model = new Category_Model;
                    $category_model->find_selected_categories($selected_category_id);
                    
                    $url_segments = Url::make_module_segments("speed",
                                                                            "index",
                                                                            $category_model->selected_category,
                                                                            $category_model->selected_subcategory);
                }
                else
                {
                    $url_segments = "speed";
                }
                
                // Building response
                $this->ajax->data->url_segments = $url_segments;
                $this->ajax->result             = 'ok';
                $this->ajax->callback           = 'redirect';
                
                $this->ajax->render();
            }
        }
    }
?>