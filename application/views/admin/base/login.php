<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        
        <!-- Connecting stylesheets -->
        <?php
            css('base/reset');
            css('login/login');
        ?>
        
        <!-- Connecting javascripts -->
        <?php
            admin_php_to_js();
            
            js('jquery');
            js('library');
            js('ajax');
            js('overlay');
            js('modal_loading');
            js('initialize');
        ?>
    </head>
    
    <body>
        <!-- Page container -->
        <div class="body-wrapper">
            <!-- Login form -->
            <div class="login-form">
                
                <form name="login-form">
                    <!-- Header -->
                    <div class="header">
                    </div>
                    <!-- Header END -->
                    
                    <!-- Login -->
                    <div class="item">
                        <div class="legend">
                            Login:
                        </div>
                        
                        <div class="element">
                            <input type="text" name="login[username]" value="" maxlength="255" class="input">
                        </div>
                    </div>
                    <!-- Login END -->
                    
                    <!-- Password -->
                    <div class="item">
                        <div class="legend">
                            Password:
                        </div>
                        
                        <div class="element">
                            <input type="password" name="login[password]" value="" maxlength="255" class="input">
                        </div>
                    </div>
                    <!-- Password END -->
                    
                    <!-- Token -->
                    <input type="hidden" name="token[name]"  value="login-form">
                    <input type="hidden" name="token[value]" value="<?php token('login-form'); ?>">
                    <!-- Token END -->
                    
                    <!-- Submit and Loading -->
                    <div class="item">
                        <div class="submit-wrapper">
                            <button type="button" id="form-submit" class="submit"
                                      onclick="ajax.process_form('login-form', 'authorization', 'login', 'ajax')">
                                DRIVE
                            </button>
                        </div>
                        
                        <div class="loading-wrapper">
                            <div id="form-loading">
                            </div>
                        </div>
                    </div>
                    <!-- Submit and Loading END -->
                </form>
                
            </div>
            <!-- Login form END -->
            
            <!-- Footer -->
            <div class="footer">
                @2012, Powered by nTechnologies
            </div>
            <!-- Footer END -->
        </div>
        <!-- Page container END -->
    </body>
</html>