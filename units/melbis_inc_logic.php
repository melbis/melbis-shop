<?php
/***************************************************************************************************
 * @version 6.5.0.304 @ 2026-07-07
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 * 
 * MELBIS_INC_LOGIC_order_create            - Create new order version
 * MELBIS_INC_LOGIC_order_norm              - Normalize order version
 * MELBIS_INC_LOGIC_order_load              - Load current order version
 * MELBIS_INC_LOGIC_order_edit              - Create new version for order
 * MELBIS_INC_LOGIC_order_calc              - Calculate order information
 * MELBIS_INC_LOGIC_order_goods_add         - Add goods to version
 * MELBIS_INC_LOGIC_order_goods_remove      - Remove goods from version 
 * MELBIS_INC_LOGIC_order_goods_sum         - Calculate goods sum in the order 
 * MELBIS_INC_LOGIC_order_goods_discount    - Calculate goods discount info
 * MELBIS_INC_LOGIC_order_option_set        - Set order option value by key
 * MELBIS_INC_LOGIC_notify_events           - Test for system events and notification
 * 
 **************************************************************************************************/
                                                                     
                                               
 
/***************************************************************************************************
 * 
 *                                   ORDER FUNCTIONS
 * 
 **************************************************************************************************/              
 
/** 
 * Function MELBIS_INC_LOGIC_order_create
 * Create new order version
 **/   
function MELBIS_INC_LOGIC_order_create()
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

    // Client   
    //-------
    $command = "SELECT id AS field_id, 
                       skey AS field_skey, 
                       name AS field_name, 
                       tindex AS field_tindex,
                       tlevel AS field_tlevel, 
                       absindex AS field_absindex, 
                       folder AS field_folder,
                       kind_key AS field_kind_key, 
                       spec_key AS field_spec_key, 
                       NULL AS value_id,
                       '' AS value_skey, 
                       '' AS value_code, 
                       '' AS value_kind_key, 
                       '' AS value_txt
                  FROM {DBNICK}_field
              ORDER BY absindex
               ";                        
    $version['client'] = MELBIS()->SqlSelectStatic(__LINE__, $command);

    // Goods 
    //------
    $version['store'] = [];  
    
    // Option
    //-------
    $command = "SELECT oo.id AS option_id, 
                       oo.skey AS option_skey, 
                       oo.name AS option_name, 
                       oo.kind_key AS option_kind_key, 
                       oo.pos AS option_pos,
                       oov.id AS value_id, 
                       IFNULL(oov.skey, '') AS value_skey, 
                       IFNULL(oov.name, '') AS value_name, 
                       IFNULL(oov.kind_key, '') AS value_kind_key,                         
                       IF(oov.modify_sum IS NULL, 0,
                        IF(c.id IS NULL, oov.modify_sum, 
                         IF(c.division = 0, oov.modify_sum*c.multiplex,
                          IF(c.multiplex <> 0, oov.modify_sum/c.multiplex, 0)))) AS value_modify_sum,                          
                       IFNULL(oov.oper_num, 0) AS value_oper_num, 
                       IFNULL(oov.source_num, 0) AS value_source_num, 
                       '' AS notice                                      
                  FROM {DBNICK}_order_option oo
             LEFT JOIN {DBNICK}_order_option_value oov
                    ON oo.id = oov.option_id 
                   AND oov.use_default = 1         
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = oov.sum_curr_id                   
              ORDER BY oo.pos
               ";  
    $version['option'] = MELBIS()->SqlSelectStatic(__LINE__, $command);
    
    return $version;   
}


/** 
 * Function MELBIS_INC_LOGIC_order_norm      
 * Normalize order version
 **/
