<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * The workshop of the tools: what every one of them needs and no one of them owns
 *
 * RightOper       - Demands an operation of the engine: true, or a refusal naming the route to it
 * RightTable      - Builds the table of rows this person may shape, for a read to join
 * RightOne        - Answers whether one row is among those this person may shape
 *
 * TablesLock      - Takes the tables into work, or refuses by naming the busy ones
 * TablesUnlock    - Hands them back marked as changed, which is how the caches learn
 *
 * TreeNodeAdd     - Seats a node in a tree, fills it and copies the rights of its parent
 * TreeBranch      - Walks a node with everything under it, as a list of ids
 * TreeBranchTable - Walks the same branch into a table a read joins
 * TreePath        - Builds the route to a node of a tree, the way a person walks it in the program
 * TreePathFind    - Builds the same route to a row found by a column of its own
 *
 * Pos             - Reorders a flat list three ways: POS, MOVE, SORT
 * PosRead         - Reads a list as a map of id to pos, in the order asked for
 * PosWrite        - Writes a list back in order, touching only the rows that moved
 *
 * DependCount     - Counts what a deletion would take along, by the map of the engine
 * DependSweep     - Sweeps the rows left without their main row, the chain of them included
 * DependSaid      - Builds the tail of an answer that says what the sweep took
 *
 * Exists          - Of the ids asked for, the ones the table really has
 * Only            - Keeps the columns of a set and drops the rest of the call
 * Number          - Answers a number, or refuses, because a zero here always means something
 * ColumnOne       - Weighs one column against the schema, or refuses by naming them all
 * OptionPair      - Weighs the pair - option and value - an option row will stand with
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_UTIL;


/**
 * Function RightOper
 * Demands an operation of the engine: true, or a refusal naming the route to that right
 **/
function RightOper($mUserId, $mCommand, $mWhat)
{
    $allowed = MELBIS()->SysOperRight($mUserId, $mCommand);
    if ( $allowed ) return true;

    // Looks the operation up in the registry and draws the route to it through the tree
    $oper_set = MELBIS()->SysOperations();
    $oper = array_column($oper_set, null, 'command');
    $oper_id = $oper[$mCommand]['id'] ?? 0;

    $path = '';
    if ( $oper_id > 0 ) $path = TreePath('oper', $oper_id);

    $where = ( $path == '' ) ? '' : ' - in the program that right is '.$path;

    return [
        'result'  => false,
        'message' => $mWhat.' is not yours: it takes the ['.$mCommand.'] right of the store'.$where
        ];
}


/**
 * Function RightTable
 * Builds the table of rows this person may shape, for a read to join instead of a list of ids
 **/
function RightTable($mKind, $mUserId, $mPlace = '')
{
    // Every kind names its tree, the operation that sees all of it, and the flag of every place
    $of_kind = [
        'topic' => ['topic', 'PUT_TOPIC_RIGHT',
                    ['descr' => 'for_frame', 'price' => 'for_price', 'place' => 'for_ctrl']],
        'info'  => ['info', 'PUT_INFO_RIGHT',
                    ['info' => 'for_info', 'value' => 'for_value']]
        ];
    list( $tree, $oper, $of_place ) = $of_kind[$mKind];

    // Builds one table per kind and place once a request: a command asks for it more than once
    static $made = [];
    $mark = ( $mPlace == '' ) ? 'any' : $mPlace;
    $table = '{DBNICK}_tmp_allow_'.$mKind.'_'.$mark;
    if ( isset($made[$mUserId][$mKind][$mark]) ) return $table;

    // A place goes by its own flag, and no place at all by any of the three
    $flag = $of_place[$mPlace] ?? '';
    if ( $flag != '' )
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

    $satellite = $tree.'_right';
    $key = $tree.'_id';

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
 * Answers whether one row is among those this person may shape, by the table of the allowed
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
 * Takes the tables into work, and a busy one comes back as a refusal ready to hand up
 **/
function TablesLock($mTables)
{
    $taken = MELBIS()->SqlTableLock(__LINE__, $mTables);
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
        'message' => 'The ['.$list.'] tables are taken into work right now - try again in a moment'
        ];
}


