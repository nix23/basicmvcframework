<?php
	class Video_Like_Model extends Model
	{
		protected $table_name = "videos_likes";
		protected $db_fields  = array("id", "video_id", "user_id", "posted_on");
		
		public $id;
		public $video_id;
		public $user_id;
		public $posted_on;
		
		// Deletes all likes from specified item
		public function delete_likes_on($video_id)
		{
			$sql = "WHERE video_id = %d";
			$sql = sprintf($sql,
								$this->database->escape_value($video_id));
			
			$this->delete_by_condition($sql);
		}
		
		public function find_count_on($video_id)
		{
			$sql = "WHERE video_id = %d";
			$sql = sprintf($sql,
								$this->database->escape_value($video_id));
			
			return $this->count($sql);
		}
		
		public function is_video_liked_by($video_id,
													 $user_id)
		{
			$sql  = "WHERE video_id = %d ";
			$sql .= " AND  user_id = %d  ";
			$sql .= " LIMIT 1            ";
			
			$sql  = sprintf($sql,
								 $this->database->escape_value($video_id),
								 $this->database->escape_value($user_id));
			
			return ($this->count($sql) == 1) ? true : false;
		}
		
		public function save()
		{
			// Updating post datetime
			$this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
			
			$video_stats_model = new Video_Stats_Model;
			$video_stats       = $video_stats_model->find_stats_on($this->video_id);
			$video_stats->increase_likes_count();
			$video_stats->save();
			
			return parent::save();
		}
		
		public function delete()
		{
			$video_stats_model = new Video_Stats_Model;
			$video_stats       = $video_stats_model->find_stats_on($this->video_id);
			$video_stats->decrease_likes_count();
			$video_stats->save();
			
			return parent::delete();
		}
		
		public function delete_all_by_user($user_id)
		{
			$sql = "WHERE user_id = %d";
			$sql = sprintf($sql,
								$this->database->escape_value($user_id));
			
			$user_likes        = $this->find_all($sql);
			$video_stats_model = new Video_Stats_Model;
			
			foreach($user_likes as $user_like)
			{
				$video_stats = $video_stats_model->find_stats_on($user_like->video_id);
				$video_stats->decrease_likes_count();
				$video_stats->save();
			}
			
			return $this->delete_by_condition($sql);
		}
		
		public function get_validation_rules()
		{
			// Validation Rules 
		}
	}
?>