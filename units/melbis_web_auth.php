<?php
/***************************************************************************************************
 * @version 6.5.1.432 @ 2026-09-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints the login window
 *
 **************************************************************************************************/

namespace MELBIS_WEB_AUTH;

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