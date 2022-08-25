<!-- Items -->
<div class="items trim-divs">
    <?php
        foreach($favorites as $favorite):
    ?>
            <!-- Item -->
            <div class="item">
                <!-- Item wrapper -->
                <div class="item-wrapper">

                    <!-- Image -->
                    <img src="<?php
                                    load_photo($favorite->module_item->main_photo->master_name,
                                                  270,
                                                  180);
                                 ?>" width="270" height="180">
                    <!-- Image END -->

                    <!-- Top panel -->
                    <div class="top-panel">

                        <!-- Likes -->
                        <div class="likes">
                            <div class="count">
                                <?php echo $favorite->module_item->likes_count; ?>
                            </div>

                            <div class="label">
                                <?php echo ($favorite->module_item->likes_count == 1) ? "Like" : "Likes"; ?>
                            </div>
                        </div>
                        <!-- Likes END -->

                        <!-- Unfavorite -->
                        <div class="item"
                              onclick="form_tools.confirmation_prompt.show('unfavorite_item',
                                                                                          'view-favorites',
                                                                                          'favorites',
                                                                                          'unfavorite',
                                                                                          'ajax/<?php echo $favorite->id; ?>/<?php echo $current_page; ?>',
                                                                                          this,
                                                                                          'modal')">
                            <div class="unfavorite-icon">
                            </div>
                        </div>
                        <!-- Unfavorite END -->

                    </div>
                    <!-- Top panel END -->

                    <!-- View item link -->
                    <?php
                        if($favorite->is_module_item_blocked):
                    ?>
                            <!-- Message -->
                            <div class="middle-panel">
                                <div class="item-locked-bg">
                                </div>

                                <div class="message">
                                    <div class="label">
                                        Item is disabled!
                                    </div>

                                    <div class="sublabel">
                                        Item is temporary disabled by user or is passing moderation.
                                    </div>
                                </div>
                            </div>
                            <!-- Message END -->

                            <!-- Bottom panel -->
                            <div class="bottom-panel">

                                <div class="heading">
                                    <div class="wrapper">
                                        <h3 class="trim-to-parent">
                                            <?php echo $favorite->module_item->get_full_heading(); ?>
                                        </h3>
                                    </div>
                                </div>

                                <div class="comments">
                                    <div class="count">
                                        <?php echo $favorite->module_item->comments_total_count; ?>
                                    </div>

                                    <div class="label">
                                        <?php echo ($favorite->module_item->comments_total_count == 1) ? "comment" : "comments"; ?>
                                    </div>
                                </div>

                            </div>
                            <!-- Bottom panel END -->
                    <?php
                        else:
                    ?>
                            <a href="<?php render_favorites_module_item_link($favorite); ?>"
                                onmouseover="html_tools.favorites_list.item_over(this)"
                                onmouseout="html_tools.favorites_list.item_out(this)">
                                <!-- Link filler -->
                                <div class="link-filler">
                                </div>
                                <!-- Link filler END -->

                                <!-- Bottom panel -->
                                <div class="bottom-panel">

                                    <div class="heading">
                                        <div class="wrapper">
                                            <h3 class="trim-to-parent">
                                                <?php echo $favorite->module_item->get_full_heading(); ?>
                                            </h3>
                                        </div>
                                    </div>

                                    <div class="comments">
                                        <div class="count">
                                            <?php echo $favorite->module_item->comments_total_count; ?>
                                        </div>

                                        <div class="label">
                                            <?php echo ($favorite->module_item->comments_total_count == 1) ? "comment" : "comments"; ?>
                                        </div>
                                    </div>

                                </div>
                                <!-- Bottom panel END -->
                            </a>
                    <?php
                        endif;
                    ?>
                    <!-- View item link -->

                </div>
                <!-- Item wrapper END -->
            </div>
            <!-- Item END -->
            <?php
        endforeach;
    ?>

    <?php
        if(!$favorites):
    ?>
            <!-- No uploads -->
            <div class="no-uploads">
                <div class="message">

                    <div class="icon">
                    </div>

                    <div class="label">
                        You haven't added any item to favorites at this module.
                    </div>

                </div>
            </div>
            <!-- No uploads END -->
    <?php
        endif;
    ?>
</div>
<!-- Items END -->