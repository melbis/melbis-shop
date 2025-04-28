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
function MELBIS_STORE_RANDOM($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();
            
    // Get random goods
    $lim = $mVars['limit']*1;
    $command = "SELECT s.id
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON s.id = ts.store_id
                  JOIN {DBNICK}_topic t
                    ON t.id = ts.topic_id                    
                 WHERE s.no_visible = 0   
                   AND t.kind_key = 'kGoods'
              ORDER BY s.id
                 LIMIT $lim 
                ";                    
    $goods = $gParser->SqlSelect(__LINE__, $command);
    if ( count($goods) == 0 ) $gParser->TplAssign($tpl, 'ITEM', '');
    foreach ( $goods as $item )
    {
        $gParser->TplAssign($tpl, 'ID', $item['id']);
        $gParser->TplParse($tpl, 'ITEM', '.item');  
    }         
    
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 



?>