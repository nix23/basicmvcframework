<?php
function parse_article_text_tags($article)
{
    // Parsing <p> tags
    $php_eol_windows = "\r\n";
    $php_eol_linux = "\n";
    $article = "<p class='first'>" . preg_replace("~(" . $php_eol_windows . "|" . $php_eol_linux . "){2,}~u",
            "</p><p>",
            $article)
        . "</p>";

    // Parsing <b> tags
    $article = preg_replace("~\[b\]~u", "<span class='bold'>", $article);
    $article = preg_replace("~\[/b\]~u", "</span>", $article);

    // Parsing <a> tags
    $article = preg_replace("~\[link=([^\]]+)\]~u", "<a href='$1' class='link'>", $article);
    $article = preg_replace("~\[/link\]~u", "</a>", $article);

    return $article;
}

function remove_p_tags_inside_photoset_tag($article)
{
    // Processing all photosets
    while (preg_match("~\[photoset\](.*)\[/photoset\]~uUs", $article)) {
        // Marking photoset,which we will process
        $article = preg_replace("~\[photoset\](.*)\[/photoset\]~uUs",
            "[photoset-to-clear]$1[/photoset-to-clear]",
            $article,
            1);

        // Clearing <p> tags
        while (preg_match("~\[photoset-to-clear\](.*)<p>(.*)\[/photoset-to-clear\]~uUs", $article)) {
            $article = preg_replace("~\[photoset-to-clear\](.*)<p>(.*)\[/photoset-to-clear\]~uUs",
                "[photoset-to-clear]$1$2[/photoset-to-clear]",
                $article);
        }

        // Clearing </p> tags
        while (preg_match("~\[photoset-to-clear\](.*)</p>(.*)\[/photoset-to-clear\]~uUs", $article)) {
            $article = preg_replace("~\[photoset-to-clear\](.*)</p>(.*)\[/photoset-to-clear\]~uUs",
                "[photoset-to-clear]$1$2[/photoset-to-clear]",
                $article);
        }

        // Marking that photoset is cleared now
        $article = preg_replace("~\[photoset-to-clear\]~uU", "[photoset-cleared]", $article);
        $article = preg_replace("~\[/photoset-to-clear\]~uU", "[/photoset-cleared]", $article);
    }

    // Restoring original [photoset] tags
    $article = preg_replace("~\[photoset-cleared\]~uU", "[photoset]", $article);
    $article = preg_replace("~\[/photoset-cleared\]~uU", "[/photoset]", $article);

    return $article;
}

