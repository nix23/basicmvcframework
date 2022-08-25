<?php

class Account_Controller extends Public_Controller
{
    public function create($request_type = "")
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        $user = new User_Model;

        // Checking first set of rules(fields not empty)
        $user->bind($this->input->post("registration"));
        $user->validate(array(),
            "get_registration_fill_rules");

        if (!$this->verify_auth_key())
            $this->model_errors->set("invalid_authkey",
                "Wrong form token, please try refresh the page.");

        $this->model_errors->ajaxify_if_has_errors();

        // Checking second set of rules(syntax and login/mail uniqness)
        $user->validate(array(),
            "get_registration_syntax_rules");

        if (!$user->is_username_unique())
            $this->model_errors->set("username_taken",
                "This login has already been taken.");

        if (!$user->is_email_unique())
            $this->model_errors->set("email_taken",
                "This email has already been taken.");

        $this->model_errors->ajaxify_if_has_errors();

        if ($user->save()) {
            $data['hash'] = $user->hash;
            $email_html = View::capture("base" . DS . "activation_email", $data);

            if (!$user->send_activation_email($email_html)) {
                $this->model_errors->set("validation_email_not_send",
                    "Can't send validation email.");
            }

            $this->model_errors->ajaxify_if_has_errors();

            $this->ajax->result = "ok";
            $this->ajax->callback = "show_activation_sent";
            $this->ajax->data->email = $user->email;

            $this->ajax->render();
        }
    }

    public function activate($hash = "")
    {
        $user_model = new User_Model;
        $user_to_activate = $user_model->find_account_by_hash($hash);

        if (!$user_to_activate) {
            $error = new Error_Controller;
            $error->show_404();
        }

        if ($user_to_activate->is_account_activated()) {
            Url::redirect("main");
        } else {
            $user_to_activate->rank = $user_model->find_max("rank") + 1;
            $user_to_activate->activated = "yes";
            $user_to_activate->hash = "";

            if ($user_to_activate->save(true)) {
                $this->session->login($user_to_activate);
                Url::redirect("account/welcome");
            }
        }
    }

    public function welcome()
    {
        $this->show_404_if_not_authorized();
        $data = array();

        $data["heading"] = "Welcome to Fordrive!";
        $data["subheading"] = "Get started by uploading your drives";
        $data["is_registred"] = true;

        $this->page_title = "Welcome / Fordrive";
        $this->view->content = View::capture("base" . DS . "welcome", $data);
    }

    public function login($request_type = "")
    {
        $this->is_ajax($request_type);
        $this->validate_token();

        $user_model = new User_Model;

        $user_model->bind($this->input->post("login"));
        $user_model->validate(array(),
            "get_authorization_rules");

        if (!$this->verify_auth_key())
            $this->model_errors->set("invalid_authkey",
                "Wrong form token, please try refresh the page.");

        $this->model_errors->ajaxify_if_has_errors();

        $user = $user_model->authenticate();
        if ($user) {
            $this->session->login($user);

            $this->ajax->result = "ok";
            $this->ajax->callback = "refresh";
        } else {
            $this->ajax->errors->login_error = "Wrong username or password.";
        }

        $this->ajax->render();
    }

    public function facebookLogin($request_type = "")
    {
        $this->is_ajax($request_type);
        //$this->validate_token();

        $facebook = Registry::get("facebook");
        $facebookUser = $facebook->getUser();

        if ($facebookUser) {
            try {
                $userProfile = $facebook->api('/me');
            } catch (FacebookApiException $e) {
                $facebookUser = null;
                $this->ajax->errors->facebookLoginError = "Can't fetch your profile from Facebook, please try again later" . $e->getMessage();
                $this->ajax->render();
            }
        }

        $facebookUserId = $userProfile["id"];
        $facebookUsername = $userProfile["first_name"] . " " . $userProfile["last_name"];
        $facebookUserHometown = $userProfile["hometown"]["name"];
        $facebookUserLocation = $userProfile["location"]["name"];

        if ($this->isNotDefaultFacebookProfilePicture($facebookUserId)) {
            $facebookUserProfilePictureLink = "http://graph.facebook.com/" . $facebookUserId . "/picture?type=large";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $facebookUserProfilePictureLink);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ignore SSL verifying
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $facebookUserProfilePictureBinaryDataString = curl_exec($ch);
            curl_close($ch);
            $hasFacebookUserProfilePicture = true;

            $userModel = new User_Model;
            $avatarClones = $userModel->avatar_clones;

            $image_uploader = new Fordrive_Uploader(100, 100);
            $image_uploader->setExtension("jpg");
            $image_uploader->createUploadDirectories($avatarClones);
            $image_uploader->createUploadedImageFromBinaryString($facebookUserProfilePictureBinaryDataString);

            if (!$image_uploader->createClones(false))
                $hasFacebookUserProfilePicture = false;

            $image_uploader->delete_master_photo();

            if ($hasFacebookUserProfilePicture)
                $masterPhotoName = $image_uploader->master_photo_name;
        } else {
            $hasFacebookUserProfilePicture = false;
            $masterPhotoName = "";
        }

        $facebookUserModel = new User_Model();
        $facebookUser = $facebookUserModel->findByFacebookUserId($facebookUserId);

        if ($facebookUser) {
            $isFirstLoginFromFBAccount = false;

            if ($facebookUser->is_account_blocked()) {
                $this->ajax->errors->facebookLoginError = "Your account is blocked. For any questions write to support@fordrive.net";
                $this->ajax->render();
            }
        } else {
            $isFirstLoginFromFBAccount = true;
            $facebookUser = $facebookUserModel;
        }

        // Should update all properties and save in any case(may change on Facebook acc)
        $facebookUser->username = $facebookUsername;

        if ($isFirstLoginFromFBAccount) {
            $facebookUser->rank = $facebookUser->find_max("rank") + 1;

            $subname = "";
            // if(strlen($facebookUserHometown) > 0)
            //  $subname .= "Hometown: " . $facebookUserHometown. " ";

            if (strlen($facebookUserLocation) > 0)
                //$subname .= "Location: " . $facebookUserLocation;
                $subname = $facebookUserLocation;

            $facebookUser->subname = $subname;

            $facebookUser->email = "facebook";
            $facebookUser->activated = "yes";
            $facebookUser->blocked = "no";
            $facebookUser->type = $facebookUser::ACCOUNT_TYPE_FACEBOOK;
            $facebookUser->facebook_id = $facebookUserId;
            $facebookUser->registred_on = strftime("%Y-%m-%d %H:%M:%S", time());

            $facebookUser->avatar_master_name = $masterPhotoName;
            if ($hasFacebookUserProfilePicture)
                $facebookUser->move_clones();
        }

        $facebookUser->save(false, true);
        $this->session->login($facebookUser);

        $this->ajax->result = "ok";

        if ($isFirstLoginFromFBAccount) {
            $this->ajax->callback = "redirect";
            $this->ajax->data->url_segments = "about";
        } else
            $this->ajax->callback = "refresh";

        $this->ajax->render();
    }

    private function isNotDefaultFacebookProfilePicture($facebookUserId)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => "http://graph.facebook.com/$facebookUserId/picture"));
        $headers = explode("\n", curl_exec($curl));
        curl_close($curl);

        $headersStr = implode(" ", $headers);
        return (strrpos($headersStr, $facebookUserId) === false) ? false : true;
    }

    public function logout($request_type = "")
    {
        $this->is_ajax($request_type);
        $this->session->logout();

        $this->ajax->result = "ok";
        $this->ajax->callback = "redirect";
        $this->ajax->data->url_segments = "main";

        $this->ajax->render();
    }
}

?>