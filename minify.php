<?php
/***************************************************************************************************
 * @version 6.5.0.330 @ 2026-07-21
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// The converter is called by the engine itself, so only local requests are allowed
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if ( $ip !== '127.0.0.1' && $ip !== '::1' )
{
    header('HTTP/1.1 403 Forbidden');
    die('Only local allowed');
}


switch ( pathinfo($_POST['name'], PATHINFO_EXTENSION) )
{                        
    case 'js':                    
        $data = array('input' => $_POST['content']);
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, 'https://www.toptal.com/developers/javascript-minifier/api/raw');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);        
        echo curl_exec($ch);
        curl_close($ch);
        break;                 
        
    case 'css':               
        $data = array('input' => $_POST['content']);
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, 'https://www.toptal.com/developers/cssminifier/api/raw');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);        
        echo curl_exec($ch);
        curl_close($ch);
        break;
        
    default:
       echo $_POST['content'];
}

?>