function parse_article_img_tag($photo,
                               $module,
                               $gallery_photo_number)
{
    // Generating image path
    $master_name_parts = explode("-", $photo->master_name);
    $directory = $master_name_parts[0];

    $image_path = Url::get_base_url() . "uploads/images/$directory/";
    $image_path .= $photo->master_name . "-270-180.jpg";
    $resolutions = pack_resolutions_for_gallery($photo->lazy_clones, false);

    if ($photo->lazy_clones_count == 1)
        $hr_photos_label = "<div class='hrphotos-icon-singular'></div>";
    else
        $hr_photos_label = "<div class='hrphotos-icon-plural'></div>";

    // Generating [img=number] replace HTML
    $html = "<div class='article-photo gallery-photo'                                                  ";
    $html .= "     data-gallery-photo-number='" . ++$gallery_photo_number . "'             ";
    $html .= "     data-photo-id='$photo->id'                                              ";
    $html .= "      data-master-photo-name='$photo->master_name'                                        ";
    $html .= "      data-upload-directory='images/$photo->directory'                                ";
    $html .= "      data-packed-resolutions='$resolutions'>                                         ";
    $html .= "  <div class='item-wrapper'>                                                                  ";
    $html .= "                                                                                                      ";
    $html .= "      <img src='$image_path' width='270' height='180'                                 ";
    $html .= "            onclick=\"gallery.load(this, 'gallery-photos', 'gallery-photo')\">    ";
    $html .= "                                                                                                      ";
    $html .= "      <div class='hr-photos'>                                                                 ";
    $html .= "                                                                                                      ";
    $html .= "          <div class='count'>                                                                 ";
    $html .= "           $photo->lazy_clones_count                                         ";
    $html .= "        </div>                                                               ";
    $html .= "                                                                             ";
    $html .= "        $hr_photos_label                                                     ";

    foreach ($photo->lazy_clones as $lazy_clone_array) {
        $lazy_clone = (object)$lazy_clone_array;

        if ($lazy_clone->exists) {
            $url_segments = "services/viewphoto/$module";
            $url_segments .= "/" . $photo->id;
            $url_segments .= "/" . $lazy_clone->width;
            $url_segments .= "/" . $lazy_clone->height;

            $html .= "  <div class='hr-photo'>                                                              ";
            $html .= "      <a href='" . public_link($url_segments, false) . "' target='_blank' ";
            $html .= "          onmouseover='html_tools.module_item.wallpaper_over(this)'       ";
            $html .= "          onmouseout='html_tools.module_item.wallpaper_out(this)'>            ";
            $html .= "          <div class='wrapper'>                                                       ";
            $html .= "              <div class='spacer'>                                                        ";
            $html .= "              </div>                                                                      ";
            $html .= "                                                                                              ";
            $html .= "              <div class='size width'>                                                ";
            $html .= "                  $lazy_clone->width                                                  ";
            $html .= "              </div>                                                                      ";
            $html .= "                                                                                              ";
            $html .= "              <div class='size height'>                                               ";
            $html .= "                  $lazy_clone->height                                                     ";
            $html .= "              </div>                                                                      ";
            $html .= "          </div>                                                                          ";
            $html .= "      </a>                                                                                    ";
            $html .= "  </div>                                                                                  ";
        }
    }

    $html .= "                                                                                         ";
    $html .= "      </div>                                                                         ";
    $html .= "                                                                                         ";
    $html .= "  </div>                                                                                        ";
    $html .= "</div>                                                                                              ";

    return array($html, $gallery_photo_number);
}

// Videos module photos requires special processing,
// because it hasn't got lazy_clones.
function parse_videos_module_article_img_tag($photo,
                                             $module,
                                             $gallery_photo_number)
{
    // Generating image path
    $master_name_parts = explode("-", $photo->master_name);
    $directory = $master_name_parts[0];

    $image_path = Url::get_base_url() . "uploads/images/$directory/";
    $image_path .= $photo->master_name . "-270-180.jpg";

    // Generating [img=number] replace HTML
    $html = "<div class='article-photo gallery-photo'                                                  ";
    $html .= "     data-gallery-photo-number='" . ++$gallery_photo_number . "'             ";
    $html .= "     data-photo-id='$photo->id'                                              ";
    $html .= "      data-master-photo-name='$photo->master_name'                                        ";
    $html .= "      data-upload-directory='images/$photo->directory'>                               ";
    $html .= "  <div class='item-wrapper'>                                                                  ";
    $html .= "                                                                                                      ";
    $html .= "      <img src='$image_path' width='270' height='180'                                 ";
    $html .= "            onclick=\"gallery.load(this, 'gallery-photos', 'gallery-photo')\">    ";
    $html .= "                                                                                         ";
    $html .= "  </div>                                                                                        ";
    $html .= "</div>                                                                                              ";

    return array($html, $gallery_photo_number);
}

