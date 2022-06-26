<?php
	class Videos_Controller extends Public_Controller
	{
		public function index($page        = 1, 
									 $sort        = "postdate-desc", 
									 $category_id = false)
		{
			$category_model = new Category_Model;
			
			if($category_id)
			{
				if(!$category_model->find_selected_categories($category_id,
																			 true,
																			 "Video_Model"))
				{
					$error = new Error_Controller;
					$error->show_404();
				}
			}
			
			list($order_by, $direction) = explode("-", $sort);
			
			$sort_items = array(
				(object) array("type" => "postdate", "label" => "post date", "direction" => "desc", "selected" => false),
				(object) array("type" => "views",    "label" => "views",     "direction" => "desc", "selected" => false),
				(object) array("type" => "activity", "label" => "activity",  "direction" => "desc", "selected" => false)
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
			
			$video_model = new Video_Model();
			$order_by    = str_replace("postdate", "posted_on",   $order_by);
			$order_by    = str_replace("views",    "views_count", $order_by);
			$direction   = mb_strtoupper($direction, "utf-8");
			$videos      = $video_model->find_all_videos($category_model->in_categories,
																		$page,
																		$order_by,
																		$direction,
																		true,
																		true,
																		true);
			
			foreach($videos as $video)
			{
				$video->find_category_and_subcategory();
				$video->find_main_photo();
			}
			
			$authorized = ($this->session->is_logged_in()) ? true : false;
			
			$this->save_catalog_url_segments_in_session($category_model->selected_category,
																	  $category_model->selected_subcategory,
																	  $page,
																	  $sort);
			
			$data['videos']               = $videos;
			$data['pages']                = $video_model->pagination->make_pages("compact");
			$data['current_page']         = $page;
			$data['categories']           = $category_model->get_not_empty_root_categories_by_module("videos", "Video_Model");
			$data['selected_category']    = $category_model->selected_category;
			$data['selected_subcategory'] = $category_model->selected_subcategory;
			$data['sort_items']           = $sort_items;
			$data['selected_sort']        = $sort;
			$data['authorized']           = $authorized;

			if($category_model->selected_category)
			{
				if($category_model->selected_subcategory)
				{
					$title_content  = $category_model->selected_category->name;
					$title_content .= " ";
					$title_content .= $category_model->selected_subcategory->name;
				}
				else
				{
					$title_content = $category_model->selected_category->name;
				}
			}
			else
			{
				$title_content = "Latest auto events";
			}

			$title        = "Videos / $title_content / Fordrive";
			$description  = "Latest auto news on fordrive. ";
			$description .= "Newest cars, car spots, user cars, supercars and more.";

			$this->page_title       = $title;
			$this->meta_description = $description;
			
			$this->view->content = View::capture("videos" . DS . "videos_list", $data);
		}
		
		public function view($category_id = false,
									$video_id    = false)
		{
			$category_model = new Category_Model;
			
			if(!$category_id 
					or
				!$category_model->find_selected_categories($category_id,
																		 true,
																		 "Video_Model"))
			{
				$error = new Error_Controller;
				$error->show_404();
			}
			
			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_id);
			
			if(!$video)
			{
				$error = new Error_Controller;
				$error->show_404();
			}

			$catalog_backlink_segments = $this->session->get_catalog_url_segments($this->current_controller);
			$catalog_backlink          = $this->current_controller . "/list";

			$catalog_backlink_segments_parts = explode("/", $catalog_backlink_segments);
			$current_sort = $catalog_backlink_segments_parts[count($catalog_backlink_segments_parts) - 1]; 
			if(strlen($current_sort) == 0)
				$current_sort = "postdate-desc";
			else
			{
				$current_sort_parts = explode("-", $current_sort);
				$current_sort_parts = array_splice($current_sort_parts, 1);
				$current_sort = implode("-", $current_sort_parts);
			}

			if($catalog_backlink_segments)
				$catalog_backlink .= $catalog_backlink_segments;

			$data['catalog_backlink'] = $catalog_backlink;

			$item_is_available = true;
			if(!$this->admin_session->is_logged_in())
			{
				if(!$video->is_authorized_user_video_author())
				{
					if(!$video->is_enabled()
							or
						!$video->is_moderated())
					{
						$this->view->content = View::capture("videos" . DS . "unavailable_item", $data);
						$item_is_available   = false;
					}
				}
			}

			if($item_is_available)
			{
				$video->find_category_and_subcategory();
				$video->find_main_photo();
				$video->find_attached_photos();
				$video->find_attached_comments_on(1);
				$video->find_comments_count();
				$video->find_author();
				$video->find_likes_count();
				$video->find_favorites_count();
				$video->find_author_followers_count();
				$video->update_views_count($this->get_ip());
				$video->find_views_count();
				if($this->session->is_logged_in())
				{
					$video->find_if_is_logged_user_post_author($this->session->user_id);
					$video->find_if_is_liked_by_logged_user($this->session->user_id);
					$video->find_if_author_is_followed_by_logged_user($this->session->user_id);
					$video->find_if_is_favorite_of_logged_user($this->session->user_id);
				}

				$video->main_photo->unpack_directory();
				foreach($video->photos as $photo)
				{
					$photo->unpack_directory();
				}
				foreach($video->comments as $comment)
				{
					$comment->find_author();
					$comment->find_answers();

					foreach($comment->answers as $answer)
					{
						$answer->find_author();
					}
				}

				$authorized       = ($this->session->is_logged_in()) ? true : false;
				$admin_authorized = ($this->admin_session->is_logged_in()) ? true : false;

				$data['video']                 = $video;
				$data['categories']            = $category_model->get_not_empty_root_categories_by_module("videos", "Video_Model");
				$data['selected_category']     = $category_model->selected_category;
				$data['selected_subcategory']  = $category_model->selected_subcategory;
				$data['comments_pages']        = $video->comments_model->pagination->make_pages("compact");
				$data['comments_current_page'] = 1;
				$data['authorized']            = $authorized;
				$data['admin_authorized']      = $admin_authorized;
				$data['current_sort']          = $current_sort;

				$this->page_title       = $video->heading;
				$this->meta_description = $video->short_description;

				$this->view->content = View::capture("videos" . DS . "view_video", $data);
			}
		}
		
		public function add_like($request_type = "",
										 $video_id     = false)
		{
			$this->show_404_if_not_authorized();
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_id);
			
			if(!$video)
			{
				$this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
				$this->ajax->render();
			}
			else if(!$video->is_enabled())
			{
				$this->ajax->errors->item_is_disabled = ERROR_ITEM_IS_DISABLED_AJAX;
				$this->ajax->render();
			}
			else if(!$video->is_moderated())
			{
				$this->ajax->errors->item_is_moderated = ERROR_ITEM_IS_MODERATED_AJAX;
				$this->ajax->render();
			}
			
			$video->find_if_is_liked_by_logged_user($this->session->user_id);
			if($video->is_liked_by_logged_user)
			{
				$this->ajax->errors->item_is_already_liked = ERROR_ITEM_IS_ALREADY_LIKED_AJAX;
				$this->ajax->render();
			}
			
			$video_like_model           = new Video_Like_Model;
			$video_like_model->video_id = $video->id;
			$video_like_model->user_id  = $this->session->user_id;
			
			if($video_like_model->save())
			{
				$this->ajax->result   = "ok";
				$this->ajax->callback = "add_like";
				
				$this->ajax->render(); 
			}
		}
		
		public function change_follow($request_type = "",
												$followed_id  = false)
		{
			$this->show_404_if_not_authorized();
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$user_model     = new User_Model;
			$user_to_follow = $user_model->find_by_id($followed_id);
			
			if(!$user_to_follow)
			{
				$this->ajax->errors->user_was_deleted = ERROR_USER_WAS_DELETED;
				$this->ajax->render();
			}
			
			$follower_model = new Follower_Model;
			$follower 		 = $follower_model->find_followed_user_by_follower($user_to_follow->id,
																									$this->session->user_id);
			if($follower)
			{
				if($follower->delete())
				{
					$this->ajax->data->new_caption  = "Follow";
					$this->ajax->data->count_action = "decrease";
				}
			}
			else
			{
				$follower_model->followed_id = $user_to_follow->id;
				$follower_model->follower_id = $this->session->user_id;
				
				if($follower_model->save())
				{
					$this->ajax->data->new_caption  = "Unfollow";
					$this->ajax->data->count_action = "increase";
				}
			}
			
			$this->ajax->result   = "ok";
			$this->ajax->callback = "change_panel_list_item";
			
			$this->ajax->render();
		}
		
		public function change_favorite($request_type = "",
												  $video_id     = false)
		{
			$this->show_404_if_not_authorized();
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_id);
			
			if(!$video)
			{
				$this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
				$this->ajax->render();
			}
			else if(!$video->is_enabled())
			{
				$this->ajax->errors->item_is_disabled = ERROR_ITEM_IS_DISABLED_AJAX;
				$this->ajax->render();
			}
			else if(!$video->is_moderated())
			{
				$this->ajax->errors->item_is_moderated = ERROR_ITEM_IS_MODERATED_AJAX;
				$this->ajax->render();
			}
			
			$favorite_model = new Favorite_Model;
			$favorite       = $favorite_model->find_favorite($video->id,
																			 $this->session->user_id,
																			 "videos");
			
			if($favorite)
			{
				if($favorite->delete())
				{
					$this->ajax->data->new_caption  = "Favorite";
					$this->ajax->data->count_action = "decrease";
				}
			}
			else
			{
				$favorite_model->item_id = $video->id;
				$favorite_model->user_id = $this->session->user_id;
				$favorite_model->module  = "videos";
				
				if($favorite_model->save())
				{
					$this->ajax->data->new_caption  = "Unfavorite";
					$this->ajax->data->count_action = "increase";
				}
			}
			
			$this->ajax->result   = "ok";
			$this->ajax->callback = "change_panel_list_item";
			
			$this->ajax->render();
		}
		
		public function add_comment($request_type = "")
		{
			$this->show_404_if_not_authorized();
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$video_comment_model = new Video_Comment_Model;
			
			$video_comment_model->bind($this->input->post("comment"));
			$video_comment_model->user_id = $this->session->user_id;

			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_comment_model->video_id);

			if(!$video)
			{
				$this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
				$this->ajax->render();
			}
			else if(!$video->is_enabled() and !$video->is_authorized_user_video_author())
			{
				$this->ajax->errors->item_is_disabled = ERROR_ITEM_IS_DISABLED_AJAX;
				$this->ajax->render();
			}
			else if(!$video->is_moderated() and !$video->is_authorized_user_video_author())
			{
				$this->ajax->errors->item_is_moderated = ERROR_ITEM_IS_MODERATED_AJAX;
				$this->ajax->render();
			}

			$video_comment_model->validate();

			if(!$this->verify_auth_key())
				$this->model_errors->set("invalid_authkey",
												 "Wrong form token, please try refresh the page.");

			if($this->session->is_set("last_comment_time"))
			{
				if((time() - $this->session->get("last_comment_time")) < MIN_SECONDS_BETWEEN_COMMENTS)
					$this->model_errors->set("too_fast_new_comment",
													 "Please wait " . MIN_SECONDS_BETWEEN_COMMENTS . "seconds before posting new comment.");
			}

			$this->model_errors->ajaxify_if_has_errors();
			
			if($video_comment_model->save())
			{
				$this->session->set("last_comment_time", time());

				// If new comment,opening first page
				if($video_comment_model->answer_id == "0")
				{
					$current_page = 1;
				}
				// If answer,opening selected page
				else
				{
					$current_page_array = $this->input->post("current_page");
					$current_page       = $current_page_array["number"];
				}
				
				// Fetching comments
				$video->find_attached_comments_on($current_page);
				$video->find_comments_count();
				
				foreach($video->comments as $comment)
				{
					$comment->find_author();
					$comment->find_answers();
					
					foreach($comment->answers as $answer)
					{
						$answer->find_author();
					}
				}
				
				// Capturing comments
				$data["authorized"]                    = ($this->session->is_logged_in()) ? true : false;
				$data["video"]                         = $video;
				$data["admin_authorized"]              = ($this->admin_session->is_logged_in()) ? true : false;
				$comments_path                         = "videos" . DS . "view_video_comments_items";
				$this->ajax->data->comments_items_html = View::capture($comments_path, $data);
				
				// Capturing pagination
				$data                                       = array();
				$data["video"]                              = $video;
				$data["comments_pages"]                     = $video->comments_model->pagination->make_pages("compact");
				$data["comments_current_page"]              = $current_page;
				$pagination_path                            = "videos" . DS . "view_video_comments_pagination";
				$this->ajax->data->comments_pagination_html = View::capture($pagination_path, $data);
				
				// Building response
				$this->ajax->data->comment_to_scroll_id = $video_comment_model->id;
				$this->ajax->data->comments_count       = $video->comments_total_count;
				$this->ajax->data->current_page         = $current_page;
				$this->ajax->data->callback             = "hide_newcomment_form";
				$this->ajax->result                     = 'ok';
				$this->ajax->callback                   = 'refresh_comments';
				
				$this->ajax->render();
			}
		}

		public function delete_comment($request_type = "",
												 $comment_id   = false)
		{
			if(!$this->admin_session->is_logged_in())
			{
				$error_controller = new Error_Controller;
				$error_controller->show_404();
			}

			$this->is_ajax($request_type);
			$this->validate_token();

			$video_comment_model = new Video_Comment_Model;
			$video_comment       = $video_comment_model->find_by_id($comment_id);

			if(!$video_comment)
				exit("Error: can't find comment with such id");

			if(!$video_comment->delete_with_all_answers())
				exit("Error: can't delete comment now");

			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_comment->video_id);

			$current_page_array = $this->input->post("current_page");
			$current_page       = $current_page_array["number"];

			// Fetching comments
			$video->find_attached_comments_on($current_page, false);

			// If no comments left on current page and it isn't first page,
			// loading previous page comments
			if(!$video->comments and $current_page != 1)
			{
				$video->find_attached_comments_on(--$current_page);
			}

			$video->find_comments_count();

			foreach($video->comments as $comment)
			{
				$comment->find_author();
				$comment->find_answers();

				foreach($comment->answers as $answer)
				{
					$answer->find_author();
				}
			}

			// Capturing comments
			$data["authorized"]                    = ($this->session->is_logged_in()) ? true : false;
			$data["admin_authorized"]              = ($this->admin_session->is_logged_in()) ? true : false;
			$data["video"]                         = $video;
			$comments_path                         = "videos" . DS . "view_video_comments_items";
			$this->ajax->data->comments_items_html = View::capture($comments_path, $data);

			// Capturing pagination
			$data                                       = array();
			$data["video"]                              = $video;
			$data["comments_pages"]                     = $video->comments_model->pagination->make_pages("compact");
			$data["comments_current_page"]              = $current_page;
			$pagination_path                            = "videos" . DS . "view_video_comments_pagination";
			$this->ajax->data->comments_pagination_html = View::capture($pagination_path, $data);

			// Building response
			$this->ajax->data->comments_count       = $video->comments_total_count;
			$this->ajax->data->current_page         = $current_page;
			$this->ajax->result                     = 'ok';
			$this->ajax->callback                   = 'refresh_comments_after_delete';

			$this->ajax->render();
		}

		public function delete_answer($request_type = "",
												$answer_id    = false)
		{
			if(!$this->admin_session->is_logged_in())
			{
				$error_controller = new Error_Controller;
				$error_controller->show_404();
			}

			$this->is_ajax($request_type);
			$this->validate_token();

			$video_comment_model = new Video_Comment_Model;
			$video_comment       = $video_comment_model->find_by_id($answer_id);

			if(!$video_comment)
				exit("Error: can't find comment with such id");

			if(!$video_comment->delete())
				exit("Error: can't delete comment now");

			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_comment->video_id);

			$video->find_comments_count();

			// Building response
			$this->ajax->data->comments_count       = $video->comments_total_count;
			$this->ajax->result                     = 'ok';
			$this->ajax->callback                   = 'delete_answer';

			$this->ajax->render();
		}

		public function load_comments($request_type = "",
												$video_id     = false,
												$page         = 1)
		{
			$this->is_ajax($request_type);
			
			$video_model = new Video_Model;
			$video       = $video_model->find_by_id($video_id);

			if(!$video)
			{
				$this->ajax->errors->item_not_found = ERROR_ITEM_NOT_FOUND_AJAX;
				$this->ajax->render();
			}

			// Fetching comments
			$video->find_attached_comments_on($page);
			$video->find_comments_count();
			
			foreach($video->comments as $comment)
			{
				$comment->find_author();
				$comment->find_answers();
				
				foreach($comment->answers as $answer)
				{
					$answer->find_author();
				}
			}
			
			// Capturing comments
			$data["authorized"]                    = ($this->session->is_logged_in()) ? true : false;
			$data["video"]                         = $video;
			$data["admin_authorized"]              = ($this->admin_session->is_logged_in()) ? true : false;
			$comments_path                         = "videos" . DS . "view_video_comments_items";
			$this->ajax->data->comments_items_html = View::capture($comments_path, $data);
			
			// Capturing pagination
			$data                                       = array();
			$data["video"]                              = $video;
			$data["comments_pages"]                     = $video->comments_model->pagination->make_pages("compact");
			$data["comments_current_page"]              = $page;
			$pagination_path                            = "videos" . DS . "view_video_comments_pagination";
			$this->ajax->data->comments_pagination_html = View::capture($pagination_path, $data);
			
			// Building response
			$this->ajax->data->comment_to_scroll_id = "heading";
			$this->ajax->data->comments_count       = $video->comments_total_count;
			$this->ajax->data->current_page         = $page;
			$this->ajax->data->callback             = "";
			$this->ajax->result                     = 'ok';
			$this->ajax->callback                   = 'refresh_comments';
			
			$this->ajax->render();
		}
		
		public function form($id          = false,
									$category_id = false)
		{
			$this->show_404_if_not_authorized();
			
			// Capturing module root categories
			$category_model     = new Category_Model;
			$data['categories'] = $category_model->get_categories_by_module(0, "videos");
			
			// Restore sorting and categories,if we opened form from catalog page
			$catalog_backlink_segments = $this->session->get_catalog_url_segments($this->current_controller);
			$catalog_backlink          = $this->current_controller . "/list";
			
			if($catalog_backlink_segments)
				$catalog_backlink .= $catalog_backlink_segments;
			
			$data['catalog_backlink'] = $catalog_backlink;
			
			$video_model = new Video_Model;
			
			// Editing video
			if($id and $id != "add")
			{
				$video = $video_model->find_by_id($id);
				
				if($video)
				{
					$data['video']      = $video;
					$data['action']     = "Edit";
					$data['is_editing'] = true;
					
					$video->find_attached_photos();
					$video->find_main_photo();
					
					if($video->photos)
					{
						$video->photos = array_reverse($video->photos);
						
						foreach($video->photos as $photo)
						{
							$photo->unpack_directory();
						}
					}
					
					$video->find_category_and_subcategory();
					
					if($video->subcategory)
					{
						$video->category->find_subcategories();
					}
				}
				else
				{
					$error = new Error_Controller;
					$error->show_404();
				}
			}
			// New video
			else
			{
				// If some categories were opened,we need
				// open them on form at start.
				if($category_id)
				{
					$video_model->category_id = $category_id;
					$video_model->find_category_and_subcategory();
					
					if($video_model->category->has_subcategories())
					{
						$video_model->category->find_subcategories();
						
						// If only root category is opened,we need
						// to open blank subcategory
						if(!$video_model->subcategory)
						{
							$video_model->subcategory = new Category_Model;
							$video_model->category_id = "";
						}
					}
				}
				
				$data['video']      = $video_model;
				$data['action']     = "Add new";
				$data['is_editing'] = false;
			}
			
			// Inserting user id
			$data['video']->user_id = $this->session->user_id;

			$this->page_title    = "Add video / Fordrive";
			$this->view->content = View::capture("videos" . DS . "video_form", $data);
		}
		
		public function load_subcategories( $request_type = "",
														$id = false)
		{ 
			$this->is_ajax($request_type);
			
			if($id)
			{
				$category_model = new Category_Model; 
				$category       = $category_model->find_by_id($id); 
				
				if($category)
				{ 
					$category->find_subcategories(); 
					$subcategories = array();
					
					if($category->subcategories)
					{
						foreach($category->subcategories as $subcategory)
						{
							$subcategories[] = array($subcategory->id, $subcategory->name);
						}
					} 
					
					$this->ajax->result              = "ok";
					$this->ajax->callback            = "parse_select_subcategories";
					$this->ajax->data->subcategories = $subcategories;
					
					$this->ajax->render(); 
				}
			}
		}
		
		public function upload_photo($request_type = "")
		{
			$this->show_404_if_not_authorized();
			$this->is_ajax($request_type);
			$image_uploader = new Fordrive_Uploader(800, 600);
			
			if(!$image_uploader->is_file_uploaded('upload-file')) 
			{ 
				$this->ajax->errors->file_upload_error = $image_uploader->error; 
			}
			else
			{
				if($image_uploader->attach_file($_FILES['upload-file']))
				{
					$video_photo_model = new Video_Photo_Model;
					$clones            = $video_photo_model->clones;
					
					if($image_uploader->upload_photo($clones))
					{
						$this->ajax->result                   = "ok";
						$this->ajax->callback                 = "insert_uploaded_photo";
						$this->ajax->data->master_photo_name  = $image_uploader->master_photo_name;
						$this->ajax->data->photo_extension    = "jpg";
						$this->ajax->data->spinner_id         = "photo-upload-spinner";
					}
					else
					{
						$this->ajax->errors->file_upload_error = $image_uploader->error;
					}
				}
				else
				{
					$this->ajax->errors->file_upload_error = $image_uploader->error;
				}
			}
			
			$this->ajax->render();
		}
		
		public function save($request_type = "")
		{
			$this->show_404_if_not_authorized();
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$video = new Video_Model;
			
			$video->bind($this->input->post("video"));

			if($video->is_record_updating())
			{
				if(!$video->is_record_updating_by_owner($this->session->user_id,
																	 $video->user_id))
					$this->model_errors->set("wrong_ownership", "You are not owner of this record.");
			}

			$video->validate(array(), "get_public_validation_rules");
			
			// Check,that user id equals session user id
			if($video->user_id != $this->session->user_id)
				$this->model_errors->set("user_required", "Wrong user login. Try refresh your form.");
			
			// Checking at least one photo exists
			$photos = $this->input->post("video-photos", "no-photos");
			$uploaded_photos_count = 0;
			
			if($photos == "no-photos")
			{
				$this->model_errors->set("photo_required", "Please add at least one photo.");
			}
			else
			{
				$no_photos = true;
				foreach($photos as $photo)
				{
					// Frames 'delete','deleteajax' will delete photo
					if(!preg_match("~^delete|deleteajax~u", $photo["frame"]))
					{
						$no_photos = false;
						$uploaded_photos_count++;
					}
				}
				
				if($no_photos)
				{
					$this->model_errors->set("photo_required", "Please add at least one photo.");
				}
			}
			
			$video->validate_article_tags($uploaded_photos_count,
													"html");
			$this->model_errors->ajaxify_if_has_errors();

			if($video->is_moderation_failed())
			{
				$video->moderation_fail_text = "";
			}

			if($video->save(true))
			{
				// Processing photo frames
				foreach($photos as $photo)
				{
					$video_photo = new Video_Photo_Model;
					
					$video_photo->unpack_frame($photo["frame"]);
					$video_photo->bind_id($video, "video_id"); 
					
					switch($video_photo->frame_action)
					{
						case "ajax":
							$video_photo->save_with_clones();
						break;
						
						case "deleteajax":
							$video_photo->delete_ajax();
						break;
						
						case "delete":
							$video_photo->delete_with_clones();
						break;
						
						case "none":
							// No frame processing required
						break;
					}
				}
				
				// Updating main photo
				$video_photo = new Video_Photo_Model;
				
				$video_photo->bind($this->input->post("main_photo"));
				$video_photo->bind_id($video, "video_id");
				
				$video_photo->update_main();
				
				// Save succesfull
				$this->session->set_modal_show_confirmation();
				
				// Building response
				$this->ajax->data->url_segments = 'drive/list/videos/page-1';
				$this->ajax->result             = 'ok';
				$this->ajax->callback           = 'redirect';
				
				$this->ajax->render();
			}
		}
	}
?>