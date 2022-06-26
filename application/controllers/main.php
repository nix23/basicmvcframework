<?php
	class Main_Controller extends Public_Controller
	{
		public function index()
		{
			$data = array();

			// Creating module objects
			$photoset_model = new Photo_Model;
			$spot_model     = new Spot_Model;
			$speed_model    = new Speed_Model;
			$video_model    = new Video_Model;

			$most_active_posts_mapper = new Most_Active_Posts_Mapper;
			$all_most_active_posts    = $most_active_posts_mapper->find_most_active_posts_in_last(24);
			
			if(count($all_most_active_posts) < 9)
				$all_most_active_posts = $most_active_posts_mapper->find_most_active_posts_in_last(24 * 7);

			// Capturing 9 most active posts
			$most_active_posts       = array();
			$most_active_posts_count = 0;
			foreach($all_most_active_posts as $most_active_post)
			{
				switch($most_active_post["module_name"])
				{
					case "photos":
						$module_model = &$photoset_model;
					break;

					case "spots":
						$module_model = &$spot_model;
					break;

					case "speed":
						$module_model = &$speed_model;
					break;

					case "videos":
						$module_model = &$video_model;
					break;
				}

				$post                = $module_model->find_by_id($most_active_post["module_item_id"]);
				$module_name         = $most_active_post["module_name"];
				$most_active_posts[] = array("module_name" => $module_name, "post" => $post);

				$most_active_posts_count++;
				if($most_active_posts_count == 9)
					break;
			}

			foreach($most_active_posts as $most_active_post)
			{
				$most_active_post["post"]->find_category_and_subcategory();
				$most_active_post["post"]->find_main_photo();
				$most_active_post["post"]->find_comments_count();
			}

			// Splitting most active posts in groups for convenient rendering
			$most_active_post             = false;
			$most_active_posts_first_set  = array();
			$most_active_posts_second_set = array();
			$most_active_posts_count      = 0;
			foreach($most_active_posts as $most_active_post_array)
			{
				if($most_active_posts_count == 0)
				{
					$most_active_post = $most_active_post_array;
				}
				else if($most_active_posts_count >= 1 and $most_active_posts_count <= 4)
				{
					$most_active_posts_first_set[] = $most_active_post_array;
				}
				else
				{
					$most_active_posts_second_set[] = $most_active_post_array;
				}

				$most_active_posts_count++;
			}

			$data["most_active_post"]             = $most_active_post;
			$data["most_active_posts_first_set"]  = $most_active_posts_first_set;
			$data["most_active_posts_second_set"] = $most_active_posts_second_set;

			// Capturing last items
			$photosets = $photoset_model->find_n_last_approved_photosets(24, true, true);
			$spots     = $spot_model->find_n_last_approved_spots(24, true, true);
			$speeds    = $speed_model->find_n_last_approved_speeds(5, true, true, true, true);
			$videos    = $video_model->find_n_last_approved_videos(5, true, true, true, true);

			foreach($photosets as $photoset)
			{
				$photoset->find_category_and_subcategory();
			}

			foreach($spots as $spot)
			{
				$spot->find_category_and_subcategory();
			}

			foreach($speeds as $speed)
			{
				$speed->find_category_and_subcategory();
			}

			foreach($videos as $video)
			{
				$video->find_category_and_subcategory();
			}

			$data["photosets"] = $photosets;
			$data["spots"]     = $spots;
			$data["speeds"]    = $speeds;
			$data["videos"]    = $videos;

			// Capturing top users
			$user_model = new User_Model;
			$top_users  = $user_model->find_n_top_active_users(5);

			foreach($top_users as $top_user)
				$top_user->find_n_last_approved_module_items(4);

			$data["top_users"] = $top_users;

			// Capturing last activities
			$main_activity_mapper = new Main_Activity_Mapper;
			$last_activities      = $main_activity_mapper->find_last_activities(5, 24);
			
			if(count($last_activities) < 5)
				$last_activities = $main_activity_mapper->find_last_activities(5, 24 * 31);

			foreach($last_activities as $last_activity)
			{
				$last_activity->find_category_and_subcategory();
				$last_activity->find_main_photo();
				$last_activity->find_user();
				$last_activity->find_comments_and_likes_count();
				$last_activity->find_views_count();
			}

			$data["last_activities"] = $last_activities;

			$authorized         = ($this->session->is_logged_in()) ? true : false;
			$data["authorized"] = $authorized;

			$title        = "Fordrive / online car enthusiasts community";
			$title       .= " for everything about cars and their owners";
			$description  = "A new social network for everything about cars.";
			$description .= " Share your car photos and events, rate others,";
			$description .= " read latest car news and share your opinion about them.";

			$this->page_title       = $title;
			$this->meta_description = $description;

			$this->view->content    = View::capture("main" . DS . "main", $data);
		}
	}
?>