<?php

// Upload images to application
class Fordrive_Uploader extends Uploader
{
    protected $upload_directory;
    protected $min_width;
    protected $min_height;
    protected $uploaded_photo;
    protected $clones = array();
    protected $original_photo_path;
    protected $master_photo_path;
    public $master_photo_name;
    protected $settings;
    protected $config;

    // Don't forget to specify list of allowed extensions
    public function __construct($min_width, $min_height)
    {
        $this->min_width = $min_width;
        $this->min_height = $min_height;

        $this->allowed_extensions[] = 'jpeg';
        $this->allowed_extensions[] = 'jpg';
        $this->allowed_extensions[] = 'gif';
        $this->allowed_extensions[] = 'png';

        $this->settings = Registry::get('settings');
        $this->config = Registry::get('config');
    }

    private function make_upload_directory()
    {
        $current_upload_directory_fs_path = UPLOADS_IMAGES;
        $current_upload_directory_fs_path .= $this->settings->current_upload_directory . DS;

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
            $this->config->max_files_count_per_uploads_directory) {
            $next_upload_directory_fs_path = UPLOADS_IMAGES;
            $next_upload_directory_fs_path .= ++$this->settings->current_upload_directory . DS;

            if (!file_exists($next_upload_directory_fs_path))
                mkdir($next_upload_directory_fs_path, 0700);

            $this->settings->save();
        }

        $this->upload_directory = $this->settings->current_upload_directory;
    }

    private function make_original_photo_path()
    {
        do {
            $unique_photo_id = uniqid() . mt_rand(0, 99999);
            $original_photo_name = $this->upload_directory . "-" . $unique_photo_id;

            $this->original_photo_path = UPLOADS_AJAX . $original_photo_name;
            $this->original_photo_path .= "-copy." . $this->extension;
        } while (file_exists($this->original_photo_path));
    }

    /**
     * Returns generated master_photo_name,which consists of
     * directory name,in which this batch of photos will be
     * moved after submitting the form(packed as first segment),
     * followed by separator "-" and unique photo id.
     **/
    private function make_master_photo_name()
    {
        do {
            $unique_photo_id = uniqid() . mt_rand(0, 99999);
            $this->master_photo_name = $this->upload_directory . "-" . $unique_photo_id;
            $this->master_photo_path = UPLOADS_AJAX . $this->master_photo_name . ".jpg";
        } while (file_exists($this->master_photo_path));
    }

    // Deletes photo from 'ajax' directory
    private function delete_uploaded_photo($full_photo_path)
    {
        unlink($full_photo_path);
    }

    // Deletes master photo,if necessary.
    // (After all clones are created)
    public function delete_master_photo()
    {
        $this->delete_uploaded_photo($this->master_photo_path);
    }

    private function is_greater_than_min_size()
    {
        if ($this->uploaded_photo->width < $this->min_width
            ||
            $this->uploaded_photo->height < $this->min_height) {
            $message = "Min size of image: " . $this->min_width;
            $message .= " * " . $this->min_height . "px";

            $this->error = $message;
            $this->delete_uploaded_photo($this->master_photo_path);

            return false;
        } else {
            return true;
        }
    }

    private function make_clones()
    {
        foreach ($this->clones as $clone) {
            // Format: dirname-mastername-width-height.jpg
            $clone_name = "";
            $clone_name .= $this->master_photo_name;
            $clone_name .= "-" . $clone["width"];
            $clone_name .= "-" . $clone["height"];

            $this->uploaded_photo->resize_image($clone["width"], $clone["height"], "crop");
            $this->uploaded_photo->save_as_jpg(UPLOADS_AJAX . $clone_name);
        }
    }

    private function save_master_photo_sizes_in_session()
    {
        $session = Registry::get('session');
        $session->set_master_photo_sizes($this->master_photo_name,
            $this->uploaded_photo->width,
            $this->uploaded_photo->height);
    }

    /**
     * Uploads photo,and than saves following images in 'ajax' directory:
     *   -Image with original uploaded image resolution(master_photo)
     *   -All image copies with resolutions provided in array(clones)
     *
     * Photos moving to 'images' directory is running only when form is
     * submitting. Don't forget to clear 'ajax' directory sometimes.
     **/
    public function upload_photo($clones = array(),
                                 $save_master_photo_sizes_in_session = true)
    {
        $this->createUploadDirectories($clones);

        // Moving uploaded file to 'ajax' folder
        if (move_uploaded_file($this->tmp_name, $this->original_photo_path)) {
            return $this->createClones();
        } else {
            $message = "The file upload failed, possibly due to incorrect";
            $message .= " permissions on the upload folder.";

            $this->error = $message;
            return false;
        }
    }

    public function createUploadedImageFromBinaryString($fileBinaryDataString)
    {
        $uploadedImage = imagecreatefromstring($fileBinaryDataString);
        imagejpeg($uploadedImage, $this->original_photo_path, 75);
    }

    public function createUploadDirectories($clones = array())
    {
        $this->clones = $clones;
        $this->make_upload_directory();
        $this->make_original_photo_path();
    }

    public function createClones($save_master_photo_sizes_in_session = true)
    {
        $this->uploaded_photo = new Image_Resizer;

        if ($this->uploaded_photo->load($this->original_photo_path)) {
            $this->make_master_photo_name();
            $this->uploaded_photo->save_original($this->master_photo_path);
            $this->delete_uploaded_photo($this->original_photo_path);
            $this->uploaded_photo->load($this->master_photo_path);

            // Check,that photo is not to small
            if ($this->is_greater_than_min_size()) {
                $this->make_clones();
                if ($save_master_photo_sizes_in_session)
                    $this->save_master_photo_sizes_in_session();

                return true;
            } else {
                return false;
            }
        } else {
            $this->error = "Image upload failed: unexcepted end of Image. ";
            $this->error .= "(Looks like it is broken)";
            return false;
        }
    }
}

?>