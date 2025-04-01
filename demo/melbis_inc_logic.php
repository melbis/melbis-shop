<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
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
    global $gParser; 
    
    $version =  array('order_id'    => null, 
                      'user_id'     => null, 
                      'client_id'   => null,  
                      'date_time'   => MELBIS_INC_STD_get_now(), 
                      'total_sum'   => 0
                      );

    // Client
    $command = "SELECT id AS field_id, skey AS field_skey, name AS field_name, tindex AS field_tindex,
                       tlevel AS field_tlevel, absindex AS field_absindex, folder AS field_folder,
                       kind_key AS field_kind_key, spec_key AS field_spec_key, NULL AS value_id,
                       '' AS value_skey, '' AS value_code, '' AS value_kind_key, '' AS value_txt
                  FROM {DBNICK}_field
              ORDER BY absindex
               ";                        
    $version['client'] = $gParser->SqlSelect(__LINE__, $command);

    // Goods
    $version['store'] = array();  
    
    // Option
    $command = "SELECT oo.id AS option_id, oo.skey AS option_skey, oo.name AS option_name, 
                       oo.kind_key AS option_kind_key, oo.pos AS option_pos,
                       oov.id AS value_id, IFNULL(oov.skey, '') AS value_skey, IFNULL(oov.name, '') AS value_name, 
                       IFNULL(oov.kind_key, '') AS value_kind_key,                         
                       IF(oov.modify_sum IS NULL, 0,
                        IF(c.id IS NULL, oov.modify_sum, 
                         IF(c.division = 0, oov.modify_sum*c.multiplex,
                          IF(c.multiplex <> 0, oov.modify_sum/c.multiplex, 0)))) AS value_modify_sum,
                       IFNULL(oov.oper_num, 0) AS value_oper_num, IFNULL(oov.source_num, 0) AS value_source_num, '' AS notice                                      
                  FROM {DBNICK}_order_option oo
             LEFT JOIN {DBNICK}_order_option_value oov
                    ON oo.id = oov.option_id 
                   AND oov.use_default = 1         
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = oov.sum_curr_id                   
              ORDER BY oo.pos
               ";  
    $version['option'] = $gParser->SqlSelect(__LINE__, $command);
    
    return $version;   
}


/** 
 * Function MELBIS_INC_LOGIC_order_norm      
 * Normalize order version
 **/
