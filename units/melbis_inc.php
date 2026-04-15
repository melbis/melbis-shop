<?php
/***************************************************************************************************
 * @version 6.5.0.230 @ 2026-04-15
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov  
 **************************************************************************************************/
 
// Core
//-----
require_once('core/core.php');

// Catch error control
//--------------------
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


// Configuration
//--------------
$config = json_decode(file_get_contents('./config.json'), true);    
foreach( $config as $const => $value)
{
    define($const, $value);
}
                    

// Shop lock
//----------
if ( file_exists('shop.lock') ) 
{
    MELBIS_INC_halt('SHOP LOCKED', __FILE__.':'.__LINE__, 'Shop locked!', 'shop.lock');
} 
 
// Composer loader
//----------------
if ( file_exists('vendor/autoload.php') ) 
{
    require_once('vendor/autoload.php');
}

// Melbis loader
//--------------
if ( !function_exists('MELBIS_INC_autoload') ) 
{
    function MELBIS_INC_autoload($mClassName)
    {                
        $melbis_space = 'Melbis\\MelbisShop\\';
        if ( strncmp($melbis_space, $mClassName, strlen($melbis_space)) === 0 ) 
        {
            $file = str_replace($melbis_space, 'core/class/', $mClassName).'.php';
            if ( file_exists($file) ) 
            {
                require_once($file);
            }
        }                
    }
    spl_autoload_register('MELBIS_INC_autoload');
}


/** 
 * Function MELBIS_INC_halt
 * Returm info message about error and die
 **/    
function MELBIS_INC_halt($mType, $mFile, $mError, $mInfo = '') 
{
    // Vars
    $config_exist = defined('MELBIS_TIME_ZONE');          
    
    // Datetime                          
    $time_zone = $config_exist ? MELBIS_TIME_ZONE : 'UTC';    
    $now = new DateTime('now', new DateTimeZone($time_zone));
               
    // Save log      
    if ( file_exists(__DIR__.'/../error.save') )
    {                               
        $log_file = __DIR__.'/../_error_front.log';
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
    
    // Backup time
    $backup_now = false;    
    $error_header = '500 Internal Server Error';
    if ( $config_exist )
    {
        $backup_begin = new DateTime(MELBIS_BACKUP_TIME_BEGIN, new DateTimeZone(MELBIS_TIME_ZONE));
        $backup_end = new DateTime(MELBIS_BACKUP_TIME_END, new DateTimeZone(MELBIS_TIME_ZONE));   
                 
        if ( $now >= $backup_begin && $now <= $backup_end ) 
        {        
            // Service Unavailable             
            $backup_now = true;
            $error_header = '503 Service Unavailable';
            if ( !headers_sent() )
            {            
                $retry = $backup_end->getTimestamp() - $now->getTimestamp();                            
                header('HTTP/1.1 '.$error_header);
                header('Retry-After: '.$retry);                
            }
        }
    }    
                                   
    if ( !$backup_now )            
    {        
        // Server Error  
        if ( !headers_sent() )
        { 
            header('HTTP/1.1 '.$error_header);              
            header('Content-Type: text/html; charset=utf-8');
        }
    }   
    
?>    
<!doctype html>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <title>System error in Melbis Shop <?php echo MELBIS_SCRIPT_VERSION.'.'.MELBIS_SCRIPT_BUILD; ?></title>
  </head>
  <body class="d-flex flex-column h-100 ">
    <main class="m-auto" style="max-width: 800px;">
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
                <span class="text-muted mr-1">Powered by Melbis Shop <?php echo MELBIS_SCRIPT_VERSION.'.'.MELBIS_SCRIPT_BUILD; ?></span>
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