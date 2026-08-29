<?php
/***************************************************************************************************
 * @version 6.5.1.416 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * PlaceOne       - Weighs a place
 * PlaceFields    - The fields without the place
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_OPTION;

// Libraries
use MELBIS_INC_AGENT_TABLE as TABLE;

// The places options stand in
const PLACE_SET = "advert, brand, info, param, param_value, provider, provider_stock, tax_area,
                   topic, topic_filter, user, user_group";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;
    $where = $place['place'];

    return TABLE\Read($where.'_key', [$where.'_key_value']);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\TreeAdd($mUserId, $place['place'].'_key', PlaceFields($mParam));
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\Update($mUserId, $place['place'].'_key', $mParam['id'], PlaceFields($mParam));
}


/**
 * Function CmdKeyMove
 **/
function CmdKeyMove($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\TreeMove($mUserId, $place['place'].'_key', PlaceFields($mParam));
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\TreeRemove($mUserId, $place['place'].'_key', $mParam['id'], $mParam);
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\Add($mUserId, $place['place'].'_key_value', PlaceFields($mParam));
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\Update($mUserId, $place['place'].'_key_value', $mParam['id'], PlaceFields($mParam));
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    return TABLE\Remove($mUserId, $place['place'].'_key_value', $mParam['id'], $mParam);
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    $place = PlaceOne($mParam['place']);
    if ( !$place['result'] ) return $place;

    // The values of one option
    $scope['key_id'] = $mParam['key_id'];

    return TABLE\Pos($mUserId, $place['place'].'_key_value', $scope, $mParam);
}


/**
 * Function PlaceOne
 **/
function PlaceOne($mPlace)
{
    $place = strtolower(trim((string)$mPlace));

    $allow = explode(',', PLACE_SET);
    $allow = array_map('trim', $allow);

    if ( !in_array($place, $allow) )
    {
        $said = implode(', ', $allow);

        return [
            'result'  => false,
            'message' => 'No options for ['.$mPlace.']; places: '.$list
            ];
    }

    return [
        'result' => true,
        'place'  => $place
        ];
}


/**
 * Function PlaceFields
 **/
function PlaceFields($mParam)
{
    // The place names the tables
    $fields = $mParam;
    unset($fields['place']);

    return $fields;
}


?>
