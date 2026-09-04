<?php
/***************************************************************************************************
 * @version 6.5.1.425 @ 2026-09-04
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_BROWSE;

// Libraries
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
        'price'         => 'float',
        'price_curr_id' => 'int',
        'in_xml'        => 'bool',
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
    return STORE\Topic($mUserId, 'browse', SCHEMA, $mParam);
}


/**
 * Function CmdQuery
 **/
function CmdQuery($mUserId, $mParam)
{
    return STORE\Query($mUserId, 'browse', SCHEMA, $mParam);
}



?>
