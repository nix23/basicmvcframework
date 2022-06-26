<?php
	class Admin_Authorization_Controller extends Admin_Controller
	{
		public function before()
		{
			// Don't call parent before
		}
		
		public function index()
		{
			if($this->session->is_logged_in())
			{
				// Redirecting to default admin route
				Url::redirect($this->admin_panel_url . "/" . $this->config->default_backend_controller);
			}
			
			$this->render_layout = false;
			
			$this->view->layout = View::capture("base" . DS . "login", $data = array());
			
			View::render($this->view->layout);
		}
		
		public function login($request_type = "")
		{
			$this->is_ajax($request_type);
			$this->validate_token();
			
			$admin = new Admin_Model();
			
			$admin->bind($this->input->post('login'));
			$admin->validate(array('username', 'password'));
			
			$this->model_errors->ajaxify_if_has_errors();
			
			$admin = $admin->authenticate();
			if($admin)
			{
				$this->session->login($admin);
				
				$this->ajax->result   = 'ok';
				$this->ajax->callback = 'refresh';
			}
			else
			{
				$this->ajax->errors->login_error = 'Wrong username or password.';
			}
			
			$this->ajax->render();
		}
		
		public function logout($request_type = "")
		{
			$this->is_ajax($request_type);
			
			$this->session->logout();
			
			$this->ajax->result   = 'ok';
			$this->ajax->callback = 'refresh';
			
			$this->ajax->render();
		}
	}
?>