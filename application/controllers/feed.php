<?php
    class Feed_Controller extends Public_Controller
    {   
        public function getMostActivePostsData($callbackFunction)
        {
            $this->render_layout = false;

            $photoset_model = new Photo_Model;
            $spot_model = new Spot_Model;
            $speed_model = new Speed_Model;
            $video_model = new Video_Model;

            $most_active_posts_mapper = new Most_Active_Posts_Mapper;
            $all_most_active_posts = $most_active_posts_mapper->find_most_active_posts_in_last(24);

            if(count($all_most_active_posts) < 9)
                $all_most_active_posts = $most_active_posts_mapper->find_most_active_posts_in_last(24 * 7);

            $most_active_posts_data = array();
            $most_active_posts_count = 0;
            foreach($all_most_active_posts as $most_active_post)
            {
                switch($most_active_post["module_name"])
                {
                    case "photos":
                        $label = "Photosets";
                        $module_model = &$photoset_model;
                    break;

                    case "spots":
                        $label = "Spots";
                        $module_model = &$spot_model;
                    break;

                    case "speed":
                        $label = "Speed";
                        $module_model = &$speed_model;
                    break;

                    case "videos":
                        $label = "Videos";
                        $module_model = &$video_model;
                    break;
                }

                $post = $module_model->find_by_id($most_active_post["module_item_id"]);
                $post->find_category_and_subcategory();
                $post->find_main_photo();
                $post->find_comments_count();
                $post->find_likes_count();

                ob_start();
                render_most_active_module_item_link($post,
                                                    $most_active_post["module_name"]);
                $linkText = ob_get_clean();

                ob_start();
                load_photo($post->main_photo->master_name, 135, 100);
                $imagePath = ob_get_clean();

                $most_active_posts_data[] = array(
                    "id" => $post->id,
                    "type" => $most_active_post["module_name"],
                    "moduleName" => $label,
                    "commentsCount" => $post->comments_total_count,
                    "likesCount" => $post->likes_count,
                    "label" => $post->get_full_heading(),
                    "linkText" => $linkText,
                    "imagePath" => $imagePath
                );

                $most_active_posts_count++;
                if($most_active_posts_count == 9)
                    break;
            }

            $this->ajax->result   = "ok";
            $this->ajax->callback = $callbackFunction;
            $this->ajax->data->mostActivePostsData = $most_active_posts_data;
            $this->ajax->render();
        }

        private function formatPost($module_name, $post, $label)
        {
            ob_start();
            render_most_active_module_item_link($post, $module_name);
            $linkText = ob_get_clean();

            ob_start();
            load_photo($post->main_photo_master_name, 135, 100);
            $imagePath = ob_get_clean();

            $postData = array(
                "id" => $post->id,
                "type" => $module_name,
                "moduleName" => $label,
                "commentsCount" => $post->comments_count,
                "label" => $post->get_full_heading(),
                "linkText" => $linkText,
                "imagePath" => $imagePath
            );

            return $postData;
        }

        public function getLastPostsData($callbackFunction)
        {
            $this->render_layout = false;
            $last_posts_data = array();
            $posts = array();

            $photoset_model = new Photo_Model;
            $spot_model = new Spot_Model;
            $speed_model = new Speed_Model;
            $video_model = new Video_Model;

            $photosets = $photoset_model->find_n_last_approved_photosets(4, true, true);
            $spots = $spot_model->find_n_last_approved_spots(4, true, true);
            $speeds = $speed_model->find_n_last_approved_speeds(4, true, true, true, true);
            $videos = $video_model->find_n_last_approved_videos(4, true, true, true, true);

            foreach($photosets as $photoset)
            {
                $photoset->find_category_and_subcategory();
                $last_posts_data[] = $this->formatPost("photos", $photoset, "Photosets");
            }

            foreach($spots as $spot)
            {
                $spot->find_category_and_subcategory();
                $last_posts_data[] = $this->formatPost("spots", $spot, "Spots");
            }

            foreach($speeds as $speed)
            {
                $speed->find_category_and_subcategory();
                $last_posts_data[] = $this->formatPost("speed", $speed, "Speed");
            }

            foreach($videos as $video)
            {
                $video->find_category_and_subcategory();
                $last_posts_data[] = $this->formatPost("videos", $video, "Videos");
            }

            $this->ajax->result   = "ok";
            $this->ajax->callback = $callbackFunction;
            $this->ajax->data->lastPostsData = $last_posts_data;
            $this->ajax->render();
        }

        private function findLinkedPostsByCategory($categoryIds,
                                                   $photoset_model,
                                                   $spot_model,
                                                   $speed_model,
                                                   $video_model,
                                                   $linked_posts,
                                                   $linked_posts_count)
        {
            $linked_photosets = $photoset_model->find_all_photosets(
                $categoryIds, 1, "year", "DESC", false, true, true, false, $linked_posts_count
            );
            
            $linked_spots = $spot_model->find_all_spots(
                $categoryIds, 1, "capture_year", "DESC", "posted_on", false, true, true, false, $linked_posts_count
            );

            $linked_speeds = $speed_model->find_all_speeds(
                $categoryIds, 1, "posted_on", "DESC", false, true, true, false, $linked_posts_count
            );

            $linked_videos = $video_model->find_all_videos(
                $categoryIds, 1, "posted_on", "DESC", false, true, true, false, $linked_posts_count
            );

            $linked_photosets_count = count($linked_photosets);
            $linked_spots_count = count($linked_spots);
            $linked_speeds_count = count($linked_speeds);
            $linked_videos_count = count($linked_videos);

            $max_items_count = $linked_photosets_count;
            if($linked_spots_count > $max_items_count) $max_items_count = $linked_spots_count;
            if($linked_speeds_count > $max_items_count) $max_items_count = $linked_speeds_count;
            if($linked_videos_count > $max_items_count) $max_items_count = $linked_videos_count;

            for($current_item = 0; $current_item < $max_items_count; $current_item++)
            {
                if($current_item < $linked_photosets_count && count($linked_posts) < $linked_posts_count)
                    $linked_posts[] = $linked_photosets[$current_item];

                if($current_item < $linked_spots_count && count($linked_posts) < $linked_posts_count)
                    $linked_posts[] = $linked_spots[$current_item];

                if($current_item < $linked_speeds_count && count($linked_posts) < $linked_posts_count)
                    $linked_posts[] = $linked_speeds[$current_item];

                if($current_item < $linked_videos_count && count($linked_posts) < $linked_posts_count)
                    $linked_videos[] = $linked_videos[$current_item];

                if(count($linked_posts) >= $linked_posts_count)
                    break;
            }

            foreach($linked_posts as $linked_post)
            {
                $linked_post->find_category_and_subcategory();
                $linked_post->find_main_photo();
                $linked_post->find_comments_count();
                $linked_post->find_likes_count();
            }

            return $linked_posts;
        }

        public function getLinkedPostsData($module = "", $item_id = null, $callbackFunction)
        {
            $this->render_layout = false;

            if(!$item_id || !is_numeric($item_id))
            {
                $error = new Error_Controller;
                $error->show_404();
            }

            $modules = array("photos", "spots", "speed", "videos");
            if(!in_array($module, $modules))
            {
                $error = new Error_Controller;
                $error->show_404();
            }

            $photoset_model = new Photo_Model;
            $spot_model = new Spot_Model;
            $speed_model = new Speed_Model;
            $video_model = new Video_Model;

            $item_model = null;
            if($module == "photos")
                $item_model = $photoset_model;
            else if($module == "spots")
                $item_model = $spot_model;
            else if($module == "speed")
                $item_model = $speed_model;
            else if($module == "videos")
                $item_model = $video_model;

            $item = $item_model->find_by_id($item_id);
            if(!$item)
            {
                $error = new Error_Controller;
                $error->show_404();
            }

            $linked_posts = array();
            $linked_posts_count = 16;
            $item->find_category_and_subcategory();

            if($item->subcategory)
            {
                $linked_posts = $this->findLinkedPostsByCategory(array($item->subcategory->id),
                                                                 $photoset_model,
                                                                 $spot_model,
                                                                 $speed_model,
                                                                 $video_model,
                                                                 $linked_posts,
                                                                 $linked_posts_count);
            }

            if(count($linked_posts) < $linked_posts_count)
            {
                $item->category->find_subcategories(false, false, false, false, false, false);
                $subcategoryIds = array();
                foreach($item->category->subcategories as $subcategory)
                {
                    if($item->subcategory)
                    {
                        if($subcategory->id != $item->subcategory->id)
                            $subcategoryIds[] = $subcategory->id;
                    }
                    else
                        $subcategoryIds[] = $subcategory->id;
                }

                $linked_posts = $this->findLinkedPostsByCategory($subcategoryIds,
                                                                 $photoset_model,
                                                                 $spot_model,
                                                                 $speed_model,
                                                                 $video_model,
                                                                 $linked_posts,
                                                                 $linked_posts_count);
            }

            $formatted_linked_posts = array();
            foreach($linked_posts as $linked_post)
            {
                $linked_post->find_category_and_subcategory();
                $linked_post->find_main_photo();
                $linked_post->find_comments_count();
                $linked_post->find_likes_count();
                
                $linked_post->main_photo_master_name = $linked_post->main_photo->master_name;
                $linked_post->comments_count = $linked_post->comments_total_count;

                if(get_class($linked_post) == "Photo_Model")
                {
                    $formatted_linked_posts[] = $this->formatPost("photos", $linked_post, "Photosets");
                }
                else if(get_class($linked_post) == "Spot_Model")
                {
                    $formatted_linked_posts[] = $this->formatPost("spots", $linked_post, "Spots");
                }
                else if(get_class($linked_post) == "Speed_Model")
                {
                    $formatted_linked_posts[] = $this->formatPost("speed", $linked_post, "Speed");
                }
                else if(get_class($linked_post) == "Video_Model")
                {
                    $formatted_linked_posts[] = $this->formatPost("videos", $linked_post, "Videos");
                }
            }

            $this->ajax->result   = "ok";
            $this->ajax->callback = $callbackFunction;
            $this->ajax->data->linkedPostsData = $formatted_linked_posts;
            $this->ajax->render();
        }
    }
?>