<?php
    /** 
    * Base class of file uploader. All file uploaders
    * should be inherited from this class.
    **/
    abstract class Uploader
    {
        // $_FILES['filename'] attributes
        protected $name;
        protected $type;
        protected $size;
        protected $tmp_name;
        
        // Unpacked file extension
        protected $extension;
        
        // Fill this array with allowed extensions
        // in subclass constructor
        protected $allowed_extensions = array();
        
        protected $upload_errors = array(
            UPLOAD_ERR_OK               => "No errors.",
            UPLOAD_ERR_INI_SIZE     => "File is larger than upload_max_filesize.",
            UPLOAD_ERR_FORM_SIZE    => "File is larger than form MAX_FILE_SIZE.",
            UPLOAD_ERR_PARTIAL      => "File was not fully uploaded.",
            UPLOAD_ERR_NO_FILE      => "File was not uploaded.",
            UPLOAD_ERR_NO_TMP_DIR   => "No temporary directory for file upload.",
            UPLOAD_ERR_CANT_WRITE   => "Can't write file to disk."
            //UPLOAD_ERR_EXTENSION  => "File upload stopped by extension."
        );
        
        public $error;
        
        // Checks that file exists in $_FILES array
        public function is_file_uploaded($upload_filename)
        {
            if(isset($_FILES[$upload_filename]))
            {
                return true;
            }
            else
            {
                $error  = "The file upload failed, possibly due exceeding";
                $error .= " maximum file size(" . MAX_FILE_SIZE . ").";
                
                $this->error = $error;
                return false;
            }
        }
        
        // Pass in $_FILES['name'] as an argument
        public function attach_file($file)
        {
            // Check if file passed
            if(!$file || empty($file['name']) || !is_array($file))
            {
                $this->error = "Please select a file for upload.";
                return false;
            }
            // Check for built-in errors
            else if($file['error'] != 0)
            {
                $this->error = $this->upload_errors[$file['error']];
                return false;
            }
            else
            {
                $this->name     = strtolower(basename($file['name']));
                $this->type     = $file['type'];
                $this->size     = $file['size'];
                $this->tmp_name = $file['tmp_name'];
                
                if($this->has_valid_extension())
                {
                    return true;
                }
                else
                {
                    $extensions   = join(", ", $this->allowed_extensions);
                    $this->error = "Only this extensions are allowed: " . $extensions;
                    
                    return false;
                }
            }
        }
        
        public function setExtension($extension)
        {
            $this->extension = $extension;
        }
        
        // Checks if uploaded file has valid extension
        protected function has_valid_extension()
        {
            $this->extension = pathinfo($this->name, PATHINFO_EXTENSION);
            return (in_array($this->extension, $this->allowed_extensions)) ? true : false;
        }
    }
?>