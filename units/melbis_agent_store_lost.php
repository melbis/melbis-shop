<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList   - Reads the goods that hang in no section, by a part of their code or name
 * CmdLink   - Hangs a lost goods back into a section
 * CmdRemove - Deletes a lost goods from the store, in two steps
 *
 * Lost      - Of the goods asked for, the ones hanging nowhere
 * Drags     - Counts what a deletion would take along
 *
 * The Recovery window: the only door in the store that deletes a goods, so it goes by operations
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_STORE_LOST;

// Libraries
use MELBIS_INC_AGENT_UTIL as UTIL;
use MELBIS_INC_AGENT_STORE as STORE;

// The columns every answer here carries, the set the Recovery window of the program loads
const FIELDS_READ = "s.id, s.code_shop, s.code_prov, s.code_made, s.name";


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'GET_REMOVE_STORE', 'Reading the lost goods');
    if ( $gate !== true ) return $gate;

    // Looks for a word inside the column, and a list of words means any of them
    $words = ['code_shop', 'code_prov', 'code_made', 'name'];
    $where = [];
    $param = [];
    foreach ( $words as $word )
    {
        if ( !isset($mParam[$word]) ) continue;

        $like = [];
        foreach ( $mParam[$word] as $num => $value )
        {
            $key = $word.'_'.$num;
            $like[] = 's.'.$word.' LIKE :'.strtoupper($key);
            $param[$key] = '%'.$value.'%';
        }

        $where[] = '( '.implode(' OR ', $like).' )';
    }

    $filter = '1';
    if ( count($where) > 0 ) $filter = implode(' AND ', $where);
    $fields = FIELDS_READ;

    $command = "SELECT $fields
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                 WHERE ts.id IS NULL
                   AND $filter
              ORDER BY s.id
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param);

    $message = count($rows).' goods hang nowhere';
    if ( count($rows) == 0 && count($where) == 0 )
    {
        $message = 'Every goods of the store hangs in a section - nothing is lost';
    }

    return [
        'result'  => true,
        'message' => $message,
        'tables'  => [
            'store' => $rows
            ]
        ];
}


/**
 * Function CmdLink
 **/
function CmdLink($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'RESTORE_REMOVE_STORE', 'Bringing a lost goods back');
    if ( $gate !== true ) return $gate;

    $topic_id = $mParam['topic_id'];
    if ( !UTIL\RightOne('topic', $mUserId, 'place', $topic_id) )
    {
        return [
            'result'  => false,
            'message' => 'The section ['.$topic_id.'] is not yours to place in'
            ];
    }

    $rows = Lost($mParam['store_id']);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mParam['store_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] are not lost: they hang somewhere already, or are '.
                         'not in the store at all. Moving what hangs is the Location tool'
            ];
    }

    $tables = ['{DBNICK}_topic_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $done = STORE\Link($topic_id, $rows);

    UTIL\TablesUnlock($tables);

    $said = implode(', ', $done['hung']);

    return [
        'result'  => true,
        'message' => count($done['hung']).' goods are back in the section ['.$topic_id.']: '.$said
        ];
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $gate = UTIL\RightOper($mUserId, 'DELETE_REMOVE_STORE', 'Deleting a goods');
    if ( $gate !== true ) return $gate;

    $rows = Lost($mParam['store_id']);
    $ids = array_column($rows, 'id');
    $lost = array_diff($mParam['store_id'], $ids);
    if ( count($lost) > 0 )
    {
        $list = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'The goods ['.$list.'] are not lost: only a goods hanging nowhere is '.
                         'deleted here, and taking one out of its sections is the Location tool'
            ];
    }

    $names = array_column($rows, 'name');
    $said = implode(', ', $names);
    $list = implode(',', $ids);
    $drags = Drags($list);

    // Without recursive the call only warns: nothing else in the store ends a goods
    if ( !$mParam['recursive'] )
    {
        $message = 'About to delete '.count($ids).' goods for good: '.$said;
        if ( $drags != '' ) $message .= '. Going with them - '.$drags;
        $message .= '. Say recursive to go on. A goods deleted here is gone from the store, and no '.
                    'Recovery of the program brings it back; orders keep their lines';

        return [
            'result'  => false,
            'message' => $message
            ];
    }

    $tables = ['{DBNICK}_store'];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_store
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock($tables);

    // Sweeps what pointed at them by the map of the engine
    $swept = UTIL\DependSweep('store');

    $message = count($ids).' goods are gone from the store: '.$said;
    $message .= UTIL\DependSaid($swept);
    $message .= '. Orders keep their lines: an order line carries its own copy of the name';

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function Lost
 **/
function Lost($mIds)
{
    // Reads the goods that hang in no section: this tool reaches no further than those
    $list = implode(',', $mIds);

    $command = "SELECT s.id, s.name
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_topic_store ts
                    ON ts.store_id = s.id
                 WHERE s.id IN ( $list )
                   AND ts.id IS NULL
              ORDER BY s.id
               ";

    return MELBIS()->SqlSelect(__LINE__, $command);
}



/**
 * Function Drags
 **/
function Drags($mList)
{
    // Counts the rows of the four tables that hang on a goods, so the warning is a fact
    $about = [
        'store_info'  => 'characteristic',
        'store_param' => 'parameter',
        'files_store' => 'file',
        'store_set'   => 'set'
        ];

    $hangs = [];
    foreach ( $about as $table => $what )
    {
        $field = ( $table == 'files_store' ) ? 'elem_id' : 'store_id';
        $command = "SELECT COUNT(*)
                      FROM {DBNICK}_$table
                     WHERE $field IN ( $mList )
                   ";
        $how = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0);
        if ( $how > 0 ) $hangs[] = $how.' '.$what.'(s)';
    }

    return implode(', ', $hangs);
}

?>
