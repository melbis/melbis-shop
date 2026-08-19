<?php
/***************************************************************************************************
 * @version 6.5.0.400 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 * 
 * Create           - Create new order version
 * Normalize        - Normalize order version
 * Verify           - What the shop refuses to take, called by Calculate and Edit
 * Before           - The order as it stands now, for Verify to compare with
 * Load             - Load current order version
 * GoodsAdd         - Add goods to version
 * GoodsRemove      - Remove goods from version 
 * GoodsSum         - Calculate goods sum in the order 
 * GoodsDiscount    - Calculate goods discount info
 * OptionSet        - Set order option value by key
 * 
 **************************************************************************************************/
                          
 
namespace MELBIS_INC_LOGIC_ORDER; 

use MELBIS_INC_LOGIC_COMMON as LOGIC_COMMON;
                                            
                                               
/** 
 * Function Create
 * Create new order version
 **/   
function Create()
{
    $now = MELBIS()->DateTime('now');  
    
    $version =  array('order_id'            => null, 
                      'user_id'             => null, 
                      'client_id'           => null,  
                      'date_time'           => $now, 
                      'total_sum'           => 0,
                      'order_code'          => '', 
                      'order_version_id'    => null,
                      'order_date_time'     => $now 
                      );

    // Client - the registry answers the fields in the order of the shop
    //-------
    $version['client'] = [];
    foreach ( MELBIS()->SysFieldValues() as $field )
    {
        $version['client'][] = [
            'field_id'          => $field['id'],
            'field_skey'        => $field['skey'],
            'field_name'        => $field['name'],
            'field_tindex'      => $field['tindex'],
            'field_tlevel'      => $field['tlevel'],
            'field_absindex'    => $field['absindex'],
            'field_folder'      => $field['folder'],
            'field_kind_key'    => $field['kind_key'],
            'field_spec_key'    => $field['spec_key'],
            'value_id'          => null,
            'value_skey'        => '',
            'value_code'        => '',
            'value_kind_key'    => '',
            'value_txt'         => ''
            ];
    }

    // Goods 
    //------
    $version['store'] = [];  
    
    // Option - the value the shop offers by itself, its sum brought to the base currency
    //-------
    $version['option'] = [];
    foreach ( MELBIS()->SysOrderOptionValues() as $option )
    {
        $value = array_column($option['value'], null, 'use_default')[1] ?? [];
        
        $version['option'][] = [
            'option_id'         => $option['id'],
            'option_skey'       => $option['skey'],
            'option_name'       => $option['name'],
            'option_kind_key'   => $option['kind_key'],
            'option_pos'        => $option['pos'],
            'value_id'          => $value['id'] ?? null,
            'value_skey'        => $value['skey'] ?? '',
            'value_name'        => $value['name'] ?? '',
            'value_kind_key'    => $value['kind_key'] ?? '',
            'value_modify_sum'  => LOGIC_COMMON\Price($value['modify_sum'] ?? 0, $value['sum_curr_id'] ?? 0),
            'value_oper_num'    => $value['oper_num'] ?? 0,
            'value_source_num'  => $value['source_num'] ?? 0,
            'notice'            => ''
            ];
    }
    
    return $version;   
}


/** 
 * Function Normalize      
 * Normalize order version
 **/
