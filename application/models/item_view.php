<?php
    class Item_View_Model extends Model
    {
        protected $table_name = "item_views";
        protected $db_fields  = array("id", "item_id", "module", "ip", "posted_on");
        protected $nested_db_fields = array("count");

        public $id;
        public $item_id;
        public $module;
        public $ip;
        public $posted_on;

        // Nested attributes
        public $count;

        public function save()
        {
            // Updating post datetime
            $this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());
            return parent::save();
        }

        public function was_item_viewed_from_ip($item_id,
                                                             $viewer_ip,
                                                             $module)
        {
            $sql  = "WHERE item_id = %d  ";
            $sql .= "  AND ip = '%s'     ";
            $sql .= "  AND module = '%s' ";

            $sql = sprintf($sql,
                                $this->database->escape_value($item_id),
                                $this->database->escape_value($viewer_ip),
                                $this->database->escape_value($module));

            return ($this->count($sql) > 0) ? true : false;
        }

        public function find_count_by_item($item_id,
                                                      $module)
        {
            $sql  = "WHERE item_id = %d  ";
            $sql .= "  AND module = '%s' ";

            $sql = sprintf($sql,
                                $this->database->escape_value($item_id),
                                $this->database->escape_value($module));

            return $this->count($sql);
        }

        // Calculate views count per every item,
        // and moves every batch into module 'stats' table
        public function move_item_views_to_module_stats()
        {
            $item_views = $this->get_table_name();

            $sql  = "SELECT $item_views.item_id AS item_id, ";
            $sql .= "       $item_views.module  AS module,  ";
            $sql .= "       COUNT(*)            AS count    ";
            $sql .= "  FROM $item_views                     ";
            $sql .= "GROUP BY item_id, module               ";

            $item_views = $this->find_by_sql($sql);

            if($item_views)
            {
                $photo_stats_model = new Photo_Stats_Model;
                $spot_stats_model  = new Spot_Stats_Model;
                $speed_stats_model = new Speed_Stats_Model;
                $video_stats_model = new Video_Stats_Model;

                foreach($item_views as $item_view)
                {
                    switch($item_view->module)
                    {
                        case "photos":
                            $item_stats = $photo_stats_model->find_stats_on($item_view->item_id);
                        break;

                        case "spots":
                            $item_stats = $spot_stats_model->find_stats_on($item_view->item_id);
                        break;

                        case "speed":
                            $item_stats = $speed_stats_model->find_stats_on($item_view->item_id);
                        break;

                        case "videos":
                            $item_stats = $video_stats_model->find_stats_on($item_view->item_id);
                        break;
                    }
                    
                    $item_stats->increase_views_count_by($item_view->count);
                    
                    if($item_stats->save())
                    {
                        $this->delete_views_on($item_view->item_id,
                                                      $item_view->module);
                    }
                }
            }
        }
        
        public function delete_views_on($item_id,
                                                  $module)
        {
            $sql  = "WHERE item_id = %d   ";
            $sql .= "  AND module  = '%s' ";
            
            $sql = sprintf($sql,
                                $this->database->escape_value($item_id),
                                $this->database->escape_value($module));
            
            return $this->delete_by_condition($sql);
        }
    }
?>