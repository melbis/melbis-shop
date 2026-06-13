<?php
/***************************************************************************************************
 * @version 6.5.0.288 @ 2026-06-13
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/


/** 
 * Function MELBIS_BASKET
 **/
function MELBIS_BASKET($mVars)
{ 
    return MELBIS()->UnitFunc($mVars['post']['func'], $mVars);  
} 


/** 
 * Function MELBIS_BASKET_plus
 **/
function MELBIS_BASKET_plus($mVars)
{ 
    // Vars
    $store_id = (int) $mVars['post']['id'];
    
    // Get order    
    $version = MELBIS()->SessionGetValue('order') ?? MELBIS_INC_LOGIC_order_create();

    // Add goods
    $version = MELBIS_INC_LOGIC_order_goods_add($version, $store_id);
     
    // Calculate
    $version = MELBIS_INC_LOGIC_order_calc(null, $version);
             
    // Save version
    MELBIS()->SessionSetValue('order', $version);    
                        
    return '';                         
}    


/** 
 * Function MELBIS_BASKET_minus
 **/
function MELBIS_BASKET_minus($mVars)
{ 
    // Vars
    $store_id = (int) $mVars['post']['id'];
    
    // Get version    
    $version = MELBIS()->SessionGetValue('order') ?? [];

    // Remove goods
    $version = MELBIS_INC_LOGIC_order_goods_remove($version, $store_id);    
    
    // Calculate
    $version = MELBIS_INC_LOGIC_order_calc(null, $version);
             
    // Save version
    MELBIS()->SessionSetValue('order', $version);        
                               
    // Return goods list
    return MELBIS_BASKET_goods($mVars);     
} 


/** 
 * Function MELBIS_BASKET_goods
 **/
function MELBIS_BASKET_goods($mVars)
{ 
    // Vars    
    $version = MELBIS()->SessionGetValue('order') ?? [];                                 
    
    // Create 
    $tpl = MELBIS()->TplCreate();  
    
    MELBIS()->TplAssign($tpl, 'GOODS', $version['store'] ?? []);    
                                                              
    // Final
    return MELBIS()->TplFinal($tpl, 'goods');        
}      


/** 
 * Function MELBIS_BASKET_fields
 **/
function MELBIS_BASKET_fields($mVars)
{     
    // Vars    
    $version = MELBIS()->SessionGetValue('order') ?? []; 
             
    // Empty fields    
    if ( empty($version['client']) ) return '';
            
    // Create 
    $tpl = MELBIS()->TplCreate();  
    
    // Fields
    $client_fields = $version['client'];                                    
                  
    $command = "SELECT f.id AS field_id, 
                       f.fixed_set, 
                       COUNT(fv.id) AS has_value 
                  FROM {DBNICK}_field f
             LEFT JOIN {DBNICK}_field_value fv
                    ON fv.field_id = f.id
              GROUP BY f.id      
                ";              
    $fields = MELBIS()->SqlSelect(__LINE__, $command);     
    $fields = array_column($fields, null, 'field_id');
    foreach ( $client_fields as &$row ) 
    {              
        $row = array_merge($row, $fields[$row['field_id']] ?? []);                             
        if ( $row['has_value'] )
        {        
            $command = "SELECT *
                          FROM {DBNICK}_field_value
                         WHERE field_id = :FIELD_ID 
                      ORDER BY pos    
                    ";                      
            $param = [
                'field_id' => $row['field_id']
                ];        
            $row['value_list'] = MELBIS()->SqlSelect(__LINE__, $command, $param);        
        }
    }    
    
    MELBIS()->TplAssign($tpl, 'FIELDS', $client_fields);    
                                                              
    // Final
    return MELBIS()->TplFinal($tpl, 'fields');                
}      


/** 
 * Function MELBIS_BASKET_options
 **/
function MELBIS_BASKET_options($mVars)
{ 
    // Vars    
    $version = MELBIS()->SessionGetValue('order') ?? [];  
    
    // Empty fields    
    if ( empty($version['option']) ) return '';    
    
    // Create 
    $tpl = MELBIS()->TplCreate();       
        
    // Options
    $order_options = $version['option'];                                    
                  
    $command = "SELECT oo.id AS option_id, 
                       oo.fixed_set, 
                       COUNT(oov.id) AS has_value 
                  FROM {DBNICK}_order_option oo
             LEFT JOIN {DBNICK}_order_option_value oov
                    ON oov.option_id = oo.id
              GROUP BY oo.id      
                ";              
    $options = MELBIS()->SqlSelect(__LINE__, $command);     
    $options = array_column($options, null, 'option_id');
    foreach ( $order_options as &$row ) 
    {              
        $row = array_merge($row, $options[$row['option_id']] ?? []);                             
        if ( $row['has_value'] )
        {        
            $command = "SELECT *
                          FROM {DBNICK}_order_option_value
                         WHERE option_id = :OPTION_ID 
                      ORDER BY pos    
                    ";                      
            $param = [
                'option_id' => $row['option_id']
                ];        
            $row['value_list'] = MELBIS()->SqlSelect(__LINE__, $command, $param);        
        }
    }    
    
    MELBIS()->TplAssign($tpl, 'OPTIONS', $order_options);    
                                                              
    // Final
    return MELBIS()->TplFinal($tpl, 'options');  
} 


/** 
 * Function MELBIS_BASKET_save
 **/
function MELBIS_BASKET_save($mVars)
{ 
    // Vars
    $data['result'] = 'OK';
    $data['message'] = '';    
    
    // Vars    
    $version = MELBIS()->SessionGetValue('order') ?? [];  
    
    // Update fields     
    foreach ( $version['client'] as &$row )
    { 
        $id = $row['field_id'];
        $value_id = $mVars['post']['field'.$id.'_id'] ?? null;        
        $row['value_id'] = ( $value_id ) ? (int) $value_id : null; 
        $row['value_txt'] = $mVars['post']['field'.$id.'_text'] ?? '';
    }               
    unset($row);
        
    // Update options                      
    foreach ( $version['option'] as &$row )
    {           
        $id = $row['option_id'];        
        $value_id = $mVars['post']['option'.$id.'_id'] ?? null;      
        $row['value_id'] = ( $value_id ) ? (int) $value_id : null;
        $row['value_name'] = $mVars['post']['option'.$id.'_text'] ?? '';
    }      
    unset($row);
    
    // Calculate
    $version = MELBIS_INC_LOGIC_order_calc(null, $version);
    
    // Save version
    MELBIS()->SessionSetValue('order', $version);                                          

    // Verify cart    
    if ( empty($version['store']) )
    {  
        $data['result'] = 'ERROR_EMPTY';
        $data['message'] = 'No goods found!';    
        
        return json_encode($data);    
    }   

    // Verify calculation
    if ( $version['result']['value'] != 'OK' )
    {
        $data['result'] = $version['result']['value'];
        $data['message'] = $version['result']['message'];
        
        return json_encode($data);  
    }     
    
    // Create order    
    $result = MELBIS_INC_LOGIC_order_edit(null, $version);
    
    // Error exists?
    if ( $result['value'] != 'OK' )
    {  
        $data['result'] = $result['value'];
        $data['message'] = $result['message'];
        
        return json_encode($data);
    }
    else
    {
        // Close order session
        MELBIS()->SessionRemoveValue('order');
    }       
                        
    return json_encode($data);   
}       

?>