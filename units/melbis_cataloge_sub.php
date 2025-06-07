<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_CATALOGE_SUB
 **/
function MELBIS_CATALOGE_SUB($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();
            
    // Vars
    $id = (int) $mVars['post']['id'];   
    
    $command = "SELECT id, tlevel
                  FROM {DBNICK}_topic
                 WHERE id = '$id' 
                ";                    
    $root = $gParser->SqlSelectToArray(__LINE__, $command);       
        
    // Get menu items     
    $command = "SELECT t.id, t.name, t.kind_key, t.link, t_s.how
                  FROM {DBNICK}_topic t                                                                           
             LEFT JOIN ( SELECT tindex, COUNT(*) AS how 
                           FROM {DBNICK}_topic
                          WHERE no_visible = '0'
                            AND tlevel = '$root[tlevel]' + 2                                
                       GROUP BY tindex
                       ) AS t_s 
                    ON t.id = t_s.tindex                                                            
                 WHERE t.tindex = '$root[id]'
                   AND t.no_visible = '0'                                                                
              ORDER BY t.absindex                
                ";                    
    $menu = $gParser->SqlSelect(__LINE__, $command);    
    foreach ($menu as $item)
    {        
        if ( $item['name'] == '-' )
        {
            $gParser->TplParse($tpl, 'ITEM', '.item_div');
        }
        else
        {                                    
            $name = htmlspecialchars($item['name']); // MELBIS_INC_LANG('kTopic', 'NAME', $item['id'], $item['name']);
            $link = ( $item['kind_key'] == 'kLink' ) ? $item['link'] : '/'.$mVars['lang'].'/?topic_id='.$item['id'] ;                                    
            $gParser->TplAssign($tpl, ['ID'    => $item['id'],
                                       'NAME'  => $name,
                                       'LINK'  => $link
                                       ]);        
            if ( !isset($item['how']) )
            {
                $gParser->TplParse($tpl, 'ITEM', '.item');
            }            
            else
            {
                $gParser->TplParse($tpl, 'ITEM', '.item_sub');
            }
        }
    } 
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 




?>