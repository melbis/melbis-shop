<?php
/***************************************************************************************************
 * @version 6.5.0.273 @ 2026-05-17
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
    $page = [];    
    $page['year'] = MELBIS()->DateTime('now', 'Y');     
    
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
            $page['path'] = '/'; 
            $page['title'] = 'Home page';
            
            MELBIS()->TplParse($tpl, 'CONTENT', 'page_index');             
        }
        else
        {          
            // 404 Not found
            $page['id'] = 0;
            $page['path'] = '/?topic_id=-404'; 
            $page['title'] = '404 Not Found';
    
            // Header
            header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");           
                             
            // Content
            MELBIS()->TplParse($tpl, 'CONTENT', 'page_404');
        } 
    }
    else              
    {                           
        // Found   
        $page['id'] = $topic['id'];           
        $page['path'] = '/?topic_id='.$topic['id'];
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
                MELBIS_halt('CONTENT NOT FOUND', __FILE__.':'.__LINE__, '');
            }           
            else
            {
                // Content                     
                MELBIS()->TplAssign($tpl, 'DESCR', $text['descr']);
                MELBIS()->TplParse($tpl, 'CONTENT', 'page_text');
            }
        }
        elseif ( $topic['kind_key'] == 'kGoods' )  
        {           
            // Page goods
            MELBIS()->TplParse($tpl, 'CONTENT', 'page_goods');        
        }
        else
        {
            // Unknown page  
            MELBIS_halt('UNKNOWN PAGE', __FILE__.':'.__LINE__, '');
        }                                
    }                                             
    
    // Save page data           
    MELBIS()->gVars['page'] = $page;

    // Windows
    MELBIS()->TplParse($tpl, 'WINDOWS', 'windows');
                                    
    // Scripts
    MELBIS()->TplParse($tpl, 'SCRIPTS', 'scripts');
                
    // Final
    return MELBIS()->TplFinal($tpl, 'main');
}   
   

?>