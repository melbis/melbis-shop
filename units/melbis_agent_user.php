<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/**
 * Function MELBIS_AGENT_USER
 * The users of the store for the agent: ACT_SIGN answers the command map, ACT_DO routes the
 * command to its function. The engine has already weighed the command and the grant
 **/
function MELBIS_AGENT_USER($mAction, $mUserId, $mCommand, $mParam = [])
{
	if ( $mAction == 'ACT_SIGN' )
	{
		// Sign
		return [
			'result'   => true,
			'commands' => [
				'CMD_LIST'   => [
					'find'      => 'a substring of the login or the name; omitted - everyone'
					],
				'CMD_ADD'    => [
					'login'     => 'required; latin letters, digits and underscore, unique',
					'name'      => 'the name people see in the program',
					'group'     => 'required; id or name of the group',
					'add_group' => 'id or name of the additional group',
					'phone'     => '',
					'email'     => ''
					],
				'CMD_UPDATE' => [
					'id'        => 'required; take it from CMD_LIST',
					'name'      => '',
					'group'     => 'id or name',
					'add_group' => 'id or name; empty clears it',
					'phone'     => '',
					'email'     => '',
					'blocked'   => 'yes closes the door, no opens it back'
					],
				'CMD_REMOVE' => [
					'id'        => 'required; take it from CMD_LIST'
					]
				],
			'message'  => 'The commands of the tool with the fields each one takes'
			];
	}

	// Do
	switch ( $mCommand )
	{
		case 'CMD_LIST'   : return MELBIS()->UnitFunc('cmd_list', $mUserId, $mParam);
		case 'CMD_ADD'    : return MELBIS()->UnitFunc('cmd_add', $mUserId, $mParam);
		case 'CMD_UPDATE' : return MELBIS()->UnitFunc('cmd_update', $mUserId, $mParam);
		case 'CMD_REMOVE' : return MELBIS()->UnitFunc('cmd_remove', $mUserId, $mParam);
	}
}


/**
 * Function MELBIS_AGENT_USER_cmd_list
 * Everyone the store knows, with group names; reading needs no lock
 **/
function MELBIS_AGENT_USER_cmd_list($mUserId, $mParam)
{
	// An empty find turns the filter into "everyone": %% matches any login
	$find = trim($mParam['find'] ?? '');
	$like = '%'.$find.'%';

	$command = "SELECT u.id, u.login, u.name,
	                   u.group_id, ug_main.name AS group_name,
	                   u.add_group_id, ug_add.name AS add_group_name,
	                   u.is_blocked
	              FROM {DBNICK}_user u
	         LEFT JOIN {DBNICK}_user_group ug_main
	                ON ug_main.id = u.group_id
	         LEFT JOIN {DBNICK}_user_group ug_add
	                ON ug_add.id = u.add_group_id
	             WHERE u.login LIKE :FIND_LOGIN
	                OR u.name LIKE :FIND_NAME
	          ORDER BY u.id
	           ";
	$param_find = [
		'find_login' => $like,
		'find_name'  => $like
		];
	$rows = MELBIS()->SqlSelect(__LINE__, $command, $param_find);

	$users = [];
	foreach ( $rows as $row )
	{
		$users[] = [
			'id'        => $row['id'],
			'login'     => $row['login'],
			'name'      => $row['name'],
			'group_id'  => $row['group_id'],
			'group'     => $row['group_name'],
			'add_group' => $row['add_group_name'],
			'blocked'   => ( $row['is_blocked'] != 0 )
			];
	}

	$count = count($users);

	$message = 'The store has '.$count.' users';
	if ( $find != '' ) $message = $count.' users match ['.$find.']';

	return [
		'result'  => true,
		'count'   => $count,
		'users'   => $users,
		'message' => $message
		];
}


/**
 * Function MELBIS_AGENT_USER_cmd_add
 * New user of the store: the password is born here and answered once, only its md5 is kept
 **/
