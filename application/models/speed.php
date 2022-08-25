<?php
    class Speed_Model extends Model
    {
        protected $table_name = "speeds";
        protected $db_fields  = array("id", "category_id", "user_id", "heading", "short_description", 
                                                "article", "posted_on", "status", "moderated", "moderation_fail_text",
                                                "author", "source");
        protected $nested_db_fields = array("likes_count", "comments_count", "favorites_count",
                                                        "views_count", "activity", "main_photo_master_name");
        
        public $id;
        public $category_id;
        public $user_id;
        public $heading;
        public $short_description;
        public $article;
        public $posted_on;
        public $status;
        public $moderated;
        public $moderation_fail_text;
        public $author;
        public $source;
        
        // Nested attributes
        public $likes_count;
        public $comments_count;
        public $favorites_count;
        public $views_count;
        public $activity;
        public $main_photo_master_name;
        
        // Shared attributes
        public $module                            = "speed";
        public $category                          = false;
        public $subcategory                       = false;
        public $category_name                     = false;
        public $subcategory_name                  = false;
        public $photos                            = false;
        public $photos_count                      = 0;
        public $main_photo                        = false;
        public $comments                          = false;
        public $comments_model                    = false;
        public $comments_total_count              = false;
        public $user                              = false;
        public $is_liked_by_logged_user           = false;
        public $is_author_followed_by_logged_user = false;
        public $is_favorite_of_logged_user        = false;
        public $is_logged_user_post_author        = false;
        public $author_followers_count            = 0;
        private $stats                            = false;
        
       private function find_speed_stats()
       {
        if($this->stats)
            return;
        
        $speed_stats_model     = new Speed_Stats_Model;
        $this->stats           = $speed_stats_model->find_stats_on($this->id);
        $this->likes_count     = $this->stats->likes_count;
        $this->comments_count  = $this->stats->comments_count;
        $this->views_count     = $this->stats->views_count;
        $this->favorites_count = $this->stats->favorites_count;
        $this->activity        = $this->stats->activity;
       }
        
        public function find_all_speeds($in_categories     = array(),
                                                  $page              = 1,
                                                  $sort              = "moderated",
                                                  $direction         = "ASC",
                                                  $validate_page     = true,
                                                  $only_enabled      = false,
                                                  $only_moderated    = false,
                                                  $only_from_user_id = false,
                                                  $limit             = false)
        {
            foreach($in_categories as &$in_category)
                $in_category = (int) $in_category;
            $in_categories = implode(", ", $in_categories); 
            
            // Conditions for pagination and items select queries
            if($in_categories or $only_enabled or $only_moderated or $only_from_user_id)
            {
                $where_sql   = "WHERE ";
                $where_parts = array();
                
                $where_parts[] = ($in_categories)     ? " category_id IN ($in_categories) "           : "";
                $where_parts[] = ($only_enabled)      ? " status = 'enabled' "                        : "";
                $where_parts[] = ($only_moderated)    ? " moderated = 'yes' "                         : "";
                $where_parts[] = ($only_from_user_id) ? sprintf(" user_id = %d ", $only_from_user_id) : "";
                
                $where_sql .= implode(" AND ", array_filter($where_parts));
            }
            else
            {
                $where_sql = "";
            }
            
            // Making pagination
            $this->pagination = new Pagination($page, $this->count($where_sql));
            if($validate_page) $this->pagination->validate_page_range(); 

            if(!$limit)
                $limit = $this->pagination->records_per_page;
            
            // Fetching speeds
            $speed_stats_model = new Speed_Stats_Model;
            
            $speeds       = $this->get_table_name();
            $speeds_stats = $speed_stats_model->get_table_name();
            
            $sql  = "SELECT $speeds.*,                                   ";
            $sql .= "       $speeds_stats.likes_count,                   ";
            $sql .= "       $speeds_stats.comments_count,                ";
            $sql .= "       $speeds_stats.views_count,                   ";
            $sql .= "       $speeds_stats.favorites_count,               ";
            $sql .= "       $speeds_stats.activity                       ";
            
            if(in_array($sort, array("moderated", "posted_on")))
            {
                $sql .= " FROM (                                        ";
                $sql .= "   SELECT * FROM $speeds                       ";
                $sql .= "   $where_sql                                  ";
                
                if($sort == "moderated")
                    $sql .= "   ORDER BY $sort $direction, posted_on DESC   ";
                else
                    $sql .= "   ORDER BY $sort $direction                   ";
                
                $sql .= "   LIMIT {$limit}                              ";
                $sql .= "   OFFSET {$this->pagination->offset}          ";
                $sql .= " ) AS $speeds                                  ";
                $sql .= " INNER JOIN $speeds_stats                      ";
                $sql .= " ON $speeds_stats.speed_id = $speeds.id        ";

                if($sort == "moderated")
                    $sql .= "   ORDER BY $sort $direction, posted_on DESC   ";
                else
                    $sql .= "   ORDER BY $sort $direction                   ";
            }
            else if(in_array($sort, array("views_count", "activity")))
            {
                $sql .= "FROM $speeds                                        ";
                $sql .= "INNER JOIN $speeds_stats                            ";
                $sql .= "ON $speeds_stats.speed_id = $speeds.id              ";
                $sql .= "$where_sql                                          ";
                $sql .= "ORDER BY $sort $direction                           ";
                $sql .= "LIMIT  {$limit}                                     ";
                $sql .= "OFFSET {$this->pagination->offset}                  ";
            }
            else
            {
                exit("Error: Unknown sort passed to find_all_speeds.");
            }
            
            return $this->find_by_sql($sql);
        }

        // Fetching N last uploaded speeds,
        // which passed moderation and are enabled
        public function find_n_last_approved_speeds($count                            = 5,
                                                                  $fetch_main_photo_in_subquery     = false,
                                                                  $fetch_likes_count_in_subquery    = false,
                                                                  $fetch_comments_count_in_subquery = false,
                                                                  $fetch_views_count_in_subquery    = false,
                                                                  $only_from_user_id                = false)
        {
            $speed_photo_model       = new Speed_Photo_Model;
            $item_view_model         = new Item_View_Model;
            $speed_stats_model       = new Speed_Stats_Model;

            $speeds            = $this->get_table_name();
            $speed_photos      = $speed_photo_model->get_table_name();
            $item_views        = $item_view_model->get_table_name();
            $speed_stats       = $speed_stats_model->get_table_name();

            $main_photo_sql  = "SELECT $speed_photos.master_name           ";
            $main_photo_sql .= "  FROM $speed_photos                       ";
            $main_photo_sql .= " WHERE $speed_photos.speed_id = $speeds.id ";
            $main_photo_sql .= "   AND $speed_photos.main    = 'yes'       ";

            $item_views_sql  = "SELECT COUNT(*) FROM $item_views       ";
            $item_views_sql .= "WHERE $item_views.item_id = $speeds.id ";
            $item_views_sql .= "  AND $item_views.module  = 'speed'    ";

            $sql  = "SELECT $speeds.id         ";
            $sql .= "  FROM $speeds            ";
            $sql .= " WHERE status = 'enabled' ";
            $sql .= "   AND moderated = 'yes'  ";
            $sql .= ($only_from_user_id) ? sprintf(" AND user_id = %d ", $only_from_user_id) : "";
            $sql .= " ORDER BY posted_on DESC  ";
            $sql .= " LIMIT %d                 ";

            $sql = sprintf($sql,
                                $this->database->escape_value($count));

            $speed_ids_result_set = $this->find_by_sql($sql);
            if(count($speed_ids_result_set) < 1)
                return array();

            $speed_ids = array();
            foreach($speed_ids_result_set as $speed_id)
                $speed_ids[] = $speed_id->id;

            $speed_ids = implode(",", $speed_ids);

            $sql  = "SELECT $speeds.*          ";
            $sql .= ($fetch_main_photo_in_subquery)     ? ", ($main_photo_sql)                      AS main_photo_master_name " : "";
            $sql .= ($fetch_likes_count_in_subquery)    ? ", $speed_stats.likes_count                                         " : "";
            $sql .= ($fetch_comments_count_in_subquery) ? ", $speed_stats.comments_count                                      " : "";
            $sql .= ($fetch_views_count_in_subquery)    ? ", SUM(($item_views_sql) + $speed_stats.views_count) AS views_count " : "";
            $sql .= "  FROM $speeds            ";
            $sql .= " INNER JOIN $speed_stats ON $speed_stats.speed_id = $speeds.id ";
            $sql .= " WHERE $speeds.id IN ($speed_ids) ";
            $sql .= " GROUP BY $speeds.id              ";
            $sql .= " ORDER BY posted_on DESC          ";

            return $this->find_by_sql($sql);
        }

        public function find_all_speeds_attached_to_category($category_id)
        {
            $sql = "WHERE category_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($category_id));

            return $this->find_all($sql);
        }
        
        // Finds speeds count uploaded by user
        public function find_uploads_count_by_user($user_id)
        {
            $sql = "WHERE user_id = %d ";
            $sql = sprintf($sql,
                                $this->database->escape_value($user_id));
            
            return $this->count($sql);
        }
        
        // Finds category and subcategory,to which speed belongs
        public function find_category_and_subcategory()
        {
            $category_model = new Category_Model;
            $this->category = $category_model->find_by_id($this->category_id); 
            
            if($this->category->parent_id != '0')
            {
                $this->subcategory = $this->category; 
                $this->category    = $category_model->find_by_id($this->subcategory->parent_id); 
            }
            
            if($this->category)    $this->category_name    = $this->category->name;
            if($this->subcategory) $this->subcategory_name = $this->subcategory->name;
        }
        
        // Fetching main speed photo
        public function find_main_photo()
        {
            $speed_photo_model = new Speed_Photo_Model;
            $this->main_photo  = $speed_photo_model->find_main_photo_on($this->id); 
        }
        
        public function find_attached_photos($fetch_main = true)
        {
            $speed_photo_model  = new Speed_Photo_Model;
            $this->photos       = $speed_photo_model->find_photos_on($this->id,
                                                                                        $fetch_main);
            $this->photos_count = count($this->photos);
        }
        
        private function delete_attached_photos()
        {
            $this->find_attached_photos();
            
            foreach($this->photos as $photo)
            {
                $photo->delete_with_clones();
            }
        }
        
        private function delete_attached_likes()
        {
            $speed_like_model = new Speed_Like_Model;
            $speed_like_model->delete_likes_on($this->id);
        }
        
        private function delete_attached_comments()
        {
            $speed_comments_model = new Speed_Comment_Model;
            $speed_comments_model->delete_comments_on($this->id);
        }
        
        private function delete_attached_views()
        {
            $item_view_model = new Item_View_Model;
            $item_view_model->delete_views_on($this->id,
                                                         "speed");
        }

        public function delete_attached_favorites()
        {
            $favorite_model = new Favorite_Model;
            $favorite_model->delete_item_from_all_user_favorites($this->id,
                                                                                  "speed");
        }
        
        private function delete_attached_stats()
        {
            $speed_stats_model = new Speed_Stats_Model;
            $speed_stats       = $speed_stats_model->find_stats_on($this->id);
            $speed_stats->delete();
        }

        public function delete()
        {
            $this->delete_attached_photos();
            $this->delete_attached_likes();
            $this->delete_attached_comments();
            $this->delete_attached_views();
            $this->delete_attached_favorites();
            $this->delete_attached_stats();
            return parent::delete();
        }
        
        public function find_attached_comments_on($page          = 1,
                                                                $validate_page = true)
        {
            $this->comments_model = new Speed_Comment_Model;
            $this->comments       = $this->comments_model->find_comments_on($this->id,
                                                                                                 $page,
                                                                                                 $validate_page);
        }
        
        public function find_comments_count()
        {
            $comments_model             = new Speed_Comment_Model;
            $this->comments_total_count = $comments_model->count_on($this->id);
        }
        
        public function find_author()
        {
            $user_model = new User_Model;
            $this->user = $user_model->find_by_id($this->user_id);
        }
        
        public function find_if_is_liked_by_logged_user($logged_user_id)
        {
            $speed_like_model = new Speed_Like_Model;
            
            if($speed_like_model->is_speed_liked_by($this->id, 
                                                                 $logged_user_id))
                $this->is_liked_by_logged_user = true;
            else
                $this->is_liked_by_logged_user = false;
        }
        
        public function find_if_author_is_followed_by_logged_user($logged_user_id)
        {
            $follower_model = new Follower_Model;
            
            if($follower_model->is_user_followed_by($this->user->id, 
                                                                 $logged_user_id))
                $this->is_author_followed_by_logged_user = true;
            else
                $this->is_author_followed_by_logged_user = false;
        }
        
        public function find_if_is_favorite_of_logged_user($logged_user_id)
        {
            $favorite_model = new Favorite_Model;
            
            if($favorite_model->is_module_item_favorite_of($this->id,
                                                                          "speed",
                                                                          $logged_user_id))
                $this->is_favorite_of_logged_user = true;
            else
                $this->is_favorite_of_logged_user = false;
        }
        
        public function find_if_is_logged_user_post_author($logged_user_id)
        {
            if($logged_user_id == $this->user_id)
                $this->is_logged_user_post_author = true;
            else
                $this->is_logged_user_post_author = false;
        }
        
        public function find_likes_count()
        {
            $speed_like_model  = new Speed_Like_Model;
            $this->likes_count = $speed_like_model->find_count_on($this->id);
        }
        
        public function find_favorites_count()
        {
            $favorite_model        = new Favorite_Model;
            $this->favorites_count = $favorite_model->find_count_on($this->id,
                                                                                      "speed");
        }
        
        public function find_author_followers_count()
        {
            $follower_model               = new Follower_Model;
            $this->author_followers_count = $follower_model->find_followers_count_on($this->user_id);
        }

        public function update_views_count($viewer_ip)
        {
            $item_view_model = new Item_View_Model;

            if(!$item_view_model->was_item_viewed_from_ip($this->id,
                                                                         $viewer_ip,
                                                                         "speed"))
            {
                $item_view_model->item_id = $this->id;
                $item_view_model->ip      = $viewer_ip;
                $item_view_model->module  = "speed";

                $item_view_model->save();
            }
        }

        public function find_views_count()
        {
            $item_view_model  = new Item_View_Model;
            $item_views_count = $item_view_model->find_count_by_item($this->id,
                                                                                       "speed");

            $this->find_speed_stats();
            $item_views_count += $this->views_count;

            $this->item_views_count = $item_views_count;
        }

        public function find_all_speeds_by_user($user_id)
        {
            $sql = "WHERE user_id = %d ";
            $sql = sprintf($sql,
                                $this->database->escape_value($user_id));
            
            return $this->find_all($sql);
        }

        public function save($force_moderation  = false,
                                    $update_exceptions = array())
        {
            // Updating post datetime
            $this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
            // Always force moderation from frontend
            if($force_moderation)
                $this->moderated = "no";
            
            if(empty($this->id))
            {
                parent::save($update_exceptions);
                
                $speed_stats_model           = new Speed_Stats_Model;
                $speed_stats_model->speed_id = $this->id;
                
                return $speed_stats_model->save();
            }
            else
            {
                return parent::save($update_exceptions);
            }
        }
        
        // Change status to opposite
        public function change_status()
        {
            if($this->status == "enabled")
            {
                $this->status = "disabled";
            }
            else
            {
                $this->status = "enabled";
            }
            
            return $this->update_only(array("status"));
        }
        
        // Change moderation status
        public function change_moderation()
        {
            if($this->moderated == "yes")
            {
                $this->moderated = "no";
            }
            else
            {
                $this->moderated            = "yes";
                $this->moderation_fail_text = "";
            }
            
            return $this->update_only(array("moderated", "moderation_fail_text"));
        }
        
        public function validate_article_tags( $uploaded_photos_count,
                                                            $newline_type = "javascript")
        {
            switch($newline_type)
            {
                case "javascript":
                    $line_break = "\n";
                break;
                
                case "html":
                    $line_break = "<br>&nbsp;&nbsp;&nbsp;";
                break;
            }
            
            $tags_parser = new Tags_Parser("article_tags",
                                                     $this->article,
                                                     $uploaded_photos_count);
            $tags_parser->validate();
            
            if($tags_parser->has_errors())
            {
                $error = "Article text contains following errors:$line_break";
                
                foreach($tags_parser->errors as $error_text)
                    $error .= "   $error_text{$line_break}";
                
                $error .= $line_break;
                
                $this->model_errors->set("article_tags_wrong_syntax", $error);
            }
        }
        
        public function get_full_heading()
        {
            return $this->heading;
        }

        public function is_enabled()
        {
            return ($this->status == "enabled") ? true : false;
        }

        public function is_moderated()
        {
            return ($this->moderated == "yes") ? true : false;
        }

        public function is_authorized_user_speed_author()
        {
            $user_session = Registry::get('session');

            if($user_session->is_logged_in())
            {
                if($user_session->user_id == $this->user_id)
                    return true;
                else
                    return false;
            }
            else
            {
                return false;
            }
        }

        public function is_moderation_failed()
        {
            if(!empty($this->moderation_fail_text))
                return true;
            else
                return false;
        }
        
        public function get_public_validation_rules()
        {
            $rules = array();
            
            $rules['category_id'] = array(array('category_name_required',
                                                            'Please select speed category.',
                                                            'required'));
            
            $rules['heading'] = array( array('heading_required',
                                                        'Please enter article heading.',
                                                        'required'),
                                                array('heading_minlength',
                                                        'Min length of heading: 20 chars.',
                                                        'min_length',
                                                        20));
            
            $rules['short_description'] = array(array('short_description_required',
                                                                    'Please enter short description.',
                                                                    'required'),
                                                            array('short_description_minlength',
                                                                    'Min length of short description: 20 chars.',
                                                                    'min_length',
                                                                    20));
            
            $rules['article'] = array(array(    'article_required',
                                                        'Please enter speed article.',
                                                        'required'),
                                              array(    'article_minlength',
                                                        'Min length of article: 300 chars.',
                                                        'min_length',
                                                        300));
            
            $rules['author'] = array(array('author_maxlength',
                                                     'Max length of author: 255 chars.',
                                                     'max_length',
                                                     255));
            
            $rules['source'] = array(array('source_maxlength',
                                                     'Max length of source: 255 chars.',
                                                     'max_length',
                                                     255));
            
            return $rules;
        }
        
        public function get_validation_rules()
        {
            $rules = array();
            
            $rules['user_id'] = array(array( 'user_name_required',
                                                        'Please select user to add.',
                                                        'required'));
            
            $rules['category_id'] = array(array('category_name_required',
                                                            'Please select category name.',
                                                            'required'));
            
            $rules['heading'] = array( array('heading_required',
                                                        'Please enter article name.',
                                                        'required'),
                                                array('heading_minlength',
                                                        'Min length of article name: 20 chars.',
                                                        'min_length',
                                                        20));
            
            $rules['short_description'] = array(array('short_description_required',
                                                                    'Please enter short description.',
                                                                    'required'),
                                                            array('short_description_minlength',
                                                                    'Min length of short description: 20 chars.',
                                                                    'min_length',
                                                                    20));
            
            $rules['article'] = array(array(    'article_required',
                                                        'Please enter speed article.',
                                                        'required'),
                                              array(    'article_minlength',
                                                        'Min length of article: 300 chars.',
                                                        'min_length',
                                                        300));
            
            $rules['author'] = array(array('author_maxlength',
                                                     'Max length of author: 255 chars.',
                                                     'max_length',
                                                     255));
            
            $rules['source'] = array(array('source_maxlength',
                                                     'Max length of source: 255 chars.',
                                                     'max_length',
                                                     255));
            
            return $rules;
        }
    }
?>