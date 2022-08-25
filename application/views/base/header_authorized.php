<!-- Header container -->
<div class="header-wrapper">
    <!-- Logo -->
    <a href="<?php public_link("main"); ?>">
        <div class="logo">
        </div>
    </a>
    <!-- Logo END -->
    
    <!-- Menu -->
    <div class="menu">
        
        <!-- First column -->
        <div class="column">
            <?php
                foreach($menu_items_first_batch as $menu_item):
            ?>
                    <div class="item">
                        <?php
                            if($menu_item->controller == $current_controller):
                        ?>
                            <span class="name selected">
                                <?php echo $menu_item->label; ?>
                            </span>
                        <?php
                            else:
                        ?>
                            <a href="<?php public_link($menu_item->controller); ?>" class="name active">
                                <?php echo $menu_item->label; ?>
                            </a>
                        <?php
                            endif;
                        ?>
                    </div>
            <?php
                endforeach;
            ?>
        </div>
        <!-- First column END -->
        
        <!-- Second column -->
        <div class="column">
            <?php
                foreach($menu_items_second_batch as $menu_item):
            ?>
                    <div class="item">
                        <?php
                            if($menu_item->controller == $current_controller):
                        ?>
                            <span class="name selected">
                                <?php echo $menu_item->label; ?>
                            </span>
                        <?php
                            else:
                        ?>
                            <a href="<?php public_link($menu_item->controller); ?>" class="name active">
                                <?php echo $menu_item->label; ?>
                            </a>
                        <?php
                            endif;
                        ?>
                    </div>
            <?php
                endforeach;
            ?>
            
            <div class="add-item">
                <span class="name" 
                        onmouseover="form_tools.header.show_add_list()">
                    + Add
                </span>
            </div>
        </div>
        <!-- Second column END -->
        
    </div>
    <!-- Menu END -->
    
    <!-- Actions -->
    <div class="actions">
        <span class="logout" title="If you are authorized through fb, you will be also logouted from facebook">
            Logout
        </span>
    </div>
    <!-- Actions END -->
</div>
<!-- Header container END -->

<!-- Add list -->
<div id="header-add-list">
    
    <a href="<?php public_link("photos/form"); ?>">
        <div class="item highlight">
            
            <div class="spacer">
            </div>
            
            <div class="legend">
                <div class="heading">
                    Photoset
                </div>
                
                <div class="subheading">
                    New photoset
                </div>
            </div>
        
        </div>
    </a>
    
    <a href="<?php public_link("spots/form"); ?>">
        <div class="item">
            
            <div class="spacer">
            </div>
            
            <div class="legend">
                <div class="heading">
                    Spot
                </div>
                
                <div class="subheading">
                    New spot
                </div>
            </div>
            
        </div>
    </a>
    
    <a href="<?php public_link("speed/form"); ?>">
        <div class="item highlight">
            
            <div class="spacer">
            </div>
            
            <div class="legend">
                <div class="heading">
                    Speed
                </div>
                
                <div class="subheading">
                    New speed
                </div>
            </div>
            
        </div>
    </a>
    
    <a href="<?php public_link("videos/form"); ?>">
        <div class="item">
            
            <div class="spacer">
            </div>
            
            <div class="legend">
                <div class="heading">
                    Video
                </div>
                
                <div class="subheading">
                    New video
                </div>
            </div>
            
        </div>
    </a>
    
</div>
<!-- Add list END -->