<?php
/***************************************************************************************************
 * @version 6.5.1.470 @ 2026-09-25
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * FileAllowed - The files of allowed elements
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_FILE;

// Libraries
use MELBIS_INC_AGENT_FILE as FILE;
use MELBIS_INC_AGENT_TABLE as TABLE;


/**
 * Function CmdList
 **/
function CmdList($mUserId, $mParam)
{
    $found = FILE\EntityOne($mParam['entity']);
    if ( $found !== true ) return $found;

    $entity = $mParam['entity'];

    foreach ( $mParam['elem_id'] as $id )
    {
        $gate = FILE\RightElem($mUserId, $entity, $id);
        if ( $gate !== true ) return $gate;
    }

    $rows = FILE\FileAll($entity, $mParam['elem_id'], $mUserId);
    $table = FILE\Home($entity);

    return [
        'result'  => true,
        'message' => 'The files of the elements named',
        'tables'  => [
            $table => $rows
            ]
        ];
}


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    // The right of the element
    $tables = [];
    $kept = [];
    $said = [];
    $touched = [];
    foreach ( $mParam['files'] as $one )
    {
        $entity = $one['entity'];

        // The entity, as elsewhere
        $found = FILE\EntityOne($entity);
        if ( $found !== true )
        {
            FILE\FileDrop($entity, $one['id'], $one['disk'], $mUserId);
            $said[] = $one['real_name'].': '.$found['message'];
            continue;
        }

        $gate = FILE\RightElem($mUserId, $entity, $one['elem_id']);
        if ( $gate === true ) $gate = FILE\Held($mUserId, $entity);
        if ( $gate !== true )
        {
            FILE\FileDrop($entity, $one['id'], $one['disk'], $mUserId);
            $said[] = $one['real_name'].': '.$gate['message'];
            continue;
        }

        $row = FILE\FileOne($entity, $one['id'], $mUserId);
        $table = FILE\Home($entity);
        $tables[$table][] = $row;
        $kept[] = $row['id'];
        $touched[$entity][] = $row['elem_id'];
    }

    foreach ( $touched as $entity => $elems )
    {
        FILE\Touched($mUserId, $entity, $elems);
    }

    // Answers files even when empty
    if ( count($kept) == 0 )
    {
        return [
            'result'  => false,
            'files'   => [],
            'message' => 'No file stayed: '.implode(' | ', $said)
            ];
    }

    $message = count($kept).' file(s) in the store, as they came';
    if ( count($said) > 0 ) $message .= '. Gone: '.implode(' | ', $said);

    return [
        'result'  => true,
        'files'   => $kept,
        'message' => $message,
        'tables'  => $tables
        ];
}


/**
 * Function CmdMake
 **/
function CmdMake($mUserId, $mParam)
{
    $found = FILE\EntityOne($mParam['entity']);
    if ( $found !== true ) return $found;

    $entity = $mParam['entity'];

    $table = FILE\Home($entity);
    $was = FILE\FileOne($entity, $mParam['id'], $mUserId);
    if ( !isset($was['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No file ['.$mParam['id'].'] in '.$table
            ];
    }

    $gate = FILE\RightElem($mUserId, $entity, $was['elem_id']);
    if ( $gate !== true ) return $gate;

    $row = FILE\ProfileOne($mParam['profile']);
    if ( !isset($row['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No profile ['.$mParam['profile'].']'
            ];
    }

    $show = FILE\ProfileShow($row, true);
    if ( isset($show['broken']) )
    {
        return [
            'result'  => false,
            'message' => 'The recipe of ['.$mParam['profile'].'] is unreadable'
            ];
    }

    $hold = TABLE\Hold($mUserId, $table);
    if ( !$hold['result'] ) return $hold;

    // One act of the workshop
    $made = FILE\Make($mUserId, $entity, $was, $mParam['profile'], $show,
                      $mParam['real_name'] ?? '');

    TABLE\Release($mUserId, $table);

    if ( !$made['result'] ) return $made;

    $elems = [$was['elem_id']];
    FILE\Touched($mUserId, $entity, $elems);

    $row = FILE\FileOne($entity, $made['id'], $mUserId);

    return [
        'result'  => true,
        'message' => $made['message'],
        'detail'  => [
            'id' => $made['id']
            ],
        'tables'  => [
            $table => [$row]
            ]
        ];
}


/**
 * Function CmdUpdate
 **/
function CmdUpdate($mUserId, $mParam)
{
    $found = FILE\EntityOne($mParam['entity']);
    if ( $found !== true ) return $found;

    $entity = $mParam['entity'];

    $named = FileAllowed($mUserId, $entity, $mParam['id']);
    if ( !$named['result'] ) return $named;

    // Every field is a column
    $fields = $mParam;
    unset($fields['entity']);

    $ids = array_column($named['rows'], 'id');
    $table = FILE\Home($entity);

    $said = TABLE\Update($mUserId, $table, $ids, $fields);
    if ( !$said['result'] ) return $said;

    $elems = array_column($named['rows'], 'elem_id');
    FILE\Touched($mUserId, $entity, $elems);

    return $said;
}


/**
 * Function CmdRemove
 **/
function CmdRemove($mUserId, $mParam)
{
    $found = FILE\EntityOne($mParam['entity']);
    if ( $found !== true ) return $found;

    $entity = $mParam['entity'];

    $named = FileAllowed($mUserId, $entity, $mParam['id']);
    if ( !$named['result'] ) return $named;

    $ids = array_column($named['rows'], 'id');
    $table = FILE\Home($entity);

    $mParam['apply'] = true;
    $said = TABLE\Remove($mUserId, $table, $ids, $mParam);
    if ( !$said['result'] ) return $said;

    $elems = array_column($named['rows'], 'elem_id');
    FILE\Touched($mUserId, $entity, $elems);

    return $said;
}


/**
 * Function CmdPos
 **/
function CmdPos($mUserId, $mParam)
{
    $found = FILE\EntityOne($mParam['entity']);
    if ( $found !== true ) return $found;

    $entity = $mParam['entity'];

    $gate = FILE\RightElem($mUserId, $entity, $mParam['elem_id']);
    if ( $gate !== true ) return $gate;

    // Every file of the element
    $scope = [
        'elem_id'  => $mParam['elem_id']
        ];

    $table = FILE\Home($entity);
    $said = TABLE\Pos($mUserId, $table, $scope, $mParam);
    if ( !$said['result'] ) return $said;

    $elems = [$mParam['elem_id']];
    FILE\Touched($mUserId, $entity, $elems);

    return $said;
}


/**
 * Function FileAllowed
 **/
function FileAllowed($mUserId, $mEntity, $mIds)
{
    // Weighs the element of each
    $list = implode(',', $mIds);
    $table = FILE\Home($mEntity);
    $mine = '';
    if ( $table != 'files_'.$mEntity ) $mine = 'AND user_id = '.(int)$mUserId;

    $command = "SELECT *
                  FROM {DBNICK}_$table
                 WHERE id IN ( $list )
                       $mine
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $lost = array_diff($mIds, array_column($rows, 'id'));
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No files ['.$said.'] in '.$table
            ];
    }

    $seen = [];
    foreach ( $rows as $was )
    {
        if ( isset($seen[$was['elem_id']]) ) continue;

        $gate = FILE\RightElem($mUserId, $mEntity, $was['elem_id']);
        if ( $gate !== true ) return $gate;

        $seen[$was['elem_id']] = 1;
    }

    return [
        'result' => true,
        'rows'   => $rows
        ];
}


?>
