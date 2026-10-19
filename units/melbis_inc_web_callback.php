<?php
/***************************************************************************************************
 * @version 6.5.1.510 @ 2026-10-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * TopicLink  - The address of a section
 * StoreLink  - The address of a goods
 * StatusName - The status of a goods
 *
 **************************************************************************************************/

namespace MELBIS_INC_WEB_CALLBACK;

// Register the template callbacks
MELBIS()->DefineCallback('TopicLink');
MELBIS()->DefineCallback('StoreLink');
MELBIS()->DefineCallback('StatusName');


// ————— Public —————
//==================================================================================================


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
    // A fresh goods shows none
    if ( $mVars[0] == 'kDefault' ) return '';

    // A key, worded in settings
    $status = MELBIS()->SysKeyValues('STORE_STATUS_KEY');
    $name = $status[$mVars[0]] ?? '';

    // The kind hint is staff's
    $cut = strpos($name, ' [');
    if ( $cut !== false ) $name = substr($name, 0, $cut);

    return $name;
} 



?>