<?php
    class Footer_Controller extends Public_Controller
    {
        public function about()
        {
            if($this->session->is_logged_in())
                Url::redirect("account/welcome");

            $data = array();

            $data["heading"]      = "Welcome to Fordrive!";
            $data["subheading"]   = "Register and join to online car enthusiasts community";
            $data["is_registred"] = false;

            $this->page_title    = "About / Fordrive";
            $this->view->content = View::capture("base" . DS . "welcome", $data);
        }

        public function terms()
        {
            $data                  = array();
            $data["support_email"] = $this->settings->support_email;

            $this->page_title    = "Terms and Conditions / Fordrive";
            $this->view->content = View::capture("base" . DS . "terms_and_conditions", $data);
        }

        public function privacypolicy()
        {
            $data                  = array();
            $data["support_email"] = $this->settings->support_email;

            $this->page_title    = "Privacy Policy / Fordrive";
            $this->view->content = View::capture("base" . DS . "privacy_policy", $data);
        }

        public function support()
        {
            $data                  = array();
            $data["support_email"] = $this->settings->support_email;

            $this->page_title    = "Support / Fordrive";
            $this->view->content = View::capture("base" . DS . "support", $data);
        }
    }
?>