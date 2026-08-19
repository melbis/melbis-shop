<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the whole tree of the groups, their rights and every setting in them
 * CmdAdd         - Adds a group under a parent, with the rights of that parent
 * CmdUpdate      - Changes the given columns of groups, by id
 * CmdMove        - Moves a group under another parent, its branch with it
 * CmdRemove      - Deletes groups with their branches, settings and rights
 * CmdValueAdd    - Adds a setting at the end of the list of its group
 * CmdValueUpdate - Changes the given columns of settings, by id
 * CmdValueRemove - Deletes settings by id
 * CmdValuePos    - Reorders the settings inside one group
 * CmdRightAdd    - Grants a right on groups, to a person or to a group of people
 * CmdRightUpdate - Changes the given columns of self_key_right rows, by id
 * CmdRightRemove - Deletes self_key_right rows by id
 *
 * The settings the people of the shop change themselves; a module of the storefront reads one by
 * its code
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_SELF_KEY;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a group
const FIELDS_KEY = "name, folder";

// The columns a call may write into a setting
const FIELDS_VALUE = "code, prefix, name, descr, mask_edit, value_txt, pos";

// The columns a call may write into a right
const FIELDS_RIGHT = "user_id, group_id";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_USER_KEY', 'Reading the settings of the people');
    if ( $gate !== true ) return $gate;

    $command = "SELECT *
                  FROM {DBNICK}_self_key
              ORDER BY absindex
               ";
    $keys = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_self_key_right
              ORDER BY key_id, id
               ";
    $rights = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_self_key_value
              ORDER BY key_id, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($keys).' groups, '.count($rights).' rights, '.count($values).' settings',
        'tables'  => [
            'self_key'       => $keys,
            'self_key_right' => $rights,
            'self_key_value' => $values
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_KEY);

    $tables = ['{DBNICK}_self_key'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $id = UTIL\TreeNodeAdd('self_key', $mParam['parent_id'], $fields);

    UTIL\TablesUnlock($tables);

    if ( $id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The group ['.$mParam['parent_id'].'] is out of the tree, so nothing can go '.
                         'under it'
            ];
    }

    return [
        'result'  => true,
        'id'      => $id,
        'message' => 'The group ['.$id.'] is under ['.$mParam['parent_id'].']'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('self_key', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No groups ['.$list.'] of the settings - CmdList answers them with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_KEY);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_self_key'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_self_key', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' groups changed: '.$changed
        ];
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_self_key'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = MELBIS()->SysTreeMove('self_key', $mParam['id'], $mParam['parent_id']);

    UTIL\TablesUnlock($tables);

    if ( !$done )
    {
        return [
            'result'  => false,
            'message' => 'The group ['.$mParam['parent_id'].'] sits inside the branch of ['.
                         $mParam['id'].'], or is out of the tree: nothing was moved'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The group ['.$mParam['id'].'] is under ['.$mParam['parent_id'].'] now'
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('self_key', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No groups ['.$list.'] of the settings'
            ];
    }

    // Walks the branch under every id: the whole of it goes, not the root alone
    $branch = [];
    foreach ( $ids as $id )
    {
        $mine = UTIL\TreeBranch('self_key', $id);
        foreach ( $mine as $one )
        {
            $branch[$one] = $one;
        }
    }
    $list = implode(',', $branch);

    $told = UTIL\DependCount('self_key', $branch);
    if ( !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => 'About to delete '.count($branch).' group(s) with the branch under them'.
                         $told['said'].'. The modules that read those settings will find nothing. '.
                         'Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_self_key'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The door of the engine takes one node with its branch, and the call takes a list of them
    foreach ( $ids as $id )
    {
        MELBIS()->SysTreeDelete('self_key', $id);
    }

    UTIL\TablesUnlock($tables);

    $swept = UTIL\DependSweep('self_key');

    return [
        'result'  => true,
        'message' => count($branch).' group(s) gone with their branches'.UTIL\DependSaid($swept)
        ];
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $key_id = $mParam['key_id'];
    if ( count(UTIL\Exists('self_key', [$key_id])) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No group ['.$key_id.'] of the settings'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_VALUE);

    $tables = ['{DBNICK}_self_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_self_key_value
                 WHERE key_id = :KEY_ID
               ";
    $param_last = [
        'key_id' => $key_id
        ];
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_last);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('self_key_value');
    $row['key_id'] = $key_id;
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_self_key_value', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The setting ['.$row['id'].'] is in the group ['.$key_id.']'
        ];
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('self_key_value', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No settings ['.$list.'] in the registry'
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

    $tables = ['{DBNICK}_self_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_self_key_value', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' settings changed: '.$changed
        ];
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('self_key_value', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No settings ['.$list.'] in the registry'
            ];
    }

    $list = implode(',', $ids);

    $tables = ['{DBNICK}_self_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_self_key_value
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' setting(s) gone. A module that reads one of them by its code will '.
                     'find nothing there'
        ];
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    // The list here is one group, and the rows put in order are its settings
    $scope = [
        'key_id' => $mParam['key_id']
        ];

    $tables = ['{DBNICK}_self_key_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = UTIL\Pos('self_key_value', $scope, $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The settings of ['.$mParam['key_id'].']: '.$done['said'].', '.$done['moved'].
                     ' row(s) moved'
        ];
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_RIGHT);
    if ( !isset($fields['user_id']) && !isset($fields['group_id']) )
    {
        return [
            'result'  => false,
            'message' => 'A right belongs to a person or to a group: name user_id or group_id'
            ];
    }

    $tables = ['{DBNICK}_self_key_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Every grant answers its own id: a right is changed and taken back by it later
    $made = [];
    foreach ( $mParam['key_id'] as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('self_key_right');
        $row['key_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_self_key_right', $row);
        $made[] = $row['id'];
    }

    UTIL\TablesUnlock($tables);

    // One right is one id, and rights on several nodes are a list of them
    $said = ( count($made) == 1 ) ? $made[0] : $made;

    return [
        'result'  => true,
        'id'      => $said,
        'message' => 'The right ['.implode(',', $made).'] stands on '.count($mParam['key_id']).' group(s)'
        ];
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('self_key_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] of self_key_right'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_RIGHT);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_self_key_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_self_key_right', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'UPDATE_USER_KEY', 'Shaping the settings of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('self_key_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] of self_key_right'
            ];
    }

    $tables = ['{DBNICK}_self_key_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_self_key_right', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone. A group with no rights left on it is seen by nobody '.
                     'in the Settings of the program'
        ];
}





?>
