<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the whole tree of the characteristics and the four tables hanging off it
 * CmdAdd         - Adds a characteristic under a parent, with the rights of that parent
 * CmdUpdate      - Changes the given columns of characteristics, by id
 * CmdMove        - Moves a characteristic under another parent, its branch with it
 * CmdRemove      - Deletes characteristics with their branches, values and links of the goods
 * CmdValueList   - Reads the values of the given characteristics, or values by id
 * CmdValueAdd    - Adds a value at the end of the list of its characteristic
 * CmdValueUpdate - Changes the given columns of values, by id
 * CmdValueRemove - Deletes values by id and the links of the goods to them
 * CmdValuePos    - Reorders the values inside one characteristic
 * CmdRightAdd    - Grants a right on characteristics, to a person or to a group
 * CmdRightUpdate - Changes the given columns of info_right rows, by id
 * CmdRightRemove - Deletes info_right rows by id
 * CmdKeyAdd      - Sets an option of the registry on characteristics
 * CmdKeyUpdate   - Changes the given columns of info_key_set rows, by id
 * CmdKeyRemove   - Deletes info_key_set rows by id
 *
 * InfoAllowed    - Of the characteristics asked for, the ones this person may shape
 * ValueAllowed   - Of the values asked for, the ones on such characteristics
 * KeyAllowed     - Of the rows asked for, the ones on such characteristics
 *
 * The rights come in a pair: for_info on the characteristic itself, for_value on its values
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_INFO;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a characteristic; the shape of the tree is the engine's
const FIELDS_INFO = "skey, name, descr, folder, kind_key, type_key, in_topic, in_goods, seo_code";

// The columns a call may write into a value
const FIELDS_VALUE = "skey, name, descr, kind_key, params, seo_code, pos";

// The columns a call may write into a right
const FIELDS_RIGHT = "user_id, group_id, for_info, for_value";

