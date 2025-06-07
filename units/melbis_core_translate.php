<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/

/** 
 * Function MELBIS_CORE_TRANSLATE
 **/
function MELBIS_CORE_TRANSLATE($mVars)
{ 
    global $gParser; 

    // Header
    header('Content-Type: text/plain; charset=utf-8');   
                                      
    // Vars   
    $update = [];
    $now = MELBIS_INC_STD_get_now();
    $pagetime = $gParser->RunTime();
    echo "> Running at the $now \r\n";             
         
    // From lang
    $command = "SELECT * FROM {DBNICK}_lang WHERE is_origin = 1";               
    $from = $gParser->SqlSelectToArray(__LINE__, $command);    
           
    // To lang
    $command = "SELECT * 
                  FROM {DBNICK}_lang
                 WHERE is_origin <> 1
              ORDER BY pos
              ";                
    $langs = $gParser->SqlSelect(__LINE__, $command);
    foreach ($langs as $lang) 
    {         
        echo "\r\n> Translate to: $lang[name] \r\n";

        // Content        
        $command = "SELECT o.id, o.trans_id, o.context, o.origin, o.is_html, tl.id AS tl_id, LENGTH(o.origin) AS size   
                      FROM {DBNICK}_trans_origin o
                 LEFT JOIN {DBNICK}_trans_lang tl
                        ON o.id = tl.origin_id 
                       AND tl.lang_id = '$lang[id]'  
                     WHERE md5(CONCAT(o.context, o.origin)) <> o.crc 
                        OR tl.id IS NULL
                  ORDER BY size ASC                    
                     LIMIT 50
                   ";
        $origins = $gParser->SqlSelect(__LINE__, $command);
        foreach ($origins as $origin)
        {
            echo '['.$origin['id']."]: ";
            
            $translate = MELBIS_INC_TRANSLATE_deepl($origin['origin'], 
                                                    $origin['context'], 
                                                    $from['skey'], 
                                                    $lang['skey'], 
                                                    $origin['is_html']);
            echo $translate."\r\n"; 
            
            if ( !isset($origin['tl_id']) )
            {   
                // Add translate
                $hash = [];
                $hash['id'] = $gParser->SqlGenId('trans_lang');
                $hash['lang_id'] = $lang['id'];
                $hash['trans_id'] = $origin['trans_id'];
                $hash['origin_id'] = $origin['id'];            
                $hash['translate'] = $translate;            
                $gParser->SqlInsert(__LINE__, '{DBNICK}_trans_lang', $hash);            
            }
            else
            {    
                // Change translate
                $hash = [];
                $hash['id'] = $origin['tl_id'];
                $hash['translate'] = $translate;
                $gParser->SqlUpdate(__LINE__, '{DBNICK}_trans_lang', $hash, 'id');                          
            }
            // Save Update
            $update[$origin['id']] = md5($origin['context'].$origin['origin']); 
        }                                      
    } 
    
    // Update
    foreach ( $update as $id => $crc )
    {    
        $hash = [];
        $hash['id'] = $id;
        $hash['crc'] = $crc;
        $gParser->SqlUpdate(__LINE__, '{DBNICK}_trans_origin', $hash, 'id');
    }                                        
    
    return '';                    
} 

?>