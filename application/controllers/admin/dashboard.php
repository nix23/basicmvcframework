<?php

class Admin_Dashboard_Controller extends Admin_Controller
{
    public function index($page = 1,
                          $days_to_fetch = 1,
                          $events_to_show = "all")
    {
        $data = array();

        $days_to_fetch_items = array(
            (object)array("label" => "Day", "value" => "1", "selected" => false),
            (object)array("label" => "Week", "value" => "7", "selected" => false),
            (object)array("label" => "Month", "value" => "30", "selected" => false)
        );

        foreach ($days_to_fetch_items as $days_to_fetch_item) {
            if ((int)$days_to_fetch_item->value == $days_to_fetch)
                $days_to_fetch_item->selected = true;
        }

        $events_to_show_items = array(
            (object)array("label" => "All", "value" => "all", "sublabel" => "All events", "selected" => false),
            (object)array("label" => "Uploads", "value" => "uploads", "sublabel" => "Latest uploads", "selected" => false),
            (object)array("label" => "Comments", "value" => "comments", "sublabel" => "New discussions", "selected" => false),
            (object)array("label" => "Likes", "value" => "likes", "sublabel" => "User likes", "selected" => false),
            (object)array("label" => "Follows", "value" => "follows", "sublabel" => "User subscribes", "selected" => false),
            (object)array("label" => "Favorites", "value" => "favorites", "sublabel" => "Favorite posts", "selected" => false),
            (object)array("label" => "Users", "value" => "users", "sublabel" => "New fordrivers", "selected" => false)
        );

        foreach ($events_to_show_items as $events_to_show_item) {
            if ($events_to_show_item->value == $events_to_show)
                $events_to_show_item->selected = true;
        }

        if ($events_to_show == "all") {
            $event_types = array("uploads", "comments", "likes", "follows",
                "favorites", "users");
        } else {
            $event_types = array("$events_to_show");
        }

        $dashboard_events_mapper = new Dashboard_Events_Mapper;
        // TODO --> CHANGE USER_DAYS_TO_FETCH
        $dashboard_events = $dashboard_events_mapper->find_dashboard_events_by($event_types,
            $page,
            $days_to_fetch);
        foreach ($dashboard_events as $dashboard_event) {
            switch ($dashboard_event->type) {
                case "upload":
                case "comment":
                case "answer":
                case "like":
                case "favorite":
                    if ($dashboard_event->type == "upload")
                        $dashboard_event->find_item_moderation_and_status();
                    // First we want to find favorite item_id,
                    // and then replace favorites id
                    // with module_item actual item id.
                    // Then we can use methods below without collisions.
                    if ($dashboard_event->type == "favorite")
                        $dashboard_event->find_favorite_item_id();

                    $dashboard_event->find_category_and_subcategory();
                    $dashboard_event->find_main_photo();
                    $dashboard_event->find_user();
                    $dashboard_event->find_comments_and_likes_count();
                    $dashboard_event->find_views_count();
                    break;

                case "follow":
                    $dashboard_event->find_follow_pair();
                    break;

                case "activated_user":
                case "registred_user":
                    $dashboard_event->find_user();
                    break;
            }
        }

        $data["dashboard_events"] = $dashboard_events;
        $data["events_to_show_items"] = $events_to_show_items;
        $data["selected_events_to_show"] = $events_to_show;
        $data["pages"] = $dashboard_events_mapper->pagination->make_pages("compact");
        $data["current_page"] = $page;
        $data["days_to_fetch_items"] = $days_to_fetch_items;
        $data["current_days_to_fetch"] = $days_to_fetch;
        $data["settings"] = $this->view->settings;

        $this->view->content = View::capture("dashboard" . DS . "dashboard", $data, true, array("settings"));
    }

