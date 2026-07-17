<?php

/** 
 * Function MELBIS_WEB_SAMPLE
 **/
function MELBIS_WEB_SAMPLE($mVars)
{ 
    return MELBIS_INC_AUTH_router(MELBIS()->UnitName(), $mVars);
} 

/** 
 * Function MELBIS_WEB_SAMPLE_default
 **/
function MELBIS_WEB_SAMPLE_default($mUserId, $mVars)
{        
    // Create 
    $tpl = MELBIS()->TplCreate(); 
        
    // Vars                 
    $page['title'] = 'Sample Web module';   
    
    // Auth                
    if ( $mUserId > 0 )      
    {    
        // Prepare
        MELBIS_INC_AUTH_web_key_prepare($mUserId);
        
        // Demo post vars
        MELBIS()->TplAssign($tpl, 'VARS', var_export($mVars, true));                
                                                                    
        // Demo Order change back                
        MELBIS()->TplAssign($tpl, 'ORDER', $mVars['post']['order'] ?? '{}');                
        
        // Scripts
        MELBIS()->TplParse($tpl, 'SCRIPTS', 'scripts');       
                      
        // Page
        MELBIS()->TplParse($tpl, 'CONTENT', 'page');                   
    }
    else
    {       
        // Vars
        MELBIS()->TplAssign($tpl, 'ORDER', '{}');
        
        // Auth
        MELBIS()->TplParse($tpl, 'CONTENT', 'auth');
    }          
    
    // Save page data           
    MELBIS()->GlobalAppend('PAGE', $page);          
                      
    // Final
    return MELBIS()->TplFinal($tpl, 'main');                       
}  


/** 
 * Function MELBIS_WEB_SAMPLE_get_cataloge
 **/
function MELBIS_WEB_SAMPLE_get_cataloge($mUserId, $mVars)
{                                            
    // Vars
    $limit = (int) $mVars['post']['limit'];
    $offset = (int) $mVars['post']['offset'];                               
    $sort = preg_replace('/[^a-z_]/', '', $mVars['post']['sort']);      
    $sort .= ( $mVars['post']['order'] == 'asc' ) ? ' ASC' : ' DESC';
        
    // Get data      
    $command = "SELECT t.id, t.name, t.tlevel, COUNT(ts.id) AS amount
                  FROM {DBNICK}_topic t
             LEFT JOIN {DBNICK}_topic_store ts
                    ON t.id = ts.topic_id           
              GROUP BY t.id    
              ORDER BY $sort
                ";  
    $data = MELBIS()->SqlSelectLimit(__LINE__, $command, $offset, $limit);
    
    return json_encode($data);                            
} 


/** 
 * Function MELBIS_WEB_SAMPLE_get_goods
 **/
function MELBIS_WEB_SAMPLE_get_goods($mUserId, $mVars)
{ 
    // Vars
    $topic_id = (int) $mVars['post']['id'];     
    $search = $mVars['post']['search'] ?? '';
    $limit = (int) $mVars['post']['limit'];
    $offset = (int) $mVars['post']['offset']; 
    $sort = preg_replace('/[^a-z_]/', '', $mVars['post']['sort']);      
    $sort .= ( $mVars['post']['order'] == 'asc' ) ? ' ASC' : ' DESC';
    
    // Conditions                          
    $cond = '';    
    if ( !empty($search) )
    {
        $cond .= " AND ( s.id = :KEY_INT OR s.code_shop LIKE :KEY_LIKE OR s.name LIKE :KEY_LIKE ) "; 
    }            
                        
    // Get data      
    $command = "SELECT s.id, 
                       s.code_shop, 
                       s.name, 
                       s.status_key, 
                       kv_s.value_txt AS status_name,                                                                     
                       IF(c.id IS NULL, s.price, 
                        IF(c.division = 0, s.price*c.multiplex,
                         IF(c.multiplex <> 0, s.price/c.multiplex, 0))) AS price
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON s.id = ts.store_id 
             LEFT JOIN {DBNICK}_key_value kv_s
                    ON s.status_key = kv_s.key_name 
                   AND kv_s.key_code = 'STORE_STATUS_KEY'                   
             LEFT JOIN {DBNICK}_currency c
                    ON s.price_curr_id = c.id
                 WHERE ts.topic_id = :TOPIC_ID
                       $cond                  
              ORDER BY $sort                 
                ";
    $param = [
        'topic_id'  => $topic_id,
        'key_int'   => (int) $search,
        'key_like'  => '%'.$search.'%'  
        ];
                  
    $data = MELBIS()->SqlSelectLimit(__LINE__, $command, $offset, $limit, $param);
    
    return json_encode($data);                            
}  

?>