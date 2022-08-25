<?php

class Video_Photo_Model extends Model
{
    protected $table_name = "videos_photos";
    protected $db_fields = array("id", "video_id", "master_name", "main");

    public $id;
    public $video_id;
    public $master_name;
    public $main;

    // Shared attributes
    public $directory;
    public $frame_action;
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

    // Fetching main photo
    public function find_main_photo_on($video_id)
    {
        $sql = "WHERE video_id = %d   ";
        $sql .= "  AND main     = 'yes'";

        $sql = sprintf($sql,
            $this->database->escape_value($video_id));

        return $this->find_by_condition($sql);
    }

    public function find_photos_on($video_id,
                                   $fetch_main = true)
    {
        $sql = "WHERE video_id = %d ";

        if (!$fetch_main)
            $sql .= "AND main = 'no'  ";

        $sql .= "ORDER BY id         ";

        $sql = sprintf($sql,
            $this->database->escape_value($video_id));

        return $this->find_all($sql);
    }

    // Unpacking action,which will execute on this photo
    public function unpack_frame($frame)
    {
        $frame_actions = array("ajax", "deleteajax", "delete");
        $frame_parts = explode("-", $frame);

        if (in_array($frame_parts[0], $frame_actions)) {
            $this->frame_action = $frame_parts[0];
            array_shift($frame_parts);
            $this->master_name = implode("-", $frame_parts);
        } else {
            $this->frame_action = "none";
        }
    }

    // Unpacks directory from master_name
    public function unpack_directory()
    {
        $master_name_parts = explode("-", $this->master_name);
        $this->directory = $master_name_parts[0];
    }

    private function move_clones()
    {
        $this->unpack_directory();

        foreach ($this->clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $ajax_path = UPLOADS_AJAX . $this->master_name . $sizes . ".jpg";
            $images_path = UPLOADS_IMAGES . $this->directory . DS;
            $images_path .= $this->master_name . $sizes . ".jpg";

            if (copy($ajax_path, $images_path)) {
                unlink($ajax_path);
            }
        }
    }

    // Move clones in images folder
    // and saves record in database.
    public function save_with_clones()
    {
        $this->move_clones();
        $this->main = "no";
        $this->save();
    }

    // Deletes temp images in /ajax directory, which
    // was uploaded,but then deleted.
    public function delete_ajax()
    {
        foreach ($this->clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $ajax_path = UPLOADS_AJAX . $this->master_name;
            $ajax_path .= $sizes . ".jpg";

            if (file_exists($ajax_path)) {
                unlink($ajax_path);
            }
        }
    }

    private function delete_clones()
    {
        $this->unpack_directory();

        foreach ($this->clones as $clone) {
            $sizes = "-" . $clone["width"];
            $sizes .= "-" . $clone["height"];

            $images_path = UPLOADS_IMAGES . $this->directory . DS;
            $images_path .= $this->master_name . $sizes . ".jpg";

            if (file_exists($images_path)) {
                unlink($images_path);
            }
        }
    }

    // Deletes images from /images/dir and db record
    public function delete_with_clones()
    {
        $this->delete_clones();

        // Deleting db record
        $sql = "WHERE master_name = '%s'";
        $sql .= "  AND video_id    = %d  ";

        $sql = sprintf($sql,
            $this->database->escape_value($this->master_name),
            $this->database->escape_value($this->video_id));

        $this->delete_by_condition($sql);
    }

    // Updates main photo
    public function update_main()
    {
        // Unsetting current main,if it exists
        $sql = "WHERE video_id = %d  ";
        $sql .= "  AND main    = 'yes'";

        $sql = sprintf($sql,
            $this->database->escape_value($this->video_id));

        $current_main = $this->find_by_condition($sql);

        if ($current_main) {
            $current_main->main = "no";
            $current_main->save();
        }

        // Setting new main
        $sql = "WHERE video_id    = %d  ";
        $sql .= "  AND master_name = '%s'";

        $sql = sprintf($sql,
            $this->database->escape_value($this->video_id),
            $this->database->escape_value($this->master_name));

        $new_main = $this->find_by_condition($sql);
        $new_main->main = "yes";
        $new_main->save();
    }

    public function get_validation_rules()
    {
        // Rules
    }
}

?>