<?php
    $count = 0;
    foreach($speed->comments as $comment): 
?>
        <!-- Comment -->
        <div class="comment<?php if($count == 0) echo " small-margin"; ?> comment-id-<?php echo $comment->id; ?>">
            <!-- Wrapper -->
            <div class="comment-wrapper">
                
                <!-- Header -->
                <div class="header">
                    
                    <!-- Avatar -->
                    <a href="<?php
                                    if($comment->user->is_current_logged_user())
                                        public_link("drive");
                                    else
                                        public_link("profile/view/user-{$comment->user->id}");
                                ?>">
                        <div class="avatar">
                            <?php
                                if($comment->user->has_avatar()):
                            ?>
                                    <img src='<?php
                                                        load_photo($comment->user->avatar_master_name,
                                                                      70,
                                                                      70);
                                                ?>' width="70" height="70">
                            <?php
                                else:
                            ?>
                                    <div class="no-avatar">
                                    </div>
                            <?php
                                endif;
                            ?>

                            <?php
                                if($comment->user->is_current_logged_user())
                                    echo "<div class='you-label'></div>";
                            ?>
                        </div>
                    </a>
                    <!-- Avatar END -->

                    <!-- Legend -->
                    <div class="legend">
                        <div class="label">
                            <a href="<?php
                                            if($comment->user->is_current_logged_user())
                                                public_link("drive");
                                            else
                                                public_link("profile/view/user-{$comment->user->id}");
                                        ?>">
                                <span class="link">
                                    <?php echo $comment->user->username; ?> wrote:
                                </span>
                            </a>
                        </div>

                        <div class="sublabel">
                            <?php
                                if($comment->user->has_subname())
                                    echo $comment->user->subname;
                                else
                                    time_on_site($comment->user->registred_on);
                            ?>
                        </div>
                    </div>
                    <!-- Legend END -->
                    
                    <!-- Postdate -->
                    <div class="postdate">
                        <div class="wrapper">
                            <?php
                                list($time_ago_label, $time_ago_sublabel) = time_ago_splitted($comment->posted_on);
                            ?>
                            <div class="<?php echo ($time_ago_label == "Just") ? "label-small" : "label"; ?>">
                                <?php echo $time_ago_label; ?>
                            </div>
                            
                            <div class="sublabel">
                                <?php echo $time_ago_sublabel; ?>
                            </div>
                        </div>
                    </div>
                    <!-- Postdate END -->
                    
                </div>
                <!-- Header END -->
                
                <!-- Message -->
                <div class="message">
                    <?php echo replace_new_lines($comment->comment); ?>
                </div>
                <!-- Message END -->
                
            </div>
            <!-- Wrapper END -->
            
            <!-- Footer -->
            <div class="footer">
                <div class="left">
                    <?php
                        echo "<span class='highlight'>$comment->answers_count</span> ";
                        echo ($comment->answers_count == 1) ? "answer" : "answers";
                    ?>
                </div>

                <?php
                    if($admin_authorized):
                ?>
                        <div class="center">
                            <span class="text"
                                    onclick="form_tools.confirmation_prompt.show('delete_comment',
                                                                                               'newcomment-form',
                                                                                               'speed',
                                                                                               'delete_comment',
                                                                                               'ajax<?php echo "/" . $comment->id; ?>',
                                                                                               this,
                                                                                               'modal')">
                                <span class="highlight">D</span>elete comment
                            </span>
                        </div>
                <?php
                    endif;
                ?>

                <?php
                    if($authorized):
                ?>
                        <div class="right"
                              onclick="form_tools.newcomment.show('Answer to comment',
                                                                              'Reply to <?php echo $comment->user->username; ?> comment',
                                                                              <?php echo $comment->id; ?>)">
                            <span class="highlight">+ </span>Add answer
                        </div>
                <?php
                    else:
                ?>
                        <div class="right"
                              onclick="form_tools.default_errors.show(new Array('Please login to answer comments.'))">
                             <span class="highlight">+ </span>Add answer
                        </div>
                <?php
                    endif;
                ?>
            </div>
            <!-- Footer END -->
        </div>
        <!-- Comment END -->
        
        <!-- Comment answers -->
        <?php
            if($comment->answers):
        ?>
                <div class="answers">
                    <?php
                        foreach($comment->answers as $answer):
                    ?>
                        <div class="answer comment-id-<?php echo $answer->id; ?>">
                            <!-- Wrapper -->
                            <div class="answer-wrapper">
                                
                                <!-- Header -->
                                <div class="header">
                                    
                                    <!-- Avatar -->
                                    <a href="<?php
                                                    if($answer->user->is_current_logged_user())
                                                        public_link("drive");
                                                    else
                                                        public_link("profile/view/user-{$answer->user->id}");
                                                ?>">
                                        <div class="avatar">
                                            <?php
                                                if($answer->user->has_avatar()):
                                            ?>
                                                    <img src='<?php
                                                                        load_photo($answer->user->avatar_master_name,
                                                                                      70,
                                                                                      70);
                                                                ?>' width="70" height="70">
                                            <?php
                                                else:
                                            ?>
                                                    <div class="no-avatar">
                                                    </div>
                                            <?php
                                                endif;
                                            ?>

                                            <?php
                                                if($comment->user->is_current_logged_user())
                                                    echo "<div class='you-label'></div>";
                                            ?>
                                        </div>
                                    </a>
                                    <!-- Avatar END -->

                                    <!-- Legend -->
                                    <div class="legend">
                                        <div class="label">
                                            <a href="<?php
                                                            if($answer->user->is_current_logged_user())
                                                                public_link("drive");
                                                            else
                                                                public_link("profile/view/user-{$answer->user->id}");
                                                        ?>">
                                                <span class="link">
                                                    <?php echo $answer->user->username; ?> answered:
                                                </span>
                                            </a>
                                        </div>

                                        <div class="sublabel">
                                            <?php
                                                if($answer->user->has_subname())
                                                    echo $answer->user->subname;
                                                else
                                                    time_on_site($answer->user->registred_on);
                                            ?>
                                        </div>
                                    </div>
                                    <!-- Legend END -->
                                    
                                    <!-- Postdate -->
                                    <div class="postdate">
                                        <?php
                                            list($time_ago_label, $time_ago_sublabel) = time_ago_splitted($answer->posted_on);
                                        ?>
                                        <div class="<?php echo ($time_ago_label == "Just") ? "label-small" : "label"; ?>">
                                            <?php echo $time_ago_label; ?>
                                        </div>
                                        
                                        <div class="sublabel">
                                            <?php echo $time_ago_sublabel; ?>
                                        </div>
                                    </div>
                                    <!-- Postdate END -->
                                    
                                </div>
                                <!-- Header END -->
                                
                                <!-- Message -->
                                <div class="message">
                                    <?php echo replace_new_lines($answer->comment); ?>
                                </div>
                                <!-- Message END -->

                                <?php
                                    if($admin_authorized):
                                ?>
                                        <!-- Delete -->
                                        <div class="delete">
                                            <span class="text"
                                                    onclick="form_tools.confirmation_prompt.show('delete_comment',
                                                                                                                'newcomment-form',
                                                                                                                'speed',
                                                                                                                'delete_answer',
                                                                                                                'ajax<?php echo "/" . $answer->id; ?>',
                                                                                                                this,
                                                                                                                'modal')">
                                                <span class="highlight">D</span>elete comment
                                            </span>
                                        </div>
                                        <!-- Delete END -->
                                <?php
                                    endif;
                                ?>

                            </div>
                            <!-- Wrapper END -->
                        </div>
                    <?php
                        endforeach;
                    ?>
                </div>
        <?php
            endif;
        ?>
        <!-- Comment answers END -->
<?php 
        $count++;
    endforeach;
?>

<?php
    if(!$speed->comments):
?>
        <!-- No comments -->
        <div class="no-comments">
            <div class="message">
                
                <div class="icon">
                </div>
                
                <div class="label">
                    No comments have been added yet.
                </div>
                
            </div>
        </div>
        <!-- No comments END -->
<?php
    endif;
?>