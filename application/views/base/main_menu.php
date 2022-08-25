<!-- Main menu -->
<div id="main-menu">
    <?php
        $last_item = count($menu_items) - 1;
        for($item = 0; $item <= $last_item; $item++):
            $menu_item = $menu_items[$item];
            
            if($item == $last_item)
                $separator = "";
            else
                $separator = " separator";
            
            if($menu_item->controller == $current_controller):
    ?>
                <div class="item">
                    <div class="wrapper<?php echo $separator; ?>">
                        
                        <div class="heading highlight">
                            <?php echo $menu_item->label; ?>
                        </div>
                        
                        <div class="subheading highlight">
                            <?php echo $menu_item->sublabel; ?>
                        </div>
                        
                    </div>
                </div>
    <?php
            else:
    ?>
                <div class="item">
                    <a href="<?php public_link($menu_item->controller); ?>">
                        <div class="wrapper<?php echo $separator; ?>"
                              onmouseover="html_tools.main_menu.over(this)"
                              onmouseout="html_tools.main_menu.out(this)">
                            
                            <div class="heading">
                                <?php echo $menu_item->label; ?>
                            </div>
                            
                            <div class="subheading">
                                <?php echo $menu_item->sublabel; ?>
                            </div>
                            
                        </div>
                    </a>
                </div>
    <?php
            endif;
        endfor;
    ?>
</div>
<!-- Main menu END -->