<?php
/***************************************************************************************************
 * @version 6.5.1.515 @ 2026-10-21
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the page foot
 *
 **************************************************************************************************/

namespace MELBIS_BASE_FOOTER;


// ————— Public —————
//==================================================================================================


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