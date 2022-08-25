<!-- Subcategories container -->
<td colspan="4" class="subcategories-wrapper">
    <!-- Subcategories table -->
    <table cellspacing="0" cellpadding="0" class="subcategories-table">
        <?php
            foreach($category->subcategories as $subcategory):
        ?>
            <!-- Subcategory row -->
            <tr class="subcategory">
                <!-- Name -->
                <td class="name-cell">
                    <div class="name">
                        <?php echo $subcategory->name; ?>
                    </div>
                </td>
                <!-- Name END -->
                
                <!-- Show in modules -->
                <td class="show-in-modules-cell">
                    <span class="show_in_modules">
                        Inherit
                    </span>
                </td>
                <!-- Show in modules END -->
                
                <!-- Status -->
                <td class="status-cell">
                    <span class="status"
                            onclick="ajax.process_form('categories-list',
                                                                'categories',
                                                                'change_status',
                                                                'ajax/<?php echo $subcategory->id; ?>',
                                                                this,
                                                                'modal')">
                        <?php echo ucfirst($subcategory->status); ?>
                    </span>
                </td>
                <!-- Status END -->
                
                <!-- Actions -->
                <td class="actions-cell">
                    <div class="wrapper">
                        
                        <div class="edit">
                            <a href="<?php admin_link("categories/form/" . $subcategory->id); ?>">
                                Edit
                            </a>
                        </div>
                        
                        <div class="delete"
                              onclick="form_tools.delete_confirmation.show('categories-list',
                                                                                          'categories',
                                                                                          'delete_subcategory',
                                                                                          'ajax/<?php echo $subcategory->id; ?>',
                                                                                          this,
                                                                                          'modal')">
                            Delete
                        </div>
                        
                    </div>
                </td>
                <!-- Actions END -->
            </tr>
            <!-- Subcategory row END -->
        <?php
            endforeach;
        ?>
    </table>
    <!-- Subcategories table END -->
</td>
<!-- Subcategories container END -->