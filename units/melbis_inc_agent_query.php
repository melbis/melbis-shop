<?php
/***************************************************************************************************
 * @version 6.5.1.415 @ 2026-08-29
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Sign          - The schema, flat
 * PageLimit     - How much of the answer
 * TotalCount    - How much the block reaches
 * PullCreate    - The ids of a page
 * PullStore     - One page of the goods
 * PullOrders    - One page of the orders
 * PullFull      - Every table of the schema
 * SqlBuild      - Json query into one WHERE
 * SqlNode       - One node of a query
 * SqlLeaf       - One condition of a query
 * SqlValue      - The values of a leaf
 * Tie           - The ends of a stitch
 * Mark          - The column of that role
 * Kind          - The role and the type
 * Allowed       - The columns of a table
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_INC_AGENT_QUERY;

// What a type takes
const TYPE_OPER = [
    'int'      => 'eq, in, between, ge, le',
    'float'    => 'eq, in, between, ge, le',
    'bool'     => 'eq',
    'str'      => 'eq, in, like',
    'date'     => 'eq, between, ge, le',
    'time'     => 'eq, between, ge, le',
    'datetime' => 'eq, between, ge, le'
    ];

// How many conditions a query
const LEAF_MAX = 20;

/**
 * Function Sign
 **/
function Sign($mSchema)
{
    $root = array_key_first($mSchema);

    $rows = [];
    foreach ( $mSchema as $table => $field_set )
    {
        foreach ( $field_set as $field => $word )
        {
            list( $role, $type ) = Kind($word);

            $rows[] = [
                'table' => $table,
                'nest'  => ( $table != $root ),
                'field' => $field,
                'tie'   => $role,
                'type'  => $type,
                'oper'  => TYPE_OPER[$type]
                ];
        }
    }

    return [
        'result'  => true,
        'message' => 'The signature of ['.$root.']',
        'tables'  => [
            'sign' => $rows
            ]
        ];
}


/**
 * Function PageLimit
 **/
function PageLimit($mParam)
{
    $limit = (int)$mParam['limit'];
    $offset = (int)$mParam['offset'];

    if ( $limit < 1 )
    {
        return [
            'result'  => false,
            'message' => 'A page carries at least one row'
            ];
    }

    if ( $offset < 0 )
    {
        return [
            'result'  => false,
            'message' => 'An offset is never below zero'
            ];
    }

    return [
        'result' => true,
        'limit'  => $limit,
        'offset' => $offset
        ];
}


/**
 * Function TotalCount
 **/
function TotalCount($mFrom, $mParam = [], $mColumn = '')
{
    // Counted whole, or by column
    $how = ( $mColumn == '' ) ? '*' : 'DISTINCT '.$mColumn;

    $command = "SELECT COUNT($how)
                       $mFrom
               ";

    return (int)MELBIS()->SqlSelectValue(__LINE__, $command, 0, $mParam);
}


/**
 * Function PullCreate
 **/
function PullCreate($mSelect, $mParam = [])
{
    $table = '{DBNICK}_tmp_pull';

    $command = "DROP TEMPORARY TABLE IF EXISTS $table";
    MELBIS()->SqlQuery(__LINE__, $command);

    $command = "CREATE TEMPORARY TABLE $table ENGINE = MEMORY
                $mSelect
               ";
    MELBIS()->SqlQuery(__LINE__, $command, $mParam);

    return $table;
}


/**
 * Function PullStore
 **/
function PullStore($mFrom, $mLimit, $mOffset, $mParam = [])
{
    $command = "SELECT ts.store_id AS id
                       $mFrom
              GROUP BY ts.store_id
              ORDER BY ts.store_id
                 LIMIT $mLimit OFFSET $mOffset";

    return PullCreate($command, $mParam);
}


/**
 * Function PullOrders
 **/
function PullOrders($mFrom, $mLimit, $mOffset, $mParam = [])
{
    $command = "SELECT o.id
                       $mFrom
              ORDER BY o.id
                 LIMIT $mLimit OFFSET $mOffset";

    return PullCreate($command, $mParam);
}


/**
 * Function PullFull
 **/
function PullFull($mSchema, $mTable)
{
    $root = array_key_first($mSchema);

    $said = [];
    foreach ( $mSchema as $table => $field_set )
    {
        // The root by id
        $fields = Allowed($mSchema, $table, 'x');
        $tie = ( $table == $root ) ? ['id', 'id'] : Tie($mSchema, $table);
        if ( count($tie) == 0 ) continue;

        list( $key, $home ) = $tie;

        // A far tie, through root
        if ( $home == 'id' )
        {
            $bridge = '';
            $place = 'p.id';
        }
        else
        {
            $bridge = "JOIN {DBNICK}_$root r
                        ON r.id = p.id
                      ";
            $place = 'r.'.$home;
        }

        $command = "SELECT $fields
                      FROM $mTable p
                     $bridge JOIN {DBNICK}_$table x
                        ON x.$key = $place
                  ORDER BY p.id, x.id
                   ";
        $said[$table] = MELBIS()->SqlSelect(__LINE__, $command);
    }

    return $said;
}


