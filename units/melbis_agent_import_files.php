<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * CmdAdd - Hangs a pack of files on their elements and derives a picture where a profile asks
 *
 * Runs after the loader of the goods: the store has laid the files down, the rows carry their ids
 *
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_IMPORT_FILES;

// Libraries
use MELBIS_INC_AGENT_FILE as FILE;
use MELBIS_INC_AGENT_UTIL as UTIL;


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $profile = trim((string)( $mParam['profile'] ?? '' ));

    // Reads the profile once for the whole pack, however many pictures lean on it
    $show = [];
    if ( $profile != '' )
    {
        $row = FILE\ProfileOne($profile);
        if ( !isset($row['id']) )
        {
            return [
                'result'  => false,
                'message' => 'No profile ['.$profile.'] in the registry - the Profiles tool '.
                             'answers them'
                ];
        }

        $show = FILE\ProfileShow($row, true);
        if ( isset($show['broken']) )
        {
            return [
                'result'  => false,
                'message' => 'The recipe of ['.$profile.'] is not readable - the program\'s editor '.
                             'owns that row'
                ];
        }
    }

    // Weighs the right of the element every file landed on, and refuses the rest by name
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

        $tables['{DBNICK}_files_'.$entity] = 1;
        $kept[] = $one;
    }

    if ( count($kept) == 0 )
    {
        return [
            'result'  => false,
            'files'   => [],
            'message' => 'Not one of the files stayed, and they are gone from the store. '.
                         implode(' | ', $said)
            ];
    }

    $names = array_keys($tables);
    $lock = UTIL\TablesLock($names);
    if ( !$lock['result'] ) return $lock;

    // Derives a second picture for the rows that carry a profile, or take the one of the pack
    $rows = [];
    $born = 0;
    $ids = [];
    foreach ( $kept as $one )
    {
        $entity = $one['entity'];
        $ids[] = $one['id'];

        $rows['files_'.$entity][] = FILE\FileOne($entity, $one['id']);

        $mask = trim((string)( $one['profile'] ?? $profile ));
        if ( $mask == '' ) continue;

        $recipe = $show;
        if ( $mask != $profile )
        {
            $was = FILE\ProfileOne($mask);
            if ( !isset($was['id']) )
            {
                $said[] = $one['real_name'].': no profile ['.$mask.'] in the registry';
                continue;
            }

            $recipe = FILE\ProfileShow($was, true);
            if ( isset($recipe['broken']) )
            {
                $said[] = $one['real_name'].': the recipe of ['.$mask.'] is not readable';
                continue;
            }
        }

        $was = FILE\FileOne($entity, $one['id']);
        $made = FILE\Make($mUserId, $entity, $was, $mask, $recipe);
        if ( !$made['result'] )
        {
            $said[] = $one['real_name'].': '.$made['message'];
            continue;
        }

        $rows['files_'.$entity][] = FILE\FileOne($entity, $made['id']);
        $born++;
    }

    UTIL\TablesUnlock($names);

    $message = count($kept).' file(s) in the store';
    if ( $born > 0 ) $message .= ', '.$born.' derived picture(s) painted beside them';
    if ( count($said) > 0 ) $message .= '. Went wrong: '.implode(' | ', $said);

    return [
        'result'  => true,
        'files'   => $ids,
        'message' => $message,
        'tables'  => $rows
        ];
}

?>
