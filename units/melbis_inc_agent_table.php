<?php
/***************************************************************************************************
 * @version 6.5.0.410 @ 2026-08-28
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Read         - Reads a table whole
 *
 * Add          - Adds one row
 * AddBlock     - Adds a row per owner
 * Update       - Changes the columns of rows
 * Remove       - Deletes rows
 *
 * Pos          - Reorders a list
 * PosOwn       - The place of base rows
 * PosWrite     - Lays an order down
 * PosRead      - A list, id against pos
 *
 * KeySetAdd    - Adds option rows on owners
 * KeySetUpdate - Changes columns of option rows
 * KeySetRemove - Deletes option rows
 *
 * TreeAdd      - Adds a node
 * TreeMove     - Moves a node and branch
 * TreeRemove   - Deletes nodes with their branches
 * TreeNodeAdd  - Seats a node
 * TreeBranch   - A node with all under
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_TABLE;

// Libraries
use MELBIS_INC_AGENT_SYSTEM as SYS;


/**
 * Function Read
 **/
function Read($mTable, $mMore = [])
{
    $command = "SELECT *
                  FROM {DBNICK}_$mTable
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $tables = [$mTable => $rows];

    foreach ( $mMore as $table )
    {
        $command = "SELECT *
                      FROM {DBNICK}_$table
                   ";
        $more = MELBIS()->SqlSelect(__LINE__, $command);

        $tables[$table] = $more;
    }

    return [
        'result'  => true,
        'message' => 'The tables asked for',
        'tables'  => $tables
        ];
}


/**
 * Function Add
 **/
function Add($mUserId, $mTable, $mParam, $mUseGen = true)
{
    // Every field is a column
    $row = $mParam;

    // A row stands by id
    $columns = MELBIS()->SysTableColumns($mTable);
    $ordered = ( isset($columns['pos']) && !isset($row['pos']) );

    // Taken before the lock
    if ( $mUseGen )
    {
        $row['id'] = MELBIS()->SqlGenId($mTable);
        if ( $ordered ) $row['pos'] = $row['id'];
    }

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_'.$mTable, $row);

    // The base hands the id
    if ( !$mUseGen )
    {
        $row['id'] = MELBIS()->SqlLastInsertId();
        if ( $ordered ) PosOwn($mTable, [$row['id']]);
    }

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'id'      => $row['id'],
        'message' => 'The row of '.$mTable.' is added'
        ];
}


/**
 * Function AddBlock
 **/
function AddBlock($mUserId, $mTable, $mKey, $mIds, $mParam, $mUseGen = true)
{
    // Every field is a column
    $fields = $mParam;
    unset($fields[$mKey]);

    // A row stands by id
    $columns = MELBIS()->SysTableColumns($mTable);
    $ordered = ( isset($columns['pos']) && !isset($fields['pos']) );

    // One ask, the whole block
    $owners = array_values($mIds);
    $block = ( $mUseGen ) ? MELBIS()->SqlGenIdBlock($mTable, count($owners)) : [];

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $made = [];
    foreach ( $owners as $num => $id )
    {
        $row = $fields;
        $row[$mKey] = $id;

        if ( $mUseGen )
        {
            $row['id'] = $block[$num];
            if ( $ordered ) $row['pos'] = $row['id'];
        }

        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_'.$mTable, $row);
        $made[] = ( $mUseGen ) ? $row['id'] : MELBIS()->SqlLastInsertId();
    }

    // The base hands ids out
    if ( $ordered && !$mUseGen ) PosOwn($mTable, $made);

    SYS\TablesUnlock($tables, $mUserId);

    // One id, or a list
    $said = ( count($made) == 1 ) ? $made[0] : $made;

    return [
        'result'  => true,
        'id'      => $said,
        'message' => 'The rows of '.$mTable.' are set'
        ];
}


/**
 * Function Update
 **/
function Update($mUserId, $mTable, $mIds, $mParam)
{
    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    foreach ( $mIds as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_'.$mTable, $row, 'id');
    }

    SYS\TablesUnlock($tables, $mUserId);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => 'The rows of '.$mTable.' are changed'
        ];
}


/**
 * Function Remove
 **/
