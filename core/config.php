<?php
	/** 
	* Class-wrapper for low-level settings
	* of application.
	**/
	class Config
	{
		private $settings = array();
		
		public function __construct()
		{
			// Loading settings into array
			$config_file    = ROOT . DS . 'config' . DS . 'config.ini.php';
			$this->settings = parse_ini_file($config_file);
		}
		
		// Getting setting from array
		public function __get($name)
		{
			if(isset($this->settings[$name]))
			{
				return $this->settings[$name];
			}
			else
			{
				exit("No setting '{$name}' in config file.");
			}
		}
	}
?>