<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
                          

/** 
 * Function MELBIS_CRON
 **/
function MELBIS_CRON($mVars)
{                          
    // Header
    header('Content-Type: text/plain; charset=utf-8');
                        
    // Safety
    MELBIS()->CronLocalOnly(); 
                                 
    // Insert
    //$param = [ 'id' => MELBIS()->SqlGenId('store') ];
    //MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store', $param);
    
    // Final
    return 'Done!';
} 


?>