/**
 * Function SqlBuild
 **/
function SqlBuild($mSchema, $mQuery, $mAlias)
{
    $root = array_key_first($mSchema);

    if ( !is_array($mQuery) || !isset($mQuery[$root]) )
    {
        return [
            'result'  => false,
            'message' => 'A query starts from the table ['.$root.']'
            ];
    }

    // Nothing stands beside the root
    foreach ( array_keys($mQuery) as $word )
    {
        if ( $word == $root ) continue;

        return [
            'result'  => false,
            'message' => 'A query holds ['.$root.'] alone'
            ];
    }

    // The state walks the tree
    $state = [
        'param' => [],
        'alias' => 0,
        'leaf'  => 0
        ];

    $said = SqlNode($mSchema, $root, $mAlias, $mQuery[$root], $state);
    if ( !$said['result'] ) return $said;

    return [
        'result' => true,
        'where'  => $said['sql'],
        'param'  => $state['param'],
        'leaf'   => $state['leaf']
        ];
}


/**
 * Function SqlNode
 **/
function SqlNode($mSchema, $mTable, $mAlias, $mNode, &$mState)
{
    if ( !is_array($mNode) || count($mNode) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'This is no node of a query'
            ];
    }

    // A list is an AND
    if ( isset($mNode[0]) )
    {
        $said = [];
        foreach ( $mNode as $one )
        {
            $part = SqlNode($mSchema, $mTable, $mAlias, $one, $mState);
            if ( !$part['result'] ) return $part;

            $said[] = $part['sql'];
        }

        return [
            'result' => true,
            'sql'    => '( '.implode("\n                         AND ", $said).' )'
            ];
    }

    if ( isset($mNode['field']) )
    {
        return SqlLeaf($mSchema, $mTable, $mAlias, $mNode, $mState);
    }

    if ( isset($mNode['or']) )
    {
        if ( !is_array($mNode['or']) || !isset($mNode['or'][0]) )
        {
            return [
                'result'  => false,
                'message' => 'An or takes a list of nodes'
                ];
        }

        $said = [];
        foreach ( $mNode['or'] as $one )
        {
            $part = SqlNode($mSchema, $mTable, $mAlias, $one, $mState);
            if ( !$part['result'] ) return $part;

            $said[] = $part['sql'];
        }

        return [
            'result' => true,
            'sql'    => '( '.implode("\n                          OR ", $said).' )'
            ];
    }

    if ( isset($mNode['not']) )
    {
        $part = SqlNode($mSchema, $mTable, $mAlias, $mNode['not'], $mState);
        if ( !$part['result'] ) return $part;

        return [
            'result' => true,
            'sql'    => 'NOT '.$part['sql']
            ];
    }

    // What is left is table
    $table = array_key_first($mNode);
    if ( !isset($mSchema[$table]) )
    {
        $list = implode(', ', array_keys($mSchema));

        return [
            'result'  => false,
            'message' => 'No table ['.$table.']; it holds: '.$list
            ];
    }

    if ( $mTable != array_key_first($mSchema) )
    {
        return [
            'result'  => false,
            'message' => 'The table ['.$table.'] goes one level deep'
            ];
    }

    // The schema says what meets
    $tie = Tie($mSchema, $table);
    if ( count($tie) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'No tie between ['.$table.'] and ['.$mTable.']'
            ];
    }
    list( $key, $home ) = $tie;

    $mState['alias']++;
    $alias = 'x'.$mState['alias'];

    $part = SqlNode($mSchema, $table, $alias, $mNode[$table], $mState);
    if ( !$part['result'] ) return $part;

    // An EXISTS, never a join
    $sql = "EXISTS ( SELECT 1
                                       FROM {DBNICK}_$table $alias
                                      WHERE $alias.$key = $mAlias.$home
                                        AND ".$part['sql'].' )';

    return [
        'result' => true,
        'sql'    => $sql
        ];
}


/**
 * Function SqlLeaf
 **/
