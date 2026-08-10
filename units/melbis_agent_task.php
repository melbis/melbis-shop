<?php
/***************************************************************************************************
 * @version 6.5.0.370 @ 2026-08-10
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/**
 * Function MELBIS_AGENT_TASK
 * The scheduler for the agent, the same four doors the program has: tasks and their feed,
 * listing and adding. ACT_SIGN answers the command map, ACT_DO routes the command
 **/
function MELBIS_AGENT_TASK($mAction, $mUserId, $mCommand, $mParam = [])
{
 if ( $mAction == 'ACT_SIGN' )
 {
  // Sign
  return [
   'result'   => true,
   'commands' => [
    'CMD_TASK_LIST' => [
     'closed'     => 'yes - the last closed tasks instead of the open ones'
     ],
    'CMD_TASK_ADD'  => [
     'name'       => 'required; one line of what to do',
     'exec_login' => 'required; the login of the executor',
     'kind'       => 'a TASK_KIND_KEY value; kDefault when omitted',
     'note'       => 'the full text of the job, the first entry of the task feed',
     'privy'      => 'yes makes the task visible to the author, the executor and the administrator only'
     ],
    'CMD_NOTE_LIST' => [
     'task_id'    => 'the task to read; omitted - the notes of every open task'
     ],
    'CMD_NOTE_ADD'  => [
     'task_id'    => 'required; take it from CMD_TASK_LIST',
     'note'       => 'required; the text of the entry',
     'state'      => 'a TASK_STATE_KEY value moves the task; omitted - kComment, the entry moves nothing',
     'exec_login' => 'a new executor when the task changes hands',
     'kind'       => 'a new TASK_KIND_KEY value',
     'privy'      => 'yes or no changes who sees the task'
     ]
    ],
   'message'  => 'The commands of the tool with the fields each one takes'
   ];
 }

 // Do
 switch ( $mCommand )
 {
  case 'CMD_TASK_LIST' : return MELBIS()->UnitFunc('cmd_task_list', $mUserId, $mParam);
  case 'CMD_TASK_ADD'  : return MELBIS()->UnitFunc('cmd_task_add', $mUserId, $mParam);
  case 'CMD_NOTE_LIST' : return MELBIS()->UnitFunc('cmd_note_list', $mUserId, $mParam);
  case 'CMD_NOTE_ADD'  : return MELBIS()->UnitFunc('cmd_note_add', $mUserId, $mParam);
 }
}


/**
 * Function MELBIS_AGENT_TASK_cmd_task_list
 * Open tasks the person may see: public ones plus their own; the administrator sees all.
 * closed=yes turns the list into the archive
 **/
function MELBIS_AGENT_TASK_cmd_task_list($mUserId, $mParam)
{
 $closed = strtolower(trim($mParam['closed'] ?? ''));
 if ( $closed == 'yes' )
 {
  return MELBIS()->UnitFunc('task_closed', $mUserId);
 }

 $command = "SELECT ut.id, ut.name, ut.kind_key, ut.state_key, ut.privy, ut.date_time,
                    u_author.login AS author, u_exec.login AS executor
               FROM {DBNICK}_user_task ut
          LEFT JOIN {DBNICK}_user u_author
                 ON u_author.id = ut.user_id
          LEFT JOIN {DBNICK}_user u_exec
                 ON u_exec.id = ut.exec_id
              WHERE ut.state_key <> 'kClose'
                AND ( ut.privy = 0 OR ut.user_id = :ME_AUTHOR OR ut.exec_id = :ME_EXEC OR :ME_ADMIN = 1 )
           ORDER BY ut.id
            ";
 $param_task = [
  'me_author' => $mUserId,
  'me_exec'   => $mUserId,
  'me_admin'  => $mUserId
  ];
 $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

 $tasks = MELBIS()->UnitFunc('task_rows', $rows);
 $count = count($tasks);

 return [
  'result'  => true,
  'count'   => $count,
  'tasks'   => $tasks,
  'message' => 'The scheduler holds '.$count.' open tasks'
  ];
}


