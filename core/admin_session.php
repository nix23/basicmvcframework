<?php
	class Admin_Session
	{
		private $logged_in = false;
		public  $admin_id;
		
		function __construct($start_session = true)
		{
			if($start_session) session_start();
			$this->check_login();
		}
		
		// Returns if administrator is logged in
		public function is_logged_in()
		{
			return $this->logged_in;
		}
		
		// Administrator authorization
		public function login(Admin_Model $admin)
		{
			if(!$admin instanceof Admin_Model)
			{
				exit("Admin_Session wrong class type(login method)");
			}
			
			$this->admin_id = $_SESSION['admin_id'] = $admin->id;
			$this->logged_in = true; 
		}
		
		// Administrator logout
		public function logout()
		{
			unset($_SESSION['admin_id']);
			unset($this->admin_id);
			
			$this->logged_in = false;
		}
		
		// Check,if administrator is logged in
		private function check_login()
		{ 
			if(isset($_SESSION['admin_id']))
			{
				$this->admin_id  = $_SESSION['admin_id'];
				$this->logged_in = true;
			}
			else
			{
				unset($this->admin_id);
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