// The columns a call may write into an option row
const FIELDS_KEY = "key_id, value_id, value_txt";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $command = "SELECT *
                  FROM {DBNICK}_info
              ORDER BY absindex
               ";
    $infos = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_info_right
              ORDER BY info_id, id
               ";
    $rights = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_info_key
              ORDER BY absindex
               ";
    $options = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_info_key_value
              ORDER BY key_id, pos
               ";
    $values = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_info_key_set
              ORDER BY info_id, id
               ";
    $keys = MELBIS()->SqlSelect(__LINE__, $command);

    // Counts the values of every characteristic, so a read of them is asked for knowingly
    $command = "SELECT info_id, COUNT(*) AS values_how
                  FROM {DBNICK}_info_value
              GROUP BY info_id
               ";
    $how = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($infos).' characteristics, '.count($rights).' rights, '.count($options).
                     ' options, '.count($keys).' settings; CmdValueList answers the values of the '.
                     'ones that hold any',
        'tables'  => [
            'info'           => $infos,
            'info_right'     => $rights,
            'info_key'       => $options,
            'info_key_value' => $values,
            'info_key_set'   => $keys,
            'info_values'    => $how
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'ADD_FAST_INFO', 'Adding a characteristic');
    if ( $gate !== true ) return $gate;

    $parent_id = $mParam['parent_id'];
    if ( $parent_id > 0 && count(InfoAllowed($mUserId, [$parent_id])) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The characteristic ['.$parent_id.'] is not yours to shape'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_INFO);

    $tables = ['{DBNICK}_info', '{DBNICK}_info_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The engine seats the node in the tree, and the rights of the parent come along
    $id = UTIL\TreeNodeAdd('info', $parent_id, $fields);

    UTIL\TablesUnlock($tables);

    if ( $id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The characteristic ['.$parent_id.'] is out of the tree, so nothing can go '.
                         'under it'
            ];
    }

    return [
        'result'  => true,
        'id'      => $id,
        'message' => 'The characteristic ['.$id.'] is under ['.$parent_id.']'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'RENAME_FAST_INFO', 'Changing a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = InfoAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The characteristics ['.$list.'] are not yours to shape: that is the right '.
                         'on the characteristic itself, given out in the program'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_INFO);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_info'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_info', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' characteristics changed: '.$changed
        ];
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'MOVE_FAST_INFO', 'Moving a characteristic');
    if ( $gate !== true ) return $gate;

    $id = $mParam['id'];
    $parent_id = $mParam['parent_id'];

    $named = InfoAllowed($mUserId, [$id, $parent_id]);
    if ( !in_array($id, $named) || ( $parent_id > 0 && !in_array($parent_id, $named) ) )
    {
        return [
            'result'  => false,
            'message' => 'Both the characteristic and the parent it goes under are yours to shape, '.
                         'or the move does not happen'
            ];
    }

    $tables = ['{DBNICK}_info'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = MELBIS()->SysTreeMove('info', $id, $parent_id);

    UTIL\TablesUnlock($tables);

    if ( !$done )
    {
        return [
            'result'  => false,
            'message' => 'The characteristic ['.$parent_id.'] sits inside the branch of ['.$id.
                         '], or is out of the tree: nothing was moved'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The characteristic ['.$id.'] is under ['.$parent_id.'] now, with its branch'
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'DELETE_FAST_INFO', 'Deleting a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = InfoAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The characteristics ['.$list.'] are not yours to shape'
            ];
    }

    // Walks the branch under every id: the whole of it goes, not the root alone
    $branch = [];
    foreach ( $ids as $id )
    {
        $mine = UTIL\TreeBranch('info', $id);
        foreach ( $mine as $one )
        {
            $branch[$one] = $one;
        }
    }
    // Counts what hangs on the branch by the map, and says it before anything is deleted
    $told = UTIL\DependCount('info', $branch);
    if ( !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => 'About to delete '.count($branch).' characteristic(s) with the branch under '.
                         'them'.$told['said'].'. Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_info', '{DBNICK}_info_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The door of the engine takes one node with its branch, and the call takes a list of them
    foreach ( $ids as $id )
    {
        MELBIS()->SysTreeDelete('info', $id);
    }

    UTIL\TablesUnlock($tables);

    // Sweeps the values, the rights and the links of the goods, by the map of the engine
    $swept = UTIL\DependSweep('info');

    return [
        'result'  => true,
        'message' => count($branch).' characteristic(s) gone with their branches'.
                     UTIL\DependSaid($swept)
        ];
}


/**
 * Function CmdValueList
 **/
function CmdValueList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_INFO_VALUE', 'Reading the values of a characteristic');
    if ( $gate !== true ) return $gate;

    $where = [];
    if ( isset($mParam['info_id']) )
    {
        $list = implode(',', $mParam['info_id']);
        $where[] = "info_id IN ( $list )";
    }
    if ( isset($mParam['id']) )
    {
        $list = implode(',', $mParam['id']);
        $where[] = "id IN ( $list )";
    }

    if ( count($where) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Name info_id to read the values of a characteristic, or id to read the '.
                         'named values - a live shop keeps thousands of them, so nothing answers all'
            ];
    }

    $filter = implode(' AND ', $where);

    $command = "SELECT *
                  FROM {DBNICK}_info_value
                 WHERE $filter
              ORDER BY info_id, pos
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($rows).' values',
        'tables'  => [
            'info_value' => $rows
            ]
        ];
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_VALUE', 'Shaping the values of a characteristic');
    if ( $gate !== true ) return $gate;

    $info_id = $mParam['info_id'];
    if ( count(InfoAllowed($mUserId, [$info_id], 'value')) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The values of ['.$info_id.'] are not yours to shape, or there is no such '.
                         'characteristic'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_VALUE);

    $tables = ['{DBNICK}_info_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_info_value
                 WHERE info_id = :INFO_ID
               ";
    $param_last = [
        'info_id' => $info_id
        ];
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_last);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('info_value');
    $row['info_id'] = $info_id;
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_info_value', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The value ['.$row['id'].'] is in the characteristic ['.$info_id.']'
        ];
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_VALUE', 'Shaping the values of a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = ValueAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The values ['.$list.'] stand on characteristics that are not yours to '.
                         'shape, or are not there at all'
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

    $tables = ['{DBNICK}_info_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_info_value', $row, 'id');
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
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_VALUE', 'Shaping the values of a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = ValueAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The values ['.$list.'] stand on characteristics that are not yours to '.
                         'shape, or are not there at all'
            ];
    }

    $list = implode(',', $ids);

    $told = UTIL\DependCount('info_value', $ids);
    if ( !$mParam['recursive'] )
    {
        return [
            'result'  => false,
            'message' => 'About to delete '.count($ids).' value(s)'.$told['said'].'. Say recursive to go on'
            ];
    }

    $tables = ['{DBNICK}_info_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_info_value
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps the links of the goods to those values, the table free again
    $swept = UTIL\DependSweep('info_value');

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
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_VALUE', 'Shaping the values of a characteristic');
    if ( $gate !== true ) return $gate;

    $info_id = $mParam['info_id'];
    if ( count(InfoAllowed($mUserId, [$info_id], 'value')) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'The values of ['.$info_id.'] are not yours to shape, or there is no such '.
                         'characteristic'
            ];
    }

    // The list here is one characteristic, and the rows put in order are its values
    $scope = [
        'info_id' => $info_id
        ];

    $tables = ['{DBNICK}_info_value'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = UTIL\Pos('info_value', $scope, $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The values of ['.$info_id.']: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    // Handing out rights is an operation of the engine, not a right on the characteristic
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_RIGHT',
                           'Handing out the rights on a characteristic');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_RIGHT);
    if ( !isset($fields['user_id']) && !isset($fields['group_id']) )
    {
        return [
            'result'  => false,
            'message' => 'A right belongs to a person or to a group: name user_id or group_id'
            ];
    }

    $tables = ['{DBNICK}_info_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Every grant answers its own id: a right is changed and taken back by it later
    $made = [];
    foreach ( $mParam['info_id'] as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('info_right');
        $row['info_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_info_right', $row);
        $made[] = $row['id'];
    }

    UTIL\TablesUnlock($tables);

    // One right is one id, and rights on several nodes are a list of them
    $said = ( count($made) == 1 ) ? $made[0] : $made;

    return [
        'result'  => true,
        'id'      => $said,
        'message' => 'The right ['.implode(',', $made).'] stands on '.count($mParam['info_id']).' characteristic(s)'
        ];
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_RIGHT',
                           'Handing out the rights on a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('info_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] of info_right - CmdList answers them with their ids'
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

    $tables = ['{DBNICK}_info_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_info_right', $row, 'id');
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
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_RIGHT',
                           'Handing out the rights on a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('info_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No rows ['.$list.'] of info_right'
            ];
    }

    $tables = ['{DBNICK}_info_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_info_right', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone. A characteristic with no rights left on it is seen '.
                     'only by whoever holds PUT_INFO_RIGHT'
        ];
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_KEY_SET', 'Setting the options of a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = InfoAllowed($mUserId, $mParam['info_id']);
    $lost = array_diff($mParam['info_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The characteristics ['.$list.'] are not yours to shape'
            ];
    }

    // The option and its value belong to the tree of this family, and a stray id is refused
    $option = UTIL\OptionPair('info', $mParam);
    if ( $option !== true ) return $option;

    $fields = UTIL\Only($mParam, FIELDS_KEY);

    $tables = ['{DBNICK}_info_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = MELBIS()->SqlGenId('info_key_set');
        $row['info_id'] = $id;
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_info_key_set', $row);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => 'The option stands on '.count($ids).' characteristic(s)'
        ];
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_KEY_SET', 'Setting the options of a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = KeyAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on characteristics that are not yours to shape, '.
                         'or are not there at all'
            ];
    }

    // The pair as it will stand after the change: a row keeps whatever the call left unsaid
    $list = implode(',', $ids);
    $command = "SELECT id, key_id, value_id
                  FROM {DBNICK}_info_key_set
                 WHERE id IN ( $list )
               ";
    $was_set = MELBIS()->SqlSelect(__LINE__, $command);
    foreach ( $was_set as $was )
    {
        $option = UTIL\OptionPair('info', $mParam, $was);
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

    $tables = ['{DBNICK}_info_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_info_key_set', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' rows changed: '.$changed
        ];
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_INFO_KEY_SET', 'Setting the options of a characteristic');
    if ( $gate !== true ) return $gate;

    $ids = KeyAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$list.'] stand on characteristics that are not yours to shape, '.
                         'or are not there at all'
            ];
    }

    $tables = ['{DBNICK}_info_key_set'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_info_key_set', $where);
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' rows gone'
        ];
}