/**
 * Function MELBIS_AGENT_TASK_cmd_task_add
 * New task: the person behind the session is the author, the task feed opens with its first note
 **/
function MELBIS_AGENT_TASK_cmd_task_add($mUserId, $mParam)
{
 $name = trim($mParam['name'] ?? '');
 if ( $name == '' )
 {
  return [
   'result'  => false,
   'message' => 'The [name] field is required: one line of what to do'
   ];
 }

 // The executor comes as a login; a miss answers with the logins the store has
 $exec = MELBIS()->UnitFunc('exec_find', $mParam['exec_login'] ?? '');
 if ( !$exec['result'] ) return $exec;

 // The kind must live in the TASK_KIND_KEY dictionary; a miss answers with its values
 $kind = trim($mParam['kind'] ?? '');
 if ( $kind == '' ) $kind = 'kDefault';
 $kind_set = MELBIS_INC_AGENT_key_value_code('TASK_KIND_KEY');
 $kinds = array_column($kind_set, 'key_name');
 if ( !in_array($kind, $kinds) )
 {
  $known = implode(', ', $kinds);
  return [
   'result'  => false,
   'message' => 'No kind ['.$kind.'] in the registry. The values are: '.$known
   ];
 }

 $privy = strtolower(trim($mParam['privy'] ?? ''));
 if ( $privy != '' && $privy != 'yes' && $privy != 'no' )
 {
  return [
   'result'  => false,
   'message' => 'The [privy] field takes yes or no'
   ];
 }
 $is_privy = ( $privy == 'yes' ) ? '1' : '0';

 // The parser clock: plain date() would miss the timezone of the store
 $note = trim($mParam['note'] ?? '');
 $now = MELBIS()->DateTime();

 // Everything is checked, so the locked stretch holds the two writes alone
 $taken = MELBIS_INC_AGENT_lock(['{DBNICK}_user_task', '{DBNICK}_user_task_note']);
 if ( !$taken['result'] ) return $taken;

 $fields = [
  'user_id'   => $mUserId,
  'exec_id'   => $exec['id'],
  'name'      => $name,
  'kind_key'  => $kind,
  'state_key' => 'kNew',
  'privy'     => $is_privy,
  'date_time' => $now
  ];
 MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task', $fields);
 $task_id = MELBIS()->SqlLastInsertId();

 // The task feed opens with the first note, the way the program itself writes one
 $fields = [
  'task_id'   => $task_id,
  'user_id'   => $mUserId,
  'kind_key'  => $kind,
  'state_key' => 'kNew',
  'content'   => $note,
  'date_time' => $now
  ];
 MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $fields);

 MELBIS()->SqlTableUnlock(__LINE__, ['{DBNICK}_user_task', '{DBNICK}_user_task_note']);

 return [
  'result'  => true,
  'id'      => $task_id,
  'message' => 'Task ['.$name.'] created, id '.$task_id.', assigned to ['.$exec['login'].'] as ['.$kind.']'
  ];
}


/**
 * Function MELBIS_AGENT_TASK_cmd_note_list
 * The feed: one task with its notes by task_id, or the notes of every open task without it
 **/
