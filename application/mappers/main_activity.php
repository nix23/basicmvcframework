<?php

class Main_Activity_Mapper extends Mapper
{
    protected $map_fields = array("id", "related_table_id", "category_id", "user_id", "heading", "secondary_heading",
        "module", "type", "moderated", "text", "posted_on");

    // Attributes to map
    public $id;
    // Here we will store related table item id
    // (likes.id, comments.id, etc...)
    public $related_table_id;
    public $category_id;
    public $user_id;
    public $heading;
    // If item consists of more than 1 column,
    // we can ferch here other column heading parts.
    public $secondary_heading;
    // { 'photos', 'spots', 'speed', 'videos' }
    public $module;
    // { 'comment', 'answer' }
    public $type;
    // Used for comment text
    public $text;
    public $posted_on;

    // Shared attributes
    public $category;
    public $subcategory;
    public $category_name;
    public $subcategory_name;
    public $main_photo;
    public $user;
    public $comments_count;
    public $likes_count;
    public $views_count;

    private function generate_module_comments_sql($module_table,
                                                  $module_comments_table,
                                                  $comments_join_column,
                                                  $module,
                                                  $module_heading_sql,
                                                  $hours_to_fetch)
    {
        $sql = "SELECT $module_table.id                 AS id,                                           ";
        $sql .= "       $module_comments_table.id        AS related_table_id,                             ";
        $sql .= "       $module_table.category_id        AS category_id,                                  ";
        $sql .= "       $module_comments_table.user_id   AS user_id,                                      ";
        $sql .= "       $module_heading_sql                                                               ";
        $sql .= "       '$module'                        AS module,                                       ";
        $sql .= "       'comment'                        AS type,                                         ";
        $sql .= "       $module_comments_table.comment   AS text,                                         ";
        $sql .= "       $module_comments_table.posted_on AS posted_on                                     ";
        $sql .= "  FROM $module_table                                                                     ";
        $sql .= " INNER JOIN $module_comments_table                                                       ";
        $sql .= "         ON $module_table.id = $module_comments_table.$comments_join_column              ";
        $sql .= " WHERE $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $hours_to_fetch hour) ";
        $sql .= "   AND $module_comments_table.answer_id = 0                                              ";
        $sql .= "   AND $module_table.moderated = 'yes'                                                   ";
        $sql .= "   AND $module_table.status = 'enabled'                                                  ";

        return $sql;
    }

    private function generate_module_answers_sql($module_table,
                                                 $module_comments_table,
                                                 $comments_join_column,
                                                 $module,
                                                 $module_heading_sql,
                                                 $hours_to_fetch)
    {
        $sql = "SELECT $module_table.id                 AS id,                                           ";
        $sql .= "       $module_comments_table.id        AS related_table_id,                             ";
        $sql .= "       $module_table.category_id        AS category_id,                                  ";
        $sql .= "       $module_comments_table.user_id   AS user_id,                                      ";
        $sql .= "       $module_heading_sql                                                               ";
        $sql .= "       '$module'                        AS module,                                       ";
        $sql .= "       'answer'                         AS type,                                         ";
        $sql .= "       $module_comments_table.comment   AS text,                                         ";
        $sql .= "       $module_comments_table.posted_on AS posted_on                                     ";
        $sql .= "  FROM $module_table                                                                     ";
        $sql .= " INNER JOIN $module_comments_table                                                       ";
        $sql .= "         ON $module_table.id = $module_comments_table.$comments_join_column              ";
        $sql .= " WHERE $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $hours_to_fetch hour) ";
        $sql .= "   AND $module_comments_table.answer_id != 0                                             ";
        $sql .= "   AND $module_table.moderated = 'yes'                                                   ";
        $sql .= "   AND $module_table.status = 'enabled'                                                  ";

        return $sql;
    }

