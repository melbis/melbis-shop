<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_CATALOGE_CENTER
 **/
function MELBIS_CATALOGE_CENTER($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();
    
        
    // Get topics
    $command = "SELECT t.id, ta.name, t.seo_psu, 
                        ft.file_name, ft.upload_time
                  FROM {DBNICK}_topic_alt ta
                  JOIN {DBNICK}_topic t
                    ON t.id = ta.topic_id                  
                  JOIN {DBNICK}_files_topic ft
                    ON ft.elem_id = t.id 
                 WHERE ta.kind_key = 'kCenter'
                   AND ft.kind_key = 'kDefault'                                     
              ORDER BY ta.absindex
                ";                    
    $topics = $gParser->SqlSelect(__LINE__, $command);
    foreach ($topics as $topic)
    {
        $gParser->TplAssign($tpl, ['ID'     => $topic['id'], 
                                   'IMAGE'  => MELBIS_INC_STD_path($topic),
                                   'NAME'   => MELBIS_INC_LANG('kTopic', 'NAME', $topic['id'], $topic['name']),
                                   'LINK'  => $topic['seo_psu']
                                   ]); 
        $gParser->TplParse($tpl, 'ITEM', '.item');  
    }


    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 



?>