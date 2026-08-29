<?php
/***************************************************************************************************
 * @version 6.5.0.411 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_PRICE;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_TABLE as TABLE;
use MELBIS_INC_AGENT_STORE as STORE;

// What this tool knows
const SCHEMA = [

    'store' => [
        'id'             => 'PK.int',
        'provider_id'    => 'int',
        'brand_id'       => 'int',
        'code_shop'      => 'str',
        'code_prov'      => 'str',
        'code_made'      => 'str',
        'meas'           => 'str',
        'name'           => 'str',
        'no_visible'     => 'bool',
        'status_key'     => 'str',
        'kind_key'       => 'str',
        'state_key'      => 'str',
        'rating'         => 'int',
        'disc_group_id'  => 'int',
        'tax_group_id'   => 'int',
        'how'            => 'int',
        'pprice'         => 'float',
        'pprice_curr_id' => 'int',
        'rprice'         => 'float',
        'rprice_curr_id' => 'int',
        'price'          => 'float',
        'price_curr_id'  => 'int',
        'price2'         => 'float',
        'price2_curr_id' => 'int',
        'price3'         => 'float',
        'price3_curr_id' => 'int',
        'relate_id'      => 'int',
        'relate_type'    => 'int',
        'relate_proc'    => 'float',
        'proc_price2'    => 'float',
        'proc_price3'    => 'float',
        'min_order'      => 'int',
        'step_order'     => 'int',
        'in_xml'         => 'bool',
        'create_time'    => 'datetime',
        'update_time'    => 'datetime',
        'exist_time'     => 'datetime',
        'award_cnt'      => 'int',
        'award_avg'      => 'float',
        'clann'          => 'int',
        'clann_title'    => 'str',
        'clann_root'     => 'bool'
        ],

    'topic_store' => [
        'id'       => 'int',
        'topic_id' => 'int',
        'store_id' => 'FK.int',
        'pos'      => 'int'
        ],

    'store_param' => [
        'id'            => 'int',
        'store_id'      => 'FK.int',
        'param_id'      => 'int',
        'value_id'      => 'int',
        'value_name'    => 'str',
        'value_set_sum' => 'float',
        'value_curr_id' => 'int',
        'pos'           => 'int'
        ],

    'store_stock' => [
        'id'                => 'int',
        'store_id'          => 'FK.int',
        'provider_stock_id' => 'int',
        'how'               => 'int',
        'params'            => 'str'
        ]
    ];

/**
 * Function CmdTopic
 **/
function CmdTopic($mUserId, $mParam)
{
    return STORE\Topic($mUserId, 'price', SCHEMA, $mParam);
}


/**
 * Function CmdQuery
 **/
function CmdQuery($mUserId, $mParam)
{
    return STORE\Query($mUserId, 'price', SCHEMA, $mParam);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !SYS\RightOne('topic', $mUserId, 'price', $topic_id) )
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
    $said = STORE\Allowed($mUserId, $mParam['id'], 'price');
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

    // The goods stamps its day
    if ( !isset($fields['update_time']) ) $fields['update_time'] = MELBIS()->DateTime('now');

    return TABLE\Update($mUserId, 'store', $said['ids'], $fields);
}


/**
 * Function CmdParamAdd
 **/
function CmdParamAdd($mUserId, $mParam)
{
    $said = STORE\Allowed($mUserId, $mParam['store_id'], 'price');
    if ( !$said['result'] ) return $said;

    return TABLE\AddBlock($mUserId, 'store_param', 'store_id', $said['ids'], $mParam);
}


/**
 * Function CmdParamUpdate
 **/
function CmdParamUpdate($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_param', $mParam['id'], 'price');
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    // The goods it moves to
    if ( isset($mParam['store_id']) )
    {
        $said = STORE\Allowed($mUserId, [$mParam['store_id']], 'price');
        if ( !$said['result'] ) return $said;
    }

    return TABLE\Update($mUserId, 'store_param', $ids, $mParam);
}


/**
 * Function CmdParamRemove
 **/
function CmdParamRemove($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_param', $mParam['id'], 'price');
    if ( !$said['result'] ) return $said;

    return TABLE\Remove($mUserId, 'store_param', $said['ids'], $mParam);
}


/**
 * Function CmdStockAdd
 **/
function CmdStockAdd($mUserId, $mParam)
{
    $said = STORE\Allowed($mUserId, $mParam['store_id'], 'price');
    if ( !$said['result'] ) return $said;

    return TABLE\AddBlock($mUserId, 'store_stock', 'store_id', $said['ids'], $mParam);
}


/**
 * Function CmdStockUpdate
 **/
function CmdStockUpdate($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_stock', $mParam['id'], 'price');
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    // The goods it moves to
    if ( isset($mParam['store_id']) )
    {
        $said = STORE\Allowed($mUserId, [$mParam['store_id']], 'price');
        if ( !$said['result'] ) return $said;
    }

    return TABLE\Update($mUserId, 'store_stock', $ids, $mParam);
}


/**
 * Function CmdStockRemove
 **/
function CmdStockRemove($mUserId, $mParam)
{
    $said = STORE\SlaveAllowed($mUserId, 'store_stock', $mParam['id'], 'price');
    if ( !$said['result'] ) return $said;

    return TABLE\Remove($mUserId, 'store_stock', $said['ids'], $mParam);
}



?>
