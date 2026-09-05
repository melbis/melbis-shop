<?php
/***************************************************************************************************
 * @version 6.5.1.426 @ 2026-09-05
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * TaskAllowed - The tasks of this person
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_TASK;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    // Author, executor and the owner
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

    // The count of every feed
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
        'message' => 'The tasks this person may see',
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
    // Every field is a column
    $fields = $mParam;
    unset($fields['content']);

    $tables = ['{DBNICK}_user_task', '{DBNICK}_user_task_note'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // This person is the author
    $now = MELBIS()->DateTime();
    $row = $fields;
    $row['user_id'] = $mUserId;
    $row['state_key'] = 'kNew';
    $row['date_time'] = $now;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task', $row);
    $task_id = MELBIS()->SqlLastInsertId();

    // The first note of feed
    $note = [
        'task_id'   => $task_id,
        'user_id'   => $mUserId,
        'kind_key'  => $row['kind_key'],
        'state_key' => 'kNew',
        'content'   => $mParam['content'] ?? '',
        'date_time' => $now
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $note);

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'id'      => $task_id,
        'message' => 'The task is in the scheduler'
        ];
}


/**
 * Function CmdNoteList
 **/
function CmdNoteList($mUserId, $mParam)
{
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
        'message' => 'The notes in the feeds asked for',
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
    $named = TaskAllowed($mUserId, [$mParam['task_id']]);
    if ( !$named['result'] ) return $named;

    $task = reset($named['rows']);

    $tables = ['{DBNICK}_user_task', '{DBNICK}_user_task_note'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // Any state but kComment moves
    $state = $mParam['state_key'];
    // The note carries the rest
    $moved = $mParam;
    unset($moved['task_id'], $moved['content'], $moved['state_key']);
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

    SYS\TablesUnlock($tables, $mUserId);

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
    // The tasks this person sees
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
            'message' => 'No tasks ['.$said.'] of yours'
            ];
    }

    return [
        'result' => true,
        'rows'   => $rows
        ];
}


?>
