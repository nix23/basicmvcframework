<?php

class Most_Active_Posts_Mapper extends Mapper
{
    // Shared attributes
    /*
     *  $most_active_item_canditates = array(
     *  ["photos-13"] => array("comments_count" => x,
     *                                "likes_count"    => y,
     *                           "views_count"    => z,...),
     *    ["spots-13"] => array(......)
     *  )
     */
    private $most_active_item_candidates = array();

    private function get_items_array_index($module_item_id,
                                           $module_name)
    {
        $index = "$module_name-$module_item_id";

        if (!array_key_exists($index, $this->most_active_item_candidates)) {
            $this->most_active_item_candidates[$index] = array();
            $this->most_active_item_candidates[$index]["comments_count"] = 0;
            $this->most_active_item_candidates[$index]["likes_count"] = 0;
            $this->most_active_item_candidates[$index]["views_count"] = 0;
            $this->most_active_item_candidates[$index]["activity"] = 0;
            $this->most_active_item_candidates[$index]["module_item_id"] = $module_item_id;
            $this->most_active_item_candidates[$index]["module_name"] = $module_name;
        }

        return $index;
    }

    private function generate_all_post_comments_in_last_n_hours_sql($hours,
                                                                    $sql_modules_source)
    {
        $sql_parts = array();

        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_comments_table = $sql_module_source["module_comments_table"];
            $comments_join_column = $sql_module_source["comments_join_column"];

            $sql = "";
            $sql .= "SELECT $module_items_table.id as module_item_id,                                ";
            $sql .= "       COUNT(*) as count,                                                       ";
            $sql .= "       '{$sql_module_source["module_name"]}' as module_name,                    ";
            $sql .= "        'comment' as type                                                        ";
            $sql .= "  FROM $module_items_table                                                      ";
            $sql .= " INNER JOIN $module_comments_table                                              ";
            $sql .= " ON $module_items_table.id = $module_comments_table.$comments_join_column       ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                                    ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                                ";
            $sql .= "   AND $module_comments_table.posted_on > DATE_SUB(NOW(), INTERVAL $hours hour) ";
            $sql .= " GROUP BY $module_comments_table.$comments_join_column                          ";

            $sql_parts[] = $sql;
        }

