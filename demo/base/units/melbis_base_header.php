<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_HEADER
 **/
function MELBIS_BASE_HEADER($mVars)
{ 
    global $gParser;
    
    // Create 
    $tpl = $gParser->TplCreate();                                                                
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');   
        
    return $gParser->TplFree($tpl, 'MAIN');
} 



?>