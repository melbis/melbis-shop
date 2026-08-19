<?php
/***************************************************************************************************
 * @version 6.5.0.401 @ 2026-08-19
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

namespace MELBIS_BASE_PAGE;

use MELBIS_INC_WEB_CALLBACK as CALLBACK;

// Define Callback  
CALLBACK\Define(); 


/** 
 * Function Main
 **/
function Main($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();        
        
    // Vars         
    $id = (int) ( $mVars['get']['topic_id'] ?? 0 ); 
    $store_id = (int) ( $mVars['get']['store_id'] ?? 0 );    
    
    if ( $store_id > 0 )
    {
        // Goods page
        $command = "SELECT id, name  
                      FROM {DBNICK}_store   
                     WHERE id = :ID 
                       AND no_visible = 0
                    ";                    
        $params = [
            'id' => $store_id 
            ];        
        $store = MELBIS()->SqlSelectFlat(__LINE__, $command, $params);          

        if ( !isset($store['id']) )
        {
            // 404 Not found
            $page['id'] = 0;             
            $page['title'] = '404 Not Found';
    
            // Header
            header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");           
                             
            // Content
            MELBIS()->TplParse($tpl, 'PAGE', 'page_404');
        }
        else
        {
            // Found
            $page['id'] = 0;
            $page['store_id'] = $store['id'];
            $page['title'] = $store['name'];

            MELBIS()->TplParse($tpl, 'PAGE', 'page_store');
        }
    }
    else
    {
        // Define page
        $command = "SELECT *  
                      FROM {DBNICK}_topic   
                     WHERE id = :ID 
                       AND no_visible = 0
                    ";                    
        $params = [
            'id' => $id 
            ];        
        $topic = MELBIS()->SqlSelectFlat(__LINE__, $command, $params);          
                                   
        if ( !isset($topic['id']) ) 
        {   
            if ( $id == 0 )
            {
                // Index page
                $page['id'] = 0;             
                $page['title'] = 'Home page';
                
                MELBIS()->TplParse($tpl, 'PAGE', 'page_index');             
            }
            else
            {          
                // 404 Not found
                $page['id'] = 0;             
                $page['title'] = '404 Not Found';
        
                // Header
                header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");           
                                 
                // Content
                MELBIS()->TplParse($tpl, 'PAGE', 'page_404');
            } 
        }
        else              
        {                           
            // Found   
            $page['id'] = $topic['id'];                   
            $page['title'] = $topic['name'];     

            if ( $topic['kind_key'] == 'kText' )  
            { 
                // Text page content                  
                $command = "SELECT s.*
                              FROM {DBNICK}_store s
                              JOIN {DBNICK}_topic_store ts
                                ON s.id = ts.store_id
                             WHERE ts.topic_id = :TOPIC_ID  
                               AND s.no_visible = 0                          
                          ORDER BY ts.pos
                             LIMIT 1             
                            ";                                       
                $params = [
                    'topic_id' => $topic['id'] 
                    ];                        
                $text = MELBIS()->SqlSelectFlat(__LINE__, $command, $params);
                if ( !isset($text['id']) )
                {
                    // Error                 
                    MELBIS()->Halt(__FILE__.':'.__LINE__, 'CONTENT NOT FOUND');
                }           
                else
                {
                    // Content                     
                    MELBIS()->TplAssign($tpl, 'DESCR', $text['descr']);
                    MELBIS()->TplParse($tpl, 'PAGE', 'page_text');
                }
            }
            elseif ( $topic['kind_key'] == 'kGoods' )  
            {           
                // Page topic
                MELBIS()->TplParse($tpl, 'PAGE', 'page_topic');        
            }
            else
            {
                // Unknown page        
                MELBIS()->Halt(__FILE__.':'.__LINE__, 'UNKNOWN PAGE');            
            }                                
        }                                               
    }
    
    // Save page data                 
    MELBIS()->GlobalAssign('PAGE', $page);    
    
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}   
   

?>