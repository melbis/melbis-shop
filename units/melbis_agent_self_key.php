<?php
/***************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_SELF_KEY;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    return TABLE\Read('self_key', ['self_key_right', 'self_key_value']);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\TreeAdd($mUserId, 'self_key', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'self_key', $mParam['id'], $mParam);
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    return TABLE\TreeMove($mUserId, 'self_key', $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\TreeRemove($mUserId, 'self_key', $mParam['id'], $mParam);
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'self_key_value', $mParam);
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'self_key_value', $mParam['id'], $mParam);
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'self_key_value', $mParam['id'], $mParam);
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    // The settings of one group
    $scope['key_id'] = $mParam['key_id'];

    return TABLE\Pos($mUserId, 'self_key_value', $scope, $mParam);
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    // A right names one owner
    if ( !isset($mParam['user_id']) && !isset($mParam['group_id']) )
    {
        return [
            'result'  => false,
            'message' => 'Name user_id or group_id'
            ];
    }

    return TABLE\AddBlock($mUserId, 'self_key_right', 'key_id', $mParam['key_id'], $mParam);
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'self_key_right', $mParam['id'], $mParam);
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'self_key_right', $mParam['id'], $mParam);
}

?>
