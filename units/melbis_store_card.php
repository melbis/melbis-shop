<?php
/***************************************************************************************************
 * @version 6.5.1.420 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints a goods card
 *
 **************************************************************************************************/

namespace MELBIS_STORE_CARD;

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

    // Every id the page collected
    $ids = MELBIS()->EnumGet('store', $id);

    // Get goods raw, view converts
    $command = "SELECT s.id, 
                       s.name, 
                       s.intro,       
                       s.status_key,
                       s.price, 
                       s.price_curr_id,
                       s.update_time,                        
                       IF(s.code_shop <> '', s.code_shop, s.id) AS code
                  FROM {DBNICK}_store s
                 WHERE s.id IN ( $ids )
                ";
    $store = MELBIS()->SqlSelectEnumFlat(__LINE__, $command, 'id', $id);
    if ( empty($store) ) return '';

    // Price
    $store['price_curr'] = LOGIC_COMMON\Price($store['price'], $store['price_curr_id']);

    // Assign
    MELBIS()->TplAssign($tpl, $store);

    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}



?>