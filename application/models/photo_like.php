<?php
    class Photo_Like_Model extends Model
    {
        protected $table_name = "photos_likes";
        protected $db_fields  = array("id", "photo_id", "user_id", "posted_on");
        
        public $id;
        public $photo_id;
        public $user_id;
        public $posted_on;
        
        // Deletes all likes from specified item
        public function delete_likes_on($photoset_id)
        {
            $sql = "WHERE photo_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($photoset_id));
            
            $this->delete_by_condition($sql);
        }
        
        public function find_count_on($photoset_id)
        {
            $sql = "WHERE photo_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($photoset_id));
            
            return $this->count($sql);
        }
        
        public function is_photoset_liked_by($photoset_id,
                                                         $user_id)
        {
            $sql  = "WHERE photo_id = %d ";
            $sql .= " AND  user_id = %d  ";
            $sql .= " LIMIT 1            ";
            
            $sql  = sprintf($sql,
                                 $this->database->escape_value($photoset_id),
                                 $this->database->escape_value($user_id));
            
            return ($this->count($sql) == 1) ? true : false;
        }
        
        public function save()
        {
            // Updating post datetime
            $this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
            
            $photoset_stats_model = new Photo_Stats_Model;
            $photoset_stats       = $photoset_stats_model->find_stats_on($this->photo_id);
            $photoset_stats->increase_likes_count();
            $photoset_stats->save();
            
            return parent::save();
        }
        
        public function delete()
        {
            $photoset_stats_model = new Photo_Stats_Model;
            $photoset_stats       = $photoset_stats_model->find_stats_on($this->photo_id);
            $photoset_stats->decrease_likes_count();
            $photoset_stats->save();
            
            return parent::delete();
        }
        
        public function delete_all_by_user($user_id)
        {
            $sql = "WHERE user_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($user_id));
            
            $user_likes           = $this->find_all($sql);
            $photoset_stats_model = new Photo_Stats_Model;
            
            foreach($user_likes as $user_like)
            {
                $photoset_stats = $photoset_stats_model->find_stats_on($user_like->photo_id);
                $photoset_stats->decrease_likes_count();
                $photoset_stats->save();
            }
            
            return $this->delete_by_condition($sql);
        }
        
        public function get_validation_rules()
        {
            // Validation Rules 
        }
    }
?>