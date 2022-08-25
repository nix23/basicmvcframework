<?php

class Spot_Stats_Model extends Model
{
    protected $table_name = "spots_stats";
    protected $db_fields = array("id", "spot_id", "comments_count", "likes_count",
        "views_count", "favorites_count", "activity");

    public $id;
    public $spot_id;
    public $comments_count = 0;
    public $likes_count = 0;
    public $views_count = 0;
    public $favorites_count = 0;
    public $activity = 0;

    public function find_stats_on($spot_id)
    {
        $sql = "WHERE spot_id = %d";
        $sql = sprintf($sql,
            $this->database->escape_value($spot_id));

        return $this->find_by_condition($sql);
    }

    public function increase_likes_count()
    {
        $this->likes_count++;
    }

    public function decrease_likes_count()
    {
        $this->likes_count--;
    }

    public function increase_comments_count()
    {
        $this->comments_count++;
    }

    public function decrease_comments_count()
    {
        $this->comments_count--;
    }

    public function increase_views_count_by($views_count)
    {
        $this->views_count += $views_count;
    }

    public function increase_favorites_count()
    {
        $this->favorites_count++;
    }

    public function decrease_favorites_count()
    {
        $this->favorites_count--;
    }

    public function save()
    {
        $new_activity = $this->likes_count;
        $new_activity += $this->comments_count;
        $new_activity += $this->views_count * 0.01;
        $new_activity += $this->favorites_count * 0.01;

        $this->activity = $new_activity;
        return parent::save();
    }

    public function get_validation_rules()
    {
        // Validation Rules
    }
}

?>