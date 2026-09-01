<?php
/***************************************************************************************************
 * @version 6.5.1.420 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the sections under
 *
 **************************************************************************************************/

namespace MELBIS_CATALOGE_SUB;

use MELBIS_INC_WEB_TOPIC as TOPIC;
use MELBIS_INC_WEB_CALLBACK as CALLBACK;

// Define Callback
CALLBACK\Define(); 


/** 
 * Function Main
 **/
function Main($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();   
    
    // Vars - parameter or post
    $id = !empty($mVars['id']) ? $mVars['id'] : (int) ( $mVars['post']['id'] ?? 0 );   
    
    // Find root - the parent
    $command = "SELECT id, tlevel
                  FROM {DBNICK}_topic
                 WHERE id = :ID 
                ";                                        
    $param = [
        'id' => $id
        ];            
    $root = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);    
    if ( !isset($root['id']) ) return '';       
        
    // Menu
    $menu = TOPIC\Menu($root['id'], $root['tlevel']);
    MELBIS()->TplAssign($tpl, 'MENU', $menu);
    
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 




?>