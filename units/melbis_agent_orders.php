<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_ORDERS;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_QUERY as QUERY;
use MELBIS_INC_LOGIC_ORDER as LOGIC_ORDER;
use MELBIS_INC_LOGIC_ORDER_CALC as LOGIC_ORDER_CALC;
use MELBIS_INC_LOGIC_ORDER_EDIT as LOGIC_ORDER_EDIT;

// What this tool knows
const SCHEMA = [

    'orders' => [
        'id'         => 'int',
        'code'       => 'str',
        'version_id' => 'PK.int',
        'date_time'  => 'datetime'
        ],

    'orders_version' => [
        'id'        => 'FK.int',
        'order_id'  => 'int',
        'user_id'   => 'int',
        'client_id' => 'int',
        'date_time' => 'datetime',
        'total_sum' => 'float'
        ],

    'orders_store' => [
        'id'                => 'int',
        'version_id'        => 'FK.int',
        'store_id'          => 'int',
        'store_provider_id' => 'int',
        'store_stock_id'    => 'int',
        'store_brand_id'    => 'int',
        'store_pprice'      => 'float',
        'store_rprice'      => 'float',
        'store_price'       => 'float',
        'store_price2'      => 'float',
        'store_price3'      => 'float',
        'store_how'         => 'int',
        'store_code_shop'   => 'str',
        'store_code_prov'   => 'str',
        'store_code_made'   => 'str',
        'store_meas'        => 'str',
        'store_name'        => 'str',
        'store_kind_key'    => 'str',
        'store_status_key'  => 'str',
        'store_state_key'   => 'str',
        'store_min_order'   => 'int',
        'store_step_order'  => 'int',
        'recalc'            => 'bool',
        'out_price'         => 'float',
        'amount'            => 'int',
        'notice'            => 'str',
        'auto_notice'       => 'str',
        'pos'               => 'int'
        ],

    'orders_option' => [
        'id'               => 'int',
        'version_id'       => 'FK.int',
        'option_id'        => 'int',
        'option_skey'      => 'str',
        'option_name'      => 'str',
        'option_kind_key'  => 'str',
        'option_pos'       => 'int',
        'value_id'         => 'int',
        'value_skey'       => 'str',
        'value_name'       => 'str',
        'value_kind_key'   => 'str',
        'value_modify_sum' => 'float',
        'value_oper_num'   => 'int',
        'value_source_num' => 'int',
        'notice'           => 'str'
        ],

    'orders_store_option' => [
        'id'               => 'int',
        'version_id'       => 'FK.int',
        'order_store_id'   => 'int',
        'option_id'        => 'int',
        'option_skey'      => 'str',
        'option_name'      => 'str',
        'option_kind_key'  => 'str',
        'option_pos'       => 'int',
        'value_id'         => 'int',
        'value_skey'       => 'str',
        'value_name'       => 'str',
        'value_modify_sum' => 'float'
        ],

    'orders_client_field' => [
        'id'             => 'int',
        'version_id'     => 'FK.int',
        'field_id'       => 'int',
        'field_skey'     => 'str',
        'field_name'     => 'str',
        'field_tindex'   => 'int',
        'field_tlevel'   => 'int',
        'field_absindex' => 'int',
        'field_folder'   => 'bool',
        'field_kind_key' => 'str',
        'field_spec_key' => 'str',
        'value_id'       => 'int',
        'value_skey'     => 'str',
        'value_code'     => 'str',
        'value_kind_key' => 'str',
        'value_txt'      => 'str'
        ]
    ];

/**
 * Function CmdQuery
 **/