function Remove($mUserId, $mTable, $mIds, $mParam = [])
{
    $list = implode(',', $mIds);

    // The depend flag takes them
    $depend = SYS\DependCount($mTable, $mIds);
    if ( $depend['count'] > 0 && !( $mParam['depend'] ?? false ) )
    {
        return [
            'result'  => false,
            'message' => 'Deleting '.count($mIds).' row(s) of '.$mTable.$depend['said'].'. Say depend'
            ];
    }

    // Counted while the rows stand
    $relate = SYS\RelateCount($mTable, $mIds);

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_$mTable
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    SYS\TablesUnlock($tables, $mUserId);

    $message = count($mIds).' row(s) of '.$mTable.' gone';
    $message .= SYS\DependSaid(SYS\DependSweep($mTable));

    // The relation flag clears them
    if ( $mParam['relation'] ?? false )
    {
        $message .= SYS\RelateSaid(SYS\RelateSweep($mTable));
    }
    else
    {
        $message .= $relate['said'];
    }

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function Pos
 **/
function Pos($mUserId, $mTable, $mScope, $mParam)
{
    $type = strtoupper(trim((string)$mParam['type']));
    $data = $mParam['data'] ?? [];

    $was = PosRead($mTable, $mScope);
    if ( count($was) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing to put in order'
            ];
    }

    if ( $type == 'SORT' )
    {
        if ( !is_array($data) || !isset($data['field']) )
        {
            return [
                'result'  => false,
                'message' => 'SORT takes a field and a direction'
                ];
        }

        $field = trim((string)$data['field']);
        $columns = MELBIS()->SysTableColumns($mTable);
        if ( !isset($columns[$field]) )
        {
            $list = implode(', ', array_keys($columns));

            return [
                'result'  => false,
                'message' => 'No column ['.$field.'] in ['.$mTable.']'
                ];
        }

        $way = strtoupper(trim((string)( $data['direction'] ?? 'ASC' )));
        if ( $way != 'ASC' && $way != 'DESC' )
        {
            return [
                'result'  => false,
                'message' => 'The direction is ASC or DESC'
                ];
        }

        $order = PosRead($mTable, $mScope, $field.' '.$way);

        return PosWrite($mUserId, $mTable, $mScope, array_keys($order), 'sorted by '.$field.' '.$way);
    }

    if ( $type != 'POS' && $type != 'MOVE' )
    {
        return [
            'result'  => false,
            'message' => 'The type is POS, MOVE or SORT'
            ];
    }

    if ( !is_array($data) || count($data) == 0 )
    {
        return [
            'result'  => false,
            'message' => $type.' takes a list of rows'
            ];
    }

    if ( $type == 'POS' )
    {
        $front = array_column($data, 'id');
        if ( count($front) != count($data) )
        {
            return [
                'result'  => false,
                'message' => 'Every row of data is an object'
                ];
        }

        // Weighed before the lock
        foreach ( $front as $id )
        {
            if ( isset($was[(int)$id]) ) continue;

            $list = implode(', ', array_keys($was));

            return [
                'result'  => false,
                'message' => 'No row ['.$id.'] in this list'
                ];
        }

        return PosWrite($mUserId, $mTable, $mScope, $front, count($front).' row(s) to the front');
    }

    // Weighed before any writing
    $named = [];
    foreach ( $data as $one )
    {
        if ( !is_array($one) )
        {
            return [
                'result'  => false,
                'message' => 'Every row of data is an object'
                ];
        }

        $id = (int)( $one['id'] ?? 0 );
        $inc = $one['inc'] ?? '';

        if ( !isset($was[$id]) || !is_numeric($inc) )
        {
            $list = implode(', ', array_keys($was));

            return [
                'result'  => false,
                'message' => 'MOVE takes id and inc'
                ];
        }

        $named[] = [$id, (int)$inc];
    }

    // One after another, in turn
    $order = array_keys($was);
    foreach ( $named as $pair )
    {
        $at = array_search($pair[0], $order);
        $to = $at + $pair[1];
        if ( $to < 0 ) $to = 0;
        if ( $to > count($order) - 1 ) $to = count($order) - 1;

        array_splice($order, $at, 1);
        array_splice($order, $to, 0, [$pair[0]]);
    }

    return PosWrite($mUserId, $mTable, $mScope, $order, count($named).' row(s) shifted');
}


/**
 * Function PosOwn
 **/
function PosOwn($mTable, $mIds)
{
    // Its own id, written after
    $list = implode(',', $mIds);

    $command = "UPDATE {DBNICK}_$mTable
                   SET pos = id
                 WHERE pos = 0
                   AND id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);
}


/**
 * Function PosWrite
 **/
function PosWrite($mUserId, $mTable, $mScope, $mOrder, $mSaid)
{
    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $moved = MELBIS()->SysPosOrder($mTable, $mOrder, $mScope);

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'message' => $moved.' row(s) of '.$mTable.' moved'
        ];
}


/**
 * Function PosRead
 **/
function PosRead($mTable, $mScope, $mOrder = 'pos, id')
{
    $where = '';
    $param_set = [];
    foreach ( $mScope as $field => $value )
    {
        $where .= ' AND '.$field.' = :'.strtoupper($field);
        $param_set[$field] = $value;
    }

    $command = "SELECT id, pos
                  FROM {DBNICK}_$mTable
                 WHERE 1 = 1
                       $where
              ORDER BY $mOrder
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_set);

    $order = [];
    foreach ( $rows as $row )
    {
        $order[(int)$row['id']] = (int)$row['pos'];
    }

    return $order;
}


/**
 * Function KeySetAdd
 **/
function KeySetAdd($mUserId, $mFamily, $mIds, $mParam)
{
    return AddBlock($mUserId, $mFamily.'_key_set', $mFamily.'_id', $mIds, $mParam);
}


/**
 * Function KeySetUpdate
 **/