    // Reloads dashboard event items and pagination into
    // Ajax object to return data in ajax response,
    // or just builts redirect to previous page,if
    // last item was deleted on current page.
    private function build_ajax_delete_item_response($page,
                                                     $days_to_fetch,
                                                     $events_to_show)
    {
        if ($events_to_show == "all") {
            $event_types = array("uploads", "comments", "likes", "follows",
                "favorites", "users");
        } else {
            $event_types = array("$events_to_show");
        }

        $dashboard_events_mapper = new Dashboard_Events_Mapper;
        // TODO --> CHANGE USER_DAYS_TO_FETCH
        $dashboard_events = $dashboard_events_mapper->find_dashboard_events_by($event_types,
            $page,
            $days_to_fetch,
            false);
        foreach ($dashboard_events as $dashboard_event) {
            switch ($dashboard_event->type) {
                case "upload":
                case "comment":
                case "answer":
                case "like":
                case "favorite":
                    if ($dashboard_event->type == "upload")
                        $dashboard_event->find_item_moderation_and_status();
                    // First we want to find favorite item_id,
                    // and then replace favorites id
                    // with module_item actual item id.
                    // Then we can use methods below without collisions.
                    if ($dashboard_event->type == "favorite")
                        $dashboard_event->find_favorite_item_id();

                    $dashboard_event->find_category_and_subcategory();
                    $dashboard_event->find_main_photo();
                    $dashboard_event->find_user();
                    $dashboard_event->find_comments_and_likes_count();
                    $dashboard_event->find_views_count();
                    break;

                case "follow":
                    $dashboard_event->find_follow_pair();
                    break;

                case "activated_user":
                case "registred_user":
                    $dashboard_event->find_user();
                    break;
            }
        }

        // If now this page has 0 items and it isn't first page,
        // redirecting to last page
        if (!$dashboard_events and $page != 1) {
            $last_page = $dashboard_events_mapper->pagination->total_pages;

            $url_segments = "dashboard/list/";
            $url_segments .= "page-$last_page/";
            $url_segments .= "days-$days_to_fetch/";
            $url_segments .= "events-$events_to_show";

            $this->ajax->data->url_segments = $url_segments;
            $this->ajax->callback = "redirect";
        } // Else we should update pagination and items
        else {
            $data["dashboard_events"] = $dashboard_events;
            $data["selected_events_to_show"] = $events_to_show;
            $data["pages"] = $dashboard_events_mapper->pagination->make_pages("compact");
            $data["current_page"] = $page;
            $data["current_days_to_fetch"] = $days_to_fetch;

            $this->ajax->callback = "update_dashboard_items_and_pagination_html";
            $this->ajax->data->items_html = View::capture("dashboard" . DS . "dashboard_events", $data);
            $this->ajax->data->pagination_html = View::capture("dashboard" . DS . "dashboard_pagination", $data);
        }
    }

    public function delete_upload($request_type = "",
                                  $item_id = false,
                                  $item_module = false,
                                  $page = false,
                                  $days_to_fetch = false,
                                  $events_to_show = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        switch ($item_module) {
            case "photos":
                $module_model = new Photo_Model;
                break;

            case "spots":
                $module_model = new Spot_Model;
                break;

            case "speed":
                $module_model = new Speed_Model;
                break;

            case "videos":
                $module_model = new Video_Model;
                break;

            default:
                exit("Wrong item module passed to delete_upload action.");
                break;
        }

        $module_item = $module_model->find_by_id($item_id);

        if ($module_item) {
            if ($module_item->delete()) {
                $this->build_ajax_delete_item_response($page,
                    $days_to_fetch,
                    $events_to_show);
                $this->ajax->result = "ok";
                $this->ajax->render();
            }
        } else {
            $message = "Wrong item id passed to delete_upload action,";
            $message .= "or user is just deleted this record          ";
            exit($message);
        }
    }

    public function change_upload_status($request_type = "",
                                         $item_id = false,
                                         $item_module = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        switch ($item_module) {
            case "photos":
                $module_model = new Photo_Model;
                break;

            case "spots":
                $module_model = new Spot_Model;
                break;

            case "speed":
                $module_model = new Speed_Model;
                break;

            case "videos":
                $module_model = new Video_Model;
                break;

            default:
                exit("Wrong item module passed to change_upload_status action.");
                break;
        }

        $module_item = $module_model->find_by_id($item_id);

        if ($module_item) {
            if ($module_item->change_status()) {
                $this->ajax->result = "ok";
                $this->ajax->callback = "update_dashboard_module_item_status";
                $this->ajax->data->status = $module_item->status;

                $this->ajax->render();
            }
        } else {
            $message = "Wrong item id passed to change_upload_status action,";
            $message .= "or user is just deleted this record                 ";
            exit($message);
        }
    }

    public function change_upload_moderation($request_type = "",
                                             $item_id = false,
                                             $item_module = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        switch ($item_module) {
            case "photos":
                $module_model = new Photo_Model;
                break;

            case "spots":
                $module_model = new Spot_Model;
                break;

            case "speed":
                $module_model = new Speed_Model;
                break;

            case "videos":
                $module_model = new Video_Model;
                break;

            default:
                exit("Wrong item module passed to change_upload_moderation action.");
                break;
        }

        $module_item = $module_model->find_by_id($item_id);

        if ($module_item) {
            if ($module_item->change_moderation()) {
                $this->ajax->result = "ok";
                $this->ajax->callback = "update_dashboard_module_item_moderation";
                $this->ajax->data->moderated = $module_item->moderated;

                $this->ajax->render();
            }
        } else {
            $message = "Wrong item id passed to change_upload_moderation action,";
            $message .= "or user is just deleted this record                     ";
            exit($message);
        }
    }

