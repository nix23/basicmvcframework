<!DOCTYPE html>
<html>
    <head>
        <title>Fordrive / 404 Not Found</title>
        <meta charset='utf-8'>

        <?php
            uncached_css('services/404_not_found');
        ?>
    </head>

    <body>
        <div class="message">
            <div class="errors">
                <div class="icon">
                </div>

                <div class="legend">
                    <div class="label">
                        Error 404
                    </div>

                    <div class="sublabel">
                        Requested page was not found.
                    </div>
                </div>
            </div>

            <a href="<?php public_link("main"); ?>">
                <div class="close">
                    Go to main page
                </div>
            </a>
        </div>

        <div class="footer">
            <div class="text">
                <?php echo date('Y'); ?>, www.fordrive.net
            </div>
        </div>
    </body>
</html>