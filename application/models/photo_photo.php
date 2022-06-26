<?php
	class Photo_Photo_Model extends Model
	{
		protected $table_name = "photos_photos";
		protected $db_fields  = array("id", "photo_id", "master_name", "main",
												"lazy_clone_greatest_width", "lazy_clone_greatest_height",
												"photos_base_url");
		
		const PRIMARY_PHOTOS_BASE_URL = 0;
		const SECONDARY_PHOTOS_BASE_URL = 1;

		public $id;
		public $photo_id;
		public $master_name;
		public $main;
		public $lazy_clone_greatest_width;
		public $lazy_clone_greatest_height;
		public $photos_base_url;
		
		// Shared attributes
		public $directory;
		public $frame_action;
		public $clones = array(
			array("width" => 800, "height" => 520), // Gallery large photo
			array("width" => 380, "height" => 245), // Module item main photo
			array("width" => 330, "height" => 210), // Most active posts big photo
			array("width" => 270, "height" => 180), // Module item large photo
			array("width" => 135, "height" => 100), // Most active posts small photo
			array("width" => 130, "height" => 90),  // View item small photo
			array("width" => 145, "height" => 95),  // Follow/activity photo
			array("width" => 100, "height" => 75),  // Module item small photo
			array("width" => 80,  "height" => 60),  // Backend list photo
			array("width" => 40,  "height" => 30)   // Main fordrivers small item photo
		);
		public $lazy_clones = array(
			array("width" => 1600, "height" => 1200, "exists" => false),
			array("width" => 1280, "height" => 960,  "exists" => false),
			array("width" => 1024, "height" => 768,  "exists" => false),
			array("width" => 800,  "height" => 600,  "exists" => false)
		);
		public $lazy_clones_count = 0;
		
		// Fetching main photo
		public function find_main_photo_on($photoset_id)
		{
			$sql  = "WHERE photo_id = %d";
			$sql .= "  AND main     = 'yes'";
			
			$sql = sprintf($sql,
								$this->database->escape_value($photoset_id));
			
			return $this->find_by_condition($sql);
		}
		
		public function find_photos_on($photoset_id,
												 $fetch_main = true)
		{
			$sql  = "WHERE photo_id = %d ";
			
			if(!$fetch_main)
				$sql .= "AND main = 'no'  ";
			
			$sql .= "ORDER BY id";
			
			$sql = sprintf($sql,
								$this->database->escape_value($photoset_id));
			
			return $this->find_all($sql);
		}
		
		public function find_lazy_clones_that_exists()
		{
			foreach($this->lazy_clones as &$lazy_clone)
			{
				if($this->lazy_clone_greatest_width >= $lazy_clone["width"]
						and
					$this->lazy_clone_greatest_height >= $lazy_clone["height"])
				{
					$lazy_clone["exists"] = true;
					$this->lazy_clones_count++;
				}
			}
		}

		public function does_lazy_clone_exists($target_width,
															$target_height)
		{
			$exists = false;
			foreach($this->lazy_clones as $lazy_clone)
			{
				if($lazy_clone["width"] == $target_width
						and
					$lazy_clone["height"] == $target_height
						and
					$lazy_clone["exists"])
				{
					$exists = true;
				}
			}

			return $exists;
		}

		public function does_photo_with_specified_sizes_exists_in_fs($photo_filename)
		{
			$photo_path  = UPLOADS_CACHE . $photo_filename . ".jpg";
			return (file_exists($photo_path)) ? true : false;
		}

		public function create_lazy_clone($target_width,
													 $target_height,
													 $photo_filename)
		{
			$this->unpack_directory();

			$original_photo_path  = UPLOADS_IMAGES . $this->directory . DS;
			$original_photo_path .= $this->master_name . ".jpg";

			$downloaded_original_photo_path = null;
			if(!file_exists($original_photo_path)) {
				
				$download_image_url = "http://83.99.185.92/fordrive/public/uploads/images/";
				$download_image_url .= $this->directory . "/" . $this->master_name . ".jpg";

				$unique_photo_id = uniqid() . mt_rand(0, 99999) . ".jpg";
				$downloaded_original_photo_path = UPLOADS_CACHE . DS . $unique_photo_id;
				file_put_contents(
					$downloaded_original_photo_path,
					file_get_contents($download_image_url)
				);
				$original_photo_path = $downloaded_original_photo_path;
			 }

			$lazy_clone_photo_path  = UPLOADS_CACHE . DS;
			$lazy_clone_photo_path .= $photo_filename;

			$image_resizer = new Image_Resizer;
			$image_resizer->load($original_photo_path);
			$image_resizer->resize_image($target_width, $target_height, "crop");
			$image_resizer->watermark_image($target_width, $target_height);
			$image_resizer->save_as_jpg($lazy_clone_photo_path);

			if($downloaded_original_photo_path != null)
				unlink($downloaded_original_photo_path);
		}

		public function are_all_lazy_clones_created()
		{
			$this->unpack_directory();

			$are_lazy_clones_are_created = true;
			foreach($this->lazy_clones as $lazy_clone)
			{
				if($lazy_clone["exists"])
				{
					$lazy_clone_path  = UPLOADS_IMAGES . $this->directory . DS;
					$lazy_clone_path .= $this->master_name;
					$lazy_clone_path .= "-" . $lazy_clone["width"] . "-" . $lazy_clone["height"];
					$lazy_clone_path .= ".jpg";

					if(!file_exists($lazy_clone_path))
					{
						$are_lazy_clones_are_created = false;
					}
				}
			}

			return $are_lazy_clones_are_created;
		}

		public function delete_original_photo()
		{
			$this->unpack_directory();

			$original_photo_path  = UPLOADS_IMAGES . $this->directory . DS;
			$original_photo_path .= $this->master_name . ".jpg";

			if(file_exists($original_photo_path))
				unlink($original_photo_path);
		}

		// Unpacking action,which will execute on this photo
		public function unpack_frame($frame)
		{
			$frame_actions = array("ajax", "deleteajax", "delete");
			$frame_parts   = explode("-", $frame);
			
			if(in_array($frame_parts[0], $frame_actions))
			{
				$this->frame_action = $frame_parts[0];
				array_shift($frame_parts);
				$this->master_name = implode("-", $frame_parts); 
			}
			else
			{
				$this->frame_action = "none";
			}
		}
		
		// Unpacks directory from master_name
		public function unpack_directory()
		{
			$master_name_parts = explode("-", $this->master_name);
			$this->directory   = $master_name_parts[0];
		}
		
		private function move_master_photo_with_clones()
		{
			$this->unpack_directory();
			
			// Moving master photo
			$ajax_path    = UPLOADS_AJAX . $this->master_name . ".jpg";
			$images_path  = UPLOADS_IMAGES . $this->directory . DS;
			$images_path .= $this->master_name . ".jpg";
			
			if(copy($ajax_path, $images_path))
			{
				unlink($ajax_path);
			}
			
			// Moving clones
			foreach($this->clones as $clone)
			{
				$sizes  = "-" . $clone["width"];
				$sizes .= "-" . $clone["height"];
				
				$ajax_path    = UPLOADS_AJAX . $this->master_name . $sizes . ".jpg";
				$images_path  = UPLOADS_IMAGES . $this->directory . DS;
				$images_path .= $this->master_name . $sizes . ".jpg";
				
				if(copy($ajax_path, $images_path))
				{
					unlink($ajax_path);
				}
			}
		}
		
		private function find_lazy_clone_greatest_sizes()
		{
			$session             = Registry::get('session');
			$sizes               = $session->get_master_photo_sizes($this->master_name);
			$master_photo_width  = $sizes["width"];
			$master_photo_height = $sizes["height"];
			
			foreach($this->lazy_clones as $lazy_clone)
			{
				if($master_photo_width >= $lazy_clone["width"]
						and
					$master_photo_height >= $lazy_clone["height"])
				{
					$this->lazy_clone_greatest_width  = $lazy_clone["width"];
					$this->lazy_clone_greatest_height = $lazy_clone["height"];
					break;
				}
			}
		}
		
		// Move image and clones in images folder
		// and saves record in database.
		public function save_with_clones()
		{
			$this->move_master_photo_with_clones();
			$this->find_lazy_clone_greatest_sizes(); 
			$this->main = "no";
			$this->save();
		}
		
		// Deletes temp images in /ajax directory, which
		// was uploaded,but then deleted.
		public function delete_ajax()
		{
			// Deleting master photo
			$ajax_path = UPLOADS_AJAX . $this->master_name . ".jpg";
			
			if(file_exists($ajax_path))
			{
				unlink($ajax_path);
			}
			
			// Deleting clones
			foreach($this->clones as $clone)
			{
				$sizes  = "-" . $clone["width"];
				$sizes .= "-" . $clone["height"];
				
				$ajax_path = UPLOADS_AJAX . $this->master_name . $sizes . ".jpg";
				
				if(file_exists($ajax_path))
				{
					unlink($ajax_path);
				}
			}
		}
		
		private function delete_master_photo_with_clones()
		{
			$this->unpack_directory();
			
			// Deleting master photo
			$images_path  = UPLOADS_IMAGES . $this->directory . DS;
			$images_path .= $this->master_name . ".jpg";
			
			if(file_exists($images_path))
			{
				unlink($images_path);
			}
			
			// Deleting clones
			$clones = array_merge($this->lazy_clones, $this->clones);
			
			foreach($clones as $clone)
			{
				$sizes  = "-" . $clone["width"];
				$sizes .= "-" . $clone["height"];
				
				$images_path  = UPLOADS_IMAGES . $this->directory . DS;
				$images_path .= $this->master_name . $sizes . ".jpg";
				
				if(file_exists($images_path))
				{
					unlink($images_path);
				}
			}
		}
		
		// Deletes images from /images/dir and db record
		public function delete_with_clones()
		{
			$this->delete_master_photo_with_clones();
			
			// Deleting db record
			$sql  = "WHERE master_name = '%s'";
			$sql .= "  AND photo_id    = %d";
			
			$sql = sprintf($sql,
								$this->database->escape_value($this->master_name),
								$this->database->escape_value($this->photo_id));
			
			$this->delete_by_condition($sql);
		}
		
		// Updates main photo
		public function update_main()
		{
			// Unsetting current main,if it exists
			$sql  = "WHERE photo_id = %d";
			$sql .= "  AND main     = 'yes'";
			
			$sql = sprintf($sql,
								$this->database->escape_value($this->photo_id));
			
			$current_main = $this->find_by_condition($sql);
			
			if($current_main)
			{
				$current_main->main = "no";
				$current_main->save();
			}
			
			// Setting new main
			$sql  = "WHERE photo_id    = %d";
			$sql .= "  AND master_name = '%s'";
			
			$sql = sprintf($sql,
								$this->database->escape_value($this->photo_id),
								$this->database->escape_value($this->master_name));
			
			$new_main = $this->find_by_condition($sql);
			$new_main->main = "yes";
			$new_main->save();
		}
		
		public function get_validation_rules()
		{
			// Rules
		}
	}
?>