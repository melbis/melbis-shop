<?php
/***************************************************************************************************
 * @version 6.5.0.310 @ 2026-07-11
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_TOPIC
 **/
function MELBIS_STORE_TOPIC($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();
            
    // Vars
    $id = $mVars['id'];
    
    // Sub topic
    MELBIS_INC_TEMP_topic_sub($id);  
    
    $command = "SELECT s.id 
                  FROM {DBNICK}_topic_sub t_sub
                  JOIN {DBNICK}_topic t  
                    ON t_sub.id = t.id
                  JOIN {DBNICK}_topic_store ts
                    ON ts.topic_id = t.id
                  JOIN {DBNICK}_store s
                    ON ts.store_id = s.id
                 WHERE s.no_visible = 0 
              ORDER BY t.absindex, ts.pos
                 LIMIT 100 
                ";                    
    $goods = MELBIS()->SqlSelect(__LINE__, $command);     
    
    // Goods
    MELBIS()->TplAssign($tpl, 'GOODS', $goods);   
                                                         
    // Save store for static      
    MELBIS()->EnumSet('store', array_column($goods, 'id'));        

    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>