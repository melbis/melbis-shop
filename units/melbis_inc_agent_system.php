<?php
/***************************************************************************************************
 * @version 6.5.1.426 @ 2026-09-05
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * RightTable   - The rows this person shapes
 * RightOne     - Whether one row is his
 *
 * TablesLock   - Takes the tables into work
 * TablesUnlock - Hands them back, marked
 *
 * DependCount  - What a deletion takes along
 * DependSweep  - Sweeps the rows left over
 * DependSaid   - The tail about the sweep
 *
 * RelateCount  - What would point at nothing
 * RelateSweep  - Clears the dead references
 * RelateSaid   - The tail about the clearing
 *
 * TreePath     - The route to a node
 * TreePathFind - The route to a row
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_SYSTEM;


/**
 * Function RightTable
 **/
function RightTable($mKind, $mUserId, $mPlace = '')
{
    // What every kind names
    $of_kind = [
        'topic' => ['topic', 'topic_right', 'topic_id', 'PUT_TOPIC_RIGHT',
                    ['descr' => 'for_frame', 'price' => 'for_price', 'place' => 'for_ctrl',
                     'browse' => 'for_browse']],
        'info'  => ['info', 'info_right', 'info_id', 'PUT_INFO_RIGHT',
                    ['info' => 'for_info', 'value' => 'for_value']],
        'order' => ['order_option_value', 'order_right', 'value_id', 'PUT_ORDER_OPTION', []]
        ];
    list( $tree, $satellite, $key, $oper, $of_place ) = $of_kind[$mKind];

    // One table a request
    static $made = [];
    $mark = ( $mPlace == '' ) ? 'any' : $mPlace;
    $table = '{DBNICK}_tmp_allow_'.$mKind.'_'.$mark;
    if ( isset($made[$mUserId][$mKind][$mark]) ) return $table;

    // No places: the grant itself
    $flag = $of_place[$mPlace] ?? '';
    if ( count($of_place) == 0 )
    {
        $right = 'r.id IS NOT NULL';
    }
    elseif ( $flag != '' )
    {
        $right = 'r.'.$flag.' > 0';
    }
    else
    {
        $any = [];
        foreach ( $of_place as $one )
        {
            $any[] = 'r.'.$one.' > 0';
        }
        $right = '( '.implode(' OR ', $any).' )';
    }

    $command = "DROP TEMPORARY TABLE IF EXISTS $table";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "CREATE TEMPORARY TABLE $table ENGINE = MEMORY
                     SELECT t.id
                       FROM {DBNICK}_$tree t
                  LEFT JOIN {DBNICK}_$satellite r
                         ON r.$key = t.id
                  LEFT JOIN {DBNICK}_user u
                         ON ( u.group_id = r.group_id
                              OR u.add_group_id = r.group_id )
                      WHERE ( :SEE_ALL = 1
                              OR ( $right
                                   AND ( r.user_id = :USER_ID
                                         OR u.id = :USER_ID ) ) )
                   GROUP BY t.id
                   ";
    $param_may = [
        'see_all' => ( MELBIS()->SysOperRight($mUserId, $oper) ) ? 1 : 0,
        'user_id' => $mUserId
        ];
    MELBIS()->SqlQuery(__LINE__, $command, $param_may);

    $command = "ALTER TABLE $table ADD PRIMARY KEY ( id )";
    MELBIS()->SqlQuery(__LINE__, $command);

    $made[$mUserId][$mKind][$mark] = 1;

    return $table;
}


/**
 * Function RightOne
 **/
function RightOne($mKind, $mUserId, $mPlace, $mId)
{
    $allow = RightTable($mKind, $mUserId, $mPlace);

    $command = "SELECT id
                  FROM $allow
                 WHERE id = :ID
               ";
    $param_row = [
        'id' => $mId
        ];
    $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_row);

    return isset($row['id']);
}


/**
 * Function TablesLock
 **/
function TablesLock($mTables, $mUserId = 0)
{
    // The person they serve
    $taken = MELBIS()->SqlTableLock(__LINE__, $mTables, $mUserId);
    if ( $taken )
    {
        return [
            'result' => true
            ];
    }

    $names = [];
    foreach ( (array)$mTables as $table )
    {
        $names[] = str_replace('{DBNICK}_', '', $table);
    }
    $list = implode(', ', $names);

    return [
        'result'  => false,
        'message' => 'The ['.$list.'] tables are busy'
        ];
}


/**
 * Function TablesUnlock
 **/
function TablesUnlock($mTables, $mUserId = 0)
{
    MELBIS()->SqlTableChange(__LINE__, $mTables, false);
    MELBIS()->SqlTableUnlock(__LINE__, $mTables, $mUserId);
}


/**
 * Function DependCount
 **/
