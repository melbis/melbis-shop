<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_BASKET
 **/
function MELBIS_BASKET($mVars)
{ 
    global $gParser;  
    
    // Vars
    $func = __FUNCTION__.'_'.$mVars['post']['func'];
    
    // Call function    
    if ( function_exists($func) )
    {
        return call_user_func($func, $mVars);    
    }  
    else
    {
        return 'Function '.$func.' is absent!';
    }     
} 


/** 
 * Function MELBIS_BASKET_plus
 **/
function MELBIS_BASKET_plus($mVars)
{ 
    global $gParser, $gSession;       

    // Vars
    $store_id = (int) $mVars['post']['id'];
    
    // Get version    
    $version = $gSession->GetValue('melbis_version');
    if ( !isset($version) )
    {
        $version = MELBIS_INC_LOGIC_order_create();   
    }               

    // Add goods
    $version = MELBIS_INC_LOGIC_order_goods_add($version, $store_id);
     
    // Calculate
    $version = MELBIS_INC_LOGIC_order_calc($version);
             
    // Save version
    $gSession->SetValue('melbis_version', $version);    
                        
    return '';                         
}    


/** 
 * Function MELBIS_BASKET_minus
 **/
function MELBIS_BASKET_minus($mVars)
{ 
    global $gParser, $gSession;       

    // Vars
    $store_id = $mVars['post']['id']*1;
    
    // Get version    
    $version = $gSession->GetValue('melbis_version');
    if ( !isset($version) )
    {
        $version = MELBIS_INC_LOGIC_order_create();   
    }               

    // Remove goods
    $version = MELBIS_INC_LOGIC_order_goods_remove($version, $store_id);    
    
    // Calculate
    $version = MELBIS_INC_LOGIC_order_calc($version);
             
    // Save version
    $gSession->SetValue('melbis_version', $version);        
                               
    // Go to list
    return MELBIS_BASKET_goods($mVars);     
} 


/** 
 * Function MELBIS_BASKET_goods
 **/
function MELBIS_BASKET_goods($mVars)
{ 
    global $gParser, $gSession;       
    
    // Get version    
    $version = $gSession->GetValue('melbis_version');
    if ( !isset($version) )
    {
        $version = MELBIS_INC_LOGIC_order_create();   
    }                                 
    
    // Create 
    $tpl = $gParser->TplCreate();  
    
    // Lang tags    
    MELBIS_INC_LANG_tags($tpl, __FUNCTION__);         
             
    // Get Goods
    if ( count($version['store']) > 0 )
    {   
        foreach($version['store'] as $hash )
        {
            $name = htmlspecialchars($hash['store_name']); // MELBIS_INC_LANG('kStore', 'NAME', $hash['store_id'], $hash['store_name']);
            $gParser->TplAssign($tpl, array('ID'            => $hash['store_id'],
                                            'AMOUNT'        => $hash['amount'],
                                            'PRICE'         => MELBIS_INC_STD_number($hash['out_price'], 0),
                                            'NAME'          => $name
                                            ));         
            $gParser->TplParse($tpl, 'ITEM', '.goods_item');  
        }                         
        $gParser->TplParse($tpl, 'MAIN', 'goods');                
    }
    else
    {
        $gParser->TplParse($tpl, 'MAIN', 'goods_empty');
    }
    
    return $gParser->TplFree($tpl, 'MAIN');     
}      


/** 
 * Function MELBIS_BASKET_fields
 **/
