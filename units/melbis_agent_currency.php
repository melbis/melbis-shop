<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList   - Reads the whole currency table
 * CmdAdd    - Adds a currency at the end of the list
 * CmdUpdate - Changes the given columns of currencies, by id
 * CmdRemove - Deletes currencies by id and empties the columns of the goods pointing at them
 * CmdPos    - Reorders the list by POS, MOVE or SORT
 *
 * The currencies prices are written in; a goods points at one by its *_curr_id columns
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_CURRENCY;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a currency
const FIELDS_CURRENCY = "skey, name, multiplex, division, provider_id, pos";

// The columns of a goods that carry a currency
const FIELDS_STORE = ['pprice_curr_id', 'rprice_curr_id', 'price_curr_id',
                      'price2_curr_id', 'price3_curr_id'];


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_CURRENCY', 'Reading the currencies');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_currency
              ORDER BY pos
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($rows).' currencies',
        'tables'  => [
            'currency' => $rows
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_CURRENCY', 'Adding a currency');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_CURRENCY);

    $tables = ['{DBNICK}_currency'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_currency
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('currency');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_currency', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The currency ['.$mParam['name'].'] is in the list, id '.$row['id']
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_CURRENCY', 'Changing a currency');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('currency', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No currencies ['.$list.'] in the store - CmdList answers them'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_CURRENCY);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_currency'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_currency', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' currency(ies) changed: '.$changed
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_CURRENCY', 'Removing a currency');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('currency', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No currencies ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    // Counts the goods priced in them, and the answer says it before anything is deleted
    $said = [];
    foreach ( FIELDS_STORE as $column )
    {
        $said[] = "$column IN ( $list )";
    }
    $when = implode(' OR ', $said);

    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_store
                 WHERE $when
               ";
    $goods = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( $goods > 0 && !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => $goods.' goods price in those currencies and will be left with the base '.
                         'one. Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_currency', '{DBNICK}_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Empties those columns by an update: no sweep of the engine knows this tie
    foreach ( FIELDS_STORE as $column )
    {
        $command = "UPDATE {DBNICK}_store
                       SET $column = NULL
                     WHERE $column IN ( $list )
                   ";
        MELBIS()->SqlQuery(__LINE__, $command);
    }

    $command = "DELETE
                  FROM {DBNICK}_currency
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    $message = count($ids).' currency(ies) gone';
    if ( $goods > 0 )
    {
        $message .= '; '.$goods.' goods price in the base one now';
    }

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_CURRENCY', 'Shaping the order of the currencies');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_currency'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // One list for the whole table, so the scope is empty
    $done = UTIL\Pos('currency', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The currencies: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}



?>
