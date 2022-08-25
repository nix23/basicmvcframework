<?php
// Base class of Mapper.
// Mappers are used for complex select queries,for example,
// when you want fetch data from batch of tables.
abstract class Mapper
{
    protected $database;
    // Attributes for query result columns
    protected $map_fields = array();
    // Pagination generator
    public $pagination;

    public function __construct()
    {
        $this->database = Registry::get('database');
    }

    public function find_count_by_sql($sql = "")
    {
        $result_set = $this->database->query($sql);
        $row = $this->database->fetch_array($result_set);

        return (int)$row['count'];
    }

    // Executes query to database and returns rows as objects
    public function find_by_sql($sql = "")
    {
        $result_set = $this->database->query($sql);
        $object_array = array();

        while ($row = $this->database->fetch_array($result_set)) {
            $object_array[] = $this->instantiate($row);
        }

        return $object_array;
    }

    // Sets db row $record values as object properties
    private function instantiate($record)
    {
        $class_name = get_class($this);
        $object = new $class_name();

        foreach ($record as $attribute => $value) {
            if ($object->has_attribute($attribute)) {
                $object->$attribute = $value;
            }
        }

        return $object;
    }

    // Checks,if object contains property $attribute
    public function has_attribute($attribute)
    {
        $object_vars = array();
        $object_vars = $this->attributes();

        return array_key_exists($attribute, $object_vars);
    }

    // Returns this object properties with their values
    public function attributes()
    {
        $attributes = array();

        foreach ($this->map_fields as $field) {
            if (property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }

        return $attributes;
    }
}

?>