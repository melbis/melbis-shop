<?php
        
/** 
 * Function MELBIS_WEB_AUTH
 **/
function MELBIS_WEB_AUTH($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();     
        
    // Scripts
    MELBIS()->TplParse($tpl, 'SCRIPTS', 'scripts');                                                     
               
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}     
   



?>