function Normalize($mUserId, $mVersion)
{ 
    // Default       
    $mVersion['result']['value'] = 'OK';
    $mVersion['result']['message'] = '';           
    
    // Create base template    
    $template = Create();
                   
    // Client fields    
    //--------------
    $field_set = array_column(MELBIS()->SysFieldValues(), null, 'id');
    $fields = array_column($mVersion['client'] ?? [], null, 'field_id');
    foreach ( $template['client'] as &$temp )
    {                                                                              
        if ( !isset($fields[$temp['field_id']]) ) continue;
        
        $field = $fields[$temp['field_id']]; 
        $row = $field_set[$field['field_id']];
        $one = array_column($row['value'], null, 'id')[$field['value_id']] ?? [];
        
        // The row of a version wears its own names
        $value = [
            'value_id'          => $one['id'] ?? null,
            'value_skey'        => $one['skey'] ?? '',
            'value_code'        => $one['code'] ?? '',
            'value_txt'         => $one['name'] ?? '',
            'value_kind_key'    => $one['kind_key'] ?? ''
            ];
        
        if ( $row['fixed_set'] == 0 )
        {                                                  
            if ( $value['value_txt'] != $field['value_txt'] )
            {
                $value['value_id'] = null;
                $value['value_skey'] = '';
                $value['value_code'] = '';
                $value['value_kind_key'] = '';
                $value['value_txt'] = $field['value_txt'];                                                
            }                         
        }
        $temp = array_merge($temp, $value);                
    }  
    unset($temp);                 
    $mVersion['client'] = $template['client'];
    
    // Order options   
    //--------------         
    $option_set = array_column(MELBIS()->SysOrderOptionValues(), null, 'id');
    $options = array_column($mVersion['option'] ?? [], null, 'option_id');
    foreach ( $template['option'] as &$temp )
    {                                                                              
        if ( !isset($options[$temp['option_id']]) ) continue;
        
        $option = $options[$temp['option_id']]; 
        $row = $option_set[$option['option_id']];
        $one = array_column($row['value'], null, 'id')[$option['value_id']] ?? [];
        
        // The row of a version wears its own names, and the sum comes to the currency of the shop
        $value = [
            'value_id'          => $one['id'] ?? null,
            'value_skey'        => $one['skey'] ?? '',
            'value_name'        => $one['name'] ?? '',
            'value_kind_key'    => $one['kind_key'] ?? '',
            'value_source_num'  => $one['source_num'] ?? 0,
            'value_oper_num'    => $one['oper_num'] ?? 0,
            'value_modify_sum'  => LOGIC_COMMON\Price($one['modify_sum'] ?? 0, $one['sum_curr_id'] ?? 0)
            ];
        
        if ( $row['fixed_set'] == 1 && !isset($value['value_id']) && isset($temp['value_id']) )
        {
            $value = array_merge($value, $temp);
        }
        if ( $row['custom_sum'] != 0 )
        {
            $value['value_modify_sum'] = $option['value_modify_sum'];
        }                                                                  
        if ( $row['fixed_set'] == 0 )
        {                                                  
            if ( $value['value_name'] != $option['value_name'] )
            {
                $value['value_id'] = null;
                $value['value_skey'] = '';                        
                $value['value_kind_key'] = '';
                $value['value_source_num'] = 0;
                $value['value_oper_num'] = 0;                                                
                $value['value_name'] = $option['value_name'];
                if ( $row['custom_sum'] == 0 ) $value['value_modify_sum'] = 0;                                                
            }                         
        }
        $temp['notice'] = $option['notice'];
        $temp = array_merge($temp, $value);                
    }                                                           
    unset($temp);
    $mVersion['option'] = $template['option'];
       
      
    // Store options 
    //--------------        
    $mVersion['store'] = $mVersion['store'] ?? []; 
    if ( !empty($mVersion['store']) )
    {
        // Get options - the registry answers them in the order of the shop
        $store_option_set = array_column(MELBIS()->SysOrderStoreOptionValues(), null, 'id');
        
        $template_option = [];
        foreach ( $store_option_set as $option )
        {
            $one = array_column($option['value'], null, 'use_default')[1] ?? [];
            
            $template_option[] = [
                'option_id'         => $option['id'],
                'option_skey'       => $option['skey'],
                'option_kind_key'   => $option['kind_key'],
                'option_name'       => $option['name'],
                'option_pos'        => $option['pos'],
                'value_id'          => $one['id'] ?? null,
                'value_skey'        => $one['skey'] ?? '',
                'value_name'        => $one['name'] ?? '',
                'value_modify_sum'  => LOGIC_COMMON\Price($one['modify_sum'] ?? 0, $one['sum_curr_id'] ?? 0)
                ];
        }
               
        // Update store options
        foreach ( $mVersion['store'] as &$store )             
        {
            $store_option = $store['store_option'] ?? [];
            unset($store['store_option']);
                                    
            if ( empty($template_option) ) continue;
            
            $template = $template_option;
            $options = array_column($store_option, null, 'option_id');                                     
            foreach ( $template as &$temp )
            {                                               
                if ( !isset($options[$temp['option_id']]) ) continue;
                        
                $option = $options[$temp['option_id']];                   
                $row = $store_option_set[$option['option_id']];
                $one = array_column($row['value'], null, 'id')[$option['value_id']] ?? [];
                
                // The row of a version wears its own names, and the sum comes to the currency of the shop
                $value = [
                    'value_id'          => $one['id'] ?? null,
                    'value_skey'        => $one['skey'] ?? '',
                    'value_name'        => $one['name'] ?? '',
                    'value_modify_sum'  => LOGIC_COMMON\Price($one['modify_sum'] ?? 0, $one['sum_curr_id'] ?? 0)
                    ];
                
                if ( $row['fixed_set'] == 1 && !isset($value['value_id']) && isset($temp['value_id']) )
                {
                    $value = array_merge($value, $temp);
                }                                                
                if ( $row['custom_sum'] != 0 )
                {
                    $value['value_modify_sum'] = $option['value_modify_sum'];
                }                                                                  
                if ( $row['fixed_set'] == 0 )
                {                                                  
                    if ( $value['value_name'] != $option['value_name'] )
                    {
                        $value['value_id'] = null;
                        $value['value_skey'] = '';                        
                        $value['value_name'] = $option['value_name'];
                        if ( $row['custom_sum'] == 0 ) $value['value_modify_sum'] = 0;                                                
                    }                         
                }
                $temp = array_merge($temp, $value);
            }                                   
            unset($temp);                                          
            $store['store_option'] = $template;            
        }   
    }
           
    
    return $mVersion;                            
}   


