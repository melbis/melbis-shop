<?php
/***************************************************************************************************
 * @version 6.5.1.470 @ 2026-09-25
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_FRAME;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_STORE as STORE;
use MELBIS_INC_AGENT_QUERY as QUERY;
use MELBIS_INC_AGENT_TABLE as TABLE;

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
        ],

    'files_store' => [
        'id'          => 'int',
        'elem_id'     => 'FK.int',
        'kind_key'    => 'str',
        'file_name'   => 'str',
        'file_size'   => 'int',
        'upload_time' => 'datetime',
        'upload_ok'   => 'int',
        'real_name'   => 'str',
        'parent_id'   => 'int',
        'format_xml'  => 'str',
        'pos'         => 'int'
        ],

    'info_value' => [
        'id'       => 'int',
        'skey'     => 'str',
        'info_id'  => 'int',
        'name'     => 'str',
        'descr'    => 'str',
        'kind_key' => 'str',
        'params'   => 'str',
        'seo_code' => 'str',
        'pos'      => 'int'
        ],

    'files_info_value' => [
        'id'          => 'int',
        'elem_id'     => 'int',
        'kind_key'    => 'str',
        'file_name'   => 'str',
        'file_size'   => 'int',
        'upload_time' => 'datetime',
        'upload_ok'   => 'int',
        'real_name'   => 'str',
        'parent_id'   => 'int',
        'format_xml'  => 'str',
        'pos'         => 'int'
        ]
    ];


/**
 * Function CmdLock
 **/
