<?php

class Activity_Mapper extends Mapper
{
    protected $map_fields = array("id", "category_id", "user_id", "heading", "secondary_heading",
        "module", "type", "text", "posted_on");

    // Attributes to map
    public $id;
    public $category_id;
    public $user_id;
    public $heading;
    // If item heading consists of more than 1 column,
    // we can fetch here other columns heading parts.
    public $secondary_heading;
    // { 'photos', 'spots', 'speed', 'videos' }
    public $module;
    // { 'like', 'comment', 'answer' }
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

    private function generate_module_comments_count_sql($module_table,
                                                        $module_comments_table,
                                                        $comments_join_column,
                                                        $user_id,
                                                        $days_to_fetch)
    {
        $sql = "SELECT COUNT(*) AS count                                                               ";
        $sql .= "  FROM $module_table                                                                   ";
        $sql .= " INNER JOIN $module_comments_table                                                     ";
        $sql .= "         ON $module_table.id = $module_comments_table.$comments_join_column            ";
        $sql .= " WHERE $module_table.user_id = $user_id                                                ";
        $sql .= "   AND $module_comments_table.user_id != $user_id                                      ";
        $sql .= "   AND $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $days_to_fetch day) ";
        $sql .= "   AND $module_table.status = 'enabled'                                                ";
        $sql .= "   AND $module_table.moderated = 'yes'                                                 ";

        return $sql;
    }

    private function generate_module_likes_count_sql($module_table,
                                                     $module_likes_table,
                                                     $likes_join_column,
                                                     $user_id,
                                                     $days_to_fetch)
    {
        $sql = "SELECT COUNT(*) AS count                                                            ";
        $sql .= "  FROM $module_table                                                                ";
        $sql .= " INNER JOIN $module_likes_table                                                     ";
        $sql .= "         ON $module_table.id = $module_likes_table.$likes_join_column               ";
        $sql .= " WHERE $module_table.user_id = $user_id                                             ";
        $sql .= "   AND $module_likes_table.user_id != $user_id                                      ";
        $sql .= "   AND $module_likes_table.posted_on > DATE_SUB(NOW(), INTERVAL $days_to_fetch day) ";
        $sql .= "   AND $module_table.status = 'enabled'                                             ";
        $sql .= "   AND $module_table.moderated = 'yes'                                              ";

        return $sql;
    }

    private function generate_module_answers_count_sql($module_table,
                                                       $module_comments_table,
                                                       $comments_join_column,
                                                       $user_root_comment_ids,
                                                       $user_id,
                                                       $days_to_fetch)
    {
        $sql = "SELECT COUNT(*) as count                                                               ";
        $sql .= "  FROM $module_table                                                                   ";
        $sql .= " INNER JOIN $module_comments_table                                                     ";
        $sql .= "         ON $module_table.id = $module_comments_table.$comments_join_column            ";
        $sql .= " WHERE $module_comments_table.answer_id IN ($user_root_comment_ids)                    ";
        $sql .= "   AND $module_comments_table.user_id != $user_id                                      ";
        $sql .= "   AND $module_table.user_id != $user_id                                               ";
        $sql .= "   AND $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $days_to_fetch day) ";
        $sql .= "   AND $module_table.status = 'enabled'                                                ";
        $sql .= "   AND $module_table.moderated = 'yes'                                                 ";

        return $sql;
    }

    private function generate_module_comments_sql($module_table,
                                                  $module_comments_table,
                                                  $comments_join_column,
                                                  $module,
                                                  $module_heading_sql,
                                                  $user_id,
                                                  $days_to_fetch)
    {
        $sql = "SELECT $module_table.id                 AS id,                                         ";
        $sql .= "       $module_table.category_id        AS category_id,                                ";
        $sql .= "       $module_comments_table.user_id   AS user_id,                                    ";
        $sql .= "       $module_heading_sql                                                             ";
        $sql .= "       $module_comments_table.comment   AS text,                                       ";
        $sql .= "       '$module'                        AS module,                                     ";
        $sql .= "       'comment'                        AS type,                                       ";
        $sql .= "       $module_comments_table.posted_on AS posted_on                                   ";
        $sql .= "  FROM $module_table                                                                   ";
        $sql .= " INNER JOIN $module_comments_table                                                     ";
        $sql .= "         ON $module_table.id = $module_comments_table.$comments_join_column            ";
        $sql .= " WHERE $module_table.user_id = $user_id                                                ";
        $sql .= "   AND $module_comments_table.user_id != $user_id                                      ";
        $sql .= "   AND $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $days_to_fetch day) ";
        $sql .= "   AND $module_table.status = 'enabled'                                                ";
        $sql .= "   AND $module_table.moderated = 'yes'                                                 ";

        return $sql;
    }

