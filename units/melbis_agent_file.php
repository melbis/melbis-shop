<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdList     - Reads the files of an element, whole rows
 * CmdAdd      - Hangs the files of the call on their elements
 * CmdMake     - Derives a second picture out of one in the store, by a profile
 * CmdUpdate   - Changes the given columns of files, by id
 * CmdRemove   - Deletes the rows of files by id; the pictures wait for the audit
 * CmdPos      - Reorders one group of files of one element
 *
 * FileAllowed - Of the files asked for, the ones behind the right of their elements
 *
 * One tool for every element: the right is that of the element the file hangs on
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_FILE;

// Libraries
use MELBIS_INC_AGENT_FILE as FILE;
use MELBIS_INC_AGENT_UTIL as UTIL;

// The columns a call may write into a file; the picture on the disk is never touched
const FIELDS_WRITE = "kind_key, real_name, pos";


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
        'message' => count($rows).' file(s) on '.count($mParam['elem_id']).' element(s)',
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
    // The engine laid the files down already, so what is left here is the right of the element
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

    // Answers files even when empty: that is how the engine knows the command cleared them
    if ( count($kept) == 0 )
    {
        return [
            'result'  => false,
            'files'   => [],
            'message' => 'Not one of the files stayed, and they are gone from the store. '.
                         implode(' | ', $said)
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
            'message' => 'No file ['.$mParam['id'].'] among files_'.$entity.' - CmdList answers them'
            ];
    }

    $gate = FILE\RightElem($mUserId, $entity, $was['elem_id']);
    if ( $gate !== true ) return $gate;

    $row = FILE\ProfileOne($mParam['profile']);
    if ( !isset($row['id']) )
    {
        return [
            'result'  => false,
            'message' => 'No profile ['.$mParam['profile'].'] in the registry - the Profiles tool '.
                         'answers them'
            ];
    }

    $show = FILE\ProfileShow($row, true);
    if ( isset($show['broken']) )
    {
        return [
            'result'  => false,
            'message' => 'The recipe of ['.$mParam['profile'].'] is not readable - the program\'s '.
                         'editor owns that row'
            ];
    }

    $tables = ['{DBNICK}_files_'.$entity];
    $lock = UTIL\TablesLock($tables);
    if ( !$lock['result'] ) return $lock;

    // The painting is one act of the workshop, the same the loader of the files runs
    $made = FILE\Make($mUserId, $entity, $was, $mParam['profile'], $show,
                      $mParam['real_name'] ?? '');

    UTIL\TablesUnlock($tables);

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

    $fields = UTIL\Only($mParam, FIELDS_WRITE);
    if ( count($fields) == 0 )
    {
        return [
            'result'  => false,
            'message' => 'Nothing was named to change'
            ];
    }

    $table = '{DBNICK}_files_'.$entity;
    $lock = UTIL\TablesLock([$table]);
    if ( !$lock['result'] ) return $lock;

    foreach ( $named['rows'] as $was )
    {
        $row = $fields;
        $row['id'] = $was['id'];
        MELBIS()->SqlUpdate(__LINE__, $table, $row, 'id');
    }

    UTIL\TablesUnlock([$table]);

    $changed = implode(', ', array_keys($fields));

    return [
        'result'  => true,
        'message' => count($named['rows']).' file(s) changed: '.$changed
        ];
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

    $list = implode(',', array_column($named['rows'], 'id'));

    $table = '{DBNICK}_files_'.$entity;
    $lock = UTIL\TablesLock([$table]);
    if ( !$lock['result'] ) return $lock;

    $command = "DELETE
                  FROM {DBNICK}_files_$entity
                 WHERE id IN ( $list )
               ";
    MELBIS()->SqlQuery(__LINE__, $command);

    UTIL\TablesUnlock([$table]);

    return [
        'result'  => true,
        'message' => count($named['rows']).' file row(s) off files_'.$entity.'. The pictures '.
                     'themselves stay on the disk until the owner runs the idle-files audit - '.
                     'nothing in this store deletes a file from there'
        ];
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

    // The list here is one group of one element, and the rows put in order are its files
    $scope = [
        'elem_id'  => $mParam['elem_id'],
        'kind_key' => $mParam['kind_key']
        ];

    $table = '{DBNICK}_files_'.$entity;
    $lock = UTIL\TablesLock([$table]);
    if ( !$lock['result'] ) return $lock;

    $done = UTIL\Pos('files_'.$entity, $scope, $mParam['type'], $mParam['data'] ?? []);

    UTIL\TablesUnlock([$table]);

    if ( !$done['result'] ) return $done;

    return [
        'result'  => true,
        'message' => 'The group ['.$mParam['kind_key'].']: '.$done['said'].', '.$done['moved'].
                     ' row(s) moved'
        ];
}


/**
 * Function FileAllowed
 **/
function FileAllowed($mUserId, $mEntity, $mIds)
{
    // Reads the rows of the files asked for, then weighs the element every one hangs on
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
            'message' => 'No files ['.$said.'] among files_'.$mEntity.' - CmdList answers them'
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