function CmdLock($mUserId, $mParam)
{
    $tables = TABLE\Names(SCHEMA, 'u_');
    $taken = MELBIS()->SqlTableLock(__LINE__, $tables, $mUserId);
    if ( !$taken )
    {
        return [
            'result'  => false,
            'message' => 'The personal workspace is held - by the window of this person, which lets it go with [MShop:FUserFrame:BSent], or already by this tool, which gives it back with CmdUnlock'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The personal workspace is held for the tool: the Descriptions window of this person opens it read-only until CmdUnlock'
        ];
}


/**
 * Function CmdUnlock
 **/
function CmdUnlock($mUserId, $mParam)
{
    $tables = TABLE\Names(SCHEMA, 'u_');
    $gone = MELBIS()->SqlTableUnlock(__LINE__, $tables, $mUserId);

    $message = 'The tool held nothing - a workspace the Descriptions window holds stays with it';
    if ( $gone > 0 ) $message = 'The personal workspace is given back: the Descriptions window takes it when opened anew';

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdLoad
 **/
function CmdLoad($mUserId, $mParam)
{
    $tables = ['u_store', 'u_topic_store', 'u_store_info', 'u_store_set', 'u_files_store', 'u_info_value', 'u_files_info_value'];

    return TABLE\Read($tables, $mUserId);
}


/**
 * Function CmdTakeTopic
 **/
function CmdTakeTopic($mUserId, $mParam)
{
    $said = STORE\Topic($mUserId, 'descr', SCHEMA, $mParam);

    return Take($mUserId, $said);
}


/**
 * Function CmdTakeQuery
 **/
function CmdTakeQuery($mUserId, $mParam)
{
    $said = STORE\Query($mUserId, 'descr', SCHEMA, $mParam);

    return Take($mUserId, $said);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $topic_id = $mParam['topic_id'];
    if ( !SYS\RightOne('topic', $mUserId, 'descr', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to describe'
            ];
    }

    // Every field is a column
    $fields = $mParam;
    unset($fields['topic_id']);

    // Born as the program births
    $row = STORE\DefaultFill($fields);
    $id = MELBIS()->SqlGenId('u_store', $mUserId);
    $row['id'] = $id;
    $row['user_id'] = $mUserId;
    $row['was_update'] = 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_u_store', $row);

    // Hung in its section
    $link_id = MELBIS()->SqlGenId('u_topic_store', $mUserId);
    $link = [
        'id'       => $link_id,
        'user_id'  => $mUserId,
        'topic_id' => $topic_id,
        'store_id' => $id,
        'pos'      => $link_id
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_u_topic_store', $link);

    return [
        'result'  => true,
        'message' => 'The goods is added to the personal workspace',
        'detail'  => [
            'id' => $id
            ]
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'store', $ids);
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

    // Marked as the window marks
    $fields['was_update'] = 1;
    if ( !isset($fields['update_time']) ) $fields['update_time'] = MELBIS()->DateTime('now');
    $fields['user_id'] = $mUserId;

    $key = ['user_id', 'id'];
    foreach ( $ids as $id )
    {
        $fields['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_u_store', $fields, $key);
    }

    $count = count($ids);

    return [
        'result'  => true,
        'message' => $count.' goods changed in the personal workspace'
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'store', $ids);
    if ( !$said['result'] ) return $said;

    // Changes go with the goods
    $changed = [];
    foreach ( $said['rows'] as $row )
    {
        if ( $row['was_update'] ) $changed[] = $row['id'];
    }

    if ( count($changed) > 0 && !$mParam['apply'] )
    {
        $named = implode(', ', $changed);

        return [
            'result'  => false,
            'message' => 'The goods ['.$named.'] carry changes not published yet, and they go with the goods. Say apply'
            ];
    }

    Drop($mUserId, $ids);

    $count = count($ids);

    return [
        'result'  => true,
        'message' => $count.' goods out of the personal workspace; the catalogue keeps them as they are'
        ];
}


/**
 * Function CmdInfoAdd
 **/
function CmdInfoAdd($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['store_id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'store', $ids);
    if ( !$said['result'] ) return $said;

    // Every field is a column
    $fields = $mParam;
    unset($fields['store_id']);

    $picked = ValuePick($mUserId, $mParam['info_id'], $fields);
    if ( !$picked['result'] ) return $picked;

    return SlaveAdd($mUserId, 'store_info', $ids, $picked['fields']);
}


/**
 * Function CmdInfoUpdate
 **/
function CmdInfoUpdate($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);

    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    // The word needs its list
    $info_id = $fields['info_id'] ?? 0;
    if ( isset($fields['value']) && $info_id == 0 )
    {
        $said = Allowed($mUserId, 'store_info', $ids);
        if ( !$said['result'] ) return $said;

        $column = array_column($said['rows'], 'info_id');
        $infos = array_unique($column);
        if ( count($infos) > 1 )
        {
            return [
                'result'  => false,
                'message' => 'The rows stand on several characteristics - info_id names the one the word belongs to'
                ];
        }
        $info_id = reset($infos);
    }

    $picked = ValuePick($mUserId, $info_id, $fields);
    if ( !$picked['result'] ) return $picked;

    return SlaveUpdate($mUserId, 'store_info', $ids, $picked['fields']);
}


/**
 * Function CmdInfoRemove
 **/
function CmdInfoRemove($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);

    return SlaveRemove($mUserId, 'store_info', $ids);
}


/**
 * Function CmdValueTake
 **/
function CmdValueTake($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['info_id']);
    $ids = array_values($unique);
    TakeValues($mUserId, $ids);

    $list = implode(',', $ids);
    $command = "SELECT *
                  FROM {DBNICK}_u_info_value
                 WHERE user_id = :USER_ID
                   AND info_id IN ( $list )
              ORDER BY info_id, pos";
    $param_user = [
        'user_id' => $mUserId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_user);

    $count = count($rows);

    return [
        'result'  => true,
        'message' => $count.' value(s) of the lists named stand in the personal workspace',
        'tables'  => [
            'u_info_value' => $rows
            ]
        ];
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $info_id = $mParam['info_id'];
    ValueList($mUserId, $info_id);

    // One word once a list
    $found = ValueFind($mUserId, $info_id, $mParam['name']);
    if ( count($found) > 0 )
    {
        return [
            'result'  => false,
            'message' => 'The list already has ['.$found['name'].'] as value ['.$found['id'].'] - CmdValueUpdate changes it'
            ];
    }

    // Every field is a column
    $fields = $mParam;
    unset($fields['info_id']);

    $said = ValueAdd($mUserId, $info_id, $fields);
    if ( !$said['result'] ) return $said;

    return [
        'result'  => true,
        'message' => 'The value is added to the list in the personal workspace',
        'detail'  => [
            'id' => $said['id']
            ]
        ];
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'info_value', $ids);
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

    // The lists must be yours
    $column = array_column($said['rows'], 'info_id');
    $infos = array_unique($column);
    foreach ( $infos as $info_id )
    {
        if ( !SYS\RightOne('info', $mUserId, 'value', $info_id) )
        {
            return [
                'result'  => false,
                'message' => 'The values of the characteristic ['.$info_id.'] are not yours to change'
                ];
        }
    }

    // One word once a list
    if ( isset($fields['name']) )
    {
        if ( count($ids) > 1 )
        {
            return [
                'result'  => false,
                'message' => 'A name goes on one value at a time'
                ];
        }

        $fields['name'] = trim($fields['name']);
        $row = $said['rows'][0];
        $found = ValueFind($mUserId, $row['info_id'], $fields['name']);
        if ( count($found) > 0 && $found['id'] != $row['id'] )
        {
            return [
                'result'  => false,
                'message' => 'The list already has ['.$found['name'].'] as value ['.$found['id'].']'
                ];
        }
    }

    // Marked as the window marks
    $fields['was_update'] = 1;
    $fields['user_id'] = $mUserId;
    $key = ['user_id', 'id'];
    foreach ( $ids as $id )
    {
        $fields['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_u_info_value', $fields, $key);
    }

    $count = count($ids);

    return [
        'result'  => true,
        'message' => $count.' value(s) changed in the personal workspace'
        ];
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'info_value', $ids);
    if ( !$said['result'] ) return $said;

    $list = implode(',', $ids);
    $param_user = [
        'user_id' => $mUserId
        ];

    // The goods lose it too
    $command = "SELECT store_id
                  FROM {DBNICK}_u_store_info
                 WHERE user_id = :USER_ID
                   AND value_id IN ( $list )";
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_user);
    $goods = array_column($rows, 'store_id');

    $command = "DELETE FROM {DBNICK}_u_store_info
                      WHERE user_id = :USER_ID
                        AND value_id IN ( $list )";
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);

    $command = "DELETE FROM {DBNICK}_u_files_info_value
                      WHERE user_id = :USER_ID
                        AND elem_id IN ( $list )";
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);

    $command = "DELETE FROM {DBNICK}_u_info_value
                      WHERE user_id = :USER_ID
                        AND id IN ( $list )";
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);

    if ( count($goods) > 0 ) Changed($mUserId, $goods);

    $count = count($ids);

    return [
        'result'  => true,
        'message' => $count.' value(s) out of the personal workspace, with the rows of goods pointing at them; the store keeps its own'
        ];
}


