<?php

/**
 * Class for working with forms. Contains
 * form security tools.
 **/
class Form
{
    // Generating anti-csrf token
    public static function generate_token($form_name)
    {
        $session = Registry::get('session');
        $form_token = $form_name . "_token";

        if (!$session->is_set($form_token)) {
            $token = sha1(uniqid(microtime(), true));
            $session->set($form_token, $token);
        } else {
            $token = $session->get($form_token);
        }

        return $token;
    }

    // Checks,if anti-csrf token is valid
    public static function verify_form_token($form_name,
                                             $form_token)
    {
        $session = Registry::get('session');
        $session_token = $session->get($form_name . '_token');

        if (!$session_token
            or
            $session_token != $form_token) {
            return false;
        } else {
            return true;
        }
    }
}

?>