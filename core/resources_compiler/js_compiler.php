<?php
	class Js_Compiler
	{
		private $layout;
		private $include_js_filenames = array();
		private $compiled_js_layout;
		private $compiled_js_filename;
		private $js_folder_filepath;
		private $include_js_file_marker;

		const RESOURCES_JS_IS_COMPILED_MARKER = "/* RESOURCES: JS COMPILED */";

		public function __construct()
		{
			$this->compiled_js_layout   = "";
			$this->compiled_js_filename = "compiled_" . time();

			$quotes = "('|\")";
			$this->include_js_file_marker = "~js\($quotes(.*?)$quotes\);~u";
		}

		public function set_layout($layout)
		{
			$this->layout = $layout;
		}

		public function get_layout()
		{
			return $this->layout;
		}

		public function set_js_folder_filepath($path)
		{
			$this->js_folder_filepath = $path;
		}

		private function get_include_js_filenames_from_layout()
		{
			preg_match(Resources_Compiler::RESOURCES_JS_FILES_MARKER, $this->layout, $matches);
			$js_layout = $matches[1];

			preg_match_all($this->include_js_file_marker, $js_layout, $matches, PREG_PATTERN_ORDER);

			if(empty($matches[2]))
				throw new Exception("Resources Compiler Error: js files to compile are not found");

			foreach($matches[2] as $match)
				$this->include_js_filenames[] = $match;
		}

		private function combine_all_js_files_layout()
		{
			foreach($this->include_js_filenames as $include_js_filename)
			{
				$js_file_fs_path = $this->js_folder_filepath . $include_js_filename . ".js";
				$this->compiled_js_layout .= file_get_contents($js_file_fs_path) . PHP_EOL . PHP_EOL;
			}
		}

		private function save_combined_js_layout_in_file()
		{
			$compiled_js_file_fs_path  = $this->js_folder_filepath . "compiled";
			$compiled_js_file_fs_path .= DS . $this->compiled_js_filename . ".js";

			$compiled_file = fopen($compiled_js_file_fs_path, "w");
			fputs($compiled_file, $this->compiled_js_layout);
			fclose($compiled_file);
		}

		private function add_combined_js_filename_in_layout()
		{
			$include_combined_js_filename = "js('compiled/{$this->compiled_js_filename}');";
			$this->layout = preg_replace(Resources_Compiler::RESOURCES_JS_FILES_MARKER,
												  $include_combined_js_filename . " " . self::RESOURCES_JS_IS_COMPILED_MARKER,
												  $this->layout);
		}

		public function compile()
		{
			$this->get_include_js_filenames_from_layout();
			$this->combine_all_js_files_layout();
			$this->save_combined_js_layout_in_file();
			$this->add_combined_js_filename_in_layout();
		}
	}
?>