/**
 * Function CmdSetAdd
 **/
function CmdSetAdd($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['store_id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'store', $ids);
    if ( !$said['result'] ) return $said;

    // Every field is a column
    $fields = $mParam;
    unset($fields['store_id']);

    return SlaveAdd($mUserId, 'store_set', $ids, $fields);
}


/**
 * Function CmdSetUpdate
 **/
function CmdSetUpdate($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);

    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    return SlaveUpdate($mUserId, 'store_set', $ids, $fields);
}


/**
 * Function CmdSetRemove
 **/
function CmdSetRemove($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);

    return SlaveRemove($mUserId, 'store_set', $ids);
}


/**
 * Function CmdClannNew
 **/
function CmdClannNew($mUserId, $mParam)
{
    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    $unique = array_unique($mParam['id']);
    $ids = array_values($unique);
    $said = Allowed($mUserId, 'store', $ids);
    if ( !$said['result'] ) return $said;

    // The first named leads
    $root = $mParam['root'] ?? 0;
    if ( $root == 0 ) $root = $ids[0];

    $clann = MELBIS()->SqlGenId('store_clann');
    $now = MELBIS()->DateTime('now');

    $key = ['user_id', 'id'];
    foreach ( $ids as $id )
    {
        $row = [
            'user_id'     => $mUserId,
            'id'          => $id,
            'clann'       => $clann,
            'clann_root'  => ( $id == $root ) ? 1 : 0,
            'was_update'  => 1,
            'update_time' => $now
            ];
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_u_store', $row, $key);
    }

    $count = count($ids);

    return [
        'result'  => true,
        'message' => $count.' goods in the clan, ['.$root.'] leads',
        'detail'  => [
            'clann' => $clann
            ]
        ];
}


/**
 * Function Held
 **/
function Held($mUserId)
{
    // The tool's own hold
    $tables = TABLE\Names(SCHEMA, 'u_');
    $held = MELBIS()->SqlTableHeld(__LINE__, $tables, $mUserId);
    if ( !$held )
    {
        return [
            'result'  => false,
            'message' => 'The tool does not hold the personal workspace - CmdLock takes it first'
            ];
    }

    return [
        'result' => true
        ];
}