function MELBIS_AGENT_TASK_cmd_note_list($mUserId, $mParam)
{
 $task_id = trim($mParam['task_id'] ?? '');
 if ( $task_id != '' )
 {
  $is_number = ctype_digit($task_id);
  if ( !$is_number )
  {
   return [
    'result'  => false,
    'message' => 'The [task_id] field takes a number - CMD_TASK_LIST shows the ids'
    ];
  }

  return MELBIS()->UnitFunc('note_task', $mUserId, $task_id);
 }

 // No task named - the last notes across every open task the person may see
 $command = "SELECT utn.task_id, utn.kind_key, utn.state_key, utn.content, utn.date_time,
                    u_author.login AS author, ut.name AS task_name
               FROM {DBNICK}_user_task_note utn
               JOIN {DBNICK}_user_task ut
                 ON ut.id = utn.task_id
          LEFT JOIN {DBNICK}_user u_author
                 ON u_author.id = utn.user_id
              WHERE ut.state_key <> 'kClose'
                AND ( ut.privy = 0 OR ut.user_id = :ME_AUTHOR OR ut.exec_id = :ME_EXEC OR :ME_ADMIN = 1 )
           ORDER BY utn.id DESC
            ";
 $param_note = [
  'me_author' => $mUserId,
  'me_exec'   => $mUserId,
  'me_admin'  => $mUserId
  ];
 $page = MELBIS()->SqlSelectLimit(__LINE__, $command, 0, 100, $param_note);

 $rows = $page['rows'];
 $total = $page['total'];

 $notes = [];
 foreach ( $rows as $row )
 {
  $notes[] = [
   'task_id' => $row['task_id'],
   'task'    => $row['task_name'],
   'author'  => $row['author'],
   'kind'    => $row['kind_key'],
   'state'   => $row['state_key'],
   'text'    => $row['content'],
   'created' => $row['date_time']
   ];
 }
 $count = count($notes);

 return [
  'result'  => true,
  'count'   => $count,
  'total'   => $total,
  'notes'   => $notes,
  'message' => 'The last '.$count.' of '.$total.' notes across the open tasks'
  ];
}



/**
 * Function MELBIS_AGENT_TASK_cmd_note_add
 * An entry into the task feed, one door for both faces the way the program writes them:
 * kComment moves nothing, any other state moves the task after the entry
 **/
function MELBIS_AGENT_TASK_cmd_note_add($mUserId, $mParam)
{
 $task_id = trim($mParam['task_id'] ?? '');
 $is_number = ctype_digit($task_id);
 if ( !$is_number )
 {
  return [
   'result'  => false,
   'message' => 'The [task_id] field is required as a number - CMD_TASK_LIST shows the ids'
   ];
 }

 $note = trim($mParam['note'] ?? '');
 if ( $note == '' )
 {
  return [
   'result'  => false,
   'message' => 'The [note] field is required: the text of the entry'
   ];
 }

 // Writing goes only into a task the person may see
 $command = "SELECT ut.id, ut.name, ut.exec_id, ut.kind_key, ut.state_key, ut.privy
               FROM {DBNICK}_user_task ut
              WHERE ut.id = :TASK_ID
                AND ( ut.privy = 0 OR ut.user_id = :ME_AUTHOR OR ut.exec_id = :ME_EXEC OR :ME_ADMIN = 1 )
            ";
 $param_task = [
  'task_id'   => $task_id,
  'me_author' => $mUserId,
  'me_exec'   => $mUserId,
  'me_admin'  => $mUserId
  ];
 $task = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_task);
 if ( empty($task) )
 {
  return [
   'result'  => false,
   'message' => 'No task with id ['.$task_id.'], or it is not yours to see'
   ];
 }

 // The state names the move; omitted it is a comment and the task stands still
 $state = trim($mParam['state'] ?? '');
 if ( $state == '' ) $state = 'kComment';
 $state_set = MELBIS_INC_AGENT_key_value_code('TASK_STATE_KEY');
 $states = array_column($state_set, 'key_name');
 if ( !in_array($state, $states) )
 {
  $known = implode(', ', $states);
  return [
   'result'  => false,
   'message' => 'No state ['.$state.'] in the registry. The values are: '.$known
   ];
 }

 // The named fields move with the task, the silent ones keep their current values
 $exec_id = $task['exec_id'];
 $exec_login = '';
 if ( isset($mParam['exec_login']) )
 {
  $exec = MELBIS()->UnitFunc('exec_find', $mParam['exec_login']);
  if ( !$exec['result'] ) return $exec;
  $exec_id = $exec['id'];
  $exec_login = $exec['login'];
 }

 $kind = trim($mParam['kind'] ?? '');
 if ( $kind == '' ) $kind = $task['kind_key'];
 $kind_set = MELBIS_INC_AGENT_key_value_code('TASK_KIND_KEY');
 $kinds = array_column($kind_set, 'key_name');
 if ( !in_array($kind, $kinds) )
 {
  $known = implode(', ', $kinds);
  return [
   'result'  => false,
   'message' => 'No kind ['.$kind.'] in the registry. The values are: '.$known
   ];
 }

 $is_privy = $task['privy'];
 $privy = strtolower(trim($mParam['privy'] ?? ''));
 if ( $privy != '' )
 {
  if ( $privy != 'yes' && $privy != 'no' )
  {
   return [
    'result'  => false,
    'message' => 'The [privy] field takes yes or no'
    ];
  }
  $is_privy = ( $privy == 'yes' ) ? '1' : '0';
 }

 $now = MELBIS()->DateTime();

 // Everything is checked, so the locked stretch holds the writes alone
 $taken = MELBIS_INC_AGENT_lock(['{DBNICK}_user_task', '{DBNICK}_user_task_note']);
 if ( !$taken['result'] ) return $taken;

 // The program's own line: a comment moves nothing, any other state moves the task
 if ( $state != 'kComment' )
 {
  $fields = [
   'id'        => $task_id,
   'exec_id'   => $exec_id,
   'kind_key'  => $kind,
   'state_key' => $state,
   'privy'     => $is_privy
   ];
  MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user_task', $fields, 'id');
 }

 $fields = [
  'task_id'   => $task_id,
  'user_id'   => $mUserId,
  'kind_key'  => $kind,
  'state_key' => $state,
  'content'   => $note,
  'date_time' => $now
  ];
 MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $fields);
 $note_id = MELBIS()->SqlLastInsertId();

 MELBIS()->SqlTableUnlock(__LINE__, ['{DBNICK}_user_task', '{DBNICK}_user_task_note']);

 $message = 'A note is written into the feed of task ['.$task['name'].'] (id '.$task_id.')';
 if ( $state != 'kComment' )
 {
  $message = 'Task ['.$task['name'].'] (id '.$task_id.') moved to ['.$state.'], the note is in its feed';
  if ( $exec_login != '' ) $message .= ', executor now ['.$exec_login.']';
 }

 return [
  'result'  => true,
  'id'      => $note_id,
  'task_id' => $task_id,
  'state'   => $state,
  'message' => $message
  ];
}


