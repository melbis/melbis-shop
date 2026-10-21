<?php
/***************************************************************************************************
 * @version 6.5.1.516 @ 2026-10-21
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints a goods picture
 *
 **************************************************************************************************/

namespace MELBIS_STORE_IMAGE;


// ————— Public —————
//==================================================================================================


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

    // Every id the page collected
    $ids = MELBIS()->EnumGet('store', $id);

    // Get image, first place wins
    $command = "SELECT elem_id, upload_time, file_name
                  FROM {DBNICK}_files_store
                 WHERE kind_key = :KEY
                   AND elem_id IN ( $ids )
              ORDER BY pos DESC
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