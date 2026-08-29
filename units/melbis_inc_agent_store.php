<?php
/***************************************************************************************************
 * @version 6.5.1.416 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Topic        - The goods of named sections
 * Query        - The same, found by query
 * LinkTopic    - Hangs goods in a section
 * TopicBranch  - The sections into a table
 * DefaultFill  - What a goods is born
 * Allowed      - The goods of this person
 * SlaveAllowed - The rows on such goods
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_STORE;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_QUERY as QUERY;


/**
 * Function Topic
 **/
function Topic($mUserId, $mPlace, $mSchema, $mParam)
{
    $page = QUERY\PageLimit($mParam);
    if ( !$page['result'] ) return $page;

    // The walk asked, right allowed
    $allow = SYS\RightTable('topic', $mUserId, $mPlace);
    $branch = TopicBranch($mParam['topic_id'], $mParam['topic_sub']);

    $command = "FROM {DBNICK}_topic_store ts
                JOIN $allow a
                  ON a.id = ts.topic_id
                JOIN $branch w
                  ON w.id = ts.topic_id
                ";

    // The block of both queries
    $from = $command;

    $found = QUERY\TotalCount($from, [], 'ts.store_id');

    $limit = $page['limit'];
    $offset = $page['offset'];

    // One row of the page
    $pull = QUERY\PullStore($from, $limit, $offset);
    $tables = QUERY\PullFull($mSchema, $pull);

    $shown = count($tables[array_key_first($mSchema)]);
    $message = $shown.' goods of '.$found.' standing in the section(s) walked';
    if ( $found > $offset + $shown )
    {
        $message .= '; the rest is asked for by offset '.( $offset + $shown );
    }

    return [
        'result'  => true,
        'found'   => $found,
        'message' => $message,
        'tables'  => $tables
        ];
}


/**
 * Function Query
 **/
function Query($mUserId, $mPlace, $mSchema, $mParam)
{
    // Asked nothing, it signs itself
    $query = $mParam['query'] ?? [];
    if ( count($query) == 0 ) return QUERY\Sign($mSchema);

    $said = QUERY\SqlBuild($mSchema, $query, 's');
    if ( !$said['result'] ) return $said;

    $page = QUERY\PageLimit($mParam);
    if ( !$page['result'] ) return $page;

    // The query what, right where
    $allow = SYS\RightTable('topic', $mUserId, $mPlace);

    $command = "FROM {DBNICK}_topic_store ts
                JOIN $allow a
                  ON a.id = ts.topic_id
                JOIN {DBNICK}_store s
                  ON s.id = ts.store_id
               WHERE ".$said['where']."
                ";

    // The block of both queries
    $from = $command;
    $param = $said['param'];

    $found = QUERY\TotalCount($from, $param, 'ts.store_id');

    $limit = $page['limit'];
    $offset = $page['offset'];

    // One row of the page
    $pull = QUERY\PullStore($from, $limit, $offset, $param);
    $tables = QUERY\PullFull($mSchema, $pull);

    $shown = count($tables[array_key_first($mSchema)]);
    $message = $shown.' goods of '.$found.' found by '.$said['leaf'].' condition(s)';
    if ( $found > $offset + $shown )
    {
        $message .= '; the rest is asked for by offset '.( $offset + $shown );
    }

    return [
        'result'  => true,
        'found'   => $found,
        'message' => $message,
        'tables'  => $tables
        ];
}


/**
 * Function LinkTopic
 **/
function LinkTopic($mTopicId, $mIds)
{
    $ids = MELBIS()->SqlGenIdBlock('topic_store', count($mIds));

    $link = [];
    foreach ( $mIds as $num => $id )
    {
        $link[] = [
            'id'       => $ids[$num],
            'topic_id' => $mTopicId,
            'store_id' => $id,
            'pos'      => $ids[$num]
            ];
    }

    MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_topic_store', $link);
}


/**
 * Function TopicBranch
 **/
function TopicBranch($mIds, $mSub)
{
    $named = array_map('intval', (array)$mIds);
    $list = implode(',', $named);

    $command = "DROP TEMPORARY TABLE IF EXISTS {DBNICK}_tmp_branch";
    MELBIS()->SqlQuery(__LINE__, $command);

    if ( $mSub )
    {
        $command = "CREATE TEMPORARY TABLE {DBNICK}_tmp_branch ENGINE = MEMORY
                    WITH RECURSIVE branch AS (
                         SELECT id
                           FROM {DBNICK}_topic
                          WHERE id IN ( $list )
                          UNION ALL
                         SELECT t.id
                           FROM {DBNICK}_topic t
                           JOIN branch b
                             ON t.tindex = b.id
                    )
                         SELECT id
                           FROM branch
                       ";
    }
    else
    {
        $command = "CREATE TEMPORARY TABLE {DBNICK}_tmp_branch ENGINE = MEMORY
                         SELECT id
                           FROM {DBNICK}_topic
                          WHERE id IN ( $list )
                       ";
    }
    MELBIS()->SqlQuery(__LINE__, $command);

    return '{DBNICK}_tmp_branch';
}


/**
 * Function DefaultFill
 **/
function DefaultFill($mRow)
{
    $now = MELBIS()->DateTime('now');

    $born = [
        'no_visible'  => 1,
        'status_key'  => 'kAbsent',
        'kind_key'    => 'kDefault',
        'state_key'   => 'kDefault',
        'templ_key'   => 'kDefault',
        'create_time' => $now,
        'update_time' => $now,
        'edit_time'   => $now,
        'exist_time'  => $now
        ];

    return array_merge($born, $mRow);
}


/**
 * Function Allowed
 **/
function Allowed($mUserId, $mIds, $mPlace)
{
    $allow = SYS\RightTable('topic', $mUserId, $mPlace);
    $list = implode(',', $mIds);

    $command = "SELECT ts.store_id AS id
                  FROM {DBNICK}_topic_store ts
                  JOIN $allow a
                    ON a.id = ts.topic_id
                 WHERE ts.store_id IN ( $list )
              GROUP BY ts.store_id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);

    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$said.'] are not yours'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids
        ];
}


/**
 * Function SlaveAllowed
 **/
function SlaveAllowed($mUserId, $mTable, $mIds, $mPlace)
{
    $allow = SYS\RightTable('topic', $mUserId, $mPlace);
    $list = implode(',', $mIds);

    $command = "SELECT x.id
                  FROM {DBNICK}_$mTable x
                  JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = x.store_id
                  JOIN $allow a
                    ON a.id = ts.topic_id
                 WHERE x.id IN ( $list )
              GROUP BY x.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);

    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The rows ['.$said.'] are not yours'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids
        ];
}

?>