    private function generate_module_likes_sql($module_table,
                                               $module_likes_table,
                                               $likes_join_column,
                                               $module,
                                               $module_heading_sql,
                                               $user_id,
                                               $days_to_fetch)
    {
        $sql = "SELECT $module_table.id              AS id,                                         ";
        $sql .= "       $module_table.category_id     AS category_id,                                ";
        $sql .= "       $module_likes_table.user_id   AS user_id,                                    ";
        $sql .= "       $module_heading_sql                                                          ";
        $sql .= "       ''                            AS text,                                       ";
        $sql .= "       '$module'                     AS module,                                     ";
        $sql .= "       'like'                        AS type,                                       ";
        $sql .= "       $module_likes_table.posted_on AS posted_on                                   ";
        $sql .= "  FROM $module_table                                                                ";
        $sql .= " INNER JOIN $module_likes_table                                                     ";
        $sql .= "         ON $module_table.id = $module_likes_table.$likes_join_column               ";
        $sql .= " WHERE $module_table.user_id = $user_id                                             ";
        $sql .= "   AND $module_likes_table.user_id != $user_id                                      ";
        $sql .= "   AND $module_likes_table.posted_on > DATE_SUB(NOW(), INTERVAL $days_to_fetch day) ";
        $sql .= "   AND $module_table.status = 'enabled'                                             ";
        $sql .= "   AND $module_table.moderated = 'yes'                                              ";

        return $sql;
    }

    private function generate_module_answers_sql($module_table,
                                                 $module_comments_table,
                                                 $comments_join_column,
                                                 $user_root_comment_ids,
                                                 $module,
                                                 $module_heading_sql,
                                                 $user_id,
                                                 $days_to_fetch)
    {
        $sql = "SELECT $module_table.id                 AS id,                                         ";
        $sql .= "       $module_table.category_id        AS category_id,                                ";
        $sql .= "       $module_comments_table.user_id   AS user_id,                                    ";
        $sql .= "       $module_heading_sql                                                             ";
        $sql .= "       $module_comments_table.comment   AS text,                                       ";
        $sql .= "       '$module'                        AS module,                                     ";
        $sql .= "       'answer'                         AS type,                                       ";
        $sql .= "       $module_comments_table.posted_on AS posted_on                                   ";
        $sql .= "  FROM $module_table                                                                   ";
        $sql .= " INNER JOIN $module_comments_table                                                     ";
        $sql .= "         ON $module_table.id = $module_comments_table.$comments_join_column            ";
        $sql .= " WHERE $module_comments_table.answer_id IN ($user_root_comment_ids)                    ";
        $sql .= "   AND $module_comments_table.user_id != $user_id                                      ";
        $sql .= "   AND $module_table.user_id != $user_id                                               ";
        $sql .= "   AND $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $days_to_fetch day) ";
        $sql .= "   AND $module_table.status = 'enabled'                                                ";
        $sql .= "   AND $module_table.moderated = 'yes'                                                 ";

        return $sql;
    }

    public function find_activities_by($user_id = false,
                                       $page = 1,
                                       $days_to_fetch = 1,
                                       $validate_page = true)
    {
        $user_id = (int)$user_id;
        $days_to_fetch = (int)$days_to_fetch;

        $photoset_model = new Photo_Model;
        $photoset_comment_model = new Photo_Comment_Model;
        $photoset_like_model = new Photo_Like_Model;
        $spot_model = new Spot_Model;
        $spot_comment_model = new Spot_Comment_Model;
        $spot_like_model = new Spot_Like_Model;
        $speed_model = new Speed_Model;
        $speed_comment_model = new Speed_Comment_Model;
        $speed_like_model = new Speed_Like_Model;
        $video_model = new Video_Model;
        $video_comment_model = new Video_Comment_Model;
        $video_like_model = new Video_Like_Model;

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
                "comments_join_column" => "photo_id",
                "module_user_comments_ids" => $photoset_comment_model->get_all_root_comment_ids_packed_by($user_id),
                "module_likes_table" => $photoset_like_model->get_table_name(),
                "likes_join_column" => "photo_id"),

