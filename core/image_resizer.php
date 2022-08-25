<?php
    class Image_Resizer
    {
        public  $image;
        private $image_resized;
        public  $extension;
        public  $width;
        public  $height;
        
        public function load($image_path)
        { 
            $extension = pathinfo($image_path, PATHINFO_EXTENSION); 
            
            switch($extension)
            {
                case 'jpg':
                case 'jpeg':
                    $this->image     = @imagecreatefromjpeg($image_path);
                    $this->extension = "jpg";
                    break;
                case 'gif':
                    $this->image     = @imagecreatefromgif($image_path);
                    $this->extension = "gif";
                    break;
                case 'png':
                    $this->image     = @imagecreatefrompng($image_path);
                    $this->extension = "png";
                    break;
                default:
                    exit('Wrong img file passed to Image_Resizer.');
                    break;
            }
            
            if(gettype($this->image) == "resource")
            {
                $this->width  = imagesx($this->image);
                $this->height = imagesy($this->image);
                return true;
            }
            else
            {
                return false;
            }
        }
        
        // Places watermark on resized image
        public function watermark_image($target_width, $target_height)
        {
            $watermark_image_path = RESOURCES . "watermark_{$target_width}_{$target_height}.png"; 
            $watermark            = new Image_Resizer();
            
            $watermark->load($watermark_image_path); 
            
            imagealphablending($this->image_resized, true);
            imagealphablending($watermark->image,    true);
            
            $watermark_x = $target_width - $watermark->width - 0;
            $watermark_y = $target_height - $watermark->height - 0;
            
            imagecopy($this->image_resized, $watermark->image, $watermark_x, $watermark_y, 0, 0, $watermark->width, $watermark->height);
        }
        
        public function resize_image($new_width, $new_height, $option="auto")
        {
            $option_array = $this->get_dimensions($new_width, $new_height, strtolower($option));
            
            $optimal_width  = $option_array['optimal_width'];
            $optimal_height = $option_array['optimal_height'];
            
            $this->image_resized = imagecreatetruecolor($optimal_width, $optimal_height);
            imagecopyresampled($this->image_resized, $this->image, 0, 0, 0, 0, $optimal_width, $optimal_height, $this->width, $this->height);
            
            if($option == 'crop')
            {
                $this->crop($optimal_width, $optimal_height, $new_width, $new_height);
            }
        }
        
        private function get_dimensions($new_width, $new_height, $option)
        {
            switch($option)
            {
                case 'exact':
                    $optimal_width  = $new_width;
                    $optimal_height = $new_height;
                break;
                case 'portrait':
                    $optimal_width  = $this->get_size_by_fixed_height($new_height);
                    $optimal_height = $new_height;
                break;
                case 'landscape':
                    $optimal_width  = $new_width;
                    $optimal_height = $this->get_size_by_fixed_width($new_width);
                break;
                case 'auto':
                    $option_array   = $this->get_size_by_auto($new_width, $new_height);
                    $optimal_width  = $option_array['optimal_width'];
                    $optimal_height = $option_array['optimal_height'];
                break;
                case 'crop':
                    $option_array   = $this->get_optimal_crop($new_width, $new_height);
                    $optimal_width  = $option_array['optimal_width'];
                    $optimal_height = $option_array['optimal_height'];
                break;
            }
            
            return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
        }
        
        private function get_size_by_fixed_height($new_height)
        {
            $ratio     = $this->width / $this->height;
            $new_width = $new_height * $ratio;
            
            return $new_width;
        }
        
        private function get_size_by_fixed_width($new_width)
        {
            $ratio      = $this->height / $this->width;
            $new_height = $new_width * $ratio;
            
            return $new_height;
        }
        
        private function get_size_by_auto($new_width, $new_height)
        {
            // Image to be resized is wider (landscape)
            if($this->height < $this->width)
            {
                $optimal_width  = $new_width;
                $optimal_height = $this->get_size_by_fixed_width($new_width);
            }
            // Image to be resized is taller (portrait) 
            else if($this->height > $this->width)
            {
                $optimal_width  = $this->get_size_by_fixed_height($new_height);
                $optimal_height = $new_height;
            }
            // Image to be resized is a square
            else
            {
                if($new_height < $new_width)
                {
                    $optimal_width  = $new_width;
                    $optimal_height = $this->get_size_by_fixed_width($new_width);
                }
                else if($new_height > $new_width)
                {
                    $optimal_width  = $this->get_size_by_fixed_height($new_height);
                    $optimal_height = $new_height;
                }
                // Square being resized to a square 
                else
                {
                    $optimal_width  = $new_width;
                    $optimal_height = $new_height;
                }
            }
            
            return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
        }
        
        private function get_optimal_crop($new_width, $new_height)
        {
            // Finding relation of width and height
            $height_ratio = $this->height / $new_height;
            $width_ratio  = $this->width / $new_width;
            
            // We are taking optimal relation for ratio in this case.
            // Optimal will be smallest ratio,because with largest ratio
            // image will be croped to much in beginning. We want to crop
            // image maximum close to new sizes,and then with function
            // 'crop' take off part of image,which exceeds it.
            if ($height_ratio < $width_ratio)
            {
                $optimal_ratio = $height_ratio;
            }
            else
            {
                $optimal_ratio = $width_ratio;
            }
            
            // Getting parametrs of resized image. In real in this place we are
            // decreasing width and height of image simultaneously to some optimal
            // value. We are diving width and height to ratio for proportional
            // decreasing.(no distortion)
            $optimal_height = $this->height / $optimal_ratio;
            $optimal_width  = $this->width  / $optimal_ratio;
            
            return array('optimal_width' => $optimal_width, 'optimal_height' => $optimal_height);
        }
        
        private function crop($optimal_width, $optimal_height, $new_width, $new_height)
        {
            // Find center - this will be used for the crop
            // Crop will be maded by specified width '$new_width' by center of '$optimal_width'
            // and specified height '$new_height' by center of '$optimal_height'
            $cropstart_x = ($optimal_width / 2) - ($new_width / 2);
            $cropstart_y = ($optimal_height / 2) - ($new_height / 2);
            
            $crop = $this->image_resized;
            
            // Now crop from center to exact requested size
            $this->image_resized = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($this->image_resized, $crop, 0, 0, $cropstart_x, $cropstart_y, $new_width, $new_height , $new_width, $new_height);
        }
        
        public function save_auto($save_path, $image_quality = "75")
        {
            switch($this->extension)
            {
                case 'jpg':
                    $this->save_as_jpg($save_path, $image_quality);
                break;
                case 'gif':
                    $this->save_as_gif($save_path);
                break;
                case 'png':
                    $this->save_as_png($save_path, $image_quality);
                break;
                default:
                    exit('Wrong Image Resize save extension.');
                break;
            }
        }

        public function capture_as_jpg($image_quality = "75")
        {
            ob_start();
            imagejpeg($this->image_resized, NULL, $image_quality);

            return ob_get_clean();
        }

        public function save_original($full_save_path, $image_quality = "75")
        {
            if(imagetypes() & IMG_JPG)
            {
                imagejpeg($this->image, $full_save_path, $image_quality);
            }
        }
        
        public function save_as_jpg($save_path, $image_quality = "75")
        {
            if(imagetypes() & IMG_JPG)
            {
                imagejpeg($this->image_resized, $save_path . ".jpg", $image_quality);
            }
        }
        
        public function save_as_gif($save_path)
        {
            if(imagetypes() & IMG_GIF)
            {
                imagegif($this->image_resized, $save_path . ".gif");
            }
        }
        
        public function save_as_png($save_path, $image_quality = "75")
        {
            // Scale quality from 0-100 to 0-9  
            $scale_quality = round(($image_quality / 100) * 9);  
            
            // Invert quality setting as 0 is best, not 9  
            $invert_scale_quality = 9 - $scale_quality;  
            
            if(imagetypes() & IMG_PNG)
            {  
                imagepng($this->image_resized, $save_path . ".png", $invert_scale_quality);  
            }  
        }
    }
?>