/**
 * Function TablesUnlock
 * Hands the tables back marked as changed, which is how the caches of the storefront learn
 **/
function TablesUnlock($mTables)
{
    MELBIS()->SqlTableChange(__LINE__, $mTables, false);
    MELBIS()->SqlTableUnlock(__LINE__, $mTables);
}


/**
 * Function TreeNodeAdd
 * Seats a node in the tree, fills its columns and copies the rights of the parent, in one act
 **/
function TreeNodeAdd($mTable, $mParentId, $mFields, $mScope = [])
{
    $id = MELBIS()->SysTreeAdd($mTable, $mParentId, $mScope);
    if ( $id == 0 ) return 0;

    // The engine seats a node bare, so the rights of the parent are copied by a second door
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
 * Walks a node with everything under it and answers a list of ids
 **/
function TreeBranch($mTable, $mId, $mScope = [])
{
    $where = '';
    $param_branch = [
        'id' => (int)$mId
        ];

    // One table may hold several trees, so the scope keeps the walk inside its own
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


/**
 * Function TreeBranchTable
 * Walks the same branch into a table a read joins instead of a list of ids
 **/
function TreeBranchTable($mTable, $mIds)
{
    $named = array_map('intval', (array)$mIds);
    $list = implode(',', $named);

    $command = "DROP TEMPORARY TABLE IF EXISTS {DBNICK}_tmp_branch";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "CREATE TEMPORARY TABLE {DBNICK}_tmp_branch ENGINE = MEMORY
                WITH RECURSIVE branch AS (
                     SELECT id
                       FROM {DBNICK}_$mTable
                      WHERE id IN ( $list )
                      UNION ALL
                     SELECT t.id
                       FROM {DBNICK}_$mTable t
                       JOIN branch b
                         ON t.tindex = b.id
                )
                     SELECT id
                       FROM branch
                   ";
    MELBIS()->SqlQuery(__LINE__, $command);

    return '{DBNICK}_tmp_branch';
}


/**
 * Function TreePath
 * Builds the route to a node: every name from the top down, the way the program shows it
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

    // Trims the dashes: the top branches are drawn as separators in the program
    $said = [trim($tree[$id]['name'], " \t-")];

    // Stops on a node met twice: a tree damaged into a ring would walk for ever
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
 * Builds the same route to a row found by a column of its own instead of by its id
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


/**
 * Function Pos
 * Reorders a flat list three ways - POS, MOVE, SORT - inside one scope
 **/
function Pos($mTable, $mScope, $mType, $mData)
{
    $type = strtoupper(trim((string)$mType));
    $was = PosRead($mTable, $mScope);

    if ( count($was) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'There is nothing there to put in order'
            ];
    }

    if ( $type == 'SORT' )
    {
        if ( !is_array($mData) || !isset($mData['field']) )
        {
            return [
                'result'  => false,
                'message' => 'SORT takes data as field and direction: {"field": "name", '.
                             '"direction": "ASC"}. A list of rows is what POS and MOVE take'
                ];
        }

        $weighed = ColumnOne($mTable, $mData['field'] ?? '');
        if ( !$weighed['result'] ) return $weighed;

        $way = strtoupper(trim((string)( $mData['direction'] ?? 'ASC' )));
        if ( $way != 'ASC' && $way != 'DESC' )
        {
            return [
                'result'  => false,
                'message' => 'The direction of an order is ASC or DESC, and ['.$way.'] is neither'
                ];
        }

        $order = PosRead($mTable, $mScope, $weighed['field'].' '.$way);
        $moved = PosWrite($mTable, $was, array_keys($order));

        return [
            'result'  => true,
            'moved'   => $moved,
            'said'    => 'sorted by '.$weighed['field'].' '.$way
            ];
    }

    if ( $type != 'POS' && $type != 'MOVE' )
    {
        return [
            'result'  => false,
            'message' => 'The type of an order is POS - the rows named go to the front, MOVE - each '.
                         'named row shifts by inc places, minus upwards, or SORT - the whole list by '.
                         'a column. And ['.$type.'] is none of them'
            ];
    }

    if ( !is_array($mData) || count($mData) == 0 )
    {
        return [
            'result'  => false,
            'message' => $type.' takes data as a list of rows: [{"id": 22}] for POS, '.
                         '[{"id": 22, "inc": -5}] for MOVE'
            ];
    }

    // Weighs every id of the call before a single row is written
    $named = [];
    foreach ( $mData as $one )
    {
        if ( !is_array($one) )
        {
            return [
                'result'  => false,
                'message' => 'Every row of data is an object of its own, {"id": 22} at the least'
                ];
        }

        $weighed = Number($one['id'] ?? '', 'id', true);
        if ( !$weighed['result'] ) return $weighed;

        $id = $weighed['value'];
        if ( !isset($was[$id]) )
        {
            $list = implode(', ', array_keys($was));

            return [
                'result'  => false,
                'message' => 'The row ['.$id.'] is not in this list - it holds: '.$list
                ];
        }

        $inc = 0;
        if ( $type == 'MOVE' )
        {
            $weighed = Number($one['inc'] ?? '', 'inc', true);
            if ( !$weighed['result'] ) return $weighed;

            $inc = $weighed['value'];
        }

        $named[] = [$id, $inc];
    }

    $order = array_keys($was);

    if ( $type == 'POS' )
    {
        // The rows of the call take the front in the order given, the rest keep theirs behind
        $front = [];
        foreach ( $named as $pair ) { $front[] = $pair[0]; }

        $tail = [];
        foreach ( $order as $id )
        {
            if ( in_array($id, $front) ) continue;

            $tail[] = $id;
        }
        $order = array_merge($front, $tail);
        $said = count($front).' row(s) to the front';
    }
    else
    {
        // Shifts one row after another, each from where the list stands after the one before
        foreach ( $named as $pair )
        {
            $at = array_search($pair[0], $order);
            $to = $at + $pair[1];
            if ( $to < 0 ) $to = 0;
            if ( $to > count($order) - 1 ) $to = count($order) - 1;

            array_splice($order, $at, 1);
            array_splice($order, $to, 0, [$pair[0]]);
        }
        $said = count($named).' row(s) shifted';
    }

    $moved = PosWrite($mTable, $was, $order);

    return [
        'result'  => true,
        'moved'   => $moved,
        'said'    => $said
        ];
}


/**
 * Function PosRead
 * Reads a flat list as a map of id to pos, in the order asked for
 **/
function PosRead($mTable, $mScope, $mOrder = 'pos')
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
 * Function PosWrite
 * Writes a list back in order: pos runs 1 to N, and only the rows that moved are touched
 **/
function PosWrite($mTable, $mWas, $mOrder)
{
    $moved = 0;
    foreach ( array_values($mOrder) as $i => $id )
    {
        $pos = $i + 1;
        if ( ( $mWas[$id] ?? 0 ) == $pos ) continue;

        $row = [
            'id'  => $id,
            'pos' => $pos
            ];
        MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_'.$mTable, $row, 'id');
        $moved++;
    }

    return $moved;
}


/**
 * Function DependCount
 * Counts what a deletion would take along, by the map and before anything is said
 **/
function DependCount($mTable, $mIds)
{
    $ids = array_values(array_unique(array_map('intval', (array)$mIds)));

    $count = MELBIS()->SysDependCount($mTable, $ids);
    $ties = MELBIS()->SysDepends($mTable);

    // The map tells the fate of a row by its key: gone with the main row, or left pointing at none
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
 * Sweeps the rows left without their main row and folds the answer by table
 **/
function DependSweep($mTable)
{
    $report = [];
    foreach ( MELBIS()->SysDependSweep($mTable) as $one )
    {
        $where = $one['table'];

        // Two ties may lead to one table, and a busy pass is worth more than a count of zero
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
 * Builds the tail of an answer that says what the sweep took
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
 * Function Exists
 * Reads the ids the table really has, so a command refuses by the difference
 **/
function Exists($mTable, $mIds)
{
    $ids = array_map('intval', (array)$mIds);
    if ( count($ids) == 0 ) return [];

    $list = implode(',', $ids);
    $command = "SELECT id
                  FROM {DBNICK}_$mTable
                 WHERE id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    return array_map('intval', array_column($rows, 'id'));
}


/**
 * Function Only
 * Keeps the columns a tool names in its constant and drops the rest of the call
 **/
function Only($mParam, $mFields)
{
    $may = explode(',', $mFields);
    $may = array_map('trim', $may);

    return array_intersect_key($mParam, array_flip($may));
}


/**
 * Function Number
 * Answers a number or refuses, because a zero here always means something of its own
 **/
function Number($mValue, $mWhat, $mWhole = false)
{
    if ( is_array($mValue) )
    {
        return [
            'result'  => false,
            'message' => 'The '.$mWhat.' takes one number, not a list'
            ];
    }

    $value = trim((string)$mValue);
    $said = ( $mWhole ) ? 'a whole number' : 'a number';

    if ( $value == '' )
    {
        return [
            'result'  => false,
            'message' => 'The '.$mWhat.' takes '.$said.', and nothing was named'
            ];
    }
    if ( !is_numeric($value) )
    {
        return [
            'result'  => false,
            'message' => 'The '.$mWhat.' takes '.$said.', and ['.$value.'] is not one'
            ];
    }
    if ( $mWhole && (float)$value != (int)$value )
    {
        return [
            'result'  => false,
            'message' => 'The '.$mWhat.' takes '.$said.', and ['.$value.'] has a fraction'
            ];
    }

    return [
        'result' => true,
        'value'  => ( $mWhole ) ? (int)$value : (float)$value
        ];
}


/**
 * Function OptionPair
 * Weighs the pair an option row will stand with: the key against its family tree, the value
 * against that key. The row as it is now fills whatever the call left unsaid
 **/
function OptionPair($mFamily, $mParam, $mWas = [])
{
    $key_id = (int)( $mParam['key_id'] ?? $mWas['key_id'] ?? 0 );
    if ( $key_id > 0 && count(Exists($mFamily.'_key', [$key_id])) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No option ['.$key_id.'] among the '.$mFamily.' options - CmdList answers'.
                         ' the tree of them'
            ];
    }

    $value_id = (int)( $mParam['value_id'] ?? $mWas['value_id'] ?? 0 );
    if ( $value_id == 0 ) return true;

    $table = '{DBNICK}_'.$mFamily.'_key_value';
    $command = "SELECT key_id
                  FROM $table
                 WHERE id = :ID
               ";
    $param_value = [
        'id' => $value_id
        ];
    $row = MELBIS()->SqlSelectFlat(__LINE__, $command, $param_value);
    if ( !isset($row['key_id']) )
    {
        return [
            'result'  => false,
            'message' => 'No value ['.$value_id.'] among the '.$mFamily.' options - CmdList'.
                         ' answers them'
            ];
    }

    $stands = (int)$row['key_id'];
    if ( $key_id > 0 && $stands != $key_id )
    {
        return [
            'result'  => false,
            'message' => 'The value ['.$value_id.'] stands under the option ['.$stands.'], not'.
                         ' under ['.$key_id.']'
            ];
    }

    return true;
}


/**
 * Function ColumnOne
 * Weighs one column against the schema, or refuses it by naming every column of the table
 **/
function ColumnOne($mTable, $mField)
{
    $field = trim((string)$mField);
    $columns = MELBIS()->SysTableColumns($mTable);

    if ( !isset($columns[$field]) )
    {
        $list = implode(', ', array_keys($columns));

        return [
            'result'  => false,
            'message' => 'No column ['.$field.'] in ['.$mTable.'] - it holds: '.$list
            ];
    }

    // Any real column may be ordered by, a long text with it: these lists are short by nature
    return [
        'result' => true,
        'field'  => $field,
        'type'   => $columns[$field]
        ];
}
