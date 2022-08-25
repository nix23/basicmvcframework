<!-- Followed users -->
<?php
    $highlight_column = 1;
    foreach($followed_users as $followed_user):
?>
        <div class="item<?php if($highlight_column % 2 == 0) echo " highlight-column"; ?>">
            <div class="item-wrapper">
                
                <!-- Avatar -->
                <div class="avatar">
                    <a href="<?php public_link("profile/view/user-{$followed_user->followed_user->id}"); ?>">
                        <?php
                            if($followed_user->followed_user->has_avatar()):
                        ?>
                                <img src="<?php load_photo($followed_user->followed_user->avatar_master_name, 70, 70); ?>">
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
                <!-- Avatar END -->
                
                <!-- Legend -->
                <div class="legend">
                    <div class="heading">
                        <a href="<?php public_link("profile/view/user-{$followed_user->followed_user->id}"); ?>">
                            <h3 class="trim-to-parent">
                                <?php echo $followed_user->followed_user->username; ?>
                            </h3>
                        </a>
                    </div>
                    
                    <div class="sublabel">
                        <?php time_on_site($followed_user->followed_user->registred_on); ?>
                    </div>
                </div>
                <!-- Legend END -->
                
                <!-- Action -->
                <div class="action">
                    <div class="button"
                          onclick="ajax.process_form('followed-users-form',
                                                              'follow',
                                                              'change_follow_status',
                                                              'ajax/<?php echo $followed_user->followed_id; ?>',
                                                              this,
                                                              'followed_users')">
                        Unfollow
                    </div>
                </div>
                <!-- Action END -->
                
            </div>
        </div>
<?php
        $highlight_column++;
    endforeach;
?>
<!-- Followed users END -->

<!-- No followed users -->
<?php
    if(!$followed_users):
?>
        <div class="no-followed-users">
            <div class="message">

                <div class="icon">
                </div>

                <div class="label">
                    You aren't following any user yet.
                </div>

            </div>
        </div>
<?php
    endif;
?>
<!-- No followed users END -->