function MELBIS_INC_LOGIC_order_norm($mUserId, $mVersion)
{ 
    // Default       
    $mVersion['result']['value'] = 'OK';
    $mVersion['result']['message'] = '';           
    
    // Create base template    
    $template = MELBIS_INC_LOGIC_order_create();
                   
    // Client fields    
    //--------------
    $fields = array_column($mVersion['client'] ?? [], null, 'field_id');
    foreach ( $template['client'] as &$temp )
    {                                                                              
        if ( !isset($fields[$temp['field_id']]) ) continue;
        
        $field = $fields[$temp['field_id']]; 
                          
        $command = "SELECT f.fixed_set, 
                           fv.id AS value_id, 
                           IFNULL(fv.skey, '') AS value_skey, 
                           IFNULL(fv.code, '') AS value_code, 
                           IFNULL(fv.name, '') AS value_txt, 
                           IFNULL(fv.kind_key, '') AS value_kind_key 
                      FROM {DBNICK}_field f
                 LEFT JOIN {DBNICK}_field_value fv
                        ON f.id = fv.field_id
                       AND fv.id = :VALUE_ID  
                     WHERE f.id = :FIELD_ID
                    ";  
        $param = [
            'value_id'  => $field['value_id'],
            'field_id'  => $field['field_id']
            ];                                                       
        $value = MELBIS()->SqlSelectStaticFlat(__LINE__, $command, $param);                
        if ( $value['fixed_set'] == 0 )
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
        unset($value['fixed_set']);                
        $temp = array_merge($temp, $value);                
    }  
    unset($temp);                 
    $mVersion['client'] = $template['client'];
    
    // Order options   
    //--------------         
    $options = array_column($mVersion['option'] ?? [], null, 'option_id');
    foreach ( $template['option'] as &$temp )
    {                                                                              
        if ( !isset($options[$temp['option_id']]) ) continue;
        
        $option = $options[$temp['option_id']]; 
                
        $command = "SELECT oo.fixed_set, 
                           oo.custom_sum, 
                           oov.id AS value_id, 
                           IFNULL(oov.skey, '') AS value_skey, 
                           IFNULL(oov.name, '') AS value_name, 
                           IFNULL(oov.kind_key, '') AS value_kind_key, 
                           IFNULL(oov.source_num, 0) AS value_source_num, 
                           IFNULL(oov.oper_num, 0) AS value_oper_num,
                           IF(oov.modify_sum IS NULL, 0,
                           IF(c.id IS NULL, oov.modify_sum, 
                            IF(c.division = 0, oov.modify_sum*c.multiplex,
                             IF(c.multiplex <> 0, oov.modify_sum/c.multiplex, 0)))) AS value_modify_sum                                                                                                                                                                                                           
                      FROM {DBNICK}_order_option oo
                 LEFT JOIN {DBNICK}_order_option_value oov  
                        ON oo.id = oov.option_id
                       AND oov.id = :VALUE_ID  
                 LEFT JOIN {DBNICK}_currency c
                        ON c.id = oov.sum_curr_id                        
                     WHERE oo.id = :OPTION_ID
                    ";                             
        $param = [
            'value_id'  => $option['value_id'],  
            'option_id' => $option['option_id']                    
            ];                            
        $value = MELBIS()->SqlSelectStaticFlat(__LINE__, $command, $param);
        if ( $value['fixed_set'] == 1 && !isset($value['value_id']) && isset($temp['value_id']) )
        {
            $value = array_merge($value, $temp);
        }
        if ( $value['custom_sum'] != 0 )
        {
            $value['value_modify_sum'] = $option['value_modify_sum'];
        }                                                                  
        if ( $value['fixed_set'] == 0 )
        {                                                  
            if ( $value['value_name'] != $option['value_name'] )
            {
                $value['value_id'] = null;
                $value['value_skey'] = '';                        
                $value['value_kind_key'] = '';
                $value['value_source_num'] = 0;
                $value['value_oper_num'] = 0;                                                
                $value['value_name'] = $option['value_name'];
                if ( $value['custom_sum'] == 0 ) $value['value_modify_sum'] = 0;                                                
            }                         
        }
        $temp['notice'] = $option['notice'];
        unset($value['fixed_set']);                
        unset($value['custom_sum']);              
        $temp = array_merge($temp, $value);                
    }                                                           
    unset($temp);
    $mVersion['option'] = $template['option'];
       
      
    // Store options 
    //--------------        
    $mVersion['store'] = $mVersion['store'] ?? []; 
    if ( !empty($mVersion['store']) )
    {
        // Get options
        $command = "SELECT oso.id AS option_id, 
                           oso.skey AS option_skey, 
                           oso.kind_key AS option_kind_key, 
                           oso.name AS option_name, 
                           oso.pos AS option_pos,
                           osov.id AS value_id, 
                           IFNULL(osov.skey, '') AS value_skey, 
                           IFNULL(osov.name, '') AS value_name, 
                           IFNULL(osov.modify_sum, 0) AS value_modify_sum                                 
                      FROM {DBNICK}_order_store_option oso
                 LEFT JOIN {DBNICK}_order_store_option_value osov
                        ON oso.id = osov.option_id   
                       AND use_default = 1
                  ORDER BY oso.pos        
                    ";  
        $template_option = MELBIS()->SqlSelectStatic(__LINE__, $command);
               
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
                                                     
                $command = "SELECT oso.fixed_set, 
                                   oso.custom_sum, osov.id AS value_id, 
                                   IFNULL(osov.skey, '') AS value_skey, 
                                   IFNULL(osov.name, '') AS value_name, 
                                   IF(osov.modify_sum IS NULL, 0,
                                   IF(c.id IS NULL, osov.modify_sum, 
                                    IF(c.division = 0, osov.modify_sum*c.multiplex,
                                     IF(c.multiplex <> 0, osov.modify_sum/c.multiplex, 0)))) AS value_modify_sum                                                                                                                                                                                                           
                              FROM {DBNICK}_order_store_option oso
                         LEFT JOIN {DBNICK}_order_store_option_value osov  
                                ON oso.id = osov.option_id
                               AND osov.id = :VALUE_ID  
                         LEFT JOIN {DBNICK}_currency c
                                ON c.id = osov.sum_curr_id                        
                             WHERE oso.id = :OPTION_ID
                            ";      
                $param = [
                    'value_id'  => $option['value_id'],
                    'option_id' => $option['option_id']
                    ];                                                           
                $value = MELBIS()->SqlSelectStaticFlat(__LINE__, $command, $param);
                if ( $value['fixed_set'] == 1 && !isset($value['value_id']) && isset($temp['value_id']) )
                {
                    $value = array_merge($value, $temp);
                }                                                
                if ( $value['custom_sum'] != 0 )
                {
                    $value['value_modify_sum'] = $option['value_modify_sum'];
                }                                                                  
                if ( $value['fixed_set'] == 0 )
                {                                                  
                    if ( $value['value_name'] != $option['value_name'] )
                    {
                        $value['value_id'] = null;
                        $value['value_skey'] = '';                        
                        $value['value_name'] = $option['value_name'];
                        if ( $value['custom_sum'] == 0 ) $value['value_modify_sum'] = 0;                                                
                    }                         
                }
                unset($value['fixed_set']);                
                unset($value['custom_sum']);
                $temp = array_merge($temp, $value);
            }                                   
            unset($temp);                                          
            $store['store_option'] = $template;            
        }   
    }
           
    
    // Store options block   
    //--------------------   
    foreach ( $mVersion['store'] as $item )
    {                                                
        if ( isset($item['store_option']) )
        {                                        
            $option_value = [];
            foreach ( $item['store_option'] as $hash )
            {                                    
                if ( isset($hash['value_id']) )
                {
                    $option_value[] = (int) $hash['value_id'];
                }
            }
            if ( count($option_value) > 0 )
            {
                $ids = implode(',', $option_value);
                $command = "SELECT id, message 
                              FROM {DBNICK}_order_store_option_block 
                             WHERE value1_id IN ( $ids )
                               AND value2_id IN ( $ids ) 
                          ORDER BY id  
                            ";
                $hash = MELBIS()->SqlSelectStaticFlat(__LINE__, $command);
                if ( isset($hash['id']) )
                {
                    $mVersion['result']['value'] = 'STORE_OPTION_BLOCK';
                    $mVersion['result']['message'] = $item['store_name'].":\r\n".$hash['message'];
            
                    return $mVersion;
                }
            }                 
        }            
    }                        
         
    
    // Order options block  
    //--------------------         
    $option_value = [];
    foreach ( $mVersion['option'] as $hash )
    {    
        if ( isset($hash['value_id']) )
        {
            $option_value[] = (int) $hash['value_id'];
        }
    }                    
    if ( count($option_value) > 0 )
    {
        $ids = implode(',', $option_value);
        $command = "SELECT id, message 
                      FROM {DBNICK}_order_option_block 
                     WHERE value1_id IN ( $ids )
                       AND value2_id IN ( $ids ) 
                  ORDER BY id  
                    ";
        $hash = MELBIS()->SqlSelectStaticFlat(__LINE__, $command);
        if ( isset($hash['id']) )
        {
            $mVersion['result']['value'] = 'OPTION_BLOCK';
            $mVersion['result']['message'] = $hash['message'];
            
            return $mVersion;                 
        }            
    }                        
       
         
    // User order options limit  
    //-------------------------
    if ( isset($mUserId) )
    {                   
        // Super user no limits
        if ( $mUserId == 1 )
        {   
            $no_limit = true;
        }
        else
        {
            $command = "SELECT or_g.id
                          FROM {DBNICK}_oper o
                          JOIN {DBNICK}_oper_right or_g
                            ON or_g.oper_id = o.id
                     LEFT JOIN {DBNICK}_user u
                            ON ( u.group_id = or_g.group_id OR u.add_group_id = or_g.group_id )
                         WHERE o.command = 'PUT_ORDER_OPTION'
                           AND ( or_g.user_id = :USER_ID OR u.id = :USER_ID )
                       ";
            $param = [
                'user_id' => $mUserId
                ];
            $access = MELBIS()->SqlSelectStaticFlat(__LINE__, $command, $param);
            $no_limit = isset($access['id']); 
        }
        
        // Checking         
        if ( !$no_limit && isset($mVersion['order_id']) && is_array($mVersion['option']) )
        {                    
            // Was options
            $command = "SELECT oo.value_id 
                          FROM {DBNICK}_orders o
                          JOIN {DBNICK}_orders_version ov
                            ON o.version_id = ov.id  
                          JOIN {DBNICK}_orders_option oo
                            ON oo.version_id = ov.id 
                         WHERE o.id = :ORDER_ID
                           AND oo.value_id IS NOT NULL 
                       ";              
            $param = [
                'order_id' => $mVersion['order_id']
                ];                       
            $options = MELBIS()->SqlSelectStatic(__LINE__, $command, $param);
            $option_was = [];
            foreach ( $options as $option ) 
            {
                $option_was[] = (int) $option['value_id'];                
            }
                     
            // New options     
            $option_set = [];                                     
            foreach ( $mVersion['option'] as $option )
            {    
                if ( isset($option['value_id']) )
                {
                    $option_set[] = (int) $option['value_id'];
                }
            }     
            
            // Verify
            if ( !empty($option_was) && !empty($option_set) )
            {
                $was = implode(',', $option_was);
                $set = implode(',', $option_set);              
                $command = "SELECT ool.id, ool.message
                              FROM {DBNICK}_order_option_limit ool
                              JOIN {DBNICK}_order_option_limit_right oolr
                                ON ool.id = oolr.limit_id
                         LEFT JOIN {DBNICK}_user u
                                ON ( u.group_id = oolr.group_id 
                                  OR u.add_group_id = oolr.group_id )
                             WHERE ool.was_value_id IN ( $was )
                               AND ool.set_value_id IN ( $set ) 
                               AND ( oolr.user_id = :USER_ID OR u.id = :USER_ID )
                           ";   
                $param = [
                    'user_id'   => $mUserId                
                    ];
                $hash = MELBIS()->SqlSelectStaticFlat(__LINE__, $command, $param);
                if ( isset($hash['id']) )
                {
                    $mVersion['result']['value'] = 'OPTION_LIMIT';
                    $mVersion['result']['message'] = $hash['message'];
                
                    return $mVersion;
                }                 
            }            
        }             
    }     
        
    return $mVersion;                            
}   


