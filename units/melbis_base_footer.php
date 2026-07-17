<?php
/***************************************************************************************************
 * @version 6.5.0.322 @ 2026-07-17
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_FOOTER
 **/
function MELBIS_BASE_FOOTER($mVars)
{ 
    // Create  
    $tpl = MELBIS()->TplCreate();
    
    // Year
    MELBIS()->TplAssign($tpl, 'YEAR', MELBIS()->DateTime('now', 'Y'));    
    
    // Main
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>