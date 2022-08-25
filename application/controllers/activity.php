<?php

class Activity_Controller extends Public_Controller
{
    // Restrict access to unauthorized users
    public function before()
    {
        $this->show_404_if_not_authorized();
        parent::before();
    }

    public function index($page = 1,
                          $days_to_fetch = 1)
    {
        $days_to_fetch_items = array(
            (object)array("label" => "Day", "value" => "1", "selected" => false),
            (object)array("label" => "Week", "value" => "7", "selected" => false),
            (object)array("label" => "Month", "value" => "30", "selected" => false)
        );

        foreach ($days_to_fetch_items as $days_to_fetch_item) {
            if ((int)$days_to_fetch_item->value == $days_to_fetch)
                $days_to_fetch_item->selected = true;
        }

        $activity_mapper = new Activity_Mapper;
        // TODO --> USER DAYS_TO_FETCH!!!
        $activities = $activity_mapper->find_activities_by($this->session->user_id,
            $page,
            $days_to_fetch);

        foreach ($activities as $activity) {
            $activity->find_category_and_subcategory();
            $activity->find_main_photo();
            $activity->find_user();
            $activity->find_comments_and_likes_count();
        }

        $data['activities'] = $activities;
        $data['pages'] = $activity_mapper->pagination->make_pages("compact");
        $data['current_page'] = $page;
        $data['days_to_fetch_items'] = $days_to_fetch_items;
        $data['current_days_to_fetch'] = $days_to_fetch;

        $this->page_title = "Activity / Fordrive";
        $this->view->content = View::capture("activity" . DS . "activity_list", $data);
    }
}

?>