function parse_article_photoset_tag($article)
{
    // First,removing </p><p> tags,if our photoset was wrapped
    // inside them because of 2+ EOL-s. Also removing last paragraph
    // </p> tag or next paragraph <p> tag.

    // Removing </p><p> tags before [photoset] tag
    while (preg_match("~<p>[\s]*\[photoset\]~u", $article)) {
        $article = preg_replace("~<p>[\s]*\[photoset\]~u",
            "[photoset]",
            $article);
    }
    while (preg_match("~</p>[\s]*\[photoset\]~u", $article)) {
        $article = preg_replace("~</p>[\s]*\[photoset\]~u",
            "[photoset]",
            $article);
    }

    // Removing </p><p> tags after [photoset] tag
    while (preg_match("~\[/photoset\][\s]*</p>~u", $article)) {
        $article = preg_replace("~\[/photoset\][\s]*</p>~u",
            "[/photoset]",
            $article);
    }
    while (preg_match("~\[/photoset\][\s]*<p>~u", $article)) {
        $article = preg_replace("~\[/photoset\][\s]*<p>~u",
            "[/photoset]",
            $article);
    }

    // Close paragraph before [photoset] tag
    $article = preg_replace("~\[photoset\]~u",
        "</p>[photoset]",
        $article);
    // Open paragraph after [/photoset] tag
    $article = preg_replace("~\[/photoset\]~u",
        "[/photoset]<p>",
        $article);

    // Removing first paragraph,if [photoset] tag is inserted
    // at the beggining of text
    if (preg_match("~<p class='first'>[\s]*</p>[\s]*\[photoset\]~u", $article)) {
        $article = preg_replace("~<p class='first'>[\s]*</p>[\s]*\[photoset\]~u",
            "[photoset]",
            $article);
    }

    // Now replacing [photoset] tags with container
    $article = preg_replace("~\[photoset\]~u",
        "<div class='article-photos'>",
        $article);
    $article = preg_replace("~\[/photoset\]~u",
        "</div><div class='article-photos-bottom-separator'></div>",
        $article);

    return $article;
}

function parse_article_caption_tag($article)
{
    $article = preg_replace("~\[caption\]~u",
        "<div class='caption-top-spacer'></div><div class='caption'><div class='wrapper'>",
        $article);

    $article = preg_replace("~\[/caption\]~u",
        "</div></div>",
        $article);

    return $article;
}

function clear_article_empty_paragraphs($article)
{
    while (preg_match("~<p>[\s]*</p>~u", $article)) {
        $article = preg_replace("~<p>[\s]*</p>~u",
            "",
            $article);
    }

    return $article;
}

// $object - module item object
// $article - article text
// $gallery_photo_number - photo number at gallery.js
function parse_article_tags($object,
                            $article,
                            $gallery_photo_number)
{
    $article = parse_article_text_tags($article);

    // *** Parsing [photoset] tags
    $photos_for_gallery = array();
    preg_match_all("~\[img=([0-9]{0,})\]~u", $article, $imgMatches);
    $imageNumbersToProcess = $imgMatches[1];

    foreach ($imageNumbersToProcess as $imageNumberToProcess) {
        for ($photo = 0; $photo < count($object->photos); $photo++) {
            $imgIndex = $photo + 1;

            if ($imgIndex == $imageNumberToProcess) {
                if ($object->module != "videos") {
                    list($img_tag_replace_html,
                        $gallery_photo_number) = parse_article_img_tag($object->photos[$photo],
                        $object->module,
                        $gallery_photo_number);
                } else {
                    list($img_tag_replace_html,
                        $gallery_photo_number) = parse_videos_module_article_img_tag($object->photos[$photo],
                        $object->module,
                        $gallery_photo_number);
                }

                $article = preg_replace("~\[img=$imgIndex\]~u",
                    $img_tag_replace_html,
                    $article);
            }
        }
    }

    for ($photo = 0; $photo < count($object->photos); $photo++) {
        $imgIndex = $photo + 1;

        $wasReplaced = false;
        foreach ($imageNumbersToProcess as $imageNumberToProcess) {
            if ($imageNumberToProcess == $imgIndex)
                $wasReplaced = true;
        }

        if (!$wasReplaced)
            $photos_for_gallery[] = $object->photos[$photo];
    }

    // Assign remaining photos(not used in article)
    $object->photos = $photos_for_gallery;

    // Clean main photo from remaining photos
    for ($photo = 0; $photo <= count($object->photos) - 1; $photo++) {
        if ($object->photos[$photo]->id == $object->main_photo->id) {
            unset($object->photos[$photo]);
            break;
        }
    }

    // Parsing [caption] tag
    $article = parse_article_caption_tag($article);

    // Parsing [photoset] tag
    $article = remove_p_tags_inside_photoset_tag($article);
    $article = parse_article_photoset_tag($article);
    $article = clear_article_empty_paragraphs($article);

    echo $article;
    return $gallery_photo_number;
}

?>