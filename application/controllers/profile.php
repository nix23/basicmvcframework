<?php
    class Profile_Controller extends Public_Controller
    {
        public function index($user_id     = false,
                                     $module      = "photos",
                                     $page        = 1,
                                     $category_id = false)
        {
            if($this->session->is_logged_in())
            {
                if($this->session->user_id == $user_id)
                {
                    Url::redirect("main/index");
                }
            }

            $user_model = new User_Model;
            $user       = $user_model->find_by_id($user_id);

            if(!$user)
            {
                $error_controller = new Error_Controller;
                $error_controller->show_404();
            }

            if(!$user->is_account_activated())
            {
                $data["message"]     = "$user->username profile isn't activated yet.";
                $this->view->content = View::capture("profiles" . DS . "unavailable_account", $data);
            }
            else
            {
                $user->find_statistics_data();

                // Creating module objects
                $photoset_model = new Photo_Model;
                $spot_model     = new Spot_Model;
                $speed_model    = new Speed_Model;
                $video_model    = new Video_Model;

                // Capturing reference to current module
                switch($module)
                {
                    case "photos":
                        $selected_module_model   = &$photoset_model;
                        $find_all_uploads_method = "find_all_photosets";
                        $no_uploads              = "photoset";
                    break;

                    case "spots":
                        $selected_module_model   = &$spot_model;
                        $find_all_uploads_method = "find_all_spots";
                        $no_uploads              = "spot";
                    break;

                    case "speed":
                        $selected_module_model   = &$speed_model;
                        $find_all_uploads_method = "find_all_speeds";
                        $no_uploads              = "speed";
                    break;

                    case "videos":
                        $selected_module_model   = &$video_model;
                        $find_all_uploads_method = "find_all_videos";
                        $no_uploads              = "video";
                    break;
                }

                $category_model = new Category_Model;

                // Finding selected category,selected subcategory
                // and selected category subcategories list
                if($category_id)
                {
                    if(!$category_model->find_selected_categories($category_id,
                                                                                 true,
                                                                                 $selected_module_model->get_model_name(),
                                                                                 true,
                                                                                 true,
                                                                                 true,
                                                                                 $user->id))
                    {
                        $error = new Error_Controller;
                        $error->show_404();
                    }
                }

                // Finding user uploads in selected module
                if($module == "spots")
                {
                    $module_uploads = $selected_module_model->$find_all_uploads_method($category_model->in_categories,
                                                                                                             $page,
                                                                                                             "moderated",
                                                                                                             "ASC",
                                                                                                             false,
                                                                                                             true,
                                                                                                             true,
                                                                                                             true,
                                                                                                             $user->id);
                }
                else
                {
                    $module_uploads = $selected_module_model->$find_all_uploads_method($category_model->in_categories,
                                                                                                             $page,
                                                                                                             "moderated",
                                                                                                             "ASC",
                                                                                                             true,
                                                                                                             true,
                                                                                                             true,
                                                                                                             $user->id);
                }

                foreach($module_uploads as $module_upload)
                {
                    $module_upload->find_category_and_subcategory();
                    $module_upload->find_main_photo();
                }

                // Generating modules list for sorting
                $modules = array(
                    (object) array(
                        "name"     => "photos",
                        "label"    => "Photosets",
                        "count"    => $photoset_model->find_uploads_count_by_user($user->id),
                        "selected" => false
                    ),
                    (object) array(
                        "name"     => "spots",
                        "label"    => "Spots",
                        "count"    => $spot_model->find_uploads_count_by_user($user->id),
                        "selected" => false
                    ),
                    (object) array(
                        "name"     => "speed",
                        "label"    => "Speeds",
                        "count"    => $speed_model->find_uploads_count_by_user($user->id),
                        "selected" => false
                    ),
                    (object) array(
                        "name"     => "videos",
                        "label"    => "Videos",
                        "count"    => $video_model->find_uploads_count_by_user($user->id),
                        "selected" => false
                    )
                );

                foreach($modules as $module_item)
                {
                    if($module_item->name == $module)
                        $module_item->selected = true;
                }

                $authorized = ($this->session->is_logged_in()) ? true : false;

                if($authorized)
                {
                    $follower_model = new Follower_Model;

                    if($follower_model->is_user_followed_by($user->id, $this->session->user_id))
                        $is_user_followed_by_viewer = true;
                    else
                        $is_user_followed_by_viewer = false;
                }
                else
                {
                    $is_user_followed_by_viewer = false;
                }

                // Setting template data
                $data['module_uploads'] = $module_uploads;
                $data['pages']          = $selected_module_model->pagination->make_pages("compact");
                $data['current_page']   = $page;
                $data['categories']     = $category_model->get_not_empty_root_categories_by_module($module,
                                                                                                                              $selected_module_model->get_model_name(),
                                                                                                                              true,
                                                                                                                              true,
                                                                                                                              $user->id);

                $data['selected_category']          = $category_model->selected_category;
                $data['selected_subcategory']       = $category_model->selected_subcategory;
                $data['modules']                    = $modules;
                $data['selected_module']            = $module;
                $data['no_uploads']                 = $no_uploads;
                $data['user']                       = $user;
                $data['authorized']                 = $authorized;
                $data['is_user_followed_by_viewer'] = $is_user_followed_by_viewer;

                $this->page_title       = "$user->username profile / Fordrive";
                $this->meta_description = "$user->username profile on fordrive. ";
                $this->view->content    = View::capture("profiles" . DS . "view_profile", $data);
            }
        }

        public function change_follow_status($request_type = "",
                                                         $followed_id  = false)
        {
            $this->show_404_if_not_authorized();
            $this->is_ajax($request_type);
            $this->validate_token();

            $user_model     = new User_Model;
            $user_to_follow = $user_model->find_by_id($followed_id);

            if(!$user_to_follow)
            {
                exit("Wrong followed_id passed to change_follow_status action.");
            }

            $follower_model = new Follower_Model;
            $follower        = $follower_model->find_followed_user_by_follower($user_to_follow->id,
                                                                                                    $this->session->user_id);
            if($follower)
            {
                if($follower->delete())
                {
                    $this->ajax->data->new_caption  = "Follow";
                    $this->ajax->data->count_action = "descrease";
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
            $this->ajax->callback = "update_profile_follow_status";

            $this->ajax->render();
        }
    }
?>