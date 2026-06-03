<?php
/***************************************************************************************************
 * @version 6.5.0.283 @ 2026-06-03
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
         

// Register 
MELBIS()->DefineCallback('page_link', 'MELBIS_INC_CALLBACK_page_link'); 
 

/** 
 * Function MELBIS_INC_CALLBACK_page_link
 **/
function MELBIS_INC_CALLBACK_page_link($mVars)
{ 
    $link = ( $mVars['kind_key'] == 'kLink' ) ? $mVars['link'] : '/?topic_id='.$mVars['id'];
    
    return $link;
} 



?>