/** 
 * Function Verify
 * What the shop refuses to take: a pair of values, or a change this person may not make
 **/
function Verify($mUserId, $mVersion, $mVersionBefore)
{ 
    // Store options block - the registry knows which pair the shop refuses
    //--------------------   
    $store_block = MELBIS()->SysOrderStoreOptionBlocks();
    foreach ( $mVersion['store'] as $item )
    {                                                
        if ( !isset($item['store_option']) ) continue;
        
        $chosen = [];
        foreach ( $item['store_option'] as $hash )
        {                                    
            if ( !isset($hash['value_id']) ) continue;
            
            $chosen[(int) $hash['value_id']] = true;
        }
        
        foreach ( $store_block as $block )
        {
            if ( !isset($chosen[$block['value1_id']]) ) continue;
            if ( !isset($chosen[$block['value2_id']]) ) continue;
            
            $mVersion['result']['value'] = 'STORE_OPTION_BLOCK';
            $mVersion['result']['message'] = $item['store_name'].":\r\n".$block['message'];
    
            return $mVersion;
        }
    }                        
         
    
    // Order options block - the registry knows which pair the shop refuses
    //--------------------         
    $chosen = [];
    foreach ( $mVersion['option'] as $hash )
    {    
        if ( !isset($hash['value_id']) ) continue;
        
        $chosen[(int) $hash['value_id']] = true;
    }                    
    foreach ( MELBIS()->SysOrderOptionBlocks() as $block )
    {
        if ( !isset($chosen[$block['value1_id']]) ) continue;
        if ( !isset($chosen[$block['value2_id']]) ) continue;
        
        $mVersion['result']['value'] = 'OPTION_BLOCK';
        $mVersion['result']['message'] = $block['message'];
        
        return $mVersion;                 
    }                        
       
         
    // A value kept for an order already created may not be set while the order is being made
    //-------------------------
    if ( is_null($mVersion['order_id']) )
    {
        $after = [];
        foreach ( MELBIS()->SysOrderOptionValues() as $one )
        {
            foreach ( $one['value'] as $value )
            {
                if ( $value['after_create'] ) $after[$value['id']] = $value['name'];
            }
        }
        
        foreach ( $mVersion['option'] as $option )
        {
            if ( !isset($after[$option['value_id']]) ) continue;
            
            $mVersion['result']['value'] = 'OPTION_AFTER_CREATE';
            $mVersion['result']['message'] = 'Error, only for an order already created: '.$option['value_name'];
            
            return $mVersion;
        }
        
        $after = [];
        foreach ( MELBIS()->SysOrderStoreOptionValues() as $one )
        {
            foreach ( $one['value'] as $value )
            {
                if ( $value['after_create'] ) $after[$value['id']] = $value['name'];
            }
        }
        
        foreach ( $mVersion['store'] as $item )
        {
            foreach ( $item['store_option'] ?? [] as $option )
            {
                if ( !isset($after[$option['value_id']]) ) continue;
                
                $mVersion['result']['value'] = 'STORE_OPTION_AFTER_CREATE';
                $mVersion['result']['message'] = $item['store_name'].":\r\n".'Error, only for an order already created: '.$option['value_name'];
                
                return $mVersion;
            }
        }
    }
    
    // The rest is about the person - null means there was nothing to compare with
    //-------------------------
    if ( is_null($mVersionBefore) ) return $mVersion;
    
    // The order stands, and Load gave nothing back: this person may not see it
    if ( empty($mVersionBefore) )
    {
        $mVersion['result']['value'] = 'ORDER_RIGHT';
        $mVersion['result']['message'] = 'Error, no right for this order!';
        
        return $mVersion;
    }
    
    $before = array_column($mVersionBefore['option'], null, 'option_id');
    
    // The right to change an option is asked only where the value really changed
    // The registry of the rights is read once: true means every option at once
    //-------------------------
    $option_right = MELBIS()->SysOrderOptionRight($mUserId);
    if ( $option_right !== true )
    {
        foreach ( $mVersion['option'] as $option )
        {
            // An option the previous version did not know came from the template, not from this person
            if ( !isset($before[$option['option_id']]) ) continue;
            
            $one = $before[$option['option_id']];
            
            if ( $option['value_id'] == ( $one['value_id'] ?? null ) 
              && $option['value_name'] == ( $one['value_name'] ?? '' ) ) continue;
            
            if ( isset($option_right[$option['option_id']]) ) continue;
            
            $mVersion['result']['value'] = 'OPTION_RIGHT';
            $mVersion['result']['message'] = 'Error, no right to change option: '.$option['option_name'];
            
            return $mVersion;
        }
    }
    
    // The same right for the options standing on a goods
    //-------------------------
    $store_right = MELBIS()->SysOrderStoreOptionRight($mUserId);
    if ( $store_right !== true )
    {
        $store_before = array_column($mVersionBefore['store'], 'store_option', 'store_id');
        foreach ( $mVersion['store'] as $item )
        {
            // A goods that was not in the order brings the values of the shop, not of this person
            if ( !isset($store_before[$item['store_id']]) ) continue;
            
            $one_set = array_column($store_before[$item['store_id']], null, 'option_id');
            foreach ( $item['store_option'] ?? [] as $option )
            {
                // An option the previous version did not know came from the template, not from this person
                if ( !isset($one_set[$option['option_id']]) ) continue;
                
                $one = $one_set[$option['option_id']];
                
                if ( $option['value_id'] == ( $one['value_id'] ?? null ) 
                  && $option['value_name'] == ( $one['value_name'] ?? '' ) ) continue;
                
                if ( isset($store_right[$option['option_id']]) ) continue;
                
                $mVersion['result']['value'] = 'STORE_OPTION_RIGHT';
                $mVersion['result']['message'] = $item['store_name'].":\r\n".'Error, no right to change option: '.$option['option_name'];
                
                return $mVersion;
            }
        }
    }
    
    // User order options limit  
    //-------------------------
    // The door knows the owner and reads the right live, a cache would hold a revoked one
    $no_limit = MELBIS()->SysOperRight($mUserId, 'PUT_ORDER_OPTION');
    if ( $no_limit ) return $mVersion;
    
    $was = array_flip(array_filter(array_column($before, 'value_id')));
    $set = [];
    foreach ( $mVersion['option'] as $option )
    {    
        if ( !isset($option['value_id']) ) continue;
        
        $set[(int) $option['value_id']] = true;
    }     
    
    // The registry knows the rules, the door knows the person
    $user = array_column(MELBIS()->SysUsers(), null, 'id')[$mUserId] ?? [];
    $group = $user['group'] ?? [];
    
    foreach ( MELBIS()->SysOrderOptionLimits() as $limit )
    {
        if ( !isset($was[$limit['was_value_id']]) ) continue;
        if ( !isset($set[$limit['set_value_id']]) ) continue;
        
        foreach ( $limit['right'] as $right )
        {
            if ( $right['user_id'] != $mUserId && !in_array($right['group_id'], $group) ) continue;
            
            $mVersion['result']['value'] = 'OPTION_LIMIT';
            $mVersion['result']['message'] = $limit['message'];
        
            return $mVersion;
        }
    }                 
    
    return $mVersion;                            
}   


