<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

namespace MELBIS_STORE_FEATURES;

/**
 * Function Main
 **/
function Main($mVars)
{
    // Create
    $tpl = MELBIS()->TplCreate();

    // Vars - the place decides which features belong here and how they print
    $id = $mVars['id'];
    $place = $mVars['place'];
    $in_place = ( $place == 'kGoods' ) ? 'i.in_goods' : 'i.in_topic';
    $view = ( $place == 'kGoods' ) ? 'goods' : 'topic';

    // Every goods id the page has collected - one query serves all the cards
    $ids = MELBIS()->EnumGet('store', $id);

    // Get features - each type fills its own column, the view prints the filled one
    $command = "SELECT si.store_id, i.name,
                       CASE WHEN i.type_key = 'kDecimal' THEN ANY_VALUE(si.value_dec)
                        END AS value_dec,
                       CASE WHEN i.type_key = 'kSet'     THEN GROUP_CONCAT(iv.name SEPARATOR ', ')
                            WHEN i.type_key = 'kText'    THEN ANY_VALUE(si.value_txt)
                        END AS value_txt
                  FROM {DBNICK}_store_info si
                  JOIN {DBNICK}_info i
                    ON i.id = si.info_id
             LEFT JOIN {DBNICK}_info_value iv
                    ON iv.id = si.value_id
                 WHERE si.store_id IN ( $ids )
                   AND $in_place = 1
              GROUP BY si.store_id, i.id
              ORDER BY i.absindex
                ";
    $info = MELBIS()->SqlSelectEnum(__LINE__, $command, 'store_id', $id);
    if ( empty($info) ) return '';

    // Assign
    MELBIS()->TplAssign($tpl, 'INFO', $info);

    // Final
    return MELBIS()->TplFinal($tpl, $view);
}



?>