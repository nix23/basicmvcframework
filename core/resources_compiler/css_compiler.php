<?php

class Css_Compiler
{
    private $layout;
    private $include_css_filenames = array();
    private $compiled_css_layout;
    private $compiled_css_filename;
    private $css_folder_filepath;
    private $include_css_file_marker;

    const RESOURCES_CSS_IS_COMPILED_MARKER = "/* RESOURCES: CSS COMPILED */";

    public function __construct()
    {
        $this->compiled_css_layout = "";
        $this->compiled_css_filename = "compiled_" . time();

        $quotes = "('|\")";
        $this->include_css_file_marker = "~css\($quotes(.*?)$quotes\);~u";
    }

    public function set_layout($layout)
    {
        $this->layout = $layout;
    }

    public function get_layout()
    {
        return $this->layout;
    }

    public function set_css_folder_filepath($path)
    {
        $this->css_folder_filepath = $path;
    }

    private function get_include_css_filenames_from_layout()
    {
        preg_match(Resources_Compiler::RESOURCES_CSS_FILES_MARKER, $this->layout, $matches);
        $css_layout = $matches[1];

        preg_match_all($this->include_css_file_marker, $css_layout, $matches, PREG_PATTERN_ORDER);

        if (empty($matches[2]))
            throw new Exception("Resources Compiler Error: css files to compile are not found");

        foreach ($matches[2] as $match)
            $this->include_css_filenames[] = $match;
    }

    private function combine_all_css_files_layout()
    {
        foreach ($this->include_css_filenames as $include_css_filename) {
            $include_css_filename = str_replace("/", DS, $include_css_filename);
            $css_file_fs_path = $this->css_folder_filepath . $include_css_filename . ".css";

            $this->compiled_css_layout .= file_get_contents($css_file_fs_path) . PHP_EOL . PHP_EOL;
        }
    }

    private function save_combined_css_layout_in_file()
    {
        $compiled_css_file_fs_path = $this->css_folder_filepath . "compiled";
        $compiled_css_file_fs_path .= DS . $this->compiled_css_filename . ".css";

        $compiled_file = fopen($compiled_css_file_fs_path, "w");
        fputs($compiled_file, $this->compiled_css_layout);
        fclose($compiled_file);
    }

    private function add_combined_css_filename_in_layout()
    {
        $include_combined_css_filename = "css('compiled/{$this->compiled_css_filename}');";
        $this->layout = preg_replace(Resources_Compiler::RESOURCES_CSS_FILES_MARKER,
            $include_combined_css_filename . " " . self::RESOURCES_CSS_IS_COMPILED_MARKER,
            $this->layout);
    }

    public function compile()
    {
        $this->get_include_css_filenames_from_layout();
        $this->combine_all_css_files_layout();
        $this->save_combined_css_layout_in_file();
        $this->add_combined_css_filename_in_layout();
    }
}

?>