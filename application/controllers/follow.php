<?php
	class Follow_Controller extends Public_Controller
	{
		// Restrict access to unauthorized users
		public function before()
		{
			$this->show_404_if_not_authorized();
			parent::before();
		}
		
		public function index($page          = 1,
									 $days_to_fetch = 1)
		{
			$days_to_fetch_items = array(
				(object) array("label" => "Day",   "value" => "1",  "selected" => false),
				(object) array("label" => "Week",  "value" => "7",  "selected" => false),
				(object) array("label" => "Month", "value" => "30", "selected" => false)
			);
			
			foreach($days_to_fetch_items as $days_to_fetch_item)
			{
				if((int)$days_to_fetch_item->value == $days_to_fetch)
					$days_to_fetch_item->selected = true;
			}
			
			$follow_mapper  = new Follow_Mapper;
			// TODO --> USER DAYS_TO_FETCH!!!
			$followed_posts = $follow_mapper->find_followed_posts_by($this->session->user_id,
																						$page,
																						$days_to_fetch);
			foreach($followed_posts as $followed_post)
			{
				$followed_post->find_category_and_subcategory();
				$followed_post->find_main_photo();
				$followed_post->find_user();
				$followed_post->find_comments_likes_and_views_count();
			}
			
			$data['followed_posts']        = $followed_posts;
			$data['pages']                 = $follow_mapper->pagination->make_pages("compact");
			$data['current_page']          = $page;
			$data['days_to_fetch_items']   = $days_to_fetch_items;
			$data['current_days_to_fetch'] = $days_to_fetch;

			$this->page_title    = "Following / Fordrive";
			$this->view->content = View::capture("follow" . DS . "follow_list", $data);
		}
		
		public function load_form($request_type = "")
		{
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$follower_model = new Follower_Model;
			$followed_users = $follower_model->find_followed_users_by($this->session->user_id);
			
			foreach($followed_users as $followed_user)
			{
				$followed_user->find_followed_user();
			}
			
			$data['followed_users']                 = $followed_users;
			$this->ajax->data->followed_users_html  = View::capture("follow" . DS . "followed_users_list", $data);
			$this->ajax->data->followed_users_count = count($followed_users);
			$this->ajax->callback                   = "update_followed_users_form";
			$this->ajax->result                     = "ok";
			
			$this->ajax->render();
		}
		
		public function change_follow_status($request_type = "",
														 $followed_id  = false)
		{
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$follower_model            = new Follower_Model;
			$followed_user_by_follower = $follower_model->find_followed_user_by_follower($followed_id,
																												  $this->session->user_id);
			
			if($followed_user_by_follower)
			{
				if($followed_user_by_follower->delete())
				{
					$this->ajax->data->new_follow_status = "follow";
				}
			}
			else
			{
				$follower_model->followed_id = $followed_id;
				$follower_model->follower_id = $this->session->user_id;
				
				if($follower_model->save())
				{
					$this->ajax->data->new_follow_status = "unfollow";
				}
			}
			
			$this->ajax->callback = "update_followed_users_form_status";
			$this->ajax->result   = "ok";
			
			$this->ajax->render();
		}
	}
?>