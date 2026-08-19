<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

namespace MELBIS_STORE_IMAGE;

/**
 * Function Main
 **/
function Main($mVars)
{
    // Create
    $tpl = MELBIS()->TplCreate();

    // Vars
    $key = $mVars['key'];
    $id = $mVars['id'];

    // Every goods id the page has collected - one query serves all the cards
    $ids = MELBIS()->EnumGet('store', $id);

    // Get image - elem_id keys the enum, the other two are what the view prints
    $command = "SELECT elem_id, upload_time, file_name
                  FROM {DBNICK}_files_store
                 WHERE kind_key = :KEY
                   AND elem_id IN ( $ids )
                ";
    $param = [
        'key'   => $key
        ];
    $image = MELBIS()->SqlSelectEnumFlat(__LINE__, $command, 'elem_id', $id, $param);

    // Image
    MELBIS()->TplAssign($tpl, 'IMG', $image);

    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}



?>