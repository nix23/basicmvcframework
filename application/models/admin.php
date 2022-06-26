<?php
	class Admin_Model extends Model
	{
		protected $table_name = "admins";
		protected $db_fields  = array("id", "username", "password");
		
		public $id;
		public $username;
		public $password;
		
		// Administrator authorization
		public function authenticate()
		{
			$sql  = "WHERE username = '%s' ";
			$sql .= "AND password = '%s'";
			
			$sql = sprintf($sql,
								$this->database->escape_value($this->username),
								sha1($this->database->escape_value($this->password)));
			
			return $this->find_by_condition($sql, "id");
		}
		
		public function get_validation_rules()
		{
			$rules = array();
			
			$rules['username'] = array(
				array('username_required', 'Please enter your username.', 'required')
			);
			
			$rules['password'] = array(
				array('password_required', 'Please enter your password.', 'required')
			);
			
			return $rules;
		}
	}
?>