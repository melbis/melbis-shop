<?php
/***************************************************************************************************
 * @version 6.5.1.550 @ 2026-11-25
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * TaskAllowed - The tasks of this person
 * TaskHeld    - The task in these hands
 * TaskWrite   - The note, the task moving
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER_TASK;

// The states with doors elsewhere
const STATE_DOOR = [
    'kNew'     => 'CmdAdd',
    'kComment' => 'CmdNoteAdd',
    'kTrans'   => 'CmdPass',
    'kExplain' => 'CmdPass',
    'kDone'    => 'CmdDone',
    'kClose'   => 'CmdClose'
    ];


// ————— Public —————
//==================================================================================================

// Tasks
//==================================================================================================


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    // Open, author, executor and the owner
    $command = "SELECT *
                  FROM {DBNICK}_user_task
                 WHERE state_key <> 'kClose'
                   AND ( privy = 0
                         OR user_id = :ME
                         OR exec_id = :ME
                         OR :ME = 1 )
              ORDER BY id
               ";
    $param_task = [
        'me' => $mUserId
        ];
    $tasks = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

    // Counted against this person
    $held = 0;
    $given = 0;
    foreach ( $tasks as $task )
    {
        if ( $task['exec_id'] == $mUserId ) $held++;
        elseif ( $task['user_id'] == $mUserId ) $given++;
    }
    $others = count($tasks) - $held - $given;

    $message = count($tasks).' open task(s): '.$held.' in your hands (exec_id '.$mUserId.'), '.
               $given.' given by you (user_id '.$mUserId.'), '.$others.' of others';

    return [
        'result'  => true,
        'message' => $message,
        'tables'  => [
            'user_task' => $tasks
            ]
        ];
}


/**
 * Function CmdListClosed
 **/
function CmdListClosed($mUserId, $mParam)
{
    // Closed, of that person
    $command = "SELECT *
                  FROM {DBNICK}_user_task
                 WHERE state_key = 'kClose'
                   AND ( user_id = :WHO
                         OR exec_id = :WHO )
                   AND ( privy = 0
                         OR user_id = :ME
                         OR exec_id = :ME
                         OR :ME = 1 )
              ORDER BY id
               ";
    $param_task = [
        'who' => $mParam['user_id'],
        'me'  => $mUserId
        ];
    $tasks = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

    return [
        'result'  => true,
        'message' => count($tasks).' closed task(s) of the person ['.$mParam['user_id'].']',
        'tables'  => [
            'user_task' => $tasks
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam, $mAgentId = null, $mPrincId = null)
{
    // Every field is a column
    $row = $mParam;
    unset($row['content']);

    // The principal of the session sets the task, otherwise this person; the work starts now unless given
    $now = MELBIS()->DateTime();
    $row['user_id'] = $mPrincId ?? $mUserId;
    $row['state_key'] = 'kNew';
    if ( !isset($row['begin_time']) ) $row['begin_time'] = $now;
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task', $row);
    $task_id = MELBIS()->SqlLastInsertId();

    // The first note of feed is signed by whoever wrote it
    $note = [
        'task_id'   => $task_id,
        'user_id'   => $mAgentId ?? $mUserId,
        'kind_key'  => $row['kind_key'],
        'state_key' => 'kNew',
        'content'   => $mParam['content'] ?? '',
        'date_time' => $now
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $note);

    return [
        'result'  => true,
        'message' => 'The task is in the scheduler',
        'detail'  => [
            'id' => $task_id
            ]
        ];
}


/**
 * Function CmdState
 **/
function CmdState($mUserId, $mParam, $mAgentId = null, $mPrincId = null)
{
    // The states with doors elsewhere
    $state = $mParam['state_key'];
    if ( array_key_exists($state, STATE_DOOR) )
    {
        return [
            'result'  => false,
            'message' => 'The state ['.$state.'] is written by '.STATE_DOOR[$state]
            ];
    }

    $held = TaskHeld($mUserId, $mPrincId, $mParam['task_id']);
    if ( !$held['result'] ) return $held;

    // Kind and privacy go along
    $moved = $mParam;
    unset($moved['task_id'], $moved['content']);
    $content = $mParam['content'] ?? '';

    return TaskWrite($mUserId, $held['task'], $moved, $content);
}


/**
 * Function CmdPass
 **/
function CmdPass($mUserId, $mParam, $mAgentId = null, $mPrincId = null)
{
    // Passed on, or asked about
    $state = $mParam['state_key'];
    if ( $state != 'kTrans' && $state != 'kExplain' )
    {
        return [
            'result'  => false,
            'message' => 'A task is passed with kTrans or kExplain, and ['.$state.'] is neither'
            ];
    }

    $held = TaskHeld($mUserId, $mPrincId, $mParam['task_id']);
    if ( !$held['result'] ) return $held;

    // Never to its executor
    $task = $held['task'];
    if ( $mParam['exec_id'] == $task['exec_id'] )
    {
        return [
            'result'  => false,
            'message' => 'A task is passed to someone else; its executor keeps it by CmdState'
            ];
    }

    $moved = [
        'state_key' => $state,
        'exec_id'   => $mParam['exec_id']
        ];
    $content = $mParam['content'] ?? '';

    return TaskWrite($mUserId, $task, $moved, $content);
}


/**
 * Function CmdDone
 **/
function CmdDone($mUserId, $mParam, $mAgentId = null, $mPrincId = null)
{
    $held = TaskHeld($mUserId, $mPrincId, $mParam['task_id']);
    if ( !$held['result'] ) return $held;

    // Done goes back to author
    $task = $held['task'];
    $moved = [
        'state_key' => 'kDone',
        'exec_id'   => $task['user_id']
        ];
    $content = $mParam['content'] ?? '';

    return TaskWrite($mUserId, $task, $moved, $content);
}


/**
 * Function CmdClose
 **/
function CmdClose($mUserId, $mParam, $mAgentId = null, $mPrincId = null)
{
    $named = TaskAllowed($mUserId, [$mParam['task_id']]);
    if ( !$named['result'] ) return $named;

    // Its author or their agent
    $task = reset($named['rows']);
    if ( $task['user_id'] != $mUserId && $task['user_id'] != $mPrincId )
    {
        return [
            'result'  => false,
            'message' => 'The task ['.$task['id'].'] is closed by its author ['.$task['user_id'].'] or their agent'
            ];
    }

    $moved = [
        'state_key' => 'kClose'
        ];
    $content = $mParam['content'] ?? '';

    return TaskWrite($mUserId, $task, $moved, $content);
}


// Notes
//==================================================================================================


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

    // A comment moves nothing
    $task = reset($named['rows']);
    $moved = [
        'state_key' => 'kComment'
        ];

    return TaskWrite($mUserId, $task, $moved, $mParam['content']);
}


// ————— Private —————
//==================================================================================================


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
                         OR user_id = :ME
                         OR exec_id = :ME
                         OR :ME = 1 )
               ";
    $param_task = [
        'me' => $mUserId
        ];
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_task);

    $found = array_column($rows, 'id');
    $lost = array_diff($mIds, $found);
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


