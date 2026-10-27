<?php
/***************************************************************************************************
 * @version 6.5.1.522 @ 2026-10-27
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_EVENT;


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
            'message' => 'No event ['.$id.'] in the journal: swept, or a test run of the Dispatcher'
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
function CmdAdd($mUserId, $mParam)
{
    $key_name = $mParam['key_name'];
    $data = $mParam['data'] ?? [];
    $state = $mParam['state'];
    $user_ids = $mParam['user_ids'] ?? [];
    $group_ids = $mParam['group_ids'] ?? [];
    $id = MELBIS()->SysUserEventAdd($key_name, $data, $state, $user_ids, $group_ids);

    return [
        'result'  => true,
        'message' => 'The event ['.$id.'] of ['.$key_name.'] is in the journal',
        'detail'  => [
            'id' => $id
            ]
        ];
}


?>