/**
 * Function InfoAllowed
 **/
function InfoAllowed($mUserId, $mIds, $mPlace = 'info')
{
    // Reads the characteristics this person may shape, by the place the caller asks about
    $allow = UTIL\RightTable('info', $mUserId, $mPlace);
    $list = implode(',', $mIds);

    $command = "SELECT i.id
                  FROM {DBNICK}_info i
                  JOIN $allow ai
                    ON ai.id = i.id
                 WHERE i.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


/**
 * Function ValueAllowed
 **/
function ValueAllowed($mUserId, $mIds)
{
    // Reads the values standing on characteristics whose values this person may shape
    $allow = UTIL\RightTable('info', $mUserId, 'value');
    $list = implode(',', $mIds);

    $command = "SELECT iv.id
                  FROM {DBNICK}_info_value iv
                  JOIN $allow ai
                    ON ai.id = iv.info_id
                 WHERE iv.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}



/**
 * Function KeyAllowed
 **/
function KeyAllowed($mUserId, $mIds)
{
    // Reads the option rows standing on characteristics this person may shape
    $allow = UTIL\RightTable('info', $mUserId, 'info');
    $list = implode(',', $mIds);

    $command = "SELECT ks.id
                  FROM {DBNICK}_info_key_set ks
                  JOIN $allow ai
                    ON ai.id = ks.info_id
                 WHERE ks.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}


?>
