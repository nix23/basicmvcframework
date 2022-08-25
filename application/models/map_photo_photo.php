<?php

class Map_Photo_Photo_Model extends Model
{
    protected $table_name = "map_photos_photos";
    protected $db_fields = array("id", "photo_id", "master_name", "main",
        "lazy_clone_greatest_width", "lazy_clone_greatest_height");

    public $id;
    public $photo_id;
    public $master_name;
    public $main;
    public $lazy_clone_greatest_width;
    public $lazy_clone_greatest_height;

    public $clones = array(
        array("width" => 800, "height" => 520), // Gallery large photo
        array("width" => 380, "height" => 245), // Module item main photo
        array("width" => 330, "height" => 210), // Most active posts big photo
        array("width" => 270, "height" => 180), // Module item large photo
        array("width" => 135, "height" => 100), // Most active posts small photo
        array("width" => 130, "height" => 90),  // View item small photo
        array("width" => 145, "height" => 95),  // Follow/activity photo
        array("width" => 100, "height" => 75),  // Module item small photo
        array("width" => 80, "height" => 60),  // Backend list photo
        array("width" => 40, "height" => 30)   // Main fordrivers small item photo
    );

    public function find_on($photo_id)
    {
        $sql = "WHERE photo_id = $photo_id";
        return $this->find_all($sql);
    }

    private function make_upload_directory()
    {
        $current_upload_directory = Registry::get('settings')->current_upload_directory;

        $current_upload_directory_fs_path = UPLOADS_IMAGES;
        $current_upload_directory_fs_path .= $current_upload_directory . DS;

        $files_count_in_current_upload_directory = 0;

        if ($directory_handle = opendir($current_upload_directory_fs_path)) {
            while (false !== ($filename = readdir($directory_handle))) {
                if ($filename != "." and $filename != "..")
                    $files_count_in_current_upload_directory++;
            }

            closedir($directory_handle);
        }

        if ($files_count_in_current_upload_directory
            >
            Registry::get('config')->max_files_count_per_uploads_directory) {
            $current_upload_directory++;
            $next_upload_directory_fs_path = UPLOADS_IMAGES;
            $next_upload_directory_fs_path .= $current_upload_directory . DS;

            if (!file_exists($next_upload_directory_fs_path))
                mkdir($next_upload_directory_fs_path, 0700);

            $settings = Registry::get('settings');
            $settings->current_upload_directory = $current_upload_directory;
            $settings->save();
        }

        return $current_upload_directory;
    }

    private function move_master_photo_with_clones_in_upload_dir($upload_directory)
    {
        // Moving master photo
        $tmp_path = UPLOADS_TMP . $this->master_name . ".jpg";
        $images_path = UPLOADS_IMAGES . $upload_directory . DS;
        $images_path .= $upload_directory . "-" . $this->master_name . ".jpg";

        if (copy($tmp_path, $images_path)) {
            unlink($tmp_path);
        }

        // Moving clones
        foreach ($this->clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $tmp_path = UPLOADS_TMP . $this->master_name . $sizes . ".jpg";
            $images_path = UPLOADS_IMAGES . $upload_directory . DS;
            $images_path .= $upload_directory . "-" . $this->master_name . $sizes . ".jpg";

            if (copy($tmp_path, $images_path)) {
                unlink($tmp_path);
            }
        }
    }

    public function save_photo_on($photoset_id)
    {
        $photoset_photo_model = new Photo_Photo_Model;

        $upload_directory = $this->make_upload_directory();

        $this->move_master_photo_with_clones_in_upload_dir($upload_directory);

        $photoset_photo_model->photo_id = $photoset_id;
        $photoset_photo_model->master_name = $upload_directory . "-" . $this->master_name;
        $photoset_photo_model->main = $this->main;
        $photoset_photo_model->lazy_clone_greatest_width = $this->lazy_clone_greatest_width;
        $photoset_photo_model->lazy_clone_greatest_height = $this->lazy_clone_greatest_height;

        $photoset_photo_model->save();
    }
}

?>