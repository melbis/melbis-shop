<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

namespace MELBIS_PAGE_STORE;

use MELBIS_INC_LOGIC_COMMON as LOGIC_COMMON;

/**
 * Function Main
 **/
function Main($mVars)
{
    // Create
    $tpl = MELBIS()->TplCreate();

    // Vars
    $id = $mVars['id'];

    // Get goods - status_key stays a key, the view turns it into a word
    $command = "SELECT s.id, s.name, s.intro, s.descr, s.how, s.update_time, s.status_key,
                       s.price, s.price_curr_id,
                       IF(s.code_shop <> '', s.code_shop, s.id) AS code,
                       c.name AS curr_name
                  FROM {DBNICK}_store s
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = s.price_curr_id
                 WHERE s.id = :ID
                   AND s.no_visible = 0
                ";
    $param = [
        'id' => $id
        ];
    $store = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    if ( !isset($store['id']) ) return '';

    // Price
    $store['price_curr'] = LOGIC_COMMON\Price($store['price'], $store['price_curr_id']);

    // Assign
    MELBIS()->TplAssign($tpl, $store);

    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}



?>