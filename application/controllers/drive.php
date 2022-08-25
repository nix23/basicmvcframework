<?php

class Drive_Controller extends Public_Controller
{
    // Restrict access to unauthorized users
    public function before()
    {
        $this->show_404_if_not_authorized();
        parent::before();
    }

    public function index($module = "photos",
                          $page = 1,
                          $category_id = false)
    {
        // Creating module objects
        $photoset_model = new Photo_Model;
        $spot_model = new Spot_Model;
        $speed_model = new Speed_Model;
        $video_model = new Video_Model;

        // Capturing reference to current module
        switch ($module) {
            case "photos":
                $selected_module_model = &$photoset_model;
                $find_all_uploads_method = "find_all_photosets";
                $no_uploads = "photoset";
                break;

            case "spots":
                $selected_module_model = &$spot_model;
                $find_all_uploads_method = "find_all_spots";
                $no_uploads = "spot";
                break;

            case "speed":
                $selected_module_model = &$speed_model;
                $find_all_uploads_method = "find_all_speeds";
                $no_uploads = "speed";
                break;

            case "videos":
                $selected_module_model = &$video_model;
                $find_all_uploads_method = "find_all_videos";
                $no_uploads = "video";
                break;
        }

        $category_model = new Category_Model;

        // Finding selected category,selected subcategory
        // and selected category subcategories list
        if ($category_id) {
            if (!$category_model->find_selected_categories($category_id,
                true,
                $selected_module_model->get_model_name(),
                false,
                false,
                true,
                $this->session->user_id)) {
                $error = new Error_Controller;
                $error->show_404();
            }
        }

        // Finding user uploads in selected module
        if ($module == "spots") {
            $module_uploads = $selected_module_model->$find_all_uploads_method($category_model->in_categories,
                $page,
                "moderated",
                "ASC",
                false,
                true,
                false,
                false,
                $this->session->user_id);
        } else {
            $module_uploads = $selected_module_model->$find_all_uploads_method($category_model->in_categories,
                $page,
                "moderated",
                "ASC",
                true,
                false,
                false,
                $this->session->user_id);
        }

        foreach ($module_uploads as $module_upload) {
            $module_upload->find_category_and_subcategory();
            $module_upload->find_main_photo();
        }

        // Generating modules list for sorting
        $modules = array(
            (object)array(
                "name" => "photos",
                "label" => "Photosets",
                "count" => $photoset_model->find_uploads_count_by_user($this->session->user_id),
                "selected" => false
            ),
            (object)array(
                "name" => "spots",
                "label" => "Spots",
                "count" => $spot_model->find_uploads_count_by_user($this->session->user_id),
                "selected" => false
            ),
            (object)array(
                "name" => "speed",
                "label" => "Speeds",
                "count" => $speed_model->find_uploads_count_by_user($this->session->user_id),
                "selected" => false
            ),
            (object)array(
                "name" => "videos",
                "label" => "Videos",
                "count" => $video_model->find_uploads_count_by_user($this->session->user_id),
                "selected" => false
            )
        );

        foreach ($modules as $module_item) {
            if ($module_item->name == $module)
                $module_item->selected = true;
        }

        $user_model = new User_Model;
        $user = $user_model->find_by_id($this->session->user_id);
        $user->find_statistics_data();

        // Setting template data
        $data['module_uploads'] = $module_uploads;
        $data['pages'] = $selected_module_model->pagination->make_pages("compact");
        $data['current_page'] = $page;
        $data['categories'] = $category_model->get_not_empty_root_categories_by_module($module,
            $selected_module_model->get_model_name(),
            false,
            false,
            $this->session->user_id);
        $data['selected_category'] = $category_model->selected_category;
        $data['selected_subcategory'] = $category_model->selected_subcategory;
        $data['modules'] = $modules;
        $data['selected_module'] = $module;
        $data['no_uploads'] = $no_uploads;
        $data['user'] = $user;

        $this->page_title = "Mydrive / Fordrive";
        $this->view->content = View::capture("drive" . DS . "mydrive", $data);
    }

