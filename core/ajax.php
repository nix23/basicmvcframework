<?php
	/** 
	* Contains attributes for creating
	* server answer.
	**/
	class Ajax
	{
		public $result = "errors";
		public $errors;
		public $callback = "none";
		public $data;
		
		public function __construct()
		{
			$this->errors = new stdClass;
			$this->data   = new stdClass;
		}
		
		public function render()
		{
			$server_answer = new stdClass;
			
			$server_answer->result   = $this->result;
			$server_answer->errors   = $this->errors;
			$server_answer->callback = $this->callback;
			$server_answer->data     = $this->data;
			
			echo Json::to_json($server_answer);
			exit;
		}
	}
?>