function CmdQuery($mUserId, $mParam)
{
    // Asked nothing, it signs itself
    $query = $mParam['query'] ?? [];
    if ( count($query) == 0 ) return QUERY\Sign(SCHEMA);

    $said = QUERY\SqlBuild(SCHEMA, $query, 'o');
    if ( !$said['result'] ) return $said;

    $page = QUERY\PageLimit($mParam);
    if ( !$page['result'] ) return $page;

    $param = $said['param'];

    // The keeper meets every order
    $allow = MELBIS()->SysOrderRight($mUserId);
    if ( empty($allow) )
    {
        return [
            'result'  => false,
            'message' => 'No order is yours to read'
            ];
    }

    // One granted value is enough
    if ( $allow !== true )
    {
        $table = SYS\RightTable('order', $mUserId);
        $command = "FROM {DBNICK}_orders o
                   WHERE ".$said['where']."
                     AND EXISTS ( SELECT 1
                                    FROM {DBNICK}_orders_option oo
                                    JOIN $table a
                                      ON a.id = oo.value_id
                                   WHERE oo.version_id = o.version_id )
                     ";
    }
    else
    {
        $command = "FROM {DBNICK}_orders o
                   WHERE ".$said['where']."
                   ";
    }

    // The block of both queries
    $from = $command;

    $found = QUERY\TotalCount($from, $param);

    $limit = $page['limit'];
    $offset = $page['offset'];

    // One row of the page
    $pull = QUERY\PullOrders($from, $limit, $offset, $param);
    $tables = QUERY\PullFull(SCHEMA, $pull);

    $shown = count($tables['orders']);
    $message = $shown.' order(s) of '.$found.' found by '.$said['leaf'].' condition(s)';
    if ( $found > $offset + $shown )
    {
        $message .= '; the rest is asked for by offset '.( $offset + $shown );
    }

    return [
        'result'  => true,
        'found'   => $found,
        'message' => $message,
        'tables'  => $tables
        ];
}


/**
 * Function CmdCreate
 **/
function CmdCreate($mUserId, $mParam)
{
    $version = LOGIC_ORDER\Create();

    return [
        'result'  => true,
        'message' => 'A blank version of a new order',
        'detail'  => $version
        ];
}


/**
 * Function CmdLoad
 **/
function CmdLoad($mUserId, $mParam)
{
    $version = LOGIC_ORDER\Load($mUserId, $mParam['order_id']);

    return [
        'result'  => true,
        'message' => 'The version the order stands at now',
        'detail'  => $version
        ];
}


/**
 * Function CmdGoodsAdd
 **/
function CmdGoodsAdd($mUserId, $mParam)
{
    $version = LOGIC_ORDER\GoodsAdd($mParam['version'], $mParam['store_id'], $mParam['amount'],
                                    $mParam['price_out']);

    return [
        'result'  => true,
        'message' => 'The goods is in the version',
        'detail'  => $version
        ];
}


/**
 * Function CmdGoodsRemove
 **/
function CmdGoodsRemove($mUserId, $mParam)
{
    $version = LOGIC_ORDER\GoodsRemove($mParam['version'], $mParam['store_id']);

    return [
        'result'  => true,
        'message' => 'The goods is out of the version',
        'detail'  => $version
        ];
}


/**
 * Function CmdOptionSet
 **/
function CmdOptionSet($mUserId, $mParam)
{
    $version = LOGIC_ORDER\OptionSet($mParam['version'], $mParam['option'], $mParam['value'],
                                     $mParam['notice'], $mParam['keep']);

    return [
        'result'  => true,
        'message' => 'The option stands at its value',
        'detail'  => $version
        ];
}


/**
 * Function CmdCalc
 **/
function CmdCalc($mUserId, $mParam)
{
    $version = LOGIC_ORDER_CALC\Run($mUserId, $mParam['version']);

    return [
        'result'  => true,
        'message' => 'The version is counted',
        'detail'  => $version
        ];
}


/**
 * Function CmdEdit
 **/
function CmdEdit($mUserId, $mParam)
{
    // The word of the shop
    $detail = LOGIC_ORDER_EDIT\Run($mUserId, $mParam['version']);

    return [
        'result'  => true,
        'message' => 'The version went to the shop',
        'detail'  => $detail
        ];
}


?>