/** 
 * Function Before
 * The order as it stands now: null when there is nothing to compare with
 **/   
function Before($mUserId, $mVersion)
{
    if ( is_null($mUserId) ) return null;
    if ( is_null($mVersion['order_id']) ) return null;
    
    return Load($mUserId, $mVersion['order_id']);
}


/** 
 * Function Load      
 * Load current order version
 **/
function Load($mUserId, $mOrderId)
{ 
    // Get version 
    //------------           
    $command = "SELECT ov.*, 
                       o.code AS order_code, 
                       o.version_id AS order_version_id, 
                       o.date_time AS order_date_time
                  FROM {DBNICK}_orders o
                  JOIN {DBNICK}_orders_version ov 
                    ON o.version_id = ov.id
                 WHERE o.id = :ORDER_ID
               ";  
    $param = [
        'order_id' => $mOrderId
        ];                       
    $version = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    $version['parent_version_id'] = $version['id'];
    $version['parameters'] = 'kDefault';
    $version_id = $version['id']; 
    unset($version['id']);    
                  
    // Get Client 
    //-----------
    $command = "SELECT *
                  FROM {DBNICK}_orders_client_field 
                 WHERE version_id = :VERSION_ID
              ORDER BY id
               ";    
    $param = [
        'version_id' => $version_id
        ];                         
    $fields = MELBIS()->SqlSelect(__LINE__, $command, $param);
    foreach ( $fields as $field ) 
    {
        unset($field['id']);
        unset($field['version_id']);
        $version['client'][] = $field;        
    }      
    
    // Get Goods 
    //----------
    $command = "SELECT *
                  FROM {DBNICK}_orders_store 
                 WHERE version_id = :VERSION_ID 
              ORDER BY id
               ";  
    $param = [
        'version_id' => $version_id
        ];                             
    $stores = MELBIS()->SqlSelect(__LINE__, $command, $param);
    foreach ( $stores as $store ) 
    {
        $store_id = $store['id'];
        unset($store['id']);
        unset($store['version_id']);                    
        
        // Get goods options
        $command = "SELECT * 
                      FROM {DBNICK}_orders_store_option 
                     WHERE version_id = :VERSION_ID
                       AND order_store_id = :STORE_ID
                  ORDER BY id
                   ";          
        $param = [
            'version_id' => $version_id,
            'store_id'   => $store_id
            ];                     
        $options = MELBIS()->SqlSelect(__LINE__, $command, $param);
        foreach ( $options as $option ) 
        {
            unset($option['id']);   
            unset($option['version_id']);
            unset($option['order_store_id']);
            $store['store_option'][] = $option;            
        }               
        $version['store'][] = $store;                
    }   
    
    // Get Options  
    //------------
    $command = "SELECT *
                  FROM {DBNICK}_orders_option 
                 WHERE version_id = :VERSION_ID
              ORDER BY id
               ";     
    $param = [
        'version_id' => $version_id
        ];                          
    $options = MELBIS()->SqlSelect(__LINE__, $command, $param);
    foreach ( $options as $option ) 
    {
        unset($option['id']);
        unset($option['version_id']);
        $version['option'][] = $option;        
    }                     
    
    // The right of an order comes from the values of its options - one of them is enough
    if ( !is_null($mUserId) )
    {
        $allow = MELBIS()->SysOrderRight($mUserId);
        if ( $allow !== true )
        {
            $allow = array_flip($allow);
            $mine = false;
            foreach ( $version['option'] ?? [] as $option )
            {
                if ( !isset($allow[$option['value_id']]) ) continue;
                
                $mine = true;
                break;
            }
            
            if ( !$mine ) return [];
        }
    }
    
    return $version;                            
}    

 

