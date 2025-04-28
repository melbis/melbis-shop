<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/

/** 
 * Function MELBIS_CATALOGE
 **/
function MELBIS_CATALOGE($mVars)
{
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();      
    
    // Find root
    $command = "SELECT id, tindex, tlevel
                  FROM {DBNICK}_topic
                 WHERE skey = 'CATALOG'              
               ";                    
    $root = $gParser->SqlSelectToArray(__LINE__, $command);         
        
    // Get item
    $command = "SELECT t.id, t.name, t.seo_psu, t.seo_title, t_s.how
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
        $gParser->TplAssign($tpl, ['ID'    => $item['id'],
                                   'SUB'   => serialize(['id' => $item['id']]),
                                   'NAME'  => MELBIS_INC_LANG('kTopic', 'NAME', $item['id'], $item['name']),
                                   'LINK'  => $item['seo_psu'],
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
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 

?>