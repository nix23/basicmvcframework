<?php
    class Spots_Controller extends Public_Controller
    {
        public function index($page        = 1, 
                                     $sort        = "postdate-desc", 
                                     $category_id = false)
        {
            $category_model = new Category_Model;
            
            if($category_id)
            {
                if(!$category_model->find_selected_categories($category_id,
                                                                             true,
                                                                             "Spot_Model"))
                {
                    $error = new Error_Controller;
                    $error->show_404();
                }
            }
            
            list($order_by, $direction) = explode("-", $sort);
            
            $sort_items = array(
                (object) array("type" => "postdate",    "label" => "post date", "direction" => "desc", "selected" => false),
                (object) array("type" => "capturedate", "label" => "spot date", "direction" => "desc", "selected" => false),
                (object) array("type" => "activity",    "label" => "activity",  "direction" => "desc", "selected" => false)
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
            
            $spot_model = new Spot_Model();
            $order_by   = str_replace("postdate", "posted_on",   $order_by);
            $direction  = mb_strtoupper($direction, "utf-8");
            
            if($order_by == "capturedate")
            {
                $order_by           = "capture_year";
                $secondary_order_by = "capture_month";
            }
            else
            {
                $secondary_order_by = false;
            }
            
            $spots = $spot_model->find_all_spots($category_model->in_categories,
                                                             $page,
                                                             $order_by,
                                                             $direction,
                                                             $secondary_order_by,
                                                             true,
                                                             true,
                                                             true);
            
            foreach($spots as $spot)
            {
                $spot->find_category_and_subcategory();
                $spot->find_main_photo();
            }
            
            $authorized = ($this->session->is_logged_in()) ? true : false;
            
            $this->save_catalog_url_segments_in_session($category_model->selected_category,
                                                                      $category_model->selected_subcategory,
                                                                      $page,
                                                                      $sort);
            
            $data['spots']                = $spots;
            $data['pages']                = $spot_model->pagination->make_pages("compact");
            $data['current_page']         = $page;
            $data['categories']           = $category_model->get_not_empty_root_categories_by_module("spots", "Spot_Model");
            $data['selected_category']    = $category_model->selected_category;
            $data['selected_subcategory'] = $category_model->selected_subcategory;
            $data['sort_items']           = $sort_items;
            $data['selected_sort']        = $sort;
            $data['authorized']           = $authorized;

            if($category_model->selected_category)
            {
                if($category_model->selected_subcategory)
                {
                    $title_content  = $category_model->selected_category->name;
                    $title_content .= " ";
                    $title_content .= $category_model->selected_subcategory->name;
                }
                else
                {
                    $title_content = $category_model->selected_category->name;
                }
            }
            else
            {
                $title_content = "User car spots";
            }

            $title        = "Spots / $title_content / Fordrive";
            $description  = "User car experience on fordrive. ";
            $description .= "User cars, spots, autoshows, drag races and more.";

            $this->page_title       = $title;
            $this->meta_description = $description;
            
            $this->view->content = View::capture("spots" . DS . "spots_list", $data);
        }
        
        public function view($category_id = false,
                                    $spot_id     = false)
        {
            $category_model = new Category_Model;
            
            if(!$category_id 
                    or
                !$category_model->find_selected_categories($category_id,
                                                                         true,
                                                                         "Spot_Model"))
            {
                $error = new Error_Controller;
                $error->show_404();
            }
            
            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_id);
            
            if(!$spot)
            {
                $error = new Error_Controller;
                $error->show_404();
            }

            $catalog_backlink_segments = $this->session->get_catalog_url_segments($this->current_controller);
            $catalog_backlink          = $this->current_controller . "/list";
            
            $catalog_backlink_segments_parts = explode("/", $catalog_backlink_segments);
            $current_sort = $catalog_backlink_segments_parts[count($catalog_backlink_segments_parts) - 1]; 
            if(strlen($current_sort) == 0)
                $current_sort = "postdate-desc";
            else
            {
                $current_sort_parts = explode("-", $current_sort);
                $current_sort_parts = array_splice($current_sort_parts, 1);
                $current_sort = implode("-", $current_sort_parts);
            }

            if($catalog_backlink_segments)
                $catalog_backlink .= $catalog_backlink_segments;

            $data['catalog_backlink'] = $catalog_backlink;

            $item_is_available = true;
            if(!$this->admin_session->is_logged_in())
            {
                if(!$spot->is_authorized_user_spot_author())
                {
                    if(!$spot->is_enabled()
                            or
                        !$spot->is_moderated())
                    {
                        $this->view->content = View::capture("spots" . DS . "unavailable_item", $data);
                        $item_is_available   = false;
                    }
                }
            }

            if($item_is_available)
            {
                $spot->find_category_and_subcategory();
                $spot->find_main_photo();
                $spot->find_attached_photos();
                $spot->find_attached_comments_on(1);
                $spot->find_comments_count();
                $spot->find_author();
                $spot->find_likes_count();
                $spot->find_favorites_count();
                $spot->find_author_followers_count();
                $spot->update_views_count($this->get_ip());
                $spot->find_views_count();
                if($this->session->is_logged_in())
                {
                    $spot->find_if_is_logged_user_post_author($this->session->user_id);
                    $spot->find_if_is_liked_by_logged_user($this->session->user_id);
                    $spot->find_if_author_is_followed_by_logged_user($this->session->user_id);
                    $spot->find_if_is_favorite_of_logged_user($this->session->user_id);
                }

                $spot->main_photo->find_lazy_clones_that_exists();
                $spot->main_photo->unpack_directory();
                foreach($spot->photos as $photo)
                {
                    $photo->find_lazy_clones_that_exists();
                    $photo->unpack_directory();
                }
                foreach($spot->comments as $comment)
                {
                    $comment->find_author();
                    $comment->find_answers();

                    foreach($comment->answers as $answer)
                    {
                        $answer->find_author();
                    }
                }

                $authorized       = ($this->session->is_logged_in()) ? true : false;
                $admin_authorized = ($this->admin_session->is_logged_in()) ? true : false;

                $data['spot']                  = $spot;
                $data['categories']            = $category_model->get_not_empty_root_categories_by_module("spots", "Spot_Model");
                $data['selected_category']     = $category_model->selected_category;
                $data['selected_subcategory']  = $category_model->selected_subcategory;
                $data['comments_pages']        = $spot->comments_model->pagination->make_pages("compact");
                $data['comments_current_page'] = 1;
                $data['authorized']            = $authorized;
                $data['admin_authorized']      = $admin_authorized;
                $data['current_sort']          = $current_sort;

                $this->page_title       = $spot->get_full_heading();
                $this->meta_description = $spot->generate_meta_description();

                $this->view->content = View::capture("spots" . DS . "view_spot", $data);
            }
        }
        
        public function add_like($request_type = "",
                                         $spot_id      = false)
        {
            $this->show_404_if_not_authorized();
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_id);
            
            if(!$spot)
            {
                $this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
                $this->ajax->render();
            }
            else if(!$spot->is_enabled())
            {
                $this->ajax->errors->item_is_disabled = ERROR_ITEM_IS_DISABLED_AJAX;
                $this->ajax->render();
            }
            else if(!$spot->is_moderated())
            {
                $this->ajax->errors->item_is_moderated = ERROR_ITEM_IS_MODERATED_AJAX;
                $this->ajax->render();
            }
            
            $spot->find_if_is_liked_by_logged_user($this->session->user_id);
            if($spot->is_liked_by_logged_user)
            {
                $this->ajax->errors->item_is_already_liked = ERROR_ITEM_IS_ALREADY_LIKED_AJAX;
                $this->ajax->render();
            }
            
            $spot_like_model           = new Spot_Like_Model;
            $spot_like_model->spot_id  = $spot->id;
            $spot_like_model->user_id  = $this->session->user_id;
            
            if($spot_like_model->save())
            {
                $this->ajax->result   = "ok";
                $this->ajax->callback = "add_like";
                
                $this->ajax->render(); 
            }
        }
        
        public function change_follow($request_type = "",
                                                $followed_id  = false)
        {
            $this->show_404_if_not_authorized();
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $user_model     = new User_Model;
            $user_to_follow = $user_model->find_by_id($followed_id);
            
            if(!$user_to_follow)
            {
                $this->ajax->errors->user_was_deleted = ERROR_USER_WAS_DELETED;
                $this->ajax->render();
            }
            
            $follower_model = new Follower_Model;
            $follower        = $follower_model->find_followed_user_by_follower($user_to_follow->id,
                                                                                                    $this->session->user_id);
            if($follower)
            {
                if($follower->delete())
                {
                    $this->ajax->data->new_caption  = "Follow";
                    $this->ajax->data->count_action = "decrease";
                }
            }
            else
            {
                $follower_model->followed_id = $user_to_follow->id;
                $follower_model->follower_id = $this->session->user_id;
                
                if($follower_model->save())
                {
                    $this->ajax->data->new_caption  = "Unfollow";
                    $this->ajax->data->count_action = "increase";
                }
            }
            
            $this->ajax->result   = "ok";
            $this->ajax->callback = "change_panel_list_item";
            
            $this->ajax->render();
        }
        
        public function change_favorite($request_type = "",
                                                  $spot_id      = false)
        {
            $this->show_404_if_not_authorized();
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_id);
            
            if(!$spot)
            {
                $this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
                $this->ajax->render();
            }
            else if(!$spot->is_enabled())
            {
                $this->ajax->errors->item_is_disabled = ERROR_ITEM_IS_DISABLED_AJAX;
                $this->ajax->render();
            }
            else if(!$spot->is_moderated())
            {
                $this->ajax->errors->item_is_moderated = ERROR_ITEM_IS_MODERATED_AJAX;
                $this->ajax->render();
            }
            
            $favorite_model = new Favorite_Model;
            $favorite       = $favorite_model->find_favorite($spot->id,
                                                                             $this->session->user_id,
                                                                             "spots");
            
            if($favorite)
            {
                if($favorite->delete())
                {
                    $this->ajax->data->new_caption  = "Favorite";
                    $this->ajax->data->count_action = "decrease";
                }
            }
            else
            {
                $favorite_model->item_id = $spot->id;
                $favorite_model->user_id = $this->session->user_id;
                $favorite_model->module  = "spots";
                
                if($favorite_model->save())
                {
                    $this->ajax->data->new_caption  = "Unfavorite";
                    $this->ajax->data->count_action = "increase";
                }
            }
            
            $this->ajax->result   = "ok";
            $this->ajax->callback = "change_panel_list_item";
            
            $this->ajax->render();
        }
        
        public function add_comment($request_type = "")
        {
            $this->show_404_if_not_authorized();
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $spot_comment_model = new Spot_Comment_Model;
            
            $spot_comment_model->bind($this->input->post("comment"));
            $spot_comment_model->user_id = $this->session->user_id;

            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_comment_model->spot_id);

            if(!$spot)
            {
                $this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
                $this->ajax->render();
            }
            else if(!$spot->is_enabled() and !$spot->is_authorized_user_spot_author())
            {
                $this->ajax->errors->item_is_disabled = ERROR_ITEM_IS_DISABLED_AJAX;
                $this->ajax->render();
            }
            else if(!$spot->is_moderated() and !$spot->is_authorized_user_spot_author())
            {
                $this->ajax->errors->item_is_moderated = ERROR_ITEM_IS_MODERATED_AJAX;
                $this->ajax->render();
            }

            $spot_comment_model->validate();

            if(!$this->verify_auth_key())
                $this->model_errors->set("invalid_authkey",
                                                 "Wrong form token, please try refresh the page.");

            if($this->session->is_set("last_comment_time"))
            {
                if((time() - $this->session->get("last_comment_time")) < MIN_SECONDS_BETWEEN_COMMENTS)
                    $this->model_errors->set("too_fast_new_comment",
                                                     "Please wait " . MIN_SECONDS_BETWEEN_COMMENTS . "seconds before posting new comment.");
            }

            $this->model_errors->ajaxify_if_has_errors();
            
            if($spot_comment_model->save())
            {
                $this->session->set("last_comment_time", time());

                // If new comment,opening first page
                if($spot_comment_model->answer_id == "0")
                {
                    $current_page = 1;
                }
                // If answer,opening selected page
                else
                {
                    $current_page_array = $this->input->post("current_page");
                    $current_page       = $current_page_array["number"];
                }
                
                // Fetching comments
                $spot->find_attached_comments_on($current_page);
                $spot->find_comments_count();
                
                foreach($spot->comments as $comment)
                {
                    $comment->find_author();
                    $comment->find_answers();
                    
                    foreach($comment->answers as $answer)
                    {
                        $answer->find_author();
                    }
                }
                
                // Capturing comments
                $data["authorized"]                    = ($this->session->is_logged_in()) ? true : false;
                $data["spot"]                          = $spot;
                $data["admin_authorized"]              = ($this->admin_session->is_logged_in()) ? true : false;
                $comments_path                         = "spots" . DS . "view_spot_comments_items";
                $this->ajax->data->comments_items_html = View::capture($comments_path, $data);
                
                // Capturing pagination
                $data                                       = array();
                $data["spot"]                               = $spot;
                $data["comments_pages"]                     = $spot->comments_model->pagination->make_pages("compact");
                $data["comments_current_page"]              = $current_page;
                $pagination_path                            = "spots" . DS . "view_spot_comments_pagination";
                $this->ajax->data->comments_pagination_html = View::capture($pagination_path, $data);
                
                // Building response
                $this->ajax->data->comment_to_scroll_id = $spot_comment_model->id;
                $this->ajax->data->comments_count       = $spot->comments_total_count;
                $this->ajax->data->current_page         = $current_page;
                $this->ajax->data->callback             = "hide_newcomment_form";
                $this->ajax->result                     = 'ok';
                $this->ajax->callback                   = 'refresh_comments';
                
                $this->ajax->render();
            }
        }

        public function delete_comment($request_type = "",
                                                 $comment_id   = false)
        {
            if(!$this->admin_session->is_logged_in())
            {
                $error_controller = new Error_Controller;
                $error_controller->show_404();
            }

            $this->is_ajax($request_type);
            $this->validate_token();

            $spot_comment_model = new Spot_Comment_Model;
            $spot_comment       = $spot_comment_model->find_by_id($comment_id);

            if(!$spot_comment)
                exit("Error: can't find comment with such id");

            if(!$spot_comment->delete_with_all_answers())
                exit("Error: can't delete comment now");

            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_comment->spot_id);

            $current_page_array = $this->input->post("current_page");
            $current_page       = $current_page_array["number"];

            // Fetching comments
            $spot->find_attached_comments_on($current_page, false);

            // If no comments left on current page and it isn't first page,
            // loading previous page comments
            if(!$spot->comments and $current_page != 1)
            {
                $spot->find_attached_comments_on(--$current_page);
            }

            $spot->find_comments_count();

            foreach($spot->comments as $comment)
            {
                $comment->find_author();
                $comment->find_answers();

                foreach($comment->answers as $answer)
                {
                    $answer->find_author();
                }
            }

            // Capturing comments
            $data["authorized"]                    = ($this->session->is_logged_in()) ? true : false;
            $data["admin_authorized"]              = ($this->admin_session->is_logged_in()) ? true : false;
            $data["spot"]                          = $spot;
            $comments_path                         = "spots" . DS . "view_spot_comments_items";
            $this->ajax->data->comments_items_html = View::capture($comments_path, $data);

            // Capturing pagination
            $data                                       = array();
            $data["spot"]                               = $spot;
            $data["comments_pages"]                     = $spot->comments_model->pagination->make_pages("compact");
            $data["comments_current_page"]              = $current_page;
            $pagination_path                            = "spots" . DS . "view_spot_comments_pagination";
            $this->ajax->data->comments_pagination_html = View::capture($pagination_path, $data);

            // Building response
            $this->ajax->data->comments_count       = $spot->comments_total_count;
            $this->ajax->data->current_page         = $current_page;
            $this->ajax->result                     = 'ok';
            $this->ajax->callback                   = 'refresh_comments_after_delete';

            $this->ajax->render();
        }

        public function delete_answer($request_type = "",
                                                $answer_id    = false)
        {
            if(!$this->admin_session->is_logged_in())
            {
                $error_controller = new Error_Controller;
                $error_controller->show_404();
            }

            $this->is_ajax($request_type);
            $this->validate_token();

            $spot_comment_model = new Spot_Comment_Model;
            $spot_comment       = $spot_comment_model->find_by_id($answer_id);

            if(!$spot_comment)
                exit("Error: can't find comment with such id");

            if(!$spot_comment->delete())
                exit("Error: can't delete comment now");

            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_comment->spot_id);

            $spot->find_comments_count();

            // Building response
            $this->ajax->data->comments_count       = $spot->comments_total_count;
            $this->ajax->result                     = 'ok';
            $this->ajax->callback                   = 'delete_answer';

            $this->ajax->render();
        }

        public function load_comments($request_type = "",
                                                $spot_id      = false,
                                                $page         = 1)
        {
            $this->is_ajax($request_type);
            
            $spot_model = new Spot_Model;
            $spot       = $spot_model->find_by_id($spot_id);

            if(!$spot)
            {
                $this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
                $this->ajax->render();
            }

            // Fetching comments
            $spot->find_attached_comments_on($page);
            $spot->find_comments_count();
            
            foreach($spot->comments as $comment)
            {
                $comment->find_author();
                $comment->find_answers();
                
                foreach($comment->answers as $answer)
                {
                    $answer->find_author();
                }
            }
            
            // Capturing comments
            $data["authorized"]                    = ($this->session->is_logged_in()) ? true : false;
            $data["spot"]                          = $spot;
            $data["admin_authorized"]              = ($this->admin_session->is_logged_in()) ? true : false;
            $comments_path                         = "spots" . DS . "view_spot_comments_items";
            $this->ajax->data->comments_items_html = View::capture($comments_path, $data);
            
            // Capturing pagination
            $data                                       = array();
            $data["spot"]                               = $spot;
            $data["comments_pages"]                     = $spot->comments_model->pagination->make_pages("compact");
            $data["comments_current_page"]              = $page;
            $pagination_path                            = "spots" . DS . "view_spot_comments_pagination";
            $this->ajax->data->comments_pagination_html = View::capture($pagination_path, $data);
            
            // Building response
            $this->ajax->data->comment_to_scroll_id = "heading";
            $this->ajax->data->comments_count       = $spot->comments_total_count;
            $this->ajax->data->current_page         = $page;
            $this->ajax->data->callback             = "";
            $this->ajax->result                     = 'ok';
            $this->ajax->callback                   = 'refresh_comments';
            
            $this->ajax->render();
        }
        
        public function form($id          = false,
                                    $category_id = false)
        {
            $this->show_404_if_not_authorized();
            
            // Capturing module root categories
            $category_model     = new Category_Model;
            $data['categories'] = $category_model->get_categories_by_module(0, "spots");
            
            // Restore sorting and categories,if we opened form from catalog page
            $catalog_backlink_segments = $this->session->get_catalog_url_segments($this->current_controller);
            $catalog_backlink          = $this->current_controller . "/list";
            
            if($catalog_backlink_segments)
                $catalog_backlink .= $catalog_backlink_segments;
            
            $data['catalog_backlink'] = $catalog_backlink;
            
            // Building month array
            $data['months'] = array(
                "January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            );
            
            $spot_model = new Spot_Model;
            
            // Editing spot
            if($id and $id != "add")
            {
                $spot = $spot_model->find_by_id($id);
                
                if($spot)
                {
                    $data['spot']       = $spot;
                    $data['action']     = "Edit";
                    $data['is_editing'] = true;
                    
                    $spot->find_attached_photos();
                    $spot->find_main_photo();
                    
                    if($spot->photos)
                    {
                        $spot->photos = array_reverse($spot->photos);
                        
                        foreach($spot->photos as $photo)
                        {
                            $photo->unpack_directory();
                        }
                    }
                    
                    $spot->find_category_and_subcategory();
                    
                    if($spot->subcategory)
                    {
                        $spot->category->find_subcategories();
                    }
                }
                else
                {
                    $error = new Error_Controller;
                    $error->show_404();
                }
            }
            // New spot
            else
            {
                // If some categories were opened,we need
                // open them on form at start.
                if($category_id)
                {
                    $spot_model->category_id = $category_id;
                    $spot_model->find_category_and_subcategory();
                    
                    if($spot_model->category->has_subcategories())
                    {
                        $spot_model->category->find_subcategories();
                        
                        // If only root category is opened,we need
                        // to open blank subcategory
                        if(!$spot_model->subcategory)
                        {
                            $spot_model->subcategory = new Category_Model;
                            $spot_model->category_id = "";
                        }
                    }
                }
                
                $data['spot']       = $spot_model;
                $data['action']     = "Add new";
                $data['is_editing'] = false;
            }
            
            // Inserting user id
            $data['spot']->user_id = $this->session->user_id;

            $this->page_title    = "Add spot / Fordrive";
            $this->view->content = View::capture("spots" . DS . "spot_form", $data);
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
            $this->show_404_if_not_authorized();
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
                    $spot_photo_model = new Spot_Photo_Model;
                    $clones           = $spot_photo_model->clones;
                    
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
            $this->show_404_if_not_authorized();
            $this->is_ajax($request_type);
            $this->validate_token();
            
            $spot = new Spot_Model;
            
            $spot->bind($this->input->post("spot"));

            if($spot->is_record_updating())
            {
                if(!$spot->is_record_updating_by_owner($this->session->user_id,
                                                                    $spot->user_id))
                    $this->model_errors->set("wrong_ownership", "You are not owner of this record.");
            }

            $spot->validate(array(), "get_public_validation_rules");
            
            // Check,that user id equals session user id
            if($spot->user_id != $this->session->user_id)
                $this->model_errors->set("user_required", "Wrong user login. Try refresh your form.");
            
            // Checking at least one photo exists
            $photos                = $this->input->post("spot-photos", "no-photos");
            $uploaded_photos_count = 0;
            
            if($photos == "no-photos")
            {
                $this->model_errors->set("photo_required", "Please add at least one photo.");
            }
            else
            {
                $no_photos = true;
                foreach($photos as $photo)
                {
                    // Frames 'delete','deleteajax' will delete photo
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
            
            $spot->validate_article_tags($uploaded_photos_count,
                                                  "html");
            $this->model_errors->ajaxify_if_has_errors();

            if($spot->is_moderation_failed())
            {
                $spot->moderation_fail_text = "";
            }

            if($spot->save(true))
            {
                // Processing photo frames
                foreach($photos as $photo)
                {
                    $spot_photo = new Spot_Photo_Model;
                    
                    $spot_photo->unpack_frame($photo["frame"]);
                    $spot_photo->bind_id($spot, "spot_id"); 
                    
                    switch($spot_photo->frame_action)
                    {
                        case "ajax":
                            $spot_photo->save_with_clones();
                        break;
                        
                        case "deleteajax":
                            $spot_photo->delete_ajax();
                        break;
                        
                        case "delete":
                            $spot_photo->delete_with_clones();
                        break;
                        
                        case "none":
                            // No frame processing required
                        break;
                    }
                }
                
                // Updating main photo
                $spot_photo = new Spot_Photo_Model;
                
                $spot_photo->bind($this->input->post("main_photo"));
                $spot_photo->bind_id($spot, "spot_id");
                
                $spot_photo->update_main();
                
                // Save succesfull
                $this->session->set_modal_show_confirmation();
                
                // Building response
                $this->ajax->data->url_segments = 'drive/list/spots/page-1';
                $this->ajax->result             = 'ok';
                $this->ajax->callback           = 'redirect';
                
                $this->ajax->render();
            }
        }
    }
?>