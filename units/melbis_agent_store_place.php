<?php
/***************************************************************************************************
 * @version 6.5.0.411 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * LinkAllowed - The links of this person
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_PLACE;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_TABLE as TABLE;
use MELBIS_INC_AGENT_STORE as STORE;

// What this tool knows
const SCHEMA = [

    'store' => [
        'id'            => 'PK.int',
        'provider_id'   => 'int',
        'brand_id'      => 'int',
        'code_shop'     => 'str',
        'code_prov'     => 'str',
        'code_made'     => 'str',
        'name'          => 'str',
        'no_visible'    => 'bool',
        'status_key'    => 'str',
        'kind_key'      => 'str',
        'state_key'     => 'str',
        'clann'         => 'int',
        'clann_title'   => 'str',
        'clann_root'    => 'bool',
        'relate_id'     => 'int',
        'rating'        => 'int',
        'how'           => 'int',
        'price'         => 'float',
        'price_curr_id' => 'int',
        'award_cnt'     => 'int',
        'award_avg'     => 'float',
        'create_time'   => 'datetime',
        'update_time'   => 'datetime',
        'exist_time'    => 'datetime'
        ],

    'topic_store' => [
        'id'       => 'int',
        'topic_id' => 'int',
        'store_id' => 'FK.int',
        'pos'      => 'int'
        ]
    ];


/**
 * Function CmdTopic
 **/
function CmdTopic($mUserId, $mParam)
{
    return STORE\Topic($mUserId, 'place', SCHEMA, $mParam);
}


/**
 * Function CmdQuery
 **/
function CmdQuery($mUserId, $mParam)
{
    return STORE\Query($mUserId, 'place', SCHEMA, $mParam);
}


/**
 * Function CmdLink
 **/
function CmdLink($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !SYS\RightOne('topic', $mUserId, 'place', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours'
            ];
    }

    $tables = ['{DBNICK}_topic_store'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    STORE\LinkTopic($topic_id, $mParam['store_id']);

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'message' => 'The goods hang in the section now'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $said = LinkAllowed($mUserId, $mParam['id']);
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Name a section, or a place'
            ];
    }

    if ( isset($fields['topic_id']) && !SYS\RightOne('topic', $mUserId, 'place', $fields['topic_id']) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$mParam['topic_id'].'] is not yours'
            ];
    }

    $tables = ['{DBNICK}_topic_store'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // A moved link goes last
    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        if ( !isset($row['pos']) ) $row['pos'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic_store', $row, 'id');
    }

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'message' => 'The links are changed'
        ];
}


/**
 * Function CmdUnlink
 **/
function CmdUnlink($mUserId, $mParam)
{
    $said = LinkAllowed($mUserId, $mParam['id']);
    if ( !$said['result'] ) return $said;

    return TABLE\Remove($mUserId, 'topic_store', $said['ids'], $mParam);
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !SYS\RightOne('topic', $mUserId, 'place', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours'
            ];
    }

    // The links of one section
    $scope['topic_id'] = $topic_id;

    return TABLE\Pos($mUserId, 'topic_store', $scope, $mParam);
}


/**
 * Function LinkAllowed
 **/
function LinkAllowed($mUserId, $mIds)
{
    $allow = SYS\RightTable('topic', $mUserId, 'place');
    $list = implode(',', $mIds);

    $command = "SELECT ts.id
                  FROM {DBNICK}_topic_store ts
                  JOIN $allow a
                    ON a.id = ts.topic_id
                 WHERE ts.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);

    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The links ['.$said.'] are not yours'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids
        ];
}



?>
