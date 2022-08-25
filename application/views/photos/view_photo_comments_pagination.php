<div class="wrapper">
    <?php
        foreach($comments_pages as $page):
            if($page == $comments_current_page):
    ?>
                <div class="page selected">
                    
                    <span class="number">
                        <?php echo $page; ?>
                    </span>
                    
                </div>
    <?php
            else:
    ?>
                <div class="page active"
                      onclick="ajax.process('photos',
                                                    'load_comments',
                                                    'ajax/<?php echo $photoset->id; ?>/<?php echo $page; ?>',
                                                    false,
                                                    'modal_no_confirmation')">
                    
                    <span class="number">
                        <?php echo $page; ?>
                    </span>
                    
                </div>
    <?php
            endif;
        endforeach;
    ?>
</div>