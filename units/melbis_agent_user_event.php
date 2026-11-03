<?php
/***************************************************************************************************
 * @version 6.5.1.531 @ 2026-11-03
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Write    - Writes an event: its author, and its principal when one is named
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_USER_EVENT;


// ————— Public —————
//==================================================================================================


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $from_id = $mParam['from_id'];
    $key_name = $mParam['key_name'] ?? '';
    $state = $mParam['state'] ?? '';
    $limit = $mParam['limit'];
    $events = MELBIS()->SysUserEventList($from_id, $key_name, $state, $limit);

    // The next page starts from the last one
    $found = count($events);
    $last_id = $from_id;
    if ( $found > 0 )
    {
        $last = end($events);
        $last_id = $last['id'];
    }

    return [
        'result'  => true,
        'message' => $found.' event(s) after ['.$from_id.'], the next page from ['.$last_id.']',
        'detail'  => [
            'found'   => $found,
            'last_id' => $last_id
            ],
        'tables'  => [
            'user_event' => $events
            ]
        ];
}


/**
 * Function CmdLoad
 **/
function CmdLoad($mUserId, $mParam)
{
    $id = $mParam['id'];
    $event = MELBIS()->SysUserEventLoad($id);
    if ( empty($event) )
    {
        return [
            'result'  => false,
            'message' => 'No event ['.$id.'] in the journal: swept, or never written'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The event ['.$id.'] of ['.$event['key_name'].'] stands in ['.$event['state'].']',
        'tables'  => [
            'user_event' => [$event]
            ]
        ];
}


/**
 * Function CmdLast
 **/
function CmdLast($mUserId, $mParam)
{
    $key_name = $mParam['key_name'] ?? '';
    $state = $mParam['state'] ?? '';
    $before_time = $mParam['before_time'] ?? '';
    $event = MELBIS()->SysUserEventLast($key_name, $state, $before_time);

    // Nothing of that kind is an answer too
    if ( empty($event) )
    {
        return [
            'result'  => true,
            'message' => 'No such event in the journal',
            'detail'  => [
                'found' => 0
                ]
            ];
    }

    return [
        'result'  => true,
        'message' => 'The event ['.$event['id'].'] of ['.$event['key_name'].'] at '.$event['date_time'],
        'detail'  => [
            'found' => 1
            ],
        'tables'  => [
            'user_event' => [$event]
            ]
        ];
}


/**
 * Function CmdState
 **/
function CmdState($mUserId, $mParam)
{
    $id = $mParam['id'];
    $state = $mParam['state'];
    $from_state = $mParam['from_state'] ?? '';
    $said = MELBIS()->SysUserEventState($id, $state, $from_state);

    if ( $said == 'ABSENT' )
    {
        return [
            'result'  => false,
            'message' => 'No event ['.$id.'] in the journal'
            ];
    }

    // Of two takers one moves it
    if ( $said == 'TAKEN' )
    {
        return [
            'result'  => false,
            'message' => 'The event ['.$id.'] is not in ['.$from_state.']: someone took it first - leave it to them'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The event ['.$id.'] stands in ['.$state.'] now'
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam, $mAgentId)
{
    // The session signs the event and lends only whose rights it runs on
    $princ_id = null;
    if ( $mParam['princ'] ) $princ_id = $mUserId;

    return Write($mAgentId, $princ_id, $mParam);
}


/**
 * Function CmdAddPrinc
 **/
function CmdAddPrinc($mUserId, $mParam, $mAgentId)
{
    // The session signs the event, the principal is whoever is named
    $princ_id = $mParam['princ_id'];

    return Write($mAgentId, $princ_id, $mParam);
}


// ————— Private —————
//==================================================================================================


/**
 * Function Write
 **/
function Write($mUserId, $mPrincId, $mParam)
{
    $key_name = $mParam['key_name'];
    $state = $mParam['state'];
    $data = $mParam['data'] ?? [];
    $for_users = $mParam['for_users'] ?? [];
    $for_groups = $mParam['for_groups'] ?? [];
    $id = MELBIS()->SysUserEventAdd($mUserId, $mPrincId, $key_name, $state, $data, $for_users, $for_groups);

    $message = 'The event ['.$id.'] of ['.$key_name.'] is in the journal';
    if ( isset($mPrincId) ) $message .= ', with the principal ['.$mPrincId.']';

    return [
        'result'  => true,
        'message' => $message,
        'detail'  => [
            'id' => $id
            ]
        ];
}


?>
