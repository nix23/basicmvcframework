<!-- Page heading -->
<div class="page-heading">
    <div class="wrapper">

        <div class="legend">
            <div class="name">
                Spots
            </div>

            <div class="subname">
                <a href="<?php admin_link("spots"); ?>">
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
<div class="form">
    <!-- Add photo form -->
    <div id="photo-upload-spinner" class="photo-upload-form">
        <div class="wrapper">
            
            <!-- Header -->
            <div class="header">
                <h2>Photo Upload</h2>
                
                <div class="close"
                      onclick="effects.spinner.toggle('photo-upload-spinner')">
                </div>
            </div>
            <!-- Header END -->
            
            <!-- File select -->
            <div class="file-select">
                
                <form enctype="multipart/form-data" method="post" target="ajax-iframe">
                    <input type="file" name="upload-file">
                </form>
                
            </div>
            <!-- File select END -->
            
            <!-- Description -->
            <div class="description">
                <p>Minimal photo dimensions are: 800 * 600px. Please upload photos only with a high quality.
                    Please remember, that all images will be adjusted to landscape mode.</p>
            </div>
            <!-- Description END -->
            
            <!-- Actions -->
            <div class="actions">
                
                <div class="uploading">
                    <div class="icon">
                    </div>
                </div>
                
                <div class="upload">
                    <button type="button" class="button"
                              onclick="ajax_file_uploader.upload('photo-upload-spinner',
                                                                             'spot-photos',
                                                                             'spots',
                                                                             'upload_photo')">
                        Upload
                    </button>
                </div>
                
            </div>
            <!-- Actions END -->
            
            <!-- Footer -->
            <div class="footer">
            </div>
            <!-- Footer END -->
            
        </div>
    </div>
    <!-- Add photo form END -->
    
    <!-- Spot form -->
    <form name="spot-form">
    
    <!-- Heading -->
    <div class="heading">
        <?php echo $action; ?> spot
    </div>
    <!-- Heading END -->
    
    <!-- ID -->
    <input type="hidden" name="spot[id]" value="<?php echo $spot->id; ?>">
    <!-- ID END -->
    
    <!-- Album name -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Album name
            </div>
            
            <div class="description">
                It will be printed right after full category name, like 
                'BMW X5 <span class="underline">Album name</span>'.<br>
                Example: 'BMW X5 <span class="underline">exotic car spotted in Germany</span>'.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="spot[album_name]" maxlength="255" class="input long"
                     value="<?php echo $spot->album_name; ?>">
        </div>
        
    </div>
    <!-- Album name END -->
    
    <!-- Category name -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Add to category<span class="required">*</span>
            </div>
            
            <div class="description">
                Choose category,to which this spot will be added.
            </div>
        </div>
        
        <div class="element">
            <select name="category[select_category]" class="select"
                        id="fake-select-category"
                        onchange="form_tools.category_select.change_category()">
                <option value=""<?php
                                        if(!$is_editing and !$spot->category)
                                            echo " selected";
                                      ?>>
                </option>
                <?php
                    foreach($categories as $category):
                ?>
                        <option value="<?php 
                                                echo $category->id;
                                            ?>"
                                            <?php
                                                if($spot->category
                                                        and
                                                    $spot->category->id == $category->id)
                                                        echo " selected";
                                            ?>>
                            <?php echo $category->name; ?>
                        </option>
                <?php
                    endforeach;
                ?>
            </select>
        </div>
        
    </div>
    <!-- Category name END -->
    
    <!-- Subcategory name -->
    <div class="item<?php if(!$spot->subcategory) echo " hidden"; ?>">
        
        <div class="legend">
            <div class="name">
                Add to subcategory<span class="required">*</span>
            </div>
            
            <div class="description">
                Please also select subcategory.
            </div>
        </div>
        
        <div class="element">
            <select name="category[select_subcategory]" class="select"
                        id="fake-select-subcategory"
                        onchange="form_tools.category_select.change_subcategory()">
                <option value="">
                </option>
                <?php
                    if($spot->subcategory):
                        foreach($spot->category->subcategories as $subcategory):
                ?>
                            <option value="<?php
                                                    echo $subcategory->id;
                                                ?>"
                                                <?php
                                                    if($spot->subcategory->id == $subcategory->id)
                                                        echo " selected";
                                                ?>>
                                <?php echo $subcategory->name; ?>
                            </option>
                <?php
                        endforeach;
                    endif;
                ?>
            </select>
        </div>
        
    </div>
    <!-- Subcategory name END -->
    
    <!-- Category id -->
    <input type="hidden" name="spot[category_id]" 
                                id="real-category-input"
                                value="<?php echo $spot->category_id; ?>">
    <!-- Category id END -->
    
    <!-- Selected category -->
    <input type="hidden" name="category[selected_category_id]"
                                value="<?php if($selected_category_id) echo $selected_category_id; ?>">
    <!-- Selected category END -->
    
    <!-- User id -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Add as user<span class="required">*</span>
            </div>
            
            <div class="description">
                Please choose a user, <br>by which this post will be added.
            </div>
        </div>
        
        <div class="element">
            <select name="spot[user_id]" class="select">
                <option value=""<?php
                                        if(!$is_editing)
                                            echo " selected";
                                      ?>>
                </option>
                <?php
                    foreach($users as $user): 
                ?>
                    <option value="<?php
                                            echo $user->id;
                                        ?>"
                                        <?php
                                            if($spot->user_id == $user->id)
                                                echo " selected";
                                        ?>>
                        <?php echo $user->username; ?>
                    </option>
                <?php
                    endforeach;
                ?>
            </select>
        </div>
        
    </div>
    <!-- User id END -->
    
    <!-- Capture year -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Capture year<span class="required">*</span>
            </div>
            
            <div class="description">
                Select year,in which you took this spot photos.
            </div>
        </div>
        
        <div class="element">
            <select name="spot[capture_year]" class="select small">
                <option value="">
                </option>
                <?php
                    $current_year_selected = false;
                    for($year = date("Y", time()); $year >= 1950; $year--): 
                ?>
                        <option value="<?php
                                                echo $year;
                                            ?>"
                                            <?php
                                                if(!$is_editing):
                                                    if(!$current_year_selected):
                                                        echo " selected";
                                                        $current_year_selected = true;
                                                    endif;
                                                else:
                                                    if($spot->capture_year == $year)
                                                        echo " selected";
                                                endif;
                                            ?>>
                            <?php echo $year; ?>
                        </option>
                <?php
                    endfor;
                ?>
            </select>
        </div>
        
    </div>
    <!-- Capture year END -->
    
    <!-- Capture month -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Capture month<span class="required">*</span>
            </div>
            
            <div class="description">
                Please also select month.
            </div>
        </div>
        
        <div class="element">
            <select name="spot[capture_month]" class="select">
                <option value="">
                </option>
                <?php
                    $current_month = date("F", time()); 
                    foreach($months as $month_value => $month_name): 
                ?>
                        <option value="<?php
                                                echo $month_value;
                                            ?>"
                                            <?php
                                                if(!$is_editing):
                                                    if($month_name == $current_month):
                                                        echo " selected";
                                                    endif;
                                                else:
                                                    if($spot->capture_month == $month_value)
                                                        echo " selected";
                                                endif;
                                            ?>>
                            <?php echo $month_name; ?>
                        </option>
                <?php
                    endforeach;
                ?>
            </select>
        </div>
        
    </div>
    <!-- Capture month END -->
    
    <!-- Location -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Location
            </div>
            
            <div class="description">
                Please provide location(country/city),
                <br>where you took this spot photos.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="spot[location]" maxlength="255" class="input"
                     value="<?php echo $spot->location; ?>">
        </div>
        
    </div>
    <!-- Location END -->
    
    <!-- Event -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Event
            </div>
            
            <div class="description">
                At what event you took these photos?
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="spot[event]" maxlength="255" class="input"
                     value="<?php echo $spot->event; ?>">
        </div>
        
    </div>
    <!-- Event END -->
    
    <!-- Short description -->
    <div class="item">

        <div class="legend">
            <div class="name">
                Short description
            </div>

            <div class="description">
                You can write here main point of article.<br>
                It will be displayed right before text.
            </div>
        </div>

        <div class="element">
            <textarea name="spot[short_description]"
                         cols="30" rows="5"
                         class="textarea"><?php echo $spot->short_description; ?></textarea>
        </div>

    </div>
    <!-- Short description END -->

    <!-- Article -->
    <div class="item">

        <div class="legend">
            <div class="name">
                Article
            </div>

            <div class="description">
                Write article text here.<br>
                Minimal length - 300 symbols.<br><br>

                <span class="small-heading">Allowed formatting tags for text:</span><br>
                [b]Your text[/b] - bold text<br>
                [link=http://example.com]Link text[/link]<br><br>

                <span class="small-heading">Allowed photos formatting:</span><br>
                All your uploaded photos will be displayed<br>
                right after article text by default,<br>
                but you can insert some of photos right inside <br>
                article text using [photoset][/photoset] tag.<br><br>

                You can add any count of photos <br>
                using [img=number] tag inside [photoset] tag.<br> Number must match
                with number on photo<br> in photos block below this redactor.<br><br>

                Optionally,you can add photoset caption tag<br> [caption]Text[/caption]
                 after all image tags<br> right before closing [/photoset] tag.<br><br>

                 <span class="small-heading">Example:</span><br>
                 [photoset]
                 [img=1][img=2][img=3]<br>[caption]BMW believes,that this model <br>looks great.[/caption]<br>
                 [/photoset]
            </div>
        </div>

        <div class="element">
            <textarea name="spot[article]"
                         cols="30" rows="5"
                         class="textarea redactor"><?php echo $spot->article; ?></textarea>
        </div>

    </div>
    <!-- Article END -->
    
    <!-- Photos -->
    <div class="item" id="update-photo-upload-spinner-top">
        
        <div class="legend">
            <div class="name">
                Photos<span class="required">*</span>
            </div>
            
            <div class="description">
                Allowed formats: JPG, GIF and PNG.
                <br>
                Minimal photo dimensions are 800 * 600px.
                <br>
                Please upload only high quality photos.
                <br>
                All photos will be moderated.
            </div>
        </div>
        
        <div class="element">
            <div id="spot-photos" class="photos">
                
                <div class="add"
                      onclick="effects.spinner.toggle('photo-upload-spinner',
                                                                 'update-photo-upload-spinner-top',
                                                                 this)">
                    Add
                </div>
                
                <span class="previews">
                    <?php
                        if($spot->photos):
                            $photo_number = $spot->photos_count;
                            foreach($spot->photos as $photo):
                    ?>
                                <div class="preview"
                                      data-master-photo-name="<?php echo $photo->master_name; ?>"
                                      data-upload-directory="images/<?php echo $photo->directory; ?>">
                                    
                                    <img src="<?php load_photo($photo->master_name, 100, 75); ?>"
                                          width="100" height="75"
                                          onclick="gallery.load(this, 'spot-photos', 'preview')">
                                    
                                    <div class="number">
                                        <?php echo $photo_number; ?>
                                    </div>
                                    
                                    <div class="actions">
                                        <div class="<?php echo ($photo->main == "yes") ? "main-selected" : "main"; ?>"
                                              onclick="form_tools.photo.set_as_main(this)">
                                        </div>
                                        
                                        <div class="delete"
                                              onclick="form_tools.photo.remove(this)">
                                        </div>
                                    </div>
                                        
                                </div>
                    <?php
                                $photo_number--;
                            endforeach;
                        endif;
                    ?>
                </span>
                
                <span class="frames">
                    <?php
                        if($spot->photos):
                            $photo_number = 0;
                            foreach($spot->photos as $photo):
                    ?>
                                <input type="hidden" name="spot-photos[<?php echo $photo_number; ?>][frame]" 
                                                            id="<?php echo $photo->master_name; ?>" 
                                                            value="<?php echo $photo->master_name; ?>">
                    <?php
                                $photo_number++;
                            endforeach;
                        endif;
                    ?>
                </span>
                
                <input type="hidden" 
                         name="main_photo[master_name]"
                         value="<?php if($spot->main_photo) echo $spot->main_photo->master_name; ?>"
                         class="main-photo-master-name">
            
            </div>
        </div>
        
    </div>
    <!-- Photos END -->
    
    <!-- Status -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Status<span class="required">*</span>
            </div>
            
            <div class="description">
                Site users can't see disabled photosets.
            </div>
        </div>
        
        <div class="element">
            <div class="radio">
                <input type="radio" name="spot[status]" value="enabled"
                         <?php if($spot->status == "enabled" or !$is_editing) echo "checked"; ?>>
                <span class="padding">
                    Enabled
                </span>
            </div>
            
            <div class="radio newrow">
                <input type="radio" name="spot[status]" value="disabled"
                         <?php if($spot->status == "disabled") echo "checked"; ?>>
                <span class="padding">
                    Disabled
                </span>
            </div>
        </div>
        
    </div>
    <!-- Status END -->
    
    <!-- Moderated -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Moderated<span class="required">*</span>
            </div>
            
            <div class="description">
                Moderation status "yes" means that you have checked,<br>that content
                has good quality and is valid.
            </div>
        </div>
        
        <div class="element">
            <div class="radio">
                <input type="radio" name="spot[moderated]" value="yes"
                         <?php if($spot->moderated == "yes" or !$is_editing) echo "checked"; ?>>
                <span class="padding">
                    Yes
                </span>
            </div>
            
            <div class="radio newrow">
                <input type="radio" name="spot[moderated]" value="no"
                         <?php if($spot->moderated == "no") echo "checked"; ?>>
                <span class="padding">
                    No
                </span>
            </div>
        </div>
        
    </div>
    <!-- Moderated END -->

    <!-- Moderation fail text -->
    <div class="item">

        <div class="legend">
            <div class="name">
                Moderation fail message
            </div>

            <div class="description">
                If this spot isn't passing moderation,please specify reason.<br>
                Uploader will see it at drive module.
            </div>
        </div>

        <div class="element">
            <input type="text" name="spot[moderation_fail_text]" maxlength="255" class="input long"
                     value="<?php echo $spot->moderation_fail_text; ?>">
        </div>

    </div>
    <!-- Moderation fail text END -->
    
    <!-- Author -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Author
            </div>
            
            <div class="description">
                If you aren't sure,that you can add this content,<br>
                please add photoset author.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="spot[author]" maxlength="255" class="input"
                     value="<?php echo $spot->author; ?>">
        </div>
        
    </div>
    <!-- Author END -->
    
    <!-- Source -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Source
            </div>
            
            <div class="description">
                Photoset or description source.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="spot[source]" maxlength="255" class="input"
                     value="<?php echo $spot->source; ?>">
        </div>
        
    </div>
    <!-- Source END -->
    
    <?php
        if($is_editing):
    ?>
            <!-- Update postdate -->
            <div class="item">
                
                <div class="legend">
                    <div class="name">
                        Update postdate<span class="required">*</span>
                    </div>
                    
                    <div class="description">
                        If you will update postdate,it will be sorted as newest.
                    </div>
                </div>
                
                <div class="element">
                    <div class="radio">
                        <input type="radio" name="special[update_postdate]" value="yes" checked>
                        <span class="padding">Yes</span>
                    </div>
                    
                    <div class="radio newrow">
                        <input type="radio" name="special[update_postdate]" value="no">
                        <span class="padding">No</span>
                    </div>
                </div>
                
            </div>
            <!-- Update postdate END -->
    <?php
        else:
    ?>
            <input type="hidden" name="special[update_postdate]" value="yes">
    <?php
        endif;
    ?>
    
    <!-- Token -->
    <input type="hidden" name="token[name]"  value="spot-form">
    <input type="hidden" name="token[value]" value="<?php token('spot-form'); ?>">
    <!-- Token END -->
    
    <!-- Submit and Loading -->
    <div class="item">
        
        <div class="save">
            <button type="button" id="form-submit" class="submit"
                      onclick="ajax.process_form('spot-form', 'spots', 'save', 'ajax')">
                Save
            </button>
        </div>
        
        <div class="loading" id="form-loading">
        </div>
        
    </div>
    <!-- Submit and Loading END -->
    
    </form>
    <!-- Spot form END -->
</div>
<!-- Form END -->