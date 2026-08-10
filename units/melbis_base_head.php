<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_HEAD
 **/
function MELBIS_BASE_HEAD($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();   
    
    // Main
    return MELBIS()->TplFinal($tpl, 'main');
} 


?>