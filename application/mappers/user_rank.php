<?php

class User_Rank_Mapper extends Mapper
{
    // Shared attributes
    public $users_data;
    public $user_model;

    public function __construct()
    {
        parent::__construct();
        $this->users_data = array();
        $this->user_model = new User_Model;
    }

    // Fills $users_data array
    private function initialize_users_data()
    {
        $users = $this->user_model->get_table_name();

        $sql = "SELECT $users.id   as user_id,     ";
        $sql .= "       $users.rank as current_rank ";
        $sql .= "  FROM $users                      ";

        $users = $this->database->query($sql);
        while ($user = $this->database->fetch_array($users)) {
            $user_id = $user["user_id"];
            $current_rank = $user["current_rank"];

            $this->users_data[$user_id] = array();
            $this->users_data[$user_id]["user_id"] = $user_id;
            $this->users_data[$user_id]["current_rank"] = $current_rank;
            $this->users_data[$user_id]["total_rating"] = 0;
            $this->users_data[$user_id]["user_posts_views_count"] = 0;
            $this->users_data[$user_id]["user_followers_count"] = 0;
            $this->users_data[$user_id]["likes_count_at_user_posts"] = 0;
            $this->users_data[$user_id]["comments_count_at_user_posts"] = 0;
            $this->users_data[$user_id]["likes_count_by_user"] = 0;
            $this->users_data[$user_id]["comments_count_by_user"] = 0;
        }
    }

    private function find_all_users_posts_views_count($sql_modules_source)
    {
        $item_view_model = new Item_View_Model;
        $item_views = $item_view_model->get_table_name();

        // *** Fetching item views from 'item_views' table
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_name = $sql_module_source["module_name"];

            $sql = "SELECT $module_items_table.user_id as user_id,   ";
            $sql .= "       COUNT(*)                    as count      ";
            $sql .= "  FROM $item_views                               ";
            $sql .= " INNER JOIN $module_items_table                  ";
            $sql .= " ON $module_items_table.id = $item_views.item_id ";
            $sql .= " WHERE $item_views.module = '$module_name'       ";
            $sql .= "   AND $module_items_table.moderated = 'yes'     ";
            $sql .= "   AND $module_items_table.status    = 'enabled' ";
            $sql .= " GROUP BY $module_items_table.user_id            ";

            $all_users_item_views = $this->database->query($sql);
            while ($user_item_views = $this->database->fetch_array($all_users_item_views)) {
                $user_id = $user_item_views["user_id"];
                $item_views_count = $user_item_views["count"];

                $this->users_data[$user_id]["user_posts_views_count"] += $item_views_count;
            }
        }

        // *** Fetching item views from module stats table
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_stats_table = $sql_module_source["module_stats_table"];
            $stats_join_column = $sql_module_source["stats_join_column"];

            $sql = "SELECT SUM($module_stats_table.views_count) AS views_count,         ";
            $sql .= "       $module_items_table.user_id                                  ";
            $sql .= "  FROM $module_items_table                                          ";
            $sql .= " INNER JOIN $module_stats_table                                     ";
            $sql .= " ON $module_items_table.id = $module_stats_table.$stats_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                        ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                    ";
            $sql .= " GROUP BY $module_items_table.user_id                               ";

