<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the providers, their groups, the option tree and what is set on each
 * CmdAdd         - Adds a provider at the end of the list
 * CmdUpdate      - Changes the given columns of providers, by id
 * CmdRemove      - Deletes providers by id and empties provider_id on the goods and currencies
 * CmdPos         - Reorders the list by POS, MOVE or SORT
 * CmdGroupAdd    - Adds a group at the end of the list
 * CmdGroupUpdate - Changes the given columns of groups, by id
 * CmdGroupRemove - Deletes groups by id, refusing while providers stand in them
 * CmdGroupPos    - Reorders the groups by POS, MOVE or SORT
 * CmdKeyAdd      - Sets an option on providers, one row of provider_key_set on each
 * CmdKeyUpdate   - Changes the given columns of option rows, by id
 * CmdKeyRemove   - Deletes option rows by id
 *
 * The option tree (provider_key, provider_key_value) belongs to the program: read, never written
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_PROVIDER;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a provider
const FIELDS_PROVIDER = "skey, group_id, name, kind_key, state_key, params, manager, phones,
                         emails, store, serv_addr, serv_phone, notice, pos";

// The columns a call may write into a group
const FIELDS_GROUP = "skey, name, pos";

// The columns a call may write into an option row
const FIELDS_KEY = "key_id, value_id, value_txt";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_PROVIDER', 'Reading the providers');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_provider
              ORDER BY pos
               ";
    $providers = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_provider_group
              ORDER BY pos
               ";
    $groups = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_provider_key
              ORDER BY absindex
               ";
    $keys = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_provider_key_value
              ORDER BY key_id, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_provider_key_set
              ORDER BY provider_id, id
               ";
    $sets = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($providers).' providers, '.count($groups).' groups, '.count($keys).
                     ' options, '.count($sets).' option rows set',
        'tables'  => [
            'provider'           => $providers,
            'provider_group'     => $groups,
            'provider_key'       => $keys,
            'provider_key_value' => $values,
            'provider_key_set'   => $sets
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Adding a provider');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_PROVIDER);

    $tables = ['{DBNICK}_provider'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_provider
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('provider');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_provider', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The provider ['.$mParam['name'].'] is in the list, id '.$row['id']
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Changing a provider');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No providers ['.$list.'] in the store - CmdList answers them'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_PROVIDER);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_provider'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_provider', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' provider(s) changed: '.$changed
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Removing a provider');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No providers ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    // Counts the goods pointing at them: they lose the provider, and the answer says how many
    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_store
                 WHERE provider_id IN ( $list )
               ";
    $goods = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_currency
                 WHERE provider_id IN ( $list )
               ";
    $currencies = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( ( $goods > 0 || $currencies > 0 ) && !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => $goods.' goods and '.$currencies.' currency(ies) point at those '.
                         'providers and will be left without one. Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_provider', '{DBNICK}_store', '{DBNICK}_currency'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Empties provider_id by an update: no sweep of the engine knows this tie
    $command = "UPDATE {DBNICK}_store
                   SET provider_id = NULL
                 WHERE provider_id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "UPDATE {DBNICK}_currency
                   SET provider_id = NULL
                 WHERE provider_id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "DELETE
                  FROM {DBNICK}_provider
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps what pointed at the providers, by the map of the engine
    $swept = UTIL\DependSweep('provider');

    $message = count($ids).' provider(s) gone';
    if ( $goods > 0 || $currencies > 0 )
    {
        $message .= '; '.$goods.' goods and '.$currencies.' currency(ies) are without one now';
    }
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
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Shaping the order of the providers');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_provider'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The whole table is one list here, so nothing narrows the scope
    $done = UTIL\Pos('provider', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The providers: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdGroupAdd
 **/
function CmdGroupAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Shaping the groups of the providers');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_GROUP);

    $tables = ['{DBNICK}_provider_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_provider_group
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('provider_group');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_provider_group', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The group ['.$row['id'].'] is in the list'
        ];
}


/**
 * Function CmdGroupUpdate
 **/
function CmdGroupUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Shaping the groups of the providers');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider_group', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No groups ['.$list.'] in the store'
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

    $tables = ['{DBNICK}_provider_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_provider_group', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' group(s) changed: '.$changed
        ];
}


/**
 * Function CmdGroupRemove
 **/
function CmdGroupRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Shaping the groups of the providers');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider_group', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No groups ['.$list.'] in the store'
            ];
    }

    $list = implode(',', $ids);

    // Counts the providers standing in those groups: a group with any of them is not deleted
    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_provider
                 WHERE group_id IN ( $list )
               ";
    $stand = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( $stand > 0 )
    {
        return [
            'result'  => false,
            'message' => $stand.' provider(s) stand in those groups and would be left without one '.
                         '- move them first, CmdUpdate with group_id'
            ];
    }

    $tables = ['{DBNICK}_provider_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_provider_group
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' group(s) gone'
        ];
}


/**
 * Function CmdGroupPos
 **/
function CmdGroupPos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Shaping the groups of the providers');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_provider_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The whole table is one list here, so nothing narrows the scope
    $done = UTIL\Pos('provider_group', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The groups: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Setting an option of a provider');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider', $mParam['provider_id']);
    $lost = array_diff($mParam['provider_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No providers ['.$list.'] in the store'
            ];
    }

    // The option and its value belong to the tree of this family, and a stray id is refused
    $option = UTIL\OptionPair('provider', $mParam);
    if ( $option !== true ) return $option;

    $fields = UTIL\Only($mParam, FIELDS_KEY);

    $tables = ['{DBNICK}_provider_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('provider_key_set');
        $row['provider_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_provider_key_set', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' row(s) of provider_key_set set'
        ];
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Setting an option of a provider');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider_key_set', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] in provider_key_set'
            ];
    }

    // The pair as it will stand after the change: a row keeps whatever the call left unsaid
    $list = implode(',', $ids);
    $command = "SELECT id, key_id, value_id
                  FROM {DBNICK}_provider_key_set
                 WHERE id IN ( $list )
               ";
    $was_set = MELBIS()->SqlSelect(__LINE__, $command);
    foreach ( $was_set as $was )
    {
        $option = UTIL\OptionPair('provider', $mParam, $was);
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

    $tables = ['{DBNICK}_provider_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_provider_key_set', $row, 'id');
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
    $gate = UTIL\RightOper($mUserId, 'PUT_PROVIDER', 'Setting an option of a provider');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('provider_key_set', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] in provider_key_set'
            ];
    }

    $list = implode(',', $ids);

    $tables = ['{DBNICK}_provider_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_provider_key_set
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
