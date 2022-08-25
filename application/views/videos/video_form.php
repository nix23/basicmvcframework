<!-- Modal helper form -->
<div id="modal-helper-form">
    <div id="modal-helper-form-spinner"
          class="modal-helper-form">
        <div class="spinner-wrapper">
            <div class="tags-example"
                    onclick="form_tools.modal_helper.hide()">
            </div>
        </div>
    </div>
</div>
<!-- Model helper form END -->

<!-- Form -->
<div class="form">
    
    <!-- Add photo form -->
    <div id="photo-upload-spinner" class="photo-upload-form">
        <div class="spinner-wrapper">
            
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
                                                                             'video-photos',
                                                                             'videos',
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
    
    <!-- Video form -->
    <form name="video-form">
    
    <!-- Backlink -->
    <div class="backlink">
        <a href="<?php public_link($catalog_backlink); ?>">
            << Back to catalog
        </a>
    </div>
    <!-- Backlink END -->
    
    <!-- Heading -->
    <div class="heading">
        <?php echo $action; ?> video
    </div>
    <!-- Heading END -->
    
    <!-- ID -->
    <input type="hidden" name="video[id]" value="<?php echo $video->id; ?>">
    <!-- ID END -->
    
    <!-- User ID -->
    <input type="hidden" name="video[user_id]" value="<?php echo $video->user_id; ?>">
    <!-- User ID END -->
    
    <!-- Heading -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Heading<span class="required">*</span>
            </div>
            
            <div class="description">
                Choose video heading.<br>
                Minimal length - 20 symbols.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="video[heading]" maxlength="255" class="input long"
                     value="<?php echo $video->heading; ?>">
        </div>
        
    </div>
    <!-- Heading END -->
    
    <!-- Category name -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Add to category<span class="required">*</span>
            </div>
            
            <div class="description">
                Choose category,to which this video will be attached.
            </div>
        </div>
        
        <div class="element">
            <select name="category[select_category]" class="select"
                        id="fake-select-category"
                        onchange="form_tools.category_select.change_category()">
                <option value=""<?php
                                        if(!$is_editing and !$video->category)
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
                                                if($video->category
                                                        and
                                                    $video->category->id == $category->id)
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
    <div class="item<?php if(!$video->subcategory) echo " hidden"; ?>">
        
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
                    if($video->subcategory):
                        foreach($video->category->subcategories as $subcategory):
                ?>
                            <option value="<?php
                                                    echo $subcategory->id;
                                                ?>"
                                                <?php
                                                    if($video->subcategory->id == $subcategory->id)
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
    <input type="hidden" name="video[category_id]" 
                                id="real-category-input"
                                value="<?php echo $video->category_id; ?>">
    <!-- Category id END -->
    
    <!-- Short description -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Short description<span class="required">*</span>
            </div>
            
            <div class="description">
                Please write here main point of video or video article.<br>
                Minimal length - 20 symbols.
            </div>
        </div>
        
        <div class="element">
            <textarea name="video[short_description]"
                         cols="30" rows="5"
                         class="textarea"><?php echo $video->short_description; ?></textarea>
        </div>
        
    </div>
    <!-- Short description END -->
    
    <!-- Article -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Video article<span class="required">*</span>
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

                 <br><span class="helper-link"
                              onclick="form_tools.modal_helper.show()">View example</span>
            </div>
        </div>
        
        <div class="element">
            <textarea name="video[article]"
                         cols="30" rows="5"
                         class="textarea redactor"><?php echo $video->article; ?></textarea>
        </div>
        
    </div>
    <!-- Article END -->
    
    <!-- Video URL -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Video URL<span class="required">*</span>
            </div>
            
            <div class="description">
                Paste video URL here.<br>
                Format: "http://www.youtube.com/embed/video_id"<br>
                It will be displayed after article text.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="video[video_url]" maxlength="255" class="input long"
                     value="<?php echo $video->video_url; ?>">
        </div>
        
    </div>
    <!-- Video URL END -->
    
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
            <?php $gallery_photo_number = 0; ?>
            <div id="video-photos" class="photos"
                    data-collect-resolutions="no"
                    data-module="videos"
                    data-heading="Video photo"
                    data-subheading="Images with all available resolutions will be created automatically">
                
                <div class="add"
                      onclick="effects.spinner.toggle('photo-upload-spinner',
                                                                 true,
                                                                 'update-photo-upload-spinner-top',
                                                                 this)">
                    Add
                </div>
                
                <span class="previews">
                    <?php
                        if($video->photos):
                            $photo_number = $video->photos_count;
                            foreach($video->photos as $photo):
                    ?>
                                <div class="preview"
                                      data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
                                      data-master-photo-name="<?php echo $photo->master_name; ?>"
                                      data-upload-directory="images/<?php echo $photo->directory; ?>">
                                    
                                    <img src="<?php load_photo($photo->master_name, 100, 75); ?>"
                                          onclick="gallery.load(this, 'video-photos', 'preview')"
                                          width="100" height="75">
                                    
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
                        if($video->photos):
                            $photo_number = 0;
                            foreach($video->photos as $photo):
                    ?>
                                <input type="hidden" name="video-photos[<?php echo $photo_number; ?>][frame]" 
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
                         value="<?php if($video->main_photo) echo $video->main_photo->master_name; ?>"
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
                Other users can't see disabled video.<br>
                You can enable it any time later.
            </div>
        </div>
        
        <div class="element">
            <div class="radio">
                <input type="radio" name="video[status]" value="enabled"
                         <?php if($video->status == "enabled" or !$is_editing) echo "checked"; ?>>
                <span class="padding">
                    Enabled
                </span>
            </div>
            
            <div class="radio newrow">
                <input type="radio" name="video[status]" value="disabled"
                         <?php if($video->status == "disabled") echo "checked"; ?>>
                <span class="padding">
                    Disabled
                </span>
            </div>
        </div>
        
    </div>
    <!-- Status END -->
    
    <!-- Author -->
    <div class="item">
        
        <div class="legend">
            <div class="name">
                Author
            </div>
            
            <div class="description">
                You can specify video author here.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="video[author]" maxlength="255" class="input"
                     value="<?php echo $video->author; ?>">
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
                If it's required,you can specify this video source link.
                <br> Format: 'http://sitename.com/path'.
            </div>
        </div>
        
        <div class="element">
            <input type="text" name="video[source]" maxlength="255" class="input"
                     value="<?php echo $video->source; ?>">
        </div>
        
    </div>
    <!-- Source END -->
    
    <!-- Token -->
    <input type="hidden" name="token[name]"  value="video-form">
    <input type="hidden" name="token[value]" value="<?php token('video-form'); ?>">
    <!-- Token END -->
    
    <!-- Submit and Loading -->
    <div class="item">
        
        <div class="save">
            <button type="button" id="form-submit" class="submit"
                      onclick="ajax.process_form('video-form', 'videos', 'save', 'ajax')">
                Save
            </button>
        </div>
        
        <div class="loading" id="form-loading">
        </div>
        
    </div>
    <!-- Submit and Loading END -->
    
    <?php
        if($is_editing):
    ?>
        <!-- Remoderation warning -->
        <div class="item">
            <div class="remoderation-warning">
                ! Please remember,that item update will cause remoderation.
            </div>
        </div>
        <!-- Remoderation warning -->
    <?php
        endif;
    ?>
    
    </form>
    <!-- Video form END -->
    
</div>
<!-- Form END -->