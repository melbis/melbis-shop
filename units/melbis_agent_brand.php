<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList      - Reads the brands, the tree of their options with values, and what is set on each
 * CmdAdd       - Adds a brand at the end of the list
 * CmdUpdate    - Changes the given columns of brands, by id
 * CmdRemove    - Deletes brands by id and empties brand_id on the goods that wore them
 * CmdPos       - Reorders the list by POS, MOVE or SORT
 * CmdKeyAdd    - Sets an option on brands, one row of brand_key_set on each
 * CmdKeyUpdate - Changes the given columns of option rows, by id
 * CmdKeyRemove - Deletes option rows by id
 *
 * The option tree (brand_key, brand_key_value) belongs to the program: read here, never written
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_BRAND;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a brand
const FIELDS_BRAND = "skey, name, descr, kind_key, params, seo_code, pos";

// The columns a call may write into an option row
const FIELDS_KEY = "key_id, value_id, value_txt";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_BRAND', 'Reading the brands');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_brand
              ORDER BY pos
               ";
    $brands = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_brand_key
              ORDER BY absindex
               ";
    $keys = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_brand_key_value
              ORDER BY key_id, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_brand_key_set
              ORDER BY brand_id, id
               ";
    $sets = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($brands).' brands, '.count($keys).' options, '.count($sets).
                     ' option rows set',
        'tables'  => [
            'brand'           => $brands,
            'brand_key'       => $keys,
            'brand_key_value' => $values,
            'brand_key_set'   => $sets
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Adding a brand');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_BRAND);

    $tables = ['{DBNICK}_brand'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_brand
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('brand');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_brand', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The brand ['.$mParam['name'].'] is in the list, id '.$row['id']
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Changing a brand');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('brand', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No brands ['.$list.'] in the store - CmdList answers them with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_BRAND);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_brand'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_brand', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' brand(s) changed: '.$changed
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Removing a brand');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('brand', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No brands ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    // Counts the goods wearing them: they lose the brand, and recursive is weighed by that
    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_store
                 WHERE brand_id IN ( $list )
               ";
    $goods = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( $goods > 0 && !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => $goods.' goods wear those brand(s) and will be left without one. Say '.
                         'recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_brand', '{DBNICK}_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Empties brand_id by an update: no sweep of the engine knows this tie
    $command = "UPDATE {DBNICK}_store
                   SET brand_id = NULL
                 WHERE brand_id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "DELETE
                  FROM {DBNICK}_brand
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps what pointed at the brands, by the map of the engine
    $swept = UTIL\DependSweep('brand');

    $message = count($ids).' brand(s) gone';
    if ( $goods > 0 ) $message .= ', '.$goods.' goods are without a brand now';
    $message .= UTIL\DependSaid($swept);

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
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Shaping the order of the brands');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_brand'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // One list for the whole table, so the scope is empty
    $done = UTIL\Pos('brand', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The brands: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Setting an option of a brand');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('brand', $mParam['brand_id']);
    $lost = array_diff($mParam['brand_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No brands ['.$list.'] in the store'
            ];
    }

    // The option and its value belong to the tree of this family, and a stray id is refused
    $option = UTIL\OptionPair('brand', $mParam);
    if ( $option !== true ) return $option;

    $fields = UTIL\Only($mParam, FIELDS_KEY);

    $tables = ['{DBNICK}_brand_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('brand_key_set');
        $row['brand_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_brand_key_set', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' row(s) of brand_key_set set'
        ];
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Setting an option of a brand');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('brand_key_set', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] in brand_key_set'
            ];
    }

    // The pair as it will stand after the change: a row keeps whatever the call left unsaid
    $list = implode(',', $ids);
    $command = "SELECT id, key_id, value_id
                  FROM {DBNICK}_brand_key_set
                 WHERE id IN ( $list )
               ";
    $was_set = MELBIS()->SqlSelect(__LINE__, $command);
    foreach ( $was_set as $was )
    {
        $option = UTIL\OptionPair('brand', $mParam, $was);
        if ( $option !== true ) return $option;
    }

    $fields = UTIL\Only($mParam, FIELDS_KEY);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_brand_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_brand_key_set', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' option row(s) changed: '.$changed
        ];
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_BRAND', 'Setting an option of a brand');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('brand_key_set', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] in brand_key_set'
            ];
    }

    $list = implode(',', $ids);

    $tables = ['{DBNICK}_brand_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_brand_key_set
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' option row(s) gone'
        ];
}




?>
