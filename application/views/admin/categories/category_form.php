<!-- Page heading -->
<div class="page-heading">
    <div class="wrapper">

        <div class="legend">
            <div class="name">
                Categories
            </div>

            <div class="subname">
                <a href="<?php echo admin_link("categories"); ?>">
                    List
                </a>

                <span class="separator padding">
                    >>
                </span>

                <span class="padding">
                    Form
                </span>
            </div>
        </div>

        <?php echo $settings; ?>
        
    </div>
</div>
<!-- Page heading END -->

<!-- Form -->
<form name="category-form">
<div class="form">
        
    <!-- Heading -->
    <div class="heading">
        <?php echo $action; ?> category
    </div>
    <!-- Heading END -->
    
    <!-- ID -->
    <input type="hidden" name="category[id]" value="<?php echo $category->id; ?>">
    <!-- ID END -->
    
    <!-- Name -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Name<span class="required">*</span>
            </div>
            
            <div class="description">
                Max length - 255 symbols.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="category[name]" maxlength="255" class="input"
                     value="<?php echo $category->name; ?>">
        </div>
        
    </div>
    <!-- Name END -->
    
    <!-- Parent category -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Parent category<span class="required">*</span>
            </div>
            
            <div class="description">
                This category will be a children of <br>category selected in this box.
            </div>
        </div>
        
        <div class="element">
            <select name="category[parent_id]" class="select" 
                      onchange="form_tools.parent_category_select(this)">
                <option value=""<?php
                                        if(!$is_editing)
                                            echo " selected";
                                      ?>>
                </option>
                <?php
                    foreach($parent_categories as $parent_category): ;
                ?>
                        <option value="<?php
                                                echo $parent_category->id;
                                            ?>"
                                            <?php
                                                if($parent_category->id == $category->parent_id)
                                                    echo " selected";
                                            ?>>
                            <?php
                                if($parent_category->show_in_modules == "all") 
                                    echo $parent_category->name;
                                else 
                                    echo $parent_category->name . " / " . ucfirst($parent_category->show_in_modules); 
                            ?>
                        </option>
                <?php
                    endforeach;
                ?>
            </select>
        </div>
        
    </div>
    <!-- Parent category END -->
    
    <!-- Show in modules -->
    <div class="item<?php if($category->parent_id != '0') echo " hidden"; ?>"
          id="show-in-modules-list">
        
        <div class="legend">
            <div class="name">
                Show in modules<span class="required">*</span>
            </div>
            
            <div class="description">
                If you will choose 'All' option,this category will be available
                <br>
                in all site modules,otherwise only in specific module.
                <br>
                Subcategories will inherit this setting from parent category.
            </div>
        </div>
        
        <?php
            // Send disabled radio value
            if($is_editing):
        ?>
                <input type="hidden" name="category[show_in_modules]"
                                            value="<?php echo $category->show_in_modules; ?>">
        <?php
            endif;
        ?>
        
        <div class="element">
            <div class="radio">
                <input type="radio" name="category[show_in_modules]" value="all"
                         <?php if($category->show_in_modules == "all" or !$is_editing) echo "checked"; ?>
                         <?php if($is_editing) echo "disabled"; ?>>
                <span class="padding">
                    All
                </span>
            </div>

            <div class="radio newrow">
                <input type="radio" name="category[show_in_modules]" value="photos"
                         <?php if($category->show_in_modules == "photos") echo "checked"; ?>
                         <?php if($is_editing) echo "disabled"; ?>>
                <span class="padding">
                    Photos
                </span>
            </div>

            <div class="radio newrow">
                <input type="radio" name="category[show_in_modules]" value="spots"
                         <?php if($category->show_in_modules == "spots") echo "checked"; ?>
                         <?php if($is_editing) echo "disabled"; ?>>
                <span class="padding">
                    Spots
                </span>
            </div>
            
            <div class="radio newrow">
                <input type="radio" name="category[show_in_modules]" value="speed"
                         <?php if($category->show_in_modules == "speed") echo "checked"; ?>
                         <?php if($is_editing) echo "disabled"; ?>>
                <span class="padding">
                    Speed
                </span>
            </div>
            
            <div class="radio newrow">
                <input type="radio" name="category[show_in_modules]" value="videos"
                         <?php if($category->show_in_modules == "videos") echo "checked"; ?>
                         <?php if($is_editing) echo "disabled"; ?>>
                <span class="padding">
                    Videos
                </span>
            </div>
        </div>
        
    </div>
    <!-- Show in modules END -->
    
    <!-- Status -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Status<span class="required">*</span>
            </div>
            
            <div class="description">
                Site users can't see disabled categories.
                <br>
                ***Under development.
            </div>
        </div>
        
        <div class="element">
            <div class="radio">
                <input type="radio" name="category[status]" value="enabled"
                         <?php if($category->status == "enabled" or !$is_editing) echo "checked"; ?>>
                <span class="padding">
                    Enabled
                </span>
            </div>
            
            <div class="radio newrow">
                <input type="radio" name="category[status]" value="disabled"
                         <?php if($category->status == "disabled") echo "checked"; ?>>
                <span class="padding">
                    Disabled
                </span>
            </div>
        </div>
        
    </div>
    <!-- Status END -->
    
    <!-- Token -->
    <input type="hidden" name="token[name]"  value="category-form">
    <input type="hidden" name="token[value]" value="<?php token('category-form'); ?>">
    <!-- Token END -->
    
    <!-- Submit and Loading -->
    <div class="item">
        
        <div class="save">
            <button type="button" id="form-submit" class="submit"
                      onclick="ajax.process_form('category-form', 'categories', 'save', 'ajax')">
                Save
            </button>
        </div>
        
        <div class="loading" id="form-loading">
        </div>
        
    </div>
    <!-- Submit and Loading END -->
    
</div>
</form>
<!-- Form END -->