<!-- Activity list -->
<div id="activity">

    <!-- Heading -->
    <div class="heading">
        <div class="wrapper">

            <!-- Legend -->
            <div class="legend">

                <div class="label">
                    Activity
                </div>

                <div class="sublabel">
                    Latest activity at your drives and comments.
                </div>

            </div>
            <!-- Legend END -->

        </div>
    </div>
    <!-- Heading END -->

    <!-- Controls -->
    <div class="controls">

        <!-- Pagination -->
        <div class="pagination">
            <div class="wrapper">
                <?php
                    foreach($pages as $page):
                        if($page == $current_page):
                ?>
                            <div class="page selected">
                                <span class="number">
                                    <?php echo $page; ?>
                                </span>
                            </div>
                <?php
                        else:
                ?>
                            <a href="<?php
                                            public_link("activity/list/page-$page/days-$current_days_to_fetch");
                                        ?>">
                                <div class="page active">

                                    <span class="number">
                                        <?php echo $page; ?>
                                    </span>

                                </div>
                            </a>
                <?php
                        endif;
                    endforeach;
                ?>
            </div>
        </div>
        <!-- Pagination END -->

        <!-- Select list -->
        <div class="select-list">

            <div class="heading">
                Last:
            </div>

            <?php
                foreach($days_to_fetch_items as $days_to_fetch_item):
                    if($days_to_fetch_item->selected):
            ?>
                        <div class="wrapper">
                            <div class="item selected">
                                <?php echo $days_to_fetch_item->label; ?>
                            </div>
                        </div>
            <?php
                    else:
            ?>
                        <a href="<?php
                                        public_link("activity/list/page-1/days-$days_to_fetch_item->value");
                                    ?>">
                            <div class="wrapper">
                                <div class="item">
                                    <?php echo $days_to_fetch_item->label; ?>
                                </div>
                            </div>
                        </a>
            <?php
                    endif;
                endforeach;
            ?>

        </div>
        <!-- Select list END -->

    </div>
    <!-- Controls END -->

    <!-- Content -->
    <div class="content">
        <?php
            $count = 1;
            foreach($activities as $post):
        ?>
                <!-- Post -->
                <div class="post<?php if($count % 2 == 0) echo " row-highlight"; ?>">
                    <div class="item-separator">
                    </div>

                    <div class="item-wrapper">

                        <!-- Left -->
                        <div class="left">
                            <div class="module-photo">
                                <a href="<?php render_activity_post_module_item_link($post); ?>">
                                    <img src="<?php
                                                    load_photo($post->main_photo->master_name,
                                                                  145,
                                                                  95);
                                                 ?>" width="145" height="95">
                                </a>

                                <div class="photo-label">
                                    <span><?php echo render_activity_module_photo_label($post->module, $post->type); ?></span>
                                </div>
                            </div>

                            <a href="<?php public_link("profile/view/user-{$post->user->id}"); ?>">
                                <?php
                                    if($post->user->has_avatar()):
                                ?>
                                        <div class="avatar">
                                            <img src="<?php
                                                            load_photo($post->user->avatar_master_name,
                                                                          95,
                                                                          95);
                                                ?>" width="95" height="95">
                                        </div>
                                <?php
                                    else:
                                ?>
                                        <div class="no-avatar">
                                        </div>
                                <?php
                                    endif;
                                ?>
                            </a>
                        </div>
                        <!-- Left END -->

                        <!-- Right -->
                        <div class="right">
                            <div class="wrapper">

                                <div class="header">
                                    <a href="<?php render_activity_post_module_item_link($post); ?>">
                                        <?php
                                            echo $post->user->username . " ";
                                            echo parse_activity_post_header($post->module, $post->type);
                                        ?>
                                    </a>
                                </div>

                                <div class="subheader">
                                    <a href="<?php render_activity_post_module_item_link($post); ?>">
                                        <?php render_activity_post_full_name($post); ?>
                                    </a>
                                </div>

                                <?php
                                    if($post->type != "like"):
                                ?>
                                        <div class="comment">
                                            <?php
                                                echo $post->user->username;
                                                echo " wrote: ";
                                                echo trim_text($post->text, 300);
                                            ?>
                                        </div>
                                <?php
                                    endif;
                                ?>

                                <div class="footer">
                                    <?php
                                        $time_ago = time_ago($post->posted_on);

                                        if($time_ago == "Just now"):
                                            list($first_part, $second_part) = explode(" ", $time_ago);
                                            echo "<span class='highlight'>$first_part</span> $second_part";
                                        else:
                                            echo $time_ago;
                                        endif;
                                    ?>
                                </div>

                            </div>
                        </div>
                        <!-- Right END -->

                    </div>

                    <div class="item-separator">
                    </div>
                </div>
                <!-- Post END -->
        <?php
                $count++;
            endforeach;
        ?>

        <?php
            if(!$activities):
        ?>
                <!-- No activities -->
                <div class="no-posts">
                    <div class="message">

                        <div class="icon">
                        </div>

                        <div class="label">
                            No activities by users yet.
                        </div>

                    </div>
                </div>
                <!-- No activities END -->
        <?php
            endif;
        ?>
    </div>
    <!-- Content END -->

    <!-- Controls -->
    <div class="controls">

        <!-- Pagination -->
        <div class="pagination">
            <div class="wrapper">
                <?php
                    foreach($pages as $page):
                        if($page == $current_page):
                ?>
                            <div class="page selected">
                                <span class="number">
                                    <?php echo $page; ?>
                                </span>
                            </div>
                <?php
                        else:
                ?>
                            <a href="<?php
                                            public_link("activity/list/page-$page/days-$current_days_to_fetch");
                                        ?>">
                                <div class="page active">

                                    <span class="number">
                                        <?php echo $page; ?>
                                    </span>

                                </div>
                            </a>
                <?php
                        endif;
                    endforeach;
                ?>
            </div>
        </div>
        <!-- Pagination END -->

    </div>
    <!-- Controls END -->

</div>
<!-- Activity list END -->