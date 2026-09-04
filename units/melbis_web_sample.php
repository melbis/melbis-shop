<?php
/***************************************************************************************************
 * @version 6.5.1.425 @ 2026-09-04
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************
 *
 * Main        - Runs through the auth door
 * Page        - Prints the module page
 * GetCataloge - Answers a page of sections
 * GetGoods    - Answers a page of goods
 *
 **************************************************************************************************/

namespace MELBIS_WEB_SAMPLE;

use MELBIS_INC_AUTH as AUTH;
use MELBIS_INC_WEB_CALLBACK as CALLBACK;
use MELBIS_INC_LOGIC_COMMON as LOGIC_COMMON;

// Define Callback, the header
CALLBACK\Define(); 


/** 
 * Function Main
 **/
function Main($mVars)
{ 
    return AUTH\Router(MELBIS()->UnitName(), $mVars);
} 


/** 
 * Function Page
 **/
function Page($mUserId, $mVars)
{        
    // Create 
    $tpl = MELBIS()->TplCreate(); 
    
    // Auth                
    if ( $mUserId > 0 )      
    {    
        // Demo post vars
        MELBIS()->TplAssign($tpl, 'VARS', var_export($mVars, true));                
                                                                    
        // Demo Order change back                
        MELBIS()->TplAssign($tpl, 'ORDER', $mVars['post']['order'] ?? '{}');                
                      
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
    MELBIS()->GlobalAppend('PAGE:TITLE', 'Sample Web module');              
                      
    // Final
    return MELBIS()->TplFinal($tpl, 'main');                       
}  


/** 
 * Function GetCataloge
 **/
function GetCataloge($mUserId, $mVars)
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
 * Function GetGoods
 **/
function GetGoods($mUserId, $mVars)
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
                        
    // Get data, the price raw
    $command = "SELECT s.id, 
                       s.code_shop, 
                       s.name, 
                       s.status_key, 
                       s.price, 
                       s.price_curr_id
                  FROM {DBNICK}_store s
                  JOIN {DBNICK}_topic_store ts
                    ON s.id = ts.store_id 
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
    
    // Calc                   
    $status = MELBIS()->SysKeyValues('STORE_STATUS_KEY');
    foreach ( $data['rows'] as &$row )
    {
        $row['price'] = LOGIC_COMMON\Price($row['price'], $row['price_curr_id']);
        $row['status_name'] = $status[$row['status_key']] ?? '';
    }
    unset($row);
    
    return json_encode($data);                            
}  

?>