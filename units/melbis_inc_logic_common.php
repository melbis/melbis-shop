<?php
/***************************************************************************************************
 * @version 6.5.1.420 @ 2026-09-01
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Rate  - The rate of one currency
 * Price - A sum in shop money
 *
 **************************************************************************************************/

namespace MELBIS_INC_LOGIC_COMMON;

/** 
 * Function Rate
 **/   
function Rate($mCurrId)
{
    static $curr = null;
    
    if ( is_null($curr) )
    {    
        $curr = array_column(MELBIS()->SysCurrencies(), 'rate', 'id');
    }
    
    return $curr[$mCurrId] ?? 1;
}


/** 
 * Function Price
 **/   
function Price($mSum, $mCurrId)
{
    return round($mSum * Rate($mCurrId), 2);
}


?>