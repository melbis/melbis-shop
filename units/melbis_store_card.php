<?php
/***************************************************************************************************
 * @version 6.5.0.298 @ 2026-07-04
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
  

/** 
 * Function MELBIS_STORE_CARD
 **/
function MELBIS_STORE_CARD($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();     
    
    // Vars
    $id = $mVars['id'];
    $ids = MELBIS()->EnumGet('store', $id);            
    
    // Get goods                      
    $command = "SELECT s.id, s.name, s.intro, s.update_time,
                       IF(s.code_shop <> '', s.code_shop, s.id) AS code,
                       IF(c.id IS NULL, s.price, 
                        IF(c.division = 0, s.price*c.multiplex,
                         IF(c.multiplex <> 0, s.price/c.multiplex, 0))) AS price_curr,
                       kv.value_txt status_name                            
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = s.price_curr_id             
             LEFT JOIN {DBNICK}_key_value kv
                    ON ( s.status_key = kv.key_name AND kv.key_code = 'STORE_STATUS_KEY' )                                  
                 WHERE s.id IN ( $ids )                               
                ";               
    $store = MELBIS()->SqlSelectEnumFlat(__LINE__, $command, 'id', $id);    
    if ( empty($store) ) return '';        
    
    // Assign
    MELBIS()->TplAssign($tpl, $store);  
    
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 
  


?>