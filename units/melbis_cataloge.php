<?php
/***************************************************************************************************
 * @version 6.5.1.525 @ 2026-10-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the menu of sections
 *
 **************************************************************************************************/

namespace MELBIS_CATALOGE;

use MELBIS_INC_PAGE_MENU as PAGE_MENU;
use MELBIS_INC_PAGE_CALLBACK;


// ————— Public —————
//==================================================================================================


/** 
 * Function Main
 **/
function Main($mVars)
{                      
    // Create 
    $tpl = MELBIS()->TplCreate();    
    
    // Find root - SHOP
    $command = "SELECT id, tlevel
                  FROM {DBNICK}_topic
                 WHERE skey = 'SHOP'              
               ";                    
    $root = MELBIS()->SqlSelectFlat(__LINE__, $command);         
        
    // Menu        
    $menu = PAGE_MENU\Menu($root['id'], $root['tlevel']);
    MELBIS()->TplAssign($tpl, 'MENU', $menu);              
    
    // Final    
    return MELBIS()->TplFinal($tpl, 'main');
} 

?>