/** 
 * Function GoodsAdd
 * Add goods to version
 **/   
function GoodsAdd($mVersion, $mStoreId, $mAmount = 1, $mPriceOut = 0)
{
    // Store exists?                      
    $rows = count($mVersion['store']); 
    for ( $i = 0; $i <= $rows - 1; $i++ )
    {                        
        $item = &$mVersion['store'][$i];
        if ( $item['store_id'] == $mStoreId ) 
        {            
            $item['amount'] += $mAmount;
            
            return $mVersion;
        }            
    }
             
    // Get Goods
    $command = "SELECT id AS store_id, 
                       provider_id AS store_provider_id, 
                       brand_id AS store_brand_id, 
                       pprice AS store_pprice, 
                       rprice AS store_rprice, 
                       price AS store_price, 
                       price2 AS store_price2, 
                       price3 AS store_price3, 
                       how AS store_how, 
                       code_shop AS store_code_shop, 
                       code_prov AS store_code_prov, 
                       code_made AS store_code_made, 
                       meas AS store_meas, 
                       name AS store_name, 
                       kind_key AS store_kind_key, 
                       status_key AS store_status_key, 
                       state_key AS store_state_key, 
                       min_order AS store_min_order, 
                       step_order AS store_step_order, 
                       pprice_curr_id, 
                       rprice_curr_id, 
                       price_curr_id, 
                       price2_curr_id, 
                       price3_curr_id
                  FROM {DBNICK}_store
                 WHERE id = :STORE_ID
               ";    
    $param = [
        'store_id'  => $mStoreId
        ];               
    $store = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    if ( isset($store['store_id']) )
    {
        // Every price is written in a currency of its own
        $store['recalc'] = 1;
        $store['store_pprice'] = LOGIC_COMMON\Price($store['store_pprice'], $store['pprice_curr_id']);
        $store['store_rprice'] = LOGIC_COMMON\Price($store['store_rprice'], $store['rprice_curr_id']);        
        $store['store_price'] = LOGIC_COMMON\Price($store['store_price'], $store['price_curr_id']);
        $store['store_price2'] = LOGIC_COMMON\Price($store['store_price2'], $store['price2_curr_id']);
        $store['store_price3'] = LOGIC_COMMON\Price($store['store_price3'], $store['price3_curr_id']);
        $store['out_price'] = $mPriceOut;
        $store['amount'] = $mAmount;
        $store['notice'] = '';
        $store['auto_notice'] = '';                          
        $store['pos'] = count($mVersion['store']) + 1; 
        unset($store['pprice_curr_id']);
        unset($store['rprice_curr_id']);
        unset($store['price_curr_id']);
        unset($store['price2_curr_id']);
        unset($store['price3_curr_id']);
        
        // Set Goods options - the registry answers them in the order of the shop
        $store['store_option'] = [];
        foreach ( MELBIS()->SysOrderStoreOptionValues() as $option )
        {
            $one = array_column($option['value'], null, 'use_default')[1] ?? [];
            
            $store['store_option'][] = [
                'option_id'         => $option['id'],
                'option_skey'       => $option['skey'],
                'option_name'       => $option['name'],
                'option_kind_key'   => $option['kind_key'],
                'option_pos'        => $option['pos'],
                'value_id'          => $one['id'] ?? null,
                'value_skey'        => $one['skey'] ?? '',
                'value_name'        => $one['name'] ?? '',
                'value_modify_sum'  => LOGIC_COMMON\Price($one['modify_sum'] ?? 0, $one['sum_curr_id'] ?? 0)
                ];
        }
        
        // Add goods
        $mVersion['store'][] = $store;                               
    }   
    
    return $mVersion;
}   


