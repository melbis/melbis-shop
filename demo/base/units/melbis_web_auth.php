<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/
       
 
/** 
 * Function MELBIS_WEB_AUTH
 **/
function MELBIS_WEB_AUTH($mVars)
{ 
    global $gParser;
    
    return MELBIS_INC_AUTH(__FUNCTION__, $mVars);                    
}  


/** 
 * Function MELBIS_WEB_AUTH_default
 **/
function MELBIS_WEB_AUTH_default($mUserId, $mResultAuth, $mVars)
{ 
    global $gParser;
                       
    // Auth     
    if ( isset($mVars['post']['form_auth']) )
    {  
        return json_encode(array('result' => $mResultAuth));              
    }
    else
    {                                
        // Create 
        $tpl = $gParser->TplCreate();          
        
        // Parse result auth
        $gParser->TplParse($tpl, 'RESULT', 'result_'.$mResultAuth);           
           
        // Scripts
        $gParser->TplParse($tpl, 'SCRIPTS', 'scripts');                                                     
               
        // Final: return content
        $gParser->TplParse($tpl, 'MAIN', 'main');

        return $gParser->TplFree($tpl, 'MAIN');
    }                           
}     




?>