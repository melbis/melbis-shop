<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Claude (AI assistant)
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_PAGE
 **/
function MELBIS_STORE_PAGE($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();
    
    // Vars
    $id = (int) $mVars['id'];
    
    // Get goods                      
    $command = "SELECT s.id, s.name, s.intro, s.descr, s.how, s.update_time,
                       IF(s.code_shop <> '', s.code_shop, s.id) AS code,
                       IF(c.id IS NULL, s.price, 
                        IF(c.division = 0, s.price*c.multiplex,
                         IF(c.multiplex <> 0, s.price/c.multiplex, 0))) AS price_curr,
                       c.name AS curr_name,
                       kv.value_txt AS status_name                            
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = s.price_curr_id             
             LEFT JOIN {DBNICK}_key_value kv
                    ON ( s.status_key = kv.key_name AND kv.key_code = 'STORE_STATUS_KEY' )                                  
                 WHERE s.id = :ID
                   AND s.no_visible = 0                               
                ";
    $params = [
        'id' => $id 
        ];        
    $store = MELBIS()->SqlSelectFlat(__LINE__, $command, $params);    
    if ( !isset($store['id']) ) return '';        
    
    // Get features
    $command = "SELECT i.name,                     
                       IF(i.type_key = 'kDecimal', ANY_VALUE(si.value_dec), NULL) AS value_dec,
                       CASE
                        WHEN i.type_key = 'kSet' THEN GROUP_CONCAT(iv.name SEPARATOR ', ')
                        WHEN i.type_key = 'kText' THEN ANY_VALUE(si.value_txt)
                       END AS value_txt                     
                  FROM {DBNICK}_store_info si
                  JOIN {DBNICK}_info i
                    ON i.id = si.info_id
             LEFT JOIN {DBNICK}_info_value iv             
                    ON iv.id = si.value_id     
                 WHERE si.store_id = :ID 
                   AND i.in_goods = 1    
              GROUP BY i.id
              ORDER BY i.absindex
                ";                    
    $info = MELBIS()->SqlSelect(__LINE__, $command, $params);

    // Assign
    MELBIS()->TplAssign($tpl, $store);
    MELBIS()->TplAssign($tpl, 'INFO', $info);
    
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>