<?php
/***************************************************************************************************
 * @version 6.5.1.416 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the menu of sections
 *
 **************************************************************************************************/

namespace MELBIS_CATALOGE;

use MELBIS_INC_WEB_TOPIC as TOPIC;

/** 
 * Function Main
 **/
function Main($mVars)
{                      
    // Create 
    $tpl = MELBIS()->TplCreate();    
    
    // Find root - kFirst
    $command = "SELECT id, tlevel
                  FROM {DBNICK}_topic
                 WHERE kind_key = 'kFirst'              
               ";                    
    $root = MELBIS()->SqlSelectFlat(__LINE__, $command);         
        
    // Menu        
    $menu = TOPIC\Menu($root['id'], $root['tlevel']);
    MELBIS()->TplAssign($tpl, 'MENU', $menu);              
    
    // Final    
    return MELBIS()->TplFinal($tpl, 'main');
} 

?>