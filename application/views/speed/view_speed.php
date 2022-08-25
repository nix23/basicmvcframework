<!-- Item -->
<div class="view-item">
    
    <!-- Catalog back link -->
    <div class="backlink">
        <a href="<?php public_link($catalog_backlink); ?>">
            << Back to catalog
        </a>
    </div>
    <!-- Catalog back link END -->
    
    <!-- Ajax requests token -->
    <form name='view-speed'>
        <input type='hidden' name='token[name]'  value='view-speed'>
        <input type='hidden' name='token[value]' value='<?php token('view-speed'); ?>'>
    </form>
    <!-- Ajax request token END -->
    
    <!-- Speed header -->
    <table cellspacing="0" cellpadding="0" class="header-table">
        <tr>
            <td class="heading-cell">
                <div class="heading">
                    <h1>
                        <?php echo $speed->heading; ?>
                    </h1>
                </div>

                <div class="panel">
                    <div class="item float-left">
                        Posted: <span class="highlight"><?php echo time_ago($speed->posted_on); ?></span>
                    </div>

                    <div class="item float-right">
                        <span class="highlight">
                            <?php echo $speed->item_views_count; ?>
                        </span>

                        <?php echo ($speed->item_views_count == 1) ? "Read" : "Reads"; ?>
                    </div>
                </div>
            </td>
            
            <td class="info-cell">
                <div class="comment">
                    <div class="count">
                        <?php echo $speed->comments_total_count; ?>
                    </div>
                    
                    <div class="label">
                        <?php echo ($speed->comments_total_count == 1) ? "Comment" : "Comments"; ?>
                    </div>
                </div>
            </td>
        </tr>
        
        <tr>
            <td colspan="2" class="separator">
            </td>
        </tr>
    </table>
    <!-- Speed header END -->
    
    <!-- Speed data and categories -->
    <div class="item-content">
        <!-- Speed data -->
        <?php $gallery_photo_number = 0; ?>
        <div id="gallery-photos" class="item-data"
              data-viewed-item-id="<?php echo $speed->id; ?>"
              data-collect-resolutions="yes"
              data-module="speed"
              data-heading="<?php full_category_name($speed); ?>"
              data-subheading="<?php echo $speed->heading; ?>">
            
            <!-- Overall -->
            <div class="overall">
                
                <!-- Main photo -->
                <div class="main-photo gallery-photo"
                      data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
                      data-photo-id="<?php echo $speed->main_photo->id; ?>"
                      data-master-photo-name="<?php echo $speed->main_photo->master_name; ?>"
                      data-upload-directory="images/<?php echo $speed->main_photo->directory; ?>"
                      data-packed-resolutions="<?php 
                                pack_resolutions_for_gallery($speed->main_photo->lazy_clones); 
                                                        ?>">
                    <div class="wrapper">
                        
                        <img src="<?php
                                        load_photo( $speed->main_photo->master_name,
                                                        380,
                                                        245);
                                     ?>" width="380" height="245"
                              onclick="gallery.load(this, 'gallery-photos', 'gallery-photo')">
                        
                        <div class="wallpapers">
                            
                            <div class="count">
                                <?php echo $speed->main_photo->lazy_clones_count; ?>
                            </div>

                            <?php
                                if($speed->main_photo->lazy_clones_count == 1)
                                    echo "<div class='hrphotos-icon-singular'></div>";
                                else
                                    echo "<div class='hrphotos-icon-plural'></div>";
                            ?>
                            
                            <?php
                                foreach($speed->main_photo->lazy_clones as $lazy_clone_array):
                                    $lazy_clone = (object) $lazy_clone_array;
                                    
                                    if($lazy_clone->exists):
                            ?>
                                        <div class="wallpaper">
                                            <a href="<?php
                                                            $url_segments  = "services/viewphoto/speed";
                                                            $url_segments .= "/" . $speed->main_photo->id;
                                                            $url_segments .= "/" . $lazy_clone->width;
                                                            $url_segments .= "/" . $lazy_clone->height;
                                                            public_link($url_segments);
                                                        ?>" target="_blank"
                                                onmouseover="html_tools.module_item.wallpaper_over(this)"
                                                onmouseout="html_tools.module_item.wallpaper_out(this)"
                                                rel="nofollow">
                                                <div class="wrapper">
                                                    <div class="spacer">
                                                    </div>
                                                    
                                                    <div class="size width">
                                                        <?php echo $lazy_clone->width; ?>
                                                    </div>
                                                    
                                                    <div class="size height">
                                                        <?php echo $lazy_clone->height; ?>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                            <?php
                                    endif;
                                endforeach;
                            ?>
                            
                        </div>
                        
                    </div>
                </div>
                <!-- Main photo END -->
                
                <!-- Information panel -->
                <div class="info">
                        
                    <!-- Header -->
                    <div class="header">
                        
                        <!-- Avatar -->
                        <a href="<?php
                                        if($speed->user->is_current_logged_user())
                                            public_link("drive");
                                        else
                                            public_link("profile/view/user-{$speed->user->id}");
                                    ?>">
                            <div class="avatar">
                                <?php
                                    if($speed->user->has_avatar()):
                                ?>
                                        <img src='<?php
                                                            load_photo($speed->user->avatar_master_name,
                                                                          70,
                                                                          70);
                                                    ?>' width="70" height="70">
                                <?php
                                    else:
                                ?>
                                        <div class="no-avatar">
                                        </div>
                                <?php
                                    endif;
                                ?>

                                <?php
                                    if($speed->user->is_current_logged_user())
                                        echo "<div class='you-label'></div>";
                                ?>
                            </div>
                        </a>
                        <!-- Avatar END -->
                        
                        <!-- Legend -->
                        <div class="legend">
                            <div class="label">
                                <div class="row">
                                    <a href="<?php
                                                    if($speed->user->is_current_logged_user())
                                                        public_link("drive");
                                                    else
                                                        public_link("profile/view/user-{$speed->user->id}");
                                                ?>">
                                        <span class="link">
                                            <?php echo $speed->user->username; ?>
                                        </span>
                                    </a>
                                </div>
                                
                                <div class="trimmer">
                                </div>
                            </div>

                            <div class="sublabel">
                                <?php
                                    if($speed->user->has_subname())
                                        echo $speed->user->subname;
                                    else
                                        time_on_site($speed->user->registred_on);
                                ?>
                            </div>
                        </div>
                        <!-- Legend END -->
                        
                    </div>
                    <!-- Header END -->
                    
                    <!-- Likes -->
                    <div class="list-item double-margin">
                        <div class="panel">
                            <div class="count">
                                <?php echo $speed->likes_count; ?>
                            </div>
                            
                            <div class="<?php 
                                                if($speed->likes_count == 1) 
                                                    echo "singular-caption";
                                                else
                                                    echo "plural-caption"; 
                                            ?> caption">
                            </div>
                        </div>
                        
                        <div class="legend">
                            <div class="label">
                                Liked
                            </div>
                            
                            <div class="sublabel">
                                this speed
                            </div>
                        </div>
                        
                        <div class="action">
                            <?php
                                if($authorized):
                                    if($speed->is_logged_user_post_author):
                            ?>
                                        <div class="button selected">
                                            <div class="wrapper selected-like-bg">
                                                Like
                                            </div>
                                        </div>
                            <?php
                                    elseif($speed->is_liked_by_logged_user):
                            ?>
                                        <div class="button selected">
                                            <div class="wrapper selected-like-bg">
                                                Liked
                                            </div>
                                        </div>
                            <?php
                                    else:
                            ?>
                                        <div class="button">
                                            <div class="wrapper like-bg"
                                                  onmouseover="html_tools.module_item.panel_button_over(this)"
                                                  onmouseout="html_tools.module_item.panel_button_out(this)"
                                                  onclick="ajax.process_form('view-speed',
                                                                                      'speed',
                                                                                      'add_like',
                                                                                      'ajax/<?php echo $speed->id; ?>',
                                                                                      this,
                                                                                      'modal')">
                                                Like
                                            </div>
                                        </div>
                            <?php
                                    endif;
                                else:
                            ?>
                                    <div class="button">
                                        <div class="wrapper like-bg"
                                              onmouseover="html_tools.module_item.panel_button_over(this)"
                                              onmouseout="html_tools.module_item.panel_button_out(this)"
                                              onclick="form_tools.default_errors.show(new Array('Please login to add rating.'))">
                                            Like
                                        </div>
                                    </div>
                            <?php
                                endif;
                            ?>
                        </div>
                    </div>
                    <!-- Likes END -->
                    
                    <!-- Followers -->
                    <div class="list-item">
                        <div class="panel">
                            <div class="count">
                                <?php echo $speed->author_followers_count; ?>
                            </div>
                            
                            <div class="<?php 
                                                if($speed->author_followers_count == 1) 
                                                    echo "singular-caption";
                                                else
                                                    echo "plural-caption"; 
                                            ?> caption">
                            </div>
                        </div>
                        
                        <div class="legend">
                            <div class="label">
                                Followed
                            </div>
                            
                            <div class="sublabel">
                                this speed author
                            </div>
                        </div>
                        
                        <div class="action">
                            <?php
                                if($authorized):
                                    if($speed->is_logged_user_post_author):
                            ?>
                                        <div class="button selected">
                                            <div class="wrapper selected-follow-bg">
                                                Follow
                                            </div>
                                        </div>
                            <?php
                                    else:
                            ?>
                                        <div class="button">
                                            <div class="wrapper follow-bg"
                                                  onmouseover="html_tools.module_item.panel_button_over(this)"
                                                  onmouseout="html_tools.module_item.panel_button_out(this)"
                                                  onclick="ajax.process_form('view-speed',
                                                                                      'speed',
                                                                                      'change_follow',
                                                                                      'ajax/<?php echo $speed->user_id; ?>',
                                                                                      this,
                                                                                      'modal')">
                                                <?php echo ($speed->is_author_followed_by_logged_user) ? "Unfollow" : "Follow"; ?>
                                            </div>
                                        </div>
                            <?php
                                    endif;
                                else:
                            ?>
                                    <div class="button">
                                        <div class="wrapper follow-bg"
                                              onmouseover="html_tools.module_item.panel_button_over(this)"
                                              onmouseout="html_tools.module_item.panel_button_out(this)"
                                              onclick="form_tools.default_errors.show(new Array('Please login to follow other users.'))">
                                            Follow
                                        </div>
                                    </div>
                            <?php
                                endif;
                            ?>
                        </div>
                    </div>
                    <!-- Followers END -->
                    
                    <!-- Favorites -->
                    <div class="list-item">
                        <div class="panel">
                            <div class="count">
                                <?php echo $speed->favorites_count; ?>
                            </div>
                            
                            <div class="<?php 
                                                if($speed->favorites_count == 1) 
                                                    echo "singular-caption";
                                                else
                                                    echo "plural-caption"; 
                                            ?> caption">
                            </div>
                        </div>
                        
                        <div class="legend">
                            <div class="label">
                                Favorite
                            </div>
                            
                            <div class="sublabel">
                                post on fordrive
                            </div>
                        </div>
                        
                        <div class="action">
                            <?php
                                if($authorized):
                                    if($speed->is_logged_user_post_author):
                            ?>
                                        <div class="button selected">
                                            <div class="wrapper selected-favorite-bg">
                                                Favorite
                                            </div>
                                        </div>
                            <?php
                                    else:
                            ?>
                                        <div class="button">
                                            <div class="wrapper favorite-bg"
                                                  onmouseover="html_tools.module_item.panel_button_over(this)"
                                                  onmouseout="html_tools.module_item.panel_button_out(this)"
                                                  onclick="ajax.process_form('view-speed',
                                                                                      'speed',
                                                                                      'change_favorite',
                                                                                      'ajax/<?php echo $speed->id; ?>',
                                                                                      this,
                                                                                      'modal')">
                                                <?php echo ($speed->is_favorite_of_logged_user) ? "Unfavorite" : "Favorite"; ?>
                                            </div>
                                        </div>
                            <?php
                                    endif;
                                else:
                            ?>
                                    <div class="button">
                                        <div class="wrapper favorite-bg"
                                              onmouseover="html_tools.module_item.panel_button_over(this)"
                                              onmouseout="html_tools.module_item.panel_button_out(this)"
                                              onclick="form_tools.default_errors.show(new Array('Please login to add to favorites.'))">
                                            Favorite
                                        </div>
                                    </div>
                            <?php
                                endif;
                            ?>
                        </div>
                    </div>
                    <!-- Favorites END -->
                        
                </div>
                <!-- Information panel END -->
                
            </div>
            <!-- Overall END -->
            
            <!-- Article -->
            <div class="article">
                <div class="heading">
                    <h2>
                        <?php echo $speed->short_description; ?>
                    </h2>
                </div>
                
                <?php
                    $gallery_photo_number = parse_article_tags($speed,
                                                                             $speed->article,
                                                                             $gallery_photo_number);
                ?>
            </div>
            <!-- Article END -->
            
            <!-- Photos -->
            <div class="photos">
                <?php
                    $items_count = 1;
                    foreach($speed->photos as $photo):
                        if($items_count <= 9):
                ?>
                            <div class="large gallery-photo"
                                  data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
                                  data-photo-id="<?php echo $photo->id; ?>"
                                  data-master-photo-name="<?php echo $photo->master_name; ?>"
                                  data-upload-directory="images/<?php echo $photo->directory; ?>"
                                  data-packed-resolutions="<?php 
                                        pack_resolutions_for_gallery($photo->lazy_clones); 
                                                                    ?>">
                                <div class="item-wrapper">
                                    
                                    <img src="<?php 
                                                    load_photo( $photo->master_name, 
                                                                    270, 
                                                                    180); 
                                                 ?>" width="270" height="180"
                                          onclick="gallery.load(this, 'gallery-photos', 'gallery-photo')">
                                    
                                    <div class="wallpapers">
                                        
                                        <div class="count">
                                            <?php echo $photo->lazy_clones_count; ?>
                                        </div>

                                        <?php
                                            if($photo->lazy_clones_count == 1)
                                                echo "<div class='hrphotos-icon-singular'></div>";
                                            else
                                                echo "<div class='hrphotos-icon-plural'></div>";
                                        ?>
                                        
                                        <?php
                                            foreach($photo->lazy_clones as $lazy_clone_array):
                                                $lazy_clone = (object) $lazy_clone_array;
                                                
                                                if($lazy_clone->exists):
                                        ?>
                                                    <div class="wallpaper">
                                                        <a href="<?php
                                                                        $url_segments  = "services/viewphoto/speed";
                                                                        $url_segments .= "/" . $photo->id;
                                                                        $url_segments .= "/" . $lazy_clone->width;
                                                                        $url_segments .= "/" . $lazy_clone->height;
                                                                        public_link($url_segments);
                                                                    ?>" target="_blank"
                                                            onmouseover="html_tools.module_item.wallpaper_over(this)"
                                                            onmouseout="html_tools.module_item.wallpaper_out(this)"
                                                            rel="nofollow">
                                                            <div class="wrapper">
                                                                <div class="spacer">
                                                                </div>
                                                                
                                                                <div class="size width">
                                                                    <?php echo $lazy_clone->width; ?>
                                                                </div>
                                                                
                                                                <div class="size height">
                                                                    <?php echo $lazy_clone->height; ?>
                                                                </div>
                                                            </div>
                                                        </a>
                                                    </div>
                                        <?php
                                                endif;
                                            endforeach;
                                        ?>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                <?php
                        else:
                ?>
                            <div class="small gallery-photo"
                                  data-gallery-photo-number="<?php echo ++$gallery_photo_number; ?>"
                                  data-photo-id="<?php echo $photo->id; ?>"
                                  data-master-photo-name="<?php echo $photo->master_name; ?>"
                                  data-upload-directory="images/<?php echo $photo->directory; ?>"
                                  data-packed-resolutions="<?php 
                                        pack_resolutions_for_gallery($photo->lazy_clones); 
                                                                    ?>">
                                <div class="item-wrapper">
                                    
                                    <img src="<?php 
                                                    load_photo( $photo->master_name, 
                                                                    130, 
                                                                    90); 
                                                 ?>" width="130" height="90"
                                          onclick="gallery.load(this, 'gallery-photos', 'gallery-photo')">
                                    
                                    <div class="wallpapers-count">
                                        
                                        <div class="count">
                                            <?php echo $photo->lazy_clones_count; ?>
                                        </div>

                                        <?php
                                            if($photo->lazy_clones_count == 1)
                                                echo "<div class='hrphotos-icon-singular'></div>";
                                            else
                                                echo "<div class='hrphotos-icon-plural'></div>";
                                        ?>
                                        
                                    </div>
                                    
                                </div>
                            </div>
                <?php
                        endif;
                        $items_count++;
                    endforeach;
                ?>
            </div>
            <!-- Photos END -->
            
            <?php
                if(!empty($speed->author) or !empty($speed->source)):
            ?>
                    <!-- Spacer -->
                    <div class="source-spacer">
                    </div>
                    <!-- Spacer END -->
        
                    <!-- Source -->
                    <div class="source">
                        <?php 
                            if(!empty($speed->author)):
                        ?>
                                <div class="item">
                                    Author: <span class="highlight"><?php echo $speed->author; ?></span>
                                </div>
                        <?php
                            endif;

                            if(!empty($speed->source)):
                        ?>
                                <div class="item">
                                    Source:
                                    
                                    <a href="<?php echo $speed->source; ?>" target="_blank" rel="nofollow">
                                        <span class="highlight"><?php echo $speed->source; ?></span>
                                    </a>
                                </div>
                        <?php
                            endif;
                        ?> 
                    </div>
                    <!-- Source END -->
                    
                    <!-- Spacer -->
                    <div class="source-spacer">
                    </div>
                    <!-- Spacer END -->
            <?php
                endif;
            ?>
            
            <!-- Comments -->
            <div class="comments comment-id-heading">
                
                <!-- Heading -->
                <div class="heading">
                    
                    <!-- Count -->
                    <div class="count ajax-comments-count">
                        <?php echo $speed->comments_total_count; ?>
                    </div>
                    <!-- Count END -->
                    
                    <!-- Label -->
                    <div class="label">
                        <div class="big">
                            Comments
                        </div>
                        
                        <div class="small">
                            Create a discussion with other fordrivers.
                        </div>
                    </div>
                    <!-- Label END -->
                    
                    <!-- Add button -->
                    <?php
                        if($authorized):
                    ?>
                            <div class="add-button">
                                <div class="wrapper"
                                        onclick="form_tools.newcomment.show('New comment',
                                                                                        'Share your opinion about this speed',
                                                                                        0)">
                                    <div class="name">
                                        <span class="add-char">+</span>&nbsp;Add comment
                                    </div>
                                </div>
                            </div>
                    <?php
                        else:
                    ?>
                            <div class="add-button">
                                <div class="wrapper"
                                      onclick="form_tools.default_errors.show(new Array('Please login to write comment.'))">
                                    <div class="name">
                                        <span class="add-char">+</span>&nbsp;Add comment
                                    </div>
                                </div>
                            </div>
                    <?php
                        endif;
                    ?>
                    <!-- Add button END -->
                    
                </div>
                <!-- Heading END -->
                
                <!-- Controls -->
                <div class="controls">
                    
                    <!-- Pagination -->
                    <div class="pagination ajax-comments-pagination">
                            <?php include("view_speed_comments_pagination.php"); ?>
                    </div>
                    <!-- Pagination END -->
                    
                    <!-- Refresh -->
                    <div class="refresh"
                          onclick="form_tools.comments.load_new(<?php echo $speed->id; ?>,
                                                                             'speed')">
                        
                        <div class="message">
                            Refresh comments
                        </div>
                        
                        <div class="icon">
                        </div>
                        
                        <div class="active-icon"
                              id="refresh-comments-active-icon">
                            <div class="wrapper">
                            </div>
                        </div>
                        
                    </div>
                    <!-- Refresh END -->
                    
                </div>
                <!-- Controls END -->
                
                <!-- Items -->
                <div class="items ajax-comments-items">
                    <?php include("view_speed_comments_items.php"); ?>
                </div>
                <!-- Items END -->
                
                <!-- Bottom pagination -->
                <div class="controls">
                    <?php
                        if($speed->comments):
                    ?>
                            <div class="pagination ajax-comments-pagination">
                                <?php include("view_speed_comments_pagination.php"); ?>
                            </div>
                    <?php
                        endif;
                    ?>
                </div>
                <!-- Bottom pagination END -->
                
            </div>
            <!-- Comments END -->
            
        </div>
        <!-- Speed data END -->
        
        <!-- Speed categories -->
        <div class="item-categories">
            
            <!-- Subcategories list -->
            <?php 
                if($selected_category
                        and
                    $selected_category->subcategories):
                        foreach($selected_category->subcategories as $subcategory):
                            if($selected_subcategory
                                    and
                                $selected_subcategory->id == $subcategory->id):
            ?>
                                <div class="category selected">
                                    <span class="text">
                                        <?php echo $subcategory->name; ?>
                                    </span>
                                </div>
            <?php
                            else:
            ?>
                                <a href="<?php
                                                module_link("speed",
                                                                "list",
                                                                $selected_category,
                                                                $subcategory,
                                                                1);
                                            ?>">
                                    <div class="category active highlight">
                                        <span class="text">
                                            <?php echo $subcategory->name; ?>
                                        </span>
                                    </div>
                                </a>
            <?php
                            endif;
                        endforeach;
            ?>
                                <!-- Separator -->
                                <div class="separator">
                                </div>
                                <!-- Separator END -->
            <?php
                endif;
            ?>
            <!-- Subcategories list END -->
            
            <!-- Sandbox item -->
            <?php
                if(!$selected_category and !$selected_subcategory):
            ?>
                    <div class="category selected">
                        <span class="text">
                            Sandbox
                        </span>
                    </div>
            <?php
                else:
            ?>
                    <a href="<?php
                                    module_link("speed",
                                                    "list",
                                                    false,
                                                    false,
                                                    1);
                                ?>">
                        <div class="category active">
                            <span class="text">
                                Sandbox
                            </span>
                        </div>
                    </a>
            <?php
                endif;
            ?>
            <!-- Sandbox item END -->
            
            <!-- Categories list -->
            <?php
                $highlight = 0;
                foreach($categories as $category):
                    if($selected_category
                            and
                        $selected_category->id == $category->id):
            ?>
                        <div class="category selected">
                            <span class="text">
                                <?php echo $category->name; ?>
                            </span>
                        </div>
            <?php
                    else:
            ?>
                        <a href="<?php
                                        module_link("speed",
                                                        "list",
                                                        $category,
                                                        false,
                                                        1);
                                    ?>">
                            <div class="category active<?php if($highlight % 2 == 0) echo " highlight"; ?>">
                                <span class="text">
                                    <?php echo $category->name; ?>
                                </span>
                            </div>
                        </a>
            <?php
                    endif;
                    $highlight++;
                endforeach;
            ?>
            <!-- Categories list END -->
            
        </div>
        <!-- Speed categories END -->
    </div>
    <!-- Speed data and categories END -->
