<?php
/***************************************************************************************************
 * @version 6.5.0.274 @ 2026-05-17
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_FEATURES
 **/
function MELBIS_STORE_FEATURES($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();    
    
    // Vars
    $id = $mVars['id'];
    $ids = MELBIS()->EnumGet('store', $id);      
        
    // Get features
    $command = "SELECT si.store_id, i.name,                     
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
                 WHERE si.store_id IN ( $ids ) 
                   AND i.in_topic = 1    
              GROUP BY si.store_id, i.id
              ORDER BY i.absindex
                ";                    
    $info = MELBIS()->SqlSelectEnum(__LINE__, $command, 'store_id', $id);
    if ( empty($info) ) return '';

    // Assign
    MELBIS()->TplAssign($tpl, 'INFO', $info);
                  
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>