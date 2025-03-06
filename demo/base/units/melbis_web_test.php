<?php
/***************************************************************************************************
 * @version 6.3.0
 * @copyright 2019 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/
       
 
/** 
 * Function MELBIS_WEB_TEST
 **/
function MELBIS_WEB_TEST($mVars)
{ 
    global $gParser;
    
    return MELBIS_INC_AUTH(__FUNCTION__, $mVars);                    
}  


/** 
 * Function MELBIS_WEB_TEST_default
 **/
function MELBIS_WEB_TEST_default($mUserId, $mResultAuth, $mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();   
                                                           
    // Auth                
    if ( $mResultAuth == 'accept' )
    {   
        // Prepare rights          
        MELBIS_INC_AUTH_web_key_prepare($mUserId);
        
        // Show demo post vars
        $gParser->TplAssign($tpl, 'VARS', var_export($mVars, true));                
                                                                    
        // Order change back demo
        $order = ( isset($mVars['post']['order']) ) ? $mVars['post']['order'] : '{}';        
        $gParser->TplAssign($tpl, 'ORDER', $order);
        
        // Scripts
        $gParser->TplParse($tpl, 'SCRIPTS', 'scripts');                                                     
                   
        // Main
        $gParser->TplParse($tpl, 'MAIN', 'main');        
    }
    else
    {       
        // Auth
        $gParser->TplParse($tpl, 'MAIN', 'auth');
    }    
            
    return $gParser->TplFree($tpl, 'MAIN');                         
} 


/** 
 * Function MELBIS_WEB_TEST_get_cataloge
 **/
function MELBIS_WEB_TEST_get_cataloge($mUserId, $mVars)
{ 
    global $gParser;                                       
    
    // Vars
    $limit = $mVars['post']['limit']*1;
    $offset = $mVars['post']['offset']*1;                           
    $order = ( $mVars['post']['order'] == 'asc' ) ? 'ASC' : 'DESC';
    $sort = ( $mVars['post']['sort'] == '' ) ? 'id' : addslashes($mVars['post']['sort']);
        
    // Get data      
    $command = "SELECT t.id, t.name, t.tlevel, COUNT(ts.id) AS amount
                  FROM {DBNICK}_topic t
             LEFT JOIN {DBNICK}_topic_store ts
                    ON t.id = ts.topic_id           
              GROUP BY t.id    
              ORDER BY $sort $order                 
                ";  
    $data = $gParser->SqlSelectLimit(__LINE__, $command, $offset, $limit);
    
    return json_encode($data);                            
}        


/** 
 * Function MELBIS_WEB_TEST_get_goods
 **/
function MELBIS_WEB_TEST_get_goods($mUserId, $mVars)
{ 
    global $gParser;    
                                 
    // Vars
    $id = $mVars['post']['id']*1;
    $limit = $mVars['post']['limit']*1;
    $offset = $mVars['post']['offset']*1;                           
    $order = ( $mVars['post']['order'] == 'asc' ) ? 'ASC' : 'DESC';
    $sort = ( $mVars['post']['sort'] == '' ) ? 'id' : addslashes($mVars['post']['sort']);
    $where = '';    
    if ( $mVars['post']['search'] != '' )
    {
        $key = addslashes($mVars['post']['search']);
        $where .= " AND ( s.id = '$key' OR s.code_shop LIKE '%$key%' OR s.name LIKE '%$key%') "; 
    }            
                        
    // Get data      
    $command = "SELECT s.id, s.code_shop, s.name, s.status_key, kv_s.value_txt AS status_name,                                                                     
                       ROUND( IF(curr.id IS NULL, s.price, 
                                IF(curr.division = 0, s.price*curr.multiplex,
                                 IF(curr.multiplex <> 0, s.price/curr.multiplex, 0))), 2) AS price
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON s.id = ts.store_id 
             LEFT JOIN {DBNICK}_key_value kv_s
                    ON s.status_key = kv_s.key_name 
                   AND kv_s.key_code = 'STORE_STATUS_KEY'                   
             LEFT JOIN {DBNICK}_currency curr
                    ON s.price_curr_id = curr.id
                 WHERE ts.topic_id = '$id'
                       $where                  
              ORDER BY $sort $order                 
                ";  
    $data = $gParser->SqlSelectLimit(__LINE__, $command, $offset, $limit);
    
    return json_encode($data);                            
}    

?>