<!-- Speeds table -->
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
    
    <!-- Speeds -->
    <?php
        $highlight = 1;
        foreach($speeds as $speed):
    ?>
            <tr class="item<?php if($highlight % 2 == 0) echo " highlight"; ?>">
                <!-- Overall -->
                <td class="overall-cell">
                    <div class="wrapper">
                        
                        <div class="photo">
                            <img src="<?php
                                            load_photo($speed->main_photo->master_name,
                                                          80,
                                                          60);
                                         ?>" width="80" height="60">
                            
                            <div class="adddate">
                                <?php echo date("d/m/Y H:i", strtotime($speed->posted_on)); ?>
                            </div>
                        </div>
                        
                        <div class="overall">
                            <div class="heading">
                                <a href="<?php 
                                                speed_item_link($speed,
                                                                     $speed->category,
                                                                     $speed->subcategory);
                                             ?>">
                                    <h3 class="trim-to-parent">
                                        <?php 
                                            echo $speed->heading;
                                        ?>
                                    </h3>
                                </a>
                            </div>
                            
                            <div class="info">
                                <span class="label">
                                    Moderated:
                                </span>
                                
                                <span class="message"
                                        onclick="ajax.process_form('speeds-list',
                                                                            'speed',
                                                                            'change_moderation',
                                                                            'ajax/<?php echo $speed->id; ?>',
                                                                            this,
                                                                            'modal')">
                                    <?php echo ucfirst($speed->moderated); ?>
                                </span>
                                
                                <span class="label">
                                    Comments:
                                </span>
                                
                                <span class="message">
                                    <?php echo $speed->comments_count; ?>
                                </span>

                                <span class="label">
                                    Views:
                                </span>

                                <span class="message">
                                    <?php echo $speed->item_views_count; ?>
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
                                <?php echo $speed->likes_count; ?>
                            </div>
                            
                            <div class="label">
                                <?php
                                    if($speed->likes_count == 1):
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
                            onclick="ajax.process_form('speeds-list',
                                                                'speed',
                                                                'change_status',
                                                                'ajax/<?php echo $speed->id; ?>',
                                                                this,
                                                                'modal')">
                        <?php echo ucfirst($speed->status); ?>
                    </span>
                </td>
                <!-- Status END -->
                
                <!-- Actions -->
                <td class="actions-cell">
                    <div class="wrapper">
                        
                        <div class="edit">
                            <a href="<?php 
                                            if($selected_subcategory)
                                                admin_link("speed/form/{$speed->id}/{$selected_subcategory->id}");
                                            else if($selected_category)
                                                admin_link("speed/form/{$speed->id}/{$selected_category->id}");
                                            else
                                                admin_link("speed/form/{$speed->id}");
                                         ?>">
                                Edit
                            </a>
                        </div>

                        <div class="delete"
                              onclick="form_tools.delete_confirmation.show('speeds-list',
                                                                                          'speed',
                                                                                          'delete',
                                                                                          'ajax/<?php
                                                                                                echo $speed->id;
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
    <!-- Speeds END -->
    
    <!-- Token -->
    <form name='speeds-list'>
        <input type='hidden' name='token[name]'  value='speeds-list'>
        <input type='hidden' name='token[value]' value='<?php token('speeds-list'); ?>'>
    </form>
    <!-- Token END -->
    
    <?php
        if(!$speeds):
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
<!-- Speeds table END -->