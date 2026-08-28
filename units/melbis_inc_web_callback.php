<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Define     - Declares the template callbacks
 * TopicLink  - The address of a section
 * StoreLink  - The address of a goods
 * StatusName - The status of a goods
 *
 **************************************************************************************************/

namespace MELBIS_INC_WEB_CALLBACK;

/** 
 * Function Define
 **/
function Define()
{        
    // Register 
    MELBIS()->DefineCallback('TopicLink');
    MELBIS()->DefineCallback('StoreLink');
    MELBIS()->DefineCallback('StatusName');
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
    // A key, worded in settings
    return MELBIS()->SysKeyValues('STORE_STATUS_KEY')[$mVars[0]] ?? '';
} 



?>