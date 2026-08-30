<?php
/***************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_DESCR;

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
        'meas'          => 'str',
        'name'          => 'str',
        'intro'         => 'str',
        'descr'         => 'str',
        'review'        => 'str',
        'no_visible'    => 'bool',
        'status_key'    => 'str',
        'kind_key'      => 'str',
        'state_key'     => 'str',
        'clann'         => 'int',
        'clann_title'   => 'str',
        'clann_descr'   => 'str',
        'clann_root'    => 'bool',
        'relate_id'     => 'int',
        'price'         => 'float',
        'price_curr_id' => 'int',
        'seo_psu'       => 'str',
        'seo_title'     => 'str',
        'templ_key'     => 'str',
        'option_code'   => 'str',
        'create_time'   => 'datetime',
        'update_time'   => 'datetime',
        'edit_time'     => 'datetime',
        'exist_time'    => 'datetime'
        ],

    'topic_store' => [
        'id'       => 'int',
        'topic_id' => 'int',
        'store_id' => 'FK.int',
        'pos'      => 'int'
        ],

    'store_info' => [
        'id'        => 'int',
        'store_id'  => 'FK.int',
        'info_id'   => 'int',
        'value_id'  => 'int',
        'value_dec' => 'float',
        'value_txt' => 'str'
        ],

    'store_set' => [
        'id'       => 'int',
        'store_id' => 'FK.int',
        'obj_key'  => 'str',
        'kind_key' => 'str',
        'elem_id'  => 'int',
        'params'   => 'str',
        'comment'  => 'str',
        'pos'      => 'int'
        ]
    ];


/**
 * Function CmdTopic
 **/
function CmdTopic($mUserId, $mParam)
{
    return STORE\Topic($mUserId, 'descr', SCHEMA, $mParam);
}


/**
 * Function CmdQuery
 **/
function CmdQuery($mUserId, $mParam)
{
    return STORE\Query($mUserId, 'descr', SCHEMA, $mParam);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !SYS\RightOne('topic', $mUserId, 'descr', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours'
            ];
    }

    // Every field is a column
    $fields = $mParam;
    unset($fields['topic_id']);

    $tables = ['{DBNICK}_store', '{DBNICK}_topic_store'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $row = STORE\DefaultFill($fields);
    $row['id'] = MELBIS()->SqlGenId('store');
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_store', $row);

    // Hung in a section
    STORE\LinkTopic($topic_id, [$row['id']]);

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The goods is in the section'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $said = STORE\Allowed($mUserId, $mParam['id'], 'descr');
    if ( !$said['result'] ) return $said;

    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    // The goods stamps its days
    $now = MELBIS()->DateTime('now');
    if ( !isset($fields['update_time']) ) $fields['update_time'] = $now;
    if ( !isset($fields['edit_time']) ) $fields['edit_time'] = $now;

    return TABLE\Update($mUserId, 'store', $said['ids'], $fields);
}


/**
 * Function CmdInfoAdd
 **/
function CmdInfoAdd($mUserId, $mParam)
{
    $said = STORE\Allowed($mUserId, $mParam['store_id'], 'descr');
    if ( !$said['result'] ) return $said;

    // The base numbers this table
    return TABLE\AddBlock($mUserId, 'store_info', 'store_id', $said['ids'], $mParam, false);
}


/**
 * Function CmdInfoUpdate
 **/
function CmdInfoUpdate($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_info', $mParam['id'], 'descr');
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    // The goods it moves to
    if ( isset($mParam['store_id']) )
    {
        $said = STORE\Allowed($mUserId, [$mParam['store_id']], 'descr');
        if ( !$said['result'] ) return $said;
    }

    return TABLE\Update($mUserId, 'store_info', $ids, $mParam);
}


/**
 * Function CmdInfoRemove
 **/
function CmdInfoRemove($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_info', $mParam['id'], 'descr');
    if ( !$said['result'] ) return $said;

    return TABLE\Remove($mUserId, 'store_info', $said['ids'], $mParam);
}


/**
 * Function CmdSetAdd
 **/
function CmdSetAdd($mUserId, $mParam)
{
    $said = STORE\Allowed($mUserId, $mParam['store_id'], 'descr');
    if ( !$said['result'] ) return $said;

    // The base numbers this table
    return TABLE\AddBlock($mUserId, 'store_set', 'store_id', $said['ids'], $mParam, false);
}


/**
 * Function CmdSetUpdate
 **/
function CmdSetUpdate($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_set', $mParam['id'], 'descr');
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    // The goods it moves to
    if ( isset($mParam['store_id']) )
    {
        $said = STORE\Allowed($mUserId, [$mParam['store_id']], 'descr');
        if ( !$said['result'] ) return $said;
    }

    return TABLE\Update($mUserId, 'store_set', $ids, $mParam);
}


/**
 * Function CmdSetRemove
 **/
function CmdSetRemove($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_set', $mParam['id'], 'descr');
    if ( !$said['result'] ) return $said;

    return TABLE\Remove($mUserId, 'store_set', $said['ids'], $mParam);
}


/**
 * Function CmdClannNew
 **/
function CmdClannNew($mUserId, $mParam)
{
    $said = STORE\Allowed($mUserId, $mParam['id'], 'descr');
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    $root = $mParam['root'] ?? 0;
    if ( $root == 0 ) $root = reset($ids);

    $tables = ['{DBNICK}_store'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $clann = MELBIS()->SqlGenId('store_clann');
    $now = MELBIS()->DateTime('now');

    foreach ( $ids as $id )
    {
        $row = [
            'id'          => $id,
            'clann'       => $clann,
            'clann_root'  => ( $id == $root ) ? 1 : 0,
            'update_time' => $now,
            'edit_time'   => $now
            ];
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_store', $row, 'id');
    }

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'clann'   => $clann,
        'message' => count($ids).' goods in the clan, ['.$root.'] leads'
        ];
}



?>
