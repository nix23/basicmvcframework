<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
    </head>
    
    <body>
        Fordrive.net<br>
        Best cars over the world<br>
        Account activation<br>
        Please activate your account by clicking the link below:<br>
        <a href="<?php public_link("account/activate/" . $hash); ?>">
            <?php public_link("account/activate/" . $hash); ?>
        </a>
        <br>If you didn't signed up to fordrive,please ignore this message.
        <br>Best regards, fordrive administration.<br>
        <br><a href="http://www.fordrive.net">www.fordrive.net</a>
    </body>
</html>