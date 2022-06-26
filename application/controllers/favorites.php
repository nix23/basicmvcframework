<?php
	class Favorites_Controller extends Public_Controller
	{
		// Restrict access to unauthorized users
		public function before()
		{
			$this->show_404_if_not_authorized();
			parent::before();
		}

		public function index($module = "photos",
									 $page   = 1)
		{
			$favorite_model = new Favorite_Model;
			$favorites      = $favorite_model->find_all_favorites_by($this->session->user_id,
																						$module,
																						$page);
			foreach($favorites as $favorite)
			{
				$favorite->find_favorited_module_item();

				// Item is temporary locked
				if($favorite->module_item->status == "disabled"
						or
					$favorite->module_item->moderated == "no")
				{
					$favorite->is_module_item_blocked = true;
				}

				$favorite->module_item->find_category_and_subcategory();
				$favorite->module_item->find_main_photo();
				$favorite->module_item->find_likes_count();
				$favorite->module_item->find_comments_count();
			}

			// Generating modules list for sorting
			$modules = array(
				(object) array(
					"name"     => "photos",
					"label"    => "Photosets",
					"count"    => $favorite_model->find_favorites_count_per_module_by($this->session->user_id,
																											"photos"),
					"selected" => false
				),
				(object) array(
					"name"     => "spots",
					"label"    => "Spots",
					"count"    => $favorite_model->find_favorites_count_per_module_by($this->session->user_id,
																											"spots"),
					"selected" => false
				),
				(object) array(
					"name"     => "speed",
					"label"    => "Speeds",
					"count"    => $favorite_model->find_favorites_count_per_module_by($this->session->user_id,
																											"speed"),
					"selected" => false
				),
				(object) array(
					"name"     => "videos",
					"label"    => "Videos",
					"count"    => $favorite_model->find_favorites_count_per_module_by($this->session->user_id,
																											"videos"),
					"selected" => false
				)
			);

			foreach($modules as $module_item)
			{
				if($module_item->name == $module)
					$module_item->selected = true;
			}

			// Setting template data
			$data['favorites']       = $favorites;
			$data['pages']           = $favorite_model->pagination->make_pages("compact");
			$data['current_page']    = $page;
			$data['modules']         = $modules;
			$data['selected_module'] = $module;

			$this->page_title    = "Favorites / Fordrive";
			$this->view->content = View::capture("favorites" . DS . "favorites", $data);
		}

		public function unfavorite($request_type = "",
											$id           = 0,
											$page         = 1)
		{
			$this->is_ajax($request_type);
			$this->validate_token();

			$favorite_model = new Favorite_Model;
			$favorite       = $favorite_model->find_by_id($id);

			if(!$favorite)
			{
				$this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
				$this->ajax->render();
			}

			if(!$favorite->is_record_deleting_by_owner($this->session->user_id,
																	 $favorite->user_id))
				exit("You are not owner of this record.");

			if($favorite->delete())
			{
				$favorites = $favorite_model->find_all_favorites_by($this->session->user_id,
																					 $favorite->module,
																					 $page,
																					 false);
				// If now this page has 0 items and it isn't first page,
				// we are redirecting to previous page
				if(!$favorites and $page != 1)
				{
					$page--;
					$url_segments = "favorites/list/$favorite->module/page-$page";

					$this->ajax->callback           = "redirect";
					$this->ajax->data->url_segments = $url_segments;
				}
				// Else we should update pagination and items
				else
				{
					foreach($favorites as $favorite)
					{
						$favorite->find_favorited_module_item();

						// Item is temporary locked
						if($favorite->module_item->status == "disabled"
								or
							$favorite->module_item->moderated == "no")
						{
							$favorite->is_module_item_blocked = true;
						}

						$favorite->module_item->find_category_and_subcategory();
						$favorite->module_item->find_main_photo();
						$favorite->module_item->find_likes_count();
						$favorite->module_item->find_comments_count();
					}

					$data['favorites']       = $favorites;
					$data['pages']           = $favorite_model->pagination->make_pages("compact");
					$data['current_page']    = $page;
					$data['selected_module'] = $favorite->module;

					$this->ajax->callback              = "update_html_after_item_unfavorite";
					$this->ajax->data->items_html      = View::capture("favorites" . DS . "favorites_items",      $data);
					$this->ajax->data->pagination_html = View::capture("favorites" . DS . "favorites_pagination", $data);
				}

				$this->ajax->result = "ok";
				$this->ajax->render();
			}
		}
	}
?>