    public function form()
    {
        $user_model = new User_Model;
        $data['user'] = $user_model->find_by_id($this->session->user_id);

        $this->page_title = "Mydrive / Edit profile / Fordrive";
        $this->view->content = View::capture("drive" . DS . "profile_form", $data);
    }

    public function upload_avatar($request_type = "")
    {
        $this->is_ajax($request_type);
        $image_uploader = new Fordrive_Uploader(100, 100);

        if (!$image_uploader->is_file_uploaded('upload-file')) {
            $this->ajax->errors->file_upload_error = $image_uploader->error;
        } else {
            if ($image_uploader->attach_file($_FILES['upload-file'])) {
                $user_model = new User_Model;
                $avatar_clones = $user_model->avatar_clones;

                if ($image_uploader->upload_photo($avatar_clones, false)) {
                    $image_uploader->delete_master_photo();

                    $this->ajax->result = "ok";
                    $this->ajax->callback = "insert_uploaded_single_photo";
                    $this->ajax->data->master_photo_name = $image_uploader->master_photo_name;
                    $this->ajax->data->photo_extension = "jpg";
                    $this->ajax->data->spinner_id = "photo-upload-spinner";
                } else {
                    $this->ajax->errors->file_upload_error = $image_uploader->error;
                }
            } else {
                $this->ajax->errors->file_upload_error = $image_uploader->error;
            }
        }

        $this->ajax->render();
    }

    public function save($request_type = "")
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        $user_model = new User_Model;
        $user = $user_model->find_by_id($this->session->user_id);

        if (!$user) {
            $error = new Error_Controller;
            $error->show_404();
        }

        // Processing avatar photos
        $avatar_photos = $this->input->post("avatar-photos", "not_exists");

        if ($avatar_photos != "not_exists") {
            foreach ($avatar_photos as $avatar_photo) {
                $user->unpack_frame($avatar_photo["frame"]);

                switch ($user->frame_action) {
                    case "ajax":
                        $user->move_clones();
                        break;

                    case "deleteajax":
                        $user->delete_ajax();
                        break;

                    case "delete":
                        $user->delete_clones();
                        $user->avatar_master_name = "";
                        break;

                    case "none":
                        // No processing required
                        break;
                }
            }
        }

        // Updating user's account
        $user->bind($this->input->post("account"));
        $user->validate(array(),
            "get_profile_update_rules");

        $this->model_errors->ajaxify_if_has_errors();

