<?php
    class Admin_Settings_Controller extends Admin_Controller
    {
        public function recalculate_users_rank($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            $user_rank_mapper = new User_Rank_Mapper;
            $user_rank_mapper->recalculate();
            $this->settings->save_that_rating_was_recalculated();

            $this->ajax->callback = "update_settings_last_rating_update";
            $this->ajax->result   = "ok";
            $this->ajax->render();
        }

        public function change_site_status($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            $this->settings->change_site_status();

            if($this->settings->mode == "enabled")
            {
                $this->ajax->data->new_status = "online";
            }
            else
            {
                $this->ajax->data->new_status = "offline";
            }

            $this->ajax->callback = "update_settings_site_mode";
            $this->ajax->result   = "ok";

            $this->ajax->render();
        }

        public function clear_ajax_directory($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            if($directory_handle = opendir(UPLOADS_AJAX))
            {
                while(false !== ($filename = readdir($directory_handle)))
                {
                    if($filename != "." and $filename != "..")
                    {
                        $file_to_delete_path = UPLOADS_AJAX . $filename;

                        if(file_exists($file_to_delete_path))
                            unlink($file_to_delete_path);
                    }
                }

                closedir($directory_handle);
            }

            $this->ajax->result   = "ok";
            $this->ajax->callback = "update_settings_clear_ajax_files_count";
            $this->ajax->render();
        }

        public function clear_cache_directory($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            if($directory_handle = opendir(UPLOADS_CACHE))
            {
                while(false !== ($filename = readdir($directory_handle)))
                {
                    if($filename != "." and $filename != "..")
                    {
                        $file_to_delete_path = UPLOADS_CACHE . $filename;

                        if(file_exists($file_to_delete_path))
                            unlink($file_to_delete_path);
                    }
                }

                closedir($directory_handle);
            }

            $this->ajax->result   = "ok";
            $this->ajax->callback = "update_settings_clear_cache_files_count";
            $this->ajax->render();
        }

        public function delete_unactivated_accounts($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            $user_model = new User_Model;
            $user_model->delete_unactivated_users_more_than_in_n_days(3);

            $this->ajax->result   = "ok";
            $this->ajax->callback = "update_settings_unactivated_accounts_count";
            $this->ajax->render();
        }


        public function compile_resources($request_type = "")
        {
            $this->is_ajax($request_type);
            $this->validate_token();

            try {
                $resources_compiler = new Resources_Compiler();
                $resources_compiler->compile_resources();
            }
            catch( Exception $e )
            {
                $this->ajax->errors->compile_error = $e->getMessage();
                $this->ajax->render();
            }

            $this->ajax->result   = "ok";
            $this->ajax->callback = "update_resources_compiled_message";
            $this->ajax->render();
        }
    }
?>