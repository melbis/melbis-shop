<?php
/***************************************************************************************************
 * 
 * minify.php - Minifier/Compressor/Obfuscator
 * 
 ***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/   
                                          
 
switch (pathinfo($_POST['name'], PATHINFO_EXTENSION)) 
{                        
    case 'js':                    
        $code = preg_replace('#<script(.*?)>#is', '', $_POST['content']);    
        $code = preg_replace('#</script>#is', '', $code);
        $data = array('input' => $code);
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