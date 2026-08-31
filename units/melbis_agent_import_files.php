<?php
/***************************************************************************************************
 * @version 6.5.1.418 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


// Name space
namespace MELBIS_AGENT_IMPORT_FILES;

// Libraries
use MELBIS_INC_AGENT_FILE as FILE;
use MELBIS_INC_AGENT_SYSTEM as SYS;


/**
 * Function CmdAdd
 **/
function CmdAdd($mUserId, $mParam)
{
    $profile = trim((string)( $mParam['profile'] ?? '' ));

    // One profile per pack
    $show = [];
    if ( $profile != '' )
    {
        $row = FILE\ProfileOne($profile);
        if ( !isset($row['id']) )
        {
            return [
                'result'  => false,
                'message' => 'No profile ['.$profile.']'
                ];
        }

        $show = FILE\ProfileShow($row, true);
        if ( isset($show['broken']) )
        {
            return [
                'result'  => false,
                'message' => 'The recipe of ['.$profile.'] is unreadable'
                ];
        }
    }

    // The right of each element
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
            'message' => 'No file stayed: '.implode(' | ', $said)
            ];
    }

    $names = array_keys($tables);
    $lock = SYS\TablesLock($names, $mUserId);
    if ( !$lock['result'] ) return $lock;

    // A second picture where asked
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

    SYS\TablesUnlock($names, $mUserId);

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
