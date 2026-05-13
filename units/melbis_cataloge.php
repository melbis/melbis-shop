<?php
/***************************************************************************************************
 * @version 6.5.0.264 @ 2026-05-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

/** 
 * Function MELBIS_CATALOGE
 **/
function MELBIS_CATALOGE($mVars)
{
    // Create 
    $tpl = MELBIS()->TplCreate();   
    
    // Find root
    $command = "SELECT id, tindex, tlevel
                  FROM {DBNICK}_topic
                 WHERE kind_key = 'kFirst'              
               ";                    
    $root = MELBIS()->SqlSelectFlat(__LINE__, $command);         
        
    // Get item
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
        'tlevel' => $root['tlevel'] + 2, 
        'tindex' => $root['id'] 
        ];                
    $menu = MELBIS()->SqlSelect(__LINE__, $command, $param);
    
    // Menu        
    MELBIS()->TplAssign($tpl, 'MENU', $menu);              
    
    // Final    
    return MELBIS()->TplFinal($tpl, 'main');
} 

?>