<?php
/***************************************************************************************************
 * @version 6.5.1.419 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Runs the jobs
 *
 **************************************************************************************************/

namespace MELBIS_CRON;

/** 
 * Function Main
 **/
function Main($mVars)
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