/**
 * Function Allowed
 **/
function Allowed($mUserId, $mTable, $mIds)
{
    $list = implode(',', $mIds);
    $command = "SELECT *
                  FROM {DBNICK}_u_$mTable
                 WHERE user_id = :USER_ID
                   AND id IN ( $list )";
    $param_user = [
        'user_id' => $mUserId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_user);
    $there = array_column($rows, 'id');
    $lost = array_diff($mIds, $there);

    if ( count($lost) > 0 )
    {
        $named = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$named.'] in u_'.$mTable.' of the personal workspace - CmdLoad names them'
            ];
    }

    return [
        'result' => true,
        'rows'   => $rows
        ];
}


/**
 * Function Changed
 **/
function Changed($mUserId, $mIds)
{
    // Marked as the window marks
    $list = implode(',', $mIds);
    $command = "UPDATE {DBNICK}_u_store
                   SET was_update = 1
                 WHERE user_id = :USER_ID
                   AND id IN ( $list )";
    $param_user = [
        'user_id' => $mUserId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);
}


/**
 * Function ValuePick
 **/
function ValuePick($mUserId, $mInfoId, $mFields)
{
    // The word is no column
    $fields = $mFields;
    $word = $fields['value'] ?? null;
    $add = $fields['value_add'] ?? false;
    unset($fields['value'], $fields['value_add']);

    // A copy of the workspace
    if ( isset($fields['value_id']) )
    {
        $named = [$fields['value_id']];
        $said = Allowed($mUserId, 'info_value', $named);
        if ( !$said['result'] ) return $said;

        $own = $said['rows'][0]['info_id'];
        if ( $mInfoId > 0 && $own != $mInfoId )
        {
            return [
                'result'  => false,
                'message' => 'The value ['.$fields['value_id'].'] stands in the list of the characteristic ['.$own.'], not ['.$mInfoId.']'
                ];
        }
    }

    if ( !is_null($word) )
    {
        $said = ValueWord($mUserId, $mInfoId, $word, $add);
        if ( !$said['result'] ) return $said;

        $fields['value_id'] = $said['id'];
    }

    return [
        'result' => true,
        'fields' => $fields
        ];
}


/**
 * Function ValueWord
 **/
function ValueWord($mUserId, $mInfoId, $mWord, $mAdd)
{
    ValueList($mUserId, $mInfoId);

    $found = ValueFind($mUserId, $mInfoId, $mWord);
    if ( count($found) > 0 )
    {
        return [
            'result' => true,
            'id'     => $found['id']
            ];
    }

    if ( !$mAdd )
    {
        return [
            'result'  => false,
            'message' => 'No value ['.$mWord.'] in the list of the characteristic ['.$mInfoId.'] - value_add adds it'
            ];
    }

    $fields = [
        'name' => $mWord
        ];

    return ValueAdd($mUserId, $mInfoId, $fields);
}


/**
 * Function ValueList
 **/
function ValueList($mUserId, $mInfoId)
{
    // The whole list, once
    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_u_info_value
                 WHERE user_id = :USER_ID
                   AND info_id = :INFO_ID";
    $param_list = [
        'user_id' => $mUserId,
        'info_id' => $mInfoId
        ];
    $count = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_list);
    if ( $count > 0 ) return;

    $info_ids = [$mInfoId];
    TakeValues($mUserId, $info_ids);
}


/**
 * Function ValueFind
 **/
function ValueFind($mUserId, $mInfoId, $mWord)
{
    $command = "SELECT id, name
                  FROM {DBNICK}_u_info_value
                 WHERE user_id = :USER_ID
                   AND info_id = :INFO_ID";
    $param_list = [
        'user_id' => $mUserId,
        'info_id' => $mInfoId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_list);

    // Case aside, nothing else
    $trimmed = trim($mWord);
    $want = mb_strtolower($trimmed);
    foreach ( $rows as $row )
    {
        $trimmed = trim($row['name']);
        $name = mb_strtolower($trimmed);
        if ( $name == $want ) return $row;
    }

    return [];
}


/**
 * Function ValueAdd
 **/
