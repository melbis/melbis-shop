<?php
/***************************************************************************************************
 * @version 6.5.0.280 @ 2026-05-27
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_CATALOGE_SUB
 **/
function MELBIS_CATALOGE_SUB($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();
            
    // Vars               
    $id = !empty($mVars['id']) ? $mVars['id'] : (int) $mVars['post']['id'];   
    
    $command = "SELECT id, tlevel
                  FROM {DBNICK}_topic
                 WHERE id = :ID 
                ";                                        
    $param = [
        'id' => $id
        ];            
    $root = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);    
    if ( !isset($root['id']) ) return '';       
        
    // Get menu items     
    $command = "SELECT t.id, t.name, t.kind_key, t.link, t_s.sub
                  FROM {DBNICK}_topic t                                                                           
             LEFT JOIN ( SELECT tindex, COUNT(*) AS sub 
                           FROM {DBNICK}_topic
                          WHERE no_visible = '0'
                            AND tlevel = :TLEVEL                                
                       GROUP BY tindex
                       ) AS t_s 
                    ON t.id = t_s.tindex                                                            
                 WHERE t.tindex = :TINDEX
                   AND t.no_visible = '0'                                                                
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