    public function delete_comment($request_type = "",
                                   $comment_id = false,
                                   $comment_module = false,
                                   $page = false,
                                   $days_to_fetch = false,
                                   $events_to_show = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        switch ($comment_module) {
            case "photos":
                $module_comment_model = new Photo_Comment_Model;
                break;

            case "spots":
                $module_comment_model = new Spot_Comment_Model;
                break;

            case "speed":
                $module_comment_model = new Speed_Comment_Model;
                break;

            case "videos":
                $module_comment_model = new Video_Comment_Model;
                break;

            default:
                exit("Wrong item module passed to delete_comment action.");
                break;
        }

        $module_comment = $module_comment_model->find_by_id($comment_id);

        if ($module_comment) {
            if ($module_comment->answer_id == 0)
                $module_comment->delete_with_all_answers();
            else
                $module_comment->delete();

            $this->build_ajax_delete_item_response($page,
                $days_to_fetch,
                $events_to_show);
            $this->ajax->result = "ok";
            $this->ajax->render();
        } else {
            $message = "Wrong item id passed to delete_comment action,";
            $message .= "or user is just deleted this record.          ";
            exit($message);
        }
    }

    public function delete_like($request_type = "",
                                $like_id = false,
                                $like_module = false,
                                $page = false,
                                $days_to_fetch = false,
                                $events_to_show = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        switch ($like_module) {
            case "photos":
                $module_like_model = new Photo_Like_Model;
                break;

            case "spots":
                $module_like_model = new Spot_Like_Model;
                break;

            case "speed":
                $module_like_model = new Speed_Like_Model;
                break;

            case "videos":
                $module_like_model = new Video_Like_Model;
                break;

            default:
                exit("Wrong item module passed to delete_like action.");
                break;
        }

        $module_like = $module_like_model->find_by_id($like_id);

        if ($module_like) {
            if ($module_like->delete()) {
                $this->build_ajax_delete_item_response($page,
                    $days_to_fetch,
                    $events_to_show);
                $this->ajax->result = "ok";
                $this->ajax->render();
            }
        } else {
            $message = "Wrong item id passed to delete_like action,";
            $message .= "or user is just deleted this record.       ";
            exit($message);
        }
    }

    public function delete_follow_pair($request_type = "",
                                       $follow_pair_id = false,
                                       $page = false,
                                       $days_to_fetch = false,
                                       $events_to_show = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        $follower_model = new Follower_Model;
        $follow_pair = $follower_model->find_by_id($follow_pair_id);

        if ($follow_pair) {
            if ($follow_pair->delete()) {
                $this->build_ajax_delete_item_response($page,
                    $days_to_fetch,
                    $events_to_show);
                $this->ajax->result = "ok";
                $this->ajax->render();
            }
        } else {
            $message = "Wrong item id passed to delete_follow_pair action,";
            $message .= "or user is just deleted this record.              ";
            exit($message);
        }
    }

    public function delete_favorite($request_type = "",
                                    $item_id = false,
                                    $item_module = false,
                                    $page = false,
                                    $days_to_fetch = false,
                                    $events_to_show = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        $favorite_model = new Favorite_Model;
        $favorite_item = $favorite_model->find_favorite_by_module($item_id,
            $item_module);

        if ($favorite_item) {
            if ($favorite_item->delete()) {
                $this->build_ajax_delete_item_response($page,
                    $days_to_fetch,
                    $events_to_show);
                $this->ajax->result = "ok";
                $this->ajax->render();
            }
        } else {
            $message = "Wrong item id passed to delete_favorite action,";
            $message .= "or user is just deleted this record.           ";
            exit($message);
        }
    }

    public function delete_user($request_type = "",
                                $user_id = false,
                                $page = false,
                                $days_to_fetch = false,
                                $events_to_show = false)
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        $user_model = new User_Model;
        $user = $user_model->find_by_id($user_id);

        if ($user) {
            if ($user->delete_account()) {
                $this->build_ajax_delete_item_response($page,
                    $days_to_fetch,
                    $events_to_show);
                $this->ajax->result = "ok";
                $this->ajax->render();
            }
        } else {
            exit("Wrong item id or module passed to delete_like action.");
        }
    }
}

?>