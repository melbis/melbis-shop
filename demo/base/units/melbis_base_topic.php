<?php
/***************************************************************************************************
 * @version 6.3.0
 * @copyright 2019 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_BASE_TOPIC
 **/
function MELBIS_BASE_TOPIC($mVars)
{ 
    global $gParser, $gServer; 
    
    // Create 
    $tpl = $gParser->TplCreate();     
    
    // Vars
    $id = $mVars['get']['topic_id'];  
    
    $command = "SELECT *
                  FROM {DBNICK}_topic
                 WHERE id = '$id' 
                ";                    
    $topic = $gParser->SqlSelectToArray(__LINE__, $command);    
    if ( is_null($topic['id']) )
    {
        // Topic not found
        header($gServer['SERVER_PROTOCOL']." 404 Not Found");
    
        // Final: return content    
        $gParser->TplParse($tpl, 'MAIN', '404');        
    } 
    else                 
    {
        // Define page vars
        $gParser->gVars['melbis']['page']['id'] = $topic['id'];
        $gParser->gVars['melbis']['page']['name'] = htmlspecialchars($topic['name']);    
                                
        // Final: return content                     
        $gParser->TplParse($tpl, 'MAIN', 'main');               
    }
                            
    return $gParser->TplFree($tpl, 'MAIN');
} 



?>