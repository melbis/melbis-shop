<?php
/***************************************************************************************************
 * @version 6.5.0.335 @ 2026-07-23
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