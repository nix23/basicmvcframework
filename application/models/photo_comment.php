<?php

class Photo_Comment_Model extends Model
{
    protected $table_name = "photos_comments";
    protected $db_fields = array("id", "photo_id", "user_id", "answer_id",
        "comment", "posted_on");
    protected $nested_db_fields = array("answers_count");

    public $id;
    public $photo_id;
    public $user_id;
    public $answer_id;
    public $comment;
    public $posted_on;

    // Nested attributes
    public $answers_count;

    // Shared attributes
    public $user;
    public $answers;

    public function find_comments_on($photoset_id = false,
                                     $page = 1,
                                     $validate_page = true)
    {
        // Making pagination
        $sql = "WHERE photo_id = %d";
        $sql .= "  AND answer_id = 0";
        $sql = sprintf($sql,
            $this->database->escape_value($photoset_id));

        $this->pagination = new Pagination($page, $this->count($sql));
        if ($validate_page) $this->pagination->validate_page_range();

        // Fetching comments
        $comments_table = $this->get_table_name();

        $answers_sql = "SELECT COUNT(*) FROM $comments_table AS comment_answers ";
        $answers_sql .= "WHERE comment_answers.answer_id = $comments_table.id    ";
        $answers_sql .= "  AND photo_id = %d                                     ";

        $answers_sql = sprintf($answers_sql,
            $this->database->escape_value($photoset_id));

        $sql = "SELECT $comments_table.*,              ";
        $sql .= "       ($answers_sql) as answers_count ";
        $sql .= "FROM $comments_table                   ";

        $sql .= "WHERE photo_id = %d                          ";
        $sql .= "  AND answer_id = 0                          ";
        $sql .= "ORDER BY posted_on DESC                      ";
        $sql .= "LIMIT  {$this->pagination->records_per_page} ";
        $sql .= "OFFSET {$this->pagination->offset}           ";

        $sql = sprintf($sql,
            $this->database->escape_value($photoset_id));

        return $this->find_by_sql($sql);
    }

    public function find_answers()
    {
        $sql = "WHERE photo_id = %d     ";
        $sql .= "  AND answer_id = %d    ";
        $sql .= "ORDER BY posted_on DESC ";

        $sql = sprintf($sql,
            $this->database->escape_value($this->photo_id),
            $this->database->escape_value($this->id));

        $this->answers = $this->find_all($sql);
    }

    public function find_author()
    {
        $user_model = new User_Model;
        $this->user = $user_model->find_by_id($this->user_id);
    }

    public function count_on($photoset_id = false)
    {
        $sql = "WHERE photo_id = %d";
        $sql = sprintf($sql,
            $this->database->escape_value($photoset_id));

        return $this->count($sql);
    }

    public function get_all_root_comment_ids_packed_by($user_id)
    {
        $sql = "WHERE answer_id = 0 ";
        $sql .= "  AND user_id = %d  ";

        $sql = sprintf($sql,
            $this->database->escape_value($user_id));

        $root_comments = $this->find_all($sql, "id");

        if ($root_comments) {
            $root_comment_ids = array();

            foreach ($root_comments as $root_comment) {
                $root_comment_ids[] = $root_comment->id;
            }

            return implode(",", $root_comment_ids);
        } else {
            return false;
        }
    }

    public function save()
    {
        $this->posted_on = strftime("%Y-%m-%d %H:%M:%S", time());

        $photoset_stats_model = new Photo_Stats_Model;
        $photoset_stats = $photoset_stats_model->find_stats_on($this->photo_id);
        $photoset_stats->increase_comments_count();
        $photoset_stats->save();

        return parent::save();
    }

    public function delete()
    {
        $photoset_stats_model = new Photo_Stats_Model;
        $photoset_stats = $photoset_stats_model->find_stats_on($this->photo_id);
        $photoset_stats->decrease_comments_count();
        $photoset_stats->save();

        return parent::delete();
    }

    public function delete_comments_on($photo_id)
    {
        $sql = "WHERE photo_id = %d";
        $sql = sprintf($sql,
            $this->database->escape_value($photo_id));

        return $this->delete_by_condition($sql);
    }

    public function delete_all_by_user($user_id)
    {
        $sql = "WHERE user_id = %d";
        $sql = sprintf($sql,
            $this->database->escape_value($user_id));

        $user_comments = $this->find_all($sql);
        $photoset_stats_model = new Photo_Stats_Model;

        foreach ($user_comments as $user_comment) {
            $photoset_stats = $photoset_stats_model->find_stats_on($user_comment->photo_id);
            $photoset_stats->decrease_comments_count();
            $photoset_stats->save();
        }

        return $this->delete_by_condition($sql);
    }

    public function delete_with_all_answers()
    {
        $sql = "WHERE photo_id = %d  ";
        $sql .= "  AND answer_id = %d ";

        $sql = sprintf($sql,
            $this->database->escape_value($this->photo_id),
            $this->database->escape_value($this->id));

        $comment_answers = $this->find_all($sql);
        foreach ($comment_answers as $comment_answer)
            $comment_answer->delete();

        return $this->delete();
    }

    public function get_validation_rules()
    {
        $rules = array();

        $rules['comment'] = array(array('comment_required',
            'Please enter comment text.',
            'required'),
            array('comment_minlength',
                'Min length of comment text: 5 chars.',
                'min_length',
                5),
            array('comment_maxlength',
                'Max length of comment text: 1000 chars.',
                'max_length',
                2500));

        return $rules;
    }
}

?>