/** 
 * Function GoodsRemove
 * Remove goods from version
 **/   
function GoodsRemove($mVersion, $mStoreId)
{    
    $key = array_search($mStoreId, array_column($mVersion['store'], 'store_id'));
    if ($key !== false) array_splice($mVersion['store'], $key, 1);      
    
    return $mVersion;
}    


/** 
 * Function GoodsSum
 * Calculate goods sum in the order
 **/   
function GoodsSum($mVersion, $mPriceType = 'store_price')
{
    return array_reduce(
        $mVersion['store'] ?? [], 
        fn($sum, $item) => $sum + $item[$mPriceType] * $item['amount'], 
        0);
} 


 

/** 
 * Function GoodsDiscount
 * Calculate goods discount info
 **/   
function GoodsDiscount($mKindKey, $mSum, $mStoreId)
{
    $now = MELBIS()->DateTime('now');
                                   
    $command = "SELECT dr.disc_proc, 
                       dr.from_sum, 
                       dr.sum_curr_id 
                  FROM {DBNICK}_store s 
                  JOIN {DBNICK}_disc_group dg 
                    ON s.disc_group_id = dg.id  
                  JOIN {DBNICK}_disc_rate dr 
                    ON dr.group_id = dg.id
                 WHERE s.id = :STORE_ID 
                   AND dr.kind_key = :KIND_KEY
                   AND dr.begin_time < :NOW
                   AND dr.end_time > :NOW
                 ";                  
    $param = [
        'store_id'  => $mStoreId,
        'kind_key'  => $mKindKey,
        'now'       => $now
        ];                  
    $rates = MELBIS()->SqlSelect(__LINE__, $command, $param); 
    
    // The rules of one group make a ladder - the highest one the sum reaches wins
    $disc_proc = 0;
    foreach ( $rates as $rate )
    {
        $from_sum = LOGIC_COMMON\Price($rate['from_sum'], $rate['sum_curr_id']);
        if ( $from_sum <= $mSum && $rate['disc_proc'] > $disc_proc ) $disc_proc = $rate['disc_proc'];
    }
    
    return (int) $disc_proc;
}


