<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the page top
 *
 **************************************************************************************************/

namespace MELBIS_BASE_HEADER;

/** 
 * Function Main
 **/
function Main($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();         
        
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>