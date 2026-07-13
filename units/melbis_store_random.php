<?php
/***************************************************************************************************
 * @version 6.5.0.318 @ 2026-07-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_RANDOM
 **/
function MELBIS_STORE_RANDOM($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();
                       
    // Vars
    $how = $mVars['how'];
    
    // Get random goods
    $command = "SELECT s.id
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON s.id = ts.store_id
                  JOIN {DBNICK}_topic t
                    ON t.id = ts.topic_id                    
                 WHERE s.no_visible = 0   
                   AND t.kind_key = 'kGoods'
              GROUP BY s.id
              ORDER BY s.id
                 LIMIT :HOW 
                ";   
    $param = [
        'how' => $how
        ];                 
    $goods = MELBIS()->SqlSelect(__LINE__, $command, $param);

    // Goods
    MELBIS()->TplAssign($tpl, 'GOODS', $goods);   
                                                         
    // Save store for static      
    MELBIS()->EnumSet('store', array_column($goods, 'id'));                

    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>