/** 
 * Function OptionSet   
 * Set order option value by key
 **/
function OptionSet($mVersion, $mOptionKey, $mValueKey, $mNotice = '', $mSaveNotice = false)
{ 
    // The registry answers every option with its values at once
    $option_set = array_column(MELBIS()->SysOrderOptionValues(), null, 'skey');
    $value_set = array_column($option_set[$mOptionKey]['value'] ?? [], null, 'skey');
    
    $value = $value_set[$mValueKey] ?? [];
    if ( empty($value) ) return $mVersion;
    
    // The row of a version wears its own names, and the sum comes to the currency of the shop
    $hash = [
        'value_id'          => $value['id'],
        'value_skey'        => $value['skey'],
        'value_name'        => $value['name'],
        'value_kind_key'    => $value['kind_key'],
        'value_source_num'  => $value['source_num'],
        'value_oper_num'    => $value['oper_num'],
        'value_modify_sum'  => LOGIC_COMMON\Price($value['modify_sum'] ?? 0, $value['sum_curr_id'])
        ];
    if ( !$mSaveNotice ) $hash['notice'] = $mNotice;
    
    foreach ( $mVersion['option'] as &$option ) 
    {
        if ( $option['option_skey'] != $mOptionKey ) continue;
        
        $option = array_merge($option, $hash);
        break;                
    }
    unset($option);
    
    return $mVersion;               
}

?>