<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_RANDOM
 **/
function MELBIS_STORE_CARD($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();

    // Get goods                  
    $command = "SELECT s.*,
                       IF(c.id IS NULL, s.price, 
                        IF(c.division = 0, s.price*c.multiplex,
                         IF(c.multiplex <> 0, s.price/c.multiplex, 0))) AS price_curr,
                       kv.value_txt status_name                            
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = s.price_curr_id             
             LEFT JOIN {DBNICK}_key_value kv
                   ON ( s.status_key = kv.key_name AND kv.key_code = 'STORE_STATUS_KEY' )                                  
                 WHERE s.id = '$mVars[id]'
              ORDER BY id
                ";                    
    $item = $gParser->SqlSelectToArray(__LINE__, $command);
    $gParser->TplAssign($tpl, ['ID'         => $item['id'],                               
                               'NAME'       => htmlspecialchars($item['name']),
                               'CODE'       => htmlspecialchars($item['code_shop']),
                               'INTRO'      => htmlspecialchars($item['intro']),
                               'STATUS'     => htmlspecialchars($item['status_name']),
                               'PRICE'      => MELBIS_INC_STD_number($item['price_curr'], 2),
                               'UPDATE'     => date("j F Y", strtotime($item['update_time'])),                                        
                               ]);   
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 



?>