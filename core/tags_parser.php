<?php
	class Tags_Parser extends Tags_Scanner
	{
		// Count of uploaded photos
		private $photos_count;
		private $root_opening_tags = array();
		
		// Opening tags.(If tags are nested,only root tag is needed)
		private $article_root_opening_tags     = array("b", "link", "photoset");
		// Not used yet.(switched to articles in photos and spots modules)
		private $description_root_opening_tags = array("b", "link");
		
		public function __construct($tags_type    = "",
											 $text         = "",
											 $photos_count = 0)
		{
			switch($tags_type)
			{
				case "article_tags":
					$this->root_opening_tags = $this->article_root_opening_tags;
				break;
				
				case "description_tags":
					$this->root_opening_tags = $this->description_root_opening_tags;
				break;
				
				default:
					exit("Wrong 'tags_type' passed in Tags_Validator.");
				break;
			}
			
			$this->photos_count = $photos_count;
			parent::__construct($text);
		}
		
		private function check_tag_is_closed_and_consists_only_from_text($tag)
		{
			$this->move_to_next_tag();
			
			if($this->are_all_tags_parsed())
			{
				$this->errors[] = "One of '[$tag]' tags is empty and isn't closed.";
				return;
			}
			
			$closing_tag_found = false;
			$tag_is_empty      = true;
			
			while(!$this->are_all_tags_parsed()
						and
					!$closing_tag_found)
			{
				list( $current_tag_name, 
						$current_tag_value) = $this->get_current_tag();
				
				if($current_tag_name == "/$tag")
				{
					$closing_tag_found = true;
				}
				else if($current_tag_name == "text")
				{
					$tag_is_empty = false;
				}
				else
				{
					if(empty($current_tag_name))
						$this->errors[] = "Empty tag [] found inside one of '[$tag]' tags.";
					else
						$this->errors[] = "Wrong closing tag '[$current_tag_name]' found inside one of '[$tag]' tags.";
					
					$tag_is_empty = false;
				}
				
				$this->move_to_next_tag();
			}
			
			if($tag_is_empty)
				$this->errors[] = "One of the '[$tag][/$tag]' tags is empty.";
			else if(!$closing_tag_found)
				$this->errors[] = "One of the '[$tag]' tags isn't closed";
			return;
		}
		
		private function check_photoset_tag()
		{
			$this->move_to_next_tag();
			
			if($this->are_all_tags_parsed())
			{
				$this->errors[] = "One of '[photoset]' tags is empty and isn't closed.";
				return;
			}
			
			$image_tags_found       = false;
			$not_image_tag_is_found = false;
			
			// Parsing [img=number] tags
			while(!$this->are_all_tags_parsed()
						and
					!$not_image_tag_is_found)
			{
				list( $current_tag_name, 
						$current_tag_value) = $this->get_current_tag();
				
				if($current_tag_name == "img")
				{
					$image_tags_found = true;
					
					if(empty($current_tag_value) and !ctype_digit($current_tag_value))
					{
						$this->errors[] = "One of photoset '[img=number]' tags is empty.";
					}
					else if(!preg_match("~^\d+$~u", $current_tag_value))
					{
						$this->errors[] = "One of photoset '[img=$current_tag_value]' tags number part is not a digit.";
					}
					else if(!($current_tag_value >= 1 and $current_tag_value <= $this->photos_count))
					{
						$error          = "One of photoset tag '[img=$current_tag_value]' is wrong,";
						$error         .= "image with that number was not uploaded.";
						$this->errors[] = $error; 
					}
					
					$this->move_to_next_tag();
				}
				else
				{
					$not_image_tag_is_found = true;
				}
			}
			
			// At least one [img=number] tag should be found
			if(!$image_tags_found)
			{
				$this->errors[] = "One of '[photoset]' tags is wrong,it should contain at least one '[img]' tag,and start from it.";
				return;
			}
			
			if($this->are_all_tags_parsed())
			{
				$this->errors[] = "One of '[photoset]' tags is not closed.";
				return;
			}
			
			// Checking,if tag is [caption]text[/caption]
			list( $current_tag_name, 
					$current_tag_value) = $this->get_current_tag();
			
			if($current_tag_name == "caption")
				$this->check_tag_is_closed_and_consists_only_from_text("caption");
			
			if($this->are_all_tags_parsed())
			{
				$this->errors[] = "One of '[photoset]' tags is not closed.";
				return;
			}
			
			// Checking,if closing tag [/photoset] exists
			list( $current_tag_name, 
					$current_tag_value) = $this->get_current_tag();
			
			if($current_tag_name == "/photoset")
				$this->move_to_next_tag();
			else
				$this->errors[] = "One of '[photoset]' tags is not closed,or there is some text before [/photoset] tag.";
			
			return;
		}
		
		public function validate()
		{
			$this->make_tags();
			
			while(!$this->are_all_tags_parsed())
			{
				list( $current_tag_name, 
						$current_tag_value) = $this->get_current_tag();
				
				if($current_tag_name == "b" and in_array($current_tag_name, $this->root_opening_tags))
				{
					$this->check_tag_is_closed_and_consists_only_from_text("b");
				}
				else if($current_tag_name == "link" and in_array($current_tag_name, $this->root_opening_tags))
				{
					if(empty($current_tag_value))
						$this->errors[] = "One of '[link=url]' tags url is empty.";
					
					$this->check_tag_is_closed_and_consists_only_from_text("link");
				}
				else if($current_tag_name == "photoset" and in_array($current_tag_name, $this->root_opening_tags))
				{
					$this->check_photoset_tag();
				}
				else if($current_tag_name == "text")
				{
					$this->move_to_next_tag();
				}
				else
				{
					if(empty($current_tag_name))
						$this->errors[] = "Empty tag [] found.";
					else
					{
						$error           = "Wrong tag '[$current_tag_name]' found";
						$error          .= "(This error can appear because of previous errors, ";
						$error          .= ",tag has wrong syntax or is placed in wrong place).";
						$this->errors[]  = $error;
					}
					
					$this->move_to_next_tag();
				}
			}
		}
	}
?>