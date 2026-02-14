<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff         
 **************************************************************************************************/

// Core
require_once('core/core.php');
 
// PHP error control
error_reporting(E_ALL);  
ini_set('display_errors', 'off');      
set_error_handler(function ($mError, $mMessage, $mFile, $mLine) 
    {    
        $error = error_reporting();
        if ( $error !== 0 && $error != 4437 ) 
            MELBIS_INC_halt('PHP Runtime Exception', $mFile.' : '.$mLine, $mMessage); 
    });
register_shutdown_function(function ()
    {
        $error = error_get_last();
        if ( isset($error) && ( $error["type"] == E_ERROR || $error["type"] == E_WARNING || $error["type"] == E_PARSE ) )
            MELBIS_INC_halt('PHP Shutdown Exception', $error["file"].' : '.$error["line"], $error["message"]); 
    });

// Set Local Setting 
if ( strlen(SHOP_LOCALE) > 0 ) setlocale(LC_CTYPE, SHOP_LOCALE); 
if ( strlen(SHOP_CHARSET) > 0 ) mb_internal_encoding(SHOP_CHARSET);

// Autoload class
if ( !function_exists('MELBIS_INC_autoload') ) 
{
    function MELBIS_INC_autoload($mClassName)
    {        
        require_once('core/class/class.'.$mClassName.'.php');
    }

    spl_autoload_register('MELBIS_INC_autoload');
}

// Set path of shop on the domain, for sample: "www.site.com/shop", right path is "/shop/"
$gSitePath = '/'; 

// For switch off caching system set the value "false", it's useful in developing process
$gUseCache = true;

// Set default shop template
$gTemplate = TEMPLATE;

// Set current build counter
$gBuild = 1;

// GET vars array
$gGet = $_GET;

// POST vars array
$gPost = $_POST;

// FILES vars array
$gFiles = $_FILES;

// SERVER vars array
$gServer = $_SERVER;


 // Shop available?
 if ( file_exists('shop.lock') ) 
 {
     MELBIS_INC_halt('SHOP LOCKED', __FILE__.':'.__LINE__, 'Shop locked!', 'shop.lock');
 } 
 

/** 
 * Function MELBIS_INC
 * Manual include module
 **/                  
function MELBIS_INC($mInclude) 
{
    if ( file_exists('units/'.$mInclude.'.php') )
    {
        require_once('units/'.$mInclude.'.php');
    
        return true;
    }
    else 
    {
        MELBIS_INC_halt('INCLUDE ABSENT', __FILE__.':'.__LINE__, 'Require include is absent!', 'units/'.$mInclude.'.php');
    }
} 


/** 
 * Function MELBIS_INC_halt
 * Returm info message about error and die
 **/    
function MELBIS_INC_halt($mType, $mFile, $mError, $mInfo = '') 
{
    if ( file_exists('./error.log') )
    {                               
        $log_file = __DIR__.'/../_error_front.log';
        $now = new DateTime('now', new DateTimeZone(TIME_ZONE));
        $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];    
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $refer = $_SERVER['HTTP_REFERER'] ?? '';
        $cookie = print_r($_COOKIE, true); 
        $sid = session_id() ?? ''; 
        $stamp = md5($ip.$agent);
                
        $log = "--- ERROR LOG [".$now->format("Y-m-d H:i:s")."] ---\n";
        $log .= "URL: http://$url\n";                                   
        $log .= "Stamp: $stamp\n";
        $log .= "Session: $sid\n";
        $log .= "IP: $ip\n";
        $log .= "Agent: $agent\n";
        $log .= "Referer: $refer\n";
        $log .= "Type: $mType\n";
        $log .= "Source: $mFile\n";
        $log .= "Message: $mError\n"; 
        $log .= "Info: ".trim($mInfo)."\n";        
        $log .= "Cookie Data: ".trim($cookie) . "\n";
        
        if (!empty($_POST)) 
        {
            $post = print_r($_POST, true);
            $log .= "POST Data: ".trim($post)."\n";
        }     
        
        $input = file_get_contents('php://input');
        if (!empty($input)) 
        {
            $json = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) 
            {
                $log .= "Input (JSON): ".trim(print_r($json, true))."\n";
            } 
            else 
            {
                $log .= "Input (RAW): ".trim($input)."\n";
            }                        
        }           
        
        $log .= "----------------------------\n\n";
                
        @file_put_contents($log_file, $log, FILE_APPEND | LOCK_EX);
    }
    
    // Headers
    $backup_begin = new DateTime(BACKUP_TIME_BEGIN, new DateTimeZone(TIME_ZONE));
    $backup_end = new DateTime(BACKUP_TIME_END, new DateTimeZone(TIME_ZONE));
    if ( $now >= $backup_begin && $now <= $backup_end ) 
    {
        // Service Unavailable
        $retry = $backup_end->getTimestamp() - $now->getTimestamp();                
        $error_header = '503 Service Unavailable';
        header('HTTP/1.1 '.$error_header);
        header('Retry-After: '.$retry);
    }
    else
    {        
        // Server Error
        $error_header = '500 Internal Server Error';
        header('HTTP/1.1 '.$error_header);              
        header('Content-Type: text/html; charset=utf-8');
    }
    
?>    
<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <title>System error in Melbis Shop <?php echo SCRIPT_VERSION.'.'.SCRIPT_BUILD; ?></title>
  </head>
  <body class="d-flex flex-column h-100 ">
    <main class="m-auto">
        <div class="card text-white bg-danger">
            <h3 class="card-header text-center">An exception occurred in shop's script</h3>
            <div class="card-body bg-white text-dark"> 
                <h1 class="text-center"><?php echo $error_header; ?></h1>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Error type:</strong> <code><?php echo $mType; ?></code></li>
                    <li class="list-group-item"><strong>Source, line:</strong> <code><?php echo $mFile; ?></code></li>
                    <li class="list-group-item"><strong>Error message:</strong> <code><?php echo $mError; ?></code> </li>
                </ul>                                
                <pre class="pre-scrollable my-2" style="white-space: pre-wrap"> <?php echo $mInfo; ?> </pre>
            </div>
            <div class="card-footer bg-light text-muted text-right">
                <?php echo date("H:i:s d-m-Y"); ?>   
            </div>  
        </div>        
    </main>
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between">
                <span class="text-muted mr-1">Powered by Melbis Shop <?php echo SCRIPT_VERSION.'.'.SCRIPT_BUILD; ?></span>
                <span class="text-muted"><a href="https://melbis.com" target="_blank">https://melbis.com</a></span>
            </div>            
        </div>
    </footer>  
  </body>
</html>
<?php

    // Now stop all scripts 
    exit;
}


?>