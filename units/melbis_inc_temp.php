<?php
/***************************************************************************************************
 * @version 6.5.0.319 @ 2026-07-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov  
 **************************************************************************************************/


/** 
 * Function MELBIS_INC_TEMP_topic_sub
 **/
function MELBIS_INC_TEMP_topic_sub($mId)
{ 
    $command = "create TEMPORARY TABLE {DBNICK}_topic_sub ENGINE=MEMORY
                WITH RECURSIVE topic_sub AS (
                SELECT t.tindex, t.id  
                  FROM {DBNICK}_topic t   
                 WHERE t.id = :ID              
                 UNION ALL      
                SELECT ts.tindex, t.id 
                  FROM topic_sub ts
                  JOIN {DBNICK}_topic t 
                    ON ts.id = t.tindex )
                SELECT * FROM topic_sub                     
               ";                                 
    $param = [
        'id' => $mId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param);  
} 



?>