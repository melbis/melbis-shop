<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_TOPIC
 **/
function MELBIS_STORE_TOPIC($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();
            
    // Vars
    $id = (int) $mVars['topic_id'];  
    
    $command = "WITH RECURSIVE topic_tree AS ( 
                    SELECT *
                      FROM {DBNICK}_topic
                     WHERE id = '$id'
                     UNION ALL     
                    SELECT t.* 
                      FROM {DBNICK}_topic t
                      JOIN topic_tree tt 
                        ON t.tindex = tt.id
                     WHERE t.kind_key = 'kGoods' )                  
                SELECT s.id 
                  FROM topic_tree tt
                  JOIN {DBNICK}_topic_store ts
                    ON ts.topic_id = tt.id
                  JOIN {DBNICK}_store s
                    ON ts.store_id = s.id
                 WHERE s.no_visible = 0 
              ORDER BY tt.absindex, ts.pos
                 LIMIT 100 
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