/**
 * Function TaskHeld
 **/
function TaskHeld($mUserId, $mPrincId, $mTaskId)
{
    $named = TaskAllowed($mUserId, [$mTaskId]);
    if ( !$named['result'] ) return $named;

    // Its executor or their agent
    $task = reset($named['rows']);
    if ( $task['exec_id'] != $mUserId && $task['exec_id'] != $mPrincId )
    {
        return [
            'result'  => false,
            'message' => 'The task ['.$task['id'].'] is in the hands of ['.$task['exec_id'].'], moved only by them or their agent; a comment is CmdNoteAdd'
            ];
    }

    return [
        'result' => true,
        'task'   => $task
        ];
}


/**
 * Function TaskWrite
 **/
function TaskWrite($mUserId, $mTask, $mMoved, $mContent)
{
    // A comment leaves the task
    $state = $mMoved['state_key'];
    if ( $state != 'kComment' )
    {
        $row = $mMoved;
        $row['id'] = $mTask['id'];
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_user_task', $row, 'id');
    }

    $note = [
        'task_id'   => $mTask['id'],
        'user_id'   => $mUserId,
        'kind_key'  => $mMoved['kind_key'] ?? $mTask['kind_key'],
        'state_key' => $state,
        'content'   => $mContent,
        'date_time' => MELBIS()->DateTime()
        ];
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_user_task_note', $note);
    $note_id = MELBIS()->SqlLastInsertId();

    // Where the task stands now
    $message = 'The note ['.$note_id.'] is in the feed of the task ['.$mTask['name'].']';
    if ( $state != 'kComment' ) $message .= ', and the task moved to ['.$state.']';
    if ( isset($mMoved['exec_id']) ) $message .= ' into the hands of ['.$mMoved['exec_id'].']';

    return [
        'result'  => true,
        'message' => $message,
        'detail'  => [
            'id' => $note_id
            ]
        ];
}


?>
