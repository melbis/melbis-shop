<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * InfoAllowed    - The characteristics of this person
 * ValueAllowed   - The values of such characteristics
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_INFO;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdListCut
 **/
function CmdListCut($mUserId, $mParam)
{
    return TABLE\Read('info');
}


/**
 * Function CmdListFull
 **/
function CmdListFull($mUserId, $mParam)
{
    $more = ['info_right', 'info_key_set'];

    return TABLE\Read('info', $more);
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    // The top belongs to all
    if ( $mParam['parent_id'] > 0 )
    {
        $said = InfoAllowed($mUserId, [$mParam['parent_id']], 'info');
        if ( !$said['result'] ) return $said;
    }

    return TABLE\TreeAdd($mUserId, 'info', $mParam);
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $said = InfoAllowed($mUserId, $mParam['id'], 'info');
    if ( !$said['result'] ) return $said;

    return TABLE\Update($mUserId, 'info', $said['ids'], $mParam);
}


/**
 * Function CmdMove
 **/
function CmdMove($mUserId, $mParam)
{
    // Both ends weighed, top free
    $weigh = [$mParam['id']];
    if ( $mParam['parent_id'] > 0 ) $weigh[] = $mParam['parent_id'];

    $said = InfoAllowed($mUserId, $weigh, 'info');
    if ( !$said['result'] ) return $said;

    return TABLE\TreeMove($mUserId, 'info', $mParam);
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $said = InfoAllowed($mUserId, $mParam['id'], 'info');
    if ( !$said['result'] ) return $said;

    return TABLE\TreeRemove($mUserId, 'info', $said['ids'], $mParam);
}


/**
 * Function CmdValueList
 **/
function CmdValueList($mUserId, $mParam)
{
    $where = [];
    if ( isset($mParam['info_id']) )
    {
        $list = implode(',', $mParam['info_id']);
        $where[] = "info_id IN ( $list )";
    }
    if ( isset($mParam['id']) )
    {
        $list = implode(',', $mParam['id']);
        $where[] = "id IN ( $list )";
    }

    if ( count($where) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Name info_id, or the ids to read'
            ];
    }

    $filter = implode(' AND ', $where);

    $command = "SELECT *
                  FROM {DBNICK}_info_value
                 WHERE $filter
              ORDER BY info_id, pos
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => 'The values asked for',
        'tables'  => [
            'info_value' => $rows
            ]
        ];
}


/**
 * Function CmdValueAdd
 **/
function CmdValueAdd($mUserId, $mParam)
{
    $said = InfoAllowed($mUserId, [$mParam['info_id']], 'value');
    if ( !$said['result'] ) return $said;

    return TABLE\Add($mUserId, 'info_value', $mParam);
}


/**
 * Function CmdValueUpdate
 **/
function CmdValueUpdate($mUserId, $mParam)
{
    $said = ValueAllowed($mUserId, $mParam['id']);
    if ( !$said['result'] ) return $said;

    return TABLE\Update($mUserId, 'info_value', $said['ids'], $mParam);
}


/**
 * Function CmdValueRemove
 **/
function CmdValueRemove($mUserId, $mParam)
{
    $said = ValueAllowed($mUserId, $mParam['id']);
    if ( !$said['result'] ) return $said;

    return TABLE\Remove($mUserId, 'info_value', $said['ids'], $mParam);
}


/**
 * Function CmdValuePos
 **/
function CmdValuePos($mUserId, $mParam)
{
    $said = InfoAllowed($mUserId, [$mParam['info_id']], 'value');
    if ( !$said['result'] ) return $said;

    // The values of one characteristic
    $scope['info_id'] = $mParam['info_id'];

    return TABLE\Pos($mUserId, 'info_value', $scope, $mParam);
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

    return TABLE\AddBlock($mUserId, 'info_right', 'info_id', $mParam['info_id'], $mParam);
}


/**
 * Function CmdRightUpdate
 **/
function CmdRightUpdate($mUserId, $mParam)
{
    return TABLE\Update($mUserId, 'info_right', $mParam['id'], $mParam);
}


/**
 * Function CmdRightRemove
 **/
function CmdRightRemove($mUserId, $mParam)
{
    return TABLE\Remove($mUserId, 'info_right', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyAdd
 **/
function CmdKeyAdd($mUserId, $mParam)
{
    return TABLE\KeySetAdd($mUserId, 'info', $mParam['info_id'], $mParam);
}


/**
 * Function CmdKeyUpdate
 **/
function CmdKeyUpdate($mUserId, $mParam)
{
    return TABLE\KeySetUpdate($mUserId, 'info', $mParam['id'], $mParam);
}


/**
 * Function CmdKeyRemove
 **/
function CmdKeyRemove($mUserId, $mParam)
{
    return TABLE\KeySetRemove($mUserId, 'info', $mParam['id']);
}


/**
 * Function InfoAllowed
 **/
function InfoAllowed($mUserId, $mIds, $mPlace)
{
    // The characteristics of this person
    $allow = SYS\RightTable('info', $mUserId, $mPlace);
    $list = implode(',', $mIds);

    $command = "SELECT i.id
                  FROM {DBNICK}_info i
                  JOIN $allow ai
                    ON ai.id = i.id
                 WHERE i.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The characteristics ['.$said.'] are not yours'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids
        ];
}


/**
 * Function ValueAllowed
 **/
function ValueAllowed($mUserId, $mIds)
{
    // The values of such characteristics
    $allow = SYS\RightTable('info', $mUserId, 'value');
    $list = implode(',', $mIds);

    $command = "SELECT iv.id
                  FROM {DBNICK}_info_value iv
                  JOIN $allow ai
                    ON ai.id = iv.info_id
                 WHERE iv.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The values ['.$said.'] are not yours'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids
        ];
}


?>
