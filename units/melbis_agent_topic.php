<?php
/***************************************************************************************************
 * @version 6.5.1.419 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * TopicAllowed   - The sections of this person
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_TOPIC;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('topic');
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    $more = ['topic_right', 'topic_alt', 'topic_key_set'];

    return TABLE\Read('topic', $more);
}



/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    // The parent is weighed
    $said = TopicAllowed($mUserId, [$mParam['parent_id']]);
    if ( !$said['result'] ) return $said;

    return TABLE\TreeAdd($mUserId, 'topic', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $said = TopicAllowed($mUserId, $mParam['id']);
    if ( !$said['result'] ) return $said;

    return TABLE\Update($mUserId, 'topic', $said['ids'], $mParam);
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    // Both ends are weighed
    $said = TopicAllowed($mUserId, [$mParam['id'], $mParam['parent_id']]);
    if ( !$said['result'] ) return $said;

    return TABLE\TreeMove($mUserId, 'topic', $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $said = TopicAllowed($mUserId, $mParam['id']);
    if ( !$said['result'] ) return $said;

    return TABLE\TreeRemove($mUserId, 'topic', $said['ids'], $mParam);
}


/**
 * Function CmdRightAdd
 **/
function CmdRightAdd($mUserId, $mParam)
{
    // A right names one owner
    if ( !isset($mParam['user_id']) && !isset($mParam['group_id']) )
    {
        return [
            'result'  => false,
            'message' => 'Name user_id or group_id'
            ];
    }

    return TABLE\AddBlock($mUserId, 'topic_right', 'topic_id', $mParam['topic_id'], $mParam);
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'topic_right', $mParam['id'], $mParam);
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'topic_right', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'topic', $mParam['topic_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'topic', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'topic', $mParam['id']);
}


/**
 * Function CmdAltAdd
 **/
function CmdAltAdd($mUserId, $mParam)
{
    // The kind is the scope
    $scope['kind_key'] = $mParam['kind_key'];

    return TABLE\TreeAdd($mUserId, 'topic_alt', $mParam, $scope);
}


/**
 * Function CmdAltUpdate
 **/
function CmdAltUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'topic_alt', $mParam['id'], $mParam);
}


/**
 * Function CmdAltMove
 **/
function CmdAltMove($mUserId, $mParam)
{
    // A node moves inside itself
    $scope['kind_key'] = $mParam['kind_key'];

    return TABLE\TreeMove($mUserId, 'topic_alt', $mParam, $scope);
}


/**
 * Function CmdAltRemove
 **/
function CmdAltRemove($mUserId, $mParam)
{
    // One catalogue, kind the scope
    $scope['kind_key'] = $mParam['kind_key'];

    return TABLE\TreeRemove($mUserId, 'topic_alt', $mParam['id'], $mParam, $scope);
}


/**
 * Function TopicAllowed
 **/
function TopicAllowed($mUserId, $mIds)
{
    // The sections of this person
    $allow = SYS\RightTable('topic', $mUserId, 'place');
    $list = implode(',', $mIds);

    $command = "SELECT t.id
                  FROM {DBNICK}_topic t
                  JOIN $allow at
                    ON at.id = t.id
                 WHERE t.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The sections ['.$said.'] are not yours'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids
        ];
}


?>
