<?php
/***************************************************************************************************
 * @version 6.5.0.411 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * LostVerify - The goods hanging nowhere
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_LOST;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;
use MELBIS_INC_AGENT_STORE as STORE;
use MELBIS_INC_AGENT_QUERY as QUERY;

// What this tool knows
const SCHEMA = [

    'store' => [
        'id'        => 'PK.int',
        'code_shop' => 'str',
        'code_prov' => 'str',
        'code_made' => 'str',
        'name'      => 'str'
        ]
    ];


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $page = QUERY\PageLimit($mParam);
    if ( !$page['result'] ) return $page;

    $command = "FROM {DBNICK}_store s
           LEFT JOIN {DBNICK}_topic_store ts
                  ON ts.store_id = s.id
               WHERE ts.id IS NULL
                ";

    // The block of both queries
    $from = $command;

    $found = QUERY\TotalCount($from);

    $limit = $page['limit'];
    $offset = $page['offset'];

    $command = "SELECT s.id
                       $from
            ORDER BY s.id
               LIMIT $limit OFFSET $offset";
    $pull = QUERY\PullCreate($command);
    $tables = QUERY\PullFull(SCHEMA, $pull);

    $shown = count($tables['store']);
    $message = $shown.' goods of '.$found.' hanging nowhere';
    if ( $found == 0 ) $message = 'Every goods of the store hangs in a section - nothing is lost';
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
 * Function CmdQuery
 **/
function CmdQuery($mUserId, $mParam)
{
    // Asked nothing, it signs itself
    $query = $mParam['query'] ?? [];
    if ( count($query) == 0 ) return QUERY\Sign(SCHEMA);

    $said = QUERY\SqlBuild(SCHEMA, $query, 's');
    if ( !$said['result'] ) return $said;

    $page = QUERY\PageLimit($mParam);
    if ( !$page['result'] ) return $page;

    $command = "FROM {DBNICK}_store s
           LEFT JOIN {DBNICK}_topic_store ts
                  ON ts.store_id = s.id
               WHERE ts.id IS NULL
                 AND ".$said['where']."
                ";

    // The block of both queries
    $from = $command;
    $param = $said['param'];

    $found = QUERY\TotalCount($from, $param);

    $limit = $page['limit'];
    $offset = $page['offset'];

    $command = "SELECT s.id
                       $from
            ORDER BY s.id
               LIMIT $limit OFFSET $offset";
    $pull = QUERY\PullCreate($command, $param);
    $tables = QUERY\PullFull(SCHEMA, $pull);

    $shown = count($tables['store']);
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
 * Function CmdLink
 **/
function CmdLink($mUserId, $mParam)
{
    $topic_id = $mParam['topic_id'];
    if ( !SYS\RightOne('topic', $mUserId, 'place', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours'
            ];
    }

    $said = LostVerify($mParam['store_id']);
    if ( !$said['result'] ) return $said;

    $ids = $said['ids'];

    $tables = ['{DBNICK}_topic_store'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    STORE\LinkTopic($topic_id, $ids);

    SYS\TablesUnlock($tables, $mUserId);

    $said = implode(', ', $ids);

    return [
        'result'  => true,
        'message' => count($ids).' goods are back in ['.$topic_id.']'
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $told = LostVerify($mParam['store_id']);
    if ( !$told['result'] ) return $told;

    $ids = $told['ids'];
    $said = implode(', ', $told['names']);
    $list = implode(',', $ids);

    $tables = ['{DBNICK}_store'];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_store
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    SYS\TablesUnlock($tables, $mUserId);

    // Sweeps by the engine map
    $swept = SYS\DependSweep('store');

    $message = count($ids).' goods are gone from the store: '.$said;
    $message .= SYS\DependSaid($swept);

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function LostVerify
 **/
function LostVerify($mIds)
{
    // The goods hanging nowhere
    $list = implode(',', $mIds);

    $command = "SELECT s.id, s.name
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                 WHERE s.id IN ( $list )
                   AND ts.id IS NULL
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $ids = array_column($rows, 'id');
    $lost = array_diff($mIds, $ids);
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$said.'] are not lost'
            ];
    }

    return [
        'result' => true,
        'ids'    => $ids,
        'names'  => array_column($rows, 'name')
        ];
}

?>
