<?php   
    // Disable before launching site
    //error_reporting(E_ALL); 
        
    // Checking php-version
    if(version_compare(phpversion(), '5.1.0', '<') == TRUE) 
    {
        exit('PHP 5.1+ version required.');
    }
    
    // mbstring used for validators and strings
    if(!extension_loaded("mbstring"))
    {
        exit('Please load mbstring extension.');
    }
    
    // Language settings
    date_default_timezone_set(date_default_timezone_get());
    setlocale(LC_ALL, 'en_US.utf-8');
    
    // Defining absolute path to folder with our application
    define('ROOT', dirname(dirname(__FILE__)));
    
    // Defining separator(\ for windows, / for UNIX)
    define('DS', DIRECTORY_SEPARATOR);
    
    // Defining main pathes of our application
    define('CORE',           ROOT . DS . 'core'        . DS);
    define('CONTROLLERS',    ROOT . DS . 'application' . DS . 'controllers' . DS);
    define('MAPPERS',        ROOT . DS . 'application' . DS . 'mappers'     . DS);
    define('MODELS',         ROOT . DS . 'application' . DS . 'models'      . DS);
    define('VIEWS',          ROOT . DS . 'application' . DS . 'views'       . DS);
    define('RESOURCES',      ROOT . DS . 'resources'   . DS);
    define('UPLOADS_IMAGES', ROOT . DS . 'public'      . DS . 'uploads'     . DS . 'images' . DS);
    define('UPLOADS_AJAX',   ROOT . DS . 'public'      . DS . 'uploads'     . DS . 'ajax'   . DS);
    define('UPLOADS_DATA',   ROOT . DS . 'public'      . DS . 'uploads'     . DS . 'data'   . DS);
    define('UPLOADS_TMP',    ROOT . DS . 'public'      . DS . 'uploads'     . DS . 'tmp'    . DS);
    define('UPLOADS_CACHE',  ROOT . DS . 'public'      . DS . 'uploads'     . DS . 'cache'  . DS);
    define('JS',             ROOT . DS . 'public'      . DS . 'js'          . DS);
    define('CSS',            ROOT . DS . 'public'      . DS . 'css'         . DS);
    
    // Defining contants
    define('MAX_FILE_SIZE', '8MB');
    define('MIN_SECONDS_BETWEEN_COMMENTS', 2);

    // Defining error constants
    define('ERROR_ITEM_NOT_FOUND_AJAX',        "Item not found. Please try refresh the page.");
    define('ERROR_ITEM_IS_DISABLED_AJAX',      "Item is temporary disabled by user. Please try again later.");
    define('ERROR_ITEM_IS_MODERATED_AJAX',     "Item is passing moderation. Please try again later.");
    define('ERROR_ITEM_IS_ALREADY_LIKED_AJAX', "You already liked this item.");
    define('ERROR_USER_WAS_DELETED',           "User account was deleted.");

    // Connecting application core
    require_once(CORE . 'config.php');
    require_once(CORE . 'database.php');
    require_once(CORE . 'model.php');
    require_once(CORE . 'model_errors.php');
    require_once(CORE . 'mapper.php');
    require_once(CORE . 'validator.php');
    require_once(CORE . 'controller.php');
    require_once(CORE . 'admin_controller.php');
    require_once(CORE . 'public_controller.php');
    require_once(CORE . 'view.php');
    require_once(CORE . 'admin_session.php');
    require_once(CORE . 'user_session.php');
    require_once(CORE . 'ajax.php');
    require_once(CORE . 'registry.php');
    require_once(CORE . 'router.php');
    require_once(CORE . 'input.php');
    require_once(CORE . 'loader.php');
    require_once(CORE . 'url.php');
    require_once(CORE . 'form.php');
    require_once(CORE . 'helpers.php');
    require_once(CORE . 'helpers_article.php');
    require_once(CORE . 'json.php');
    require_once(CORE . 'uploader.php');
    require_once(CORE . 'fordrive_uploader.php');
    require_once(CORE . 'image_resizer.php');
    require_once(CORE . 'pagination.php');
    require_once(CORE . 'tags_scanner.php');
    require_once(CORE . 'tags_parser.php');
    require_once(CORE . 'datetime_converter.php');
    require_once(CORE . 'profiler.php');
    require_once(CORE . 'mailer.php');
    require_once(CORE . "resources_compiler" . DS . "resources_compiler.php");
    require_once(CORE . "resources_compiler" . DS . "css_compiler.php");
    require_once(CORE . "resources_compiler" . DS . "js_compiler.php");

    // Connecting side modules
    require_once(CORE . 'modules' . DS . 'phpmailer' . DS . 'class.phpmailer.php');
    require_once(CORE . 'modules' . DS . 'facebook' . DS . 'facebook.php');
    
    // Autoloader loads models and controllers
    function __autoload($class_name) 
    {
        Loader::load_class($class_name);
    }
    
    // Connecting profiler
    Profiler::init();

    // Creating core objects
    Registry::set('config',    new Config);
    Registry::set('database',  new MySQL_Database);
    Registry::set('input',     new Input);
    Registry::set('facebook',  new Facebook(array(
        'appId' => 'appid',
        'secret' => 'appsecret'
    )));
    
    // Here current application query is processing
    Registry::set('router',   new Router);
    
    // Closing profiler file handle
    Profiler::destroy();
    
    // Closing database connection
    Registry::get('database')->close_connection();
?>