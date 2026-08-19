<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 * 
 * Rate     - Rate of a currency against the base one
 * Price    - A sum written in a currency, brought to the base one
 * 
 **************************************************************************************************/

namespace MELBIS_INC_LOGIC_COMMON;

/** 
 * Function Rate
 * Rate of a currency against the base one, by the id a row points at
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
 * A sum written in a currency, brought to the base one
 **/   
function Price($mSum, $mCurrId)
{
    return round($mSum * Rate($mCurrId), 2);
}


?>