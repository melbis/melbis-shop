<?php
/***************************************************************************************************
 * @version 6.5.0.289 @ 2026-06-15
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_HEADER
 **/
function MELBIS_BASE_HEADER($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();         
        
    // Main
    return MELBIS()->TplFinal($tpl, 'main');
} 



?>