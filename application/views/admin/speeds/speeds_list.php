<!-- Page heading -->
<div class="page-heading">
    <div class="wrapper">

        <div class="legend">
            <div class="name">
                Speed
            </div>

            <div class="subname">
                <span>
                    List
                </span>

                <span class="separator padding">
                    >>
                </span>

                <span class="padding">
                    <a href="<?php admin_link("speed"); ?>">
                        All
                    </a>
                </span>

        <?php
            if($selected_category):
        ?>
                <span class="separator padding">
                    >>
                </span>

                <span class="padding">
                    <a href="<?php
                                    admin_module_link("speed",
                                                            "index",
                                                            $selected_category,
                                                            false,
                                                            1,
                                                            $selected_sort);
                                ?>">
                        <?php echo $selected_category->name; ?>
                    </a>
                </span>
        <?php
            endif;

            if($selected_subcategory):
        ?>
                <span class="separator padding">
                    >>
                </span>

                <span class="padding">
                    <a href="<?php
                                    admin_module_link("speed",
                                                            "index",
                                                            $selected_category,
                                                            $selected_subcategory,
                                                            1,
                                                            $selected_sort);
                                ?>">
                        <?php echo $selected_subcategory->name; ?>
                    </a>
                </span>
        <?php
            endif;
        ?>
            </div>
        </div>

        <?php echo $settings; ?>
        
    </div>
</div>
<!-- Page heading END -->

<!-- Page controls -->
<div class="page-controls">
    
    <!-- Pagination -->
    <div class="pagination">
        <div class="wrapper ajax-pagination">
            <?php include("speeds_list_pagination.php"); ?>
        </div>
    </div>
    <!-- Pagination END -->
    
    <!-- Sorting -->
    <div class="sorting">
        <div class="heading">
            Sort by:
        </div>
        
        <?php
            foreach($sort_items as $sort_item):
        ?>
                <div class="wrapper">
                    <a href="<?php
                                    admin_module_link("speed",
                                                            "index",
                                                            $selected_category,
                                                            $selected_subcategory,
                                                            $current_page,
                                                            $sort_item->sort);
                                ?>">
                        <div class="<?php
                                            if($sort_item->selected):
                                                echo "item selected";
                                            else:
                                                echo "item active";
                                            endif;
                                        ?>">
                            <?php echo ucfirst($sort_item->name); ?>
                        </div>
                    </a>
                </div>
        <?php
            endforeach;
        ?>
    </div>
    <!-- Sorting END -->
    
    <!-- Add button -->
    <div class="add-button">
        <a href="<?php 
                        if($selected_subcategory)
                            admin_link("speed/form/add/" . $selected_subcategory->id);
                        else if($selected_category)
                            admin_link("speed/form/add/" . $selected_category->id);
                        else
                            admin_link("speed/form");
                    ?>">
            <div class="wrapper">
                <div class="name">
                    <span class="add-char">+</span>&nbsp;Add speed
                </div>
            </div>
        </a>
    </div>
    <!-- Add button END -->
    
</div>
<!-- Page controls END -->

<!-- Speeds and categories -->
<div class="page-content">
    
    <!-- Speeds -->
    <div class="module-items-wrapper">
        <div class="module-items ajax-module-items">
            <?php include("speeds_list_items.php"); ?>
        </div>

        <div class="bottom-controls">
            <!-- Pagination -->
            <div class="pagination">
                <div class="wrapper ajax-pagination">
                    <?php include("speeds_list_pagination.php"); ?>
                </div>
            </div>
            <!-- Pagination END -->
        </div>
    </div>
    <!-- Speeds END -->
    
    <!-- Categories -->
    <div class="module-categories">
        <?php include("speeds_list_categories.php"); ?>
    </div>
    <!-- Categories END -->
    
</div>
<!-- Speeds and categories END -->