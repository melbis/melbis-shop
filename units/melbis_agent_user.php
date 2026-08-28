<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * People         - The people with their tables
 * Login          - Weighs a login
 * Password       - A password of clear letters
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_TABLE as TABLE;

// The columns a read gives
const FIELDS_READ = "id, group_id, add_group_id, login, name, phone, email, params, is_blocked";


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return People(['user_group']);
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    $more = ['user_group', 'oper', 'oper_right', 'oper_table',
             'user_key_set', 'user_group_key_set'];

    return People($more);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $weighed = Login($mParam['login'], 0);
    if ( !$weighed['result'] ) return $weighed;

    $password = Password();

    $tables = ['{DBNICK}_user'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // Every field is a column
    $row = $mParam;
    $row['id'] = MELBIS()->SqlGenId('user');
    $row['pass_code'] = md5($password);
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user', $row);

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'   => true,
        'id'       => $row['id'],
        'password' => $password,
        'message' => 'The person ['.$mParam['login'].'] is in the shop'
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $ids = $mParam['id'];

    // A login weighed against taken
    if ( isset($mParam['login']) )
    {
        if ( count($ids) > 1 )
        {
            return [
                'result'  => false,
                'message' => 'A login belongs to one person'
                ];
        }

        $weighed = Login($mParam['login'], reset($ids));
        if ( !$weighed['result'] ) return $weighed;
    }

    return TABLE\Update($mUserId, 'user', $ids, $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $ids = $mParam['id'];

    if ( in_array($mUserId, $ids) )
    {
        return [
            'result'  => false,
            'message' => 'This session does not remove itself'
            ];
    }

    // History is blocked, never deleted
    $told = SYS\DependCount('user', $ids);
    if ( $told['count'] > 0 )
    {
        return [
            'result'  => false,
            'message' => 'Those people carry history'.$told['said'].' - block them'
            ];
    }

    return TABLE\Remove($mUserId, 'user', $ids, $mParam);
}


/**
 * Function CmdPassword
 **/
function CmdPassword($mUserId, $mParam)
{
    $password = Password();

    $tables = ['{DBNICK}_user'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $row = [
        'id'        => $mParam['id'],
        'pass_code' => md5($password)
        ];
    MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user', $row, 'id');

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'   => true,
        'password' => $password,
        'message' => 'A new password of ['.$mParam['id'].'], said once'
        ];
}


/**
 * Function CmdGroupAdd
 **/
function CmdGroupAdd($mUserId, $mParam)
{
    return TABLE\Add($mUserId, 'user_group', $mParam);
}


/**
 * Function CmdGroupUpdate
 **/
function CmdGroupUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'user_group', $mParam['id'], $mParam);
}


/**
 * Function CmdGroupRemove
 **/
function CmdGroupRemove($mUserId, $mParam)
{
    $list = implode(',', $mParam['id']);

    // A group with people stays
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
            'message' => $people.' person(s) would lose their group'
            ];
    }

    return TABLE\Remove($mUserId, 'user_group', $mParam['id'], $mParam);
}


/**
 * Function CmdGroupPos
 **/
function CmdGroupPos($mUserId, $mParam)
{
    return TABLE\Pos($mUserId, 'user_group', [], $mParam);
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    // A right names one owner
    $user_id = $mParam['user_id'] ?? 0;
    $group_id = $mParam['group_id'] ?? 0;
    if ( $user_id == 0 && $group_id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Name user_id or group_id'
            ];
    }

    return TABLE\Add($mUserId, 'oper_right', $mParam);
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'oper_right', $mParam['id'], $mParam);
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'oper_right', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'user', $mParam['user_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'user', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'user', $mParam['id']);
}


/**
 * Function CmdGroupKeyAdd
 **/
function CmdGroupKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'user_group', $mParam['user_group_id'], $mParam);
}


/**
 * Function CmdGroupKeyUpdate
 **/
function CmdGroupKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'user_group', $mParam['id'], $mParam);
}


/**
 * Function CmdGroupKeyRemove
 **/
function CmdGroupKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'user_group', $mParam['id']);
}


/**
 * Function People
 **/
function People($mMore)
{
    // Read by name of column
    $fields = FIELDS_READ;

    $command = "SELECT $fields
                  FROM {DBNICK}_user
               ";
    $users = MELBIS()->SqlSelect(__LINE__, $command);

    $said = TABLE\Read($mMore[0], array_slice($mMore, 1));
    $tables = $said['tables'];
    $tables['user'] = $users;

    return [
        'result'  => true,
        'message' => 'The people of the shop',
        'tables'  => $tables
        ];
}


/**
 * Function Login
 **/
function Login($mLogin, $mSelfId)
{
    // The shape, then the taken
    $login = trim((string)$mLogin);
    $is_clean = preg_match('/^[a-z0-9_]+$/i', $login);
    if ( !$is_clean )
    {
        return [
            'result'  => false,
            'message' => 'A login takes latin, digits, underscore'
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
            'message' => 'The login ['.$mParam['login'].'] is taken'
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
    // Letters nobody misreads aloud
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