/**
 * Function MELBIS_AGENT_TASK_exec_find
 * The executor by login: result=true with id and login, or a ready refusal carrying the
 * logins the store has
 **/
function MELBIS_AGENT_TASK_exec_find($mLogin)
{
 $exec_login = trim($mLogin);
 $command = "SELECT id, login, is_blocked
               FROM {DBNICK}_user
              WHERE login = :LOGIN
            ";
 $param_exec = [
  'login' => $exec_login
  ];
 $exec = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_exec);
 if ( empty($exec) )
 {
  $command = "SELECT login
                FROM {DBNICK}_user
               WHERE is_blocked = 0
            ORDER BY id
             ";
  $people = MELBIS()->SqlSelect(__LINE__, $command);
  $logins = array_column($people, 'login');
  $known = implode(', ', $logins);
  return [
   'result'  => false,
   'message' => 'No user with the login ['.$exec_login.']. The store has: '.$known
   ];
 }
 if ( $exec['is_blocked'] != 0 )
 {
  return [
   'result'  => false,
   'message' => 'The user ['.$exec_login.'] is blocked - a task cannot be assigned to them'
   ];
 }

 return [
  'result' => true,
  'id'     => $exec['id'],
  'login'  => $exec['login']
  ];
}


/**
 * Function MELBIS_AGENT_TASK_task_closed
 * The archive: the last hundred closed tasks the person may see
 **/
