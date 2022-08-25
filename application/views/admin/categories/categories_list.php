<!-- Page heading -->
<div class="page-heading">
    <div class="wrapper">

        <div class="legend">
            <div class="name">
                Categories
            </div>

            <div class="subname">
                List
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
    </div>
    <!-- Pagination END -->
    
    <!-- Sorting -->
    <div class="sorting">
    </div>
    <!-- Sorting END -->
    
    <!-- Add button -->
    <div class="add-button">
        <a href="<?php admin_link("categories/form"); ?>">
            <div class="wrapper">
                <div class="name">
                    <span class="add-char">+</span>&nbsp;Add category
                </div>
            </div>
        </a>
    </div>
    <!-- Add button END -->
    
</div>
<!-- Page controls END -->

<!-- Categories table -->
<table cellspacing="0" cellpadding="0" class="categories-table">
    <!-- Heading -->
    <tr id="heading">
        <th id="name">
            Name
        </th>
        
        <th id="show-in-modules">
            Show in modules
        </th>
        
        <th id="status">
            Status
        </th>
        
        <th id="actions">
            Actions
        </th>
    </tr>
    <!-- Heading END -->
    
    <!-- Categories -->
    <?php
        $highlight = 1;
        foreach($categories as $category):
    ?>
            <!-- Category row -->
            <tr class="category<?php if($highlight % 2 == 0) echo " highlight"; ?>">
                <!-- Name -->
                <td class="name-cell">
                    <div class="wrapper"
                          onclick="category.toggle_children(this,
                                                                         <?php echo $category->id; ?>)">
                        
                        <div class="name">
                            <?php echo $category->name; ?>
                        </div>
                        
                        <div class="subcategories-count">
                            <?php
                                echo "<span class='count'>" . $category->subcategories_count . "</span>";
                                
                                if($category->subcategories_count == 1):
                                    echo " subcategory";
                                else:
                                    echo " subcategories";
                                endif;
                            ?>
                        </div>
                        
                    </div>
                </td>
                <!-- Name END -->
                
                <!-- Show in modules -->
                <td class="show-in-modules-cell">
                    <span class="show_in_modules">
                        <?php echo ucfirst($category->show_in_modules); ?>
                    </span>
                </td>
                <!-- Show in modules END -->
                
                <!-- Status -->
                <td class="status-cell">
                    <span class="status"
                            onclick="ajax.process_form('categories-list',
                                                                'categories',
                                                                'change_status',
                                                                'ajax/<?php echo $category->id; ?>',
                                                                this,
                                                                'modal')">
                        <?php echo ucfirst($category->status); ?>
                    </span>
                </td>
                <!-- Status END -->
                
                <!-- Actions -->
                <td class="actions-cell">
                    <div class="wrapper">
                        
                        <div class="edit">
                            <a href="<?php admin_link("categories/form/" . $category->id); ?>">
                                Edit
                            </a>
                        </div>
                        
                        <div class="delete"
                              onclick="form_tools.delete_confirmation.show('categories-list',
                                                                                          'categories',
                                                                                          'delete_root_category',
                                                                                          'ajax/<?php echo $category->id; ?>',
                                                                                          this,
                                                                                          'modal')">
                            Delete
                        </div>
                        
                    </div>
                </td>
                <!-- Actions END -->
            </tr>
            <!-- Category row END -->
            
            <!-- Subcategories -->
            <tr class="hidden" id="<?php echo $category->id; ?>">
            </tr>
            <!-- Subcategories END -->
    <?php
            $highlight++;
        endforeach;
    ?>
    <!-- Categories END -->
    
    <!-- Token -->
    <form name='categories-list'>
        <input type='hidden' name='token[name]'  value='categories-list'>
        <input type='hidden' name='token[value]' value='<?php token('categories-list'); ?>'>
    </form>
    <!-- Token END -->
    
    <?php
        //if(!$categories):
        //  echo "no categories. Please add some";
    ?>
</table>
<!-- Categories table END -->