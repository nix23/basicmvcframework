<?php

class User_Model extends Model
{
    protected $table_name = "users";
    protected $db_fields = array("id", "username", "password", "rank", "subname",
        "avatar_master_name", "description", "email",
        "activated", "registred_on", "blocked", "hash", "type", "facebook_id");
    protected $nested_db_fields = array("drives_count", "favorites_count", "followers_count");
    protected $special_fields = array("license", "password_confirmation");

    public $id;
    public $username;
    public $password;
    public $rank;
    public $subname;
    public $avatar_master_name;
    public $description;
    public $email;
    public $activated;
    public $registred_on;
    public $blocked;
    public $hash;
    public $type;
    public $facebook_id;

    // Nested attributes
    public $drives_count;
    public $favorites_count;
    public $followers_count;

    // Shared attributes
    public $avatar_clones = array(
        array("width" => 100, "height" => 100),
        array("width" => 95, "height" => 95),
        array("width" => 85, "height" => 85),
        array("width" => 75, "height" => 75),
        array("width" => 70, "height" => 70),
        array("width" => 60, "height" => 60)
    );
    public $directory;
    public $frame_action;
    public $user_posts_views_count = 0;
    public $user_followers_count = 0;
    public $likes_count_at_user_posts = 0;
    public $comments_count_at_user_posts = 0;
    public $module_items = array();
    public $username_prefixes = array();

    // Form specific attributes
    public $license;
    public $password_confirmation;

    const ACCOUNT_TYPE_STANDARD = "standard";
    const ACCOUNT_TYPE_FACEBOOK = "facebook";

    public function __construct()
    {
        $this->username_prefixes[] = "all";
        $this->username_prefixes[] = "other";

        for ($char = "a"; $char <= "z"; $char++) {
            $this->username_prefixes[] = $char;
            if ($char == "z") break;
        }

        parent::__construct();
    }

    public function find_unactivated_users_count_more_than_in_n_days($days)
    {
        $sql = "WHERE activated = 'no'                                   ";
        $sql .= "  AND registred_on < DATE_SUB(NOW(), INTERVAL $days day) ";

        return $this->count($sql);
    }

    public function delete_unactivated_users_more_than_in_n_days($days)
    {
        $sql = "WHERE activated = 'no'                                   ";
        $sql .= "  AND registred_on < DATE_SUB(NOW(), INTERVAL $days day) ";

        return $this->delete_by_condition($sql);
    }

    public function find_n_top_active_users($count = 5)
    {
        $sql = "WHERE blocked = 'no'  ";
        $sql .= "AND activated = 'yes' ";
        $sql .= "ORDER BY rank ASC     ";
        $sql .= "LIMIT %d              ";

        $sql = sprintf($sql,
            $this->database->escape_value($count));
        return $this->find_all($sql);
    }

    public static function compare_module_items_post_dates($first_module_item_object,
                                                           $second_module_item_object)
    {
        $first_postdate_in_seconds = Datetime_Converter::get_datetime_in_seconds($first_module_item_object->posted_on);
        $second_postdate_in_seconds = Datetime_Converter::get_datetime_in_seconds($second_module_item_object->posted_on);

        if ($first_postdate_in_seconds == $second_postdate_in_seconds)
            return 0;
        else if ($first_postdate_in_seconds < $second_postdate_in_seconds)
            return 1;
        else
            return -1;
    }

    public function find_n_last_approved_module_items($count = 4)
    {
        $photoset_model = new Photo_Model;
        $spot_model = new Spot_Model;
        $speed_model = new Speed_Model;
        $video_model = new Video_Model;

        $photosets = $photoset_model->find_n_last_approved_photosets(4, true, false, $this->id);
        $spots = $spot_model->find_n_last_approved_spots(4, true, false, $this->id);
        $speeds = $speed_model->find_n_last_approved_speeds(4, true, false, false, false, $this->id);
        $videos = $video_model->find_n_last_approved_videos(4, true, false, false, false, $this->id);

        $module_items = array();

        foreach ($photosets as $photoset)
            $module_items[] = $photoset;

        foreach ($spots as $spot)
            $module_items[] = $spot;

        foreach ($speeds as $speed)
            $module_items[] = $speed;

        foreach ($videos as $video)
            $module_items[] = $video;

        usort($module_items, array("User_Model", "compare_module_items_post_dates"));

        $items_count = 0;
        foreach ($module_items as $module_item) {
            $module_item->find_category_and_subcategory();
            $this->module_items[] = $module_item;

            $items_count++;
            if ($items_count == $count)
                break;
        }
    }

