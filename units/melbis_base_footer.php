<?php
/***************************************************************************************************
 * @version 6.5.0.411 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the page foot
 *
 **************************************************************************************************/

namespace MELBIS_BASE_FOOTER;

/** 
 * Function Main
 **/
function Main($mVars)
{ 
    // Create  
    $tpl = MELBIS()->TplCreate();
    
    // Year
    MELBIS()->TplAssign($tpl, 'YEAR', MELBIS()->DateTime('now', 'Y'));    
    
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>