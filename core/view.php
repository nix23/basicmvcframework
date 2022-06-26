<?php
	/** 
	* Class wrapper for templates. Allows load templates
	* from files(by pieces),also display templates
	* in browser
	**/
	class View
	{
		private static $base_path = "";
		
		private static function process_xss_elements_subset($data)
		{
			if(is_bool($data))
			{
				// Boolean values shouldn't be escaped
			}
			elseif(is_array($data))
			{
				foreach($data as &$array_value)
				{
					if(is_bool($array_value))
						; // Boolean values shouldn't be escaped
					elseif(is_array($array_value))
						$array_value = self::escape_xss($array_value);
					elseif(is_object($array_value))
						$array_value = self::escape_xss($array_value);
					else
						$array_value = htmlspecialchars($array_value, ENT_QUOTES, 'UTF-8');
				}
			}
			elseif(is_object($data))
			{
				$object_vars = get_object_vars($data);
				foreach($object_vars as $var_name => &$var_value)
				{
					if(is_bool($var_value))
						; // Boolean values shouldn't be escaped
					elseif(is_array($var_value))
						$data->$var_name = self::escape_xss($var_value);
					elseif(is_object($var_value))
						$data->$var_name = self::escape_xss($var_value);
					else
						$data->$var_name = htmlspecialchars($var_value, ENT_QUOTES, 'UTF-8');
				}
			}
			else
				$data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

			return $data;
		}
		
		private static function escape_xss($data 						      = array(),
													  $root_escape_key_exceptions = array())
		{
			foreach($data as $root_key => &$root_value)
			{
				if(!in_array($root_key, $root_escape_key_exceptions))
				{
					$root_value = self::process_xss_elements_subset($root_value);
				}
			}
			
			return $data;
		}
		
		/** 
		* Captures output in local scope,
		* which is generated,when template is including.
		* 
		* Usage: $output = View::capture($path, $data)
		**/
		public static function capture($view_path, 
												 $view_data,
												 $escape_xss                 = true,
												 $root_escape_key_exceptions = array())
		{
			if($escape_xss)
				$view_data = self::escape_xss($view_data,
														$root_escape_key_exceptions);
			
			// Imports vars in local scope
			extract($view_data);
			
			// Capturing template output
			ob_start();
			
			// Connecting template
			include(self::$base_path . $view_path . ".php");
			
			// Getting captured buffer
			// and returning output
			return ob_get_clean();
		}
		
		// Renders template in browser
		public static function render($template)
		{
			echo $template;
		}
		
		// Sets base path for templates
		public static function set_base_path($path = "")
		{
			self::$base_path = $path;
		}
	}
?>