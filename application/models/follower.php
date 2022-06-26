<?php
	class Follower_Model extends Model
	{
		protected $table_name = "followers";
		protected $db_fields  = array("id", "followed_id", "follower_id", "posted_on");
		
		public $id;
		public $followed_id;
		public $follower_id;
		public $posted_on;
		
		// Shared attributes
		public $followed_user;
		
		public function find_followed_user_by_follower($followed_id,
														  			  $follower_id)
		{
			$sql  = "WHERE followed_id = %d ";
			$sql .= " AND  follower_id = %d ";
			
			$sql = sprintf($sql,
								$this->database->escape_value($followed_id),
								$this->database->escape_value($follower_id));
			
			return $this->find_by_condition($sql);
		}
		
		public function find_followed_users_by($follower_id)
		{
			$sql  = "WHERE follower_id = %d  ";
			$sql .= " ORDER BY posted_on DESC";
			$sql  = sprintf($sql,
								 $this->database->escape_value($follower_id));
			
			return $this->find_all($sql);
		}
		
		public function find_followers_count_on($followed_id)
		{
			$sql = "WHERE followed_id = %d ";
			$sql = sprintf($sql,
								$this->database->escape_value($followed_id));
			
			return $this->count($sql);
		}
		
		public function is_user_followed_by($followed_id,
														$follower_id)
		{
			$sql  = "WHERE followed_id = %d ";
			$sql .= " AND  follower_id = %d ";
			$sql .= " LIMIT 1               ";
			
			$sql  = sprintf($sql,
								 $this->database->escape_value($followed_id),
								 $this->database->escape_value($follower_id));
			
			return ($this->count($sql) == 1) ? true : false;
		}
		
		public function find_followed_user()
		{
			$user_model          = new User_Model;
			$this->followed_user = $user_model->find_by_id($this->followed_id); 
		}

		public function delete_all_followed_users_by_user($user_id)
		{
			$sql = "WHERE follower_id = %d ";
			$sql = sprintf($sql,
								$this->database->escape_value($user_id));

			return $this->delete_by_condition($sql);
		}

		public function delete_all_user_followers($user_id)
		{
			$sql = "WHERE followed_id = %d ";
			$sql = sprintf($sql,
								$this->database->escape_value($user_id));

			return $this->delete_by_condition($sql);
		}
		
		public function save()
		{
			// Updating post datetime
			$this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
			return parent::save();
		}
		
		public function get_validation_rules()
		{
			// Validation Rules 
		}
	}
?>