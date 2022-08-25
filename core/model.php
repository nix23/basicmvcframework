<?php
    /** 
    * Base class of model.
    * All application models should inherit it.
    *  
    *  All models support 4 types of attributes:
    *   -Attributes,which exists inside $db_fields array.
    *    It means,that this attribute is stored right in table,
    *    which is directly attached to this model and can be
    *    fetched in queries directly.
    *
    *   -Attributes,which exists inside $nested_db_fields array.
    *   It means,that this attribute is stored in some children
    *   table,but can be fetched directly to this model from
    *   nested select queries.
    *
    *   -Attributes,which exists inside $special_fields array.
    *   It means,that this attribute is part of this model logic,
    *   and can be used in different methods(like validate()),
    *   but it will not be used in db queries.(Example - password
    *   confirmation field and license).
    *
    *   -Remaining attributes are called shared,and are used to
    *   work with other models.
    *
    **/
    abstract class Model
    {
        protected $table_name;
        protected $database;
        protected $model_errors;
        
        // This model db fields.
        // Are used in (INSERT,UPDATE,DELETE,SELECT)
        protected $db_fields        = array();
        
        // Nested model db fields.
        // Are used only in (SELECT)
        protected $nested_db_fields = array();
        
        // Incapsulates pagination routine
        public $pagination;
        
        public function __construct()
        {
            $this->database     = Registry::get('database');
            $this->model_errors = Registry::get('model_errors');
        }
        
        // Returns database table name
        public function get_table_name()
        {
            return $this->table_name;
        }
        
        // Returns instance class name
        public function get_model_name()
        {
            return get_class($this);
        }
        
        // Get all table records
        public function find_all($conditions = "", $columns = "*")
        {
            $sql = "SELECT {$columns} FROM {$this->table_name} {$conditions}";
            
            return $this->find_by_sql($sql);
        }
    
        // Get record by id
        public function find_by_id($id = 0, $columns = "*")
        {
            $sql = "SELECT {$columns} FROM {$this->table_name}
                      WHERE id=%d LIMIT 1";
            
            $sql = sprintf($sql, $this->database->escape_value($id));
            
            $result_array = $this->find_by_sql($sql); 
            
            return !empty($result_array) ? array_shift($result_array) : false;
        }
        
        // Get record by condition
        public function find_by_condition($condition = "", $columns = "*")
        {
            $sql = "SELECT {$columns} FROM {$this->table_name}
                      {$condition} LIMIT 1";
            
            $result_array = $this->find_by_sql($sql);
            
            return !empty($result_array) ? array_shift($result_array) : false;
        }
        
        // Arbitrary query to db
        public function find_by_sql($sql = "")
        {
            $result_set = $this->database->query($sql);
            
            // One object in array corresponds to one row in table
            $object_array = array();
            
            while($row = $this->database->fetch_array($result_set))
            { 
                $object_array[] = $this->instantiate($row);
            } 
            
            return $object_array;
        }
        
        // Get max column value
        public function find_max($column, $conditions = "")
        {
            $sql = "SELECT MAX({$column}) as max_column_value
                      FROM {$this->table_name} {$conditions}";
            
            $result_set = $this->database->query($sql);
            
            $row = $this->database->fetch_array($result_set);
            
            $max = $row['max_column_value'];
            
            if($max == null)
            {
                $max = 0;
            }
            
            return $max;
        }
        
        // Get table records count
        public function count($conditions = "")
        {
            $sql = "SELECT COUNT(*) as count FROM
                      {$this->table_name} {$conditions}";
            
            $result_set = $this->database->query($sql);
            
            $row = $this->database->fetch_array($result_set);
             
            return (int) $row['count'];
        }
        
        // Fill object attributes with attributes from row $record
        private function instantiate($record)
        {
            $class_name = get_class($this);
            $object     = new $class_name(); 
            
            foreach($record as $attribute => $value)
            {
                if($object->has_attribute($attribute, true))
                {
                    $object->$attribute = $value;
                }
            } 
            
            return $object;
        }
        
        // Checks,if object contains attribute $attribute
        public function has_attribute($attribute,
                                                $search_in_nested  = false,
                                                $search_in_special = false)
        {
            $object_vars = array();
            
            // Getting list of attributes,which are db fields
            $object_vars = $this->attributes();
            
            // Getting list of nested attributes
            if($search_in_nested)
            {
                $object_vars = array_merge($object_vars, $this->nested_attributes());
            }
            
            // Getting list of special attributes
            if($search_in_special)
            {
                $object_vars = array_merge($object_vars, $this->special_attributes());
            }
            
            return array_key_exists($attribute, $object_vars);
        }
        
        // Get array keys with their values
        protected function attributes()
        {
            $attributes = array();
            
            // We need only fields from db table
            foreach($this->db_fields as $field)
            {
                if(property_exists($this, $field))
                {
                    $attributes[$field] = $this->$field;
                }
            }
            
            return $attributes;
        }
        
        // Get array of nested keys with their values
        protected function nested_attributes()
        {
            $attributes = array();
            
            if(isset($this->nested_db_fields))
            {
                foreach($this->nested_db_fields as $field)
                {
                    if(property_exists($this, $field))
                    {
                        $attributes[$field] = $this->$field;
                    }
                }
            }
            
            return $attributes;
        }
        
        // Get array of special keys with their values
        protected function special_attributes()
        {
            $attributes = array();
            
            if(isset($this->special_fields))
            {
                foreach($this->special_fields as $field)
                {
                    if(property_exists($this, $field))
                    {
                        $attributes[$field] = $this->$field;
                    }
                }
            }
            
            return $attributes;
        }
        
        // Escaping all object attributes
        public function sanitized_attributes()
        {
            $clean_attributes = array();
            
            foreach($this->attributes() as $key => $value)
            { 
                $clean_attributes[$key] = $this->database->escape_value($value);
            }
            
            return $clean_attributes;
        }
        
        // Adds record to table
        protected function create()
        {
            $attributes = $this->sanitized_attributes();
            
            $sql = "INSERT INTO {$this->table_name} (";
            $sql .= join(", ", array_keys($attributes));
            $sql .= ") VALUES ('";
            $sql .= join("', '", array_values($attributes));
            $sql .= "')";
            
            if($this->database->query($sql))
            {
                $this->id = $this->database->insert_id();
                return true;
            }
            else
            {
                return false;
            }
        }
        
        // Updates record in table
        protected function update($update_exceptions = array())
        {
            $attributes = $this->sanitized_attributes();
            
            // We don't need to update id
            unset($attributes["id"]);
            
            $attribute_pairs = array();
            
            foreach($attributes as $key => $value)
            {
                if(!in_array($key, $update_exceptions))
                    $attribute_pairs[] = "{$key}='{$value}'";
            }
        
            $sql = "UPDATE {$this->table_name} SET ";
            $sql .= join(", ", $attribute_pairs);
            $sql .= " WHERE id=%d";
            
            $sql = sprintf($sql, $this->id);
            
            return ($this->database->query($sql)) ? true : false;
        }
        
        // Updating arbitrary columns in table
        public function update_only($columns = array())
        {
            $attributes      = $this->sanitized_attributes();
            $attribute_pairs = array();
            
            foreach($columns as $column)
            {
                if(array_key_exists($column, $attributes))
                {
                    $attribute_pairs[] = $column . "='" . $attributes[$column] . "'";
                }
                else
                {
                    $message  = "Attribute '{$column}' not found ";
                    $message .= "in class " . get_class($this);
                    $message .= " (update_only)";
                    exit($message);
                }
            }
            
            $sql  = "UPDATE {$this->table_name} SET ";
            $sql .= join(", ", $attribute_pairs);
            $sql .= " WHERE id=%d";
            $sql  = sprintf($sql, $this->id);
            
            return ($this->database->query($sql)) ? true : false;
        }
        
        // Decides,create record or update current
        public function save($update_exceptions = array())
        {
            // New record doesn't has id
            return empty($this->id) ? $this->create() : $this->update($update_exceptions);
        }
        
        // Deletes record from table
        public function delete()
        {
            $sql = "DELETE FROM {$this->table_name}
                      WHERE id=%d LIMIT 1";
            
            $sql = sprintf($sql, $this->database->escape_value($this->id));
            
            $this->database->query($sql);
            
            return ($this->database->affected_rows() == 1) ? true : false;
        }
        
        // Deletes row batch by condition
        public function delete_by_condition($condition = "")
        {
            $sql = "DELETE FROM {$this->table_name} {$condition}";
            
            $this->database->query($sql);
            
            return ($this->database->affected_rows() > 0) ? true : false;
        }

        public function delele_all_rows()
        {
            $sql = "DELETE FROM {$this->table_name}";
            $this->database->query($sql);
        }
        
        // Loads attributes to model from form
        public function bind($form_keys_and_values = array())
        {
            foreach($form_keys_and_values as $key => $value)
            {
                if($this->has_attribute($key,
                                                false,
                                                true))
                {
                    $value      = trim($value);
                    $this->$key = $value;
                }
            }
        }
        
        // Loads foreign key value from parent to model
        public function bind_id($object, $foreign_key_name)
        {
            $this->$foreign_key_name = $object->id;
        }
        
        // Parsing one validation rule
        private function parse_validation_rule($value, $rule_array)
        {
            $error_name       = array_shift($rule_array);
            $error_value      = array_shift($rule_array);
            $validator        = array_shift($rule_array);
            $parametrs_packed = "";
            
            if(isset($rule_array[0]) && !empty($rule_array[0]))
            {
                $parametrs_packed = $rule_array[0];
            }
            
            if(method_exists('Validator', $validator))
            {
                list($is_valid, $value) = call_user_func_array( array('Validator', $validator), 
                                                                                array($value, $parametrs_packed));
            }
            else
            {
                exit("Validator doesn't have method {$validator}");
            }
            
            // "None" - validator will only modify the value
            if($error_name != "none" && $error_value != "none")
            {
                if(!$is_valid)
                {
                    $this->model_errors->set($error_name, $error_value);
                }
            }
            
            return $value;
        }
        
        // Building list of attributes,which needs to be checked
        private function select_rules_to_validate($validate_only = array(),
                                                                $rules_method  = false)
        {
            if($rules_method)
            {
                $rules = $this->$rules_method();
            }
            else
            {
                $rules = $this->get_validation_rules();
            }
            
            if(!empty($validate_only))
            {
                $rules_only = array();
                
                foreach($validate_only as $attribute)
                {
                    if(!array_key_exists($attribute, $rules))
                    {
                        $model = get_class($this);
                        exit("{$model} validate only rule error: no '{$attribute}'");
                    }
                    
                    $rules_only[$attribute] = $rules[$attribute];
                }
                
                $rules = $rules_only;
            }
            
            return $rules;
        }
        
        // Checks this model attributes
        public function validate($validate_only = array(),
                                         $rules_method  = false)
        {
            $rules = $this->select_rules_to_validate($validate_only,
                                                                  $rules_method);
            
            foreach($rules as $attribute => $rules_array)
            {
                if($this->has_attribute($attribute,
                                                false,
                                                true))
                {
                    // Apply all rules to attribute
                    foreach($rules_array as $rule_array)
                    {
                        $value = $this->parse_validation_rule($this->$attribute, $rule_array);
                        $this->$attribute = $value;
                    }
                }
                else
                {
                    // Informing about no attribute error
                    $model = get_class($this);
                    exit("{$model} rule error: no '{$attribute}'");
                }
            }
        }
        
        // All models should realize this method,
        // which contains validation rules
        public function get_validation_rules()
        {
            /** 
            * All rules should have following format:
            *     array['model_attribute_name'] = array(
            *        array('error_name', 'error_value', 'validator', [parametrs])    
            *     );
            *     where parametrs - parametrs passed to validator,
            *     which are separated by char '|', for example "13|5|3"
            **/ 
        }
        
        // Checks column value for uniqueness
        public function is_unique($column_name, $column_value, $conditions = "")
        {
            $sql  = "WHERE {$column_name} = '%s' ";
            $sql .= "{$conditions}";
            
            $sql = sprintf($sql,
                                $this->database->escape_value($column_value));
            
            return ($this->count($sql) > 0) ? false : true;
        }

        public function is_record_updating()
        {
            return (!empty($this->id)) ? true : false;
        }

        public function is_record_updating_by_owner($update_initialiser_id,
                                                                  $owner_id)
        {
            return ($update_initialiser_id == $owner_id) ? true : false;
        }

        public function is_record_deleting_by_owner($delete_initialiser_id,
                                                                  $owner_id)
        {
            return ($delete_initialiser_id == $owner_id) ? true : false;
        }
    }
?>