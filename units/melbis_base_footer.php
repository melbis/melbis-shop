<?php
/***************************************************************************************************
 * @version 6.5.0.230 @ 2026-04-15
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_FOOTER
 **/
function MELBIS_BASE_FOOTER($mVars)
{ 
    global $gParser;
    
    // Create 
    $tpl = $gParser->TplCreate();    
    
    // Lang tags    
    MELBIS_INC_LANG_tags($tpl, __FUNCTION__);                                                                 
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');   
        
    return $gParser->TplFree($tpl, 'MAIN');
} 



?>