function ValueAdd($mUserId, $mInfoId, $mFields)
{
    if ( !SYS\RightOne('info', $mUserId, 'value', $mInfoId) )
    {
        return [
            'result'  => false,
            'message' => 'The values of the characteristic ['.$mInfoId.'] are not yours to add'
            ];
    }

    $param_list = [
        'user_id' => $mUserId,
        'info_id' => $mInfoId
        ];

    // A list takes the end
    $row = $mFields;
    if ( !isset($row['pos']) )
    {
        $command = "SELECT MAX(pos)
                      FROM {DBNICK}_u_info_value
                     WHERE user_id = :USER_ID
                       AND info_id = :INFO_ID";
        $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_list);
        $row['pos'] = $last + 1;
    }

    // Born in the workspace, marked
    $id = MELBIS()->SqlGenId('u_info_value', $mUserId);
    $row['id'] = $id;
    $row['user_id'] = $mUserId;
    $row['info_id'] = $mInfoId;
    $row['was_update'] = 1;
    $row['name'] = trim($row['name']);
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_u_info_value', $row);

    return [
        'result' => true,
        'id'     => $id
        ];
}


/**
 * Function Drop
 **/
function Drop($mUserId, $mIds)
{
    $list = implode(',', $mIds);
    $param_user = [
        'user_id' => $mUserId
        ];

    // What hangs on it first
    $names = array_keys(SCHEMA);
    foreach ( $names as $table )
    {
        $tie = QUERY\Tie(SCHEMA, $table);
        if ( count($tie) == 0 ) continue;

        $key = $tie[0];
        $command = "DELETE FROM {DBNICK}_u_$table
                          WHERE user_id = :USER_ID
                            AND $key IN ( $list )";
        MELBIS()->SqlQuery(__LINE__, $command, $param_user);
    }

    $command = "DELETE FROM {DBNICK}_u_store
                      WHERE user_id = :USER_ID
                        AND id IN ( $list )";
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);
}


/**
 * Function SlaveAdd
 **/
function SlaveAdd($mUserId, $mTable, $mIds, $mFields)
{
    // A list takes the end
    $ordered = ( isset(SCHEMA[$mTable]['pos']) && !isset($mFields['pos']) );

    // One row a goods
    $block = MELBIS()->SqlGenIdBlock('u_'.$mTable, count($mIds), $mUserId);
    foreach ( $mIds as $num => $id )
    {
        $row = $mFields;
        $row['id'] = $block[$num];
        $row['user_id'] = $mUserId;
        $row['store_id'] = $id;
        if ( $ordered ) $row['pos'] = $row['id'];
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_u_'.$mTable, $row);
    }

    Changed($mUserId, $mIds);

    // One id, or a list
    $made = ( count($block) == 1 ) ? $block[0] : $block;

    return [
        'result'  => true,
        'message' => 'The rows of u_'.$mTable.' are set',
        'detail'  => [
            'id' => $made
            ]
        ];
}


/**
 * Function SlaveUpdate
 **/
