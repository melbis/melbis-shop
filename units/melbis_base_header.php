<?php
/***************************************************************************************************
 * @version 6.5.0.277 @ 2026-05-18
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