<?php
	class Category_Model extends Model
	{
		protected $table_name = "categories";
		protected $db_fields  = array("id", "parent_id", "name", "show_in_modules", "status");
		protected $nested_db_fields = array("attached_items_count");
		
		public $id;
		public $parent_id;
		public $name;
		public $show_in_modules;
		public $status;
		
		// Nested attributes
		public $attached_items_count;
		
		// Shared attributes
		public $subcategories        = false;
		public $subcategories_count  = false;
		public $in_categories        = array();
		public $selected_category    = false;
		public $selected_subcategory = false;
		
		// Finds selected category,subcategory and
		// category subcategories. Subcategories can
		// be fetched only with attached items.
		// Also fils $in_categories list with id's.
		public function find_selected_categories($category_id                   = false,
															  $only_with_attached_items      = false,
															  $attached_items_model          = false,
															  $only_moderated_attached_items = true,
															  $only_enabled_attached_items   = true,
															  $only_from_specified_user      = false,
															  $user_id                       = false)
		{
			$this->selected_category = $this->find_by_id($category_id);
			
			if($this->selected_category)
			{
				// Category and subcategory are both selected
				if($this->selected_category->parent_id != '0')
				{
					$this->selected_subcategory = $this->selected_category;
					$this->selected_category    = $this->find_by_id($this->selected_subcategory->parent_id);
					
					$this->selected_category->find_subcategories($only_with_attached_items,
																				$attached_items_model,
																				$only_moderated_attached_items,
																				$only_enabled_attached_items,
																				$only_from_specified_user,
																				$user_id);
					
					$this->in_categories[] = $this->selected_subcategory->id;
				}
				// Only category is selected
				else
				{
					$this->selected_category->find_subcategories($only_with_attached_items,
																				$attached_items_model,
																				$only_moderated_attached_items,
																				$only_enabled_attached_items,
																				$only_from_specified_user,
																				$user_id);
					
					foreach($this->selected_category->subcategories as $subcategory)
					{
						$this->in_categories[] = $subcategory->id;
					}
					
					$this->in_categories[] = $this->selected_category->id;
				}
				
				return true;
			}
			else
			{
				return false;
			}
		}
		
		/*
		// Finds all this category children
		public function find_subcategories($only_with_attached_items      = false,
													  $attached_items_model          = false,
													  $only_moderated_attached_items = true,
													  $only_enabled_attached_items   = true,
													  $only_from_specified_user      = false,
													  $user_id                       = false)
		{
			if($only_with_attached_items)
			{
				$items_model      = new $attached_items_model;
				$items_table      = $items_model->get_table_name();
				$categories_table = $this->get_table_name();
				
				$sql  = " SELECT $categories_table.*,                                   ";
				$sql .= "        $categories_table.id AS root_category_id,              ";
				$sql .= "        (SELECT COUNT(*)                                       ";
				$sql .= "           FROM $items_table                                   ";
				$sql .= "          WHERE $items_table.category_id = $categories_table.id";
				
				if($only_enabled_attached_items)
					$sql .="				AND status = 'enabled'";
				if($only_moderated_attached_items)
					$sql .= "			AND moderated = 'yes'";
				if($only_from_specified_user)
					$sql .= sprintf("	AND user_id = %d ", $user_id);
				
				$sql .= "        ) AS attached_items_count                              ";
				$sql .= "  FROM $categories_table                                       ";
				$sql .= " WHERE parent_id = %d                                          ";
				$sql .= "HAVING attached_items_count > 0                                ";
				$sql .= " ORDER BY name ASC                                             ";
				
				$sql = sprintf($sql,
									$this->database->escape_value($this->id));
				
				$this->subcategories = $this->find_by_sql($sql);
			}
			else
			{
				$sql  = "WHERE parent_id = %d ";
				$sql .= "ORDER BY name ASC";
				
				$sql = sprintf($sql,
									$this->database->escape_value($this->id));
				
				$this->subcategories = $this->find_all($sql);
			}
		}
		*/
		
		// Finds all this category children
		public function find_subcategories($only_with_attached_items      = false,
													  $attached_items_model          = false,
													  $only_moderated_attached_items = true,
													  $only_enabled_attached_items   = true,
													  $only_from_specified_user      = false,
													  $user_id                       = false)
		{
			if($only_with_attached_items)
			{
				$items_model = new $attached_items_model;
				$items       = $items_model->get_table_name();
				$categories  = $this->get_table_name();
				
				$sql  = " SELECT $categories.* FROM $categories                         ";
				$sql .= " INNER JOIN (                                                  ";
				$sql .= "		SELECT DISTINCT $categories.id FROM $categories          ";
				$sql .= "      INNER JOIN $items ON $categories.id = $items.category_id ";
				$sql .= "		WHERE $categories.parent_id = %d                         ";
				
				if($only_enabled_attached_items)
					$sql .="				AND $items.status = 'enabled' ";
				if($only_moderated_attached_items)
					$sql .= "			AND $items.moderated = 'yes'  ";
				if($only_from_specified_user)
					$sql .= sprintf("	AND $items.user_id = %d ", $user_id);
				
				$sql .= " ) AS not_empty_subcategories                                  ";
				$sql .= " ON $categories.id = not_empty_subcategories.id                ";
				$sql .= " ORDER BY name ASC                                             ";
				
				$sql = sprintf($sql,
									$this->database->escape_value($this->id));

				$this->subcategories = $this->find_by_sql($sql);
			}
			else
			{
				$sql  = "WHERE parent_id = %d ";
				$sql .= "ORDER BY name ASC    ";
				
				$sql = sprintf($sql,
									$this->database->escape_value($this->id));
				
				$this->subcategories = $this->find_all($sql);
			}
		}
		
		// Returns all(special and shared) root categories
		public function get_all_special_and_shared_categories()
		{
			$special_categories = $this->get_all_special_categories();
			
			foreach($special_categories as $special_category)
			{
				$special_category->get_subcategories_count();
			}
			
			$shared_categories  = $this->get_shared_categories();
			
			foreach($shared_categories as $shared_category)
			{
				$shared_category->get_subcategories_count();
			}
			
			return array_merge($special_categories, $shared_categories);
		}
		
		// Returns all modules specific categories
		public function get_all_special_categories($parent_id = 0)
		{
			$sql  = "WHERE parent_id = %d ";
			$sql .= "AND show_in_modules != 'all' ";
			$sql .= "ORDER BY show_in_modules, name ASC";
			
			$sql = sprintf($sql,
								$this->database->escape_value($parent_id));
			
			return $this->find_all($sql);
		}
		
		
		// private function find_all_root_categories_with_attached_items($module                        = false,
		// 																				  $attached_items_model          = false,
		// 																				  $only_moderated_attached_items = true,
		// 																				  $only_enabled_attached_items   = true,
		// 																				  $only_from_user_id             = false)
		// {
		// 	$items_model      = new $attached_items_model;
		// 	$items_table      = $items_model->get_table_name();
		// 	$categories_table = $this->get_table_name();
			
		// 	$sql  = " SELECT $categories_table.*,                                                     ";
		// 	$sql .= "        $categories_table.id AS root_category_id,                                ";
		// 	$sql .= "        (SELECT COUNT(*)                                                         ";
		// 	$sql .= "           FROM $items_table                                                     ";
		// 	$sql .= "          WHERE ($items_table.category_id IN (SELECT $categories_table.id        ";
		// 	$sql .= "                                                FROM $categories_table           ";
		// 	$sql .= "                                               WHERE parent_id = root_category_id";
		// 	$sql .= "                                             )                                   ";
		// 	$sql .= "             		OR $items_table.category_id = root_category_id                 ";
		// 	$sql .= "                )                                                                ";
			
		// 	if($only_enabled_attached_items)
		// 		$sql .="					AND status = 'enabled'";
		// 	if($only_moderated_attached_items)
		// 		$sql .= "				AND moderated = 'yes' ";
		// 	if($only_from_user_id)
		// 		$sql .= sprintf("		AND user_id = %d ", $only_from_user_id);
			
		// 	$sql .= "        ) AS attached_items_count                                                ";
		// 	$sql .= "  FROM $categories_table                                                         ";
		// 	$sql .= " WHERE parent_id = 0                                                             ";
		// 	$sql .= "   AND show_in_modules = '%s'                                                    ";
		// 	$sql .= "HAVING attached_items_count > 0                                                  ";
		// 	$sql .= " ORDER BY name ASC                                                               ";
			
		// 	$sql = sprintf($sql,
		// 						$this->database->escape_value($module));
		// 	echo $sql . "<br><br>";
		// 	return $this->find_by_sql($sql);
		// }
		
		
		private function find_all_root_categories_with_attached_items($module                        = false,
																						  $attached_items_model          = false,
																						  $only_moderated_attached_items = true,
																						  $only_enabled_attached_items   = true,
																						  $only_from_user_id             = false)
		{
			$items_model      = new $attached_items_model;
			$items_table      = $items_model->get_table_name();
			$categories_table = $this->get_table_name();
			
			$item_conditions_sql = sprintf(" AND $categories_table.show_in_modules = '%s' ",
													 $this->database->escape_value($module));
			
			if($only_enabled_attached_items)
				$item_conditions_sql .= " AND $items_table.status = 'enabled' ";
			
			if($only_moderated_attached_items)
				$item_conditions_sql .= " AND $items_table.moderated = 'yes'  ";
			
			if($only_from_user_id)
				$item_conditions_sql .= sprintf(" AND $items_table.user_id = %d ", $only_from_user_id);
			
			$sql  = " SELECT $categories_table.* FROM $categories_table                                     ";
			$sql .= " INNER JOIN (                                                                          ";
			$sql .= "      SELECT DISTINCT $categories_table.parent_id AS id FROM $categories_table         ";
			$sql .= "      INNER JOIN $items_table ON $categories_table.id = $items_table.category_id       ";
			$sql .= "      WHERE $categories_table.parent_id != 0                                           ";
			$sql .= "            $item_conditions_sql                                                       ";
			$sql .= "      UNION                                                                            ";
			$sql .= "      SELECT DISTINCT $categories_table.id AS id FROM $categories_table                ";
			$sql .= "      INNER JOIN $items_table ON $categories_table.id = $items_table.category_id       ";
			$sql .= "      WHERE $categories_table.parent_id = 0                                            ";
			$sql .= "            $item_conditions_sql                                                       ";
			$sql .= " ) AS not_empty_root_categories ON $categories_table.id = not_empty_root_categories.id ";
			$sql .= " ORDER BY name ASC                                                                     ";

			return $this->find_by_sql($sql);
		}
		
		public function get_not_empty_root_categories_by_module($module                        = false,
																				  $attached_items_model          = false,
																				  $only_moderated_attached_items = true,
																				  $only_enabled_attached_items   = true,
																				  $only_from_user_id             = false)
		{
			$special_categories = $this->find_all_root_categories_with_attached_items($module,
																											  $attached_items_model,
																											  $only_moderated_attached_items,
																											  $only_enabled_attached_items,
																											  $only_from_user_id);
			$shared_categories  = $this->find_all_root_categories_with_attached_items("all",
																											  $attached_items_model,
																											  $only_moderated_attached_items,
																											  $only_enabled_attached_items,
																											  $only_from_user_id);
			$module_categories  = array_merge($special_categories, $shared_categories);
			
			return $module_categories;
		}
		
		// Returns only modules specific categories
		public function get_special_categories($parent_id    = 0,
															$module       = "",
															$only_enabled = false)
		{
			$sql  = "WHERE parent_id = %d ";
			$sql .= "AND show_in_modules = '%s' ";
			
			if($only_enabled)
			{
				$sql .= "AND status = 'enabled' ";
			}
			
			$sql .= "ORDER BY name ASC";
			
			$sql = sprintf($sql,
								$this->database->escape_value($parent_id),
								$this->database->escape_value($module));
			
			return $this->find_all($sql);
		}
		
		// Returns shared between modules categories
		public function get_shared_categories($parent_id    = 0,
														  $only_enabled = false)
		{
			$sql  = "WHERE parent_id = %d ";
			$sql .= "AND show_in_modules = 'all' ";
			
			if($only_enabled)
			{
				$sql .= "AND status = 'enabled' ";
			}
			
			$sql .= "ORDER BY name ASC";
			
			$sql = sprintf($sql,
								$this->database->escape_value($parent_id));
			
			return $this->find_all($sql);
		}
		
		// Returns special and shared categories,which belongs to specific module
		public function get_categories_by_module($parent_id           = 0,
															  $module              = "",
															  $fetch_subcategories = false)
		{
			$special_categories = $this->get_special_categories($parent_id, $module);
			$shared_categories  = $this->get_shared_categories($parent_id);
			
			$module_categories = array_merge($special_categories, $shared_categories);
			
			if($fetch_subcategories)
			{
				foreach($module_categories as $category)
				{
					$category->find_subcategories();
				}
			}
			
			return $module_categories;
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
		}
		
		// Returns category subcategories count
		public function get_subcategories_count()
		{
			$sql = "WHERE parent_id = %d";
			
			$sql = sprintf($sql,
								$this->database->escape_value($this->id));
			
			$this->subcategories_count = $this->count($sql);
		}
		
		// Checks,if category has childrens
		public function has_subcategories()
		{
			$this->get_subcategories_count();
			return ($this->subcategories_count > 0) ? true : false;
		}
		
		// Delete category and all children (if exists)
		public function delete_root_category()
		{
			if($this->has_subcategories())
			{
				$sql = "WHERE parent_id = %d";
				$sql = sprintf($sql,
									$this->database->escape_value($this->id));
				
				$subcategories = $this->find_all($sql);

				foreach($subcategories as $subcategory)
					$subcategory->delete_category();
			}
			
			return $this->delete_category();
		}

		// Deletes this category with all attached items to it.
		public function delete_category()
		{
			$photoset_model = new Photo_Model;
			$spot_model     = new Spot_Model;
			$speed_model    = new Speed_Model;
			$video_model    = new Video_Model;
			$attached_items = array();

			$photosets = $photoset_model->find_all_photosets_attached_to_category($this->id);
			$spots     = $spot_model->find_all_spots_attached_to_category($this->id);
			$speeds    = $speed_model->find_all_speeds_attached_to_category($this->id);
			$videos    = $video_model->find_all_videos_attached_to_category($this->id);

			foreach($photosets as $photoset)
				$attached_items[] = $photoset;

			foreach($spots as $spot)
				$attached_items[] = $spot;

			foreach($speeds as $speed)
				$attached_items[] = $speed;

			foreach($videos as $video)
				$attached_items[] = $video;

			foreach($attached_items as $attached_item)
				$attached_item->delete();

			return $this->delete();
		}
		
		// If we are saving subcategory,we must inherit parent show_in_modules field
		public function if_is_subcategory_inherit_show_in_modules()
		{
			if((int)$this->parent_id != 0)
			{
				$sql = "AND parent_id = 0";
				
				$category = $this->find_by_id($this->parent_id);
				
				if($category)
				{
					$this->show_in_modules = $category->show_in_modules;
				}
			}
		}
		
		// Checks,that category does not have sibling with same name
		public function is_unique_name_in_siblings_list()
		{
			$sql  = "AND parent_id = %d         ";
			$sql .= "AND show_in_modules = '%s' ";
			
			$sql = sprintf($sql,
								$this->database->escape_value($this->parent_id),
								$this->database->escape_value($this->show_in_modules));
			
			// If it is update,do not check current active name
			if(!empty($this->id))
			{
				$category = $this->find_by_id($this->id);
				
				if($category)
				{
					$sql .= " AND id != '{$category->id}'";
				}
			}
			
			return $this->is_unique("name", $this->name, $sql);
		}
		
		public function get_validation_rules()
		{
			$rules = array();
			
			$rules['name'] = array(
				array('name_required',  'Please enter category name.',             'required'),
				array('name_maxlength', 'Max length of category name: 255 chars.', 'max_length', 255)
			);
			
			$rules['parent_id'] = array(
				array('parent_id_required', 'Please select parent category.', 'required')
			);
			
			return $rules;
		}
	}
?>