/** 
 * Function MELBIS_INC_LOGIC_order_load      
 * Load current order version
 **/
function MELBIS_INC_LOGIC_order_load($mOrderId)
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
    
    return $version;                            
}    


/** 
 * Function MELBIS_INC_LOGIC_order_edit
 * Create new version for order
 **/   
function MELBIS_INC_LOGIC_order_edit($mUserId, $mVersion)
{
    // Vars
    $now = MELBIS()->DateTime('now');
    $result['value'] = 'OK';
    $result['message'] = ''; 
    $result['orders'] = []; 
    
    // Normalize                                           
    $mVersion = MELBIS_INC_LOGIC_order_norm($mUserId, $mVersion);                  
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
 
/** 
 * Function MELBIS_INC_LOGIC_order_calc
 * Calculate order information
 **/   
function MELBIS_INC_LOGIC_order_calc($mUserId, $mVersion)
{
    // Normalize     
    $mVersion = MELBIS_INC_LOGIC_order_norm($mUserId, $mVersion);          
    if ( $mVersion['result']['value'] != 'OK' ) return $mVersion;      
                
    // No goods      
    if ( !isset($mVersion['store']) )
    {                                
        $mVersion['total_sum'] = 0;       

        return $mVersion;
    }       
    
    // Get goods sum
    $goods_sum = MELBIS_INC_LOGIC_order_goods_sum($mVersion);            
    
    // Calculate goods
    foreach ( $mVersion['store'] as &$mStore )
    { 
        if ( $mStore['recalc'] == 1 )
        {
            $mStore['recalc'] = 0;
            $mStore['auto_notice'] = 'Calculated';
            $disc_proc = MELBIS_INC_LOGIC_order_goods_discount('kOrder', $goods_sum, $mStore['store_id']);
            $mStore['out_price'] = ceil($mStore['store_price']*(1 - $disc_proc/100));
        } 
    }
    unset($mStore);        
    
    // Calculate total sum
    $goods_sum = MELBIS_INC_LOGIC_order_goods_sum($mVersion, 'out_price');
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


/** 
 * Function MELBIS_INC_LOGIC_order_goods_add
 * Add goods to version
 **/   
function MELBIS_INC_LOGIC_order_goods_add($mVersion, $mStoreId, $mAmount = 1, $mPriceOut = 0)
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
             
    // Prepare currency
    $curr = array('' => 1);
    $command = "SELECT * 
                  FROM {DBNICK}_currency
                ";  
    $currencies = MELBIS()->SqlSelect(__LINE__, $command);
    foreach ( $currencies as $currency )
    {    
        if ( $currency['division'] > 0 && $currency['multiplex'] != 0 )
        {
            $curr[$currency['id']] = 1/$currency['multiplex'];
        }
        else 
        {
            $curr[$currency['id']] = $currency['multiplex']; 
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
        $store['recalc'] = 1;
        $store['store_pprice'] = round($store['store_pprice'] * $curr[$store['pprice_curr_id']], 2);
        $store['store_rprice'] = round($store['store_rprice'] * $curr[$store['rprice_curr_id']], 2);        
        $store['store_price'] = round($store['store_price'] * $curr[$store['price_curr_id']], 2);
        $store['store_price2'] = round($store['store_price2'] * $curr[$store['price2_curr_id']], 2);
        $store['store_price3'] = round($store['store_price3'] * $curr[$store['price3_curr_id']], 2);
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
        
        // Set Goods options
        $command = "SELECT oso.id AS option_id, 
                           oso.skey AS option_skey, 
                           oso.name AS option_name,
                           oso.kind_key AS option_kind_key, 
                           oso.pos AS option_pos,
                           osov.id AS value_id, 
                           IFNULL(osov.skey, '') AS value_skey, 
                           IFNULL(osov.name, '') AS value_name,                            
                           IF(osov.modify_sum IS NULL, 0,
                            IF(c.id IS NULL, osov.modify_sum, 
                             IF(c.division = 0, osov.modify_sum*c.multiplex,
                              IF(c.multiplex <> 0, osov.modify_sum/c.multiplex, 0)))) AS value_modify_sum                                                                                       
                      FROM {DBNICK}_order_store_option oso
                 LEFT JOIN {DBNICK}_order_store_option_value osov
                        ON oso.id = osov.option_id
                       AND use_default = 1     
                 LEFT JOIN {DBNICK}_currency c
                        ON c.id = osov.sum_curr_id                                               
                  ORDER BY oso.pos
                   ";             
        $store['store_option'] = MELBIS()->SqlSelect(__LINE__, $command);
        
        // Add goods
        $mVersion['store'][] = $store;                               
    }   
    
    return $mVersion;
}   


/** 
 * Function MELBIS_INC_LOGIC_order_goods_remove
 * Remove goods from version
 **/   
function MELBIS_INC_LOGIC_order_goods_remove($mVersion, $mStoreId)
{    
    $key = array_search($mStoreId, array_column($mVersion['store'], 'store_id'));
    if ($key !== false) array_splice($mVersion['store'], $key, 1);      
    
    return $mVersion;
}    


/** 
 * Function MELBIS_INC_LOGIC_order_goods_sum
 * Calculate goods sum in the order
 **/   
function MELBIS_INC_LOGIC_order_goods_sum($mVersion, $mPriceType = 'store_price')
{
    return array_reduce(
        $mVersion['store'] ?? [], 
        fn($sum, $item) => $sum + $item[$mPriceType] * $item['amount'], 
        0);
} 


 

/** 
 * Function MELBIS_INC_LOGIC_order_goods_discount
 * Calculate goods discount info
 **/   
function MELBIS_INC_LOGIC_order_goods_discount($mKindKey, $mSum, $mStoreId)
{
    $now = MELBIS()->DateTime('now');
                                   
    $command = "SELECT MAX(dr.disc_proc) AS value 
                  FROM {DBNICK}_store s 
                  JOIN {DBNICK}_disc_group dg 
                    ON s.disc_group_id = dg.id  
                  JOIN {DBNICK}_disc_rate dr 
                    ON dr.group_id = dg.id
             LEFT JOIN {DBNICK}_currency c 
                    ON dr.sum_curr_id = c.id  
                 WHERE s.id = :STORE_ID 
                   AND dr.kind_key = :KIND_KEY
                   AND IF(c.id IS NOT NULL, 
                        IF(c.division > 0, 
                         IF(c.multiplex <> 0, dr.from_sum/c.multiplex, 0), 
                            dr.from_sum*c.multiplex), dr.from_sum) <= :SUM  
                   AND dr.begin_time < :NOW
                   AND dr.end_time > :NOW
                 ";                  
    $param = [
        'store_id'  => $mStoreId,
        'kind_key'  => $mKindKey,
        'sum'       => $mSum,
        'now'       => $now
        ];                  
    $hash = MELBIS()->SqlSelectFlat(__LINE__, $command, $param); 
    
    return (int) $hash['value'];
}


/** 
 * Function MELBIS_INC_LOGIC_order_option_set   
 * Set order option value by key
 **/
function MELBIS_INC_LOGIC_order_option_set($mVersion, $mOptionKey, $mValueKey, $mNotice = '', $mSaveNotice = false)
{ 
    $command = "SELECT oov.id AS value_id, 
                       oov.skey AS value_skey, 
                       oov.name AS value_name, 
                       oov.kind_key AS value_kind_key,
                       oov.source_num AS value_source_num, 
                       oov.oper_num AS value_oper_num, 
                       IF(c.id IS NULL, oov.modify_sum, 
                        IF(c.division = 0, oov.modify_sum*c.multiplex,
                         IF(c.multiplex <> 0, oov.modify_sum/c.multiplex, 0))) AS value_modify_sum   
                  FROM {DBNICK}_order_option oo
                  JOIN {DBNICK}_order_option_value oov
                    ON oo.id = oov.option_id   
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = oov.sum_curr_id                    
                 WHERE oo.skey = :OPTION_SKEY
                   AND oov.skey = :VALUE_SKEY                   
               ";    
    $param = [
        'option_skey'   => $mOptionKey,
        'value_skey'    => $mValueKey
        ];   
    $hash = MELBIS()->SqlSelectFlat(__LINE__, $command, $param);
    if ( isset($hash['value_id']) )
    {                    
        if ( !$mSaveNotice ) $hash['notice'] = $mNotice;
                
        foreach ( $mVersion['option'] as &$option ) 
        {
            if ( $option['option_skey'] == $mOptionKey ) 
            {
                $option = array_merge($option, $hash);
                break;                
            }
        }
    }
    
    return $mVersion;               
}




/***************************************************************************************************
 * 
 *                                   NOTIFY FUNCTIONS
 * 
 **************************************************************************************************/    

/** 
 * Function MELBIS_INC_LOGIC_notify_events
 * Test for system events and notification
 **/   
function MELBIS_INC_LOGIC_notify_events($mUserId, $mWaitEvents)
{
    return $mWaitEvents;    
}  