function MELBIS_BASKET_fields($mVars)
{ 
    global $gParser, $gSession; 
                                        
    // Vars
    $gParser->gVars['ms']['page']['lang'] = $mVars['lang'];      
    
    // Get order version    
    $version = $gSession->GetValue('melbis_version');
    if ( !isset($version) )
    {
        $version = MELBIS_INC_LOGIC_order_create();   
    }                                 
    
    // Create 
    $tpl = $gParser->TplCreate();  
             
    // Get fields
    if ( count($version['client']) > 0 )
    {   
        foreach($version['client'] as $hash )
        {
            if ( $hash['field_folder'] == 1 )
            {
                $name = htmlspecialchars($hash['field_name']); // MELBIS_INC_LANG('kClient', 'FIELD', $hash['field_id'], $hash['field_name']);
                $gParser->TplAssign($tpl, 'NAME', $name);
                $gParser->TplParse($tpl, 'FIELD', '.fields_group');
                
                continue;                        
            }
                                              
            $command = "SELECT *
                          FROM {DBNICK}_field
                         WHERE id = '$hash[field_id]' 
                        ";                              
            $field = $gParser->SqlSelectToArray(__LINE__, $command);
                                            
            $command = "SELECT *
                          FROM {DBNICK}_field_value
                         WHERE field_id = '$field[id]' 
                      ORDER BY pos    
                        ";                              
            $value = $gParser->SqlSelect(__LINE__, $command);
            
            if ( count($value) > 0 && $field['fixed_set'] == 1 )
            {          
                $gParser->TplClear($tpl, 'VALUE');
                foreach( $value as $val )
                {
                    $name = htmlspecialchars($val['name']); // MELBIS_INC_LANG('kClient', 'VALUE', $val['id'], $val['name']);
                    $gParser->TplAssign($tpl, array('ID'    => $val['id'],
                                                    'NAME'  => $name,
                                                    'SELECT'=> ( $hash['value_id'] == $val['id'] ) ? 'selected' : ''
                                                    )); 
                    $gParser->TplParse($tpl, 'VALUE', '.fields_box_val');            
                }
                $name = htmlspecialchars($hash['field_name']); // MELBIS_INC_LANG('kClient', 'FIELD', $hash['field_id'], $hash['field_name']); 
                $gParser->TplAssign($tpl, array('ID'    => $hash['field_id'],
                                                'NAME'  => $name
                                                )); 
                $gParser->TplParse($tpl, 'FIELD', '.fields_box');                                       
            }
            else
            {                                                                 
                $name = htmlspecialchars($hash['field_name']); // MELBIS_INC_LANG('kClient', 'FIELD', $hash['field_id'], $hash['field_name']);
                $gParser->TplAssign($tpl, array('ID'        => $hash['field_id'], 
                                                'VALUE_ID'  => $hash['value_id'],
                                                'VALUE'     => htmlspecialchars($hash['value_txt']),                                            
                                                'NAME'      => $name,
                                                'AUTO'      => ''
                                                ));
                if ( count($value) > 0 )
                {                                                         
                    $gParser->TplParse($tpl, 'AUTO', '.fields_text_auto');
                }                                                         
                $gParser->TplParse($tpl, 'FIELD', '.fields_text');
            }
        }                         
        $gParser->TplParse($tpl, 'MAIN', 'fields');
        
        return $gParser->TplFree($tpl, 'MAIN');                
    }
    else
    {
        return '';
    }
}      


/** 
 * Function MELBIS_BASKET_options
 **/
function MELBIS_BASKET_options($mVars)
{ 
    global $gParser, $gSession;  
    
    // Vars
    $gParser->gVars['ms']['page']['lang'] = $mVars['lang'];         
    
    // Get version    
    $version = $gSession->GetValue('melbis_version');
    if ( !isset($version) )
    {
        $version = MELBIS_INC_LOGIC_order_create();   
    }                                 
    
    // Create 
    $tpl = $gParser->TplCreate();  
             
    // Get options
    if ( count($version['option']) > 0 )
    {   
        foreach($version['option'] as $hash )
        {     
            $command = "SELECT *
                          FROM {DBNICK}_order_option
                         WHERE id = '$hash[option_id]' 
                        ";                              
            $option = $gParser->SqlSelectToArray(__LINE__, $command);
                                            
            $command = "SELECT *
                          FROM {DBNICK}_order_option_value
                         WHERE option_id = '$option[id]' 
                      ORDER BY pos    
                        ";                              
            $value = $gParser->SqlSelect(__LINE__, $command);
            
            if ( count($value) > 0 && $option['fixed_set'] == 1 )
            {          
                $gParser->TplClear($tpl, 'VALUE');
                foreach( $value as $val )
                {                                                    
                    $name = htmlspecialchars($val['name']); // MELBIS_INC_LANG('kOption', 'VALUE', $val['id'], $val['name']);
                    $gParser->TplAssign($tpl, array('ID'    => $val['id'],
                                                    'NAME'  => $name,
                                                    'SELECT'=> ( $hash['value_id'] == $val['id'] ) ? 'selected' : ''
                                                    )); 
                    $gParser->TplParse($tpl, 'VALUE', '.options_box_val');            
                }
                $name = htmlspecialchars($hash['option_name']); MELBIS_INC_LANG('kOption', 'FIELD', $hash['option_id'], $hash['option_name']); 
                $gParser->TplAssign($tpl, array('ID'    => $hash['option_id'],
                                                'NAME'  => $name
                                                )); 
                $gParser->TplParse($tpl, 'OPTION', '.options_box');                                       
            }
            else
            {                                                                 
                $name = htmlspecialchars($hash['option_name']); // MELBIS_INC_LANG('kOption', 'FIELD', $hash['option_id'], $hash['option_name']);
                $gParser->TplAssign($tpl, array('ID'        => $hash['option_id'],  
                                                'VALUE_ID'  => $hash['value_id'],
                                                'VALUE'     => htmlspecialchars($hash['value_name']),                                            
                                                'NAME'      => $name,
                                                'AUTO'      => ''
                                                ));
                if ( count($value) > 0 )
                {                                                         
                    $gParser->TplParse($tpl, 'AUTO', '.options_text_auto');
                }                                                         
                $gParser->TplParse($tpl, 'OPTION', '.options_text');
            }
        }                         
        $gParser->TplParse($tpl, 'MAIN', 'options');
        
        return $gParser->TplFree($tpl, 'MAIN');                
    }
    else
    {
        return '';
    }
} 


