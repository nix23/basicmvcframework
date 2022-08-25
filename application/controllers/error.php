<?php

class Error_Controller extends Controller
{
    public function show_404()
    {
        header("HTTP/1.0 404 Not Found");

        if (Url::is_backend()) {
            View::set_base_path(VIEWS . DS . "admin" . DS);
            View::render(View::capture("services" . DS . "404_not_found", array()));
        } else {
            View::set_base_path(VIEWS);
            View::render(View::capture("services" . DS . "404_not_found", array()));
        }
        exit();
    }
}

?>