</div>
<!-- Item END -->

<?php
    if($authorized):
?>
        <!-- Add comment -->
        <div id="newcomment-form">
            <div id="newcomment-form-spinner"
                  class="newcomment-form">
                <div class="spinner-wrapper">
                    
                    <form name="newcomment-form">
                        
                        <!-- Heading -->
                        <div class="heading">
                            
                            <div class="info">
                                <div class="label">
                                </div>
                                
                                <div class="sublabel">
                                </div>
                            </div>
                            
                            <div class="close"
                                  onclick="form_tools.newcomment.hide()">
                            </div>
                            
                        </div>
                        <!-- Heading END -->
                        
                        <!-- Comment -->
                        <div class="item">
                            
                            <div class="legend">
                                <div class="name">
                                    Comment text<span class="required">*</span>
                                </div>
                                
                                <div class="description">
                                    Please follow the rules of the site.<br>
                                    Insulting others,advertising or spamming<br>
                                    will cause deletion of comment<br>
                                    and blocking your account.
                                </div>
                            </div>
                            
                            <div class="element">
                                <textarea name="comment[comment]"
                                             class="textarea"
                                             id="newcomment-comment"></textarea>
                            </div>
                            
                        </div>
                        <!-- Comment END -->
                        
                        <!-- Speed id -->
                        <input type="hidden" name="comment[speed_id]" value="<?php echo $speed->id; ?>">
                        <!-- Speed id END -->
                        
                        <!-- Answer id -->
                        <input type="hidden" name="comment[answer_id]"
                                                    id="newcomment-answer-id" value="0">
                        <!-- Answer id END -->
                        
                        <!-- Current page -->
                        <input type="hidden" name="current_page[number]"
                                                   id="newcomment-current-page" value="1">
                        <!-- Current page END -->
                        
                        <!-- Token -->
                        <input type="hidden" name="token[name]"  value="newcomment-form">
                        <input type="hidden" name="token[value]" value="<?php token('newcomment-form'); ?>">
                        <!-- Token END -->
                        
                        <!-- Submit and Loading -->
                        <div class="item">
                            
                            <div class="save">
                                <button type="button" id="third-modal-form-submit" class="submit"
                                          onclick="ajax.process_form( 'newcomment-form', 
                                                                                'speed', 
                                                                                'add_comment', 
                                                                                'ajax',
                                                                                false,
                                                                                'third_modal_form',
                                                                                'compact')">
                                    Add
                                </button>
                            </div>
                            
                            <div class="loading" id="third-modal-form-loading">
                            </div>
                            
                        </div>
                        <!-- Submit and Loading END -->
                        
                    </form>
                    
                </div>
            </div>
        </div>
        <!-- Add comment END -->
<?php
    endif;
?>