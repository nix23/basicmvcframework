<!-- Header container -->
<div class="wrapper">
    <!-- Logo -->
    <a href="<?php public_link("main"); ?>" target="_blank">
        <div class="logo">
        </div>
    </a>
    <!-- Logo END -->
    
    <!-- Menu -->
    <div class="menu">
        <!-- Menu wrapper -->
        <div class="wrapper">
            <?php
                $count = 0;  
                foreach($menu_items as $menu_item):
                    if($count == 3):
                        $class = " newline";
                        $count = 0;
                    else:
                        $class = "";
                    endif;
            ?>
                    <!-- Menu item -->
                    <div class="item<?php echo $class; ?>">
                        <?php
                            if($current_url == $menu_item->url):
                        ?>
                                <span class="selected-name">
                                    <?php echo $menu_item->label; ?>
                                </span>
                        <?php
                            else:
                        ?>
                                <a href="<?php admin_link($menu_item->url); ?>" class="name">
                                    <?php echo $menu_item->label; ?>
                                </a>
                        <?php
                            endif;
                        ?>
                    </div>
                    <!-- Menu item END -->
            <?php
                    $count++;
                endforeach;
            ?>

            <!-- Settings -->
            <div class="item">
                <span class="name"
                        onclick="form_tools.settings.show()">
                    Settings
                </span>
            </div>
            <!-- Settings END -->
        </div>
        <!-- Menu wrapper END -->
    </div>
    <!-- Menu END -->
    
    <!-- Actions -->
    <div class="actions">
        <span onclick="ajax.process('authorization',
                            'logout',
                            'ajax',
                            '',
                            'logout')">
            Logout
        </span>
    </div>
    <!-- Actions END -->
</div>
<!-- Header container END -->