<?php
	/** 
	* Class-wrapper for Mysql Database
	**/
	class MySQL_Database
	{
		private $connection;
		public  $last_query;
		private $magic_quotes_active;
		private $real_escape_string_exists;
		private $config;
		
		public function __construct()
		{
			$this->config = Registry::get('config');
			$this->open_connection(); 
			$this->magic_quotes_active = get_magic_quotes_gpc();
			$this->real_escape_string_exists = function_exists("mysql_real_escape_string");
			$this->query("set names utf8");
		}
		
		// Database connection and selection
		public function open_connection()
		{
			$this->connection = mysql_connect($this->config->db_server,
														 $this->config->db_user,
														 $this->config->db_pass);
			
			if(!$this->connection)
			{
				exit("Database connection failed: ") . mysql_error();
			}
			else
			{
				$db_select = mysql_select_db($this->config->db_name,
													  $this->connection);
				if(!$db_select)
				{
					exit("Database selection failed: ") . mysql_error();
				}
			}
		}
		
		// Database disconnection
		public function close_connection()
		{
			if(isset($this->connection))
			{
				mysql_close($this->connection);
				unset($this->connection);
			}
		}
		
		// Performs query to database
		public function query($sql)
		{
			$this->last_query = $sql;
			$result = mysql_query($sql, $this->connection);
			$this->confirm_query($result);
			
			return $result;
		}
		
		// Checking,if query returned result from database
		private function confirm_query($result)
		{
			if(!$result)
			{
				//$output = "Database query failed: " . mysql_error() . "<br><br>";
				// !!! DISABLE BEFORE PRODUCTION !!!
				//$output .= "Last SQL query: " . $this->last_query;
				//exit($output);
				throw new Exception("Database error!");
			}
		}
		
		// Escaping variable
		public function escape_value($value)
		{
			if($this->real_escape_string_exists)
			{
				// Deleting all magic quotes effects
				if($this->magic_quotes_active)
				{
					$value = stripslashes($value);
				}
				$value = mysql_real_escape_string($value);
			}
			else
			{
				if(!$this->magic_quotes_active)
				{
					$value = addslashes($value);
				}
			}
			
			return $value;
		}
		
		// Wrapper for mysql_fetch_array
		public function fetch_array($result_set)
		{
			return mysql_fetch_array($result_set, MYSQL_ASSOC);
		}
		
		// Wrapper for mysql_num_rows
		public function num_rows($result_set)
		{
			return mysql_num_rows($result_set);
		}
		
		// Wrapper for mysql_insert_id
		public function insert_id()
		{
			return mysql_insert_id($this->connection);
		}
		
		// Wrapper for mysql_affected_rows
		public function affected_rows()
		{
			return mysql_affected_rows($this->connection);
		}
	}
?>