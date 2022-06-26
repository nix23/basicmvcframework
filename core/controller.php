<?php
	/** 
	* Base class of controller.
	* All application controllers should extend it.
	**/
	abstract class Controller
	{
		/** 
		* Contains all templates,which are
		* compiled through View::capture.
		**/
		protected $view;
		protected $input;
		protected $session;
		protected $model_errors;
		protected $config;
		protected $settings;
		
		// Contains server answer of ajax requests
		protected $ajax;
		
		// At ajax requests we don't need to display base template
		protected $render_layout = true;

		protected $routedController;
		protected $routedAction;
		
		public function __construct()
		{
			// View can be accessed with ->
			$this->view = new stdClass;
			
			$this->input  = Registry::get('input');
			$this->config = Registry::get('config');
			
			// Contains all errors of models validation
			Registry::set('ajax',         new Ajax);
			Registry::set('model_errors', new Model_Errors);
			
			$this->model_errors = Registry::get('model_errors');
			$this->ajax         = Registry::get('ajax');

			$settings_model = new Settings_Model;
			Registry::set('settings', $settings_model->find_settings_by_name("main_settings"));
			$this->settings = Registry::get('settings');
		}
		
		public function setRouterData($controller, $action)
		{
			$this->routedController = $controller;
			$this->routedAction = $action;
		}

		/**
		 * Executes automatically before firing controllers action.
		 * It can be overrided in child objects to make some
		 * useful actions.(Like authorization checking)
		 */
		public function before()
		{
			// Empty by default
		}
		
		/**
		 *  Executes automatically after controllers action
		 *  is complete.
		 */
		public function after()
		{
			// Empty by default
		}
		
		/** 
		* Anti-csrf token validation
		**/
		protected function validate_token()
		{
			$is_valid_token = false;
			
			$token = $this->input->post('token', 'not_set');
			
			if($token != 'not_set')
			{
				if(Form::verify_form_token($token['name'], $token['value']))
				{
					$is_valid_token = true;
				}
			}
			
			return ($is_valid_token) ? true : false; 
		}

		protected function get_ip()
		{
			return $_SERVER["REMOTE_ADDR"];
		}
	}
?>