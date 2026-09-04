<?php
/***************************************************************************************************
 * @version 6.5.1.425 @ 2026-09-04
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_PARAM;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('param', ['param_value']);
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    $more = ['param_value', 'param_key_set', 'param_value_key_set'];

    return TABLE\Read('param', $more);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'param', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'param', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'param', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'param', [], $mParam);
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'param_value', $mParam);
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'param_value', $mParam['id'], $mParam);
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'param_value', $mParam['id'], $mParam);
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    // The values of one parameter
    $scope['param_id'] = $mParam['param_id'];

    return TABLE\Pos($mUserId, 'param_value', $scope, $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'param', $mParam['param_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'param', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'param', $mParam['id']);
}


/**
 * Function CmdValueKeyAdd
 **/
function CmdValueKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'param_value', $mParam['param_value_id'], $mParam);
}


/**
 * Function CmdValueKeyUpdate
 **/
function CmdValueKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'param_value', $mParam['id'], $mParam);
}


/**
 * Function CmdValueKeyRemove
 **/
function CmdValueKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'param_value', $mParam['id']);
}

?>
