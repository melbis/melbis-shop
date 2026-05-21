<?php
/***************************************************************************************************
 * @version 6.5.0.279 @ 2026-05-21
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
                          

/** 
 * Function MELBIS_CRON
 **/
function MELBIS_CRON($mVars)
{                   
    // Up limits            
    set_time_limit(300);
    ignore_user_abort(true);    

    // Header
    header('Content-Type: text/plain; charset=utf-8'); 
                                 
    // Insert
    //$param = [ 'id' => MELBIS()->SqlGenId('store') ];
    //MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store', $param);
    
    // Final
    return 'Done!';
} 


?>