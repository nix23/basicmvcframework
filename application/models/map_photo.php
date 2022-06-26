<?php
	class Map_Photo_Model extends Model
	{
		protected $table_name = "map_photos";
		protected $db_fields  = array("id", "category_id", "user_id", "name", "year",
												"posted_on", "status", "moderated");
		
		public $id;
		public $category_id;
		public $user_id;
		public $name;
		public $year;
		public $posted_on;
		public $status;
		public $moderated;
		
		public $map_photo_photos = array();
		public $photos           = array();
		
		public function save_photos()
		{
			foreach($this->map_photo_photos as $map_photo_photo_model)
			{
				$map_photo_photo_model->photo_id = $this->id;
				$map_photo_photo_model->save();
			}
		}
		
		public function find_all_photos()
		{
			$map_photo_photo_model = new Map_Photo_Photo_Model();
			
			$this->photos = $map_photo_photo_model->find_on($this->id);
		}
		
		public function move_to_original()
		{
			$photoset_model = new Photo_Model;
			$photoset_model->category_id = $this->category_id;
			$photoset_model->user_id     = $this->user_id;
			$photoset_model->name        = $this->name;
			$photoset_model->year        = $this->year;
			$photoset_model->posted_on   = $this->posted_on;
			$photoset_model->moderated   = $this->moderated;
			$photoset_model->status      = $this->status;
			
			$photoset_model->save();
			
			foreach($this->photos as $photo)
			{
				$photo->save_photo_on($photoset_model->id);
				$photo->delete();
			}
		}
	}
?>