    private function find_posts_views_count($sql_modules_source)
    {
        $item_view_model = new Item_View_Model;
        $item_views = $item_view_model->get_table_name();

        // *** Fetching item views from 'item_views' table
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_name = $sql_module_source["module_name"];

            $sql = "SELECT COUNT(*) as count                         ";
            $sql .= "  FROM $item_views                               ";
            $sql .= " INNER JOIN $module_items_table                  ";
            $sql .= " ON $module_items_table.id = $item_views.item_id ";
            $sql .= " WHERE $item_views.module = '$module_name'       ";
            $sql .= "   AND $module_items_table.moderated = 'yes'     ";
            $sql .= "   AND $module_items_table.status    = 'enabled' ";
            $sql .= "   AND $module_items_table.user_id   = %d        ";
            $sql .= " GROUP BY $module_items_table.user_id            ";

            $sql = sprintf($sql,
                $this->database->escape_value($this->id));

            $result_row = $this->database->query($sql);
            $user_posts_views_count = $this->database->fetch_array($result_row);
            $this->user_posts_views_count += $user_posts_views_count["count"];
        }

        // *** Fetching item views from module stats table
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_stats_table = $sql_module_source["module_stats_table"];
            $stats_join_column = $sql_module_source["stats_join_column"];

            $sql = "SELECT SUM($module_stats_table.views_count) AS views_count          ";
            $sql .= "  FROM $module_items_table                                          ";
            $sql .= " INNER JOIN $module_stats_table                                     ";
            $sql .= " ON $module_items_table.id = $module_stats_table.$stats_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                        ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                    ";
            $sql .= "   AND $module_items_table.user_id   = %d                           ";
            $sql .= " GROUP BY $module_items_table.user_id                               ";

            $sql = sprintf($sql,
                $this->database->escape_value($this->id));

            $result_row = $this->database->query($sql);
            $views_count = $this->database->fetch_array($result_row);

