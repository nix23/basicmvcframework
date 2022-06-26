<?php
	class Speed_Like_Model extends Model
	{
		protected $table_name = "speeds_likes";
		protected $db_fields  = array("id", "speed_id", "user_id", "posted_on");
		
		public $id;
		public $speed_id;
		public $user_id;
		public $posted_on;
		
		// Deletes all likes from specified item
		public function delete_likes_on($speed_id)
		{
			$sql = "WHERE speed_id = %d";
			$sql = sprintf($sql,
								$this->database->escape_value($speed_id));
			
			$this->delete_by_condition($sql);
		}
		
		public function find_count_on($speed_id)
		{
			$sql = "WHERE speed_id = %d";
			$sql = sprintf($sql,
								$this->database->escape_value($speed_id));
			
			return $this->count($sql);
		}
		
		public function is_speed_liked_by($speed_id,
													 $user_id)
		{
			$sql  = "WHERE speed_id = %d ";
			$sql .= " AND  user_id  = %d ";
			$sql .= " LIMIT 1            ";
			
			$sql  = sprintf($sql,
								 $this->database->escape_value($speed_id),
								 $this->database->escape_value($user_id));
			
			return ($this->count($sql) == 1) ? true : false;
		}
		
		public function save()
		{
			// Updating post datetime
			$this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
			
			$speed_stats_model = new Speed_Stats_Model;
			$speed_stats       = $speed_stats_model->find_stats_on($this->speed_id);
			$speed_stats->increase_likes_count();
			$speed_stats->save();
			
			return parent::save();
		}
		
		public function delete()
		{
			$speed_stats_model = new Speed_Stats_Model;
			$speed_stats       = $speed_stats_model->find_stats_on($this->speed_id);
			$speed_stats->decrease_likes_count();
			$speed_stats->save();
			
			return parent::delete();
		}
		
		public function delete_all_by_user($user_id)
		{
			$sql = "WHERE user_id = %d";
			$sql = sprintf($sql,
								$this->database->escape_value($user_id));
			
			$user_likes        = $this->find_all($sql);
			$speed_stats_model = new Speed_Stats_Model;
			
			foreach($user_likes as $user_like)
			{
				$speed_stats = $speed_stats_model->find_stats_on($user_like->speed_id);
				$speed_stats->decrease_likes_count();
				$speed_stats->save();
			}
			
			return $this->delete_by_condition($sql);
		}
		
		public function get_validation_rules()
		{
			// Validation Rules 
		}
	}
?>