function KeySetUpdate($mUserId, $mFamily, $mIds, $mParam)
{
    $table = '{DBNICK}_'.$mFamily.'_key_set';

    // Every field is a column
    $fields = $mParam;
    unset($fields['id']);

    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $tables = [$table];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    foreach ( $mIds as $id )
    {
        $row = $fields;
        $row['id'] = $id;
        MELBIS()->SqlUpdate(__LINE__, $table, $row, 'id');
    }

    SYS\TablesUnlock($tables, $mUserId);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => 'The rows of '.$mFamily.'_key_set are changed'
        ];
}


/**
 * Function KeySetRemove
 **/
function KeySetRemove($mUserId, $mFamily, $mIds)
{
    $table = '{DBNICK}_'.$mFamily.'_key_set';
    $list = implode(',', $mIds);

    $tables = [$table];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM $table
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    SYS\TablesUnlock($tables, $mUserId);

    return [
        'result'  => true,
        'message' => 'The rows of '.$mFamily.'_key_set are gone'
        ];
}


/**
 * Function TreeAdd
 **/
function TreeAdd($mUserId, $mTable, $mParam, $mScope = [])
{
    $parent_id = $mParam['parent_id'];

    // Every field is a column
    $fields = $mParam;
    unset($fields['parent_id']);

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $id = TreeNodeAdd($mTable, $parent_id, $fields, $mScope);

    SYS\TablesUnlock($tables, $mUserId);

    if ( $id == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No row ['.$parent_id.'] in the tree'
            ];
    }

    return [
        'result'  => true,
        'id'      => $id,
        'message' => 'The row of '.$mTable.' is seated'
        ];
}


/**
 * Function TreeMove
 **/
function TreeMove($mUserId, $mTable, $mParam, $mScope = [])
{
    $id = $mParam['id'];
    $parent_id = $mParam['parent_id'];

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    $done = MELBIS()->SysTreeMove($mTable, $id, $parent_id, $mScope);

    SYS\TablesUnlock($tables, $mUserId);

    if ( !$done )
    {
        return [
            'result'  => false,
            'message' => 'The parent ['.$parent_id.'] does not take it'
            ];
    }

    return [
        'result'  => true,
        'message' => 'The row of '.$mTable.' is moved'
        ];
}


/**
 * Function TreeRemove
 **/
function TreeRemove($mUserId, $mTable, $mIds, $mParam = [], $mScope = [])
{
    // The whole branch, not root
    $branch = [];
    foreach ( $mIds as $id )
    {
        $mine = TreeBranch($mTable, $id, $mScope);
        foreach ( $mine as $one )
        {
            $branch[$one] = $one;
        }
    }

    // The depend flag takes them
    $depend = SYS\DependCount($mTable, $branch);
    if ( !( $mParam['depend'] ?? false ) )
    {
        return [
            'result'  => false,
            'message' => 'Deleting '.count($branch).' row(s) with branches'.$depend['said'].'. Say depend'
            ];
    }

    // Counted while the rows stand
    $relate = SYS\RelateCount($mTable, $branch);

    $tables = ['{DBNICK}_'.$mTable];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // One node a door
    foreach ( $mIds as $id )
    {
        MELBIS()->SysTreeDelete($mTable, $id, $mScope);
    }

    SYS\TablesUnlock($tables, $mUserId);

    $message = count($branch).' row(s) of '.$mTable.' gone with their branches';
    $message .= SYS\DependSaid(SYS\DependSweep($mTable));

    // The relation flag clears them
    if ( $mParam['relation'] ?? false )
    {
        $message .= SYS\RelateSaid(SYS\RelateSweep($mTable));
    }
    else
    {
        $message .= $relate['said'];
    }

    return [
        'result'  => true,
        'message' => $message
        ];
}


/**
 * Function TreeNodeAdd
 **/
function TreeNodeAdd($mTable, $mParentId, $mFields, $mScope = [])
{
    $id = MELBIS()->SysTreeAdd($mTable, $mParentId, $mScope);
    if ( $id == 0 ) return 0;

    // Seated bare, the rights after
    MELBIS()->SysTreeRightCopy($mTable, $mParentId, $id);

    if ( count($mFields) == 0 ) return $id;

    $fields = $mFields;
    $fields['id'] = $id;
    $table = '{DBNICK}_'.$mTable;
    MELBIS()->SqlUpdate(__LINE__, $table, $fields, 'id');

    return $id;
}


/**
 * Function TreeBranch
 **/
function TreeBranch($mTable, $mId, $mScope = [])
{
    $where = '';
    $param_branch = [
        'id' => (int)$mId
        ];

    // The scope keeps the walk
    foreach ( $mScope as $field => $value )
    {
        if ( !preg_match('/^[a-zA-Z0-9_]+$/', $field) ) continue;

        $where .= ' AND '.$field.' = :'.strtoupper($field);
        $param_branch[$field] = $value;
    }

    $command = "WITH RECURSIVE branch AS (
                     SELECT id
                       FROM {DBNICK}_$mTable
                      WHERE id = :ID
                            $where
                      UNION ALL
                     SELECT t.id
                       FROM {DBNICK}_$mTable t
                       JOIN branch b
                         ON t.tindex = b.id
                )
                     SELECT id
                       FROM branch
                   ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command, $param_branch);

    return array_map('intval', array_column($rows, 'id'));
}

?>
