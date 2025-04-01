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
    $gParser->gVars['ms']['var']['lang'] = $mVars['lang'];        
    $gParser->gVars['ms']['var']['year'] = MELBIS_INC_STD_get_now('Y');      
                
    // Define page
    $psu = '/'.addslashes($mVars['get']['psu'] ?? '');       
    $command = "SELECT t.id, t.name, t.kind_key, t.seo_psu AS path
                  FROM {DBNICK}_topic t   
                 WHERE t.no_visible = 0
                   AND t.seo_psu = '$psu'                                                                                                              
                ";     
    $topic = $gParser->SqlSelectToArray(__LINE__, $command);
    if ( !isset($topic['id']) ) 
    {
        // Not found
        header($gServer['SERVER_PROTOCOL']." 404 Not Found");           
        $gParser->gVars['ms']['page']['id'] = 0;
        $gParser->gVars['ms']['page']['path'] = '/';         
        $gParser->gVars['ms']['page']['title'] = MELBIS_INC_LANG_tag('BASE_PAGE', '404_TITLE');
        
        $gParser->TplParse($tpl, 'CONTENT', '404'); 
    }
    else 
    {                           
        if ( $topic['kind_key'] == 'kText' )  
        { 
            // Get page content                  
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
                $gParser->gVars['ms']['page']['id'] = $topic['id'];
                $gParser->gVars['ms']['page']['path'] = $topic['path'];   
                $gParser->gVars['ms']['page']['title'] = MELBIS_INC_LANG('kTopic', 'TITLE', $page['id'], $page['name']); 
                $descr = MELBIS_INC_LANG('kTopic', 'DESCR', $page['id'], $page['descr'], true);                                                            
                $gParser->TplAssign($tpl, ['ID'     => $topic['id'], 
                                           'DESCR'  => MELBIS_INC_STD_text($descr)]);  

                $gParser->TplParse($tpl, 'CONTENT', 'content_text');                
            } 
        }
        
        // Page goods 
        if ( $topic['kind_key'] == 'kGoods' )  
        {                  
            $gParser->gVars['ms']['page']['id'] = $topic['id'];  
            $gParser->gVars['ms']['page']['path'] = $topic['path'];
            $gParser->gVars['ms']['page']['title'] = MELBIS_INC_LANG('kTopic', 'NAME', $topic['id'], $topic['name']);
                                     
            $gParser->TplParse($tpl, 'CONTENT', 'content_goods');        
        }                
    }      
         
    $gParser->TplParse($tpl, 'SCRIPTS', 'scripts');
    
    // Final: return content
    $gParser->TplParse($tpl, 'MAIN', 'main');   
        
    return $gParser->TplFree($tpl, 'MAIN');
} 



?>