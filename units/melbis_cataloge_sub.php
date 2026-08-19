<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

namespace MELBIS_CATALOGE_SUB;

use MELBIS_INC_WEB_TOPIC as TOPIC;
use MELBIS_INC_WEB_CALLBACK as CALLBACK;

// Define Callback - the ajax call is an entry point of its own
CALLBACK\Define(); 


/** 
 * Function Main
 **/
function Main($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();   
    
    // Vars - from a template the id comes as a parameter, over AJAX in the post               
    $id = !empty($mVars['id']) ? $mVars['id'] : (int) ( $mVars['post']['id'] ?? 0 );   
    
    // Find root - the section whose children are asked for
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