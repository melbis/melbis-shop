<?php
/***************************************************************************************************
 * @version 6.5.1.521 @ 2026-10-27
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main - Prints goods taken at random
 *
 **************************************************************************************************/

namespace MELBIS_STORE_RANDOM;


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
    $how = $mVars['how'];

    // Get random, RAND scans
    $command = "SELECT s.id
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON s.id = ts.store_id
                  JOIN {DBNICK}_topic t
                    ON t.id = ts.topic_id
                 WHERE s.no_visible = 0
                   AND t.kind_key <> 'kDoc'
              GROUP BY s.id
              ORDER BY RAND()
                 LIMIT :HOW
                ";
    $param = [
        'how' => $how
        ];
    $goods = MELBIS()->SqlSelect(__LINE__, $command, $param);

    // Goods
    MELBIS()->TplAssign($tpl, 'GOODS', $goods);

    // Save store for static
    $ids = array_column($goods, 'id');
    MELBIS()->EnumSet('store', $ids);

    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}



?>