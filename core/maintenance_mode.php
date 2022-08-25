<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>

        <style type="text/css">
            /* Reset styles */
            body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,
            form,fieldset,input,textarea,p,blockquote,th,td {
                padding: 0; margin: 0;
            }

            table {
                border-collapse: collapse; border-spacing: 0;
            }

            fieldset,img {
                border: 0;
            }

            address,caption,cite,code,dfn,em,strong,th,var {
                font-weight: normal; font-style: normal;
            }

            ol,ul {
                list-style: none;
            }

            caption,th {
                text-align: left;
            }

            h1,h2,h3,h4,h5,h6 {
                font-weight: normal; font-size: 100%;
            }

            q:before,q:after {
                content:'';
            }

            abbr,acronym {
                border: 0;
            }

            html,body {
                font-family: Arial;
            }

            /* Page styles */
            body {
                background: rgb(222,222,222);
            }

            .message {
                position: absolute; left: 0px; top: 50%; width: 100%; height: 240px;
                margin-top: -120px; background: rgb(81,81,181);
            }

            .message .wrapper {
                position: absolute; left: 50%; top: 0px; width: 960px;
                height: 240px; margin-left: -480px;
            }

            .footer {
                position: absolute; bottom: 0px; left: 50%; margin-bottom: 5px;
                font-size: 12px; width: 960px; margin-left: -480px;
            }

            .footer .text {
                text-align: center; color: rgb(90,90,90);
            }
        </style>
    </head>

    <body>
        <div class="message">
            <div class="wrapper">
                <img src="data:image/jpg;base64,<?php echo $offline_image; ?>" width="960" height="240">
            </div>
        </div>

        <div class="footer">
            <div class="text">
                <?php echo date('Y'); ?>, www.fordrive.net
            </div>
        </div>
    </body>
</html>