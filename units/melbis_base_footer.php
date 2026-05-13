<?php
/***************************************************************************************************
 * @version 6.5.0.263 @ 2026-05-13
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
    
    // Main
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>