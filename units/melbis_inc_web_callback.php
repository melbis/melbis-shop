<?php
/***************************************************************************************************
 * @version 6.5.0.348 @ 2026-07-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
     
/** 
 * Function MELBIS_INC_WEB_CALLBACK
 **/
function MELBIS_INC_WEB_CALLBACK()
{        
    // Register 
    MELBIS()->DefineCallback('page_link', 'MELBIS_INC_WEB_CALLBACK_page_link');
}       

 

/** 
 * Function MELBIS_INC_WEB_CALLBACK_page_link
 **/
function MELBIS_INC_WEB_CALLBACK_page_link($mVars)
{ 
    $link = ( $mVars['kind_key'] == 'kLink' ) ? $mVars['link'] : '/?topic_id='.$mVars['id'];
    
    return $link;
} 



?>