function SlaveUpdate($mUserId, $mTable, $mIds, $mFields)
{
    $said = Allowed($mUserId, $mTable, $mIds);
    if ( !$said['result'] ) return $said;

    if ( count($mFields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    // The goods they stand on
    $goods = array_column($said['rows'], 'store_id');

    // The goods it moves to
    if ( isset($mFields['store_id']) )
    {
        $moved = [$mFields['store_id']];
        $said = Allowed($mUserId, 'store', $moved);
        if ( !$said['result'] ) return $said;

        $goods[] = $mFields['store_id'];
    }

    $fields = $mFields;
    $fields['user_id'] = $mUserId;
    $key = ['user_id', 'id'];
    foreach ( $mIds as $id )
    {
        $fields['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_u_'.$mTable, $fields, $key);
    }

    Changed($mUserId, $goods);

    $count = count($mIds);

    return [
        'result'  => true,
        'message' => $count.' row(s) of u_'.$mTable.' changed'
        ];
}


/**
 * Function SlaveRemove
 **/
function SlaveRemove($mUserId, $mTable, $mIds)
{
    $said = Allowed($mUserId, $mTable, $mIds);
    if ( !$said['result'] ) return $said;

    $goods = array_column($said['rows'], 'store_id');

    $list = implode(',', $mIds);
    $command = "DELETE FROM {DBNICK}_u_$mTable
                      WHERE user_id = :USER_ID
                        AND id IN ( $list )";
    $param_user = [
        'user_id' => $mUserId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);

    Changed($mUserId, $goods);

    $count = count($mIds);

    return [
        'result'  => true,
        'message' => $count.' row(s) of u_'.$mTable.' gone'
        ];
}


/**
 * Function Take
 **/
function Take($mUserId, $mSaid)
{
    // A refusal or a signature
    if ( !$mSaid['result'] ) return $mSaid;
    if ( !isset($mSaid['tables']['store']) ) return $mSaid;

    // Written only under the hold
    $held = Held($mUserId);
    if ( !$held['result'] ) return $held;

    // The search tells the page
    $page = $mSaid['message'];
    $detail = [
        'found' => $mSaid['detail']['found']
        ];

    $ids = array_column($mSaid['tables']['store'], 'id');
    if ( count($ids) == 0 )
    {
        return [
            'result'  => true,
            'message' => $page.'. Nothing taken',
            'detail'  => $detail
            ];
    }

    // Already in the workspace
    $list = implode(',', $ids);
    $command = "SELECT id, base_id, was_update
                  FROM {DBNICK}_u_store
                 WHERE user_id = :USER_ID
                   AND base_id IN ( $list )";
    $param_user = [
        'user_id' => $mUserId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_user);

    // Changed copies stay, the rest anew
    $there = [];
    $stale = [];
    foreach ( $rows as $row )
    {
        if ( $row['was_update'] ) $there[] = $row['base_id'];
        else $stale[] = $row['id'];
    }
    if ( count($stale) > 0 ) Drop($mUserId, $stale);

    $diff = array_diff($ids, $there);
    $take = array_values($diff);

    $kept = '';
    $fresh = count($stale);
    if ( $fresh > 0 ) $kept = '; '.$fresh.' of them anew over copies not changed';
    if ( count($there) > 0 )
    {
        $named = implode(', ', $there);
        $kept .= '; changed in the workspace and left as they stand: ['.$named.']';
    }

    if ( count($take) == 0 )
    {
        return [
            'result'  => true,
            'message' => $page.'. Nothing taken'.$kept,
            'detail'  => $detail
            ];
    }

    // The card, piece by piece
    $list = implode(',', $take);

    // The lists the cards use
    $command = "SELECT DISTINCT si.info_id
                  FROM {DBNICK}_store_info si
                  JOIN {DBNICK}_info i
                    ON i.id = si.info_id
                 WHERE si.store_id IN ( $list )";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);
    $info_ids = array_column($rows, 'info_id');
    if ( count($info_ids) > 0 ) TakeValues($mUserId, $info_ids);

    $goods = TakeGoods($mUserId, $list);
    TakeTopics($goods);
    TakeSets($goods);
    TakeInfos($goods);
    TakeFiles($goods);

    $command = "SELECT id, base_id, name
                  FROM {DBNICK}_u_store
                 WHERE user_id = :USER_ID
                   AND id BETWEEN :FIRST AND :LAST
              ORDER BY id";
    $taken = MELBIS()->SqlSelect(__LINE__, $command, $goods);

    $count = count($taken);

    return [
        'result'  => true,
        'message' => $page.'. '.$count.' goods taken into the personal workspace'.$kept,
        'detail'  => $detail,
        'tables'  => [
            'u_store' => $taken
            ]
        ];
}


/**
 * Function TakeValues
 **/
function TakeValues($mUserId, $mInfoIds)
{
    $infos = implode(',', $mInfoIds);
    $param_user = [
        'user_id' => $mUserId
        ];

    // Untouched values follow the catalogue
    $command = "UPDATE {DBNICK}_u_info_value v
                  JOIN {DBNICK}_info_value iv
                    ON iv.id = v.base_id
                   SET v.info_id = iv.info_id,
                       v.name = iv.name,
                       v.descr = iv.descr,
                       v.kind_key = iv.kind_key,
                       v.params = iv.params,
                       v.seo_code = iv.seo_code,
                       v.pos = iv.pos
                 WHERE v.user_id = :USER_ID
                   AND v.was_update = 0
                   AND iv.info_id IN ( $infos )";
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);

    // Their files come anew
    $command = "DELETE f
                  FROM {DBNICK}_u_files_info_value f
                  JOIN {DBNICK}_u_info_value v
                    ON v.id = f.elem_id
                   AND v.user_id = f.user_id
                  JOIN {DBNICK}_info_value iv
                    ON iv.id = v.base_id
                 WHERE f.user_id = :USER_ID
                   AND v.was_update = 0
                   AND iv.info_id IN ( $infos )";
    MELBIS()->SqlQuery(__LINE__, $command, $param_user);

    // Values the workspace lacks
    $command = "INSERT INTO {DBNICK}_u_info_value
                       ( id, user_id, base_id, was_update, info_id, name, descr, kind_key, params, seo_code, pos )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY iv.id ), :USER_ID, iv.id, 0, iv.info_id,
                       iv.name, iv.descr, iv.kind_key, iv.params, iv.seo_code, iv.pos";
    $insert = $command;

    $command = "FROM {DBNICK}_info_value iv
           LEFT JOIN {DBNICK}_u_info_value v
                  ON v.base_id = iv.id
                 AND v.user_id = :USER_ID
               WHERE iv.info_id IN ( $infos )
                 AND v.id IS NULL";
    $from = $command;

    TakeRows('u_info_value', $insert, $from, $param_user);

    // The files of untouched values
    $command = "INSERT INTO {DBNICK}_u_files_info_value
                       ( id, base_id, user_id, elem_id, kind_key, file_name, file_size, upload_time, upload_ok,
                         real_name, parent_id, format_xml, pos )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY f.id ), f.id, :USER_ID, v.id, f.kind_key,
                       f.file_name, f.file_size, f.upload_time, f.upload_ok, f.real_name, NULL, f.format_xml, f.pos";
    $insert = $command;

    $command = "FROM {DBNICK}_files_info_value f
                JOIN {DBNICK}_info_value iv
                  ON iv.id = f.elem_id
                JOIN {DBNICK}_u_info_value v
                  ON v.base_id = f.elem_id
                 AND v.user_id = :USER_ID
                 AND v.was_update = 0
               WHERE iv.info_id IN ( $infos )";
    $from = $command;

    $files = TakeRows('u_files_info_value', $insert, $from, $param_user);
    if ( count($files) > 0 ) TakeFileParents('u_files_info_value', 'files_info_value', $files);
}


