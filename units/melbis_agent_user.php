<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList        - Reads the people, the groups, the operations and the rights
 * CmdAdd         - Adds a person with a password born here and said once
 * CmdUpdate      - Changes the given columns of people, by id
 * CmdRemove      - Deletes people by id, refusing while they carry history
 * CmdPassword    - Gives one person a new password and says it once
 * CmdGroupAdd    - Adds a group at the end of the list
 * CmdGroupUpdate - Changes the given columns of groups, by id
 * CmdGroupRemove - Deletes groups by id, refusing while people stand in them
 * CmdGroupPos    - Reorders the groups by POS, MOVE or SORT
 * CmdRightAdd    - Grants an operation to a person or to a group
 * CmdRightUpdate - Changes the given columns of rights, by id
 * CmdRightRemove - Takes rights back, by id
 *
 * Login          - Weighs the shape of a login and the ones already taken
 * Password       - Builds a password of letters that cannot be mistaken for one another
 *
 * A password is kept as md5 and said once; the tree of the operations belongs to the engine
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a person; the password is never one of them
const FIELDS_USER = "login, name, group_id, add_group_id, phone, email, params, is_blocked";

// The columns a read gives back; the md5 of the password is not among them
const FIELDS_READ = "id, group_id, add_group_id, login, name, phone, email, params, is_blocked";

// The columns a call may write into a group
const FIELDS_GROUP = "skey, name, phone, email, pos";

// The columns a call may write into a right: an operation, and the one it is given to
const FIELDS_RIGHT = "oper_id, user_id, group_id";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_USERS', 'Reading the people of the shop');
    if ( $gate !== true ) return $gate;

    $fields = FIELDS_READ;

    $command = "SELECT $fields
                  FROM {DBNICK}_user
              ORDER BY id
               ";
    $users = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_user_group
              ORDER BY pos
               ";
    $groups = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_oper
              ORDER BY absindex
               ";
    $opers = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_oper_right
              ORDER BY id
               ";
    $rights = MELBIS()->SqlSelect(__LINE__, $command);

    $command = "SELECT *
                  FROM {DBNICK}_oper_table
              ORDER BY oper_id, pos
               ";
    $tables = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($users).' people, '.count($groups).' groups, '.count($opers).
                     ' operations, '.count($rights).' rights',
        'tables'  => [
            'user'       => $users,
            'user_group' => $groups,
            'oper'       => $opers,
            'oper_right' => $rights,
            'oper_table' => $tables
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Adding a person to the shop');
    if ( $gate !== true ) return $gate;

    $weighed = Login($mParam['login'], 0);
    if ( !$weighed['result'] ) return $weighed;

    $fields = UTIL\Only($mParam, FIELDS_USER);
    $password = Password();

    $tables = ['{DBNICK}_user'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('user');
    $row['pass_code'] = md5($password);
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'   => true,
        'id'       => $row['id'],
        'password' => $password,
        'message'  => 'The person ['.$mParam['login'].'] is in the shop, id '.$row['id'].
                      '. The password is said once and kept as md5 only: hand it over, advise to '.
                      'change it, and store it nowhere. Rights are given out with CmdRightAdd'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Changing a person of the shop');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('user', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No people ['.$list.'] in the shop - CmdList answers who is there'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_USER);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    // A login belongs to one person, so a change of it is weighed against the ones taken
    if ( isset($fields['login']) )
    {
        if ( count($ids) > 1 )
        {
            return [
                'result'  => false,
                'message' => 'A login belongs to one person: name one id when the login changes'
                ];
        }

        $weighed = Login($fields['login'], reset($ids));
        if ( !$weighed['result'] ) return $weighed;
    }

    $tables = ['{DBNICK}_user'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' people changed: '.$changed
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Removing a person of the shop');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('user', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No people ['.$list.'] in the shop'
            ];
    }

    if ( in_array($mUserId, $ids) )
    {
        return [
            'result'  => false,
            'message' => 'The person of this session is not removed by it'
            ];
    }

    $list = implode(',', $ids);

    // Counts the history a person carries: deleting would orphan it, so they are blocked instead
    $told = UTIL\DependCount('user', $ids);
    if ( $told['count'] > 0 )
    {
        return [
            'result'  => false,
            'message' => 'Those people carry history'.$told['said'].'. Deleting would orphan it - '.
                         'block them instead, CmdUpdate with is_blocked'
            ];
    }

    $tables = ['{DBNICK}_user'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_user
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps the rights of the gone people, by the map of the engine
    $swept = UTIL\DependSweep('user');

    $message = count($ids).' people gone. What they wrote in the shop keeps their id: an order '.
               'line, a version of a file, a task';
    $message .= UTIL\DependSaid($swept);

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdPassword
 **/
function CmdPassword($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Setting a password of a person');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('user', [$mParam['id']]);
    if ( count($ids) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No person ['.$mParam['id'].'] in the shop'
            ];
    }

    $password = Password();

    $tables = ['{DBNICK}_user'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $row = [
        'id'        => $mParam['id'],
        'pass_code' => md5($password)
        ];
    MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user', $row, 'id');

    UTIL\TablesUnlock($tables);

    return [
        'result'   => true,
        'password' => $password,
        'message'  => 'A new password of the person ['.$mParam['id'].'], said once and kept as md5 '.
                      'only. The old one stops working at once'
        ];
}


/**
 * Function CmdGroupAdd
 **/
function CmdGroupAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Shaping the groups of the people');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_GROUP);

    $tables = ['{DBNICK}_user_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_user_group
               ";
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('user_group');
    if ( !isset($row['pos']) ) $row['pos'] = $last + 1;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_group', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The group ['.$row['id'].'] is in the list. Its rights are given out with '.
                     'CmdRightAdd'
        ];
}


