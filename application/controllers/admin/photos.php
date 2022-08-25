<?php
    class Admin_Photos_Controller extends Admin_Controller
    {
        public function index($page = 1, $sort = "moderated-asc", $category_id = false)
        {
            $category = new Category_Model;
            
            if($category_id)
            {
                if(!$category->find_selected_categories($category_id,
                                                                     true,
                                                                     "Photo_Model"))
                {
                    $error = new Error_Controller;
                    $error->show_404();
                }
            }
            
            list($order_by, $direction) = explode("-", $sort);
            
            $sort_items = array(
                (object) array("type" => "moderated", "direction" => "asc",  "selected" => false),
                (object) array("type" => "year",      "direction" => "desc", "selected" => false),
                (object) array("type" => "activity",  "direction" => "desc", "selected" => false)
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
            
            $photoset  = new Photo_Model;
            $direction = mb_strtoupper($direction, "utf-8");
            $photosets = $photoset->find_all_photosets($category->in_categories,
                                                                     $page,
                                                                     $order_by,
                                                                     $direction);
            
            foreach($photosets as $fordrive_photoset)
            {
                $fordrive_photoset->find_category_and_subcategory();
                $fordrive_photoset->find_main_photo();
                $fordrive_photoset->find_views_count();
            }
            
            $data['photosets']            = $photosets;
            $data['pages']                = $photoset->pagination->make_pages("compact");
            $data['current_page']         = $page;
            $data['categories']           = $category->get_not_empty_root_categories_by_module("photos", "Photo_Model");
            $data['selected_category']    = $category->selected_category;
            $data['selected_subcategory'] = $category->selected_subcategory;
            $data['sort_items']           = $sort_items;
            $data['selected_sort']        = $sort;
            $data['settings']             = $this->view->settings;
            
            $this->view->content = View::capture("photos" . DS . "photos_list", $data, true, array("settings"));
        }
        
        public function delete($request_type  = "",
                                      $id            = 0,
                                      $page          = 1,
                                      $sort          = false,
                                      $category_id   = false)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $photoset_model = new Photo_Model();
            $photoset       = $photoset_model->find_by_id($id);
            
            if($photoset)
            {
                $photoset->delete();
                
                $category_model = new Category_Model;
                if($category_id)
                {
                    $category_model->find_selected_categories($category_id, 
                                                                            true, 
                                                                            "Photo_Model");
                }
                
                list($order_by, $direction) = explode("-", $sort);

                $direction = mb_strtoupper($direction, "utf-8");
                $photosets = $photoset_model->find_all_photosets($category_model->in_categories,
                                                                                 $page,
                                                                                 $order_by,
                                                                                 $direction,
                                                                                 false);
                
                // If now this page has 0 items and it isn't first page,
                // we are redirecting to previous page
                if(!$photosets and $page != 1)
                {
                    $page--;
                    $url_segments = Url::make_module_segments("photos",
                                                                            "index",
                                                                            $category_model->selected_category,
                                                                            $category_model->selected_subcategory,
                                                                            $page,
                                                                            $sort);
                    $this->ajax->callback = "redirect";
                    $this->ajax->data->url_segments = $url_segments;
                }
                // Else we should update pagination and items
                else
                {
                    foreach($photosets as $photoset)
                    {
                        $photoset->find_category_and_subcategory();
                        $photoset->find_main_photo();
                        $photoset->find_views_count();
                    }
                    
                    $data['photosets']            = $photosets;
                    $data['pages']                = $photoset_model->pagination->make_pages("compact");
                    $data['current_page']         = $page;
                    $data['selected_category']    = $category_model->selected_category;
                    $data['selected_subcategory'] = $category_model->selected_subcategory;
                    $data['selected_sort']        = $sort;
                    
                    $this->ajax->callback              = "update_items_and_pagination_html";
                    $this->ajax->data->items_html      = View::capture("photos" . DS . "photos_list_items", $data);
                    $this->ajax->data->pagination_html = View::capture("photos" . DS . "photos_list_pagination", $data);
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
        
        public function change_status($request_type = "", $id = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $photoset_model = new Photo_Model;
            $photoset       = $photoset_model->find_by_id($id);
            
            if($photoset)
            {
                if($photoset->change_status())
                {
                    $this->ajax->result       = 'ok';
                    $this->ajax->callback     = 'update_status';
                    $this->ajax->data->status = $photoset->status;
                    
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
        
        public function change_moderation($request_type = "", $id = 0)
        {
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $photoset_model = new Photo_Model;
            $photoset       = $photoset_model->find_by_id($id);
            
            if($photoset)
            {
                if($photoset->change_moderation())
                {
                    $this->ajax->result            = 'ok';
                    $this->ajax->callback          = 'update_caption';
                    $this->ajax->data->new_caption = $photoset->moderated;
                    
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
            $user          = new User_Model();
            $data['users'] = $user->find_all();
            
            // Capturing module root categories
            $category_model     = new Category_Model();
            $data['categories'] = $category_model->get_categories_by_module(0, "photos");
            
            // Remember opened category
            if($category_id)
                $data['selected_category_id'] = $category_id;
            else
                $data['selected_category_id'] = false;
            
            $photoset_model = new Photo_Model();
            
            if($id and $id != "add")
            {
                $photoset = $photoset_model->find_by_id($id);
                
                if($photoset)
                {
                    $data['photoset']   = $photoset;
                    $data['action']     = "Edit";
                    $data['is_editing'] = true;
                    
                    $photoset->find_attached_photos();
                    $photoset->find_main_photo();
                    
                    if($photoset->photos)
                    {
                        $photoset->photos = array_reverse($photoset->photos);
                        
                        foreach($photoset->photos as $photo)
                        {
                            $photo->unpack_directory();
                        }
                    }
                    
                    $photoset->find_category_and_subcategory();
                    
                    if($photoset->subcategory)
                    {
                        $photoset->category->find_subcategories();
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
                    $photoset_model->category_id = $category_id;
                    $photoset_model->find_category_and_subcategory();
                    
                    if($photoset_model->category->has_subcategories())
                    {
                        $photoset_model->category->find_subcategories();
                        
                        if(!$photoset_model->subcategory)
                        {
                            $photoset_model->subcategory = new Category_Model;
                            $photoset_model->category_id = "";
                        }
                    }
                }
                
                $data['photoset']   = $photoset_model;
                $data['action']     = "Add new";
                $data['is_editing'] = false;
            }

            $data['settings']    = $this->view->settings;
            $this->view->content = View::capture("photos" . DS . "photoset_form", $data, true, array("settings"));
        }
        
        public function load_subcategories($request_type = "", $id = false)
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
                    $photoset_photo_model = new Photo_Photo_Model;
                    $clones               = $photoset_photo_model->clones;
                    
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
            
            $photoset = new Photo_Model();
            
            $photoset->bind($this->input->post("photo"));
            $photoset->validate();
            
            $photos                = $this->input->post("photoset-photos", "no-photos");
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
            
            $photoset->validate_article_tags($uploaded_photos_count);
            $this->model_errors->ajaxify_if_has_errors();

            if($photoset->moderated == "yes")
            {
                $photoset->moderation_fail_text = "";
            }
            
            $update_exceptions = array();
            $special_form      = $this->input->post("special");
            if($special_form["update_postdate"] == "no")
                $update_exceptions[] = "posted_on";

            if($photoset->save(false, $update_exceptions))
            {
                // Processing photos
                foreach($photos as $photo)
                {
                    $photoset_photo = new Photo_Photo_Model();
                    
                    $photoset_photo->unpack_frame($photo["frame"]);
                    $photoset_photo->bind_id($photoset, "photo_id"); 
                    
                    switch($photoset_photo->frame_action)
                    {
                        case "ajax":
                            $photoset_photo->save_with_clones();
                        break;
                        
                        case "deleteajax":
                            $photoset_photo->delete_ajax();
                        break;
                        
                        case "delete":
                            $photoset_photo->delete_with_clones();
                        break;
                        
                        case "none":
                            // No photo processing required
                        break;
                    }
                }
                
                // Updating main photo
                $photoset_photo = new Photo_Photo_Model();
                
                $photoset_photo->bind($this->input->post("main_photo"));
                $photoset_photo->bind_id($photoset, "photo_id");
                
                $photoset_photo->update_main();
                
                // Save succesfull
                $this->session->set_modal_show_confirmation(); 
                
                // If some categories were opened,redirecting to them
                $category             = $this->input->post("category");
                $selected_category_id = $category["selected_category_id"];
                
                if($selected_category_id)
                {
                    $category_model = new Category_Model;
                    $category_model->find_selected_categories($selected_category_id);
                    
                    $url_segments = Url::make_module_segments("photos",
                                                                            "index",
                                                                            $category_model->selected_category,
                                                                            $category_model->selected_subcategory);
                }
                else
                {
                    $url_segments = "photos";
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