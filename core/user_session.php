<?php
	class User_Session
	{
		private $logged_in = false;
		public  $user_id;
		
		function __construct($start_session = true)
		{
			if($start_session) 
			{
				if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) 
					session_start();
			}
			$this->check_login();
		}
		
		// Returns if user is logged in
		public function is_logged_in()
		{
			return $this->logged_in;
		}
		
		// User authorization
		public function login(User_Model $user)
		{
			if(!$user instanceof User_Model)
			{
				exit("User_Session wrong class type(login method)");
			}
			
			$this->user_id   = $_SESSION['user_id'] = $user->id;
			$this->logged_in = true; 
		}
		
		// User logout
		public function logout()
		{
			unset($_SESSION['user_id']);
			unset($this->user_id);
			
			$this->logged_in = false;
		}
		
		// Check,if user is logged in
		private function check_login()
		{ 
			if(isset($_SESSION['user_id']))
			{
				$this->user_id   = $_SESSION['user_id'];
				$this->logged_in = true;
			}
			else
			{
				unset($this->user_id);
				$this->logged_in = false;
			}
		}
		
		// At next page message of successful save
		// will be displayed
		public function set_modal_show_confirmation()
		{
			$this->set("modal_show_confirmation", true);
		}
		
		public function is_modal_show_confirmation_set()
		{
			return ($this->is_set("modal_show_confirmation")) ? true : false;
		}
		
		// Deletes message of successful save
		public function unset_modal_show_confirmation()
		{
			$this->delete("modal_show_confirmation");
		}
		
		// Saving sizes of master photo
		public function set_master_photo_sizes($master_photo_name,
														 	$width,
														 	$height)
		{
			$_SESSION[$master_photo_name . "_master-photo-sizes"]["width"]  = $width;
			$_SESSION[$master_photo_name . "_master-photo-sizes"]["height"] = $height;
		}
		
		// Getting sizes of master photo
		public function get_master_photo_sizes($master_photo_name)
		{
			$sizes           = array();
			$sizes["width"]  = $_SESSION[$master_photo_name . "_master-photo-sizes"]["width"];
			$sizes["height"] = $_SESSION[$master_photo_name . "_master-photo-sizes"]["height"];
			
			unset($_SESSION[$master_photo_name . "_master-photo-sizes"]);
			return $sizes;
		}
		
		public function save_catalog_url_segments($current_controller,
																$url_segments)
		{
			$_SESSION["catalog_url_segments"]["$current_controller"] = $url_segments;
		}
		
		public function get_catalog_url_segments($controller)
		{
			if(isset($_SESSION["catalog_url_segments"]["$controller"]))
				return $_SESSION["catalog_url_segments"]["$controller"];
			else
				return false;
		}
		
		public function clear_all_catalog_url_segments_except($current_controller)
		{
			if(isset($_SESSION["catalog_url_segments"]))
			{
				foreach($_SESSION["catalog_url_segments"] as $controller => $url_segments)
				{
					if($controller != $current_controller)
						unset($_SESSION["catalog_url_segments"]["$controller"]);
				}
			}
		}
		
		// Saving element in session
		public function set($key, $value)
		{
			$_SESSION[$key] = $value;
		}
		
		// Deleting element from session
		public function delete($key)
		{
			unset($_SESSION[$key]);
		}
		
		public function is_set($key)
		{
			return (isset($_SESSION[$key])) ? true : false;
		}
		
		// Getting element from session
		public function get($key)
		{
			if(isset($_SESSION[$key]))
			{
				return $_SESSION[$key];
			}
			else
			{
				return false;
			}
		}
	}
?>