function MELBIS_INC_LOGIC_order_norm($mVersion)
{ 
    global $gParser;  
    
    // Default       
    $mVersion['result']['value'] = 'OK';
    $mVersion['result']['message'] = '';           
    
    // Create base template    
    $templ = MELBIS_INC_LOGIC_order_create();

    // Check base arrays
    if ( !isset($mVersion['client']) ) $mVersion['client'] = [];    
    if ( !isset($mVersion['store']) ) $mVersion['store'] = [];
    if ( !isset($mVersion['option']) ) $mVersion['option'] = [];
                   
    // Normalize client fields  
    foreach ( $mVersion['client'] as $hash )
    {
        $rows = count($templ['client']); 
        for ( $i = 0; $i <= $rows - 1; $i++ )
        {                                                                              
            if ( $hash['field_id'] == $templ['client'][$i]['field_id'] )
            {                      
                $command = "SELECT f.fixed_set, fv.id AS value_id, IFNULL(fv.skey, '') AS value_skey, 
                                    IFNULL(fv.code, '') AS value_code, IFNULL(fv.name, '') AS value_txt, 
                                    IFNULL(fv.kind_key, '') AS value_kind_key 
                                FROM {DBNICK}_field f
                            LEFT JOIN {DBNICK}_field_value fv
                                ON f.id = fv.field_id
                                AND fv.id = '$hash[value_id]'  
                                WHERE f.id = '$hash[field_id]'
                            ";                             
                $value = $gParser->SqlSelectToArray(__LINE__, $command);                
                if ( $value['fixed_set'] == 0 )
                {                                                  
                    if ( $value['value_txt'] != $hash['value_txt'] )
                    {
                        $value['value_id'] = null;
                        $value['value_skey'] = '';
                        $value['value_code'] = '';
                        $value['value_kind_key'] = '';
                        $value['value_txt'] = $hash['value_txt'];                                                
                    }                         
                }
                unset($value['fixed_set']);                
                $templ['client'][$i] = array_merge($templ['client'][$i], $value);                
                break;
            }
        }
    }                     
    $mVersion['client'] = $templ['client'];   
    
    // Normalize option      
    foreach ( $mVersion['option'] as $hash )
    {
        $rows = count($templ['option']); 
        for ( $i = 0; $i <= $rows - 1; $i++ )
        {                                                                              
            if ( $hash['option_id'] == $templ['option'][$i]['option_id'] )
            {
                $command = "SELECT oo.fixed_set, oo.custom_sum, oov.id AS value_id, IFNULL(oov.skey, '') AS value_skey, 
                                    IFNULL(oov.name, '') AS value_name, IFNULL(oov.kind_key, '') AS value_kind_key, 
                                    IFNULL(oov.source_num, 0) AS value_source_num, IFNULL(oov.oper_num, 0) AS value_oper_num,
                                    IF(oov.modify_sum IS NULL, 0,
                                    IF(c.id IS NULL, oov.modify_sum, 
                                        IF(c.division = 0, oov.modify_sum*c.multiplex,
                                        IF(c.multiplex <> 0, oov.modify_sum/c.multiplex, 0)))) AS value_modify_sum                                                                                                                                                                                                           
                                FROM {DBNICK}_order_option oo
                            LEFT JOIN {DBNICK}_order_option_value oov  
                                ON oo.id = oov.option_id
                                AND oov.id = '$hash[value_id]'  
                            LEFT JOIN {DBNICK}_currency c
                                ON c.id = oov.sum_curr_id                        
                                WHERE oo.id = '$hash[option_id]'
                            ";                             
                $value = $gParser->SqlSelectToArray(__LINE__, $command);
                if ( $value['fixed_set'] == 1 && is_null($value['value_id']) && !is_null($templ['option'][$i]['value_id']) )
                {
                    $value = array_merge($value, $templ['option'][$i]);
                }
                if ( $value['custom_sum'] != 0 )
                {
                    $value['value_modify_sum'] = $hash['value_modify_sum'];
                }                                                                  
                if ( $value['fixed_set'] == 0 )
                {                                                  
                    if ( $value['value_name'] != $hash['value_name'] )
                    {
                        $value['value_id'] = null;
                        $value['value_skey'] = '';                        
                        $value['value_kind_key'] = '';
                        $value['value_source_num'] = 0;
                        $value['value_oper_num'] = 0;                                                
                        $value['value_name'] = $hash['value_name'];
                        if ( $value['custom_sum'] == 0 ) $value['value_modify_sum'] = 0;                                                
                    }                         
                }
                $templ['option'][$i]['notice'] = $hash['notice'];
                unset($value['fixed_set']);                
                unset($value['custom_sum']);              
                $templ['option'][$i] = array_merge($templ['option'][$i], $value);                
                break;
            }                                                           
        }                 
    }                       
    $mVersion['option'] = $templ['option'];   
      
    // Normalize store_option          
    $rows = count($mVersion['store']); 
    for ( $i = 0; $i <= $rows - 1; $i++ )
    {
        $command = "SELECT oso.id AS option_id, oso.skey AS option_skey, oso.kind_key AS option_kind_key, 
                            oso.name AS option_name, oso.pos AS option_pos,
                            osov.id AS value_id, IFNULL(osov.skey, '') AS value_skey, IFNULL(osov.name, '') AS value_name, 
                            IFNULL(osov.modify_sum, 0) AS value_modify_sum                                 
                        FROM {DBNICK}_order_store_option oso
                    LEFT JOIN {DBNICK}_order_store_option_value osov
                        ON oso.id = osov.option_id   
                        AND use_default = 1
                    ORDER BY oso.pos        
                    ";  
        $query_opt = $gParser->SqlQuery(__LINE__, $command);
        $rows_opt = $gParser->SqlNumRows($query_opt);
        $store_option = $mVersion['store'][$i]['store_option'] ?? [];
        unset($mVersion['store'][$i]['store_option']);                 
        for ( $n = 1; $n <= $rows_opt; $n++ )
        {    
            $templ = $gParser->SqlFetchHash($query_opt);
            if ( is_array($store_option) )
            {                            
                foreach( $store_option as $hash )
                {                                                            
                    if ( $hash['option_id'] == $templ['option_id'] )
                    {                                           
                        $command = "SELECT oso.fixed_set, oso.custom_sum, osov.id AS value_id, 
                                            IFNULL(osov.skey, '') AS value_skey, IFNULL(osov.name, '') AS value_name, 
                                            IF(osov.modify_sum IS NULL, 0,
                                            IF(c.id IS NULL, osov.modify_sum, 
                                                IF(c.division = 0, osov.modify_sum*c.multiplex,
                                                IF(c.multiplex <> 0, osov.modify_sum/c.multiplex, 0)))) AS value_modify_sum                                                                                                                                                                                                           
                                        FROM {DBNICK}_order_store_option oso
                                    LEFT JOIN {DBNICK}_order_store_option_value osov  
                                        ON oso.id = osov.option_id
                                        AND osov.id = '$hash[value_id]'  
                                    LEFT JOIN {DBNICK}_currency c
                                        ON c.id = osov.sum_curr_id                        
                                        WHERE oso.id = '$hash[option_id]'
                                    ";                             
                        $value = $gParser->SqlSelectToArray(__LINE__, $command);
                        if ( $value['fixed_set'] == 1 && is_null($value['value_id']) && !is_null($templ['value_id']) )
                        {
                            $value = array_merge($value, $templ);
                        }                                                
                        if ( $value['custom_sum'] != 0 )
                        {
                            $value['value_modify_sum'] = $hash['value_modify_sum'];
                        }                                                                  
                        if ( $value['fixed_set'] == 0 )
                        {                                                  
                            if ( $value['value_name'] != $hash['value_name'] )
                            {
                                $value['value_id'] = null;
                                $value['value_skey'] = '';                        
                                $value['value_name'] = $hash['value_name'];
                                if ( $value['custom_sum'] == 0 ) $value['value_modify_sum'] = 0;                                                
                            }                         
                        }
                        unset($value['fixed_set']);                
                        unset($value['custom_sum']);
                        $templ = array_merge($templ, $value);                        
                        break;                      
                    }
                }
            }                                                
            $mVersion['store'][$i]['store_option'][] = $templ;            
        }   
    }
           
    
    // Store option block verify      
    foreach ( $mVersion['store'] as $item )
    {                                                
        if ( isset($item['store_option']) )
        {                                        
            $option_value = array();
            foreach ( $item['store_option'] as $hash )
            {                                    
                if ( !is_null($hash['value_id']) )
                {
                    $option_value[] = $hash['value_id'];
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
                $hash = $gParser->SqlSelectToArray(__LINE__, $command);
                if ( !is_null($hash['id'] ?? null) )
                {
                    $mVersion['result']['value'] = 'STORE_OPTION_BLOCK';
                    $mVersion['result']['message'] = $item['store_name'].":\r\n".$hash['message'];
            
                    return $mVersion;
                }
            }                 
        }            
    }                        
         
    
    // Option block verify           
    $option_value = array();
    foreach ( $mVersion['option'] as $hash )
    {    
        if ( !is_null($hash['value_id']) )
        {
            $option_value[] = $hash['value_id'];
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
        $hash = $gParser->SqlSelectToArray(__LINE__, $command);
        if ( !is_null($hash['id'] ?? null) )
        {
            $mVersion['result']['value'] = 'OPTION_BLOCK';
            $mVersion['result']['message'] = $hash['message'];
            
            return $mVersion;                 
        }            
    }                        
       
         
    // Option limit verify
    if ( !is_null($mVersion['user_id']) )
    {                   
        $user_admin = MELBIS_INC_AUTH_user_command($mVersion['user_id'], 'PUT_ORDER_OPTION');
        $user_admin = false;
        if ( is_array($mVersion['option']) && !is_null($mVersion['order_id']) && !$user_admin )
        {                    
            // Get previous option value
            $option_was = array(); 
            $command = "SELECT oo.value_id 
                          FROM {DBNICK}_orders o
                          JOIN {DBNICK}_orders_version ov
                            ON o.version_id = ov.id  
                          JOIN {DBNICK}_orders_option oo
                            ON oo.version_id = ov.id 
                         WHERE o.id = '$mVersion[order_id]'
                           AND oo.value_id IS NOT NULL 
                       ";     
            $query = $gParser->SqlQuery(__LINE__, $command);
            $rows = $gParser->SqlNumRows($query);
            for ( $i = 1; $i <= $rows; $i++ ) 
            {
                $hash = $gParser->SqlFetchHash($query);            
                $option_was[] = $hash['value_id'];                
            }         
            // Get now option value                                          
            $option_set = array();
            foreach ( $mVersion['option'] as $hash )
            {    
                if ( !is_null($hash['value_id']) )
                {
                    $option_set[] = $hash['value_id'];
                }
            }  
            // Verify
            if ( count($option_was) > 0 && count($option_set) > 0)
            {
                $was = implode(',', $option_was);
                $set = implode(',', $option_set);              
                $command = "SELECT ool.id, ool.message
                              FROM {DBNICK}_order_option_limit ool
                              JOIN {DBNICK}_order_option_limit_right oolr
                                ON ool.id = oolr.limit_id
                         LEFT JOIN {DBNICK}_user u
                                ON ( u.group_id = oolr.group_id OR u.add_group_id = oolr.group_id )
                             WHERE ool.was_value_id IN ( $was )
                               AND ool.set_value_id IN ( $set ) 
                               AND ( oolr.user_id = '$mVersion[user_id]' OR u.id = '$mVersion[user_id]' )
                           ";
                $hash = $gParser->SqlSelectToArray(__LINE__, $command);
                if ( !is_null($hash['id'] ?? null) )
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
    global $gParser;       
    
    // Get version            
    $command = "SELECT ov.*
                  FROM {DBNICK}_orders o
                  JOIN {DBNICK}_orders_version ov 
                    ON o.version_id = ov.id
                 WHERE o.id = '$mOrderId'
               ";          
    $version = $gParser->SqlSelectToArray(__LINE__, $command);
    $ver_id = $version['id'];    
    $version['user_id'] = null;
    $version['date_time'] = MELBIS_INC_STD_get_now();
    $version['_']['parent_version_id'] = $version['id'];
    $version['_']['parameters'] = 'kDefault';
    unset($version['id']);    
                  
    // Get Client
    $command = "SELECT *
                  FROM {DBNICK}_orders_client_field 
                 WHERE version_id = '$ver_id'
               ";              
    $query = $gParser->SqlQuery(__LINE__, $command);
    $rows = $gParser->SqlNumRows($query);
    for ( $i = 1; $i <= $rows; $i++ ) 
    {
        $hash = $gParser->SqlFetchHash($query);
        
        unset($hash['id']);
        unset($hash['version_id']);
        $version['client'][] = $hash;        
    }      
    
    // Get Goods
    $command = "SELECT *
                  FROM {DBNICK}_orders_store 
                 WHERE version_id = '$ver_id'
               ";              
    $query = $gParser->SqlQuery(__LINE__, $command);
    $rows = $gParser->SqlNumRows($query);
    for ( $i = 1; $i <= $rows; $i++ ) 
    {
        $hash = $gParser->SqlFetchHash($query);
        
        $store_id = $hash['id'];
        unset($hash['id']);
        unset($hash['version_id']);                    
        
        // Get goods options
        $command = "SELECT * 
                      FROM {DBNICK}_orders_store_option 
                     WHERE version_id = '$ver_id'
                       AND order_store_id = '$store_id'
                   ";
        $s_query = $gParser->SqlQuery(__LINE__, $command);
        $s_rows = $gParser->SqlNumRows($s_query);
        for ( $n = 1; $n <= $s_rows; $n++ ) 
        {
            $sub = $gParser->SqlFetchHash($s_query);
            
            unset($sub['id']);   
            unset($sub['version_id']);
            unset($sub['order_store_id']);
            $hash['store_option'][] = $sub;
            
        }               
        $version['store'][] = $hash;                
    }   
    
    // Get options
    $command = "SELECT *
                  FROM {DBNICK}_orders_option 
                 WHERE version_id = '$ver_id'
               ";              
    $query = $gParser->SqlQuery(__LINE__, $command);
    $rows = $gParser->SqlNumRows($query);
    for ( $i = 1; $i <= $rows; $i++ ) 
    {
        $hash = $gParser->SqlFetchHash($query);
        
        unset($hash['id']);
        unset($hash['version_id']);
        $version['option'][] = $hash;        
    }                     
    
    return $version;                            
}    


/** 
 * Function MELBIS_INC_LOGIC_order_edit
 * Create new version for order
 **/   
function MELBIS_INC_LOGIC_order_edit($mVersion)
{
    global $gParser;      

    // Vars
    $now = MELBIS_INC_STD_get_now();
    $result['value'] = 'OK';
    $result['message'] = ''; 
    $result['orders'] = array(); 
    
    // Normalize                                           
    $mVersion = MELBIS_INC_LOGIC_order_norm($mVersion);                  
    if ( $mVersion['result']['value'] != 'OK' ) return $mVersion['result'];
    
    // Test for intermediate version
    if ( !is_null($mVersion['order_id']) )
    {            
        $command = "SELECT * FROM {DBNICK}_orders WHERE id = '$mVersion[order_id]'";
        $hash = $gParser->SqlSelectToArray(__LINE__, $command);                
        if ( $mVersion['_']['parent_version_id'] != $hash['version_id'] )
        {
            $result['orders'][] = $mVersion['order_id'];
            $result['value'] = 'VERSION_ERROR';
            $result['message'] = 'Error, order has intermediate version!';
            
            return $result;
        }                
    }             

    // Version 
    $version = array();
    $version['id'] = $gParser->SqlGenId('orders_version');
    $version['user_id'] = $mVersion['user_id'];
    $version['order_id'] = $mVersion['order_id'];
    $version['client_id'] = $mVersion['client_id'];
    $version['date_time'] = $now;
    $version['total_sum'] = $mVersion['total_sum'];      
    $gParser->SqlInsert(__LINE__, '{DBNICK}_orders_version', $version);                     
    
    // Client
    $command = "SELECT * FROM {DBNICK}_client WHERE id = '$mVersion[client_id]'";  
    $client = $gParser->SqlSelectToArray(__LINE__, $command);
    if ( !isset($client['id']) )
    {
        // New client
        $client['id'] = $gParser->SqlGenId('client');
        srand((double)microtime()*1000000);
        $time = explode( " ", microtime());
        $client['login'] = 'auto'.$client['id'].substr((double)$time[0], 2, 3);
        $client['pass'] = substr(md5(rand(0,9999999)),1 ,8);
        $client['reg_date'] = $now;
        
        // Add new client        
        $gParser->SqlInsert(__LINE__, '{DBNICK}_client', $client);        
        
        // Save client field
        $client_field['client_id'] = $client['id'];
        if ( is_array($mVersion['client']) )
        {
            foreach ( $mVersion['client'] as $item ) 
            {
                $client_field['id'] = $gParser->SqlGenId('client_field');
                $client_field['field_id'] = $item['field_id'];
                $client_field['value_id'] = $item['value_id'];
                $client_field['value_txt'] = $item['value_txt'];
                $gParser->SqlInsert(__LINE__, '{DBNICK}_client_field', $client_field);
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
                $value_txt = addslashes($item['value_txt']);
                $value_id = ( is_null($item['value_id']) ) ? 'NULL' : $item['value_id'];
                $command = "UPDATE {DBNICK}_client_field cfv,
                                   {DBNICK}_field cf
                               SET cfv.value_txt = '$value_txt',
                                   cfv.value_id = $value_id 
                             WHERE cfv.client_id = '$client[id]'
                               AND cfv.field_id = '$item[field_id]'
                               AND cfv.field_id = cf.id 
                               AND cf.read_only = 0
                            ";
                $gParser->SqlQuery(__LINE__, $command);
            }
        }             
    } 
    
    // Update client last date                                           
    $hash = array();
    $hash['id'] = $client['id'];
    $hash['last_date'] = $now;            
    $gParser->SqlUpdate(__LINE__, '{DBNICK}_client', $hash, 'id');      
    
    // Order client field      
    if ( is_array($mVersion['client']) )
    {
        foreach ( $mVersion['client'] as $item ) 
        {    
            $item['version_id'] = $version['id'];
            $gParser->SqlInsert(__LINE__, '{DBNICK}_orders_client_field', $item);            
        }
    }   

    // Order Store   
    if ( is_array($mVersion['store']) )
    {
        foreach ( $mVersion['store'] as $item ) 
        {                                
            $item['version_id'] = $version['id'];            
            $store = $item;
            unset($store['store_option']);            
            $gParser->SqlInsert(__LINE__, '{DBNICK}_orders_store', $store);
            $store_id = $gParser->SqlLastInsertId();
    
            // Order Store Option                     
            if ( isset($item['store_option']) )
            {
                foreach ( $item['store_option'] as $option ) 
                {                                            
                    $option['version_id'] = $version['id'];
                    $option['order_store_id'] = $store_id;                                
                    $gParser->SqlInsert(__LINE__, '{DBNICK}_orders_store_option', $option);                
                }      
            }
        }
    }         
    
    // Order Option  
    if ( is_array($mVersion['option']) )
    {
        foreach ( $mVersion['option'] as $item ) 
        {  
            $item['version_id'] = $version['id'];
            $gParser->SqlInsert(__LINE__, '{DBNICK}_orders_option', $item);
        } 
    }
              
    
    // Order 
    $command = "SELECT * FROM {DBNICK}_orders WHERE id = '$mVersion[order_id]'";  
    $order = $gParser->SqlSelectToArray(__LINE__, $command);
    if ( !isset($order['id']) )
    {
        $order['id'] = $gParser->SqlGenId('orders');                                                      
        $code = substr(date("U"), -6);
        $def = ceil(strlen($code)/3);
        $line = '';
        for ( $i = 1; $i <= $def; $i++ )
        {
            $line .= substr($code, ($i - 1)*3, 3).'-';
        }
        $order['code'] = '#'.date("d").'-'.substr($line, 0, -1);  
        $order['date_time'] = $now;         
        $gParser->SqlInsert(__LINE__, '{DBNICK}_orders', $order);
    }    
        
    // Update version
    $version['client_id'] = $client['id'];
    $version['order_id'] = $order['id'];
    $gParser->SqlUpdate(__LINE__, '{DBNICK}_orders_version', $version, 'id');    
    
    // Update order
    $order['version_id'] = $version['id'];
    $gParser->SqlUpdate(__LINE__, '{DBNICK}_orders', $order, 'id');
    
    // Result orders
    $result['orders'][] = $order['id'];    
        
    return $result;             
}
 
/** 
 * Function MELBIS_INC_LOGIC_order_calc
 * Calculate order information
 **/   
function MELBIS_INC_LOGIC_order_calc($mVersion)
{
    global $gParser;           
    
    // Normalize     
    $mVersion = MELBIS_INC_LOGIC_order_norm($mVersion);          
    if ( $mVersion['result']['value'] != 'OK' ) return $mVersion;      
            
    // Get goods sum
    $goods_sum = MELBIS_INC_LOGIC_order_goods_sum($mVersion);        
    
    // No goods      
    if ( !isset($mVersion['store']) )
    {                                
        $mVersion['total_sum'] = 0;       

        return $mVersion;
    }    
    
    // Calculate goods
    $rows = count($mVersion['store']); 
    for ( $i = 0; $i <= $rows - 1; $i++ )
    { 
        $mStore = &$mVersion['store'][$i];
        if ( $mVersion['store'][$i]['recalc'] == 1 )
        {
            $mStore['recalc'] = 0;
            $mStore['auto_notice'] = 'Calculated';
            $disc_proc = MELBIS_INC_LOGIC_order_goods_discount('kOrder', $goods_sum, $mStore['store_id']*1);
            $mStore['out_price'] = ceil($mStore['store_price']*(1 - $disc_proc/100));
        } 
    }
    
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
                    if ( $sum != 0 ) $goods_sum = $goods_sum / $sum;
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
                    if ( $sum != 0 ) $total_sum = $total_sum / $sum;
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
function MELBIS_INC_LOGIC_order_goods_add($mVersion, $mId, $mAmount = 1, $mPriceOut = 0)
{
    global $gParser;   
    
    // Store exists?                      
    $rows = count($mVersion['store']); 
    for ( $i = 0; $i <= $rows - 1; $i++ )
    {                        
        $item = &$mVersion['store'][$i];
        if ( $item['store_id'] == $mId ) 
        {
            $mId = 0;
            $item['amount'] += $mAmount;
        }            
    }
    if ( $mId == 0 ) return $mVersion;    
             
    // Prepare currency
    $curr = array('' => 1);
    $command = "SELECT * FROM {DBNICK}_currency";  
    $query = $gParser->SqlQuery(__LINE__, $command);
    $rows = $gParser->SqlNumRows($query); 
    for ( $i = 1; $i <= $rows; $i++ )
    {    
        $hash = $gParser->SqlFetchHash($query);    
        if ( $hash['division'] > 0 && $hash['multiplex'] != 0 )
        {
            $curr[$hash['id']] = 1/$hash['multiplex'];
        }
        else 
        {
            $curr[$hash['id']] = $hash['multiplex']; 
        }
    }                     
    
    // Get Goods
    $command = "SELECT id AS store_id, provider_id AS store_provider_id, brand_id AS store_brand_id, 
                       pprice AS store_pprice, rprice AS store_rprice, price AS store_price, 
                       price2 AS store_price2, price3 AS store_price3, how AS store_how, 
                       code_shop AS store_code_shop, code_prov AS store_code_prov, code_made AS store_code_made, 
                       meas AS store_meas, name AS store_name, kind_key AS store_kind_key, status_key AS store_status_key, 
                       state_key AS store_state_key, min_order AS store_min_order, step_order AS store_step_order, 
                       pprice_curr_id, rprice_curr_id, price_curr_id, price2_curr_id, price3_curr_id
                  FROM {DBNICK}_store
                 WHERE id = '$mId'
               ";  
    $query = $gParser->SqlQuery(__LINE__, $command);
    if ( $gParser->SqlNumRows($query) > 0 )
    {
        $store = $gParser->SqlFetchHash($query);
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
        $command = "SELECT oso.id AS option_id, oso.skey AS option_skey, oso.name AS option_name,
                           oso.kind_key AS option_kind_key, oso.pos AS option_pos,
                           osov.id AS value_id, IFNULL(osov.skey, '') AS value_skey, IFNULL(osov.name, '') AS value_name,                            
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
        $store['store_option'] = $gParser->SqlSelect(__LINE__, $command);
        
        // Add goods
        $mVersion['store'][] = $store;                               
    }   
    
    return $mVersion;
}   


/** 
 * Function MELBIS_INC_LOGIC_order_goods_remove
 * Remove goods from version
 **/   
function MELBIS_INC_LOGIC_order_goods_remove($mVersion, $mId)
{
    global $gParser; 

    $rows = count($mVersion['store'])-1;
    for( $i = 0; $i <= $rows; $i++ )
    {
        if ( $mVersion['store'][$i]['store_id'] == $mId ) 
        {
            array_splice($mVersion['store'], $i, 1);
            break;
        }
    }     
    
    return $mVersion;
}    


/** 
 * Function MELBIS_INC_LOGIC_order_goods_sum
 * Calculate goods sum in the order
 **/   
function MELBIS_INC_LOGIC_order_goods_sum($mVersion, $mPriceType = 'store_price')
{
    $goods_sum = 0;             
    
    if ( !isset($mVersion['store']) ) return $goods_sum;
    
    foreach ( $mVersion['store'] as $item )
    {        
        $goods_sum += $item[$mPriceType]*$item['amount']*1;
    } 
 
    return $goods_sum;   
} 


 

/** 
 * Function MELBIS_INC_LOGIC_order_goods_discount
 * Calculate goods discount info
 **/   
function MELBIS_INC_LOGIC_order_goods_discount($mKindKey, $mSuma, $mStoreId)
{
    global $gParser;   
    
    $now = MELBIS_INC_STD_get_now();
                                   
    $command = "SELECT MAX(disc_rate.disc_proc) AS proc 
                  FROM {DBNICK}_store store 
                  JOIN {DBNICK}_disc_group disc_group ON store.disc_group_id = disc_group.id  
                  JOIN {DBNICK}_disc_rate disc_rate ON disc_rate.group_id = disc_group.id
             LEFT JOIN {DBNICK}_currency curr ON disc_rate.sum_curr_id = curr.id  
                 WHERE store.id = $mStoreId 
                   AND disc_rate.kind_key = '$mKindKey'
                   AND $mSuma >= IF(curr.id IS NOT NULL, 
                                 IF(curr.division > 0, 
                                  IF(curr.division <> 0, disc_rate.from_sum/curr.multiplex, 0), 
                                 disc_rate.from_sum*curr.multiplex),
                                disc_rate.from_sum) 
                   AND disc_rate.begin_time < '$now'
                   AND disc_rate.end_time > '$now'
                 ";                   
    $hash = $gParser->SqlSelectToArray(__LINE__, $command); 
    
    return $hash['proc']*1;
}


/** 
 * Function MELBIS_INC_LOGIC_order_option_set   
 * Set order option value by key
 **/
function MELBIS_INC_LOGIC_order_option_set($mVersion, $mOptionKey, $mValueKey, $mNotice = '', $mSaveNotice = false)
{ 
    global $gParser;       
                        
    $command = "SELECT oov.id AS value_id, oov.skey AS value_skey, oov.name AS value_name, oov.kind_key AS value_kind_key,
                       oov.source_num AS value_source_num, oov.oper_num AS value_oper_num, 
                       IF(c.id IS NULL, oov.modify_sum, 
                        IF(c.division = 0, oov.modify_sum*c.multiplex,
                         IF(c.multiplex <> 0, oov.modify_sum/c.multiplex, 0))) AS value_modify_sum   
                  FROM {DBNICK}_order_option oo
                  JOIN {DBNICK}_order_option_value oov
                    ON oo.id = oov.option_id   
             LEFT JOIN {DBNICK}_currency c
                    ON c.id = oov.sum_curr_id                    
                 WHERE oo.skey = '$mOptionKey'
                   AND oov.skey = '$mValueKey'                   
               ";       
    $hash = $gParser->SqlSelectToArray(__LINE__, $command);
    if ( $hash['value_id'] > 0 )
    {                    
        $rows = count($mVersion['option']);  
        for ( $i = 0; $i <= $rows - 1; $i++ )
        {             
            if ( $mVersion['option'][$i]['option_skey'] == $mOptionKey )
            {               
                if ( !$mSaveNotice ) 
                {
                    $hash['notice'] = $mNotice;
                }
                $mVersion['option'][$i] = array_merge($mVersion['option'][$i], $hash);                             
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
    global $gParser;
    
    return $mWaitEvents;    
}  
