<?php
	/** 
	* Loads model and controller classes,
	* also library classes
	**/
	class Loader 
	{
		public static function load_class($class_name) 
		{
			$class_name_segments = explode("_", $class_name);
			
			// Getting last element(type of class)
			$class_type = $class_name_segments[count($class_name_segments)-1];
			
			// Deleting last element (type of class)
			array_pop($class_name_segments);
			
			if($class_type == "Controller")
			{
				self::load_controller($class_name, $class_name_segments);
			}
			else if($class_type == "Model")
			{
				self::load_model($class_name, $class_name_segments);
			}
			else if($class_type == "Mapper")
			{
				self::load_mapper($class_name, $class_name_segments);
			}
			else
			{ 
				exit("Can't load class {$class_name}");
			}
		}
		
		public static function load_controller($class_name, $class_name_segments)
		{
			// Checking,if it's backend or frontend controller
			if($class_name_segments[0] == "Admin") 
			{
				$is_admin_class = true;
				array_shift($class_name_segments);
			}
			else
			{
				$is_admin_class = false;
			}
			
			$file_name = implode("_", $class_name_segments);
			$file_name = strtolower($file_name);
			
			if($is_admin_class)
			{
				$path = CONTROLLERS . "admin" . DS . $file_name . ".php";
			}
			else
			{
				$path = CONTROLLERS . $file_name . ".php";
			}
			
			if(file_exists($path))
			{
				require_once($path);
			}
			// Else controller don't exists
			else 
			{
				$error = new Error_Controller;
				$error->show_404();
			}
		}
		
		public static function load_mapper($class_name, $class_name_segments)
		{
			$file_name = implode("_", $class_name_segments);
			$file_name = strtolower($file_name);
			$path      = MAPPERS . $file_name . ".php";
			
			if(file_exists($path))
			{
				require_once($path);
			}
			else
			{
				exit("Can't load class {$class_name}");
			}
		}
		
		public static function load_model($class_name, $class_name_segments)
		{
			$file_name = implode("_", $class_name_segments);
			$file_name = strtolower($file_name);
			
			$path = MODELS . $file_name . ".php";
			
			if(file_exists($path))
			{
				require_once($path);
			}
			else 
			{
				exit("Can't load class {$class_name}");
			}
		}
	}
?>