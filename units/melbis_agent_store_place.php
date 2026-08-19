<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList     - Reads the links of the given sections or goods, with the goods themselves
 * CmdLink     - Hangs goods into a section, each at the end of it
 * CmdUpdate   - Moves links into another section, each at the end of it
 * CmdUnlink   - Deletes links and names the goods left hanging nowhere
 * CmdPos      - Reorders the goods inside one section
 *
 * LinkAllowed - Of the links asked for, the ones in sections this person may place in
 *
 * The rows of topic_store and nothing else; the right is for_ctrl, given out on the section
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_PLACE;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;
use MELBIS_INC_AGENT_STORE as STORE;

// The columns every answer here carries, the set the Location window of the program loads
const FIELDS_READ = "s.id, s.provider_id, s.brand_id, s.code_shop, s.code_prov, s.code_made,
                s.name, s.no_visible, s.status_key, s.kind_key, s.state_key, s.clann,
                s.clann_title, s.clann_root, s.relate_id, s.rating, s.how, s.price,
                s.price_curr_id, s.create_time, s.update_time, s.exist_time, s.award_cnt,
                s.award_avg";

// The columns a call may write into a link; the order of a whole list is CmdPos
const FIELDS_WRITE = "topic_id, pos";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $where = [];
    $join = '';
    if ( isset($mParam['topic_id']) )
    {
        if ( $mParam['topic_sub'] )
        {
            // The branch of every section walked by the base, as a table the read joins
            $branch = UTIL\TreeBranchTable('topic', $mParam['topic_id']);
            $join = "JOIN $branch b ON b.id = ts.topic_id";
        }
        else
        {
            $list = implode(',', $mParam['topic_id']);
            $where[] = "ts.topic_id IN ( $list )";
        }
    }
    if ( isset($mParam['store_id']) )
    {
        $list = implode(',', $mParam['store_id']);
        $where[] = "ts.store_id IN ( $list )";
    }

    if ( count($where) == 0 && $join == '' )
    {
        return [
            'result'  => false,
            'message' => 'Name a section to see what stands in it, or a goods to see where it hangs'
            ];
    }

    $allow = UTIL\RightTable('topic', $mUserId, 'place');
    if ( count($where) == 0 ) $where[] = '1';
    $filter = implode(' AND ', $where);

    $command = "SELECT ts.*
                  FROM {DBNICK}_topic_store ts
                  JOIN $allow at
                    ON at.id = ts.topic_id
                       $join
                 WHERE $filter
              ORDER BY ts.topic_id, ts.pos
               ";
    $links = MELBIS()->SqlSelect(__LINE__, $command);

    if ( count($links) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing of yours hangs there'
            ];
    }

    $ids = array_column($links, 'store_id');
    $ids = array_unique($ids);
    $list = implode(',', $ids);
    $fields = FIELDS_READ;

    $command = "SELECT $fields
                  FROM {DBNICK}_store s
                 WHERE s.id IN ( $list )
              ORDER BY s.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return [
        'result'  => true,
        'message' => count($links).' links on '.count($rows).' goods',
        'tables'  => [
            'topic_store' => $links,
            'store'       => $rows
            ]
        ];
}


/**
 * Function CmdLink
 **/
function CmdLink($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !UTIL\RightOne('topic', $mUserId, 'place', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to place in'
            ];
    }

    $hang = [];
    foreach ( $mParam['store_id'] as $id )
    {
        $hang[] = [
            'id'   => $id,
            'name' => $id
            ];
    }

    $tables = ['{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = STORE\Link($topic_id, $hang);

    UTIL\TablesUnlock($tables);

    $message = count($done['hung']).' goods hang in the section ['.$topic_id.'] now';
    if ( count($done['stood']) > 0 )
    {
        $said = implode(', ', $done['stood']);
        $message .= '. Already there: '.$said;
    }

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $ids = LinkAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The links ['.$list.'] are in sections that are not yours to place in, or '.
                         'are not there at all - CmdList answers the links with their ids'
            ];
    }

    $fields = UTIL\Only($mParam, FIELDS_WRITE);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Name the section to move them into, or the place to put them at'
            ];
    }

    if ( isset($fields['topic_id']) && !UTIL\RightOne('topic', $mUserId, 'place', $fields['topic_id']) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$fields['topic_id'].'] is not yours to place in'
            ];
    }

    $tables = ['{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // Puts a moved link at the end of the section it comes to
    $command = "SELECT MAX(pos)
                  FROM {DBNICK}_topic_store
                 WHERE topic_id = :TOPIC_ID
               ";
    $param_last = [
        'topic_id' => $fields['topic_id'] ?? 0
        ];
    $last = MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_last);

    foreach ( $ids as $id )
    {
        $last++;
        $row = $fields;
        $row['id'] = $id;
        if ( !isset($row['pos']) ) $row['pos'] = $last;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_topic_store', $row, 'id');
    }

    UTIL\TablesUnlock($tables);

    return [
        'result'  => true,
        'message' => count($ids).' links changed: '.implode(', ', array_keys($fields))
        ];
}


/**
 * Function CmdUnlink
 **/
function CmdUnlink($mUserId, $mParam)
{
    $ids = LinkAllowed($mUserId, $mParam['id']);
    $lost = array_diff($mParam['id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The links ['.$list.'] are in sections that are not yours to place in, or '.
                         'are not there at all'
            ];
    }

    $list = implode(',', $ids);
    $command = "SELECT store_id
                  FROM {DBNICK}_topic_store
                 WHERE id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);
    $goods = array_column($rows, 'store_id');

    $tables = ['{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    foreach ( $ids as $id )
    {
        $where = [
            'id' => $id
            ];
        MELBIS()->SqlDelete(__LINE__, '{DBNICK}_topic_store', $where);
    }

    UTIL\TablesUnlock($tables);

    // Names the goods left in no section: only the Recovery tool reaches them now
    $list = implode(',', $goods);
    $command = "SELECT s.id
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                 WHERE s.id IN ( $list )
                   AND ts.id IS NULL
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $message = count($ids).' links gone';
    if ( count($rows) > 0 )
    {
        $gone = array_column($rows, 'id');
        $said = implode(', ', $gone);
        $message .= '. The goods ['.$said.'] hang nowhere now and only the Recovery tool sees them';
    }

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !UTIL\RightOne('topic', $mUserId, 'place', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to place in'
            ];
    }

    // The list here is one section, and the rows put in order are its links
    $scope = [
        'topic_id' => $topic_id
        ];
    $data = $mParam['data'] ?? [];

    $tables = ['{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = UTIL\Pos('topic_store', $scope, $mParam['type'], $data);

    UTIL\TablesUnlock($tables);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The section ['.$topic_id.']: '.$done['said'].', '.$done['moved'].' row(s) moved'
        ];
}


/**
 * Function LinkAllowed
 **/
function LinkAllowed($mUserId, $mIds)
{
    // Reads the links that stand in sections this person may place in
    $allow = UTIL\RightTable('topic', $mUserId, 'place');
    $list = implode(',', $mIds);

    $command = "SELECT ts.id
                  FROM {DBNICK}_topic_store ts
                  JOIN $allow at
                    ON at.id = ts.topic_id
                 WHERE ts.id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_column($rows, 'id');
}



?>