            array("module_name" => "spots",
                "module_items_table" => $spot_model->get_table_name(),
                "module_heading_sql" => $spot_heading_sql,
                "module_comments_table" => $spot_comment_model->get_table_name(),
                "comments_join_column" => "spot_id",
                "module_user_comments_ids" => $spot_comment_model->get_all_root_comment_ids_packed_by($user_id),
                "module_likes_table" => $spot_like_model->get_table_name(),
                "likes_join_column" => "spot_id"),

            array("module_name" => "speed",
                "module_items_table" => $speed_model->get_table_name(),
                "module_heading_sql" => $speed_heading_sql,
                "module_comments_table" => $speed_comment_model->get_table_name(),
                "comments_join_column" => "speed_id",
                "module_user_comments_ids" => $speed_comment_model->get_all_root_comment_ids_packed_by($user_id),
                "module_likes_table" => $speed_like_model->get_table_name(),
                "likes_join_column" => "speed_id"),

            array("module_name" => "videos",
                "module_items_table" => $video_model->get_table_name(),
                "module_heading_sql" => $video_heading_sql,
                "module_comments_table" => $video_comment_model->get_table_name(),
                "comments_join_column" => "video_id",
                "module_user_comments_ids" => $video_comment_model->get_all_root_comment_ids_packed_by($user_id),
                "module_likes_table" => $video_like_model->get_table_name(),
                "likes_join_column" => "video_id"),
        );

        // Making pagination
        $sql_parts = array();

        foreach ($sql_modules_source as $sql_module_source) {
            $sql_parts[] = $this->generate_module_comments_count_sql($sql_module_source["module_items_table"],
                $sql_module_source["module_comments_table"],
                $sql_module_source["comments_join_column"],
                $user_id,
                $days_to_fetch);

            $sql_parts[] = $this->generate_module_likes_count_sql($sql_module_source["module_items_table"],
                $sql_module_source["module_likes_table"],
                $sql_module_source["likes_join_column"],
                $user_id,
                $days_to_fetch);

            if ($sql_module_source["module_user_comments_ids"]) {
                $sql_parts[] = $this->generate_module_answers_count_sql($sql_module_source["module_items_table"],
                    $sql_module_source["module_comments_table"],
                    $sql_module_source["comments_join_column"],
                    $sql_module_source["module_user_comments_ids"],
                    $user_id,
                    $days_to_fetch);
            }
        }

        $sql = "SELECT SUM(count) AS count FROM (";
        $sql .= implode(" UNION ALL ", $sql_parts);
        $sql .= ") AS total_count";

        $this->pagination = new Pagination($page, $this->find_count_by_sql($sql));
        if ($validate_page) $this->pagination->validate_page_range();

        // Fetching posts
        $sql_parts = array();

        foreach ($sql_modules_source as $sql_module_source) {
            $sql_parts[] = $this->generate_module_comments_sql($sql_module_source["module_items_table"],
                $sql_module_source["module_comments_table"],
                $sql_module_source["comments_join_column"],
                $sql_module_source["module_name"],
                $sql_module_source["module_heading_sql"],
                $user_id,
                $days_to_fetch);

            $sql_parts[] = $this->generate_module_likes_sql($sql_module_source["module_items_table"],
                $sql_module_source["module_likes_table"],
                $sql_module_source["likes_join_column"],
                $sql_module_source["module_name"],
                $sql_module_source["module_heading_sql"],
                $user_id,
                $days_to_fetch);

            if ($sql_module_source["module_user_comments_ids"]) {
                $sql_parts[] = $this->generate_module_answers_sql($sql_module_source["module_items_table"],
                    $sql_module_source["module_comments_table"],
                    $sql_module_source["comments_join_column"],
                    $sql_module_source["module_user_comments_ids"],
                    $sql_module_source["module_name"],
                    $sql_module_source["module_heading_sql"],
                    $user_id,
                    $days_to_fetch);
            }
        }

        $sql = "SELECT * FROM (";
        $sql .= implode(" UNION ALL ", $sql_parts);
        $sql .= ") AS activity_at_user_posts ";
        $sql .= "ORDER BY activity_at_user_posts.posted_on DESC ";
        $sql .= "LIMIT  {$this->pagination->records_per_page}   ";
        $sql .= "OFFSET {$this->pagination->offset}             ";

        return $this->find_by_sql($sql);
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
}

?>