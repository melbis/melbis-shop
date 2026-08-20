<?php
/***************************************************************************************************
 * @version 6.5.0.402 @ 2026-08-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
                          
 
namespace MELBIS_INC_LOGIC_ORDER_CALC; 

use MELBIS_INC_LOGIC_ORDER as LOGIC_ORDER;
use MELBIS_INC_LOGIC_COMMON as LOGIC_COMMON;
                                                                                           
 
/** 
 * Function Run
 * Calculate order information
 **/   
function Run($mUserId, $mVersion)
{
    // Normalize and verify
    $before = LOGIC_ORDER\Before($mUserId, $mVersion);
    $mVersion = LOGIC_ORDER\Normalize($mUserId, $mVersion);          
    $mVersion = LOGIC_ORDER\Verify($mUserId, $mVersion, $before);          
    if ( $mVersion['result']['value'] != 'OK' ) return $mVersion;      
                
    // No goods      
    if ( !isset($mVersion['store']) )
    {                                
        $mVersion['total_sum'] = 0;       

        return $mVersion;
    }       
    
    // Get goods sum
    $goods_sum = LOGIC_ORDER\GoodsSum($mVersion);            
    
    // Calculate goods
    foreach ( $mVersion['store'] as &$mStore )
    { 
        if ( $mStore['recalc'] == 1 )
        {
            $mStore['recalc'] = 0;
            $mStore['auto_notice'] = 'Calculated';
            $disc_proc = LOGIC_ORDER\GoodsDiscount('kOrder', $goods_sum, $mStore['store_id']);
            $mStore['out_price'] = ceil($mStore['store_price']*(1 - $disc_proc/100));
        } 
    }
    unset($mStore);        
    
    // Calculate total sum
    $goods_sum = LOGIC_ORDER\GoodsSum($mVersion, 'out_price');
    foreach ( $mVersion['option'] as $option )
    {             
        $sum = $option['value_modify_sum'];
        if ( $option['value_source_num'] == 1 ) 
        {          
            switch ($option['value_oper_num']) 
            {            
                case '1':
                    $goods_sum += $sum;
                    break;
                case '2':              
                    $goods_sum *= $sum;
                    break;
                case '3':              
                    $goods_sum -= $sum;
                    break;
                case '4':              
                    $goods_sum = ( $sum != 0 ) ? $goods_sum / $sum : 0;
                    break;                        
            } 
        }
    }                                          
    $total_sum = $goods_sum;   
    foreach ( $mVersion['option'] as $option )
    {      
        $sum = $option['value_modify_sum'];
        if ( $option['value_source_num'] == 2 ) 
        {          
            switch ($option['value_oper_num']) 
            {            
                case '1':
                    $total_sum += $sum;
                    break;
                case '2':              
                    $total_sum *= $sum;
                    break;
                case '3':              
                    $total_sum -= $sum;
                    break;
                case '4':              
                    $total_sum = ( $sum != 0 ) ? $total_sum / $sum : 0;
                    break;                        
            } 
        }
    }     
    $mVersion['total_sum'] = $total_sum;       

    return $mVersion;
}


?>