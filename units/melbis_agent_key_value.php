<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the tree of the codes and every value under them
 * CmdValueAdd    - Adds a value at the end of the list of its code
 * CmdValueUpdate - Changes the given columns of values, by id
 * CmdValueRemove - Deletes values by id
 * CmdValuePos    - Reorders the values inside one code
 *
 * ValueNamed     - Of the values asked for, the rows that are there, with their sys_key
 * System         - Refuses a value the code of the platform holds by name
 *
 * The lists the whole program picks from; the tree of the codes is the platform's, read only
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_KEY_VALUE;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a value; sys_key belongs to the platform, not to a caller
const FIELDS_VALUE = "key_name, value_txt, pos";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_KEY_VALUE', 'Reading the base settings');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_key
              ORDER BY absindex
               ";
    $keys = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_key_value
              ORDER BY key_code, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($keys).' codes, '.count($values).' values',
        'tables'  => [
            'key'       => $keys,
            'key_value' => $values
            ]
        ];
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_KEY_VALUE', 'Shaping the base settings');
    if ( $gate !== true ) return $gate;

    $key_code = $mParam['key_code'];

    $command = "SELECT id
                  FROM {DBNICK}_key
                 WHERE code = :CODE
               ";
    $param_code = [
        'code' => $key_code
        ];
    $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_code);
    if ( !isset($row['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No code ['.$key_code.'] in the registry - CmdList answers the codes'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_VALUE);

    $tables = ['{DBNICK}_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_key_value
                 WHERE key_code = :CODE
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_code);

    $value = $fields;
    $value['id'] = MELBIS()->SqlGenId('key_value');
    $value['key_code'] = $key_code;
    $value['sys_key'] = 0;
    if ( !isset($value['pos']) ) $value['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_key_value', $value);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $value['id'],
        'message' => 'The value ['.$value['id'].'] is in the code ['.$key_code.']'
        ];
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_KEY_VALUE', 'Shaping the base settings');
    if ( $gate !== true ) return $gate;

    $rows = ValueNamed($mParam['id']);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No values ['.$list.'] in the registry'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_VALUE);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    // The text of a system value may change, but not its name: the code looks it up by name
    if ( isset($fields['key_name']) )
    {
        foreach ( $rows as $was )
        {
            if ( $was['sys_key'] > 0 ) return System($was, 'renamed');
        }
    }

    $tables = ['{DBNICK}_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_key_value', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' values changed: '.$changed
        ];
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_KEY_VALUE', 'Shaping the base settings');
    if ( $gate !== true ) return $gate;

    $rows = ValueNamed($mParam['id']);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No values ['.$list.'] in the registry'
            ];
    }

    foreach ( $rows as $was )
    {
        if ( $was['sys_key'] > 0 ) return System($was, 'removed');
    }

    $list = implode(',', $ids);

    $tables = ['{DBNICK}_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_key_value
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps what hung on the gone values, files among them, by the map of the engine
    $swept = UTIL\DependSweep('key_value');

    $message = count($ids).' value(s) gone. What already holds such a value keeps the word: '.
               'nothing of the shop is rewritten behind a gone value';
    $message .= UTIL\DependSaid($swept);

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_KEY_VALUE', 'Shaping the base settings');
    if ( $gate !== true ) return $gate;

    // The list here is one code, and the rows put in order are its values
    $scope = [
        'key_code' => $mParam['key_code']
        ];

    $tables = ['{DBNICK}_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = UTIL\Pos('key_value', $scope, $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The values of ['.$mParam['key_code'].']: '.$done['said'].', '.$done['moved'].
                     ' row(s) moved'
        ];
}


/**
 * Function ValueNamed
 **/
function ValueNamed($mIds)
{
    // Reads the rows of the values asked for, sys_key with them, to weigh what may be touched
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
    // Builds the refusal and points at the place in the program where the value lives
    $path = UTIL\TreePathFind('key', 'code', $mWas['key_code']);
    $where = ( $path == '' ) ? '' : ' In the program it is '.$path.'.';

    return [
        'result'  => false,
        'message' => 'The value ['.$mWas['key_name'].'] of ['.$mWas['key_code'].'] is one the program '.
                     'itself relies on, and cannot be '.$mWord.' - the code of the platform names it '.
                     'outright. Its text is another matter: value_txt may be changed on any value.'.
                     $where
        ];
}


?>
