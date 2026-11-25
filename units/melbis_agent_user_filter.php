<?php
/***************************************************************************************************
 * @version 6.5.1.550 @ 2026-11-25
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER_FILTER;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;


// ————— Public —————
//==================================================================================================

// Filters
//==================================================================================================


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    return TABLE\Read(['user_filter', 'user_filter_param']);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'user_filter', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'user_filter', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $mParam['apply'] = true;
    return TABLE\Remove($mUserId, 'user_filter', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    // The filters of one place
    $scope = [
        'place' => $mParam['place']
        ];

    return TABLE\Pos($mUserId, 'user_filter', $scope, $mParam);
}


// Value lists
//==================================================================================================


/**
 * Function CmdParamAdd
 **/
function CmdParamAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'user_filter_param', $mParam);
}


/**
 * Function CmdParamUpdate
 **/
function CmdParamUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'user_filter_param', $mParam['id'], $mParam);
}


/**
 * Function CmdParamRemove
 **/
function CmdParamRemove($mUserId, $mParam)
{
    $mParam['apply'] = true;
    return TABLE\Remove($mUserId, 'user_filter_param', $mParam['id'], $mParam);
}


/**
 * Function CmdParamPos
 **/
function CmdParamPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'user_filter_param', [], $mParam);
}


?>
