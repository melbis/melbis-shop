<?php
/***************************************************************************************************
 * @version 6.4.0
 * @copyright 2025 Melbis Company
 * @link https://melbis.com
 * @author Dmitriy Kasyanoff
 **************************************************************************************************/


/** 
 * Function MELBIS_STORE_FEATURES
 **/
function MELBIS_STORE_FEATURES($mVars)
{ 
    global $gParser;
                       
    // Create 
    $tpl = $gParser->TplCreate();
    
        
    // Get features
    $command = "SELECT i.name AS info_name, i.type_key, 
                       si.id, si.value_dec, si.value_txt, si.value_id, si.info_id, 
                       iv.name AS value_name 
                  FROM {DBNICK}_store_info si
                  JOIN {DBNICK}_info i
                    ON i.id = si.info_id
             LEFT JOIN {DBNICK}_info_value iv
                    ON iv.id = si.value_id     
                 WHERE si.store_id = '$mVars[id]'
                   AND i.in_topic = 1   
              ORDER BY i.absindex
                ";                    
    $query = $gParser->SqlQuery(__LINE__, $command);
    $rows = $gParser->SqlNumRows($query); 
    $value = '';
    for ( $i = 1; $i <= $rows; $i++ )
    {           
        // Get feature values               
        $gParser->SqlDataSeek($query, $i - 1);
        $row = $gParser->SqlFetchHash($query);        
            
        switch ( $row['type_key'] ) 
        {
            case 'kSet':
                $value .= htmlspecialchars($row['value_name']); // MELBIS_INC_LANG('kInfo', 'VALUE', $row['value_id'], $row['value_name']);
                break;
            case 'kDecimal':
                $value = MELBIS_INC_STD_number($row['value_dec']);
                break;                  
            case 'kText':
                $value = htmlspecialchars($row['value_txt']); // MELBIS_INC_LANG('kInfo', 'TEXT', $row['id'], $row['value_txt']);
                break;                                  
        }
                             
        // Verify next
        $next = $gParser->SqlFetchHash($query);
        if ( ( $next['info_id'] ?? 0 ) != $row['info_id'] )
        {                                              
            $name = htmlspecialchars($row['info_name']); // MELBIS_INC_LANG('kInfo', 'NAME', $row['info_id'], $row['info_name']);
            $gParser->TplAssign($tpl, array('NAME'  => $name, 
                                            'VALUE' => $value
                                            )); 
            $gParser->TplParse($tpl, 'ITEM', '.item');
            $value = '';
        }  
        else
        {
            $value .= ', ';
        }
    }    
    
    if ( $rows > 0 ) 
    {
    
        // Final: return content
        $gParser->TplParse($tpl, 'MAIN', 'main');

        return $gParser->TplFree($tpl, 'MAIN');
    } 
    else
    {
        return '';
    }
} 



?>