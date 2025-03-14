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
    
    list( $date, $time ) = explode(' ', $mFile['upload_time']);
    list( $y, $m, $d ) = explode('-', $date);
    list( $h, $n, $s ) = explode(':', $time);
    
    return $gSitePath.'files/'.$y.'/'.$m.'_'.$d.'/'.$h.'_'.$n.'/'.$mFile['file_name']; 
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
    return number_format($mValue, $mPrec, "," ,"'");
}

/** 
 * Function MELBIS_INC_STD_number_short
 * Return format price
 **/
function MELBIS_INC_STD_number_short($mValue, $mMaxPrec = 2) 
{
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
    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));

    return gmdate("Y-m-d", time() + $offset);
}

/** 
 * Function MELBIS_INC_STD_get_week
 * Return first date of current week
 **/
function MELBIS_INC_STD_get_week() 
{

    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));
    $time = time() + $offset;
    $now_day = gmdate("w", $time);
    if ( $now_day == 0 ) $now_day = 7;
    
    return gmdate("Y-m-d", $time - ($now_day-1)*86400 );
}

/** 
 * Function MELBIS_INC_STD_get_month
 * Return current month date
 **/
function MELBIS_INC_STD_get_month() 
{
    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));

    return gmdate("Y-m-1",  time() + $offset );
}

/** 
 * Function MELBIS_INC_STD_get_time
 * Return current time
 **/
function MELBIS_INC_STD_get_time() 
{
    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));

    return gmdate("H:i:s", time() + $offset);
}

/** 
 * Function MELBIS_INC_STD_get_now
 * Return current formated date and time
 **/
function MELBIS_INC_STD_get_now($mFormat = "Y-m-d H:i:s") 
{
    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));

    return gmdate($mFormat, time() + $offset);
}

/** 
 * Function MELBIS_INC_STD_make_date
 * Return datetime formated with offset
 **/
function MELBIS_INC_STD_make_date($mOffSet, $mFormat = "Y-m-d H:i:s") 
{
    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));

    return gmdate($mFormat, time() + $offset + $mOffSet);
}

/** 
 * Function MELBIS_INC_STD_make_time
 * Return time formated with offset
 **/
function MELBIS_INC_STD_make_time($mOffSet, $mFormat = "H:i:s") 
{
    $offset = timezone_offset_get(timezone_open(TIME_ZONE), new DateTime("now", new DateTimeZone("GMT")));

    return gmdate($mFormat, time() + $offset + $mOffSet);
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