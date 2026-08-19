<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the parameters and all their values
 * CmdAdd         - Adds a parameter at the end of the list
 * CmdUpdate      - Changes the given columns of parameters, by id
 * CmdRemove      - Deletes parameters by id, their values and the links of the goods with them
 * CmdPos         - Reorders the parameters by POS, MOVE or SORT
 * CmdValueAdd    - Adds a value at the end of the list of its parameter
 * CmdValueUpdate - Changes the given columns of values, by id
 * CmdValueRemove - Deletes values by id and the links of the goods to them
 * CmdValuePos    - Reorders the values inside one parameter
 *
 * The commercial properties a price is built of; what a buyer reads is Attributes, another tool
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_PARAM;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a parameter
const FIELDS_PARAM = "skey, name, kind_key, fixed_set, custom_sum, pos";

// The columns a call may write into a value
const FIELDS_VALUE = "skey, name, set_sum, sum_curr_id, pos";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_PARAM', 'Reading the parameters');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_param
              ORDER BY pos
               ";
    $params = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_param_value
              ORDER BY param_id, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($params).' parameters, '.count($values).' values',
        'tables'  => [
            'param'       => $params,
            'param_value' => $values
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_PARAM);

    $tables = ['{DBNICK}_param'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_param
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('param');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_param', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The parameter ['.$row['id'].'] is in the list'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('param', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No parameters ['.$list.'] in the store - CmdList answers them with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_PARAM);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_param'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_param', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' parameters changed: '.$changed
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('param', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No parameters ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    $told = UTIL\DependCount('param', $ids);
    if ( !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => 'About to delete '.count($ids).' parameter(s)'.$told['said'].' - the prices '.
                         'built on them change. Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_param', '{DBNICK}_param_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_param
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps the values and the links of the goods, by the map of the engine
    $swept = UTIL\DependSweep('param');

    return [
        'result'  => true,
        'message' => count($ids).' parameter(s) gone'.UTIL\DependSaid($swept)
        ];
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_param'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // One list for the whole table, so the scope is empty
    $done = UTIL\Pos('param', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The parameters: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $param_id = $mParam['param_id'];
    $named = UTIL\Exists('param', [$param_id]);
    if ( count($named) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No parameter ['.$param_id.'] in the store'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_VALUE);

    $tables = ['{DBNICK}_param_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_param_value
                 WHERE param_id = :PARAM_ID
               ";
    $param_last = [
        'param_id' => $param_id
        ];
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_last);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('param_value');
    $row['param_id'] = $param_id;
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_param_value', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The value ['.$row['id'].'] is in the parameter ['.$param_id.']'
        ];
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('param_value', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No values ['.$list.'] in the store - CmdList answers them with their ids'
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

    $tables = ['{DBNICK}_param_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_param_value', $row, 'id');
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
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('param_value', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No values ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    $told = UTIL\DependCount('param_value', $ids);
    if ( !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => 'About to delete '.count($ids).' value(s)'.$told['said'].' - the prices built '.
                         'on them change. Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_param_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_param_value
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps the links of the goods to those values, the table free again
    $swept = UTIL\DependSweep('param_value');

    return [
        'result'  => true,
        'message' => count($ids).' value(s) gone'.UTIL\DependSaid($swept)
        ];
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PARAM', 'Shaping the parameters');
    if ( $gate !== true ) return $gate;

    $param_id = $mParam['param_id'];
    $named = UTIL\Exists('param', [$param_id]);
    if ( count($named) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No parameter ['.$param_id.'] in the store'
            ];
    }

    // The list here is one parameter, and the rows put in order are its values
    $scope = [
        'param_id' => $param_id
        ];

    $tables = ['{DBNICK}_param_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = UTIL\Pos('param_value', $scope, $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The values of ['.$param_id.']: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}




?>
