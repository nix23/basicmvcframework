<?php
	/** 
	* Frontend controller base class.
	* All frontend controllers should extend this class.
	**/
	abstract class Public_Controller extends Controller
	{
		protected $current_controller;
		protected $currrent_action;
		protected $admin_session;
		protected $logged_user;
		protected $page_title       = "";
		protected $meta_description = "";
		
		public function __construct()
		{
			parent::__construct();
			
			// Creating user session
			Registry::set('session', new User_Session);
			$this->session = Registry::get('session');

			// Creating admin session
			Registry::set('admin_session', new Admin_Session(false));
			$this->admin_session = Registry::get('admin_session');
			
			// Setting frontend templates base path
			View::set_base_path(VIEWS);
		}
		
		public function before()
		{
			$this->settings->move_item_views_to_module_stats();
			$this->get_current_controller();

			if($this->session->is_logged_in())
			{
				$user_model        = new User_Model;
				$this->logged_user = $user_model->find_by_id($this->session->user_id);

				// If user not found,he was deleted -> dropping session
				if(!$this->logged_user)
				{
					$this->session->logout();
					Url::redirect("main");
				}
			}
		}
		
		private function get_current_controller()
		{
			// Getting current controller name to catch
			// selected menu item
			$url = $this->input->get("url", "not_set");
			
			if($url != "not_set")
			{
				$url_segments = explode("/", $url);
				
				if(isset($url_segments[0]) && !empty($url_segments[0]))
				{
					$this->current_controller = $url_segments[0];
				}
				else
				{
					$this->current_controller = $this->config->default_frontend_controller;
				}

				if(isset($url_segments[1]) && !empty($url_segments[1]))
				{
					$this->current_action = $url_segments[1];
				}
				else
				{
					$this->current_action = $this->config->default_frontend_action;
				}
			}
			else
			{
				$this->current_controller = $this->config->default_frontend_controller;
			}
		}
		
		private function capture_header()
		{
			$data['current_controller'] = $this->current_controller;
			
			// If user is authorized,we are showing
			// user panel header
			if($this->session->is_logged_in())
			{
				$data['menu_items_first_batch'] = array(
					(object) array("controller" => "drive",    "label" => "Drive"),
					(object) array("controller" => "activity", "label" => "Activity"),
					(object) array("controller" => "follow",   "label" => "Following")
				);
				
				$data['menu_items_second_batch'] = array(
					(object) array("controller" => "favorites", "label" => "Favorites")
				);
				
				$this->view->header = View::capture("base" . DS . "header_authorized", $data);
			}
			// Else we are showing standart header
			else
			{
				$this->view->header = View::capture("base" . DS . "header", $data);
			}
		}
		
		private function capture_main_menu()
		{
			$data['current_controller'] = $this->current_controller;
			$data['menu_items']         = array(
				(object) array("controller" => "main",   "label" => "Main",   "sublabel" => "Newest drives"),
				(object) array("controller" => "photos", "label" => "Photos", "sublabel" => "Newest cars"),
				(object) array("controller" => "spots",  "label" => "Spots",  "sublabel" => "User car spots"),
				(object) array("controller" => "speed",  "label" => "Speed",  "sublabel" => "Latest auto news"),
				(object) array("controller" => "videos", "label" => "Videos", "sublabel" => "Latest auto events")
			);
			
			$this->view->main_menu = View::capture("base" . DS . "main_menu", $data);
		}
		
		protected function save_catalog_url_segments_in_session($category    = false,
																				  $subcategory = false,
																				  $page,
																				  $sort)
		{
			$url_segments = "";
			
			if($category)
			{
				$url_segments .= "/";
				$url_segments .= Url::create_slug($category->name);
				$url_segments .= "-";
				
				if($subcategory)
				{
					$url_segments .= Url::create_slug($subcategory->name);
					$url_segments .= "-";
					$url_segments .= $subcategory->id;
				}
				else
				{
					$url_segments .= $category->id;
				}
			}
			
			$url_segments .= "/page-{$page}/sort-{$sort}";
			$this->session->save_catalog_url_segments($this->current_controller,
																	$url_segments);
		}
		
		public function after()
		{
			if($this->render_layout)
			{
				$this->session->clear_all_catalog_url_segments_except($this->current_controller);
				$this->capture_header();
				$this->capture_main_menu();
				$this->get_current_controller(); 
				
				// Capturing and rendering main template(layout)
				$data['main_menu']        = $this->view->main_menu;
				$data['header']           = $this->view->header;

				if($this->session->is_logged_in())
				{
					if($this->logged_user->is_account_blocked())
					{
						$data['message']       = "Your account is blocked.";
						$data['support_email'] = $this->settings->support_email;
						$this->view->content = View::capture("profiles" . DS . "unavailable_account", $data);
					}
				}

				$data['content']          = $this->view->content;
				$data['authorized']       = ($this->session->is_logged_in()) ? true : false;
				$data['page_title']       = $this->page_title;
				$data['meta_description'] = $this->meta_description;
				$data['current_controller'] = $this->routedController;
				$data['current_action'] = $this->routedAction;
				
				$this->view->layout = View::capture("base" . DS . "layout", $data, false);
				View::render($this->view->layout);
			}
		}
		
		// Show 404 Not Found page,if user isn't logged in
		protected function show_404_if_not_authorized()
		{
			if(!$this->session->is_logged_in())
			{
				$error = new Error_Controller;
				$error->show_404();
			}
		}
		
		protected function is_ajax($request_type)
		{
			if($request_type != "ajax")
			{
				$error = new Error_Controller;
				$error->show_404();
			}
			
			$this->render_layout = false;
		}

		public function init_auth($request_type = "", $callback_type)
		{
			$this->is_ajax($request_type);
			//$this->validate_token();

			$auth_key = sha1(rand(1, 100000) . "povar");
			$this->session->set("auth_key", $auth_key);

			$this->ajax->result = "ok";

			if($callback_type == "init_registration")
				$this->ajax->callback = "init_registration";
			else if($callback_type == "init_login")
				$this->ajax->callback = "init_login";
			else if($callback_type == "init_comment")
				$this->ajax->callback = "init_comment";

			$this->ajax->data->auth_key = $auth_key;

			$this->ajax->render();
		}

		protected function verify_auth_key()
		{
			$auth_form_key = $this->input->post("auth_key", "not_set");

			if($auth_form_key == "not_set")
				return false;

			if(!$this->session->is_set("auth_key"))
				return false;

			$session_auth_key = $this->session->get("auth_key");

			if(empty($session_auth_key))
				return false;

			if($session_auth_key == $auth_form_key["key"])
				return true;
			else
				return false;
		}
		
		protected function validate_token()
		{
			$is_valid_token = parent::validate_token();
			
			if(!$is_valid_token)
			{
				$this->ajax->errors->token = "Wrong form token. Please refresh form.";
				$this->ajax->render();
			}
		}
	}
?>