<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 *     
 **************************************************************************************************
 * 
 * MELBIS_INC_STD_path                  - Return file path from time
 * MELBIS_INC_STD_utf_encode            - Encoding to UTF-8             
 * MELBIS_INC_STD_utf_decode            - Decoding from UTF-8  
 * MELBIS_INC_STD_number                - Return format price
 * MELBIS_INC_STD_number_short          - Return format price
 * MELBIS_INC_STD_get_date              - Return current date
 * MELBIS_INC_STD_get_week              - Return first date of current week
 * MELBIS_INC_STD_get_month             - Return current month date
 * MELBIS_INC_STD_get_time              - Return current time
 * MELBIS_INC_STD_get_now               - Return current formated date and time
 * MELBIS_INC_STD_make_date             - Return datetime formated with offset
 * MELBIS_INC_STD_make_time             - Return time formated with offset
 * MELBIS_INC_STD_html2text             - Convert HTML to plain text      
 *     
 **************************************************************************************************/

/** 
 * Function MELBIS_INC_STD_path
 * Return file path from time
 **/
function MELBIS_INC_STD_path($mFile) 
{
    global $gSitePath;
    
    if ( !strtotime($mFile['upload_time'] ?? '') ) return '';

    list( $date, $time ) = explode(' ', $mFile['upload_time']);
    list( $y, $m, $d ) = explode('-', $date);
    list( $h, $n, $s ) = explode(':', $time);
    
    return $gSitePath.'files/'.$y.'/'.$m.'_'.$d.'/'.$h.'_'.$n.'/'.$mFile['file_name']; 
} 

/** 
 * Function MELBIS_INC_STD_text
 * Return text with rigth path images
 **/
function MELBIS_INC_STD_text($mText) 
{
    global $gSitePath;
        
    $text = str_replace('src="files/2', 'src="'.$gSitePath.'files/2', $mText);
    $text = str_replace('href="files/2', 'href="'.$gSitePath.'files/2', $text);

    return $text;
} 
 
/** 
 * Function MELBIS_INC_STD_utf_decode
 * Decoding from UTF-8
 **/
function MELBIS_INC_STD_utf_decode($mValue) 
{
    if ( is_array($mValue) )
    {
        foreach ( $mValue as $key => $value ) 
        {
            $mValue[$key] = iconv('UTF-8', SHOP_CHARSET.'//IGNORE', $value);
        }
    }    
    else
    {
        $mValue = iconv('UTF-8', SHOP_CHARSET.'//IGNORE', $mValue);
    }
    
    return $mValue;
} 
 

/** 
 * Function MELBIS_INC_STD_utf_encode
 * Encoding to UTF-8
 **/
function MELBIS_INC_STD_utf_encode($mValue) 
{
    if ( is_array($mValue) )
    {
        foreach ( $mValue as $key => $value ) 
        {
            $mValue[$key] = iconv(SHOP_CHARSET, 'UTF-8//IGNORE', $value);
        }
    }    
    else
    {
        $mValue = iconv(SHOP_CHARSET, 'UTF-8//IGNORE', $mValue);
    }
    
    return $mValue;
} 
 
     
/** 
 * Function MELBIS_INC_STD_number
 * Return format price
 **/
function MELBIS_INC_STD_number($mValue, $mPrec = 2) 
{
    return number_format((float)$mValue, $mPrec, "," ,"'");
}

/** 
 * Function MELBIS_INC_STD_number_short
 * Return format price
 **/
function MELBIS_INC_STD_number_short($mValue, $mMaxPrec = 2) 
{
    $mValue = (float) $mValue;
    if ( ceil($mValue) == $mValue ) 
    {
        $mPrec = 0;  
    }
    else
    {
        $mPrec = $mMaxPrec;
    }         
    
    return number_format($mValue, $mPrec, "," ,"'");
}
 
/** 
 * Function MELBIS_INC_STD_get_date
 * Return current date
 **/
function MELBIS_INC_STD_get_date() 
{
    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));

    return $date->format("Y-m-d");     

}

/** 
 * Function MELBIS_INC_STD_get_week
 * Return first date of current week
 **/
function MELBIS_INC_STD_get_week() 
{

    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));
    $now_day = (int)$date->format('N'); 
    $date->modify('-'.($now_day - 1).' days');

    return $date->format('Y-m-d');
}

/** 
 * Function MELBIS_INC_STD_get_month
 * Return current month date
 **/
function MELBIS_INC_STD_get_month() 
{
    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));

    return $date->format("Y-m-1");     
}

/** 
 * Function MELBIS_INC_STD_get_time
 * Return current time
 **/
function MELBIS_INC_STD_get_time() 
{
    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));

    return $date->format("H:i:s");    
}

/** 
 * Function MELBIS_INC_STD_get_now
 * Return current formated date and time
 **/
function MELBIS_INC_STD_get_now($mFormat = "Y-m-d H:i:s") 
{
    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));

    return $date->format($mFormat);    
}

/** 
 * Function MELBIS_INC_STD_make_date
 * Return datetime formated with offset
 **/
function MELBIS_INC_STD_make_date($mOffSet, $mFormat = "Y-m-d H:i:s") 
{
    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));
    $date->modify("{$mOffSet} seconds");

    return $date->format($mFormat);
}

/** 
 * Function MELBIS_INC_STD_make_time
 * Return time formated with offset
 **/
function MELBIS_INC_STD_make_time($mOffSet, $mFormat = "H:i:s") 
{
    $date = new DateTime('now', new DateTimeZone(TIME_ZONE));
    $date->modify("{$mOffSet} seconds");

    return $date->format($mFormat);
}
 
/** 
 * Function MELBIS_INC_STD_html2text
 * Convert HTML to plain text
 **/
function MELBIS_INC_STD_html2text($Document) 
{
    $Rules = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
                    '@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
                    '@([\r\n])[\s]+@',                // Strip out white space
                    '@&(quot|#34);@i',                // Replace HTML entities
                    '@&(amp|#38);@i',                 //   Ampersand &
                    '@&(lt|#60);@i',                  //   Less Than <
                    '@&(gt|#62);@i',                  //   Greater Than >
                    '@&(nbsp|#160);@i',               //   Non Breaking Space
                    '@&(iexcl|#161);@i',              //   Inverted Exclamation point
                    '@&(cent|#162);@i',               //   Cent 
                    '@&(pound|#163);@i',              //   Pound
                    '@&(copy|#169);@i',               //   Copyright
                    '@&(reg|#174);@i'                 //   Registered
                    );                
    $Replace = array ('',
                      '',
                      ' ',
                      '"',
                      '&',
                      '<',
                      '>',
                      ' ',
                      chr(161),
                      chr(162),
                      chr(163),
                      chr(169),
                      chr(174),
                      '');
                             
    return preg_replace($Rules, $Replace, $Document);
}


?>