<?php
	class Admin_Users_Controller extends Admin_Controller
	{
		public function index($page            = 1,
									 $sort            = "username-asc",
									 $username_prefix = "all")
		{
			// Sort by list
			list($order_by, $direction) = explode("-", $sort);

			$sort_items = array(
				(object) array("name" => "username",     "type" => "username",     "direction" => "asc",  "selected" => false),
				(object) array("name" => "registration", "type" => "registration", "direction" => "desc", "selected" => false),
				(object) array("name" => "rank",         "type" => "rank",         "direction" => "asc",  "selected" => false)
			);

			foreach($sort_items as $sort_item)
			{
				if($sort_item->type == $order_by)
				{
					$sort_item->direction = ($direction == "asc") ? "desc" : "asc";
					$sort_item->selected  = true;
				}

				$sort_item->sort = $sort_item->type . "-" . $sort_item->direction;
			}

			// Username prefixes list
			$username_prefixes   = array();
			$username_prefixes[] = (object) array("name" => "all",   "selected" => false);

			for($char = "a"; $char <= "z"; $char++)
			{
				$username_prefixes[] = (object) array("name" => "$char", "selected" => false);
				if($char == "z") break;
			}

			$username_prefixes[] = (object) array("name" => "other", "selected" => false);

			foreach($username_prefixes as $username_prefix_object)
			{
				if($username_prefix_object->name == $username_prefix)
					$username_prefix_object->selected = true;
			}

			$user_model = new User_Model;
			$direction  = mb_strtoupper($direction, "utf-8");
			$order_by   = str_replace("registration", "registred_on", $order_by);
			$users      = $user_model->find_all_users($page,
															 		$order_by,
															 		$direction,
																	$username_prefix);

			$data['users']             = $users;
			$data['pages']             = $user_model->pagination->make_pages("compact");
			$data['current_page']      = $page;
			$data['sort_items']        = $sort_items;
			$data['selected_sort']     = $sort;
			$data['username_prefixes'] = $username_prefixes;
			$data['current_prefix']    = $username_prefix;
			$data['settings']          = $this->view->settings;

			$this->view->content = View::capture("users" . DS . "users_list", $data, true, array("settings"));
		}

		public function delete_user($request_type    = "",
											 $user_id         = false,
											 $page            = false,
									       $sort            = false,
									       $username_prefix = false)
		{
			$this->is_ajax($request_type);
			$this->validate_token();

			$user_model = new User_Model;
			$user       = $user_model->find_by_id($user_id);

			if($user)
			{
				if($user->delete_account())
				{
					list($order_by, $direction) = explode("-", $sort);

					$user_model = new User_Model;
					$direction  = mb_strtoupper($direction, "utf-8");
					$order_by   = str_replace("registration", "registred_on", $order_by);
					$users      = $user_model->find_all_users($page,
																			$order_by,
																			$direction,
																			$username_prefix,
																			false);

					// If now this page has 0 items and it isn't first page,
					// redirecting to last page
					if(!$users and $page != 1)
					{
						$last_page = $user_model->pagination->total_pages;

						$url_segments  = "users/list/";
						$url_segments .= "page-$last_page/";
						$url_segments .= "sort-$sort/";
						$url_segments .= "prefix-$username_prefix";

						$this->ajax->data->url_segments = $url_segments;
						$this->ajax->callback           = "redirect";
					}
					// Else we should update pagination and items
					else
					{
						$data["users"]          = $users;
						$data["pages"]          = $user_model->pagination->make_pages("compact");
						$data["current_page"]   = $page;
						$data["selected_sort"]  = $sort;
						$data["current_prefix"] = $username_prefix;

						$this->ajax->callback              = "update_user_items_and_pagination_html";
						$this->ajax->data->items_html      = View::capture("users" . DS . "users_list_items",      $data);
						$this->ajax->data->pagination_html = View::capture("users" . DS . "users_list_pagination", $data);
					}

					$this->ajax->result = "ok";
					$this->ajax->render();
				}
			}
			else
			{
				exit("Wrong user id passed to delete_user action.");
			}
		}

		public function change_user_status($request_type = "",
													  $user_id      = false)
		{
			$this->is_ajax($request_type);
			$this->validate_token();

			$user_model = new User_Model;
			$user       = $user_model->find_by_id($user_id);

			if(!$user)
			{
				exit("Wrong user id passed to change_user_status action.");
			}

			if($user->change_blocked_status())
			{
				$this->ajax->result           = "ok";
				$this->ajax->callback         = "update_user_blocked_status";
				$this->ajax->data->new_status = ($user->is_account_blocked()) ? "Unblock" : "Block";

				$this->ajax->render();
			}
		}
	}
?>