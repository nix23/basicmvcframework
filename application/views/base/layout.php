<!DOCTYPE html>
<html>
  <head>
    <meta name="google-site-verification" content="hSDXuYPKL6J7_NkO8MzPF0mRf5wQ0pEpMIO-jQkwlBc" />
    <title><?php echo $page_title; ?></title>
    <meta charset="utf-8">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <link rel="shortcut icon" href="<?php echo get_base_url(); ?>img/favicon.ico">
    
    <!-- Connecting stylesheets -->
    <?php
      css('compiled/compiled_1394662570'); /* RESOURCES: CSS COMPILED */
      css_ie_less_than_version('browser_specific/ie7', '8');
    ?>
    
    <!-- Connecting javascripts -->
    <?php
      php_to_js($current_controller, $current_action);

      js('jquery');
      js('compiled/compiled_1394662570'); /* RESOURCES: JS COMPILED */
    ?>
  </head>
  
  <body xmlns:fb="http://www.facebook.com/2008/fbml">
    <!-- Page container -->
    <div id="body-wrapper">
      
      <!-- Debugger -->
      <div id="debugger">
        
        <div class="header">
          <h2>Debugger</h2>
          
          <div class="close"
              onclick="debug.hide()">
          </div>
        </div>
        
        <div id="debugger-content">
        </div>
        
      </div>
      <!-- Debugger END -->
      
      <!-- Overlay -->
      <div id="overlay">
      </div>
      <!-- Overlay END -->
      
      <!-- Gallery -->
      <div id="gallery">
        <div class="gallery-wrapper">
          
          <div class="visible-image-bg">
          </div>
          
          <div class="image">
          </div>
          
          <div class="previous">
          </div>
          
          <div class="next">
          </div>
          
          <div class="close">
          </div>
          
          <div class="panel">
            
            <div class="current">
              <span class="count"></span>&nbsp;
              <span class="of">of</span>
            </div>
            
            <div class="total">
            </div>
            
            <div class="description">
              
              <div class="heading">
              </div>
              
              <div class="subheading">
              </div>
              
            </div>
            
            <div class="resolutions">
            </div>
            
          </div>
          
        </div>
      </div>
      <!-- Gallery END -->
      
      <!-- Default errors -->
      <div id="default-errors">
        
        <div class="errors">
          <div class="heading">
            
            <div class="legend">
              <span class="count"></span>
              <span class="label"></span>
            </div>
            
          </div>
          
          <div class="message">
            <div class="wrapper">
            </div>
          </div>
        </div>
        
        <div class="close"
            onclick="form_tools.default_errors.hide()">
          Click to close
        </div>
        
      </div>
      <!-- Default errors END -->
      
      <!-- Modal errors -->
      <div id="modal-errors" 
          onclick="form_tools.modal_errors.hide()">
        <div class="wrapper">
          
          <div class="icon">
          </div>
          
          <div class="legend">
            <span class="count">
            </span>
            
            <span class="label">
            </span>
          </div>
          
          <div class="errors">
            <div class="first-list">
            </div>
          </div>
          
          <div class="errors">
            <div class="second-list">
            </div>
          </div>
          
        </div>
      </div>
      <!-- Model errors END -->

      <!-- Confirmation prompt -->
      <div id="confirmation-prompt">

        <div class="top">
          <div class="prompt-icon">
          </div>

          <div class="message">
            <div class="wrapper">
            </div>
          </div>
        </div>

        <div class="bottom">
          <div class="item process"
              onclick="form_tools.confirmation_prompt.process()">
          </div>

          <div class="item cancel"
              onclick="form_tools.confirmation_prompt.cancel()">
          </div>
        </div>

      </div>
      <!-- Confirmation prompt END -->

      <!-- Header -->
      <div id="header">
        <?php echo $header; ?>
      </div>
      <!-- Header END -->
      
      <!-- Content -->
      <div id="content">
        
        <!-- Modal ajax loading -->
        <div id="overlay-loading">
          
          <div class="wrapper">
            <div class="message">
            </div>
            
            <div class="icon">
            </div>
          </div>
          
        </div>
        <!-- Modal ajax loading END -->
        
        <?php echo $main_menu; ?>
        <?php echo $content; ?>
        
      </div>
      <!-- Content END -->

      <!-- Footer -->
      <div id="footer">
        <!-- Left -->
        <div class="left">
          <div class="item">
            <a href="<?php public_link("about"); ?>"
              class="link">
              About
            </a>
          </div>

          <div class="item">
            <a href="<?php public_link("terms"); ?>"
              class="link">
              Terms
            </a>
          </div>

          <div class="item">
            <a href="<?php public_link("privacy-policy"); ?>"
              class="link">
              Privacy Policy
            </a>
          </div>

          <div class="item">
            <a href="<?php public_link("support"); ?>"
              class="link">
              Support
            </a>
          </div>
        </div>
        <!-- Left END -->

        <!-- Right -->
        <div class="right">
          <?php echo date('Y'); ?>, www.fordrive.net
        </div>
        <!-- Right END -->
      </div>
      <!-- Footer END -->
      
      <!-- Facebook service wrapper -->
      <div id="fb-root">
      </div>
      <!-- Facebook service wrapper END -->
      
    </div>
    <!-- Page container END -->
    
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-42060975-1', 'fordrive.net');
  ga('send', 'pageview');

</script>
  </body>
</html>