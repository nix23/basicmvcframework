<?php
	abstract class Tags_Scanner
	{
		private $text;
		private $last_text_char_index    = 0;
		private $current_text_char_index = 0;
		
		// Format: array(
		// 	array("name" => "tag1", "value" => "param"),
		// 	array("name" => "tag2", "value" => "param")
		// )
		protected $tags              = array();
		protected $current_tag_index = 0;
		protected $last_tag_index    = 0;
		
		public $errors = array();
		
		private $current_char = "";
		private $current_tag  = "";
		
		public function __construct($text)
		{
			$this->text                 = $text;
			$this->last_text_char_index = strlen($this->text) - 1;
		}
		
		public function has_errors()
		{
			return (count($this->errors) > 0) ? true : false;
		}
		
		/**
		*	Common methods for working with tags
		**/
		protected function are_all_tags_parsed()
		{
			if($this->current_tag_index > $this->last_tag_index)
				return true;
			else
				return false;
		}
		
		protected function get_current_tag()
		{
			return array(
						$this->tags[$this->current_tag_index]["name"],
						$this->tags[$this->current_tag_index]["value"]
					);
		}
		
		protected function move_to_next_tag()
		{
			$this->current_tag_index++;
		}
		
		/**
		*	Common methods for recognizing tags
		**/
		private function get_current_char()
		{
			return $this->text[$this->current_text_char_index];
		}
		
		private function move_to_next_char()
		{
			$this->current_text_char_index++;
		}
		
		private function are_all_text_chars_scanned()
		{
			if($this->current_text_char_index > $this->last_text_char_index)
				return true;
			else
				return false;
		}
		
		private function add_tag()
		{
			// Removing '[' char from start and ']' char from end
			$this->current_tag = substr($this->current_tag, 
												 1,
												 strlen($this->current_tag) - 2);
			
			// Checking,if tag has a format with value([tag=val])
			if(preg_match("~=~u", $this->current_tag))
			{
				list($tag_name, $tag_value) = explode("=", $this->current_tag);
			}
			else
			{
				$tag_name  = $this->current_tag;
				$tag_value = "";
			}
			
			$this->tags[] = array("name"  => $tag_name, 
										 "value" => $tag_value);
		}
		
		private function add_text_tag()
		{
			$this->tags[] = array("name" => "text", "value" => "text");
		}
		
		private function is_current_tag_empty()
		{
			if(mb_strlen($this->current_tag, "UTF-8") == 0)
				return true;
			else
				return false;
		}
		
		private function add_to_current_tag($current_char)
		{
			$this->current_tag .= $current_char;
		}
		
		private function clear_current_tag()
		{
			$this->current_tag = "";
		}
		
		private function is_whitespace_char($current_char)
		{
			return (ctype_space($current_char)) ? true : false;
		}
		
		protected function make_tags()
		{
			$tag_start_found = false;
			
			while(!$this->are_all_text_chars_scanned())
			{
				$current_char = $this->get_current_char();
				
				if($this->is_whitespace_char($current_char))
				{
					$this->move_to_next_char();
					continue;
				}
				
				// Tag opening char found
				if($current_char == "[")
				{
					if(!$this->is_current_tag_empty())
					{
						$this->add_text_tag();
						$this->clear_current_tag();
					}
					
					$this->add_to_current_tag($current_char);
					$tag_start_found = true;
				}
				// Tag closing char found
				else if($current_char == "]")
				{
					if($tag_start_found)
					{
						$tag_start_found = false;
						$this->add_to_current_tag($current_char);
						$this->add_tag();
						$this->clear_current_tag();
					}
					else
					{
						$this->add_text_tag();
						$this->clear_current_tag();
					}
				}
				else
				{
					$this->add_to_current_tag($current_char);
				}
				
				$this->move_to_next_char();
			}
			
			if(!$this->is_current_tag_empty())
			{
				$this->add_text_tag();
				$this->clear_current_tag();
			}
			
			$this->last_tag_index = count($this->tags) - 1;
		}
		
		abstract function validate();
	}
?>