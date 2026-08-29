<?php
/***************************************************************************************************
 * @version 6.5.1.415 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov  
 **************************************************************************************************
 *
 * Sub  - The sections under each
 * Menu - The sections of one menu
 *
 **************************************************************************************************/
 
namespace MELBIS_INC_WEB_TOPIC; 

/** 
 * Function Sub
 **/
function Sub($mId)
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

/** 
 * Function Menu
 **/
function Menu($mId, $mLevel)
{ 
    // The sections right under it
    $command = "SELECT t.id, t.name, t.kind_key, t.link, t_s.sub                       
                  FROM {DBNICK}_topic t                                                                           
             LEFT JOIN ( SELECT tindex, COUNT(*) AS sub 
                           FROM {DBNICK}_topic
                          WHERE no_visible = 0
                            AND tlevel = :TLEVEL
                       GROUP BY tindex
                       ) AS t_s 
                    ON t.id = t_s.tindex                                                            
                 WHERE t.tindex = :TINDEX
                   AND t.no_visible = 0                                                                
              ORDER BY t.absindex   
                ";                                       
    $param = [ 
        'tlevel' => $mLevel + 2, 
        'tindex' => $mId 
        ];                

    return MELBIS()->SqlSelect(__LINE__, $command, $param);
} 



?>
