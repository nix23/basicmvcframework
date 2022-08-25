<?php
    $count = 1;
    foreach($dashboard_events as $dashboard_event):
?>
        <!-- Event -->
        <div class="event<?php if($count % 2 == 0) echo " row-highlight"; ?>">
            <div class="event-separator">
            </div>

            <div class="event-wrapper">

                <!-- Left -->
                <div class="left">
                    <?php
                        if(in_array($dashboard_event->type, array("upload", "answer", "comment",
                                                                                "like", "favorite"))):
                    ?>
                            <!-- Module item -->
                            <a href="<?php render_dashboard_event_module_item_link($dashboard_event); ?>">
                                <div class="module-item-photo">
                                    <img src="<?php
                                                    load_photo($dashboard_event->main_photo->master_name,
                                                                  145,
                                                                  95);
                                                 ?>" width="145" height="95">

                                    <div class="photo-label">
                                        <span><?php echo ucfirst(parse_dashboard_event_module($dashboard_event->module)); ?></span>
                                    </div>
                                </div>
                            </a>

                            <a href="<?php public_link("profile/view/user-{$dashboard_event->user->id}"); ?>">
                                <?php
                                    if($dashboard_event->user->has_avatar()):
                                ?>
                                        <div class="avatar">
                                            <img src="<?php
                                                            load_photo($dashboard_event->user->avatar_master_name,
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
                            <!-- Module item END -->
                    <?php
                        elseif($dashboard_event->type == "follow"):
                    ?>
                            <!-- Follow pair -->
                            <div class="left-spacer">
                            </div>

                            <a href="<?php public_link("profile/view/user-{$dashboard_event->followed_user->id}"); ?>">
                                <?php
                                    if($dashboard_event->followed_user->has_avatar()):
                                ?>
                                        <div class="avatar">
                                            <img src="<?php
                                                            load_photo($dashboard_event->followed_user->avatar_master_name,
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

                            <div class="follow-bg">
                            </div>

                            <a href="<?php public_link("profile/view/user-{$dashboard_event->follower_user->id}"); ?>">
                                <?php
                                    if($dashboard_event->follower_user->has_avatar()):
                                ?>
                                        <div class="avatar">
                                            <img src="<?php
                                                            load_photo($dashboard_event->follower_user->avatar_master_name,
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
                            <!-- Follow pair END -->
                    <?php
                        elseif(in_array($dashboard_event->type, array("activated_user", "registred_user"))):
                    ?>
                            <!-- New user -->
                            <div class="new-user-bg">
                            </div>

                            <a href="<?php public_link("profile/view/user-{$dashboard_event->user->id}"); ?>">
                                <?php
                                    if($dashboard_event->user->has_avatar()):
                                ?>
                                        <div class="avatar">
                                            <img src="<?php
                                                            load_photo($dashboard_event->user->avatar_master_name,
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
                            <!-- New user END -->
                    <?php
                        endif;
                    ?>
                </div>
                <!-- Left END -->

                <!-- Right -->
                <div class="right">
                    <div class="wrapper">

                        <?php
                            if(in_array($dashboard_event->type, array("upload", "answer", "comment",
                                                                                    "like", "favorite"))):
                        ?>
                                <div class="header">
                                    <a href="<?php render_dashboard_event_module_item_link($dashboard_event); ?>">
                                        <?php
                                            echo parse_dashboard_event_header($dashboard_event);
                                        ?>
                                    </a>
                                </div>

                                <div class="subheader">
                                    <a href="<?php render_dashboard_event_module_item_link($dashboard_event); ?>">
                                        <?php render_dashboard_event_full_name($dashboard_event); ?>
                                    </a>
                                </div>

                                <?php
                                    if(in_array($dashboard_event->type, array("comment", "answer"))):
                                ?>
                                        <div class="comment">
                                            <?php
                                                echo $dashboard_event->user->username;
                                                echo " wrote: ";
                                                echo trim_text($dashboard_event->text, 300);
                                            ?>
                                        </div>
                                <?php
                                    endif;
                                ?>

                                <div class="footer">
                                    <div class="panel-item no-margin">
                                        <?php render_dashboard_event_time_ago($dashboard_event); ?>
                                    </div>

                                    <div class="panel-item">
                                        <span class="highlight"><?php echo $dashboard_event->likes_count; ?></span>
                                        <?php echo ($dashboard_event->likes_count == 1) ? "like" : "likes"; ?>
                                    </div>

                                    <div class="panel-item">
                                        <span class="highlight"><?php echo $dashboard_event->comments_count; ?></span>
                                        <?php echo ($dashboard_event->comments_count == 1) ? "comment" : "comments"; ?>
                                    </div>

                                    <div class="panel-item">
                                        <span class="highlight"><?php echo $dashboard_event->views_count; ?></span>
                                        <?php echo ($dashboard_event->views_count == 1) ? "view" : "views"; ?>
                                    </div>

                                    <?php
                                        if($dashboard_event->type == "upload"):
                                    ?>
                                            <div class="panel-item active-item"
                                                  onclick="ajax.process_form('dashboard-events',
                                                                                      'dashboard',
                                                                                      'change_upload_moderation',
                                                                                      'ajax<?php
                                                                                            echo "/$dashboard_event->id";
                                                                                            echo "/$dashboard_event->module";
                                                                                              ?>',
                                                                                      this,
                                                                                      'modal')">
                                                <?php
                                                    if($dashboard_event->moderated == "yes")
                                                        echo "<span class='highlight'>u</span>nmoderate";
                                                    else
                                                        echo "<span class='highlight'>m</span>oderate";
                                                ?>
                                            </div>

                                            <div class="panel-item active-item"
                                                  onclick="ajax.process_form('dashboard-events',
                                                                                      'dashboard',
                                                                                      'change_upload_status',
                                                                                      'ajax<?php
                                                                                            echo "/$dashboard_event->id";
                                                                                            echo "/$dashboard_event->module";
                                                                                              ?>',
                                                                                      this,
                                                                                      'modal')">
                                                <?php
                                                    if($dashboard_event->status == "enabled")
                                                        echo "<span class='highlight'>d</span>isable";
                                                    else
                                                        echo "<span class='highlight'>e</span>nable";
                                                ?>
                                            </div>

                                            <div class="panel-item active-item">
                                                <a href="<?php admin_link("$dashboard_event->module/form/$dashboard_event->id"); ?>">
                                                    <span class="highlight">e</span>dit
                                                </a>
                                            </div>

                                            <div class="panel-item active-item"
                                                  onclick="form_tools.delete_confirmation.show('dashboard-events',
                                                                                                              'dashboard',
                                                                                                              'delete_upload',
                                                                                                              'ajax<?php
                                                                                                                    echo "/$dashboard_event->id";
                                                                                                                    echo "/$dashboard_event->module";
                                                                                                                    echo "/$current_page";
                                                                                                                    echo "/$current_days_to_fetch";
                                                                                                                    echo "/$selected_events_to_show";
                                                                                                                      ?>',
                                                                                                              false,
                                                                                                              'modal')">
                                                <span class="highlight">d</span>elete
                                            </div>
                                    <?php
                                        elseif(in_array($dashboard_event->type, array("comment", "answer"))):
                                    ?>
                                            <div class="panel-item active-item"
                                                  onclick="form_tools.delete_confirmation.show('dashboard-events',
                                                                                                              'dashboard',
                                                                                                              'delete_comment',
                                                                                                              'ajax<?php
                                                                                                                    echo "/$dashboard_event->related_table_id";
                                                                                                                    echo "/$dashboard_event->module";
                                                                                                                    echo "/$current_page";
                                                                                                                    echo "/$current_days_to_fetch";
                                                                                                                    echo "/$selected_events_to_show";
                                                                                                                      ?>',
                                                                                                              false,
                                                                                                              'modal')">
                                                <span class="highlight">d</span>elete
                                            </div>
                                    <?php
                                        elseif($dashboard_event->type == "like"):
                                    ?>
                                            <div class="panel-item active-item"
                                                  onclick="form_tools.delete_confirmation.show('dashboard-events',
                                                                                                              'dashboard',
                                                                                                              'delete_like',
                                                                                                              'ajax<?php
                                                                                                                    echo "/$dashboard_event->related_table_id";
                                                                                                                    echo "/$dashboard_event->module";
                                                                                                                    echo "/$current_page";
                                                                                                                    echo "/$current_days_to_fetch";
                                                                                                                    echo "/$selected_events_to_show";
                                                                                                                      ?>',
                                                                                                              false,
                                                                                                              'modal')">
                                                <span class="highlight">d</span>elete
                                            </div>
                                    <?php
                                        elseif($dashboard_event->type == "favorite"):
                                    ?>
                                            <div class="panel-item active-item"
                                                  onclick="form_tools.delete_confirmation.show('dashboard-events',
                                                                                                              'dashboard',
                                                                                                              'delete_favorite',
                                                                                                              'ajax<?php
                                                                                                                    echo "/$dashboard_event->id";
                                                                                                                    echo "/$dashboard_event->module";
                                                                                                                    echo "/$current_page";
                                                                                                                    echo "/$current_days_to_fetch";
                                                                                                                    echo "/$selected_events_to_show";
                                                                                                                      ?>',
                                                                                                              false,
                                                                                                              'modal')">
                                                <span class="highlight">d</span>elete
                                            </div>
                                    <?php
                                        endif;
                                    ?>
                                </div>
                        <?php
                            elseif($dashboard_event->type == "follow"):
                        ?>
                                <div class="header header-text">
                                    <?php echo parse_dashboard_event_header($dashboard_event); ?>
                                </div>

                                <div class="subheader subheader-text">
                                    New followers pair
                                </div>

                                <div class="footer">
                                    <div class="panel-item no-margin">
                                        <?php render_dashboard_event_time_ago($dashboard_event); ?>
                                    </div>

                                    <div class="panel-item active-item"
                                          onclick="form_tools.delete_confirmation.show('dashboard-events',
                                                                                                      'dashboard',
                                                                                                      'delete_follow_pair',
                                                                                                      'ajax<?php
                                                                                                            echo "/$dashboard_event->id";
                                                                                                            echo "/$current_page";
                                                                                                            echo "/$current_days_to_fetch";
                                                                                                            echo "/$selected_events_to_show";
                                                                                                              ?>',
                                                                                                      false,
                                                                                                      'modal')">
                                        <span class="highlight">d</span>elete
                                    </div>
                                </div>
                        <?php
                            elseif(in_array($dashboard_event->type, array("activated_user", "registred_user"))):
                        ?>
                                <div class="header header-text">
                                    <?php echo parse_dashboard_event_header($dashboard_event); ?>
                                </div>

                                <div class="subheader subheader-text">
                                    New fordriver
                                </div>

                                <div class="footer">
                                    <div class="panel-item no-margin">
                                        <?php render_dashboard_event_time_ago($dashboard_event); ?>
                                    </div>

                                    <div class="panel-item active-item"
                                          onclick="form_tools.delete_confirmation.show('dashboard-events',
                                                                                                      'dashboard',
                                                                                                      'delete_user',
                                                                                                      'ajax<?php
                                                                                                            echo "/$dashboard_event->user_id";
                                                                                                            echo "/$current_page";
                                                                                                            echo "/$current_days_to_fetch";
                                                                                                            echo "/$selected_events_to_show";
                                                                                                              ?>',
                                                                                                      false,
                                                                                                      'modal')">
                                        <span class="highlight">d</span>elete
                                    </div>
                                </div>
                        <?php
                            endif;
                        ?>

                    </div>
                </div>
                <!-- Right END -->

            </div>

            <div class="event-separator">
            </div>
        </div>
        <!-- Event END -->
<?php
        $count++;
    endforeach;
?>

<?php
    if(!$dashboard_events):
?>
        <!-- No dashboard events -->
        <div class="no-events">
            <div class="message">

                <div class="icon">
                </div>

                <div class="label">
                    No events for last <?php echo $current_days_to_fetch; ?> days.
                </div>

            </div>
        </div>
        <!-- No dashboard events END -->
<?php
    endif;
?>