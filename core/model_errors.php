<?php

/**
 * Class-wrapper for all errors,which can happen
 * in bisness-logic of models,for example if model
 * attributes validation goes wrong.
 **/
class Model_Errors
{
    private $errors;
    private $ajax;
    private $has_errors;

    public function __construct()
    {
        $this->ajax = Registry::get('ajax');
        $this->errors = array();
        $this->has_errors = false;
    }

    public function set($error_name, $error_value)
    {
        $this->errors[$error_name] = $error_value;

        $this->has_errors = true;
    }

    // Returns errors list,if ajax-request was made
    public function ajaxify_if_has_errors()
    {
        if ($this->has_errors) {
            foreach ($this->errors as $error_name => $error_value) {
                $this->ajax->errors->$error_name = $error_value;
            }

            $this->ajax->render();
        }
    }

    /**
     * In not ajax controller actions,errors
     * should return in following format:
     * array(
     *   [0] => stdClass(error_name => error_value),
     *   [1] => stdClass(error_name => error_value)
     * )
     **/
    public function get()
    {
        $errors_object_array = array();

        foreach ($this->errors as $error_name => $error_value) {
            $error_object = new stdClass;

            $error_object->name = $error_name;
            $error_object->value = $error_value;

            $errors_object_array[] = $error_object;
        }

        return $errors_object_array;
    }
}

?>