function MELBIS_AGENT_USER_cmd_add($mUserId, $mParam)
{
	// Login: required, latin letters, digits and underscore
	$login = trim($mParam['login'] ?? '');
	$is_clean = preg_match('/^[a-z0-9_]+$/i', $login);
	if ( !$is_clean )
	{
		return [
			'result'  => false,
			'message' => 'The login is required: latin letters, digits and underscore, no spaces'
			];
	}

	$command = "SELECT id
	              FROM {DBNICK}_user
	             WHERE login = :LOGIN
	           ";
	$param_login = [
		'login' => $login
		];
	$busy = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_login);
	if ( $busy != 0 )
	{
		return [
			'result'  => false,
			'message' => 'The login ['.$login.'] is taken already (id '.$busy.')'
			];
	}

	// The main group is required; id and name are both welcome
	$group = trim($mParam['group'] ?? '');
	$group_id = MELBIS()->UnitFunc('group_find', $group);
	if ( $group_id == 0 )
	{
		return MELBIS()->UnitFunc('group_refuse', $group);
	}

	// The additional group is optional, but a named one must exist
	$add_group_id = 0;
	$add_group = trim($mParam['add_group'] ?? '');
	if ( $add_group != '' )
	{
		$add_group_id = MELBIS()->UnitFunc('group_find', $add_group);
		if ( $add_group_id == 0 )
		{
			return MELBIS()->UnitFunc('group_refuse', $add_group);
		}
	}

	// A password without lookalike characters, kept as md5 only
	$alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';
	$max = strlen($alphabet) - 1;
	$password = '';
	for ( $i = 1; $i <= 12; $i++ )
	{
		$pick = random_int(0, $max);
		$password .= $alphabet[$pick];
	}
	$pass_code = md5($password);

	// Everything is checked, so the locked stretch holds the write alone
	$taken = MELBIS_INC_AGENT_lock(['{DBNICK}_user']);
	if ( !$taken['result'] ) return $taken;

	// The id is dealt by the generator: the user table has no auto increment
	$user_id = MELBIS()->SqlGenId('user');

	$name = trim($mParam['name'] ?? '');
	$phone = trim($mParam['phone'] ?? '');
	$email = trim($mParam['email'] ?? '');
	$fields = [
		'id'           => $user_id,
		'group_id'     => $group_id,
		'add_group_id' => $add_group_id,
		'login'        => $login,
		'pass_code'    => $pass_code,
		'name'         => $name,
		'phone'        => $phone,
		'email'        => $email
		];
	MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user', $fields);

	MELBIS()->SqlTableUnlock(__LINE__, ['{DBNICK}_user']);

	return [
		'result'  => true,
		'id'       => $user_id,
		'login'    => $login,
		'password' => $password,
		'message'  => 'User ['.$login.'] created, id '.$user_id.'. Temporary password: '.$password.
		              ' - hand it to the person and advise to change it after the first sign in. '.
		              'It is shown this once, do not store it anywhere.'
		];
}


/**
 * Function MELBIS_AGENT_USER_cmd_update
 * Changes the named fields of one user; every check runs first, the lock holds the write alone
 **/
function MELBIS_AGENT_USER_cmd_update($mUserId, $mParam)
{
	$id = trim($mParam['id'] ?? '');
	$is_number = ctype_digit($id);
	if ( !$is_number )
	{
		return [
			'result'  => false,
			'message' => 'The [id] field is required as a number - CMD_LIST shows the ids'
			];
	}

	$command = "SELECT id, login
	              FROM {DBNICK}_user
	             WHERE id = :USER_ID
	           ";
	$param_user = [
		'user_id' => $id
		];
	$found = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_user);
	if ( empty($found) )
	{
		return [
			'result'  => false,
			'message' => 'No user with id ['.$id.'] - CMD_LIST shows who is there'
			];
	}

	// What travels is what changes; the id alone changes nothing
	$fields = [
		'id' => $id
		];

	if ( isset($mParam['name']) ) $fields['name'] = trim($mParam['name']);
	if ( isset($mParam['phone']) ) $fields['phone'] = trim($mParam['phone']);
	if ( isset($mParam['email']) ) $fields['email'] = trim($mParam['email']);

	if ( isset($mParam['group']) )
	{
		$group = trim($mParam['group']);
		$group_id = MELBIS()->UnitFunc('group_find', $group);
		if ( $group_id == 0 )
		{
			return MELBIS()->UnitFunc('group_refuse', $group);
		}
		$fields['group_id'] = $group_id;
	}

	// An empty additional group clears it, a named one must exist
	if ( isset($mParam['add_group']) )
	{
		$add_group = trim($mParam['add_group']);
		$add_group_id = 0;
		if ( $add_group != '' )
		{
			$add_group_id = MELBIS()->UnitFunc('group_find', $add_group);
			if ( $add_group_id == 0 )
			{
				return MELBIS()->UnitFunc('group_refuse', $add_group);
			}
		}
		$fields['add_group_id'] = $add_group_id;
	}

	if ( isset($mParam['blocked']) )
	{
		$blocked = strtolower(trim($mParam['blocked']));
		if ( $blocked != 'yes' && $blocked != 'no' )
		{
			return [
				'result'  => false,
				'message' => 'The [blocked] field takes yes or no'
				];
		}
		// Blocking the person this very session works for cuts the branch under both of you
		if ( $blocked == 'yes' && $id == $mUserId )
		{
			return [
				'result'  => false,
				'message' => 'Refused: ['.$found['login'].'] is the authorized person of this session'
				];
		}
		$fields['is_blocked'] = ( $blocked == 'yes' ) ? '1' : '0';
	}

	$change_count = count($fields);
	if ( $change_count < 2 )
	{
		return [
			'result'  => false,
			'message' => 'Nothing to change: name the fields beside the id'
			];
	}

	$taken = MELBIS_INC_AGENT_lock(['{DBNICK}_user']);
	if ( !$taken['result'] ) return $taken;

	MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user', $fields, 'id');

	MELBIS()->SqlTableUnlock(__LINE__, ['{DBNICK}_user']);

	// Answer by the declared names, not by the column ones
	$names = array_keys($mParam);
	$names = array_diff($names, ['id']);
	$changed = implode(', ', $names);

	return [
		'result'  => true,
		'id'      => $id,
		'login'   => $found['login'],
		'message' => 'User ['.$found['login'].'] (id '.$id.') updated: '.$changed
		];
}


