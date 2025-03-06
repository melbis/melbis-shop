<?php
/***************************************************************************************************
 * @version 6.3.0
 * @copyright 2019 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_META
 **/
function MELBIS_BASE_META($mVars)
{ 
    global $gParser; 
    
    // Create 
    $tpl = $gParser->TplCreate();                                                                
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');   
        
    return $gParser->TplFree($tpl, 'MAIN');
} 




?>