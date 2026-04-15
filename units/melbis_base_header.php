<?php
/***************************************************************************************************
 * @version 6.5.0.229 @ 2026-04-15
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_HEADER
 **/
function MELBIS_BASE_HEADER($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();         
    
    // Auto tag    
    MELBIS_INC_LANG_tags($tpl, __FUNCTION__);     
    
    // Make language switcher
    $command = "SELECT *   
                  FROM {DBNICK}_lang                 
              ORDER BY pos
                ";                  
    $langs = $gParser->SqlSelect(__LINE__, $command);  
    foreach ( $langs as $lang )
    {        
        $gParser->TplAssign($tpl, array('NAME'  => htmlspecialchars($lang['name']),
                                        'LINK'  => $lang['skey'] 
                                        ));                        
        $gParser->TplParse($tpl, 'LANG', '.lang');
    }                   
    
    $gParser->TplParse($tpl, 'SCRIPTS', 'scripts');               
      
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 



?>