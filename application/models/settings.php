<?php
    class Settings_Model extends Model
    {
        protected $table_name = "settings";
        protected $db_fields  = array("id", "name", "last_item_views_pack", "last_rating_update",
                                                "mode", "offline_image_binary", "support_email", "current_upload_directory");

        public $id;
        public $name;
        public $last_item_views_pack;
        public $last_rating_update;
        public $mode;
        public $offline_image_binary;
        public $support_email;
        public $current_upload_directory;

        public function find_settings_by_name($name = "main_settings")
        {
            // Fetching everything except blob images
            // (They are fetched only when it's required)
            $db_fields_except_blobs = array();

            foreach($this->db_fields as $db_field)
            {
                if($db_field != "offline_image_binary")
                    $db_fields_except_blobs[] = $db_field;
            }

            $sql = " WHERE name = '%s' ";
            $sql = sprintf($sql,
                                $this->database->escape_value($name));

            return $this->find_by_condition($sql,
                                                      implode(",", $db_fields_except_blobs));
        }

        public function move_item_views_to_module_stats()
        {
            if(Datetime_Converter::is_datetime_older_than_n_hours($this->last_item_views_pack, 1))
            {
                $item_view_model = new Item_View_Model;
                $item_view_model->move_item_views_to_module_stats();

                $this->last_item_views_pack = strftime("%Y-%m-%d %H:%M:%S", time());
                $this->update_only(array("last_item_views_pack"));
            }
        }

        public function save_that_rating_was_recalculated()
        {
            $this->last_rating_update = strftime("%Y-%m-%d %H:%M:%S", time());
            $this->update_only(array("last_rating_update"));
        }

        public function change_site_status()
        {
            if($this->mode == "enabled")
            {
                $this->mode = "disabled";
            }
            else
            {
                $this->mode = "enabled";
            }

            $this->update_only(array("mode"));
        }
    }
?>