/**
 * Function MELBIS_AGENT_USER_cmd_remove
 * Deletes a user with no history behind them, their right rows included
 **/
function MELBIS_AGENT_USER_cmd_remove($mUserId, $mParam)
{
	$id = trim($mParam['id'] ?? '');
	$is_number = ctype_digit($id);
	if ( !$is_number )
	{
		return [
			'result'  => false,
			'message' => 'The [id] field is required as a number - CMD_LIST shows the ids'
			];
	}

	if ( $id == $mUserId )
	{
		return [
			'result'  => false,
			'message' => 'Refused: this is the authorized person of the session'
			];
	}

	$command = "SELECT id, login
	              FROM {DBNICK}_user
	             WHERE id = :USER_ID
	           ";
	$param_user = [
		'user_id' => $id
		];
	$found = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_user);
	if ( empty($found) )
	{
		return [
			'result'  => false,
			'message' => 'No user with id ['.$id.'] - CMD_LIST shows who is there'
			];
	}

	// History keeps the row: tasks, their notes and agent memory point at users by id
	$command = "SELECT COUNT(*) AS how
	              FROM {DBNICK}_user_task
	             WHERE user_id = :AUTHOR_ID
	                OR exec_id = :EXEC_ID
	           ";
	$param_task = [
		'author_id' => $id,
		'exec_id'   => $id
		];
	$tasks = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_task);

	$command = "SELECT COUNT(*) AS how
	              FROM {DBNICK}_user_task_note
	             WHERE user_id = :USER_ID
	           ";
	$notes = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_user);

	$command = "SELECT COUNT(*) AS how
	              FROM {DBNICK}_agent_memory
	             WHERE user_id = :USER_ID
	           ";
	$memory = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_user);

	$history = $tasks + $notes + $memory;
	if ( $history > 0 )
	{
		return [
			'result'  => false,
			'message' => 'User ['.$found['login'].'] carries history: '.$tasks.' tasks, '.$notes.
			             ' task notes, '.$memory.' memory notes. Deleting would orphan it - '.
			             'block the user instead: CMD_UPDATE, blocked=yes'
			];
	}

	$taken = MELBIS_INC_AGENT_lock(['{DBNICK}_user']);
	if ( !$taken['result'] ) return $taken;

	MELBIS()->SqlDelete(__LINE__, '{DBNICK}_user', 'id', $id);
	MELBIS()->SqlDelete(__LINE__, '{DBNICK}_oper_right', 'user_id', $id);
	MELBIS()->SqlDelete(__LINE__, '{DBNICK}_agent_tool_right', 'user_id', $id);

	MELBIS()->SqlTableUnlock(__LINE__, ['{DBNICK}_user']);

	return [
		'result'  => true,
		'id'      => $id,
		'login'   => $found['login'],
		'message' => 'User ['.$found['login'].'] (id '.$id.') removed, together with their right rows'
		];
}


/**
 * Function MELBIS_AGENT_USER_group_find
 * Group by id or by name; 0 when the store has no such group
 **/
function MELBIS_AGENT_USER_group_find($mGroup)
{
	$group = trim($mGroup);
	if ( $group == '' ) return 0;

	if ( ctype_digit($group) )
	{
		$command = "SELECT id
		              FROM {DBNICK}_user_group
		             WHERE id = :GROUP_ID
		           ";
		$param_group = [
			'group_id' => $group
			];
		return MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_group);
	}

	$command = "SELECT id
	              FROM {DBNICK}_user_group
	             WHERE name = :GROUP_NAME
	           ";
	$param_group = [
		'group_name' => $group
		];
	return MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_group);
}


/**
 * Function MELBIS_AGENT_USER_group_refuse
 * The refusal for a group miss, carrying the groups the store actually has
 **/
function MELBIS_AGENT_USER_group_refuse($mGroup)
{
	$command = "SELECT id, name
	              FROM {DBNICK}_user_group
	          ORDER BY pos
	           ";
	$groups = MELBIS()->SqlSelectStatic(__LINE__, $command);

	$known = [];
	foreach ( $groups as $row )
	{
		$known[] = $row['id'].' - '.$row['name'];
	}
	$list = implode('; ', $known);

	return [
		'result'  => false,
		'message' => 'No group ['.$mGroup.'] in this store. The groups are: '.$list
		];
}


?>
