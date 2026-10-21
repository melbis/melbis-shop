<?php
/***************************************************************************************************
 * @version 6.5.1.515 @ 2026-10-21
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Run - Writes an order version
 *
 **************************************************************************************************/
                          
 
namespace MELBIS_INC_LOGIC_ORDER_EDIT; 

use MELBIS_INC_LOGIC_ORDER as LOGIC_ORDER;
use MELBIS_INC_LOGIC_COMMON as LOGIC_COMMON;


// ————— Public —————
//==================================================================================================


/** 
 * Function Run
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
        if ( is_array($mVersion['client']) )
        {
            $count = count($mVersion['client']);
            $ids = MELBIS()->SqlGenIdBlock('client_field', $count);
            $rows = [];
            foreach ( $mVersion['client'] as $item )
            {
                $id = array_shift($ids);
                $rows[] = [
                    'id'        => $id,
                    'client_id' => $client['id'],
                    'field_id'  => $item['field_id'],
                    'value_id'  => $item['value_id'],
                    'value_txt' => $item['value_txt']
                    ];
            }
            MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_client_field', $rows);
        }
    } 
    else
    {
        // Update client fields    
        if ( is_array($mVersion['client']) )
        {        
            $field_set = array_column(MELBIS()->SysFieldValues(), null, 'id');
            $rows = [];
            foreach ( $mVersion['client'] as $item ) 
            {
                // Read-only fields stay put
                if ( $field_set[$item['field_id']]['read_only'] ) continue;

                $rows[] = [
                    'client_id' => $client['id'],
                    'field_id'  => $item['field_id'],
                    'value_id'  => $item['value_id'],
                    'value_txt' => $item['value_txt']
                    ];
            }
            $keys = ['client_id', 'field_id'];
            MELBIS()->SqlUpdateBlock(__LINE__, '{DBNICK}_client_field', $rows, $keys);
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
        $rows = [];
        foreach ( $mVersion['client'] as $item )
        {
            $item['version_id'] = $version['id'];
            $rows[] = $item;
        }
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_orders_client_field', $rows);
    }

    // Store
    //------   
    if ( is_array($mVersion['store']) )
    {
        $rows = [];
        foreach ( $mVersion['store'] as $item )
        {
            $item['version_id'] = $version['id'];
            unset($item['store_option']);
            $rows[] = $item;
        }
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_orders_store', $rows);

        // The rows in the order written
        $command = "SELECT id
                      FROM {DBNICK}_orders_store
                     WHERE version_id = :VERSION_ID
                  ORDER BY id
                   ";
        $param = [
            'version_id' => $version['id']
            ];
        $stores = MELBIS()->SqlSelect(__LINE__, $command, $param);
        $ids = array_column($stores, 'id');

        // Order Store Option
        $options = [];
        foreach ( $mVersion['store'] as $item )
        {
            $store_id = array_shift($ids);
            if ( isset($item['store_option']) )
            {
                foreach ( $item['store_option'] as $option ) 
                {                                            
                    $option['version_id'] = $version['id'];
                    $option['order_store_id'] = $store_id;
                    $options[] = $option;
                }
            }
        }
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_orders_store_option', $options);
    }
    
    // Options
    //--------  
    if ( is_array($mVersion['option']) )
    {
        $rows = [];
        foreach ( $mVersion['option'] as $item )
        {
            $item['version_id'] = $version['id'];
            $rows[] = $item;
        }
        MELBIS()->SqlInsertBlock(__LINE__, '{DBNICK}_orders_option', $rows);
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