function DependCount($mTable, $mIds)
{
    $ids = array_values(array_unique(array_map('intval', (array)$mIds)));

    $count = MELBIS()->SysDependCount($mTable, $ids);
    $ties = MELBIS()->SysDepends($mTable);

    // The map tells the fate
    $nullable = [];
    foreach ( $ties as $tie )
    {
        if ( $tie['nullable'] ) $nullable[$tie['table']] = true;
    }

    $gone = [];
    $left = [];
    $total = 0;
    foreach ( $count as $table => $num )
    {
        if ( $num == 0 ) continue;

        $total += $num;
        if ( isset($nullable[$table]) )
        {
            $left[] = $num.' '.$table;
        }
        else
        {
            $gone[] = $num.' '.$table;
        }
    }

    $said = '';
    if ( count($gone) > 0 ) $said .= ', and with them '.implode(', ', $gone);
    if ( count($left) > 0 ) $said .= ', leaving '.implode(', ', $left).' pointing at nothing';

    return [
        'count' => $total,
        'gone'  => $gone,
        'left'  => $left,
        'said'  => $said
        ];
}


/**
 * Function DependSweep
 **/
function DependSweep($mTable)
{
    $report = [];
    foreach ( MELBIS()->SysDependSweep($mTable) as $one )
    {
        $where = $one['table'];

        // A busy pass beats zero
        if ( ( $report[$where] ?? 0 ) === 'busy' ) continue;

        if ( $one['busy'] )
        {
            $report[$where] = 'busy';
            continue;
        }

        $was = $report[$where] ?? 0;
        $report[$where] = $was + $one['gone'];
    }

    return $report;
}


/**
 * Function DependSaid
 **/
function DependSaid($mReport)
{
    $said = [];
    foreach ( $mReport as $where => $count )
    {
        if ( $count === 0 ) continue;

        $said[] = $where.': '.$count;
    }

    if ( count($said) == 0 ) return '';

    return '. Swept with them - '.implode(', ', $said);
}


/**
 * Function RelateCount
 **/
function RelateCount($mTable, $mIds)
{
    $ids = array_values(array_unique(array_map('intval', (array)$mIds)));

    $count = MELBIS()->SysRelateCount($mTable, $ids);

    // A reference outlives its row
    $left = [];
    $total = 0;
    foreach ( $count as $table => $num )
    {
        if ( $num == 0 ) continue;

        $total += $num;
        $left[] = $num.' '.$table;
    }

    $said = '';
    if ( count($left) > 0 )
    {
        $said = '. References left standing - '.implode(', ', $left).'; the relation flag clears them';
    }

    return [
        'count' => $total,
        'left'  => $left,
        'said'  => $said
        ];
}


/**
 * Function RelateSweep
 **/
function RelateSweep($mTable)
{
    $report = [];
    foreach ( MELBIS()->SysRelateSweep($mTable) as $one )
    {
        $where = $one['table'];

        // A busy pass beats zero
        if ( ( $report[$where] ?? 0 ) === 'busy' ) continue;

        if ( $one['busy'] )
        {
            $report[$where] = 'busy';
            continue;
        }

        $was = $report[$where] ?? 0;
        $report[$where] = $was + $one['cleared'];
    }

    return $report;
}


/**
 * Function RelateSaid
 **/
function RelateSaid($mReport)
{
    $said = [];
    foreach ( $mReport as $where => $count )
    {
        if ( $count === 0 ) continue;

        $said[] = $where.': '.$count;
    }

    if ( count($said) == 0 ) return '';

    return '. References cleared - '.implode(', ', $said);
}


/**
 * Function TreePath
 **/
function TreePath($mTable, $mId)
{
    $command = "SELECT id, name, tindex
                  FROM {DBNICK}_$mTable
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $tree = [];
    foreach ( $rows as $row )
    {
        $tree[(int)$row['id']] = $row;
    }

    $id = (int)$mId;
    if ( !isset($tree[$id]) ) return '';

    // The dashes of a separator
    $said = [trim($tree[$id]['name'], " \t-")];

    // Stops on a node twice
    $seen = [$id => true];
    $up = (int)$tree[$id]['tindex'];
    while ( isset($tree[$up]) && !isset($seen[$up]) )
    {
        $seen[$up] = true;
        array_unshift($said, trim($tree[$up]['name'], " \t-"));
        $up = (int)$tree[$up]['tindex'];
    }

    return implode(' -> ', $said);
}


/**
 * Function TreePathFind
 **/
function TreePathFind($mTable, $mField, $mValue)
{
    $command = "SELECT id
                  FROM {DBNICK}_$mTable
                 WHERE BINARY $mField = :VALUE
               ";
    $param_find = [
        'value' => $mValue
        ];
    $id = (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $param_find);
    if ( $id == 0 ) return '';

    return TreePath($mTable, $id);
}


?>
