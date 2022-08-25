<?php
    class Spot_Like_Model extends Model
    {
        protected $table_name = "spots_likes";
        protected $db_fields  = array("id", "spot_id", "user_id", "posted_on");
        
        public $id;
        public $spot_id;
        public $user_id;
        public $posted_on;
        
        // Deletes all likes from specified item
        public function delete_likes_on($spot_id)
        {
            $sql = "WHERE spot_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($spot_id));
            
            $this->delete_by_condition($sql);
        }
        
        public function find_count_on($spot_id)
        {
            $sql = "WHERE spot_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($spot_id));
            
            return $this->count($sql);
        }
        
        public function is_spot_liked_by($spot_id,
                                                    $user_id)
        {
            $sql  = "WHERE spot_id = %d ";
            $sql .= " AND  user_id = %d ";
            $sql .= " LIMIT 1           ";
            
            $sql  = sprintf($sql,
                                 $this->database->escape_value($spot_id),
                                 $this->database->escape_value($user_id));
            
            return ($this->count($sql) == 1) ? true : false;
        }
        
        public function save()
        {
            // Updating post datetime
            $this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
            
            $spot_stats_model = new Spot_Stats_Model;
            $spot_stats       = $spot_stats_model->find_stats_on($this->spot_id);
            $spot_stats->increase_likes_count();
            $spot_stats->save();
            
            return parent::save();
        }
        
        public function delete()
        {
            $spot_stats_model = new Spot_Stats_Model;
            $spot_stats       = $spot_stats_model->find_stats_on($this->spot_id);
            $spot_stats->decrease_likes_count();
            $spot_stats->save();
            
            return parent::delete();
        }
        
        public function delete_all_by_user($user_id)
        {
            $sql = "WHERE user_id = %d";
            $sql = sprintf($sql,
                                $this->database->escape_value($user_id));
            
            $user_likes       = $this->find_all($sql);
            $spot_stats_model = new Spot_Stats_Model;
            
            foreach($user_likes as $user_like)
            {
                $spot_stats = $spot_stats_model->find_stats_on($user_like->spot_id);
                $spot_stats->decrease_likes_count();
                $spot_stats->save();
            }
            
            return $this->delete_by_condition($sql);
        }
        
        public function get_validation_rules()
        {
            // Validation Rules 
        }
    }
?>