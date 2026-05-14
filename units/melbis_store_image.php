<?php
/***************************************************************************************************
 * @version 6.5.0.268 @ 2026-05-14
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_IMAGE
 **/
function MELBIS_STORE_IMAGE($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();              
        
    // Vars
    $key = $mVars['key'];                            
    $id = $mVars['id'];
    $ids = MELBIS()->EnumGet('store', $id);                          
    
    // Get image           
    $command = "SELECT *
                  FROM {DBNICK}_files_store
                 WHERE kind_key = :KEY 
                   AND elem_id IN ( $ids ) 
                ";                          
    $param = [    
        'key'   => $key
        ];
    $image = MELBIS()->SqlSelectEnumFlat(__LINE__, $command, 'id', $id, $param);                          
      
    // Image
    MELBIS()->TplAssign($tpl, $image);

    // Final    
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>