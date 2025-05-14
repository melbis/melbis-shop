<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************
 *
 * MELBIS_INC_LANG                      - Translater from origin
 * MELBIS_INC_LANG_tag                  - One tag parser in script
 * MELBIS_INC_LANG_tags                 - All tags parser on page
 * 
 **************************************************************************************************/
   

/** 
 * Function MELBIS_INC_LANG
 **/
function MELBIS_INC_LANG($mTable, $mKey, $mId, $mTxt, $mIsHtml = false)
{ 
    global $gParser, $gLang;                                             
            
    // Vars
    $result = '';       
    
    // Empty
    if ( $mTxt == '' ) return '';
               
    // Get data      
    $command = "SELECT t.id, tl.translate, o.origin, o.crc, o.id AS oid   
                  FROM {DBNICK}_trans t
             LEFT JOIN {DBNICK}_trans_origin o
                    ON t.id = o.trans_id
                   AND o.item_id = '$mId'
             LEFT JOIN {DBNICK}_trans_lang tl
                    ON o.id = tl.origin_id 
                   AND tl.lang_id = ( SELECT id FROM {DBNICK}_lang where skey = '$gLang' )                    
                 WHERE t.table_key = '$mTable'
                   AND t.skey = '$mKey'                                     
                ";  
    $item = $gParser->SqlSelectToArray(__LINE__, $command);
    if ( isset($item['origin']) )
    {        
        // Found origin
        if ( $item['crc'] == md5($mTxt) )     
        {
            // CRC ok
            if ( isset($item['translate']) )
            {
                // Translate exists
                $result = $item['translate']; 
            }
            else
            {
                // No translate
                $result = $mTxt;        
            }
        }                                                                     
        else
        {
            // Crc fail
            $result = $mTxt;  
            $gParser->SqlUpdate(__LINE__, '{DBNICK}_trans_origin', ['id' => $item['oid'], 'origin' => $mTxt], 'id');    
        }    
    }
    else
    {
        // Not found  
        $result = $mTxt;
        if ( isset($item['id']) )
        {                                                    
            $hash = [];
            $hash['id'] = $gParser->SqlGenId('trans_origin');        
            $hash['trans_id'] = $item['id'];
            $hash['item_id'] = $mId;
            $hash['origin'] = $mTxt;
            $hash['crc'] = md5($mTxt);
            $hash['kind_key'] = 'kDefault';
            $hash['is_html'] = ( $mIsHtml ) ? 1 : 0;
            $hash['pos'] = $hash['id'];  
            $gParser->SqlInsert(__LINE__, '{DBNICK}_trans_origin', $hash);
        }  
    }       
    
    // Html
    if ( $mIsHtml )
    {
        return $result;
    }
    else
    {
        return htmlspecialchars($result);
    }                                
}   
 
 
/** 
 * Function MELBIS_INC_LANG_tag
 **/
function MELBIS_INC_LANG_tag($mMod, $mName)
{ 
    global $gParser, $gLang;    
    
    $command = "SELECT IFNULL(tl.translate, o.origin) AS value, 
                       o.is_active,
                       o.is_html   
                  FROM {DBNICK}_trans t
                  JOIN {DBNICK}_trans_origin o
                    ON t.id = o.trans_id
             LEFT JOIN {DBNICK}_trans_lang tl
                    ON o.id = tl.origin_id 
                   AND tl.lang_id = ( SELECT id FROM {DBNICK}_lang where skey = '$gLang' )                    
                 WHERE t.table_key = 'kTag'
                   AND t.name = '$mMod'
                   AND o.item_code = '$mName'
               ";                         
    $tag = $gParser->SqlSelectToArray(__LINE__, $command);    
    if ( !isset($tag['is_active']) || $tag['is_active'] == 0  )
    {
        return '';
    }
    else
    {
        if ( $tag['is_html'] == 0 )
        {
            return htmlspecialchars($tag['value']);
        }
        else
        {
            return $tag['value'];
        }    
    }                                     
} 

/** 
 * Function MELBIS_INC_LANG_tags
 **/
function MELBIS_INC_LANG_tags($mTpl, $mModule)
{ 
    global $gParser, $gLang;
                       
    $parts = explode('_', $mModule);
    $page_short =$parts[1];    
    $page_full = $parts[1].'_'.$parts[2];    
            
    $command = "SELECT o.item_code AS name, 
                       IFNULL(tl.translate, o.origin) AS value, 
                       o.is_active,
                       o.is_html   
                  FROM {DBNICK}_trans t
                  JOIN {DBNICK}_trans_origin o
                    ON t.id = o.trans_id
             LEFT JOIN {DBNICK}_trans_lang tl
                    ON o.id = tl.origin_id 
                   AND tl.lang_id = ( SELECT id FROM {DBNICK}_lang where skey = '$gLang' )                    
                 WHERE t.table_key = 'kTag'
                   AND ( t.name = '$page_short' OR t.name = '$page_full' )     
               ";                                                     
    $tags = $gParser->SqlSelect(__LINE__, $command);
    foreach ($tags as $tag)
    {                
        if ( $tag['is_active'] == 0 )
        {
            $gParser->TplAssign($mTpl, 'TAG_'.$tag['name'], '');
        }
        else
        {
            if ( $tag['is_html'] == 0 )
            {                                                    
                $gParser->TplAssign($mTpl, 'TAG_'.$tag['name'], htmlspecialchars($tag['value']));
                $gParser->TplAssign($mTpl, 'TAGS_'.$tag['name'], addcslashes($tag['value'], "'"));
            }
            else
            {
                $gParser->TplAssign($mTpl, 'TAG_'.$tag['name'], $tag['value']);                
            }
        }        
    }
} 

 
 
?>