/** 
 * Function MELBIS_BASKET_auto_field
 **/
function MELBIS_BASKET_auto_field($mVars)
{ 
    global $gParser;       
                
    // Vars   
    $id = $mVars['post']['id']*1;                     
    $query = addslashes($mVars['post']['query']); 

    // Get data      
    $command = "SELECT name AS value, id AS data
                  FROM {DBNICK}_field_value
                 WHERE name LIKE '%$query%' 
                   AND field_id = '$id'                
                ";  
    $data['suggestions'] = $gParser->SqlSelect(__LINE__, $command);
    
    return json_encode($data);                            
}    


/** 
 * Function MELBIS_BASKET_auto_option
 **/
function MELBIS_BASKET_auto_option($mVars)
{ 
    global $gParser;       
                
    // Vars                                      
    $id = $mVars['post']['id']*1;
    $query = addslashes($mVars['post']['query']); 

    // Get data      
    $command = "SELECT name AS value, id AS data
                  FROM {DBNICK}_order_option_value                  
                 WHERE name LIKE '%$query%'
                   AND option_id = '$id'                 
                ";  
    $data['suggestions'] = $gParser->SqlSelect(__LINE__, $command);
    
    return json_encode($data);                            
}    


/** 
 * Function MELBIS_BASKET_save
 **/
function MELBIS_BASKET_save($mVars)
{ 
    global $gParser, $gSession; 
                               
    // Vars
    $data['result'] = 'OK';
    $data['message'] = '';    
    
    // Get version    
    $version = $gSession->GetValue('melbis_version');
    if ( !is_array($version) )
    {
        $version = MELBIS_INC_LOGIC_order_create();   
    }  
    
    // Update fields
    $rows = count($version['client']); 
    for ( $i = 0; $i <= $rows - 1; $i++ )
    { 
        $id = $version['client'][$i]['field_id'];
        $value_id = $mVars['post']['field'.$id.'_id'] ?? 0;        
        $version['client'][$i]['value_id'] = ( $value_id == 0 ) ? null : (int)$value_id;
        $version['client'][$i]['value_txt'] = $mVars['post']['field'.$id.'_text'] ?? '';
    }
        
    // Update options
    $rows = count($version['option']); 
    for ( $i = 0; $i <= $rows - 1; $i++ )
    { 
        $id = $version['option'][$i]['option_id'];
        $value_id = $mVars['post']['option'.$id.'_id'] ?? 0;        
        $version['option'][$i]['value_id'] = ( $value_id == 0 ) ? null : (int)$value_id;
        $version['option'][$i]['value_name'] = $mVars['post']['option'.$id.'_text'];
    }
    
    // Calculate
    $version = MELBIS_INC_LOGIC_order_calc($version);
    
    // Save version
    $gSession->SetValue('melbis_version', $version);                                          

    // Verify cart    
    if ( count($version['store']) == 0 )
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
    $result = MELBIS_INC_LOGIC_order_edit($version);
    
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
        $gSession->RemoveValue('melbis_version');
    }       
                        
    return json_encode($data);   
}       

?>