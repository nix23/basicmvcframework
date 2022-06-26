<?php
	class Resources_Compiler
	{
		protected $layout;
		protected $layout_file_path;

		const RESOURCES_JS_FILES_MARKER     = "~// RESOURCES: COMPILE JS(.*)// RESOURCES: COMPILE JS END~su";
		const RESOURCES_CSS_FILES_MARKER    = "~// RESOURCES: COMPILE CSS(.*)// RESOURCES: COMPILE CSS END~su";

		private function set_layout_file_path($path)
		{
			if(empty($path))
				throw new Exception("Resources Compiler Error: empty layout file name.");

			$this->layout_file_path = $path;
		}

		private function load_layout_file()
		{
			if(!file_exists($this->layout_file_path))
				throw new Exception("Resources Compiler Error: can't load layout input file.");

			$this->layout = file_get_contents($this->layout_file_path);
		}

		private function check_if_layout_file_contains_css_and_js_to_compile()
		{
			if(!preg_match(self::RESOURCES_JS_FILES_MARKER, $this->layout)
					or
				!preg_match(self::RESOURCES_CSS_FILES_MARKER, $this->layout))
			{
				$message  = "Resources Compiler Error: looks like layout file doesn't               ";
				$message .= "contain CSS and JS include blocks to compile.                          ";
				$message .= "CSS marker: // RESOURCES: COMPILE JS ... // RESOURCES: COMPILE JS END  ";
				$message .= "JS marker: // RESOURCES: COMPILE CSS ... // RESOURCES: COMPILE CSS END ";

				throw new Exception($message);
			}
		}

		private function save_layout_file()
		{
			if(!file_exists($this->layout_file_path))
				throw new Exception("Resources Compiler Error: can't load layout output file.");

			$layout_file = fopen($this->layout_file_path, "w");
			fwrite($layout_file, $this->layout);
			fclose($layout_file);
		}

		public function compile_resources()
		{
			$this->set_layout_file_path(VIEWS . "base" . DS . "layout.php");
			$this->load_layout_file();
			$this->check_if_layout_file_contains_css_and_js_to_compile();

			$js_compiler = new Js_Compiler();
			$js_compiler->set_layout($this->layout);
			$js_compiler->set_js_folder_filepath(JS);
			$js_compiler->compile();
			$this->layout = $js_compiler->get_layout();

			$css_compiler = new Css_Compiler();
			$css_compiler->set_layout($this->layout);
			$css_compiler->set_css_folder_filepath(CSS);
			$css_compiler->compile();
			$this->layout = $css_compiler->get_layout();

			$this->save_layout_file();
		}
	}
?>