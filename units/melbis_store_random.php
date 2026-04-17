<?php
/***************************************************************************************************
 * @version 6.5.0.238 @ 2026-04-17
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_RANDOM
 **/
function MELBIS_STORE_RANDOM($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();
                       
    // Vars
    $lim = (int) $mVars['limit'];
    
    // Get random goods
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