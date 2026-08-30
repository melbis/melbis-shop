<?php
/***************************************************************************************************
 * @version 6.5.1.417 @ 2026-08-30
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main    - Runs what the form names
 * Plus    - Puts a goods in
 * Minus   - Takes a goods out
 * Goods   - Prints the basket goods
 * Fields  - Prints the buyer fields
 * Options - Prints the order options
 * Save    - Counts and saves the order
 *
 **************************************************************************************************/
                    
namespace MELBIS_BASKET;  

use MELBIS_INC_LOGIC_ORDER as LOGIC_ORDER;
use MELBIS_INC_LOGIC_ORDER_CALC as LOGIC_ORDER_CALC;
use MELBIS_INC_LOGIC_ORDER_EDIT as LOGIC_ORDER_EDIT;


/** 
 * Function Main
 **/
function Main($mVars)
{ 
    return MELBIS()->UnitFunc($mVars['post']['func'] ?? '', $mVars);  
} 


/** 
 * Function Plus
 **/
function Plus($mVars)
{ 
    // Vars
    $store_id = (int) ( $mVars['post']['id'] ?? 0 );
    if ( $store_id < 1 ) return '';
    
    // Get order    
    $version = MELBIS()->SessionGetValue('order') ?? LOGIC_ORDER\Create();

    // Add goods
    $version = LOGIC_ORDER\GoodsAdd($version, $store_id);
     
    // Calculate
    $version = LOGIC_ORDER_CALC\Run(null, $version);
             
    // Save version
    MELBIS()->SessionSetValue('order', $version);    
                        
    return '';                         
}    


/** 
 * Function Minus
 **/
function Minus($mVars)
{ 
    // Vars
    $store_id = (int) ( $mVars['post']['id'] ?? 0 );
    if ( $store_id < 1 ) return '';
    
    // Get version    
    $version = MELBIS()->SessionGetValue('order') ?? [];

    // Remove goods
    $version = LOGIC_ORDER\GoodsRemove($version, $store_id);    
    
    // Calculate
    $version = LOGIC_ORDER_CALC\Run(null, $version);
             
    // Save version
    MELBIS()->SessionSetValue('order', $version);            
                               
    // Return goods list
    return Goods($mVars);     
} 


/** 
 * Function Goods
 **/
function Goods($mVars)
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
 * Function Fields
 **/
function Fields($mVars)
{     
    // Vars    
    $version = MELBIS()->SessionGetValue('order') ?? []; 
             
    // Empty fields    
    if ( empty($version['client']) ) return '';
            
    // Create 
    $tpl = MELBIS()->TplCreate();  
    
    // Fields
    $client_fields = $version['client'];                                    
                  
    // The registry answers every field
    $field_set = array_column(MELBIS()->SysFieldValues(), null, 'id');
    
    foreach ( $client_fields as &$row ) 
    {              
        $field = $field_set[$row['field_id']] ?? [];
        $row['fixed_set'] = $field['fixed_set'] ?? 0;
        $row['value_list'] = $field['value'] ?? [];
        
        // The value the order holds
        foreach ( $row['value_list'] as &$value ) 
        {
            $value['is_selected'] = ( $value['id'] == $row['value_id'] );
        }
        unset($value);
    }    
    unset($row);
    
    MELBIS()->TplAssign($tpl, 'FIELDS', $client_fields);    
                                                              
    // Final
    return MELBIS()->TplFinal($tpl, 'fields');                
}      


/** 
 * Function Options
 **/
function Options($mVars)
{ 
    // Vars    
    $version = MELBIS()->SessionGetValue('order') ?? [];  
    
    // Empty fields    
    if ( empty($version['option']) ) return '';    
    
    // Create 
    $tpl = MELBIS()->TplCreate();       
        
    // Options
    $order_options = $version['option'];                                    
                  
    // The registry answers every option
    $option_set = array_column(MELBIS()->SysOrderOptionValues(), null, 'id');
    
    foreach ( $order_options as &$row ) 
    {              
        $option = $option_set[$row['option_id']] ?? [];
        $row['fixed_set'] = $option['fixed_set'] ?? 0;
        $row['value_list'] = $option['value'] ?? [];
        
        // The value the order holds
        foreach ( $row['value_list'] as &$value ) 
        {
            $value['is_selected'] = ( $value['id'] == $row['value_id'] );
        }
        unset($value);
    }    
    unset($row);
    
    MELBIS()->TplAssign($tpl, 'OPTIONS', $order_options);    
                                                              
    // Final
    return MELBIS()->TplFinal($tpl, 'options');  
} 


/** 
 * Function Save
 **/
function Save($mVars)
{ 
    // Vars
    $data['result'] = 'OK';
    $data['message'] = '';    
    
    // An order unstarted holds nothing
    $version = MELBIS()->SessionGetValue('order') ?? [];  
    $version['client'] = $version['client'] ?? [];
    $version['option'] = $version['option'] ?? [];
    
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
    $version = LOGIC_ORDER_CALC\Run(null, $version);
    
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
    $result = LOGIC_ORDER_EDIT\Run(null, $version);
    
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