    public function find_last_activities($items_to_fetch = 5,
                                         $hours_to_fetch = 24)
    {
        $hours_to_fetch = (int)$hours_to_fetch;

        $photoset_model = new Photo_Model;
        $photoset_comment_model = new Photo_Comment_Model;
        $spot_model = new Spot_Model;
        $spot_comment_model = new Spot_Comment_Model;
        $speed_model = new Speed_Model;
        $speed_comment_model = new Speed_Comment_Model;
        $video_model = new Video_Model;
        $video_comment_model = new Video_Comment_Model;

        $photoset = $photoset_model->get_table_name();
        $spot = $spot_model->get_table_name();
        $speed = $speed_model->get_table_name();
        $video = $video_model->get_table_name();

        $photoset_heading_sql = "CAST($photoset.year AS CHAR) AS heading,   CAST($photoset.name AS CHAR) AS secondary_heading,";
        $spot_heading_sql = "CAST($spot.album_name AS CHAR) AS heading, CAST('' AS CHAR) AS secondary_heading,";
        $speed_heading_sql = "CAST($speed.heading AS CHAR) AS heading,   CAST('' AS CHAR) AS secondary_heading,";
        $video_heading_sql = "CAST($video.heading AS CHAR) AS heading,   CAST('' AS CHAR) AS secondary_heading,";

        $sql_modules_source = array(
            array("module_name" => "photos",
                "module_items_table" => $photoset_model->get_table_name(),
                "module_heading_sql" => $photoset_heading_sql,
                "module_comments_table" => $photoset_comment_model->get_table_name(),
                "comments_join_column" => "photo_id"),

            array("module_name" => "spots",
                "module_items_table" => $spot_model->get_table_name(),
                "module_heading_sql" => $spot_heading_sql,
                "module_comments_table" => $spot_comment_model->get_table_name(),
                "comments_join_column" => "spot_id"),

            array("module_name" => "speed",
                "module_items_table" => $speed_model->get_table_name(),
                "module_heading_sql" => $speed_heading_sql,
                "module_comments_table" => $speed_comment_model->get_table_name(),
                "comments_join_column" => "speed_id"),

            array("module_name" => "videos",
                "module_items_table" => $video_model->get_table_name(),
                "module_heading_sql" => $video_heading_sql,
                "module_comments_table" => $video_comment_model->get_table_name(),
                "comments_join_column" => "video_id"),
        );

        // *** Fetching activities
        $sql_parts = array();

        foreach ($sql_modules_source as $sql_module_source) {
            $sql_parts[] = $this->generate_module_comments_sql($sql_module_source["module_items_table"],
                $sql_module_source["module_comments_table"],
                $sql_module_source["comments_join_column"],
                $sql_module_source["module_name"],
                $sql_module_source["module_heading_sql"],
                $hours_to_fetch);

            $sql_parts[] = $this->generate_module_answers_sql($sql_module_source["module_items_table"],
                $sql_module_source["module_comments_table"],
                $sql_module_source["comments_join_column"],
                $sql_module_source["module_name"],
                $sql_module_source["module_heading_sql"],
                $hours_to_fetch);
        }

        $sql = "SELECT * FROM (";
        $sql .= implode(" UNION ALL ", $sql_parts);
        $sql .= ") AS last_activities ";
        $sql .= "ORDER BY last_activities.posted_on DESC ";
        $sql .= "LIMIT %d ";

        $sql = sprintf($sql,
            $this->database->escape_value($items_to_fetch));
        return $this->find_by_sql($sql);
    }

    public function find_category_and_subcategory()
    {
        $category = new Category_Model;
        $this->category = $category->find_by_id($this->category_id);

        if ($this->category->parent_id != '0') {
            $this->subcategory = $this->category;
            $this->category = $category->find_by_id($this->subcategory->parent_id);
        }

        if ($this->category) $this->category_name = $this->category->name;
        if ($this->subcategory) $this->subcategory_name = $this->subcategory->name;
    }

    public function find_main_photo()
    {
        switch ($this->module) {
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

        $this->main_photo = $module_photo_model->find_main_photo_on($this->id);
    }

    public function find_user()
    {
        $user_model = new User_Model;
        $this->user = $user_model->find_by_id($this->user_id);
    }

    public function find_comments_and_likes_count()
    {
        switch ($this->module) {
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
        }

        $module_model->id = $this->id;

        $module_model->find_likes_count();
        $module_model->find_comments_count();

        $this->likes_count = $module_model->likes_count;
        $this->comments_count = $module_model->comments_total_count;
    }

    public function find_views_count()
    {
        switch ($this->module) {
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
        }

        $module_item = $module_model->find_by_id($this->id);
        $module_item->find_views_count();
        $this->views_count = $module_item->item_views_count;
    }
}

?>