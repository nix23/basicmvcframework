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
                                drive_link( $selected_module,
                                                $page,
                                                $selected_category,
                                                $selected_subcategory);
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