function MELBIS_AGENT_TASK_task_closed($mUserId)
{
 $command = "SELECT ut.id, ut.name, ut.kind_key, ut.state_key, ut.privy, ut.date_time,
                    u_author.login AS author, u_exec.login AS executor
               FROM {DBNICK}_user_task ut
          LEFT JOIN {DBNICK}_user u_author
                 ON u_author.id = ut.user_id
          LEFT JOIN {DBNICK}_user u_exec
                 ON u_exec.id = ut.exec_id
              WHERE ut.state_key = 'kClose'
                AND ( ut.privy = 0 OR ut.user_id = :ME_AUTHOR OR ut.exec_id = :ME_EXEC OR :ME_ADMIN = 1 )
           ORDER BY ut.id DESC
            ";
 $param_task = [
  'me_author' => $mUserId,
  'me_exec'   => $mUserId,
  'me_admin'  => $mUserId
  ];
 $page = MELBIS()->SqlSelectLimit(__LINE__, $command, 0, 100, $param_task);

 $rows = $page['rows'];
 $total = $page['total'];
 $tasks = MELBIS()->UnitFunc('task_rows', $rows);
 $count = count($tasks);

 return [
  'result'  => true,
  'count'   => $count,
  'total'   => $total,
  'tasks'   => $tasks,
  'message' => 'The last '.$count.' of '.$total.' closed tasks'
  ];
}


/**
 * Function MELBIS_AGENT_TASK_task_rows
 * Task rows the way the agent reads them, shared by both list branches
 **/
function MELBIS_AGENT_TASK_task_rows($mRows)
{
 $tasks = [];
 foreach ( $mRows as $row )
 {
  $tasks[] = [
   'id'       => $row['id'],
   'name'     => $row['name'],
   'kind'     => $row['kind_key'],
   'state'    => $row['state_key'],
   'author'   => $row['author'],
   'executor' => $row['executor'],
   'private'  => ( $row['privy'] != 0 ),
   'created'  => $row['date_time']
   ];
 }

 return $tasks;
}


/**
 * Function MELBIS_AGENT_TASK_note_task
 * One task with its whole feed; privacy holds the same line the lists hold
 **/
function MELBIS_AGENT_TASK_note_task($mUserId, $mTaskId)
{
 $command = "SELECT ut.id, ut.name, ut.kind_key, ut.state_key, ut.privy, ut.date_time,
                    u_author.login AS author, u_exec.login AS executor
               FROM {DBNICK}_user_task ut
          LEFT JOIN {DBNICK}_user u_author
                 ON u_author.id = ut.user_id
          LEFT JOIN {DBNICK}_user u_exec
                 ON u_exec.id = ut.exec_id
              WHERE ut.id = :TASK_ID
                AND ( ut.privy = 0 OR ut.user_id = :ME_AUTHOR OR ut.exec_id = :ME_EXEC OR :ME_ADMIN = 1 )
            ";
 $param_task = [
  'task_id'   => $mTaskId,
  'me_author' => $mUserId,
  'me_exec'   => $mUserId,
  'me_admin'  => $mUserId
  ];
 $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_task);
 if ( empty($row) )
 {
  return [
   'result'  => false,
   'message' => 'No task with id ['.$mTaskId.'], or it is not yours to see'
   ];
 }

 $rows = [$row];
 $tasks = MELBIS()->UnitFunc('task_rows', $rows);
 $task = $tasks[0];

 $command = "SELECT utn.kind_key, utn.state_key, utn.content, utn.date_time,
                    u_author.login AS author
               FROM {DBNICK}_user_task_note utn
          LEFT JOIN {DBNICK}_user u_author
                 ON u_author.id = utn.user_id
              WHERE utn.task_id = :TASK_ID
           ORDER BY utn.id
            ";
 $param_note = [
  'task_id' => $mTaskId
  ];
 $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_note);

 $notes = [];
 foreach ( $rows as $row )
 {
  $notes[] = [
   'author'  => $row['author'],
   'kind'    => $row['kind_key'],
   'state'   => $row['state_key'],
   'text'    => $row['content'],
   'created' => $row['date_time']
   ];
 }
 $count = count($notes);

 return [
  'result'  => true,
  'task'    => $task,
  'notes'   => $notes,
  'count'   => $count,
  'message' => 'Task ['.$task['name'].'] with '.$count.' feed entries'
  ];
}


?>