/**
 * Function CmdGroupUpdate
 **/
function CmdGroupUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Shaping the groups of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('user_group', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No groups ['.$list.'] in the shop'
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

    $tables = ['{DBNICK}_user_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user_group', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' groups changed: '.$changed
        ];
}


/**
 * Function CmdGroupRemove
 **/
function CmdGroupRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Shaping the groups of the people');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('user_group', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No groups ['.$list.'] in the shop'
            ];
    }

    $list = implode(',', $ids);

    // Counts the people standing in those groups: a group with any of them is not deleted
    $command = "SELECT COUNT(*)
                  FROM {DBNICK}_user
                 WHERE group_id IN ( $list )
                    OR add_group_id IN ( $list )
               ";
    $people = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);

    if ( $people > 0 )
    {
        return [
            'result'  => false,
            'message' => $people.' person(s) stand in those groups and would be left without one - '.
                         'move them first, CmdUpdate with group_id'
            ];
    }

    $tables = ['{DBNICK}_user_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_user_group
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps the rights given to the gone groups, by the map of the engine
    $swept = UTIL\DependSweep('user_group');

    $message = count($ids).' group(s) gone';
    $message .= UTIL\DependSaid($swept);

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdGroupPos
 **/
function CmdGroupPos($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Shaping the groups of the people');
    if ( $gate !== true ) return $gate;

    $tables = ['{DBNICK}_user_group'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // One list for the whole table, so the scope is empty
    $done = UTIL\Pos('user_group', [], $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The groups: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Giving out a right');
    if ( $gate !== true ) return $gate;

    // A right stands on an operation of the program, so the operation is weighed first
    $oper_id = $mParam['oper_id'];
    if ( count(UTIL\Exists('oper', [$oper_id])) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No operation ['.$oper_id.'] in the program - CmdList answers them in the oper table'
            ];
    }

    // A right belongs to a person or to a group, and to one that is really there
    $user_id = $mParam['user_id'] ?? 0;
    $group_id = $mParam['group_id'] ?? 0;
    if ( $user_id == 0 && $group_id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'A right belongs to a person or to a group: name user_id or group_id'
            ];
    }
    if ( $user_id != 0 && count(UTIL\Exists('user', [$user_id])) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No people ['.$user_id.'] in the shop - CmdList answers who is there'
            ];
    }
    if ( $group_id != 0 && count(UTIL\Exists('user_group', [$group_id])) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No groups ['.$group_id.'] in the shop - CmdList answers them'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_RIGHT);

    $tables = ['{DBNICK}_oper_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $row = $fields;
    $row['id'] = MELBIS()->SqlGenId('oper_right');
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_oper_right', $row);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The right ['.$row['id'].'] is given out'
        ];
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Changing a right');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('oper_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No right rows ['.$list.'] in the shop'
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

    $tables = ['{DBNICK}_oper_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_oper_right', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($ids).' right(s) changed: '.$changed
        ];
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'PUT_USERS', 'Taking a right back');
    if ( $gate !== true ) return $gate;

    $ids = UTIL\Exists('oper_right', $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No right rows ['.$list.'] in the shop'
            ];
    }

    $list = implode(',', $ids);

    $tables = ['{DBNICK}_oper_right'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_oper_right
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' right(s) taken back'
        ];
}





/**
 * Function Login
 **/
function Login($mLogin, $mSelfId)
{
    // Weighs the shape of a login before the base sees it, then the logins already taken
    $login = trim((string)$mLogin);
    $is_clean = preg_match('/^[a-z0-9_]+$/i', $login);
    if ( !$is_clean )
    {
        return [
            'result'  => false,
            'message' => 'A login is latin letters, digits and underscore, and ['.$login.'] is not'
            ];
    }

    $command = "SELECT id
                  FROM {DBNICK}_user
                 WHERE login = :LOGIN
                   AND id <> :SELF_ID
               ";
    $param_login = [
        'login'   => $login,
        'self_id' => $mSelfId
        ];
    $busy = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_login);
    if ( $busy != 0 )
    {
        return [
            'result'  => false,
            'message' => 'The login ['.$login.'] is taken already, by the person ['.$busy.']'
            ];
    }

    return [
        'result' => true
        ];
}


/**
 * Function Password
 **/
function Password()
{
    // Letters that cannot be mistaken for one another when read out: no 0, no 1, no l, no O
    $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
    $max = strlen($alphabet) - 1;

    $password = '';
    for ( $i = 0; $i < 12; $i++ )
    {
        $pick = random_int(0, $max);
        $password .= $alphabet[$pick];
    }

    return $password;
}


?>
