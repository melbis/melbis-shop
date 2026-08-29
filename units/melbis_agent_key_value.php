<?php
/***************************************************************************************************
 * @version 6.5.1.415 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * ValueNamed     - The rows, with sys_key
 * System         - Refuses a platform value
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_KEY_VALUE;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    return TABLE\Read('key', ['key_value']);
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'key_value', $mParam);
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $ids = $mParam['id'];

    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    // The text changes, name stays
    if ( isset($fields['key_name']) )
    {
        foreach ( ValueNamed($ids) as $was )
        {
            if ( $was['sys_key'] > 0 ) return System($was, 'renamed');
        }
    }

    return TABLE\Update($mUserId, 'key_value', $ids, $mParam);
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    $ids = $mParam['id'];

    // A platform value stays
    foreach ( ValueNamed($ids) as $was )
    {
        if ( $was['sys_key'] > 0 ) return System($was, 'removed');
    }

    return TABLE\Remove($mUserId, 'key_value', $ids, $mParam);
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    // The values of one code
    $scope['key_code'] = $mParam['key_code'];

    return TABLE\Pos($mUserId, 'key_value', $scope, $mParam);
}


/**
 * Function ValueNamed
 **/
function ValueNamed($mIds)
{
    // The rows, sys_key with them
    $list = implode(',', $mIds);

    $command = "SELECT id, key_code, key_name, sys_key
                  FROM {DBNICK}_key_value
                 WHERE id IN ( $list )
               ";

    return MELBIS()->SqlSelect(__LINE__, $command);
}


/**
 * Function System
 **/
function System($mWas, $mWord)
{
    // Points at the place
    $path = SYS\TreePathFind('key', 'code', $mWas['key_code']);
    $where = ( $path == '' ) ? '' : ' In the program it is '.$path.'.';

    return [
        'result'  => false,
        'message' => 'The value ['.$mWas['key_name'].'] is the shop\'s own'
        ];
}


?>
