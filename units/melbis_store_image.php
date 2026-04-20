<?php
/***************************************************************************************************
 * @version 6.5.0.242 @ 2026-04-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_IMAGE
 **/
function MELBIS_STORE_IMAGE($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();   
    
    // Lang tags    
    MELBIS_INC_LANG_tags($tpl, __FUNCTION__);      
       
    // Get image    
    $command = "SELECT *
                  FROM {DBNICK}_files_store
                 WHERE elem_id = '$mVars[id]' 
                   AND kind_key = '$mVars[key]'
              ORDER BY pos
                 LIMIT 1
                ";                    
    $hash = $gParser->SqlSelectToArray(__LINE__, $command);                 
    
    $gParser->TplAssign($tpl, 'IMAGES', '');
    if ( isset($hash['id']) )
    {    
        // Additional
        $command = "SELECT *
                      FROM {DBNICK}_files_store
                     WHERE elem_id = '$mVars[id]' 
                       AND kind_key <> '$mVars[key]'
                  ORDER BY pos
                ";                    
        $imgs = $gParser->SqlSelect(__LINE__, $command);   
        foreach ($imgs as $img) 
        {
            $gParser->TplAssign($tpl, 'FILE', MELBIS_INC_STD_path($img));
            $gParser->TplParse($tpl, 'IMAGES', '.img');                              
        }                                                           
                                                           
        // Base
        $gParser->TplAssign($tpl, 'FILE', MELBIS_INC_STD_path($hash));
        $gParser->TplParse($tpl, 'IMAGE', 'image');                 
    }
    else
    {
        $gParser->TplParse($tpl, 'IMAGE', 'no_image'); 
    }

    return $gParser->TplFree($tpl, 'IMAGE');
} 



?>