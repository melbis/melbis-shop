<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

namespace MELBIS_INC_WEB_CALLBACK;

/** 
 * Function Define
 * Every entry point that draws these tags calls it at its own top level
 **/
function Define()
{        
    // Register 
    MELBIS()->DefineCallback('TopicLink', TopicLink(...));
    MELBIS()->DefineCallback('StoreLink', StoreLink(...));
    MELBIS()->DefineCallback('StatusName', StatusName(...));
}       

 

/** 
 * Function TopicLink
 **/
function TopicLink($mVars)
{ 
    $link = ( $mVars['kind_key'] == 'kLink' ) ? $mVars['link'] : '/?topic_id='.$mVars['id'];
    
    return $link;
} 

/** 
 * Function StoreLink
 **/
function StoreLink($mVars)
{ 
    return '/?store_id='.$mVars[0];
} 

/** 
 * Function StatusName
 **/
function StatusName($mVars)
{ 
    // The status of a goods is a key - its word lives in the base settings    
    return MELBIS()->SysKeyValues('STORE_STATUS_KEY')[$mVars[0]] ?? '';
} 



?>