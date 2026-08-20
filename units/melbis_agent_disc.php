<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList       - Reads the discount groups and every rule in them
 * CmdAdd        - Adds a group at the end of the list
 * CmdUpdate     - Changes the given columns of groups, by id
 * CmdRemove     - Deletes groups by id and empties disc_group_id on the goods priced by them
 * CmdPos        - Reorders the list by POS, MOVE or SORT
 * CmdRateAdd    - Adds a rule to groups, one row of disc_rate on each
 * CmdRateUpdate - Changes the given columns of rules, by id
 * CmdRateRemove - Deletes rules by id
 *
 * A group gives nothing away until a rule stands in it: from_sum, disc_proc and the days between
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_DISC;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a group
const FIELDS_GROUP = "skey, name, pos";

// The columns a call may write into a rule; the group it hangs on is the address of the call
const FIELDS_RATE = "kind_key, from_sum, sum_curr_id, disc_proc, begin_time, end_time";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_DISCOUNT', 'Reading the discount groups');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_disc_group
              ORDER BY pos
               ";
    $groups = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_disc_rate
              ORDER BY group_id, from_sum
               ";
    $rates = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($groups).' discount groups, '.count($rates).' rules',
        'tables'  => [
            'disc_group' => $groups,
            'disc_rate'  => $rates
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Adding a discount group');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_GROUP);

    $tables = ['{DBNICK}_disc_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_disc_group
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('disc_group');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_disc_group', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The discount group ['.$mParam['name'].'] is in the list, id '.$row['id'].
                     '. It gives nothing away until a rule stands in it - CmdRateAdd'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Changing a discount group');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('disc_group', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No discount groups ['.$list.'] in the store - CmdList answers them'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_GROUP);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_disc_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_disc_group', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' group(s) changed: '.$changed
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Removing a discount group');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('disc_group', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No discount groups ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    // Counts the goods priced by them: they lose the discount, and recursive is weighed by that
    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_store
                 WHERE disc_group_id IN ( $list )
               ";
    $goods = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( $goods > 0 && !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => $goods.' goods price by those groups and will be left without a discount. '.
                         'Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_disc_group', '{DBNICK}_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Empties disc_group_id by an update: no sweep of the engine knows this tie
    $command = "UPDATE {DBNICK}_store
                   SET disc_group_id = NULL
                 WHERE disc_group_id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "DELETE
                  FROM {DBNICK}_disc_group
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps the rules of the gone groups, by the map of the engine
    $swept = UTIL\DependSweep('disc_group');

    $message = count($ids).' group(s) gone';
    if ( $goods > 0 ) $message .= '; '.$goods.' goods are without a discount now';
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
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Shaping the order of the discount groups');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_disc_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // One list for the whole table, so the scope is empty
    $done = UTIL\Pos('disc_group', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The discount groups: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdRateAdd
 **/
function CmdRateAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Adding a rule of a discount group');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('disc_group', $mParam['group_id']);
    $lost = array_diff($mParam['group_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No discount groups ['.$list.'] in the store'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_RATE);

    // Opens the days of a rule the way the program does: from now, for ten years
    $now = MELBIS()->DateTime();
    if ( !isset($fields['begin_time']) ) $fields['begin_time'] = $now;
    if ( !isset($fields['end_time']) ) $fields['end_time'] = date('Y-m-d H:i:s',
                                                                  strtotime($now.' +10 years'));

    $tables = ['{DBNICK}_disc_rate'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('disc_rate');
        $row['group_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_disc_rate', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rule(s) in the discount groups, living from '.
                     $fields['begin_time'].' to '.$fields['end_time']
        ];
}


/**
 * Function CmdRateUpdate
 **/
function CmdRateUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Changing a rule of a discount group');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('disc_rate', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rules ['.$list.'] in the store - CmdList answers them with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_RATE);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_disc_rate'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_disc_rate', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rule(s) changed: '.$changed
        ];
}


/**
 * Function CmdRateRemove
 **/
function CmdRateRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_DISCOUNT', 'Removing a rule of a discount group');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('disc_rate', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rules ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    $tables = ['{DBNICK}_disc_rate'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_disc_rate
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rule(s) gone. The group stays, and gives nothing away while it '.
                     'holds no rule'
        ];
}




?>