        return $sql_parts;
    }

    private function generate_all_post_likes_in_last_n_hours_sql($hours,
                                                                 $sql_modules_source)
    {
        $sql_parts = array();

        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_likes_table = $sql_module_source["module_likes_table"];
            $likes_join_column = $sql_module_source["likes_join_column"];

            $sql = "";
            $sql .= "SELECT $module_items_table.id as module_item_id,                             ";
            $sql .= "       COUNT(*) as count,                                                    ";
            $sql .= "       '{$sql_module_source["module_name"]}' as module_name,                 ";
            $sql .= "       'like' as type                                                        ";
            $sql .= "  FROM $module_items_table                                                   ";
            $sql .= " INNER JOIN $module_likes_table                                              ";
            $sql .= " ON $module_items_table.id = $module_likes_table.$likes_join_column          ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                                 ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                             ";
            $sql .= "   AND $module_likes_table.posted_on > DATE_SUB(NOW(), INTERVAL $hours hour) ";
            $sql .= " GROUP BY $module_likes_table.$likes_join_column                             ";

            $sql_parts[] = $sql;
        }

        return $sql_parts;
    }

    private function generate_all_post_views_in_last_n_hours_sql($hours,
                                                                 $sql_modules_source)
    {
        $sql_parts = array();
        $item_view_model = new Item_View_Model;
        $item_views = $item_view_model->get_table_name();

        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_name = $sql_module_source["module_name"];

            $sql = "";
            $sql .= "SELECT $module_items_table.id as module_item_id,                     ";
            $sql .= "       COUNT(*) as count,                                            ";
            $sql .= "       '{$sql_module_source["module_name"]}' as module_name,         ";
            $sql .= "       'view' as type                                                ";
            $sql .= "  FROM $item_views                                                   ";
            $sql .= " INNER JOIN $module_items_table                                      ";
            $sql .= " ON $module_items_table.id = $item_views.item_id                     ";
            $sql .= " WHERE $item_views.module = '$module_name'                           ";
            $sql .= "   AND $module_items_table.moderated = 'yes'                         ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                     ";
            $sql .= "   AND $item_views.posted_on > DATE_SUB(NOW(), INTERVAL $hours hour) ";
            $sql .= " GROUP BY $item_views.item_id                                        ";

            $sql_parts[] = $sql;
        }

        return $sql_parts;
    }

    public function calculate_activity_per_every_most_active_item_candidate()
    {
        foreach ($this->most_active_item_candidates as &$most_active_item_candidate) {
            $most_active_item_candidate["activity"] += $most_active_item_candidate["comments_count"];
            $most_active_item_candidate["activity"] += $most_active_item_candidate["likes_count"] * 0.1;
            $most_active_item_candidate["activity"] += $most_active_item_candidate["views_count"] * 0.01;
        }
    }

    public function compare_activities($first_most_active_item_candidate_array,
                                       $second_most_active_item_candidate_array)
    {
        if ($first_most_active_item_candidate_array["activity"] == $second_most_active_item_candidate_array["activity"])
            return 0;
        else if ($first_most_active_item_candidate_array["activity"] < $second_most_active_item_candidate_array["activity"])
            return 1;
        else
            return -1;
    }

    protected function grab_last_actions_by_sql($sql)
    {
        $all_last_actions = $this->database->query($sql);
        while ($last_action = $this->database->fetch_array($all_last_actions)) {
            $module_name = $last_action["module_name"];
            $module_item_id = $last_action["module_item_id"];
            $actions_count = $last_action["count"];
            $type = $last_action["type"];

            $item_index = $this->get_items_array_index($module_item_id,
                $module_name);

            switch ($type) {
                case 'like':
                    $this->most_active_item_candidates[$item_index]["likes_count"] = $actions_count;
                    break;

                case 'view':
                    $this->most_active_item_candidates[$item_index]["views_count"] = $actions_count;
                    break;

                case 'comment':
                    $this->most_active_item_candidates[$item_index]["comments_count"] = $actions_count;
                    break;

                default:
                    throw new Exception("Unknown row type in Most Active Posts mapper.");
                    break;
            }
        }
    }

    public function find_most_active_posts_in_last($hours = 24)
    {
        $hours = (int)$hours;
        $this->most_active_item_candidates = array();

        $photoset_model = new Photo_Model;
        $spot_model = new Spot_Model;
        $speed_model = new Speed_Model;
        $video_model = new Video_Model;
        $photoset_likes_model = new Photo_Like_Model;
        $spot_likes_model = new Spot_Like_Model;
        $speed_likes_model = new Speed_Like_Model;
        $video_likes_model = new Video_Like_Model;
        $photoset_comments_model = new Photo_Comment_Model;
        $spot_comments_model = new Spot_Comment_Model;
        $speed_comments_model = new Speed_Comment_Model;
        $video_comments_model = new Video_Comment_Model;

        $photosets = $photoset_model->get_table_name();
        $spots = $spot_model->get_table_name();
        $speeds = $speed_model->get_table_name();
        $videos = $video_model->get_table_name();
        $photoset_likes = $photoset_likes_model->get_table_name();
        $spot_likes = $spot_likes_model->get_table_name();
        $speed_likes = $speed_likes_model->get_table_name();
        $video_likes = $video_likes_model->get_table_name();
        $photoset_comments = $photoset_comments_model->get_table_name();
        $spot_comments = $spot_comments_model->get_table_name();
        $speed_comments = $speed_comments_model->get_table_name();
        $video_comments = $video_comments_model->get_table_name();

        $sql_modules_source = array(array("module_items_table" => $photosets,
            "module_name" => "photos",
            "module_likes_table" => $photoset_likes,
            "likes_join_column" => "photo_id",
            "module_comments_table" => $photoset_comments,
            "comments_join_column" => "photo_id"),

            array("module_items_table" => $spots,
                "module_name" => "spots",
                "module_likes_table" => $spot_likes,
                "likes_join_column" => "spot_id",
                "module_comments_table" => $spot_comments,
                "comments_join_column" => "spot_id"),

            array("module_items_table" => $speeds,
                "module_name" => "speed",
                "module_likes_table" => $speed_likes,
                "likes_join_column" => "speed_id",
                "module_comments_table" => $speed_comments,
                "comments_join_column" => "speed_id"),

            array("module_items_table" => $videos,
                "module_name" => "videos",
                "module_likes_table" => $video_likes,
                "likes_join_column" => "video_id",
                "module_comments_table" => $video_comments,
                "comments_join_column" => "video_id"));

        $sql_parts = array();
        $grabbed_sql_parts = $this->generate_all_post_comments_in_last_n_hours_sql($hours,
            $sql_modules_source);
        $sql_parts = array_merge($sql_parts, $grabbed_sql_parts);

        $grabbed_sql_parts = $this->generate_all_post_likes_in_last_n_hours_sql($hours,
            $sql_modules_source);
        $sql_parts = array_merge($sql_parts, $grabbed_sql_parts);

        $grabbed_sql_parts = $this->generate_all_post_views_in_last_n_hours_sql($hours,
            $sql_modules_source);
        $sql_parts = array_merge($sql_parts, $grabbed_sql_parts);

        $sql = implode(" UNION ", $sql_parts);
        $this->grab_last_actions_by_sql($sql);

        $this->calculate_activity_per_every_most_active_item_candidate();
        usort($this->most_active_item_candidates, array("Most_Active_Posts_Mapper", "compare_activities"));

        return $this->most_active_item_candidates;
    }
}

?>