        if ($user->save()) {
            // Building response
            $this->session->set_modal_show_confirmation();
            $this->ajax->data->url_segments = "drive";
            $this->ajax->result = "ok";
            $this->ajax->callback = "redirect";

            $this->ajax->render();
        }
    }

    public function delete($request_type = "",
                           $item_id = false,
                           $module = false,
                           $page = false,
                           $category_id = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        // Creating module objects
        $photoset_model = new Photo_Model;
        $spot_model = new Spot_Model;
        $speed_model = new Speed_Model;
        $video_model = new Video_Model;

        // Capturing reference to current module
        switch ($module) {
            case "photos":
                $selected_module_model = &$photoset_model;
                $find_all_uploads_method = "find_all_photosets";
                $no_uploads = "photoset";
                break;

            case "spots":
                $selected_module_model = &$spot_model;
                $find_all_uploads_method = "find_all_spots";
                $no_uploads = "spot";
                break;

            case "speed":
                $selected_module_model = &$speed_model;
                $find_all_uploads_method = "find_all_speeds";
                $no_uploads = "speed";
                break;

            case "videos":
                $selected_module_model = &$video_model;
                $find_all_uploads_method = "find_all_videos";
                $no_uploads = "video";
                break;

            default:
                exit("Wrong module name passed to delete action.");
                break;
        }

        $selected_module_item = $selected_module_model->find_by_id($item_id);

        if (!$selected_module_item)
            exit("Wrong item_id passed to delete action. Please try refresh the form.");

        if (!$selected_module_item->is_record_deleting_by_owner($this->session->user_id,
            $selected_module_item->user_id))
            exit("You are not owner of this record.");

        if (!$selected_module_item->delete())
            exit("Item can't be deleted now. Please try refresh the form.");

        $category_model = new Category_Model;

        // Finding selected category,selected subcategory
        // and selected category subcategories list
        if ($category_id) {
            if (!$category_model->find_selected_categories($category_id,
                true,
                $selected_module_model->get_model_name(),
                false,
                false,
                true,
                $this->session->user_id)) {
                exit("Wrong category_id passed to delete action.");
            }
        }

        // Finding user uploads in selected module
        if ($module == "spots") {
            $module_uploads = $selected_module_model->$find_all_uploads_method($category_model->in_categories,
                $page,
                "moderated",
                "ASC",
                false,
                false,
                false,
                false,
                $this->session->user_id);
        } else {
            $module_uploads = $selected_module_model->$find_all_uploads_method($category_model->in_categories,
                $page,
                "moderated",
                "ASC",
                false,
                false,
                false,
                $this->session->user_id);
        }

        foreach ($module_uploads as $module_upload) {
            $module_upload->find_category_and_subcategory();
            $module_upload->find_main_photo();
        }

        $user_model = new User_Model;
        $user = $user_model->find_by_id($this->session->user_id);
        $user->find_statistics_data();

        // If now this page has 0 items and it isn't first page,
        // we are redirecting to previous page
        if (!$module_uploads and $page != 1) {
            $last_page = $selected_module_model->pagination->total_pages;
            $url_segments = Url::make_drive_segments($module,
                $last_page,
                $category_model->selected_category,
                $category_model->selected_subcategory);
            $this->ajax->callback = "redirect";
            $this->ajax->data->url_segments = $url_segments;
        } // Else we should update pagination,items and rating stats
        else {
            $data['module_uploads'] = $module_uploads;
            $data['pages'] = $selected_module_model->pagination->make_pages("compact");
            $data['current_page'] = $page;
            $data['selected_category'] = $category_model->selected_category;
            $data['selected_subcategory'] = $category_model->selected_subcategory;
            $data['selected_module'] = $module;
            $data['no_uploads'] = $no_uploads;
            $data['user'] = $user;

            $this->ajax->callback = "update_mydrive_html_after_item_delete";
            $this->ajax->data->items_html = View::capture("drive" . DS . "mydrive_items", $data);
            $this->ajax->data->pagination_html = View::capture("drive" . DS . "mydrive_pagination", $data);
            $this->ajax->data->rating_stats_html = View::capture("drive" . DS . "mydrive_rating_stats", $data);
        }

        $this->ajax->result = "ok";
        $this->ajax->render();
    }

    public function change_upload_status($request_type = "",
                                         $item_id = false,
                                         $module = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        // Creating module objects
        $photoset_model = new Photo_Model;
        $spot_model = new Spot_Model;
        $speed_model = new Speed_Model;
        $video_model = new Video_Model;

        // Capturing reference to current module
        switch ($module) {
            case "photos":
                $selected_module_model = &$photoset_model;
                break;

            case "spots":
                $selected_module_model = &$spot_model;
                break;

            case "speed":
                $selected_module_model = &$speed_model;
                break;

            case "videos":
                $selected_module_model = &$video_model;
                break;

            default:
                exit("Wrong module name passed to change_status action.");
                break;
        }

        $selected_module_item = $selected_module_model->find_by_id($item_id);

        if (!$selected_module_item)
            exit("Wrong item_id passed to change_status action. Please try refresh the form.");

        if (!$selected_module_item->change_status())
            exit("Can't change item status now. Please try refresh the form.");

        $this->ajax->callback = "update_mydrive_status";
        $this->ajax->data->new_status = $selected_module_item->status;
        $this->ajax->result = "ok";
        $this->ajax->render();
    }
}

?>