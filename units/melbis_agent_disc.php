<?php
/***************************************************************************************************
 * @version 6.5.1.425 @ 2026-09-04
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_DISC;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('disc_group');
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    return TABLE\Read('disc_group', ['disc_rate']);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'disc_group', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'disc_group', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'disc_group', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'disc_group', [], $mParam);
}


/**
 * Function CmdRateAdd
 **/
function CmdRateAdd($mUserId, $mParam)
{
    // The days the program opens
    $now = MELBIS()->DateTime();
    $fields = $mParam;
    if ( !isset($fields['begin_time']) ) $fields['begin_time'] = $now;
    if ( !isset($fields['end_time']) ) $fields['end_time'] = date('Y-m-d H:i:s',
                                                                 strtotime($now.' +10 years'));

    return TABLE\AddBlock($mUserId, 'disc_rate', 'group_id', $mParam['group_id'], $fields);
}


/**
 * Function CmdRateUpdate
 **/
function CmdRateUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'disc_rate', $mParam['id'], $mParam);
}


/**
 * Function CmdRateRemove
 **/
function CmdRateRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'disc_rate', $mParam['id'], $mParam);
}

?>
