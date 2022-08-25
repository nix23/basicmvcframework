<!-- Spots table -->
<table cellspacing="0" cellpadding="0" class="items-table trim-divs">
    <!-- Heading -->
    <tr id="heading">
        <th id="overall">
            Overall
        </th>
        
        <th id="likes">
            Likes
        </th>
        
        <th id="status">
            Status
        </th>
        
        <th id="actions">
            Actions
        </th>
    </tr>
    <!-- Heading END -->
    
    <!-- Spots -->
    <?php
        $highlight = 1;
        foreach($spots as $spot):
    ?>
            <tr class="item<?php if($highlight % 2 == 0) echo " highlight"; ?>">
                <!-- Overall -->
                <td class="overall-cell">
                    <div class="wrapper">
                        
                        <div class="photo">
                            <img src="<?php
                                            load_photo($spot->main_photo->master_name,
                                                          80,
                                                          60);
                                         ?>" width="80" height="60">
                            
                            <div class="adddate">
                                <?php echo date("d/m/Y H:i", strtotime($spot->posted_on)); ?>
                            </div>
                        </div>
                        
                        <div class="overall">
                            <div class="heading">
                                <a href="<?php 
                                                spot_item_link($spot,
                                                                    $spot->category,
                                                                    $spot->subcategory);
                                             ?>">
                                    <h3 class="trim-to-parent">
                                        <?php 
                                            stringify(array(
                                                "{$spot->capture_year}/" . ++$spot->capture_month,
                                                $spot->category_name,
                                                $spot->subcategory_name,
                                                $spot->album_name
                                            ));
                                        ?>
                                    </h3>
                                </a>
                            </div>
                            
                            <div class="info">
                                <span class="label">
                                    Moderated:
                                </span>
                                
                                <span class="message"
                                        onclick="ajax.process_form('spots-list',
                                                                            'spots',
                                                                            'change_moderation',
                                                                            'ajax/<?php echo $spot->id; ?>',
                                                                            this,
                                                                            'modal')">
                                    <?php echo ucfirst($spot->moderated); ?>
                                </span>
                                
                                <span class="label">
                                    Comments:
                                </span>
                                
                                <span class="message">
                                    <?php echo $spot->comments_count; ?>
                                </span>

                                <span class="label">
                                    Views:
                                </span>

                                <span class="message">
                                    <?php echo $spot->item_views_count; ?>
                                </span>
                            </div>
                        </div>
                        
                    </div>
                </td>
                <!-- Overall END -->
                
                <!-- Likes -->
                <td class="likes-cell">
                    <div class="likes">
                        
                        <div class="wrapper">
                            <div class="count">
                                <?php echo $spot->likes_count; ?>
                            </div>
                            
                            <div class="label">
                                <?php
                                    if($spot->likes_count == 1):
                                        echo ucfirst("like");
                                    else:
                                        echo ucfirst("likes");
                                    endif;
                                ?>
                            </div>
                        </div>
                        
                    </div>
                </td>
                <!-- Likes END -->
                
                <!-- Status -->
                <td class="status-cell">
                    <span class="status"
                            onclick="ajax.process_form('spots-list',
                                                                'spots',
                                                                'change_status',
                                                                'ajax/<?php echo $spot->id; ?>',
                                                                this,
                                                                'modal')">
                        <?php echo ucfirst($spot->status); ?>
                    </span>
                </td>
                <!-- Status END -->
                
                <!-- Actions -->
                <td class="actions-cell">
                    <div class="wrapper">
                        
                        <div class="edit">
                            <a href="<?php 
                                            if($selected_subcategory)
                                                admin_link("spots/form/{$spot->id}/{$selected_subcategory->id}");
                                            else if($selected_category)
                                                admin_link("spots/form/{$spot->id}/{$selected_category->id}");
                                            else
                                                admin_link("spots/form/{$spot->id}");
                                         ?>">
                                Edit
                            </a>
                        </div>

                        <div class="delete"
                              onclick="form_tools.delete_confirmation.show('spots-list',
                                                                                          'spots',
                                                                                          'delete',
                                                                                          'ajax/<?php
                                                                                                echo $spot->id;
                                                                                                echo "/" . $current_page;
                                                                                                echo "/" . $selected_sort;
                                                                                                if($selected_subcategory)
                                                                                                    echo "/" . $selected_subcategory->id;
                                                                                                else if($selected_category)
                                                                                                    echo "/" . $selected_category->id;
                                                                                                ?>',
                                                                                          false,
                                                                                          'modal')">
                            Delete
                        </div>
                        
                    </div>
                </td>
                <!-- Actions END -->
            </tr>
    <?php
            $highlight++;
        endforeach;
    ?>
    <!-- Spots END -->
    
    <!-- Token -->
    <form name='spots-list'>
        <input type='hidden' name='token[name]'  value='spots-list'>
        <input type='hidden' name='token[value]' value='<?php token('spots-list'); ?>'>
    </form>
    <!-- Token END -->
    
    <?php
        if(!$spots):
    ?>
            <tr class="no-items">
                <td colspan="4">
                    
                    <div>
                        No items attached to this category. Please add some.
                    </div>
                    
                </td>
            </tr>
    <?php
        endif;
    ?>
</table>
<!-- Spots table END -->