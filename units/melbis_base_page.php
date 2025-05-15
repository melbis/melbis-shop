<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_PAGE
 **/
function MELBIS_BASE_PAGE($mVars)
{ 
    global $gParser, $gServer;
                       
    // Create 
    $tpl = $gParser->TplCreate();                

    // Lang tags    
    MELBIS_INC_LANG_tags($tpl, __FUNCTION__);                                                     
    
    // Vars
    $id = (int) ($mVars['get']['topic_id'] ?? 0);
    
    $gParser->gVars['ms']['page']['lang'] = $mVars['lang'];        
    $gParser->gVars['ms']['page']['year'] = MELBIS_INC_STD_get_now('Y');      
                     
    // Define page
    $command = "SELECT *
                  FROM {DBNICK}_topic   
                 WHERE id = '$id' 
                   AND no_visible = 0
                ";     
    $topic = $gParser->SqlSelectToArray(__LINE__, $command);
    if ( !isset($topic['id']) ) 
    {   
        if ( $id == 0 )
        {
            // Index page
            $gParser->gVars['ms']['page']['id'] = 0;      
            $gParser->gVars['ms']['page']['path'] = '/';         
            $gParser->gVars['ms']['page']['title'] = 'Home page';         
            
            $gParser->TplParse($tpl, 'CONTENT', 'index');             
        }
        else
        {          
            // Not found
            $gParser->gVars['ms']['page']['id'] = 0;         
            $gParser->gVars['ms']['page']['path'] = '/?topic_id=-404';
            $gParser->gVars['ms']['page']['title'] = '404 Not Found'; // MELBIS_INC_LANG_tag('BASE_PAGE', '404_TITLE');         
    
            // Header
            header($gServer['SERVER_PROTOCOL']." 404 Not Found");           
                             
            // Content
            $gParser->TplParse($tpl, 'CONTENT', '404');
        } 
    }
    else              
    {                           
        // Found
        $gParser->gVars['ms']['page']['id'] = $topic['id'];   
        $gParser->gVars['ms']['page']['path'] = '/?topic_id='.$topic['id'];
        $gParser->gVars['ms']['page']['title'] = htmlspecialchars($topic['name']); // MELBIS_INC_LANG('kTopic', 'TITLE', $topic['id'], $topic['name']);     

        if ( $topic['kind_key'] == 'kText' )  
        { 
            // Text page content                  
            $command = "SELECT s.*
                          FROM {DBNICK}_store s
                          JOIN {DBNICK}_topic_store ts
                            ON s.id = ts.store_id
                         WHERE ts.topic_id = '$topic[id]'  
                           AND s.no_visible = 0                          
                      ORDER BY ts.pos
                         LIMIT 1             
                        ";                    
            $page = $gParser->SqlSelectToArray(__LINE__, $command);
            if ( !isset($page['id']) )
            {
                // Error 
                MELBIS_INC_halt('CONTENT NOT FOUND', __FILE__.':'.__LINE__, '');
            }           
            else
            {
                // Content                     
                $descr = $page['descr']; // MELBIS_INC_LANG('kTopic', 'DESCR', $page['id'], $page['descr'], true); 
                $gParser->TplAssign($tpl, 'DESCR', MELBIS_INC_STD_text($descr));  
                $gParser->TplParse($tpl, 'CONTENT', 'text');                
            }
        }
                 
        if ( $topic['kind_key'] == 'kGoods' )  
        {                  
            // Page goods
            $gParser->TplParse($tpl, 'CONTENT', 'goods');        
        }                                
    }      
         
    $gParser->TplParse($tpl, 'SCRIPTS', 'scripts');
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');   
        
    return $gParser->TplFree($tpl, 'MAIN');
} 



?>