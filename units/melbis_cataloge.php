<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
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
    
    // Find root - the catalogue starts from the section marked kFirst
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