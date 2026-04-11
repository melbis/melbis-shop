<?php
/***************************************************************************************************
 * @version 6.5.0.219 @ 2026-04-12
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_HEAD
 **/
function MELBIS_BASE_HEAD($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();   

    // Lang tags    
    MELBIS_INC_LANG_tags($tpl, __FUNCTION__); 
                                                                
    // Alternate languages
    $command = "SELECT * FROM {DBNICK}_lang ORDER BY pos";
    $langs = $gParser->SqlSelect(__LINE__, $command);        
    foreach ( $langs as $lang )
    {
        $gParser->TplAssign($tpl, 'LANG', $lang['skey']);
        $gParser->TplParse($tpl, 'ALTER', '.alter');
    }    
            
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');

    return $gParser->TplFree($tpl, 'MAIN');
} 




?>