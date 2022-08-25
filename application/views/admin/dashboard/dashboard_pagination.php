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
                            $link  = "dashboard/list/";
                            $link .= "page-$page/";
                            $link .= "days-$current_days_to_fetch/";
                            $link .= "events-$selected_events_to_show";
                            admin_link($link);
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