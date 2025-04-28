<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
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