function SqlLeaf($mSchema, $mTable, $mAlias, $mLeaf, &$mState)
{
    $mState['leaf']++;
    if ( $mState['leaf'] > LEAF_MAX )
    {
        return [
            'result'  => false,
            'message' => 'A query takes '.LEAF_MAX.' conditions at most'
            ];
    }

    $field = (string)$mLeaf['field'];
    if ( !isset($mSchema[$mTable][$field]) )
    {
        $list = implode(', ', array_keys($mSchema[$mTable]));

        return [
            'result'  => false,
            'message' => 'No column ['.$field.'] in ['.$mTable.']'
            ];
    }

    list( $role, $type ) = Kind($mSchema[$mTable][$field]);
    $oper = strtolower(trim((string)( $mLeaf['op'] ?? '' )));

    $may = explode(', ', TYPE_OPER[$type]);
    if ( !in_array($oper, $may) )
    {
        return [
            'result'  => false,
            'message' => 'The column ['.$field.'] takes: '.TYPE_OPER[$type]
            ];
    }

    $said = SqlValue($type, $oper, $mLeaf['value'] ?? null, $field, $mState);
    if ( !$said['result'] ) return $said;

    return [
        'result' => true,
        'sql'    => $mAlias.'.'.$field.' '.$said['sql']
        ];
}


/**
 * Function SqlValue
 **/
function SqlValue($mType, $mOper, $mValue, $mField, &$mState)
{
    // How many values, and which
    $set = ( is_array($mValue) ) ? array_values($mValue) : [$mValue];

    if ( $mOper == 'between' && count($set) != 2 )
    {
        return [
            'result'  => false,
            'message' => 'Between takes two values'
            ];
    }

    if ( $mOper == 'in' && count($set) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'In takes a list of values'
            ];
    }

    if ( $mOper != 'between' && $mOper != 'in' && count($set) != 1 )
    {
        return [
            'result'  => false,
            'message' => 'The operation ['.$mOper.'] takes one value'
            ];
    }

    $bound = [];
    foreach ( $set as $one )
    {
        if ( is_array($one) || is_null($one) )
        {
            return [
                'result'  => false,
                'message' => 'A value is number, word or flag'
                ];
        }

        if ( ( $mType == 'int' || $mType == 'float' ) && !is_numeric($one) )
        {
            return [
                'result'  => false,
                'message' => 'The column ['.$mField.'] takes a number'
                ];
        }

        if ( $mType == 'bool' && !is_bool($one) )
        {
            return [
                'result'  => false,
                'message' => 'The column ['.$mField.'] takes true or false'
                ];
        }

        // The shape here, base after
        if ( ( $mType == 'date' || $mType == 'time' || $mType == 'datetime' )
             && !preg_match('/^[0-9 :\-]{4,19}$/', (string)$one) )
        {
            return [
                'result'  => false,
                'message' => 'The column ['.$mField.'] takes a '.$mType
                ];
        }

        $key = 'q'.count($mState['param']);
        if ( $mType == 'bool' ) $one = ( $one ) ? 1 : 0;
        if ( $mOper == 'like' ) $one = '%'.$one.'%';

        $mState['param'][$key] = $one;
        $bound[] = ':'.strtoupper($key);
    }

    if ( $mOper == 'eq' )      return ['result' => true, 'sql' => '= '.$bound[0]];
    if ( $mOper == 'ge' )      return ['result' => true, 'sql' => '>= '.$bound[0]];
    if ( $mOper == 'le' )      return ['result' => true, 'sql' => '<= '.$bound[0]];
    if ( $mOper == 'like' )    return ['result' => true, 'sql' => 'LIKE '.$bound[0]];
    if ( $mOper == 'between' ) return ['result' => true, 'sql' => 'BETWEEN '.$bound[0].' AND '.$bound[1]];

    return [
        'result' => true,
        'sql'    => 'IN ( '.implode(', ', $bound).' )'
        ];
}


/**
 * Function Tie
 **/
function Tie($mSchema, $mTable)
{
    $root = array_key_first($mSchema);

    $home = Mark($mSchema[$root], 'PK');
    $key = Mark($mSchema[$mTable], 'FK');
    if ( $home == '' || $key == '' ) return [];

    return [$key, $home];
}


/**
 * Function Mark
 **/
function Mark($mFieldSet, $mRole)
{
    foreach ( $mFieldSet as $field => $word )
    {
        list( $role, $type ) = Kind($word);
        if ( $role == $mRole ) return $field;
    }

    return '';
}


/**
 * Function Kind
 **/
function Kind($mWord)
{
    // The role ahead of type
    $said = explode('.', $mWord, 2);
    if ( count($said) < 2 ) return ['', $mWord];

    return [$said[0], $said[1]];
}


/**
 * Function Allowed
 **/
function Allowed($mSchema, $mTable, $mAlias)
{
    $said = [];
    foreach ( $mSchema[$mTable] as $field => $type )
    {
        $said[] = $mAlias.'.'.$field;
    }

    return implode(', ', $said);
}


?>
