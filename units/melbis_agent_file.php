<?php
/***************************************************************************************************
 * @version 6.5.1.425 @ 2026-09-04
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
use MELBIS_INC_AGENT_SYSTEM as SYS;
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

    $rows = FILE\FileAll($entity, $mParam['elem_id']);

    return [
        'result'  => true,
        'message' => 'The files of the elements named',
        'tables'  => [
            'files_'.$entity => $rows
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
    foreach ( $mParam['files'] as $one )
    {
        $entity = $one['entity'];

        $gate = FILE\RightElem($mUserId, $entity, $one['elem_id']);
        if ( $gate !== true )
        {
            FILE\FileDrop($entity, $one['id'], $one['disk']);
            $said[] = $one['real_name'].': '.$gate['message'];
            continue;
        }

        $row = FILE\FileOne($entity, $one['id']);
        $tables['files_'.$entity][] = $row;
        $kept[] = $row['id'];
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

    $was = FILE\FileOne($entity, $mParam['id']);
    if ( !isset($was['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No file ['.$mParam['id'].'] in files_'.$entity
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

    $tables = ['{DBNICK}_files_'.$entity];
    $lock = SYS\TablesLock($tables, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // One act of the workshop
    $made = FILE\Make($mUserId, $entity, $was, $mParam['profile'], $show,
                      $mParam['real_name'] ?? '');

    SYS\TablesUnlock($tables, $mUserId);

    if ( !$made['result'] ) return $made;

    return [
        'result'  => true,
        'id'      => $made['id'],
        'message' => $made['message'],
        'tables'  => [
            'files_'.$entity => [FILE\FileOne($entity, $made['id'])]
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

    return TABLE\Update($mUserId, 'files_'.$entity, $ids, $fields);
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

    return TABLE\Remove($mUserId, 'files_'.$entity, $ids, $mParam);
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

    // The files of one group
    $scope = [
        'elem_id'  => $mParam['elem_id'],
        'kind_key' => $mParam['kind_key']
        ];

    return TABLE\Pos($mUserId, 'files_'.$entity, $scope, $mParam);
}


/**
 * Function FileAllowed
 **/
function FileAllowed($mUserId, $mEntity, $mIds)
{
    // Weighs the element of each
    $list = implode(',', $mIds);

    $command = "SELECT *
                  FROM {DBNICK}_files_$mEntity
                 WHERE id IN ( $list )
               ";
    $rows = MELBIS()->SqlSelect(__LINE__, $command);

    $lost = array_diff($mIds, array_column($rows, 'id'));
    if ( count($lost) > 0 )
    {
        $said = implode(', ', $lost);

        return [
            'result'  => false,
            'message' => 'No files ['.$said.'] in files_'.$entity
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
