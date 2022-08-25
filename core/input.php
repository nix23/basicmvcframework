<?php

/**
 * Class contains all super-global variables,
 * which are passed to script
 **/
class Input
{
    private $get = array();
    private $post = array();

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    // Returns variable from $_GET array
    public function get($key, $default_value = "")
    {
        if (!array_key_exists($key, $this->get)) {
            if (!empty($default_value)) {
                return $default_value;
            } else {
                exit("Error: key {$key} not exists in GET array");
            }
        }

        return $this->get[$key];
    }

    // Returns variable from $_POST array
    public function post($key, $default_value = "")
    {
        if (!array_key_exists($key, $this->post)) {
            if (!empty($default_value)) {
                return $default_value;
            } else {
                exit("Error: key {$key} not exists in POST array");
            }
        }

        return $this->post[$key];
    }
}

?>