<?php
/***************************************************************************************************
 * 
 * minify.php - Minifier/Compressor/Obfuscator
 * 
 ***************************************************************************************************
 * @version 6.3.0
 * @copyright 2019 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/   
                                          
 
switch (pathinfo($_POST['name'], PATHINFO_EXTENSION)) 
{
    case 'php':
        $temp = tmpfile();
        fwrite($temp, $_POST['content']); 
        $info = stream_get_meta_data($temp);                   
        echo php_strip_whitespace($info['uri']);    
        fclose($temp);        
        break;                 
        
    case 'js':                    
        $code = preg_replace('#<script(.*?)>#is', '', $_POST['content']);    
        $code = preg_replace('#</script>#is', '', $code);
        $data = array('input' => $code);
        $ch = curl_init();    
        curl_setopt($ch, CURLOPT_URL, 'https://javascript-minifier.com/raw');
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
        curl_setopt($ch, CURLOPT_URL, 'https://cssminifier.com/raw');
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