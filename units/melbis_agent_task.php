<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList     - Reads every task this person may see, each with the count of its notes
 * CmdAdd      - Adds a task in kNew and opens its feed with the first note
 * CmdNoteList - Reads the whole feed of the given tasks
 * CmdNoteAdd  - Writes a note into the feed; any state but kComment moves the task with it
 *
 * TaskAllowed - Of the tasks asked for, the ones this person may see
 *
 * Add-only, the way the program keeps it: a task is never edited or deleted, it moves by its notes
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_TASK;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a task; the author, the state and the clock are stamped here
const FIELDS_TASK = "name, exec_id, kind_key, privy";

// The columns a note may carry over into the task it moves
const FIELDS_MOVE = "exec_id, kind_key, privy";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_SCHED_TASK', 'Reading the scheduler');
    if ( $gate !== true ) return $gate;

    // A private task is read by its author, its executor and the person with id 1
    $command = "SELECT *
                  FROM {DBNICK}_user_task
                 WHERE ( privy = 0
                         OR user_id = :ME_AUTHOR
                         OR exec_id = :ME_EXEC
                         OR :ME_ADMIN = 1 )
              ORDER BY id
               ";
    $param_task = [
        'me_author' => $mUserId,
        'me_exec'   => $mUserId,
        'me_admin'  => $mUserId
        ];
    $tasks = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

    // Counts the notes of every task, so a read of a feed is asked for knowingly
    $command = "SELECT utn.task_id, COUNT(*) AS notes_how
                  FROM {DBNICK}_user_task_note utn
                  JOIN {DBNICK}_user_task ut
                    ON ut.id = utn.task_id
                 WHERE ( ut.privy = 0
                         OR ut.user_id = :ME_AUTHOR
                         OR ut.exec_id = :ME_EXEC
                         OR :ME_ADMIN = 1 )
              GROUP BY utn.task_id
               ";
    $how = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

    return [
        'result'  => true,
        'message' => count($tasks).' task(s) this person may see, closed ones too - state_key '.
                     'kClose tells them apart; CmdNoteList answers the feeds',
        'tables'  => [
            'user_task'  => $tasks,
            'task_notes' => $how
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'ADD_SCHED_TASK', 'Adding a task');
    if ( $gate !== true ) return $gate;

    $fields = UTIL\Only($mParam, FIELDS_TASK);

    $tables = ['{DBNICK}_user_task', '{DBNICK}_user_task_note'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Stamps the person of this session as the author and opens the task in kNew
    $now = MELBIS()->DateTime();
    $row = $fields;
    $row['user_id'] = $mUserId;
    $row['state_key'] = 'kNew';
    $row['date_time'] = $now;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task', $row);
    $task_id = MELBIS()->SqlLastInsertId();

    // Writes the first note of the feed, the way the program opens a task
    $note = [
        'task_id'   => $task_id,
        'user_id'   => $mUserId,
        'kind_key'  => $row['kind_key'],
        'state_key' => 'kNew',
        'content'   => $mParam['content'] ?? '',
        'date_time' => $now
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $note);

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'id'      => $task_id,
        'message' => 'The task ['.$mParam['name'].'] is in the scheduler, id '.$task_id.
                     ', on the person ['.$row['exec_id'].']'
        ];
}


/**
 * Function CmdNoteList
 **/
function CmdNoteList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_SCHED_NOTE', 'Reading the feed of a task');
    if ( $gate !== true ) return $gate;

    $named = TaskAllowed($mUserId, $mParam['task_id']);
    if ( !$named['result'] ) return $named;

    $list = implode(',', $mParam['task_id']);

    $command = "SELECT *
                  FROM {DBNICK}_user_task_note
                 WHERE task_id IN ( $list )
              ORDER BY task_id, id
               ";
    $notes = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($notes).' note(s) in the feed of '.count($mParam['task_id']).' task(s)',
        'tables'  => [
            'user_task_note' => $notes
            ]
        ];
}


/**
 * Function CmdNoteAdd
 **/
function CmdNoteAdd($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'ADD_SCHED_NOTE', 'Writing into the feed of a task');
    if ( $gate !== true ) return $gate;

    $named = TaskAllowed($mUserId, [$mParam['task_id']]);
    if ( !$named['result'] ) return $named;

    $task = reset($named['rows']);

    $tables = ['{DBNICK}_user_task', '{DBNICK}_user_task_note'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // kComment leaves the task where it is; any other state moves it, and kClose ends it
    $state = $mParam['state_key'];
    $moved = UTIL\Only($mParam, FIELDS_MOVE);
    if ( $state != 'kComment' )
    {
        $row = $moved;
        $row['id'] = $task['id'];
        $row['state_key'] = $state;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user_task', $row, 'id');
    }

    $note = [
        'task_id'   => $task['id'],
        'user_id'   => $mUserId,
        'kind_key'  => $mParam['kind_key'] ?? $task['kind_key'],
        'state_key' => $state,
        'content'   => $mParam['content'],
        'date_time' => MELBIS()->DateTime()
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $note);
    $note_id = MELBIS()->SqlLastInsertId();

    UTIL\TablesUnlock($tables);

    $message = 'The note ['.$note_id.'] is in the feed of the task ['.$task['name'].']';
    if ( $state != 'kComment' )
    {
        $message .= ', and the task moved to ['.$state.']';
        $said = array_keys($moved);
        if ( count($said) > 0 ) $message .= ' with '.implode(', ', $said);
    }

    return [
        'result'  => true,
        'id'      => $note_id,
        'message' => $message
        ];
}


/**
 * Function TaskAllowed
 **/
function TaskAllowed($mUserId, $mIds)
{
    // Reads the tasks this person may see and refuses the rest without telling them apart
    $list = implode(',', $mIds);

    $command = "SELECT *
                  FROM {DBNICK}_user_task
                 WHERE id IN ( $list )
                   AND ( privy = 0
                         OR user_id = :ME_AUTHOR
                         OR exec_id = :ME_EXEC
                         OR :ME_ADMIN = 1 )
               ";
    $param_task = [
        'me_author' => $mUserId,
        'me_exec'   => $mUserId,
        'me_admin'  => $mUserId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

    $lost = array_diff($mIds, array_column($rows, 'id'));
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No tasks ['.$said.'] in the scheduler, or they are not yours to see'
            ];
    }

    return [
        'result' => true,
        'rows'   => $rows
        ];
}


?>