            $all_users_item_views_packed = $this->database->query($sql);
            while ($user_item_views = $this->database->fetch_array($all_users_item_views_packed)) {
                $user_id = $user_item_views["user_id"];
                $item_views_count = $user_item_views["views_count"];

                $this->users_data[$user_id]["user_posts_views_count"] += $item_views_count;
            }
        }
    }

    private function find_all_users_followers_count()
    {
        $follower_model = new Follower_Model;
        $followers = $follower_model->get_table_name();

        $sql = "SELECT $followers.followed_id AS user_id,        ";
        $sql .= "       COUNT(*)               AS followers_count ";
        $sql .= "  FROM $followers                                ";
        $sql .= " GROUP BY followed_id                            ";

        $all_users_followers = $this->database->query($sql);
        while ($user_followers = $this->database->fetch_array($all_users_followers)) {
            $user_id = $user_followers["user_id"];
            $followers_count = $user_followers["followers_count"];

            $this->users_data[$user_id]["user_followers_count"] += $followers_count;
        }
    }

    private function find_all_users_likes_count_at_user_posts($sql_modules_source)
    {
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_likes_table = $sql_module_source["module_likes_table"];
            $likes_join_column = $sql_module_source["likes_join_column"];

            $sql = "SELECT $module_items_table.user_id as user_id,                      ";
            $sql .= "       COUNT(*)                    as count                         ";
            $sql .= "  FROM $module_items_table                                          ";
            $sql .= " INNER JOIN $module_likes_table                                     ";
            $sql .= " ON $module_items_table.id = $module_likes_table.$likes_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                        ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                    ";
            $sql .= " GROUP BY $module_items_table.user_id                               ";

            $all_users_posts_likes = $this->database->query($sql);
            while ($user_posts_likes = $this->database->fetch_array($all_users_posts_likes)) {
                $user_id = $user_posts_likes["user_id"];
                $likes_count_at_all_user_posts = $user_posts_likes["count"];

                $this->users_data[$user_id]["likes_count_at_user_posts"] += $likes_count_at_all_user_posts;
            }
        }
    }

    private function find_all_users_comments_count_at_user_posts($sql_modules_source)
    {
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_comments_table = $sql_module_source["module_comments_table"];
            $comments_join_column = $sql_module_source["comments_join_column"];

            $sql = "SELECT $module_items_table.user_id as user_id,                            ";
            $sql .= "       COUNT(*)                    as count                               ";
            $sql .= "  FROM $module_items_table                                                ";
            $sql .= " INNER JOIN $module_comments_table                                        ";
            $sql .= " ON $module_items_table.id = $module_comments_table.$comments_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                              ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                          ";
            $sql .= " GROUP BY $module_items_table.user_id                                     ";

            $all_users_posts_comments = $this->database->query($sql);
            while ($user_posts_comments = $this->database->fetch_array($all_users_posts_comments)) {
                $user_id = $user_posts_comments["user_id"];
                $comments_count_at_all_user_posts = $user_posts_comments["count"];

                $this->users_data[$user_id]["comments_count_at_user_posts"] += $comments_count_at_all_user_posts;
            }
        }
    }

    private function find_all_users_likes_count_by_user($sql_modules_source)
    {
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_likes_table = $sql_module_source["module_likes_table"];
            $likes_join_column = $sql_module_source["likes_join_column"];

            $sql = "SELECT $module_likes_table.user_id as user_id,                      ";
            $sql .= "       COUNT(*)                    as count                         ";
            $sql .= "  FROM $module_items_table                                          ";
            $sql .= " INNER JOIN $module_likes_table                                     ";
            $sql .= " ON $module_items_table.id = $module_likes_table.$likes_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                        ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                    ";
            $sql .= " GROUP BY $module_likes_table.user_id                               ";

            $all_likes_by_all_users = $this->database->query($sql);
            while ($likes_by_user = $this->database->fetch_array($all_likes_by_all_users)) {
                $user_id = $likes_by_user["user_id"];
                $likes_count_by_user = $likes_by_user["count"];

                $this->users_data[$user_id]["likes_count_by_user"] += $likes_count_by_user;
            }
        }
    }

    private function find_all_users_comments_count_by_user($sql_modules_source)
    {
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_comments_table = $sql_module_source["module_comments_table"];
            $comments_join_column = $sql_module_source["comments_join_column"];

            $sql = "SELECT $module_comments_table.user_id as user_id,                         ";
            $sql .= "       COUNT(*)                       as count                            ";
            $sql .= "  FROM $module_items_table                                                ";
            $sql .= " INNER JOIN $module_comments_table                                        ";
            $sql .= " ON $module_items_table.id = $module_comments_table.$comments_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                              ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                          ";
            $sql .= " GROUP BY $module_comments_table.user_id                                  ";

            $all_comments_by_all_users = $this->database->query($sql);
            while ($comments_by_user = $this->database->fetch_array($all_comments_by_all_users)) {
                $user_id = $comments_by_user["user_id"];
                $comments_count_by_user = $comments_by_user["count"];

                $this->users_data[$user_id]["comments_count_by_user"] += $comments_count_by_user;
            }
        }
    }

    public static function compare_ratings($first_rating_array,
                                           $second_rating_array)
    {
        if ($first_rating_array["total_rating"] == $second_rating_array["total_rating"])
            return 0;
        else if ($first_rating_array["total_rating"] < $second_rating_array["total_rating"])
            return 1;
        else
            return -1;
    }

    private function calculate_total_rating_per_every_user()
    {
        foreach ($this->users_data as &$user_data) {
            $user_data["total_rating"] += $user_data["user_posts_views_count"] * 0.01;
            $user_data["total_rating"] += $user_data["user_followers_count"];
            $user_data["total_rating"] += $user_data["likes_count_at_user_posts"];
            $user_data["total_rating"] += $user_data["comments_count_at_user_posts"];
            $user_data["total_rating"] += $user_data["likes_count_by_user"] * 0.1;
            $user_data["total_rating"] += $user_data["comments_count_by_user"] * 0.1;
        }
    }

    private function update_ranks_in_users_table()
    {
        $new_rank = 1;
        foreach ($this->users_data as $user_data) {
            if ($user_data["current_rank"] != $new_rank) {
                $users = $this->user_model->get_table_name();
                $sql = "UPDATE $users SET rank = $new_rank ";
                $sql .= " WHERE id = %d                     ";

                $sql = sprintf($sql,
                    $this->database->escape_value($user_data["user_id"]));
                $this->database->query($sql);
            }

            $new_rank++;
        }
    }

    public function recalculate()
    {
        $this->initialize_users_data();

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
        $photoset_stats_model = new Photo_Stats_Model;
        $spot_stats_model = new Spot_Stats_Model;
        $speed_stats_model = new Speed_Stats_Model;
        $video_stats_model = new Video_Stats_Model;

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
        $photoset_stats = $photoset_stats_model->get_table_name();
        $spot_stats = $spot_stats_model->get_table_name();
        $speed_stats = $speed_stats_model->get_table_name();
        $video_stats = $video_stats_model->get_table_name();

        $sql_modules_source = array(array("module_items_table" => $photosets,
            "module_name" => "photos",
            "module_likes_table" => $photoset_likes,
            "likes_join_column" => "photo_id",
            "module_comments_table" => $photoset_comments,
            "comments_join_column" => "photo_id",
            "module_stats_table" => $photoset_stats,
            "stats_join_column" => "photo_id"),

            array("module_items_table" => $spots,
                "module_name" => "spots",
                "module_likes_table" => $spot_likes,
                "likes_join_column" => "spot_id",
                "module_comments_table" => $spot_comments,
                "comments_join_column" => "spot_id",
                "module_stats_table" => $spot_stats,
                "stats_join_column" => "spot_id"),

            array("module_items_table" => $speeds,
                "module_name" => "speed",
                "module_likes_table" => $speed_likes,
                "likes_join_column" => "speed_id",
                "module_comments_table" => $speed_comments,
                "comments_join_column" => "speed_id",
                "module_stats_table" => $speed_stats,
                "stats_join_column" => "speed_id"),

            array("module_items_table" => $videos,
                "module_name" => "videos",
                "module_likes_table" => $video_likes,
                "likes_join_column" => "video_id",
                "module_comments_table" => $video_comments,
                "comments_join_column" => "video_id",
                "module_stats_table" => $video_stats,
                "stats_join_column" => "video_id"));

        $this->find_all_users_posts_views_count($sql_modules_source);
        $this->find_all_users_followers_count();
        $this->find_all_users_likes_count_at_user_posts($sql_modules_source);
        $this->find_all_users_comments_count_at_user_posts($sql_modules_source);
        $this->find_all_users_likes_count_by_user($sql_modules_source);
        $this->find_all_users_comments_count_by_user($sql_modules_source);

        $this->calculate_total_rating_per_every_user();
        usort($this->users_data, array("User_Rank_Mapper", "compare_ratings"));
        $this->update_ranks_in_users_table();
    }
}

?>