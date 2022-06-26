<?php
	class Pagination
	{
		public $current_page;
		public $total_pages;
		public $records_per_page;
		public $offset;
		public $total_records;
		
		// Generated page numbers
		public $pages = array();
		
		public $is_lower_than_first = false;
		public $is_higher_than_last = false;
		
		public function __construct($current_page, $total_records)
		{
			$config = Registry::get('config');
			
			$this->total_records    = (int)$total_records;
			$this->records_per_page = (int)$config->records_per_page;
			$this->current_page     = (int)$current_page;
			$this->offset           = (int)($this->current_page - 1) * $this->records_per_page;
			
			if($total_records == 0)
			{
				$this->total_pages = 1;
			}
			else
			{
				$this->total_pages = (int)ceil($this->total_records / $this->records_per_page);
			}
			
			// Checking page range
			if($this->current_page < 1)
			{
				$this->is_lower_than_first = true;
			}
			else if($this->current_page > $this->total_pages)
			{
				$this->is_higher_than_last = true;
			}
		}
		
		public function validate_page_range()
		{
			if($this->is_lower_than_first
					or
				$this->is_higher_than_last)
			{ 
				$error = new Error_Controller;
				$error->show_404();
			}
		}
		
		public function make_pages($type = "")
		{
			switch($type)
			{
				case "compact":
					return $this->make_compact_pages();
				break;
			}
		}
		
		private function make_compact_pages()
		{
			if($this->total_pages > 7)
			{
				if($this->current_page <= 4)
				{
					$this->paginate_low_segment();
				}
				else if($this->current_page >= ($this->total_pages - 3))
				{
					$this->paginate_high_segment();
				}
				else
				{
					$this->paginate_middle_segment();
				}
			}
			else
			{
				$this->paginate_min_segment();
			}
			
			return $this->pages;
		}
		
		// Pages: 1 2 3 4 5 6 last_page
		private function paginate_low_segment()
		{
			for($page = 1; $page <= 6; $page++)
			{
				$this->pages[] = $page;
			}
			
			$this->pages[] = $this->total_pages;
		}
		
		// Pages: 1 tp-5 tp-4 tp-3 tp-2 tp-1 total_pages
		private function paginate_high_segment()
		{
			$this->pages[] = 1;
			
			for($page = $this->total_pages - 5; $page <= $this->total_pages; $page++)
			{
				$this->pages[] = $page;
			}
		}
		
		// Pages: 1 cp-2 cp-1 cp cp+1 cp+2 total_pages
		private function paginate_middle_segment()
		{
			$this->pages[] = 1;
			
			for($page = $this->current_page - 2; $page <= $this->current_page + 2; $page++)
			{
				$this->pages[] = $page;
			}
			
			$this->pages[] = $this->total_pages;
		}
		
		// Pages: 1 ... total_pages
		private function paginate_min_segment()
		{
			for($page = 1; $page <= $this->total_pages; $page++)
			{
				$this->pages[] = $page;
			}
		}
	}
?>