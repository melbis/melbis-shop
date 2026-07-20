<?php
/***************************************************************************************************
 * @version 6.5.0.329 @ 2026-07-20
 * @copyright 2002-2026 Melbis
 * @link https://melbis.com
 * @author Dmytro Kasianov
 **************************************************************************************************/

/** 
 * Function MELBIS_BASE_PAGE
 **/
function MELBIS_BASE_PAGE($mVars)
{ 
    // Create 
    $tpl = MELBIS()->TplCreate();    
        
    // Vars         
    $id = (int) ( $mVars['get']['topic_id'] ?? 0 ); 
    
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
            // Page goods
            MELBIS()->TplParse($tpl, 'PAGE', 'page_goods');        
        }
        else
        {
            // Unknown page        
            MELBIS()->Halt(__FILE__.':'.__LINE__, 'UNKNOWN PAGE');            
        }                                
    }                                               
    
    // Save page data                 
    MELBIS()->GlobalAssign('PAGE', $page);    
    
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}   
   

?>