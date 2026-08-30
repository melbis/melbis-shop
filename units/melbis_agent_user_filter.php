<?php
/***************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * PlaceOne       - Weighs a place
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER_FILTER;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;

// The places a filter stands
const PLACE_SET = "0, 1, 2";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    return TABLE\Read('user_filter', ['user_filter_param']);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\Add($mUserId, 'user_filter', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    // Written and weighed when named
    if ( isset($mParam['place']) )
    {
        $place = PlaceOne($mParam['place']);
        if ( !$place['result'] ) return $place;
    }

    return TABLE\Update($mUserId, 'user_filter', $mParam['id'], $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'user_filter', $mParam['id'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    // The filters of one place
    $scope['place'] = $place['place'];

    return TABLE\Pos($mUserId, 'user_filter', $scope, $mParam);
}


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
    return TABLE\Remove($mUserId, 'user_filter_param', $mParam['id'], $mParam);
}


/**
 * Function CmdParamPos
 **/
function CmdParamPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'user_filter_param', [], $mParam);
}


/**
 * Function PlaceOne
 **/
function PlaceOne($mPlace)
{
    $place = trim((string)$mPlace);

    $allow = explode(',', PLACE_SET);
    $allow = array_map('trim', $allow);

    if ( !in_array($place, $allow, true) )
    {
        return [
            'result'  => false,
            'message' => 'The place is 0, 1 or 2'
            ];
    }

    return [
        'result' => true,
        'place'  => (int)$place
        ];
}


?>
