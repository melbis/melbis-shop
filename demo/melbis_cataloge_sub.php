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
    $id = $mVars['post']['id']*1;   
    
    $command = "SELECT id, tlevel
                  FROM {DBNICK}_topic
                 WHERE id = '$id' 
                ";                    
    $root = $gParser->SqlSelectToArray(__LINE__, $command);       
        
    // Get menu items     
    $command = "SELECT t.id, t.name, t.seo_psu, t.seo_title, t.kind_key, t.link, t.in_xml, t_s.how
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
            $link = ( $item['kind_key'] == 'kLink' ) ? $item['link'] : '/'.$mVars['lang'].$item['seo_psu'] ;                                    
            $gParser->TplAssign($tpl, ['ID'    => $item['id'],
                                       'NAME'  => MELBIS_INC_LANG('kTopic', 'NAME', $item['id'], $item['name']),
                                       'LINK'  => $link,
                                       'ICON'  => $item['seo_title']
                                       ]);        
            if ( is_null($item['how']) )
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