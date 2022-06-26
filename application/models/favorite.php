<?php
	class Favorite_Model extends Model
	{
		protected $table_name = "favorites";
		protected $db_fields  = array("id", "item_id", "user_id",
												"module", "posted_on");
		
		public $id;
		public $item_id;
		public $user_id;
		public $module;
		public $posted_on;

		// Shared attributes
		public $module_item = false;
		// Not moderated or disabled
		public $is_module_item_blocked = false;

		public function find_favorited_module_item()
		{
			switch($this->module)
			{
				case "photos":
					$module_item_model = new Photo_Model;
				break;

				case "spots":
					$module_item_model = new Spot_Model;
				break;

				case "speed":
					$module_item_model = new Speed_Model;
				break;

				case "videos":
					$module_item_model = new Video_Model;
				break;
			}

			$this->module_item = $module_item_model->find_by_id($this->item_id);
		}

		public function find_favorites_count_per_module_by($user_id = false,
																			$module  = "photos")
		{
			$sql  = "WHERE module = '%s' ";
			$sql .= "  AND user_id = %d  ";

			$sql = sprintf($sql,
								$this->database->escape_value($module),
								$this->database->escape_value($user_id));

			return $this->count($sql);
		}

		public function find_all_favorites_by($user_id       = false,
														  $module        = "photos",
														  $page          = 1,
														  $validate_page = true)
		{
			$sql  = "WHERE module = '%s' ";
			$sql .= "  AND user_id = %d  ";

			$sql = sprintf($sql,
								$this->database->escape_value($module),
								$this->database->escape_value($user_id));

			$this->pagination = new Pagination($page, $this->count($sql));
			if($validate_page) $this->pagination->validate_page_range();

			$sql .= "ORDER BY posted_on DESC ";
			$sql .= "LIMIT {$this->pagination->records_per_page} ";
			$sql .= "OFFSET {$this->pagination->offset}          ";

			return $this->find_all($sql);
		}

		public function find_favorite($item_id = false,
												$user_id = false,
												$module  = "")
		{
			$sql  = "WHERE item_id = %d   ";
			$sql .= "  AND module  = '%s' ";
			$sql .= "  AND user_id = %d   ";
			
			$sql = sprintf($sql,
								$this->database->escape_value($item_id),
								$this->database->escape_value($module),
								$this->database->escape_value($user_id));
			
			return $this->find_by_condition($sql);
		}

		public function find_favorite_by_module($item_id = false,
															 $module  = "")
		{
			$sql  = "WHERE item_id = %d   ";
			$sql .= "  AND module  = '%s' ";

			$sql = sprintf($sql,
								$this->database->escape_value($item_id),
								$this->database->escape_value($module));

			return $this->find_by_condition($sql);
		}

		// Finds,how many times users added item to favorites
		public function find_count_on($item_id,
												$module)
		{
			$sql  = "WHERE item_id = %d   ";
			$sql .= "  AND module  = '%s' ";
			
			$sql = sprintf($sql,
								$this->database->escape_value($item_id),
								$this->database->escape_value($module));
			
			return $this->count($sql);
		}
		
		public function is_module_item_favorite_of($item_id = false,
																 $module  = false,
																 $user_id = false)
		{
			$sql  = "WHERE  item_id     = %d   ";
			$sql .= "  AND  module      = '%s' ";
			$sql .= "  AND  user_id = %d       ";
			$sql .= " LIMIT 1                  ";
			
			$sql  = sprintf($sql,
								 $this->database->escape_value($item_id),
								 $this->database->escape_value($module),
								 $this->database->escape_value($user_id));
			
			return ($this->count($sql) == 1) ? true : false;
		}
		
		public function save()
		{
			// Updating post datetime
			$this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
			
			switch($this->module)
			{
				case "photos":
					$module_stats_model = new Photo_Stats_Model;
				break;

				case "spots":
					$module_stats_model = new Spot_Stats_Model;
				break;

				case "speed":
					$module_stats_model = new Speed_Stats_Model;
				break;

				case "videos":
					$module_stats_model = new Video_Stats_Model;
				break;
			}
			
			$item_stats = $module_stats_model->find_stats_on($this->item_id);
			$item_stats->increase_favorites_count();
			$item_stats->save();
			
			return parent::save();
		}
		
		public function delete()
		{
			switch($this->module)
			{
				case "photos":
					$module_stats_model = new Photo_Stats_Model;
				break;

				case "spots":
					$module_stats_model = new Spot_Stats_Model;
				break;

				case "speed":
					$module_stats_model = new Speed_Stats_Model;
				break;

				case "videos":
					$module_stats_model = new Video_Stats_Model;
				break;
			}
			
			$item_stats = $module_stats_model->find_stats_on($this->item_id);
			$item_stats->decrease_favorites_count();
			$item_stats->save();
			
			return parent::delete();
		}

		public function delete_all_user_favorites($user_id)
		{
			$sql = "WHERE user_id = %d ";
			$sql = sprintf($sql,
								$this->database->escape_value($user_id));
			
			$user_favorites    = $this->find_all($sql);
			$photo_stats_model = new Photo_Stats_Model;
			$spot_stats_model  = new Spot_Stats_Model;
			$speed_stats_model = new Speed_Stats_Model;
			$video_stats_model = new Video_Stats_Model;
			
			foreach($user_favorites as $user_favorite)
			{
				switch($user_favorite->module)
				{
					case "photos":
						$item_stats = $photo_stats_model->find_stats_on($user_favorite->item_id);
					break;

					case "spots":
						$item_stats = $spot_stats_model->find_stats_on($user_favorite->item_id);
					break;

					case "speed":
						$item_stats = $speed_stats_model->find_stats_on($user_favorite->item_id);
					break;

					case "videos":
						$item_stats = $video_stats_model->find_stats_on($user_favorite->item_id);
					break;
				}
				
				$item_stats->decrease_favorites_count();
				$item_stats->save();
			}

			return $this->delete_by_condition($sql);
		}

		public function delete_batch_in_module($ids_batch = array(),
															$module    = "")
		{
			$ids = implode(", ", $ids_batch);

			$sql  = "WHERE item_id IN ($ids)  ";
			$sql .= "  AND module = '%s'      ";

			$sql = sprintf($sql,
								$this->database->escape_value($module));
			
			$user_favorites = $this->find_all($sql);

			switch($module)
			{
				case "photos":
					$item_stats_model = new Photo_Stats_Model;
				break;

				case "spots":
					$item_stats_model  = new Spot_Stats_Model;
				break;

				case "speed":
					$item_stats_model = new Speed_Stats_Model;
				break;

				case "videos":
					$item_stats_model = new Video_Stats_Model;
				break;
			}
			
			foreach($user_favorites as $user_favorite)
			{
				$item_stats = $item_stats_model->find_stats_on($user_favorite->item_id);
				$item_stats->decrease_favorites_count();
				$item_stats->save();
			}
				
			return $this->delete_by_condition($sql);
		}

		public function delete_item_from_all_user_favorites($item_id = false,
																    		 $module  = "")
		{
			$sql  = "WHERE item_id = %d  ";
			$sql .= "  AND module = '%s' ";

			$sql = sprintf($sql,
								$this->database->escape_value($item_id),
								$this->database->escape_value($module));

			return $this->delete_by_condition($sql);
		}
		
		public function get_validation_rules()
		{
			// Validation Rules 
		}
	}
?>