            $this->user_posts_views_count += $views_count["views_count"];
        }
    }

    private function find_followers_count()
    {
        $follower_model = new Follower_Model;
        $this->user_followers_count = $follower_model->find_followers_count_on($this->id);
    }

    private function find_likes_count_at_user_posts($sql_modules_source)
    {
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_likes_table = $sql_module_source["module_likes_table"];
            $likes_join_column = $sql_module_source["likes_join_column"];

            $sql = "SELECT COUNT(*) as count                                            ";
            $sql .= "  FROM $module_items_table                                          ";
            $sql .= " INNER JOIN $module_likes_table                                     ";
            $sql .= " ON $module_items_table.id = $module_likes_table.$likes_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                        ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                    ";
            $sql .= "   AND $module_items_table.user_id   = %d                           ";
            $sql .= " GROUP BY $module_items_table.user_id                               ";

            $sql = sprintf($sql,
                $this->database->escape_value($this->id));

            $result_row = $this->database->query($sql);
            $likes_count_at_user_posts = $this->database->fetch_array($result_row);
            $this->likes_count_at_user_posts += $likes_count_at_user_posts["count"];
        }
    }

    private function find_comments_count_at_user_posts($sql_modules_source)
    {
        foreach ($sql_modules_source as $sql_module_source) {
            $module_items_table = $sql_module_source["module_items_table"];
            $module_comments_table = $sql_module_source["module_comments_table"];
            $comments_join_column = $sql_module_source["comments_join_column"];

            $sql = "SELECT COUNT(*) as count                                                  ";
            $sql .= "  FROM $module_items_table                                                ";
            $sql .= " INNER JOIN $module_comments_table                                        ";
            $sql .= " ON $module_items_table.id = $module_comments_table.$comments_join_column ";
            $sql .= " WHERE $module_items_table.moderated = 'yes'                              ";
            $sql .= "   AND $module_items_table.status    = 'enabled'                          ";
            $sql .= "   AND $module_items_table.user_id   = %d                                 ";
            $sql .= " GROUP BY $module_items_table.user_id                                     ";

            $sql = sprintf($sql,
                $this->database->escape_value($this->id));

            $result_row = $this->database->query($sql);
            $comments_count_at_user_posts = $this->database->fetch_array($result_row);
            $this->comments_count_at_user_posts += $comments_count_at_user_posts["count"];
        }
    }

    public function find_statistics_data()
    {
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

        $this->find_posts_views_count($sql_modules_source);
        $this->find_followers_count();
        $this->find_likes_count_at_user_posts($sql_modules_source);
        $this->find_comments_count_at_user_posts($sql_modules_source);
    }

    public function find_all_users($page = 1,
                                   $sort = "username",
                                   $direction = "ASC",
                                   $username_prefix = "all",
                                   $validate_page = true)
    {
        if (!in_array($username_prefix, $this->username_prefixes))
            $username_prefix = "all";

        // Conditions for pagination and items select queries
        if ($username_prefix != "all") {
            if ($username_prefix == "other")
                $where_sql = "WHERE username NOT RLIKE '^[A-Z]' ";
            else
                $where_sql = "WHERE username RLIKE '^[$username_prefix]' ";
        } else {
            $where_sql = "";
        }

        // Making pagination
        $this->pagination = new Pagination($page, $this->count($where_sql));
        if ($validate_page) $this->pagination->validate_page_range();

        // Fetching users
        $photoset_model = new Photo_Model;
        $spot_model = new Spot_Model;
        $speed_model = new Speed_Model;
        $video_model = new Video_Model;
        $favorite_model = new Favorite_Model;
        $follower_model = new Follower_Model;

        $users_table = $this->get_table_name();
        $photosets_table = $photoset_model->get_table_name();
        $spots_table = $spot_model->get_table_name();
        $speeds_table = $speed_model->get_table_name();
        $videos_table = $video_model->get_table_name();
        $favorites_table = $favorite_model->get_table_name();
        $followers_table = $follower_model->get_table_name();

        $photosets_sql = "SELECT COUNT(*) FROM $photosets_table           ";
        $photosets_sql .= "WHERE $photosets_table.user_id = $users_table.id";

        $spots_sql = "SELECT COUNT(*) FROM $spots_table           ";
        $spots_sql .= "WHERE $spots_table.user_id = $users_table.id";

        $speeds_sql = "SELECT COUNT(*) FROM $speeds_table           ";
        $speeds_sql .= "WHERE $speeds_table.user_id = $users_table.id";

        $videos_sql = "SELECT COUNT(*) FROM $videos_table           ";
        $videos_sql .= "WHERE $videos_table.user_id = $users_table.id";

        $favorites_sql = "SELECT COUNT(*) FROM $favorites_table           ";
        $favorites_sql .= "WHERE $favorites_table.user_id = $users_table.id";

        $followers_sql = "SELECT COUNT(*) FROM $followers_table               ";
        $followers_sql .= "WHERE $followers_table.followed_id = $users_table.id";

        $sql = "SELECT users.*,                                ";
        $sql .= "       ($favorites_sql) as favorites_count,    ";
        $sql .= "       ($followers_sql) as followers_count,    ";
        $sql .= "       SUM(($photosets_sql) +                  ";
        $sql .= "           ($spots_sql) +                      ";
        $sql .= "           ($speeds_sql) +                     ";
        $sql .= "           ($videos_sql)                       ";
        $sql .= "          ) as drives_count                    ";
        $sql .= "FROM (                                         ";
        $sql .= "   SELECT * FROM $users_table                  ";
        $sql .= "   $where_sql                                  ";
        $sql .= "   ORDER BY $sort $direction                   ";
        $sql .= "   LIMIT {$this->pagination->records_per_page} ";
        $sql .= "   OFFSET {$this->pagination->offset}          ";
        $sql .= ") AS users GROUP BY users.id                   ";

        return $this->find_by_sql($sql);
    }

    public function has_avatar()
    {
        return !empty($this->avatar_master_name) ? true : false;
    }

    public function has_subname()
    {
        return !empty($this->subname) ? true : false;
    }

    public function has_description()
    {
        return !empty($this->description) ? true : false;
    }

    public function is_current_logged_user()
    {
        $user_session = Registry::get('session');

        if ($user_session->is_logged_in()) {
            if ($user_session->user_id == $this->id)
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    public function authenticate()
    {
        $sql = "WHERE username = '%s' ";
        $sql .= "AND password = '%s'   ";
        $sql .= "AND activated = 'yes' ";
        $sql .= "AND type = '" . self::ACCOUNT_TYPE_STANDARD . "' ";

        $sql = sprintf($sql,
            $this->database->escape_value($this->username),
            sha1($this->database->escape_value($this->password)));

        return $this->find_by_condition($sql, "id");
    }

    public function findByFacebookUserId($facebookUserId)
    {
        $sql = "WHERE type = '" . self::ACCOUNT_TYPE_FACEBOOK . "' ";
        $sql .= "AND facebook_id = '%s' AND facebook_id IS NOT NULL";

        $sql = sprintf($sql,
            $this->database->escape_value($facebookUserId));
        return $this->find_by_condition($sql);
    }

    public function is_username_unique()
    {
        return $this->is_unique("username",
            $this->username);
    }

    public function is_email_unique()
    {
        return $this->is_unique("email",
            $this->email,
            "AND activated = 'yes'");
    }

    public function save($activation = false, $facebookActivation = false)
    {
        // Facebook activation
        if ($facebookActivation) {
            return parent::save();
        } // Create
        else if (empty($this->id)) {
            $this->password = sha1($this->password);
            $this->registred_on = strftime("%Y-%m-%d %H:%M:%S", time());
            $this->hash = sha1(uniqid() . mt_rand(0, 99999) . $this->username);
            $this->activated = "no";
            $this->blocked = "no";

            return parent::save();
        } // Activate
        else if ($activation) {
            return parent::update_only(array("rank",
                "activated",
                "hash"));
        } // Profile update
        else {
            return parent::update_only(array("subname",
                "avatar_master_name",
                "description"));
        }
    }

    public function send_activation_email($email_html)
    {
        $mailer = new Mailer;
        $mailer->set_subject("Account activation.");
        $mailer->set_body_html($email_html);
        $mailer->add_target($this->email);

        return $mailer->send();
    }

    public function find_account_by_hash($hash = "")
    {
        $sql = "WHERE hash = '%s'     ";
        $sql .= "  AND activated = 'no'";

        $sql = sprintf($sql,
            $this->database->escape_value($hash));

        return $this->find_by_condition($sql);
    }

    // Unpacking action,which will execute on this photo
    public function unpack_frame($frame)
    {
        $frame_actions = array("ajax", "deleteajax", "delete");
        $frame_parts = explode("-", $frame);

        if (in_array($frame_parts[0], $frame_actions)) {
            $this->frame_action = $frame_parts[0];
            array_shift($frame_parts);
            $this->avatar_master_name = implode("-", $frame_parts);
        } else {
            $this->frame_action = "none";
        }
    }

    // Unpacks directory from master_name
    public function unpack_directory()
    {
        $master_name_parts = explode("-", $this->avatar_master_name);
        $this->directory = $master_name_parts[0];
    }

    public function move_clones()
    {
        $this->unpack_directory();

        // Moving clones
        foreach ($this->avatar_clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $ajax_path = UPLOADS_AJAX . $this->avatar_master_name . $sizes . ".jpg";
            $images_path = UPLOADS_IMAGES . $this->directory . DS;
            $images_path .= $this->avatar_master_name . $sizes . ".jpg";

            if (copy($ajax_path, $images_path)) {
                unlink($ajax_path);
            }
        }
    }

    public function delete_ajax()
    {
        // Deleting clones
        foreach ($this->avatar_clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $ajax_path = UPLOADS_AJAX . $this->avatar_master_name . $sizes . ".jpg";

            if (file_exists($ajax_path)) {
                unlink($ajax_path);
            }
        }
    }

    public function delete_clones()
    {
        $this->unpack_directory();

        // Deleting clones
        foreach ($this->avatar_clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $images_path = UPLOADS_IMAGES . $this->directory . DS;
            $images_path .= $this->avatar_master_name . $sizes . ".jpg";

            if (file_exists($images_path)) {
                unlink($images_path);
            }
        }
    }

    // Deletes user,and all his actions on site
    public function delete_account()
    {
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
        $favorite_model = new Favorite_Model;
        $follower_model = new Follower_Model;

        $user_photosets = $photoset_model->find_all_photosets_by_user($this->id);
        $user_spots = $spot_model->find_all_spots_by_user($this->id);
        $user_speeds = $speed_model->find_all_speeds_by_user($this->id);
        $user_videos = $video_model->find_all_videos_by_user($this->id);

        $favorite_model->delete_all_user_favorites($this->id);

        // Deleting all favorited user items by other users
        // $items_and_modules = array(array("items" => array(), "module" => "name"))
        $items_and_modules = array();

        $items_and_modules[] = array("items" => $user_photosets,
            "module" => "photos");

        $items_and_modules[] = array("items" => $user_spots,
            "module" => "spots");

        $items_and_modules[] = array("items" => $user_speeds,
            "module" => "speed");

        $items_and_modules[] = array("items" => $user_videos,
            "module" => "videos");

        foreach ($items_and_modules as $items_and_module) {
            $ids_batch = array();

            foreach ($items_and_module["items"] as $module_item)
                $ids_batch[] = $module_item->id;

            if (!empty($ids_batch)) {
                $favorite_model->delete_batch_in_module($ids_batch,
                    $items_and_module["module"]);
            }
        }

        $follower_model->delete_all_followed_users_by_user($this->id);
        $follower_model->delete_all_user_followers($this->id);

        foreach ($user_photosets as $photoset)
            $photoset->delete();

        foreach ($user_spots as $spot)
            $spot->delete();

        foreach ($user_speeds as $speed)
            $speed->delete();

        foreach ($user_videos as $video)
            $video->delete();

        $photoset_comment_model->delete_all_by_user($this->id);
        $photoset_like_model->delete_all_by_user($this->id);
        $spot_comment_model->delete_all_by_user($this->id);
        $spot_like_model->delete_all_by_user($this->id);
        $speed_comment_model->delete_all_by_user($this->id);
        $speed_like_model->delete_all_by_user($this->id);
        $video_comment_model->delete_all_by_user($this->id);
        $video_like_model->delete_all_by_user($this->id);

        $this->delete_clones();
        $this->delete();

        return true;
    }

    public function change_blocked_status()
    {
        $this->blocked = ($this->blocked == "yes") ? "no" : "yes";
        return $this->update_only(array("blocked"));
    }

    public function is_account_blocked()
    {
        return ($this->blocked == "yes") ? true : false;
    }

    public function is_account_activated()
    {
        return ($this->activated == "yes") ? true : false;
    }

    public function get_registred_users_count($only_activated = true,
                                              $days = 1)
    {
        $sql = "WHERE registred_on > DATE_SUB(NOW(), INTERVAL %d day) ";

        if ($only_activated)
            $sql .= "AND activated = 'yes'";

        $sql = sprintf($sql,
            $days);

        return $this->count($sql);
    }

    public function get_registration_fill_rules()
    {
        $rules = array();

        $rules['username'] = array(array("username_required",
            "Please enter login.",
            "required"));

        $rules['password'] = array(array("password_required",
            "Please enter password.",
            "required"));

        $rules['email'] = array(array("email_required",
            "Please enter e-mail.",
            "required"));

        $rules['password_confirmation'] = array(array("password_confirmation_required",
            "Please repeat password.",
            "required"));

        $rules['license'] = array(array("license_required",
            "For registration you must accept our site license.",
            "equals",
            "confirmed"));

        return $rules;
    }

    public function get_registration_syntax_rules()
    {
        $rules = array();

        $rules['username'] = array(array("username_min_length",
            "Min length of login: 3 chars.",
            "min_length",
            3),
            array("username_max_length",
                "Max length of login: 25 chars.",
                "max_length",
                25),
            array("username_format",
                "Login can consist only from '0-9A-Za-z_' chars.",
                "only_letters_digits_and_underscore"));

        $rules['password'] = array(array("password_min_length",
            "Min length of password: 6 chars.",
            "min_length",
            6),
            array("password_equalness",
                "Passwords don't match.",
                "equals",
                "$this->password_confirmation"));

        $rules['email'] = array(array("email_format",
            "Email format should be: 'name@mail.domain'.",
            "regex",
            ".+@.+\..+"));

        return $rules;
    }

    public function get_authorization_rules()
    {
        $rules = array();

        $rules["username"] = array(array("username_required",
            "Please enter login.",
            "required"));

        $rules["password"] = array(array("password_required",
            "Please enter password.",
            "required"));

        return $rules;
    }

    public function get_profile_update_rules()
    {
        $rules = array();

        $rules["subname"] = array(array("subname_maxlength",
            "Max length of subname: 40 chars.",
            "max_length",
            40));

        $rules["description"] = array(array("description_maxlength",
            "Max lentgth of description: 3000 chars.",
            "max_length",
            3000));

        return $rules;
    }
}

?>