/**
 * Function TakeGoods
 **/
function TakeGoods($mUserId, $mList)
{
    // As the catalogue keeps it
    $command = "INSERT INTO {DBNICK}_u_store
                       ( id, user_id, base_id, was_update, was_edit, provider_id, brand_id, code_shop, code_prov,
                         code_made, meas, name, intro, descr, review, no_visible, status_key, kind_key, state_key,
                         clann, clann_title, clann_descr, clann_root, relate_id, price, price_curr_id, seo_psu,
                         seo_title, templ_key, create_time, update_time, exist_time, edit_time, option_code )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY s.id ), :USER_ID, s.id, 0, 0, s.provider_id,
                       s.brand_id, s.code_shop, s.code_prov, s.code_made, s.meas, s.name, s.intro, s.descr,
                       s.review, s.no_visible, s.status_key, s.kind_key, s.state_key, s.clann, s.clann_title,
                       s.clann_descr, s.clann_root, s.relate_id, s.price, s.price_curr_id, s.seo_psu, s.seo_title,
                       s.templ_key, s.create_time, s.update_time, s.exist_time, s.edit_time, s.option_code";
    $insert = $command;

    $command = "FROM {DBNICK}_store s
               WHERE s.id IN ( $mList )";
    $from = $command;

    $param_user = [
        'user_id' => $mUserId
        ];
    $rows = TakeRows('u_store', $insert, $from, $param_user);

    // The range the rest keys on
    return [
        'user_id' => $mUserId,
        'first'   => $rows['row_first'],
        'last'    => $rows['row_last']
        ];
}


/**
 * Function TakeTopics
 **/
function TakeTopics($mGoods)
{
    $command = "INSERT INTO {DBNICK}_u_topic_store
                       ( id, user_id, base_id, topic_id, store_id, pos )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY ts.id ), :USER_ID, ts.id, ts.topic_id,
                       u.id, ts.pos";
    $insert = $command;

    $command = "FROM {DBNICK}_topic_store ts
                JOIN {DBNICK}_u_store u
                  ON u.base_id = ts.store_id
                 AND u.user_id = :USER_ID
                 AND u.id BETWEEN :FIRST AND :LAST";
    $from = $command;

    TakeRows('u_topic_store', $insert, $from, $mGoods);
}


/**
 * Function TakeSets
 **/
