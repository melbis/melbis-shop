<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/
                          
 
namespace MELBIS_INC_LOGIC_ORDER_EDIT; 

use MELBIS_INC_LOGIC_ORDER as LOGIC_ORDER;
use MELBIS_INC_LOGIC_COMMON as LOGIC_COMMON;

                                                                                           

/** 
 * Function Run
 * Create new version for order
 **/   
function Run($mUserId, $mVersion)
{
    // Vars
    $now = MELBIS()->DateTime('now');
    $result['value'] = 'OK';
    $result['message'] = ''; 
    $result['orders'] = []; 
    
    // Normalize and verify                                          
    $before = LOGIC_ORDER\Before($mUserId, $mVersion);
    $mVersion = LOGIC_ORDER\Normalize($mUserId, $mVersion);                  
    $mVersion = LOGIC_ORDER\Verify($mUserId, $mVersion, $before);                  
    if ( $mVersion['result']['value'] != 'OK' ) return $mVersion['result'];
    
    // Test for intermediate version
    if ( isset($mVersion['order_id']) )
    {            
        $command = "SELECT * 
                      FROM {DBNICK}_orders 
                     WHERE id = :ORDER_ID
                   ";
        $param = [
            'order_id'  => $mVersion['order_id']
            ];
        $hash = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);                
        if ( $mVersion['parent_version_id'] != $hash['version_id'] )
        {
            $result['orders'][] = $mVersion['order_id'];
            $result['value'] = 'VERSION_ERROR';
            $result['message'] = 'Error, order has intermediate version!';
            
            return $result;
        }                
    }             

    // Version   
    //--------
    $version = [];
    $version['id'] = MELBIS()->SqlGenId('orders_version');
    $version['user_id'] = $mUserId;
    $version['order_id'] = $mVersion['order_id'];
    $version['client_id'] = $mVersion['client_id'];
    $version['date_time'] = $now;
    $version['total_sum'] = $mVersion['total_sum'];      
    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders_version', $version);                     
    
    // Client 
    //-------
    $command = "SELECT * 
                  FROM {DBNICK}_client 
                 WHERE id = :CLIENT_ID
               ";     
    $param = [
        'client_id' => $mVersion['client_id']
        ];
    $client = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    if ( !isset($client['id']) )
    {
        // New client
        $client['id'] = MELBIS()->SqlGenId('client');
        $client['login'] = 'auto'.$client['id'].rand(100, 999);                
        $client['pass'] = substr(md5(random_int(0, 9999999)), 1, 8);                
        $client['reg_date'] = $now;
        
        // Add new client        
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_client', $client);        
        
        // Save client field
        $client_field['client_id'] = $client['id'];
        if ( is_array($mVersion['client']) )
        {
            foreach ( $mVersion['client'] as $item ) 
            {
                $client_field['id'] = MELBIS()->SqlGenId('client_field');
                $client_field['field_id'] = $item['field_id'];
                $client_field['value_id'] = $item['value_id'];
                $client_field['value_txt'] = $item['value_txt'];
                MELBIS()->SqlInsert(__LINE__, '{DBNICK}_client_field', $client_field);
            }
        }                                                                 
    } 
    else
    {
        // Update client fields    
        if ( is_array($mVersion['client']) )
        {        
            foreach ( $mVersion['client'] as $item ) 
            {
                $command = "UPDATE {DBNICK}_client_field cfv,
                                   {DBNICK}_field cf
                               SET cfv.value_txt = :VALUE_TXT,
                                   cfv.value_id = :VALUE_ID 
                             WHERE cfv.client_id = :CLIENT_ID
                               AND cfv.field_id = :FIELD_ID
                               AND cfv.field_id = cf.id 
                               AND cf.read_only = 0
                            ";        
                $param = [ 
                    'client_id' => $client['id'],
                    'field_id'  => $item['field_id'],
                    'value_txt' => $item['value_txt'],
                    'value_id'  => $item['value_id']                    
                    ]; 
                MELBIS()->SqlQuery(__LINE__, $command, $param);
                MELBIS()->SqlTableChange(__LINE__, '{DBNICK}_client_field');
            }
        }             
    } 
    
    // Update client last date                                           
    $hash = [];
    $hash['id'] = $client['id'];
    $hash['last_date'] = $now;            
    MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_client', $hash, 'id');      
    
    // Save client field      
    if ( is_array($mVersion['client']) )
    {
        foreach ( $mVersion['client'] as $item ) 
        {    
            $item['version_id'] = $version['id'];
            MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders_client_field', $item);            
        }
    }   

    // Store
    //------   
    if ( is_array($mVersion['store']) )
    {
        foreach ( $mVersion['store'] as $item ) 
        {                                
            $item['version_id'] = $version['id'];            
            $store = $item;
            unset($store['store_option']);            
            MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders_store', $store);
            $store_id = MELBIS()->SqlLastInsertId();
    
            // Order Store Option                     
            if ( isset($item['store_option']) )
            {
                foreach ( $item['store_option'] as $option ) 
                {                                            
                    $option['version_id'] = $version['id'];
                    $option['order_store_id'] = $store_id;                                
                    MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders_store_option', $option);                
                }      
            }
        }
    }         
    
    // Options
    //--------  
    if ( is_array($mVersion['option']) )
    {
        foreach ( $mVersion['option'] as $item ) 
        {  
            $item['version_id'] = $version['id'];
            MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders_option', $item);
        } 
    }
              
    
    // Order  
    //------
    $command = "SELECT * 
                  FROM {DBNICK}_orders 
                 WHERE id = :ORDER_ID
                ";         
    $param = [
        'order_id' => $mVersion['order_id']
        ];
    $order = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    if ( !isset($order['id']) )
    {
        $order['id'] = MELBIS()->SqlGenId('orders');
        $code_parts = str_split(substr(time(), -6), 3);
        $order['code'] = '#'.date("d").'-'.implode('-', $code_parts);                                                                                
        $order['date_time'] = $now;         
        MELBIS()->SqlInsert(__LINE__, '{DBNICK}_orders', $order);
    }    
        
    // Update version
    $version['client_id'] = $client['id'];
    $version['order_id'] = $order['id'];
    MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_orders_version', $version, 'id');    
    
    // Update order
    $order['version_id'] = $version['id'];
    MELBIS()->SqlUpdate(__LINE__, '{DBNICK}_orders', $order, 'id');
    
    // Result orders
    $result['orders'][] = $order['id'];    
        
    return $result;             
}
 

?>