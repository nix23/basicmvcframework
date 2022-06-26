<?php
	/** 
	* Backend controller base class.
	* All backend controllers should extend this class.
	**/
	abstract class Admin_Controller extends Controller
	{
		protected $admin_panel_url;
		
		public function __construct()
		{
			parent::__construct(); 
			
			Registry::set('session', new Admin_Session);
			$this->session = Registry::get('session');

			View::set_base_path(VIEWS . "admin" . DS);
			$this->admin_panel_url = $this->config->admin_panel_url;
		}

		private function find_ajax_directory_files_count()
		{
			$files_count = 0;

			if($directory_handle = opendir(UPLOADS_AJAX))
			{
				while(false !== ($filename = readdir($directory_handle)))
				{
					if($filename != "." and $filename != "..")
						$files_count++;
				}

				closedir($directory_handle);
			}

			return $files_count;
		}

		private function find_cache_directory_files_count()
		{
			$files_count = 0;

			if($directory_handle = opendir(UPLOADS_CACHE))
			{
				while(false !== ($filename = readdir($directory_handle)))
				{
					if($filename != "." and $filename != "..")
						$files_count++;
				}

				closedir($directory_handle);
			}

			return $files_count;
		}

		private function find_unactivated_users_count_more_than_in_n_days($days = 3)
		{
			$user_model              = new User_Model;
			$unactivated_users_count = $user_model->find_unactivated_users_count_more_than_in_n_days($days);

			return $unactivated_users_count;
		}

		public function before()
		{
			if(!$this->session->is_logged_in())
			{
				Url::redirect($this->admin_panel_url . "/login");
			}

			// *** Capturing settings
			$data                                = array();
			$user_model                          = new User_Model;
			$data["registred_users_today_count"] = $user_model->get_registred_users_count(false, 1);
			$data["activated_users_today_count"] = $user_model->get_registred_users_count(true,  1);
			$data["settings"]                    = $this->settings;
			$this->view->settings                = View::capture("base" . DS . "settings", $data);
		}
		
		public function after()
		{
			if($this->render_layout)
			{
				/** 
				* Here we need to get controller's name to display administrator
				* menu correctly.(selected item) But instance of router is not
				* available in this place,so we need to cut url again. In this
				* method url contains at least admin_url_path,otherwise frontend
				* would be loaded.
				**/
				$url = $this->input->get('url');
				
				$url_segments = explode("/", $url);
				
				if(isset($url_segments[1]) && !empty($url_segments[1]))
				{
					$data['current_url'] = $url_segments[1];
				}
				else
				{
					$data['current_url'] = $this->config->default_backend_controller;
				}
				
				// url - controller name, label - name of menu item
				$data['menu_items'] = array(
					(object) array("url" => "dashboard",  "label" => "Dashboard"),
					(object) array("url" => "categories", "label" => "Categories"),
					(object) array("url" => "photos",     "label" => "Photos"),
					(object) array("url" => "spots",      "label" => "Spots"),
					(object) array("url" => "speed",      "label" => "Speed"),
					(object) array("url" => "videos",     "label" => "Videos"),
					(object) array("url" => "users",      "label" => "Users")
				); 
				
				$this->view->header = View::capture("base" . DS . "header", $data);

				// *** Capturing layout
				$data = array();
				$data['header']                      = $this->view->header;
				$data['content']                     = $this->view->content;
				$data["ajax_directory_files_count"]  = $this->find_ajax_directory_files_count();
				$data["cache_directory_files_count"] = $this->find_cache_directory_files_count();
				$data["unactivated_users_count"]     = $this->find_unactivated_users_count_more_than_in_n_days(3);
				
				$this->view->layout = View::capture("base" . DS . "layout", $data, false);
				
				View::render($this->view->layout);
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