function TakeSets($mGoods)
{
    $command = "INSERT INTO {DBNICK}_u_store_set
                       ( id, user_id, store_id, obj_key, kind_key, elem_id, params, comment, pos )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY ss.id ), :USER_ID, u.id, ss.obj_key,
                       ss.kind_key, ss.elem_id, ss.params, ss.comment, ss.pos";
    $insert = $command;

    $command = "FROM {DBNICK}_store_set ss
                JOIN {DBNICK}_u_store u
                  ON u.base_id = ss.store_id
                 AND u.user_id = :USER_ID
                 AND u.id BETWEEN :FIRST AND :LAST";
    $from = $command;

    TakeRows('u_store_set', $insert, $from, $mGoods);
}


/**
 * Function TakeInfos
 **/
function TakeInfos($mGoods)
{
    // Values point at workspace copies
    $command = "INSERT INTO {DBNICK}_u_store_info
                       ( id, user_id, store_id, info_id, value_id, value_dec, value_txt )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY si.id ), :USER_ID, u.id, si.info_id,
                       v.id, si.value_dec, si.value_txt";
    $insert = $command;

    $command = "FROM {DBNICK}_store_info si
                JOIN {DBNICK}_u_store u
                  ON u.base_id = si.store_id
                 AND u.user_id = :USER_ID
                 AND u.id BETWEEN :FIRST AND :LAST
           LEFT JOIN {DBNICK}_u_info_value v
                  ON v.base_id = si.value_id
                 AND v.user_id = :USER_ID";
    $from = $command;

    TakeRows('u_store_info', $insert, $from, $mGoods);
}


/**
 * Function TakeFiles
 **/
function TakeFiles($mGoods)
{
    $command = "INSERT INTO {DBNICK}_u_files_store
                       ( id, base_id, user_id, elem_id, kind_key, file_name, file_size, upload_time, upload_ok,
                         real_name, parent_id, format_xml, pos )
                SELECT :ROW_FIRST - 1 + ROW_NUMBER() OVER ( ORDER BY f.id ), f.id, :USER_ID, u.id, f.kind_key,
                       f.file_name, f.file_size, f.upload_time, f.upload_ok, f.real_name, NULL, f.format_xml, f.pos";
    $insert = $command;

    $command = "FROM {DBNICK}_files_store f
                JOIN {DBNICK}_u_store u
                  ON u.base_id = f.elem_id
                 AND u.user_id = :USER_ID
                 AND u.id BETWEEN :FIRST AND :LAST";
    $from = $command;

    $files = TakeRows('u_files_store', $insert, $from, $mGoods);
    if ( count($files) > 0 ) TakeFileParents('u_files_store', 'files_store', $files);
}


/**
 * Function TakeRows
 **/
function TakeRows($mTable, $mInsert, $mFrom, $mParam)
{
    // As many ids as rows
    $command = "SELECT COUNT(*) $mFrom";
    $count = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $mParam);
    if ( $count == 0 ) return [];

    $ids = MELBIS()->SqlGenIdBlock($mTable, $count, $mParam['user_id']);
    $param_rows = $mParam;
    $param_rows['row_first'] = reset($ids);
    $param_rows['row_last'] = end($ids);

    $command = "$mInsert $mFrom";
    MELBIS()->SqlQuery(__LINE__, $command, $param_rows);

    return $param_rows;
}


/**
 * Function TakeFileParents
 **/
function TakeFileParents($mTable, $mSource, $mRows)
{
    // Copies point at copies
    $command = "UPDATE {DBNICK}_$mTable c
                  JOIN {DBNICK}_$mSource f
                    ON f.id = c.base_id
                  JOIN {DBNICK}_$mTable p
                    ON p.base_id = f.parent_id
                   AND p.user_id = :USER_ID
                   AND p.id BETWEEN :ROW_FIRST AND :ROW_LAST
                   SET c.parent_id = p.id
                 WHERE c.user_id = :USER_ID
                   AND c.id BETWEEN :ROW_FIRST AND :ROW_LAST";
    MELBIS()->SqlQuery(__LINE__, $command, $mRows);

    // The window keeps it empty
    $command = "UPDATE {DBNICK}_$mTable
                   SET base_id = NULL
                 WHERE user_id = :USER_ID
                   AND id BETWEEN :ROW_FIRST AND :ROW_LAST";
    MELBIS()->SqlQuery(__LINE__, $command, $mRows);
}



?>
